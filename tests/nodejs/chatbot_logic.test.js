const test = require('node:test');
const assert = require('node:assert/strict');

// Fungsi-fungsi inti dari Chatbot AI Logic (SkinCheck)
function detectProvider(apiKey, defaultProvider = 'groq') {
    if (!apiKey) return defaultProvider;
    const key = apiKey.trim();
    if (key.startsWith('gsk_')) return 'groq';
    if (/^sk-[0-9a-f]{32}$/i.test(key)) return 'deepseek';
    if (key.startsWith('sk-')) return 'openai';
    if (key.startsWith('AIza') || key.startsWith('AQ.')) return 'gemini';
    if (defaultProvider === 'deepseek') return 'deepseek';
    if (defaultProvider === 'gemini') return 'gemini';
    if (defaultProvider === 'openai') return 'openai';
    return key.length > 20 ? 'gemini' : defaultProvider;
}

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

const isCustomerCountOnlyQuery = (txt) => {
    return (
        (txt.includes('berapa customer') || 
         txt.includes('ada berapa customer') || 
         txt.includes('jumlah customer') || 
         txt.includes('total customer') || 
         txt.includes('banyak customer')) &&
        !txt.includes('siapa') &&
        !txt.includes('daftar') &&
        !txt.includes('nama') &&
        !txt.includes('rincian')
    );
};

// ==========================================
// TEST SUITE: NODE.JS CHATBOT AI LOGIC
// ==========================================

test('1. detectProvider: Mengenali Groq API Key (awalan gsk_)', () => {
    assert.equal(detectProvider('gsk_mock_test_key_for_groq_unit_test'), 'groq');
});

test('2. detectProvider: Mengenali Google Gemini API Key (awalan AIza atau AQ.)', () => {
    assert.equal(detectProvider('AIzaMockTestGeminiKey12345'), 'gemini');
    assert.equal(detectProvider('AQ.MockTestVertexGeminiKey12345'), 'gemini');
});

test('3. detectProvider: Mengenali OpenAI API Key (awalan sk-)', () => {
    assert.equal(detectProvider('sk-mock_openai_test_key_sample'), 'openai');
});

test('4. detectProvider: Fallback ke default provider jika key kosong', () => {
    assert.equal(detectProvider('', 'groq'), 'groq');
    assert.equal(detectProvider(null, 'gemini'), 'gemini');
});

test('5. isCodingQuery: Mendeteksi permintaan coding yang dilarang guardrails', () => {
    assert.equal(isCodingQuery('tuliskan coding python untuk menghitung luas lingkaran'), true);
    assert.equal(isCodingQuery('buatkan kode python'), true);
    assert.equal(isCodingQuery('bikin script python'), true);
});

test('6. isCodingQuery: Mengizinkan pertanyaan seputar kesehatan kulit normal', () => {
    assert.equal(isCodingQuery('bagaimana cara merawat kulit wajah berjerawat?'), false);
    assert.equal(isCodingQuery('rekomendasikan sunscreen untuk kulit berminyak'), false);
});

test('7. isCustomerCountOnlyQuery: Pertanyaan jumlah customer hanya menampilkan total', () => {
    assert.equal(isCustomerCountOnlyQuery('ada berapa customer?'), true);
    assert.equal(isCustomerCountOnlyQuery('jumlah customer saat ini'), true);
    assert.equal(isCustomerCountOnlyQuery('total customer di sistem'), true);
});

test('8. isCustomerCountOnlyQuery: Pertanyaan rincian nama customer TIDAK dianggap count only', () => {
    assert.equal(isCustomerCountOnlyQuery('siapa saja customer nya?'), false);
    assert.equal(isCustomerCountOnlyQuery('tampilkan daftar customer'), false);
    assert.equal(isCustomerCountOnlyQuery('sebutkan nama-nama customer'), false);
});
