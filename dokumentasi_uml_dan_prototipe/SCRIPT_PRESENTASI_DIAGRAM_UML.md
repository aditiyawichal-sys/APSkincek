# 🎙️ NASKAH & SCRIPT PRESENTASI RESMI SISTEM SKINCHECK
## Khusus untuk Use Case Diagram & Activity Diagram Resmi SkinCheck

Dokumen ini berisi naskah lisan kata demi kata (*word-for-word presentation script*) dan instruksi sorotan visual untuk mempresentasikan diagram resmi **SkinCheck — AI-Powered Skin Health Monitoring & Skincare Assistant** di hadapan dosen penguji, audiens, maupun rekaman video demo.

---

## 🧭 PANDUAN NAVIGASI SLIDE PRESENTASI
- **URL Presentasi Chrome:** `http://localhost/APSkincek/dokumentasi_uml_dan_prototipe/presentasi_skincheck.html` (atau klik dua kali file `presentasi_skincheck.html`)
- **Navigasi Keyboard:** 
  - `Tombol Kanan (→)` atau `Spasi`: Slide Berikutnya
  - `Tombol Kiri (←)`: Slide Sebelumnya
  - `Tombol F`: Mode Layar Penuh (Fullscreen)
  - `Tombol P`: Menampilkan / Menyembunyikan Catatan Pembicara (Presenter Notes)

---

## 🌟 SLIDE 1: PEMBUKAAN (OPENING)

- 🎯 **Fokus Layar:** Slide Judul *"SkinCheck — AI-Powered Skin Health Monitoring & Skincare Assistant"*.
- 🎙️ **Teks Narasi Lisan:**
> *"Selamat pagi / siang kepada Bapak/Ibu dosen penguji serta rekan-rekan sekalian.*  
> *Pada kesempatan hari ini, saya akan mempresentasikan perancangan sistem dan Product Requirements Document (PRD) dari aplikasi **SkinCheck**.*  
> *SkinCheck adalah platform cerdas pemantauan kesehatan kulit wajah dan evaluasi pemerataan tabir surya (*sunscreen*) yang memadukan teknologi Computer Vision (MediaPipe & OpenCV) serta Large Language Model dengan Retrieval-Augmented Generation (LLM + RAG).*  
> *Dalam presentasi ini, saya akan memaparkan secara runtut: ringkasan PRD produk, Use Case Diagram resmi sistem kami, Activity Diagram alur pemindaian wajah dan analisis kulit, arsitektur basis data, serta prototipe interaktif aplikasi. Mari kita masuk ke bagian pertama."*

---

## 📋 SLIDE 2: RINGKASAN PRD (PRODUCT REQUIREMENTS DOCUMENT)

- 🎯 **Fokus Layar:** Tab PRD & Ruang Lingkup Produk.
- 🎙️ **Teks Narasi Lisan:**
> *"Sebelum melihat diagram teknis, mari kita pahami latar belakang perancangan SkinCheck.*  
> *Banyak masyarakat kesulitan mengukur kondisi kesehatan kulitnya secara objektif, serta sering melewatkan area rentan saat memakai tabir surya sehingga perlindungan sinar UV menjadi tidak optimal.*  
> *SkinCheck hadir sebagai solusi pemantauan visual harian non-medis dengan prioritas fitur MVP (P0):*  
> *1. Autentikasi aman terintegrasi verifikasi One-Time Password (OTP) eksternal.*  
> *2. Pemindaian wajah real-time via kamera browser (WebRTC).*  
> *3. Pipeline Computer Vision untuk analisis visual lesi jerawat (acne), indeks kemerahan (redness), tekstur pori, noda hitam (hyperpigmentation), dan estimasi cakupan sunscreen.*  
> *4. Rekomendasi produk perawatan yang terikat katalog resmi (anti-halusinasi).*  
> *5. Asisten cerdas Chatbot AI dengan sistem multi-AI failover uptime 99.9%.*  
> *Penting digarisbawahi bahwa seluruh output SkinCheck adalah indikator visual perawatan kulit, dan bukan diagnosis medis."*

---

## 📊 SLIDE 3: USE CASE DIAGRAM RESMI SKINCHECK

