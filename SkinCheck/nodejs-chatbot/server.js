const express = require('express');
const cors = require('cors');
const Groq = require('groq-sdk');
const fs = require('fs');
const path = require('path');
const { getUserDatabaseContext } = require('./db.js');

const app = express();
app.use(cors());
app.use(express.json());

function getAiConfig() {
    const configPath = path.join(__dirname, '..', 'config', 'ai_config.json');
    try {
        if (fs.existsSync(configPath)) {
            const raw = fs.readFileSync(configPath, 'utf8');
            return JSON.parse(raw);
        }
    } catch (e) {
        console.error('Failed reading ai_config.json:', e.message);
    }
    return {
        provider: 'groq',
        model: 'qwen/qwen3.8-27b',
        primary_api_key: process.env.GROQ_API_KEY || '',
        backup_api_key_1: '',
        backup_api_key_2: '',
        backup_api_key_3: '',
        auto_failover: true
    };
}

async function callChatWithFailover(messages) {
    const config = getAiConfig();
    const modelToUse = config.model || 'qwen/qwen3.8-27b';

    const candidateKeys = [
        { id: 'primary', label: 'Kunci Utama (Primary)', key: config.primary_api_key?.trim() },
        { id: 'backup_1', label: 'Kunci Cadangan 1', key: config.backup_api_key_1?.trim() },
        { id: 'backup_2', label: 'Kunci Cadangan 2', key: config.backup_api_key_2?.trim() },
        { id: 'backup_3', label: 'Kunci Cadangan 3', key: config.backup_api_key_3?.trim() }
    ];

    const activePool = candidateKeys.filter(k => k.key && k.key.length > 5);

    if (activePool.length === 0) {
        throw new Error('Tidak ada API Key yang terkonfigurasi. Harap masukkan API Key di Admin -> Konfigurasi AI.');
    }

    let lastError = null;
    for (let i = 0; i < activePool.length; i++) {
        const item = activePool[i];
        try {
            const client = new Groq({ 
                apiKey: item.key,
                maxRetries: 0,
                timeout: 4500
            });

            const completion = await client.chat.completions.create({
                messages: messages,
                model: modelToUse,
                max_tokens: 300,
                temperature: 0.1
            });

            return completion;
        } catch (err) {
            console.warn(`[Chatbot FAILOVER] ${item.label} (${err.status || err.message}). Mencoba kunci cadangan berikutnya...`);
            lastError = err;
            if (!config.auto_failover && i === 0) {
                break;
            }
        }
    }

    throw lastError || new Error('Semua API Key (Utama & Cadangan) gagal merespons.');
}

