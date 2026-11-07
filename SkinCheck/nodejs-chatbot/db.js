const mysql = require('mysql2/promise');

const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'skincheck_db',
    waitForConnections: true,
    connectionLimit: 20,
    queueLimit: 0,
    idleTimeout: 60000
});

// Cache for system-wide stats to minimize database load and maximize response speed
let systemDataCache = null;
let lastCacheTime = 0;
const CACHE_TTL_MS = 3000; // 3 seconds TTL

async function getSystemData() {
    const now = Date.now();
    if (systemDataCache && (now - lastCacheTime) < CACHE_TTL_MS) {
        return systemDataCache;
    }

    try {
        const [
            prodRows,
            eduRows,
            statRows,
            customerRows,
            adminRows,
            scanStats,
            faceScanUsers,
            sunscreenScanUsers,
            recentScanRows,
            logRows
        ] = await Promise.all([
            // 1. Products
            pool.query('SELECT id, name, brand, category, description FROM products WHERE status = "active"')
                .then(([rows]) => rows).catch(e => []),

            // 2. Education
            pool.query('SELECT title, content, category FROM education_contents WHERE status = "published" LIMIT 5')
                .then(([rows]) => rows).catch(e => []),

            // 3. Overall user counts
            pool.query(`
                SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN role IN ('user', 'customer') THEN 1 ELSE 0 END) as total_customers,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admins,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as total_active
                FROM users
            `).then(([rows]) => rows[0] || {}).catch(e => ({})),

            // 4. All registered customers with scan stats
            pool.query(`
                SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    u.role, 
                    u.status, 
                    u.created_at,
                    COUNT(s.id) as total_scans,
                    SUM(CASE WHEN s.scan_type = 'face' THEN 1 ELSE 0 END) as face_scans,
                    SUM(CASE WHEN s.scan_type = 'sunscreen' THEN 1 ELSE 0 END) as sunscreen_scans
                FROM users u
                LEFT JOIN skin_scans s ON s.user_id = u.id
                WHERE u.role IN ('user', 'customer')
                GROUP BY u.id, u.name, u.email, u.role, u.status, u.created_at
                ORDER BY u.created_at DESC
            `).then(([rows]) => rows).catch(e => []),

            // 5. Admins
            pool.query('SELECT id, name, email, role, status, created_at FROM users WHERE role = "admin" ORDER BY created_at DESC')
                .then(([rows]) => rows).catch(e => []),

            // 6. Overall Scans
            pool.query(`
                SELECT 
                    COUNT(*) as total_scans,
                    SUM(CASE WHEN scan_type = 'face' THEN 1 ELSE 0 END) as face_scans,
                    SUM(CASE WHEN scan_type = 'sunscreen' THEN 1 ELSE 0 END) as sunscreen_scans
                FROM skin_scans
            `).then(([rows]) => rows[0] || {}).catch(e => ({})),

            // 7. Users who performed face scans
            pool.query(`
                SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    COUNT(s.id) as total_scans, 
                    MAX(s.created_at) as last_scan_date
                FROM skin_scans s
                JOIN users u ON u.id = s.user_id
                WHERE s.scan_type = 'face'
                GROUP BY u.id, u.name, u.email
                ORDER BY total_scans DESC, last_scan_date DESC
            `).then(([rows]) => rows).catch(e => []),

            // 8. Users who performed sunscreen scans
            pool.query(`
                SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    COUNT(s.id) as total_scans, 
                    MAX(s.created_at) as last_scan_date
                FROM skin_scans s
                JOIN users u ON u.id = s.user_id
                WHERE s.scan_type = 'sunscreen'
                GROUP BY u.id, u.name, u.email
                ORDER BY total_scans DESC, last_scan_date DESC
            `).then(([rows]) => rows).catch(e => []),

            // 9. Recent scans detail
            pool.query(`
                SELECT 
                    s.id, 
                    s.scan_type, 
                    s.status, 
                    s.created_at, 
                    u.name as user_name,
                    u.email as user_email,
                    a.acne_score,
                    a.hydration_level,
                    a.overall_health,
                    sr.coverage_score,
                    sr.missed_areas
                FROM skin_scans s
                LEFT JOIN users u ON u.id = s.user_id
                LEFT JOIN analysis_results a ON a.scan_id = s.id
                LEFT JOIN sunscreen_results sr ON sr.scan_id = s.id
                ORDER BY s.created_at DESC
                LIMIT 25
            `).then(([rows]) => rows).catch(e => []),

            // 10. Recent activity logs
            pool.query(`
                SELECT a.id, a.action, a.description, a.created_at, u.name as user_name
                FROM activity_logs a
                LEFT JOIN users u ON u.id = a.user_id
                ORDER BY a.created_at DESC
                LIMIT 15
            `).then(([rows]) => rows).catch(e => [])
        ]);

        systemDataCache = {
            products: prodRows,
            education: eduRows,
            systemStats: {
                totalUsers: Number(statRows.total_users || 0),
                totalCustomers: Number(statRows.total_customers || 0),
                totalAdmins: Number(statRows.total_admins || 0),
                totalActive: Number(statRows.total_active || 0),
                customers: customerRows || [],
                admins: adminRows || [],
                totalScans: Number(scanStats.total_scans || 0),
                faceScans: Number(scanStats.face_scans || 0),
                sunscreenScans: Number(scanStats.sunscreen_scans || 0),
                faceScanUsers: faceScanUsers || [],
                sunscreenScanUsers: sunscreenScanUsers || [],
                recentScans: recentScanRows || [],
                recentLogs: logRows || []
            }
        };
        lastCacheTime = now;
        return systemDataCache;
    } catch (err) {
        console.error('Error fetching system data:', err);
        return {
            products: [],
            education: [],
            systemStats: {
                totalUsers: 0,
                totalCustomers: 0,
                totalAdmins: 0,
                totalActive: 0,
                customers: [],
                admins: [],
                totalScans: 0,
                faceScans: 0,
                sunscreenScans: 0,
                faceScanUsers: [],
                sunscreenScanUsers: [],
                recentScans: [],
                recentLogs: []
            }
        };
    }
}

