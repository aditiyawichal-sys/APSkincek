<?php

class OtpVerification {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Membuat kode OTP 6-digit baru dan menyimpannya ke database
     */
    public function createOtp($phone, $type, $payload = null, $email = null) {
        // Standarisasi format nomor
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strpos($cleanPhone, '62') === 0) {
            $standardPhone = '0' . substr($cleanPhone, 2);
        } else if (strpos($cleanPhone, '0') === 0) {
            $standardPhone = $cleanPhone;
        } else {
            $standardPhone = '0' . $cleanPhone;
        }

        // Generate 6 digit angka acak
        $otp = (string)random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', time() + (5 * 60)); // Berlaku 5 menit
        $payloadJson = $payload !== null ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null;

        // Nonaktifkan OTP aktif sebelumnya untuk nomor & tipe yang sama
        $stmtDeactivate = $this->db->prepare("UPDATE otp_verifications SET is_used = 1 WHERE phone = :phone AND type = :type AND is_used = 0");
        $stmtDeactivate->bindParam(':phone', $standardPhone);
        $stmtDeactivate->bindParam(':type', $type);
        $stmtDeactivate->execute();

        // Simpan OTP baru
        $stmt = $this->db->prepare("
            INSERT INTO otp_verifications (phone, email, otp, type, payload, expires_at, is_used) 
            VALUES (:phone, :email, :otp, :type, :payload, :expires_at, 0)
        ");
        $stmt->bindParam(':phone', $standardPhone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':otp', $otp);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':payload', $payloadJson);
        $stmt->bindParam(':expires_at', $expiresAt);
        $stmt->execute();

        return [
            'id' => $this->db->lastInsertId(),
            'phone' => $standardPhone,
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'expires_timestamp' => time() + 300
        ];
    }

    /**
     * Verifikasi kode OTP yang dimasukkan user
     */
    public function verifyOtp($phone, $otp, $type) {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strpos($cleanPhone, '62') === 0) {
            $standardPhone = '0' . substr($cleanPhone, 2);
        } else if (strpos($cleanPhone, '0') === 0) {
            $standardPhone = $cleanPhone;
        } else {
            $standardPhone = '0' . $cleanPhone;
        }

        $cleanOtp = trim($otp);

        // Cari OTP yang cocok, belum kedaluwarsa, dan belum digunakan
        $stmt = $this->db->prepare("
            SELECT * FROM otp_verifications 
            WHERE phone = :phone AND otp = :otp AND type = :type AND is_used = 0 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->bindParam(':phone', $standardPhone);
        $stmt->bindParam(':otp', $cleanOtp);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            // Cek apakah nomor punya OTP yang salah atau sudah expired
            return [
                'valid' => false,
                'error' => 'Kode OTP yang Anda masukkan salah atau tidak valid.'
            ];
        }

        // Cek kedaluwarsa (5 menit)
        if (strtotime($record['expires_at']) < time()) {
            return [
                'valid' => false,
                'error' => 'Kode OTP telah kedaluwarsa (melebihi batas 5 menit). Silakan minta kode OTP baru.'
            ];
        }

        // Tandai sebagai digunakan
        $stmtUpdate = $this->db->prepare("UPDATE otp_verifications SET is_used = 1 WHERE id = :id");
        $stmtUpdate->bindParam(':id', $record['id']);
        $stmtUpdate->execute();

        return [
            'valid' => true,
            'record' => $record,
            'payload' => !empty($record['payload']) ? json_decode($record['payload'], true) : null
        ];
    }

    /**
     * Cek apakah user diperbolehkan mengirim ulang OTP (cooldown 60 detik)
     */
    public function canResend($phone, $type) {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strpos($cleanPhone, '62') === 0) {
            $standardPhone = '0' . substr($cleanPhone, 2);
        } else if (strpos($cleanPhone, '0') === 0) {
            $standardPhone = $cleanPhone;
        } else {
            $standardPhone = '0' . $cleanPhone;
        }

        $stmt = $this->db->prepare("
            SELECT created_at FROM otp_verifications 
            WHERE phone = :phone AND type = :type 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->bindParam(':phone', $standardPhone);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        $last = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last) {
            $lastTime = strtotime($last['created_at']);
            $diff = time() - $lastTime;
            $cooldown = 60; // 60 detik cooldown
            if ($diff < $cooldown) {
                return [
                    'allowed' => false,
                    'wait_seconds' => $cooldown - $diff
                ];
            }
        }

        return ['allowed' => true, 'wait_seconds' => 0];
    }
}