app.post('/api/chat', async (req, res) => {
    try {
        const { message, userId, userName, userEmail, history } = req.body;
        
        if (!message) {
            return res.status(400).json({ error: 'Message is required' });
        }
        
        // 1. Fetch real-time data from database (parallel & cached)
        const dbCtx = await getUserDatabaseContext({ userId, userEmail, userName });
        
        const effectiveName = dbCtx.user?.name || userName || 'Pengguna';
        const effectiveEmail = dbCtx.user?.email || userEmail || '';
        const userRole = dbCtx.user?.role === 'admin' ? 'Administrator' : 'Customer';

        // 2. Format user-specific scan summary
        let userFaceScan = 'Belum pernah scan wajah.';
        if (dbCtx.faceScan) {
            const scanDate = dbCtx.faceScan.created_at ? new Date(dbCtx.faceScan.created_at).toLocaleDateString('id-ID') : '-';
            userFaceScan = `${scanDate} (Skor Jerawat: ${dbCtx.faceScan.acne_score ?? 0}, Hidrasi: ${dbCtx.faceScan.hydration_level ?? 0}%, Skor Kulit: ${dbCtx.faceScan.overall_health ?? 0}%)`;
        }

        let userSunscreenScan = 'Belum pernah cek sunscreen.';
        if (dbCtx.sunscreenScan) {
            const scanDate = dbCtx.sunscreenScan.created_at ? new Date(dbCtx.sunscreenScan.created_at).toLocaleDateString('id-ID') : '-';
            userSunscreenScan = `${scanDate} (Cakupan: ${dbCtx.sunscreenScan.coverage_score}%)`;
        }

        // 3. Compact catalog
        let catalogSummary = 'Katalog produk kosong.';
        if (dbCtx.products && dbCtx.products.length > 0) {
            catalogSummary = dbCtx.products.map(p => `- ${p.name} [${p.category}]: ${p.description}`).join('\n');
        }

        // 4. Compact stats
        const stats = dbCtx.systemStats || { totalCustomers: 0, totalAdmins: 0, totalUsers: 0, totalScans: 0, faceScans: 0, sunscreenScans: 0 };
        
        const customerList = (stats.customers && stats.customers.length > 0)
            ? stats.customers.map((c, i) => `${i + 1}. ${c.name} (${c.email}) - Total Scan: ${c.total_scans || 0}`).join('\n')
            : 'Belum ada customer terdaftar.';

        const faceScanList = (stats.faceScanUsers && stats.faceScanUsers.length > 0)
            ? stats.faceScanUsers.map((u, i) => `${i + 1}. ${u.name} (${u.total_scans} scan)`).join('\n')
            : 'Belum ada.';

        const sunscreenScanList = (stats.sunscreenScanUsers && stats.sunscreenScanUsers.length > 0)
            ? stats.sunscreenScanUsers.map((u, i) => `${i + 1}. ${u.name} (${u.total_scans} scan)`).join('\n')
            : 'Belum ada.';

        const adminList = (stats.admins && stats.admins.length > 0)
            ? stats.admins.map((a, i) => `${i + 1}. ${a.name} (${a.email})`).join('\n')
            : 'Belum ada.';

        const recentScansList = (stats.recentScans && stats.recentScans.length > 0)
            ? stats.recentScans.slice(0, 5).map(s => {
                const dt = new Date(s.created_at).toLocaleDateString('id-ID');
                const info = s.scan_type === 'face' ? `Jerawat: ${s.acne_score ?? '-'}` : `Cakupan: ${s.coverage_score}%`;
                return `#${s.id}: ${s.user_name || 'User'} (${s.scan_type === 'face' ? 'Wajah' : 'Sunscreen'}, ${info}, ${dt})`;
            }).join('\n')
            : 'Belum ada scan.';

        // 5. Lean, focused system prompt (~400 tokens)
        const systemPrompt = `Anda SkinCheck AI Assistant resmi aplikasi APSkincek. Jawab langsung, akurat, to-the-point sesuai data database berikut. Dilarang bertele-tele dan dilarang menggunakan emoji.

[DATA DATABASE RESMI APSKINCEK]
- User Aktif Chat: ${effectiveName} (${effectiveEmail}, ${userRole})
- Scan Terakhir User: ${userFaceScan} | Sunscreen: ${userSunscreenScan}
- Total Pengguna: ${stats.totalUsers} (Customer: ${stats.totalCustomers}, Admin: ${stats.totalAdmins})
- Total Scan: ${stats.totalScans} kali (${stats.faceScans} scan wajah, ${stats.sunscreenScans} cek sunscreen)

[DAFTAR SELURUH CUSTOMER (${stats.totalCustomers} Orang)]
${customerList}

[CUSTOMER SCAN WAJAH]
${faceScanList}

[CUSTOMER CEK SUNSCREEN]
${sunscreenScanList}

[DAFTAR ADMINISTRATOR (${stats.totalAdmins} Orang)]
${adminList}

[5 SCAN TERAKHIR]
${recentScansList}

[KATALOG PRODUK]
${catalogSummary}

ATURAN MENJAWAB:
1. Ditanya berapa customer / daftar customer: Langsung jawab total customer (${stats.totalCustomers} orang) dan daftarkan namanya.
2. Ditanya siapa yang scan wajah / sunscreen: Sebutkan nama dan jumlah scan dari data di atas.
3. Ditanya total scan: Sebutkan ${stats.totalScans} kali (${stats.faceScans} wajah, ${stats.sunscreenScans} sunscreen).
4. Ditanya siapa saya: Anda adalah ${effectiveName} (${effectiveEmail}, ${userRole} APSkincek).
5. Ditanya siapa admin: Daftarkan admin dari data di atas.
6. Ditanya perawatan / rekomendasi produk: Berikan solusi singkat dan rekomendasikan dari katalog.`;

        const messages = [
            { role: 'system', content: systemPrompt }
        ];

        // Sanitize history to prevent duplicates
        if (Array.isArray(history) && history.length > 0) {
            let lastRole = null;
            for (const h of history.slice(-4)) {
                if (h && h.content && typeof h.content === 'string') {
                    const content = h.content.trim();
                    if (!content) continue;
                    if (h.role === 'user' && content.toLowerCase() === message.trim().toLowerCase()) {
                        continue;
                    }
                    const role = h.role === 'assistant' ? 'assistant' : 'user';
                    if (role === lastRole) continue;
                    messages.push({ role, content });
                    lastRole = role;
                }
            }
        }

        messages.push({ role: 'user', content: message.trim() });

        const chatCompletion = await callChatWithFailover(messages);

        let responseText = chatCompletion.choices[0]?.message?.content || "Halo! Maaf, saya sedang memproses sistem saat ini. Silakan coba lagi sebentar ya.";
        
        // Clean formatting
        responseText = responseText
            .replace(/\p{Extended_Pictographic}/gu, '')
            .replace(/^#+\s+/gm, '')
            .replace(/[`~]/g, '')
            .replace(/[^\S\r\n]{2,}/g, ' ')
            .replace(/\n{3,}/g, '\n\n')
            .trim();

        res.json({ reply: responseText });
    } catch (error) {
        console.error("Error calling Groq API:", error);
        res.status(500).json({ error: 'Failed to process request. Please check API Key or try again later.' });
    }
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Chatbot Server running on http://localhost:${PORT}`);
});
