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

function detectProvider(apiKey, defaultProvider = 'groq') {
    if (!apiKey) return defaultProvider;
    const key = apiKey.trim();
    if (key.startsWith('gsk_')) return 'groq';
    if (key.startsWith('sk-')) return 'openai';
    if (key.startsWith('AIza') || key.startsWith('AQ.')) return 'gemini';
    if (defaultProvider === 'gemini') return 'gemini';
    if (defaultProvider === 'openai') return 'openai';
    return key.length > 20 ? 'gemini' : defaultProvider;
}

async function callGroq(apiKey, messages, model = 'qwen/qwen3.8-27b') {
    const client = new Groq({ 
        apiKey: apiKey,
        maxRetries: 0,
        timeout: 4500
    });

    return await client.chat.completions.create({
        messages: messages,
        model: model,
        max_tokens: 300,
        temperature: 0.1
    });
}

async function callGemini(apiKey, messages, model = 'gemini-1.5-flash') {
    const systemMsg = messages.find(m => m.role === 'system');
    const nonSystem = messages.filter(m => m.role !== 'system');
    
    const contents = nonSystem.map(m => ({
        role: m.role === 'assistant' ? 'model' : 'user',
        parts: [{ text: m.content }]
    }));
    
    const payload = {
        contents: contents,
        generationConfig: {
            maxOutputTokens: 300,
            temperature: 0.1
        }
    };
    
    if (systemMsg && systemMsg.content) {
        payload.system_instruction = {
            parts: [{ text: systemMsg.content }]
        };
    }
    
    const geminiModel = (model && model.includes('gemini')) ? model : 'gemini-1.5-flash';
    const url = `https://generativelanguage.googleapis.com/v1beta/models/${geminiModel}:generateContent?key=${apiKey}`;
    
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        signal: AbortSignal.timeout(5000)
    });
    
    if (!res.ok) {
        const errText = await res.text();
        throw new Error(`Gemini HTTP ${res.status}: ${errText}`);
    }
    
    const data = await res.json();
    const replyText = data.candidates?.[0]?.content?.parts?.[0]?.text || '';
    if (!replyText) {
        throw new Error('Gemini tidak mengembalikan teks respon.');
    }
    
    return {
        choices: [
            {
                message: {
                    content: replyText
                }
            }
        ]
    };
}

async function callOpenAI(apiKey, messages, model = 'gpt-4o-mini') {
    const oaiModel = (model && model.startsWith('gpt')) ? model : 'gpt-4o-mini';
    const res = await fetch('https://api.openai.com/v1/chat/completions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${apiKey}`
        },
        body: JSON.stringify({
            model: oaiModel,
            messages: messages,
            max_tokens: 300,
            temperature: 0.1
        }),
        signal: AbortSignal.timeout(5000)
    });
    
    if (!res.ok) {
        const errText = await res.text();
        throw new Error(`OpenAI HTTP ${res.status}: ${errText}`);
    }
    
    return await res.json();
}

