<?php

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($name, $email, $password, $role = 'user', $phone = null) {
        // Create UUID/CHAR(36)
        $id = $this->generateUuid();
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Ensure role is valid
        $validRoles = ['user', 'customer', 'admin'];
        if (!in_array($role, $validRoles)) {
            $role = 'user';
        }
        
        $stmt = $this->db->prepare("INSERT INTO users (id, name, email, phone, password, role) VALUES (:id, :name, :email, :phone, :password, :role)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        
        return $stmt->execute();
    }
    
    private function generateUuid() {
        // Fallback simple UUID v4 generator for MySQL CHAR(36)
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function getAllUsers() {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomers() {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = 'user' OR role = 'customer' ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdmins() {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = 'admin' ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserStats() {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_users,
                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admins,
                SUM(CASE WHEN role = 'user' OR role = 'customer' THEN 1 ELSE 0 END) as total_customers,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as total_active
            FROM users
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUserStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE users SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateUserRole($id, $role) {
        $stmt = $this->db->prepare("UPDATE users SET role = :role WHERE id = :id");
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateUser($id, $name, $email, $role = null, $password = null, $phone = null) {
        $validRoles = ['user', 'customer', 'admin'];
        if ($role && !in_array($role, $validRoles)) {
            $role = 'user';
        }

        $query = "UPDATE users SET name = :name, email = :email";
        if ($phone !== null) {
            $query .= ", phone = :phone";
        }
        if (!empty($role)) {
            $query .= ", role = :role";
        }
        if (!empty($password)) {
            $query .= ", password = :password";
        }
        $query .= " WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id);

        if ($phone !== null) {
            $stmt->bindParam(':phone', $phone);
        }
        if (!empty($role)) {
            $stmt->bindParam(':role', $role);
        }
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hashed);
        }

        return $stmt->execute();
    }

    public function findByPhone($phone) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = :phone LIMIT 1");
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByPhoneExceptId($phone, $id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = :phone AND id != :id LIMIT 1");
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmailExceptId($email, $id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND id != :id LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteUser($id) {
        try {
            // Hapus log aktivitas pengguna
            $stmtLogs = $this->db->prepare("DELETE FROM activity_logs WHERE user_id = :id");
            $stmtLogs->bindParam(':id', $id);
            $stmtLogs->execute();

            // Ambil ID scan untuk membersihkan relasi jika FK cascade belum aktif
            $stmtScans = $this->db->prepare("SELECT id FROM skin_scans WHERE user_id = :id");
            $stmtScans->bindParam(':id', $id);
            $stmtScans->execute();
            $scanIds = $stmtScans->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($scanIds)) {
                $placeholders = implode(',', array_fill(0, count($scanIds), '?'));
                
                $stmtAnalysis = $this->db->prepare("DELETE FROM analysis_results WHERE scan_id IN ($placeholders)");
                $stmtAnalysis->execute($scanIds);

                $stmtSunscreen = $this->db->prepare("DELETE FROM sunscreen_results WHERE scan_id IN ($placeholders)");
                $stmtSunscreen->execute($scanIds);

                $stmtRec = $this->db->prepare("DELETE FROM recommendations WHERE scan_id IN ($placeholders)");
                $stmtRec->execute($scanIds);

                $stmtDelScans = $this->db->prepare("DELETE FROM skin_scans WHERE user_id = :id");
                $stmtDelScans->bindParam(':id', $id);
                $stmtDelScans->execute();
            }

            // Hapus pengguna dari tabel users
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
}