async function getUserDatabaseContext(identifier) {
    const { userId, userEmail, userName } = identifier || {};
    
    let user = null;
    let faceScan = null;
    let sunscreenScan = null;

    try {
        // Step 1: Resolve user and fetch system data concurrently
        const [resolvedUser, systemData] = await Promise.all([
            (async () => {
                if (userId) {
                    const [rows] = await pool.query('SELECT id, name, email, role, created_at FROM users WHERE id = ? LIMIT 1', [userId]);
                    if (rows.length > 0) return rows[0];
                }
                if (userEmail) {
                    const [rows] = await pool.query('SELECT id, name, email, role, created_at FROM users WHERE email = ? LIMIT 1', [userEmail]);
                    if (rows.length > 0) return rows[0];
                }
                if (userName) {
                    const [rows] = await pool.query('SELECT id, name, email, role, created_at FROM users WHERE name = ? LIMIT 1', [userName]);
                    if (rows.length > 0) return rows[0];
                }
                return null;
            })(),
            getSystemData()
        ]);

        user = resolvedUser;
        const effectiveUserId = user ? user.id : userId;

        // Step 2: If user exists, fetch their specific scans concurrently
        if (effectiveUserId) {
            const [scansFace, scansSunscreen] = await Promise.all([
                pool.query(`
                    SELECT s.id as scan_id, s.image_url, s.created_at, a.acne_score, a.hydration_level, a.overall_health, a.details
                    FROM skin_scans s
                    LEFT JOIN analysis_results a ON a.scan_id = s.id
                    WHERE s.user_id = ? AND s.scan_type = 'face'
                    ORDER BY s.created_at DESC
                    LIMIT 1
                `, [effectiveUserId]).then(([rows]) => rows[0] || null).catch(() => null),

                pool.query(`
                    SELECT s.id as scan_id, s.image_url, s.created_at, sr.coverage_score, sr.missed_areas
                    FROM skin_scans s
                    LEFT JOIN sunscreen_results sr ON sr.scan_id = s.id
                    WHERE s.user_id = ? AND s.scan_type = 'sunscreen'
                    ORDER BY s.created_at DESC
                    LIMIT 1
                `, [effectiveUserId]).then(([rows]) => rows[0] || null).catch(() => null)
            ]);

            faceScan = scansFace;
            sunscreenScan = scansSunscreen;
        }

        return {
            user,
            faceScan,
            sunscreenScan,
            products: systemData.products,
            education: systemData.education,
            systemStats: systemData.systemStats
        };

    } catch (err) {
        console.error('Error fetching database context:', err);
        return {
            user: null,
            faceScan: null,
            sunscreenScan: null,
            products: [],
            education: [],
            systemStats: {
                totalUsers: 0,
                totalCustomers: 0,
                totalAdmins: 0,
                totalActive: 0,
                customers: [],
                admins: [],
                totalScans: 0,
                faceScans: 0,
                sunscreenScans: 0,
                faceScanUsers: [],
                sunscreenScanUsers: [],
                recentScans: [],
                recentLogs: []
            }
        };
    }
}

module.exports = {
    pool,
    getUserDatabaseContext
};
