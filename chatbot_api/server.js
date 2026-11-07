const express = require('express');
const cors = require('cors');
const axios = require('axios');

const app = express();
const port = 3000;

app.use(cors());
app.use(express.json());

const API_KEY = process.env.GEMINI_API_KEY || '';

// Chatbot endpoint
app.post('/api/chat', async (req, res) => {
    try {
        const { message } = req.body;
        
        // This is where you would normally call the external AI API using the API_KEY
        // Example (commented out):
        /*
        const response = await axios.post('https://api.external-ai.com/v1/chat', {
            messages: [{ role: 'user', content: message }]
        }, {
            headers: {
                'Authorization': `Bearer ${API_KEY}`,
                'Content-Type': 'application/json'
            }
        });
        const reply = response.data.reply;
        */

        // For now, we simulate a response
        const reply = "Halo! Saya adalah asisten virtual SkinCheck. Ada yang bisa saya bantu terkait kesehatan kulit Anda?";
        
        res.json({ reply: reply, status: 'success' });
    } catch (error) {
        console.error('Error handling chat:', error);
        res.status(500).json({ error: 'Terjadi kesalahan pada server chatbot.' });
    }
});

app.listen(port, () => {
    console.log(`Chatbot server running at http://localhost:${port}`);
});
