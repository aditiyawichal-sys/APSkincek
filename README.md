# APSkincek - Aplikasi Pemeriksaan & Edukasi Kesehatan Kulit Wajah (SkinCheck)

APSkincek adalah platform berbasis web untuk analisis kesehatan kulit wajah, pemeriksaan cakupan sunscreen menggunakan Computer Vision (Python AI), serta asisten chatbot dermatologi berbasis AI (Groq Qwen 27B) yang terintegrasi secara real-time dengan basis data sistem.

---

## Fitur Utama

- **Analisis Kulit Wajah AI (Face Scan)**: Mendeteksi wajah secara otomatis dan menghitung skor jerawat (acne), hidrasi, tekstur, serta kesehatan kulit secara menyeluruh menggunakan Python OpenCV.
- **Pemeriksaan Cakupan Sunscreen**: Memeriksa persebaran dan ketebalan aplikasi tabir surya di wajah dengan visualisasi heatmap.
- **AI Chatbot Asisten Pintar**: Chatbot cepat (Groq LLM) yang dapat membaca konteks database pengguna (riwayat scan, rekomendasi produk, dan data statistik).
- **Verifikasi OTP WhatsApp**: Pengamanan registrasi dan verifikasi akun menggunakan OTP via Fonnte WhatsApp API.
- **Admin Dashboard**: Manajemen pengguna, rekap statistik scan, katalog produk perawatan kulit, dan konfigurasi API AI.

---

## Arsitektur & Teknologi

- **Backend**: PHP 8.2 (MVC Architecture), MySQL / MariaDB (XAMPP)
- **Chatbot Server**: Node.js & Express.js (Port 3000), Groq SDK
- **Computer Vision AI**: Python 3.13, OpenCV (`cv2`), NumPy
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5, FontAwesome / Bootstrap Icons

---

## Panduan Instalasi & Menjalankan

### 1. Prasyarat
- [XAMPP](https://www.apachefriends.org/) (Apache & MySQL)
- [Node.js](https://nodejs.org/) (v18+)
- [Python](https://www.python.org/) (v3.10+ dengan `pip`)

### 2. Setup Database
1. Buka **phpMyAdmin** di `http://localhost/phpmyadmin/`.
2. Buat database baru bernama: `skincheck_db`.
3. Import file `SkinCheck/database/schema.sql`.
4. (Opsional) Import file `SkinCheck/database/seed.sql` untuk data awal.

### 3. Setup Python Dependencies
Buka terminal dan jalankan:
```bash
cd SkinCheck/python_ai
pip install opencv-python numpy
```

### 4. Setup Node.js Chatbot Server
Buka terminal dan jalankan:
```bash
cd SkinCheck/nodejs-chatbot
npm install
```

### 5. Konfigurasi API (Kunci Rahasia)
Salin file konfigurasi contoh dan isi kredensial Anda:
- Salin `SkinCheck/config/ai_config.example.json` menjadi `SkinCheck/config/ai_config.json`, lalu masukkan API Key [Groq](https://console.groq.com/).
- Salin `SkinCheck/config/fonnte_config.example.json` menjadi `SkinCheck/config/fonnte_config.json`, lalu masukkan Token [Fonnte](https://fonnte.com/) untuk pengiriman OTP WhatsApp.
- Salin `SkinCheck/config/api.example.php` menjadi `SkinCheck/config/api.php`.

### 6. Menjalankan Aplikasi
Cukup klik ganda file **`start_all.bat`** di direktori utama `APSkincek/`:
- Skrip akan otomatis mengaktifkan Apache & MySQL.
- Server Chatbot Node.js akan dijalankan pada port `3000`.
- Browser otomatis terbuka di: `http://localhost/APSkincek/`.

---

## Lisensi
Proyek ini dikembangkan untuk keperluan edukasi dan pemeriksaan kesehatan kulit.

