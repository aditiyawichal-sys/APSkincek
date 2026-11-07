<?php

class FonnteHelper {
    private static function getConfigPath() {
        return __DIR__ . '/../../config/fonnte_config.json';
    }

    public static function getConfig() {
        $path = self::getConfigPath();
        $defaults = [
            'api_token' => '',
            'endpoint' => 'https://api.fonnte.com/send',
            'country_code' => '62',
            'enable_registration_notification' => true,
            'otp_message' => "Halo! Kode OTP Anda untuk registrasi/login adalah: *{otp}*. Demi keamanan, jangan bagikan kode ini kepada siapa pun, termasuk pihak admin. Kode ini berlaku selama 5 menit.",
            'welcome_message' => "Halo *{name}*! 👋\n\nSelamat datang di *APSkincek* (Aplikasi Pemeriksaan & Edukasi Kulit Wajah).\n\nAkun Anda telah berhasil terdaftar:\n👤 *Nama*: {name}\n📧 *Email*: {email}\n📱 *No. WhatsApp*: {phone}\n🛡️ *Tipe Akun*: {role}\n\nSilakan login dan mulai periksa kesehatan kulit Anda di:\n🌐 {login_url}\n\nTerima kasih dan jaga selalu kesehatan kulit Anda! ✨",
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (file_exists($path)) {
            $content = file_get_contents($path);
            $json = json_decode($content, true);
            if (is_array($json)) {
                return array_merge($defaults, $json);
            }
        }
        return $defaults;
    }

    public static function saveConfig($data) {
        $path = self::getConfigPath();
        $current = self::getConfig();
        $updated = array_merge($current, $data);
        $updated['updated_at'] = date('Y-m-d H:i:s');
        return file_put_contents($path, json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) !== false;
    }

    public static function isConfigured() {
        $config = self::getConfig();
        return !empty(trim($config['api_token'] ?? ''));
    }

    /**
     * Membersihkan dan menstandarisasi nomor telepon/WA ke format yang diterima Fonnte
     */
    public static function normalizePhone($phone) {
        if (empty($phone)) return '';
        
        // Hapus karakter non-digit selain '+'
        $clean = preg_replace('/[^0-9]/', '', $phone);
        
        // Jika berawalan '08', bisa dikonversi ke '628' untuk standarisasi internasional Fonnte
        if (strpos($clean, '08') === 0) {
            $clean = '628' . substr($clean, 2);
        } else if (strpos($clean, '8') === 0 && strlen($clean) <= 12) {
            $clean = '62' . $clean;
        }

        return $clean;
    }

    /**
     * Mengirim pesan WhatsApp via Fonnte API
     */
    public static function sendWhatsApp($target, $message) {
        $config = self::getConfig();
        $token = trim($config['api_token'] ?? '');

        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Fonnte API Token belum dikonfigurasi. Silakan isi token di menu Admin > Konfigurasi WhatsApp (Fonnte).',
                'http_code' => 0
            ];
        }

        $normalizedTarget = self::normalizePhone($target);
        if (empty($normalizedTarget)) {
            return [
                'success' => false,
                'message' => 'Nomor tujuan WhatsApp tidak valid.',
                'http_code' => 0
            ];
        }

        $endpoint = !empty($config['endpoint']) ? $config['endpoint'] : 'https://api.fonnte.com/send';
        $countryCode = !empty($config['country_code']) ? $config['country_code'] : '62';

        $postData = [
            'target' => $normalizedTarget,
            'message' => $message,
            'countryCode' => $countryCode
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $token
        ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'message' => 'Koneksi ke Fonnte gagal: ' . $curlError,
                'http_code' => $httpCode,
                'raw' => null
            ];
        }

        $decoded = json_decode($response, true);
        $status = false;
        $msg = 'Pengiriman pesan via Fonnte diproses.';

        if ($httpCode === 200 && is_array($decoded)) {
            if (isset($decoded['status']) && ($decoded['status'] === true || $decoded['status'] === 'true' || $decoded['status'] === 1)) {
                $status = true;
                $msg = 'Pesan WhatsApp berhasil dikirim ke ' . $normalizedTarget . '!';
            } else {
                $msg = $decoded['reason'] ?? $decoded['detail'] ?? 'Fonnte mengembalikan status false.';
            }
        } else {
            $msg = 'HTTP ' . $httpCode . ': ' . ($response ?: 'Gagal terhubung ke Fonnte.');
        }

        return [
            'success' => $status,
            'message' => $msg,
            'http_code' => $httpCode,
            'raw' => $decoded ?? $response
        ];
    }

    /**
     * Mengirim notifikasi selamat datang saat user berhasil registrasi
     */
    public static function sendRegistrationNotification($userData) {
        $config = self::getConfig();

        // Cek apakah notifikasi registrasi diaktifkan dan token sudah diisi
        if (empty($config['enable_registration_notification']) || !self::isConfigured()) {
            return false;
        }

        $phone = $userData['phone'] ?? '';
        if (empty($phone)) {
            return false;
        }

        $roleText = ($userData['role'] ?? 'user') === 'admin' ? 'Administrator' : 'Customer (Pelanggan)';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $loginUrl = 'http://' . $host . '/APSkincek/SkinCheck/public/login';

        $template = $config['welcome_message'] ?? '';
        $message = str_replace(
            ['{name}', '{email}', '{phone}', '{role}', '{login_url}', '{date}'],
            [
                $userData['name'] ?? '',
                $userData['email'] ?? '',
                $userData['phone'] ?? '',
                $roleText,
                $loginUrl,
                date('d-m-Y H:i')
            ],
            $template
        );

        return self::sendWhatsApp($phone, $message);
    }

    /**
     * Mengirim kode OTP verifikasi WhatsApp via Fonnte
     */
    public static function sendOtp($target, $otp) {
        $config = self::getConfig();
        $template = $config['otp_message'] ?? "Halo! Kode OTP Anda untuk registrasi/login adalah: *{otp}*. Demi keamanan, jangan bagikan kode ini kepada siapa pun, termasuk pihak admin. Kode ini berlaku selama 5 menit.";
        $message = str_replace('{otp}', $otp, $template);
        return self::sendWhatsApp($target, $message);
    }
}