- 🎯 **Fokus Layar:** Tampilkan gambar diagram [01_use_case_diagram_official.jpg](file:///c:/xampp/htdocs/APSkincek/dokumentasi_uml_dan_prototipe/01_use_case_diagram_official.jpg).
- 🧭 **Arah Sorotan Kursor:**
  1. Tunjuk aktor di sebelah kiri: **Customer / User** (atas) dan **Super Admin** (bawah).
  2. Tunjuk kotak batas sistem: **Sistem SkinCheck**.
  3. Sorot 5 paket use case milik Customer (Autentikasi, Scan & Analisis, Riwayat & Progress, AI & Skincare, Edukasi).
  4. Sorot 4 paket use case milik Super Admin (Manajemen User, Produk & Konten, Monitoring, Dashboard).
  5. Tunjuk 2 aktor eksternal di sebelah kanan: **Asisten AI (LLM + RAG)** dan **Kode OTP (Layanan OTP Eksternal)**.

- 🎙️ **Teks Narasi Lisan:**
> *"Berikut adalah **Use Case Diagram resmi sistem SkinCheck**. Diagram ini mendefinisikan batas fungsionalitas sistem (*system boundary*) serta interaksi antara 4 aktor utama.*  
> 
> *Di sisi kiri, terdapat **dua aktor pengguna**:*
> *1. **Customer / User**, yang merupakan pengguna umum aplikasi.*
> *2. **Super Admin**, pengelola operasional dan manajerial sistem.*
> 
> *Sedangkan di sisi kanan, terdapat **dua aktor eksternal berbasis layanan**:*
> *3. **Asisten AI (LLM + RAG)**, yang menyediakan kecerdasan komputasi dermatologi dan bahasa alami.*
> *4. **Kode OTP (Layanan OTP Eksternal)**, yang memfasilitasi pengiriman token keamanan verifikasi.*
> 
> *Mari kita bedah use case untuk **Customer / User**:*
> *- Pada paket **Autentikasi & Profil**: Pengguna dapat melakukan **Registrasi**, **Login**, **Logout**, dan **Kelola Profil**. Perhatikan bahwa use case Registrasi meng-`<<include>>` **Verifikasi Registrasi**, Login meng-`<<include>>` **Verifikasi Login**, dan Kelola Profil meng-`<<include>>` **Verifikasi Perubahan Data Sensitif**. Ketiga proses verifikasi wajib ini dieksekusi bersama aktor eksternal Layanan OTP.*
> *- Pada paket **Scan & Analisis**: Pengguna dapat **Melakukan Face Scan** yang meng-`<<include>>` **Analisis Kondisi Kulit** ke Layanan AI, **Melihat Hasil Analisis Kulit**, **Melakukan Sunscreen Check** yang meng-`<<include>>` **Analisis Sunscreen Coverage**, serta **Melihat Heatmap** area wajah.*
> *- Pada paket **Riwayat & Progress**: Pengguna dapat mengakses **Melihat Riwayat Scan** dan **Melihat Progress Tracking** untuk memantau perkembangan kesehatan kulit dari waktu ke waktu.*
> *- Pada paket **AI & Skincare**: Terdapat 3 use case interaktif yang seluruhnya meng-`<<include>>` layanan Asisten AI, yaitu: **Bertanya kepada Asisten AI**, **Mendapatkan Rekomendasi Produk**, dan **Membuat Skincare Routine** (jadwal AM/PM).*
> *- Pada paket **Edukasi**: Pengguna dapat **Melihat Konten Edukasi** seputar perawatan kulit yang valid.*
> 
> *Selanjutnya, mari kita lihat peran **Super Admin**:*
> *- Pada **Manajemen User**: Super Admin melakukan **Login Super Admin**, **Kelola User**, dan **Mengelola Status User** (aktif/nonaktif).*
> *- Pada **Produk & Konten**: Mengelola data master skincare melalui use case **Kelola Produk**, **Kelola Konten Edukasi**, dan **Kelola Rekomendasi**.*
> *- Pada **Monitoring**: Super Admin memantau transaksi melalui **Melihat Data Scan**, **Melihat Hasil Analisis**, dan mengaudit keamanan melalui **Melihat Activity Log**.*
> *- Serta pada paket **Dashboard**: Mengakses ringkasan metrik melalui **Melihat Dashboard Admin**.*
> 
> *Diagram ini dengan sangat tegas memisahkan batasan fungsional pengguna biasa dan administrator, serta menjamin bahwa integrasi AI dan OTP terdokumentasi secara standar melalui relasi include wajib."*

---

## 🔄 SLIDE 4: ACTIVITY DIAGRAM RESMI SKINCHECK (FACE SCAN & ANALISIS KULIT)

- 🎯 **Fokus Layar:** Tampilkan gambar diagram [02_activity_diagram_official.jpg](file:///c:/xampp/htdocs/APSkincek/dokumentasi_uml_dan_prototipe/02_activity_diagram_official.jpg).
- 🧭 **Arah Sorotan Kursor:**
  1. Tunjuk 4 kolom swimlane dari kiri ke kanan: **Customer / User**, **Sistem SkinCheck**, **Asisten AI / Computer Vision**, dan **Database**.
  2. Sorot lingkaran hitam *Initial Node* di Customer.
  3. Sorot decision diamond pertama di Sistem: *Izin kamera diberikan?* (Ya / Tidak).
  4. Sorot decision diamond kedua di Sistem: *Gambar valid?* (Ya / Tidak).
  5. Sorot swimlane ke-3: *Deteksi Wajah (MediaPipe)* -> *Analisis Kondisi Kulit (OpenCV/ML)* -> *5 Indikator*.
  6. Sorot penyimpanan ke swimlane ke-4: *Database* (Gambar, Hasil, Timestamp, User ID).
  7. Tunjuk lingkaran konsentris hitam *Final Node* di kolom Customer.

- 🎙️ **Teks Narasi Lisan:**
> *"Diagram berikutnya adalah **Activity Diagram resmi SkinCheck**, yang memodelkan alur kerja pemindaian wajah (*Face Scan*) dan analisis kondisi kulit secara runtut.*  
> *Aktivitas ini dibagi ke dalam **4 Swimlanes** untuk memperjelas batas tanggung jawab setiap komponen:*  
> *1. Customer / User,*  
> *2. Sistem SkinCheck (Aplikasi & Backend),*  
> *3. Asisten AI / Computer Vision, dan*  
> *4. Database.*  
> 
> *Mari kita telusuri alurnya mulai dari titik hitam awal di kolom Customer:*  
> *1. Pengguna memulai dengan **Membuka menu Face Scan**.*  
> *2. Sistem SkinCheck merespon dengan **Meminta izin kamera** melalui WebRTC API.*  
> *3. Di sini terdapat percabangan keputusan (*decision*): Apakah izin kamera diberikan?*  
> *   - Jika **Tidak**, sistem menampilkan pesan error *'Akses kamera ditolak'* dan mengembalikan pengguna ke halaman awal.*  
> *   - Jika **Ya**, sistem mengeksekusi aktivitas **Mengaktifkan kamera** dan **Menampilkan preview kamera** secara real-time di layar.*  
> *4. Pengguna melihat pratinjau wajahnya dan melakukan aksi **Mengambil foto wajah**.*  
> *5. Sistem SkinCheck menerima berkas citra dan melakukan **Validasi kualitas gambar** (pencahayaan, format berkas, dan resolusi minimum).*  
> *6. Terjadi percabangan keputusan kedua: Apakah gambar valid?*  
> *   - Jika **Tidak**, muncul notifikasi *'Gambar tidak valid, ambil ulang'* dan pengguna diarahkan memfoto ulang.*  
> *   - Jika **Ya**, sistem melanjutkan ke aktivitas **Menyimpan gambar secara aman** di direktori privat server dan **Mengirim gambar untuk analisis**.*  
> 
> *Kini alur beralih ke swimlane ketiga, yaitu **Asisten AI / Computer Vision**:*  
> *7. Model pertama-tama melakukan **Deteksi wajah menggunakan MediaPipe** untuk mengisolasi titik landmark dan Region of Interest (ROI).*  
> *8. Selanjutnya dilakukan **Analisis kondisi kulit menggunakan OpenCV dan Machine Learning**.*  
> *9. Algoritma ini **Menghasilkan 5 indikator kuantitatif**, yaitu:*  
> *   - **Acne Score** (tingkat lesi jerawat),*  
> *   - **Redness Score** (derajat eritema kemerahan),*  
> *   - **Texture Score** (kehalusan tekstur pori),*  
> *   - **Hyperpigmentation Score** (noda hitam), dan*  
> *   - **Overall Score** (skor komposit kesehatan kulit).*  
> 
> *10. Hasil komputasi ini dikembalikan ke Sistem SkinCheck, yang kemudian **Menerima hasil analisis** dan **Menghitung Overall Score**.*  
> *11. Sistem lalu menjalankan aktivitas **Menyimpan hasil analisis ke database**.*  
> *12. Di swimlane keempat, **Database** mencatat 4 parameter penting secara atomik: Berkas Gambar, Hasil Analisis, Timestamp waktu pemindaian, dan ID Pengguna (User ID).*  
> *13. Setelah tersimpan, sistem **Menampilkan hasil analisis kepada user** berupa dashboard visual grafis.*  
> *14. Pengguna menyelesaikan alur dengan **Melihat hasil analisis kulit**, dan alur berakhir pada Final Node.*  
> 
> *Di bagian bawah diagram, kami mencantumkan catatan kepatuhan regulasi: Seluruh hasil analisis ini adalah indikator estimasi perawatan kulit visual, bukan diagnosis medis."*

---

## 🏛️ SLIDE 5: CLASS DIAGRAM, SEQUENCE DIAGRAM & ERD BASIS DATA

- 🎯 **Fokus Layar:** Tampilkan Class Diagram, Sequence Diagram, dan ERD Basis Data.
- 🎙️ **Teks Narasi Lisan:**
> *"Untuk melengkapi use case dan activity diagram tadi, kami telah merancang arsitektur perangkat lunak berbasis Model-View-Controller (MVC) dan basis data relasional 9 tabel.*  
> *- **Class Diagram** memisahkan tanggung jawab antara Controller (seperti `AuthController`, `ScanController`, `AdminController`), Service AI (`PythonAIService`, `LLMService`, `FonnteHelper`), serta Model Data.*  
> *- **Sequence Diagram** membuktikan bahwa saat terjadi kegagalan kuota pada primary API key AI (misalnya HTTP 402 Insufficient Balance), sistem kami memiliki mekanisme **Auto-Failover** instan ke kunci cadangan (Gemini 3.5 / DeepSeek) dalam hitungan milidetik sehingga pengguna tidak pernah mengalami kendala.*  
> *- **ERD Basis Data** memastikan integritas referensial 9 tabel utama dari akun pengguna, log scan, hasil analisis, sunscreen coverage, hingga katalog produk terkurasi."*

---

## 📱 SLIDE 6: DEMONSTRASI PROTOTIPE INTERAKTIF FIGMA AI/UX

- 🎯 **Fokus Layar:** Buka live simulator prototipe di browser Chrome.
- 🎙️ **Teks Narasi Lisan:**
> *"Kini mari kita saksikan implementasi visualnya pada **Prototipe Interaktif AI/UX Figma**.  
> Seperti yang Bapak/Ibu lihat di layar, kita dapat menguji langsung alur pengguna:*  
> *1. Masuk ke halaman login dan menerima notifikasi OTP WhatsApp otomatis.*  
> *2. Mengisi onboarding preferensi tipe kulit.*  
> *3. Melihat dashboard utama dengan skor 82/100.*  
> *4. Membuka kamera scanner dengan animasi laser pemindai.*  
> *5. Menampilkan hasil analisis visual parameter jerawat, kemerahan, dan sunscreen.*  
> *6. Serta berdialog langsung dengan Asisten AI yang terhubung ke database.*  
> *Kita juga dapat beralih ke Mode Admin untuk melihat panel kontrol dan konfigurasi Multi-API key.*  
> *Prototipe ini membuktikan bahwa rancangan use case dan activity diagram kami telah terimplementasi secara fungsional dan siap pakai."*

---

## 🎯 SLIDE 7: PENUTUP (CLOSING)

- 🎙️ **Teks Narasi Lisan:**
> *"Demikian presentasi perancangan sistem SkinCheck.*  
> *Melalui pemodelan Use Case Diagram yang memetakan peran pengguna, Super Admin, Layanan AI, dan Layanan OTP secara presisi, serta Activity Diagram yang menggambarkan pipeline MediaPipe dan OpenCV secara sistematis, SkinCheck siap menjadi solusi cerdas perawatan kulit bagi masyarakat Indonesia.*  
> *Terima kasih atas perhatian Bapak/Ibu dosen penguji. Saya membuka sesi untuk tanya jawab."*