async function callChatWithFailover(messages) {
    const config = getAiConfig();

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
        const provider = detectProvider(item.key, config.provider || 'groq');
        try {
            let completion;
            if (provider === 'groq') {
                const groqModel = (config.model && !config.model.includes('gemini') && !config.model.includes('gpt')) 
                    ? config.model 
                    : 'qwen/qwen3.8-27b';
                completion = await callGroq(item.key, messages, groqModel);
            } else if (provider === 'openai') {
                completion = await callOpenAI(item.key, messages, config.model);
            } else {
                completion = await callGemini(item.key, messages, config.model);
            }

            return completion;
        } catch (err) {
            console.warn(`[Chatbot FAILOVER] ${item.label} [${provider}] (${err.status || err.message}). Mencoba kunci berikutnya...`);
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

        // Khusus pertanyaan meminta coding / kode pemrograman (misal: "tuliskan coding python untuk menuliskan nama anda?")
        const isCodingQuery = (txt) => {
            const lower = txt.toLowerCase();
            return (
                lower.includes('coding python') ||
                lower.includes('koding python') ||
                lower.includes('kode python') ||
                lower.includes('script python') ||
                (lower.includes('coding') && (lower.includes('nama anda') || lower.includes('tuliskan') || lower.includes('buatkan') || lower.includes('bikin'))) ||
                (lower.includes('koding') && (lower.includes('nama anda') || lower.includes('tuliskan') || lower.includes('buatkan') || lower.includes('bikin'))) ||
                (lower.includes('kode') && (lower.includes('nama anda') || lower.includes('pemrograman') || lower.includes('tuliskan') || lower.includes('buatkan')))
            );
        };

        if (isCodingQuery(message)) {
            return res.json({ 
                reply: "Maaf, saya tidak dapat menjawab pertanyaan tersebut. Saya adalah asisten khusus kesehatan kulit dan layanan aplikasi APSkincek, dan tidak melayani pembuatan kode atau pemrograman." 
            });
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
            userFaceScan = `${scanDate}, Skor Jerawat: ${dbCtx.faceScan.acne_score ?? 0}, Hidrasi: ${dbCtx.faceScan.hydration_level ?? 0}%, Skor Kulit: ${dbCtx.faceScan.overall_health ?? 0}%`;
        }

        let userSunscreenScan = 'Belum pernah cek sunscreen.';
        if (dbCtx.sunscreenScan) {
            const scanDate = dbCtx.sunscreenScan.created_at ? new Date(dbCtx.sunscreenScan.created_at).toLocaleDateString('id-ID') : '-';
            userSunscreenScan = `${scanDate}, Cakupan: ${dbCtx.sunscreenScan.coverage_score}%`;
        }

        // 3. Compact catalog
        let catalogSummary = 'Katalog produk kosong.';
        if (dbCtx.products && dbCtx.products.length > 0) {
            catalogSummary = dbCtx.products.map(p => `- ${p.name} [${p.category}]: ${p.description}`).join('\n');
        }

        // 4. Compact stats
        const stats = dbCtx.systemStats || { totalCustomers: 0, totalAdmins: 0, totalUsers: 0, totalScans: 0, faceScans: 0, sunscreenScans: 0 };
        
        const customerList = (stats.customers && stats.customers.length > 0)
            ? stats.customers.map((c, i) => `${i + 1}. ${c.name} - email: ${c.email} - Total Scan: ${c.total_scans || 0} kali`).join('\n')
            : 'Belum ada customer terdaftar.';

        const faceScanList = (stats.faceScanUsers && stats.faceScanUsers.length > 0)
            ? stats.faceScanUsers.map((u, i) => `${i + 1}. ${u.name} - ${u.total_scans} kali scan`).join('\n')
            : 'Belum ada.';

        const sunscreenScanList = (stats.sunscreenScanUsers && stats.sunscreenScanUsers.length > 0)
            ? stats.sunscreenScanUsers.map((u, i) => `${i + 1}. ${u.name} - ${u.total_scans} kali scan`).join('\n')
            : 'Belum ada.';

        const adminList = (stats.admins && stats.admins.length > 0)
            ? stats.admins.map((a, i) => `${i + 1}. ${a.name} - email: ${a.email}`).join('\n')
            : 'Belum ada.';

        const recentScansList = (stats.recentScans && stats.recentScans.length > 0)
            ? stats.recentScans.slice(0, 5).map(s => {
                const dt = new Date(s.created_at).toLocaleDateString('id-ID');
                const info = s.scan_type === 'face' ? `Jerawat: ${s.acne_score ?? '-'}` : `Cakupan: ${s.coverage_score}%`;
                return `#${s.id}: ${s.user_name || 'User'} - ${s.scan_type === 'face' ? 'Wajah' : 'Sunscreen'} - ${info} - ${dt}`;
            }).join('\n')
            : 'Belum ada scan.';

        // 5. Lean, focused system prompt (~400 tokens)
        const systemPrompt = `Anda SkinCheck AI Assistant resmi aplikasi APSkincek. Jawab langsung, akurat, to-the-point sesuai data database berikut. Dilarang bertele-tele, dilarang menggunakan emoji, dan DILARANG MENGGUNAKAN TANDA KURUNG () saat menyebutkan nama orang, email, atau peran seseorang. Sebutkan secara langsung dan mengalir.

[DATA DATABASE RESMI APSKINCEK]
- User Aktif Chat: ${effectiveName}, email: ${effectiveEmail}, peran: ${userRole}
- Scan Terakhir User: ${userFaceScan} | Sunscreen: ${userSunscreenScan}
- Total Pengguna: ${stats.totalUsers} orang, terdiri dari ${stats.totalCustomers} Customer dan ${stats.totalAdmins} Admin
- Total Scan: ${stats.totalScans} kali, rincian: ${stats.faceScans} scan wajah dan ${stats.sunscreenScans} cek sunscreen

[DAFTAR SELURUH CUSTOMER: ${stats.totalCustomers} Orang]
${customerList}

[CUSTOMER SCAN WAJAH]
${faceScanList}

[CUSTOMER CEK SUNSCREEN]
${sunscreenScanList}

[DAFTAR ADMINISTRATOR: ${stats.totalAdmins} Orang]
${adminList}

[5 SCAN TERAKHIR]
${recentScansList}

[KATALOG PRODUK]
${catalogSummary}

ATURAN MENJAWAB:
1. SANGAT PENTING: DILARANG MENGGUNAKAN TANDA KURUNG () saat menyebutkan identitas seseorang. Langsung saja sebutkan namanya, perannya, atau datanya secara mengalir tanpa tanda kurung.
2. Ditanya siapa saya: Jawab langsung tanpa tanda kurung: "Anda adalah ${effectiveName}, ${userRole} APSkincek dengan email ${effectiveEmail}."
3. Ditanya siapa yang scan wajah / sunscreen: Sebutkan nama dan jumlah scan langsung tanpa tanda kurung. Contoh: "1. michael - 10 kali scan".
4. Ditanya siapa admin / daftar customer: Sebutkan nama dan email langsung tanpa tanda kurung.
5. Ditanya berapa customer / total scan: Sebutkan langsung jumlah dan rinciannya.
6. Ditanya siapa anda: "Saya adalah SkinCheck AI Assistant resmi aplikasi APSkincek."
7. Ditanya perawatan / rekomendasi produk: Berikan solusi singkat dan rekomendasikan dari katalog.
8. DILARANG MEMBUAT CODING / PEMROGRAMAN: Jika pengguna meminta menuliskan coding, kode pemrograman, skrip python, atau hal seputar coding (contoh: "tuliskan coding python untuk menuliskan nama anda?"), Anda DILARANG menjawab atau membuatkan kodenya. Jawablah: "Maaf, saya tidak dapat menjawab pertanyaan tersebut. Saya adalah asisten khusus kesehatan kulit dan layanan aplikasi APSkincek, dan tidak melayani pembuatan kode atau pemrograman." Hanya tolak pertanyaan yang meminta coding tersebut, pertanyaan lain seputar kulit dan sistem tetap dijawab seperti biasa.`;

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
        
        // Clean formatting: remove emojis, code ticks, and convert any parentheses to smooth text
        responseText = responseText
            .replace(/\(([^)]+)\)/g, ', $1')
            .replace(/,\s*,/g, ',')
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
