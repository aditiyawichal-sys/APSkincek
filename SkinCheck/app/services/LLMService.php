<?php
require_once '../config/api.php';

class LLMService {
    public static function getAiConfig() {
        $configFile = __DIR__ . '/../../config/ai_config.json';
        if (file_exists($configFile)) {
            $data = json_decode(file_get_contents($configFile), true);
            if (is_array($data)) return $data;
        }
        return [
            'provider' => 'groq',
            'model' => 'qwen/qwen3.8-27b',
            'primary_api_key' => defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '',
            'backup_api_key_1' => '',
            'backup_api_key_2' => '',
            'backup_api_key_3' => '',
            'auto_failover' => true
        ];
    }

    public static function generateResponse($systemPrompt, $chatHistory, $userMessage) {
        $config = self::getAiConfig();
        $provider = $config['provider'] ?? 'groq';
        $model = $config['model'] ?? 'qwen/qwen3.8-27b';

        // Prepare pool of keys
        $candidateKeys = [
            ['label' => 'Primary Key', 'key' => trim($config['primary_api_key'] ?? '')],
            ['label' => 'Backup 1', 'key' => trim($config['backup_api_key_1'] ?? '')],
            ['label' => 'Backup 2', 'key' => trim($config['backup_api_key_2'] ?? '')],
            ['label' => 'Backup 3', 'key' => trim($config['backup_api_key_3'] ?? '')]
        ];

        $keysToTry = array_filter($candidateKeys, fn($k) => !empty($k['key']) && strlen($k['key']) > 5);

        if (empty($keysToTry)) {
            return "Halo! Asisten AI belum dapat menjawab karena belum ada API Key yang dikonfigurasi. Silakan tambahkan API Key di panel Admin -> Konfigurasi AI.";
        }

        $lastError = '';

        foreach ($keysToTry as $keyItem) {
            $apiKey = $keyItem['key'];

            // Auto-detect provider per key
            $keyProvider = 'gemini';
            if (str_starts_with($apiKey, 'gsk_')) {
                $keyProvider = 'groq';
            } elseif (str_starts_with($apiKey, 'sk-')) {
                $keyProvider = 'openai';
            } elseif (str_starts_with($apiKey, 'AIza') || str_starts_with($apiKey, 'AQ.')) {
                $keyProvider = 'gemini';
            } elseif ($provider === 'groq') {
                $keyProvider = 'groq';
            }

            if ($keyProvider === 'groq') {
                $groqModel = (!empty($model) && !str_contains($model, 'gemini') && !str_contains($model, 'gpt')) ? $model : 'qwen/qwen3.8-27b';
                $messages = [
                    ['role' => 'system', 'content' => $systemPrompt]
                ];
                foreach ($chatHistory as $msg) {
                    $messages[] = [
                        'role' => ($msg['role'] === 'user' ? 'user' : 'assistant'),
                        'content' => $msg['text'] ?? ''
                    ];
                }
                $messages[] = ['role' => 'user', 'content' => $userMessage];

                $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
                $payload = [
                    'model' => $groqModel,
                    'messages' => $messages,
                    'max_tokens' => 800
                ];

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey
                ]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && $response) {
                    $resJson = json_decode($response, true);
                    $text = $resJson['choices'][0]['message']['content'] ?? '';
                    if (!empty($text)) {
                        return self::cleanOutput($text);
                    }
                }
                $lastError = "HTTP $httpCode";
            } elseif ($keyProvider === 'openai') {
                $oaiModel = (!empty($model) && str_starts_with($model, 'gpt')) ? $model : 'gpt-4o-mini';
                $messages = [
                    ['role' => 'system', 'content' => $systemPrompt]
                ];
                foreach ($chatHistory as $msg) {
                    $messages[] = [
                        'role' => ($msg['role'] === 'user' ? 'user' : 'assistant'),
                        'content' => $msg['text'] ?? ''
                    ];
                }
                $messages[] = ['role' => 'user', 'content' => $userMessage];

                $ch = curl_init('https://api.openai.com/v1/chat/completions');
                $payload = [
                    'model' => $oaiModel,
                    'messages' => $messages,
                    'max_tokens' => 800
                ];

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey
                ]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && $response) {
                    $resJson = json_decode($response, true);
                    $text = $resJson['choices'][0]['message']['content'] ?? '';
                    if (!empty($text)) {
                        return self::cleanOutput($text);
                    }
                }
                $lastError = "HTTP $httpCode";
            } else {
                // Gemini Provider
                $geminiModel = (!empty($model) && str_contains($model, 'gemini')) ? $model : 'gemini-1.5-flash';
                $contents = [];
                foreach ($chatHistory as $msg) {
                    $contents[] = [
                        'role' => ($msg['role'] === 'user' ? 'user' : 'model'),
                        'parts' => [['text' => $msg['text'] ?? '']]
                    ];
                }
                $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

                $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $geminiModel . ':generateContent?key=' . $apiKey;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents' => $contents
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && $response) {
                    $resJson = json_decode($response, true);
                    $text = $resJson['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    if (!empty($text)) {
                        return self::cleanOutput($text);
                    }
                }
                $lastError = "HTTP $httpCode";
            }

            if (empty($config['auto_failover'])) {
                break;
            }
        }

        return "Maaf, sistem AI sedang tidak dapat merespons permintaan saat ini. Silakan coba beberapa saat lagi. ($lastError)";
    }

    private static function cleanOutput($text) {
        $text = preg_replace('/(\*\*|__)(.*?)\1/s', '$2', $text);
        $text = preg_replace('/(\*|_)(.*?)\1/s', '$2', $text);
        $text = preg_replace('/^#+\s+/m', '', $text);
        $text = preg_replace('/[`\*~#]/', '', $text);
        $text = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $text);
        return trim($text);
    }
}
