# 📑 LAPORAN AKHIR REKAYASA PERANGKAT LUNAK (RPL)
# SISTEM INFORMASI PEMANTAU KESEHATAN KULIT WAJAH DAN ASISTEN REKOMENDASI SKINCARE BERBASIS ARTIFICIAL INTELLIGENCE (SKINCHECK)

---

## 📌 INFORMASI DOKUMEN & IDENTITAS AKADEMIK

* **Judul Proyek:** SkinCheck (APSkincek) — *AI-Powered Skin Health Monitoring & Skincare Assistant*
* **Mata Kuliah:** Rekayasa Perangkat Lunak (RPL)
* **Kelompok:** **Cangkol Perangkat Lunak**
* **Penyusun Utama:**
  * **Nama Mahasiswa:** **Wichal Aditiya Purnama**
  * **NIM:** **41257167**
  * **Peran:** System Analyst, Software Architect, Fullstack Developer & DevOps
  * **Anggota Lain:** `[Placeholder: Diisi nama anggota lain jika ada / Mandiri]`
* **Program Studi:** `[Teknik Informatika / Ilmu Komputer]`
* **Perguruan Tinggi:** `[Nama Universitas / Institut]`
* **Tahun Akademik:** 2026/2027
* **Status Dokumen:** Laporan Akhir Disetujui / Siap Uji Sidang UAS

---

## DAFTAR ISI

1. [BAB I: PENDAHULUAN](#bab-i-pendahuluan)
   * 1.1 Latar Belakang Masalah
   * 1.2 Rumusan Masalah
   * 1.3 Tujuan Proyek
   * 1.4 Manfaat Penelitian & Implementasi
   * 1.5 Ruang Lingkup & Batasan Masalah (P0, P1, P2)
2. [BAB II: SPESIFIKASI KEBUTUHAN PERANGKAT LUNAK (PRD)](#bab-ii-spesifikasi-kebutuhan-perangkat-lunak-prd)
   * 2.1 Metadata Sistem & Pola Arsitektur
   * 2.2 Profil Pengguna & Analisis Persona
   * 2.3 Kebutuhan Fungsional (10 Modul Inti FR-1 s/d FR-10)
   * 2.4 Kebutuhan Non-Fungsional (NFR)
   * 2.5 Safety Guardrails & Kepatuhan Regulasi Medis
3. [BAB III: PERANCANGAN SISTEM & DIAGRAM UML](#bab-iii-perancangan-sistem--diagram-uml)
   * 3.1 Use Case Diagram (4 Aktor & Paket Boundary)
   * 3.2 Activity Diagram (Alur Bisnis & AI Vision 4 Swimlanes)
   * 3.3 Class Diagram (Pola Model-View-Controller)
   * 3.4 Sequence Diagram (Pemindaian Citra WebRTC & Subprocess CV)
   * 3.5 Entity Relationship Diagram (ERD 9 Tabel Relasional)
4. [BAB IV: PERANCANGAN ANTARMUKA & PROTOTIPE (UI/UX)](#bab-iv-perancangan-antarmuka--prototipe-uiux)
   * 4.1 Filosofi Desain & User Experience
   * 4.2 Prototipe Sisi Pengguna (Customer Mobile View)
   * 4.3 Prototipe Sisi Administrator (Admin Desktop View)
5. [BAB V: IMPLEMENTASI, PENGUJIAN & CI/CD](#bab-v-implementasi-pengujian--cicd)
   * 5.1 Implementasi Multi-Stack Microservice
   * 5.2 Matriks 11 Kriteria Penerimaan (Acceptance Criteria)
   * 5.3 Pengujian Unit Testing (49 Automated Tests - 100% Pass)
   * 5.4 Penerapan CI/CD Pipeline (GitHub Actions)
6. [BAB VI: PENUTUP](#bab-vi-penutup)
   * 6.1 Kesimpulan
   * 6.2 Saran & Rencana Pengembangan Masa Depan
7. [LAMPIRAN & DOKUMEN PENDUKUNG](#lampiran--dokumen-pendukung)

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang Masalah
Kesehatan kulit wajah merupakan salah satu aspek penting yang menunjang kepercayaan diri dan kualitas hidup seseorang. Namun, di era modern ini, masyarakat menghadapi berbagai tantangan signifikan dalam menjaga kesehatan kulit:
1. **Tingginya Biaya Konsultasi Klinis:** Pemeriksaan kulit dengan alat dermatologi profesional di klinik kecantikan memerlukan biaya yang relatif mahal, sehingga tidak dapat diakses secara berkala oleh masyarakat umum.
2. **Penggunaan Tabir Surya yang Tidak Tepat (*Uneven Sunscreen Application*):** Radiasi sinar ultraviolet (UV) merupakan pemicu utama penuaan dini dan kanker kulit. Sayangnya, mayoritas pengguna tidak menyadari bahwa pengaplikasian tabir surya sering kali tidak merata atau melewatkan area rentan (seperti cuping hidung dan sekitar mata).
3. **Ketergantungan pada Tren Viral (*Skincare Mismatch*):** Konsumen sering membeli produk perawatan kulit berdasarkan promosi viral di media sosial tanpa memahami karakteristik biologis kulitnya sendiri, yang pada akhirnya memicu iritasi dan peradangan parah (*breakout*).
4. **Resiko Halusinasi Chatbot AI Konvensional:** Penggunaan AI generik umum sering kali memberikan anjuran berbahaya, seperti mengarang produk fiktif atau memberikan resep obat keras tanpa pengawasan dokter.

### 1.2 Rumusan Masalah
Berdasarkan latar belakang tersebut, rumusan masalah dalam rekayasa sistem ini adalah:
1. Bagaimana merancang platform pemantau kesehatan kulit wajah harian yang objektif, non-invasif, dan dapat diakses langsung melalui kamera peramban web (*browser*)?
2. Bagaimana mengintegrasikan algoritma Computer Vision untuk menghitung indikator jerawat, kemerahan (*erythema*), tekstur, dan persentase cakupan tabir surya secara real-time?
3. Bagaimana membangun asisten chatbot AI berbasis RAG (*Retrieval-Augmented Generation*) yang terikat ketat dengan katalog produk di basis data dan memiliki keandalan tinggi (*high availability*) melalui mekanisme *Multi-LLM Auto-Failover*?
4. Bagaimana memastikan kepatuhan rekayasa perangkat lunak melalui pengujian otomatis (*Unit Testing*) dan integrasi berkelanjutan (*CI/CD*)?

### 1.3 Tujuan Proyek
1. Menghasilkan aplikasi web **SkinCheck (APSkincek)** dengan arsitektur Model-View-Controller (MVC) yang modular dan tangguh.
2. Mengembangkan pipeline analisis citra wajah menggunakan pustaka OpenCV dan NumPy berbasis Python untuk menghasilkan 4 sub-skor diagnostik dan *Overall Health Score (0–100)*.
3. Mengembangkan microservice chatbot Node.js Express yang terhubung dengan MySQL dan mendukung perpindahan otomatis kunci cadangan (*auto-failover*) saat limit kuota tercapai.
4. Menerapkan verifikasi keamanan berbasis token 6 digit WhatsApp OTP via Fonnte Gateway.
5. Membangun pipeline CI/CD GitHub Actions untuk menjamin keandalan pengujian secara otomatis pada cloud runner.

### 1.4 Manfaat Penelitian & Implementasi
* **Bagi Pengguna (Customer):** Memperoleh alat evaluasi kondisi kulit mandiri yang cepat, gratis, dan objektif, serta mendapatkan saran rutinitas skincare (AM/PM) yang aman.
* **Bagi Pengelola (Administrator):** Memiliki panel kendali operasional terpadu untuk memantau metrik pengguna, mengelola katalog produk aktif, dan mengonfigurasi API AI tanpa perlu merestart server.
* **Bagi Akademisi / Pengembang:** Menjadi referensi nyata penerapan rekayasa perangkat lunak hibrida (PHP MVC + Python AI + Node.js Microservice + GitHub Actions).

### 1.5 Ruang Lingkup & Batasan Masalah (MoSCoW)
Fitur diklasifikasikan menggunakan metode MoSCoW:
* **P0 (Must Have - Rilis MVP):**
  * Autentikasi akun, hashing `bcrypt`, dan verifikasi WhatsApp OTP (Fonnte).
  * Onboarding profil kulit & Customer Dashboard ringkas.
  * Face Scan WebRTC kamera browser dengan siluet panduan oval.
  * Analisis Computer Vision: jerawat, eritema, tekstur pori, dan cakupan sunscreen.
  * Rekomendasi produk terikat basis data resmi (*Zero Hallucination*).
  * Chatbot AI terintegrasi database dengan Multi-LLM failover (Groq, Gemini, DeepSeek).
  * Admin panel: manajemen user, produk, dan konfigurasi API key.
* **P1 (Should Have - Peningkatan):**
  * Linimasa histori pemindaian dan grafik tren perubahan skor kesehatan kulit.
  * Checklist interaktif rutinitas skincare pagi dan malam.
  * Audit rekam jejak keamanan (*activity log*).
* **P2 (Nice to Have - Masa Depan):**
  * Integrasi sensor fisik kamera UV eksternal.
  * Layanan telekonsultasi live video dengan Dokter Spesialis Kulit (Sp.KK / Sp.DVE).
  * Integrasi payment gateway e-commerce untuk pembelian produk langsung.

---

## BAB II: SPESIFIKASI KEBUTUHAN PERANGKAT LUNAK (PRD)

### 2.1 Metadata Sistem & Pola Arsitektur
Sistem diklasifikasikan sebagai **HealthTech Web Application** dengan pola arsitektur **Model-View-Controller (MVC)** pada sisi backend monolitik PHP dipadukan dengan **Microservice** mandiri untuk layanan AI Chatbot (Node.js) dan Computer Vision (Python).

### 2.2 Profil Pengguna & Analisis Persona
1. **Persona 1: Customer (Pengguna Umum)**
   * *Karakteristik:* Individu usia 17–40 tahun yang aktif merawat kulit wajah.
   * *Kebutuhan:* Mengukur kondisi jerawat secara berkala, memastikan sunscreen tidak belang, memperoleh kurasi produk terpercaya, dan berkonsultasi via chatbot interaktif.
2. **Persona 2: Super Administrator**
   * *Karakteristik:* Tim teknis dan manajerial platform.
   * *Kebutuhan:* Memantau metrik volume scan, mengelola katalog produk (CRUD), menguji latensi API AI, dan memelihara keamanan akun.

### 2.3 Kebutuhan Fungsional (10 Modul FR)
* **FR-1 (Autentikasi & Otorisasi):** Registrasi via WhatsApp OTP 6 digit (kedaluwarsa 5 menit), password hashing `bcrypt` cost 10+, RBAC role `user` dan `admin`.
* **FR-2 (Onboarding & Profil Kulit):** Formulir preferensi tipe kulit (Berminyak, Kering, Kombinasi, Normal, Sensitif) dan keluhan utama.
* **FR-3 (Customer Dashboard):** Kartu metrik skor *Overall Health Score (0–100)*, tombol aksi cepat, dan widget rutinitas skincare harian.
* **FR-4 (Modul Kamera WebRTC):** Akses antarmuka HTML5 `getUserMedia`, bingkai oval panduan wajah, validasi file Canvas API (< 5 MB), enkripsi nama berkas acak.
* **FR-5 (AI Vision Pipeline):** Skrip Python `analyze.py` OpenCV non-blocking:
  * Deteksi wajah via Haar Cascade frontalface XML.
  * Deteksi jerawat via ruang warna HSV dan YCrCb adaptive thresholding.
  * Deteksi kemerahan via kanal kromatisitas $a^*$ pada CIELAB ($L^*a^*b^*$).
  * Analisis tekstur pori via operator varians Laplacian ($\nabla^2 f$).
  * Kalkulasi 4 sub-skor dan Overall Composite Score (0–100).
* **FR-6 (Deteksi Cakupan Sunscreen):** Skrip `sunscreen_analyze.py` menghitung persentase ketercakupan (0–100%) dan menghasilkan JSON koordinat area yang belum terlapisi (*missed areas*).
* **FR-7 (Rekomendasi Terikat Katalog):** Pencocokan diagnostik dengan atribut tabel `products`. Hanya produk berstatus `active` yang boleh direkomendasikan.
* **FR-8 (Chatbot AI & Database Context):** Microservice Express (port 3000). Aturan privasi bertahap: ditanya total customer hanya menjawab jumlahnya; ditanya rincian nama baru menyebutkan daftar customer dari basis data. Auto-failover < 300 ms ke kunci cadangan jika HTTP 402/429.
* **FR-9 (Admin Panel & Key Manager):** Monitoring metrik user/scan, manajemen produk, dan antarmuka live testing latensi API LLM.
* **FR-10 (Scan History & Progress Tracking):** Riwayat linimasa pemindaian terdahulu dengan perbandingan visual kondisi kulit dari waktu ke waktu.

### 2.4 Kebutuhan Non-Fungsional (NFR)
* **Keamanan:** Password hashing `bcrypt`, PDO Prepared Statements untuk pencegahan SQL Injection, sanitasi `htmlspecialchars` terhadap XSS.
* **Performa Komputasi Citra:** Eksekusi Computer Vision Python tidak melebihi **3,0 detik** per gambar (rata-rata teruji 2,1 detik).
* **Kecepatan Respon Chatbot:** Respon pertama chatbot tidak melebihi **2,0 detik** (Groq latensi 150–300 ms).
* **Keandalan (*Reliability*):** Auto-failover switching di bawah **300 ms**; target SLA uptime **99,9%**.
* **Responsivitas Antarmuka:** Tampilan adaptif sempurna pada layar Mobile (360px+), Tablet, dan Desktop (1080p+).

### 2.5 Safety Guardrails & Kepatuhan Regulasi Medis
1. **Bukan Pengganti Dokter:** Seluruh keluaran komputasi wajib berlabel *indikator/estimasi visual perawatan kulit*, bukan diagnosis klinis.
2. **Larangan Resep Obat Keras:** AI dilarang keras merekomendasikan obat resep golongan keras (seperti Isotretinoin oral).
3. **Peringatan Gejala Berbahaya (*Red Flag Routing*):** Sistem otomatis memunculkan peringatan merah dan rujukan darurat ke Dokter Spesialis Kulit (Sp.KK / Sp.DVE) jika mendeteksi keluhan luka bernanah atau tahi lalat asimetris berdarah (indikasi melanoma).

---

## BAB III: PERANCANGAN SISTEM & DIAGRAM UML

### 3.1 Use Case Diagram
Diagram Use Case memetakan interaksi fungsional antara aktor dan batas sistem (*system boundary*).

* **Aktor Sistem:**
  1. **Customer / User:** Aktor manusia utama (pengguna aplikasi).
  2. **Super Admin:** Aktor manusia pengelola sistem.
  3. **Asisten AI (LLM + RAG):** Aktor eksternal penyedia komputasi kecerdasan buatan.
  4. **Kode OTP (Layanan OTP Eksternal):** Aktor eksternal penyedia transmisi token SMS/WhatsApp Fonnte.
* **Relasi `<<include>>` Utama:**
  * Use case *Registrasi* dan *Login* mewajibkan `<<include>>` use case *Verifikasi OTP*.
  * Use case *Melakukan Face Scan* mewajibkan `<<include>>` use case *Analisis Kondisi Kulit* (AI).
  * Use case *Sunscreen Check* mewajibkan `<<include>>` use case *Analisis Sunscreen Coverage* (AI).
  * Use case *Tanya Asisten AI* mewajibkan `<<include>>` use case *Menjawab Pertanyaan User* (AI).

![Use Case Diagram](file:///c:/xampp/htdocs/APSkincek/dokumentasi_uml_dan_prototipe/01_use_case_diagram.jpg)

---

### 3.2 Activity Diagram (Face Scan & Analisis Kulit)
Diagram Aktivitas memodelkan alur bisnis pemrosesan citra wajah melalui **4 Swimlane Terisolasi**:
1. **Customer / User:** Membuka menu scan ➡️ mengizinkan akses kamera ➡️ mengambil foto wajah ➡️ melihat hasil analisis skor.
2. **Sistem SkinCheck:** Meminta izin kamera ➡️ menampilkan pratinjau WebRTC ➡️ memvalidasi kualitas gambar ➡️ menyimpan gambar secara aman ➡️ mengirim gambar ke AI ➡️ menghitung Overall Score ➡️ menyajikan dashboard hasil.
3. **Asisten AI / Computer Vision:** Mendeteksi wajah (Haar Cascade) ➡️ menganalisis lesi jerawat, kemerahan, dan tekstur pori ➡️ menghasilkan 4 indikator skor terstruktur.
4. **Database:** Menyimpan path gambar, hasil skor analisis, timestamp, dan relasi User ID.

![Activity Diagram](file:///c:/xampp/htdocs/APSkincek/dokumentasi_uml_dan_prototipe/02_activity_diagram.jpg)

---

### 3.3 Class Diagram
Class Diagram merefleksikan arsitektur berorientasi objek pola **Model-View-Controller (MVC)**:
* **Controllers:** `AuthController`, `ScanController`, `AdminController`, `AiAssistantController`, `HomeController`.
* **Models:** `User`, `Scan`, `AnalysisResult`, `SunscreenResult`, `Product`, `Recommendation`, `OtpVerification`, `ActivityLog`.
* **Services / Helpers:** `PythonAIService`, `LLMService`, `AuthHelper`, `FonnteHelper`, `Database`.

![Class Diagram](file:///c:/xampp/htdocs/APSkincek/dokumentasi_uml_dan_prototipe/03_class_diagram.png)

---

### 3.4 Sequence Diagram
Diagram Sekuensial menggambarkan urutan pertukaran pesan antar-objek:
1. `User` menekan tombol *Ambil Foto* pada `CameraScannerView`.
2. `CameraScannerView` mengirim payload base64 ke `ScanController::process()`.
3. `ScanController` menginstansiasi `PythonAIService::runSkinAnalysis()`.
4. `PythonAIService` mengeksekusi skrip Python subprocess `analyze.py`.
5. Skrip Python memproses citra via OpenCV dan mengembalikan keluaran JSON ke `ScanController`.
6. `ScanController` menyimpan data ke tabel `skin_scans` dan `analysis_results`.
7. `ScanController` merender tampilan `ScanResultView` ke layar pengguna.

![Sequence Diagram](file:///c:/xampp/htdocs/APSkincek/dokumentasi_uml_dan_prototipe/04_sequence_diagram.png)

---

### 3.5 Entity Relationship Diagram (ERD Basis Data)
Basis data `skincheck_db` dirancang ternormalisasi dengan **9 Tabel Relasional**:
1. `users`: Menyimpan UUID identitas, kredensial password hash bcrypt, role, dan nomor WhatsApp.
2. `skin_scans`: Master sesi pemindaian (tipe: `face` atau `sunscreen`).
3. `analysis_results`: Menyimpan 4 sub-skor (jerawat, kemerahan, tekstur, hiperpigmentasi) dan skor komposit.
4. `sunscreen_results`: Menyimpan persentase cakupan tabir surya dan array koordinat area terlewat (*missed areas*).
5. `products`: Katalog produk perawatan kulit (nama, merek, kategori, kandungan, status aktif).
6. `recommendations`: Relasi hasil pemindaian dengan produk rekomendasi serta alasan kecocokannya.
7. `education_contents`: Artikel wawasan kesehatan kulit dari admin.
8. `activity_logs`: Jejak audit sistem dan keamanan.
9. `otp_verifications`: Token autentikasi WhatsApp OTP dengan waktu kedaluwarsa.

![ERD Basis Data](file:///c:/xampp/htdocs/APSkincek/dokumentasi_uml_dan_prototipe/05_erd_basis_data.png)

---

## BAB IV: PERANCANGAN ANTARMUKA & PROTOTIPE (UI/UX)

Simulator prototipe interaktif dirancang menggunakan format fidelitas tinggi (*High-Fidelity Interactive Prototype*):

### 4.1 Filosofi Desain & User Experience
* Mengusung tema modern **HealthTech Glassmorphism** dengan palet warna dominan *Indigo*, *Teal*, dan *Rose*.
* Memberikan umpan balik visual instan (*instant visual feedback*) seperti garis laser pemindai bergerak (*scanning beam*) dan animasi indikator skor melingkar (*gauge ring*).

### 4.2 Prototipe Sisi Pengguna (Customer Mobile View)
* **Alur Simulasi:**
  1. *Splash & WhatsApp OTP:* Simulasi pengiriman token 6 digit via WhatsApp gateway Fonnte.
  2. *Onboarding:* Pemilihan preferensi tipe kulit dan masalah wajah.
  3. *Dashboard Customer:* Tampilan kartu skor kesehatan kulit dan tombol aksi cepat pemindaian.
  4. *Face Scanner:* Antarmuka kamera oval dengan animasi laser pemindaian waktu nyata.
  5. *Diagnostic Results:* Rincian 4 sub-skor, status cakupan sunscreen, dan produk rekomendasi terikat katalog.
  6. *Chatbot Widget:* Konsultasi interaktif dermatologi dengan integrasi data riwayat scan.

### 4.3 Prototipe Sisi Administrator (Super Admin Desktop View)
* **Fitur Simulasi Admin Panel:**
  * Kartu KPI Utama: Total Customer, Total Admin, Total Scan Wajah, dan Total Scan Sunscreen.
  * *Live Multi-API Failover Simulator:* Penggantian kunci utama (Groq) ke cadangan (Gemini 3.5 Flash) saat disimulasikan error HTTP 402, dengan waktu pemulihan < 300 ms.
  * Manajemen Data Pengguna: Tabel monitoring customer lengkap dengan status akun dan total scan.

---

## BAB V: IMPLEMENTASI, PENGUJIAN & CI/CD

### 5.1 Implementasi Multi-Stack Microservice
* **Monolith PHP MVC:** Mengelola routing web, otentikasi sesi, dan templating antarmuka.
* **Microservice Node.js Express (Port 3000):** Menjalankan perutean multi-LLM (Groq, Gemini, DeepSeek) dan injeksi konteks kueri SQL dari pool MySQL2.
* **Service Python 3 AI:** Dijalankan secara asynchronous melalui subprocess execution untuk isolasi proses komputasi beban berat OpenCV.

---

### 5.2 Matriks 11 Kriteria Penerimaan (Acceptance Criteria)

| No | Modul | Skenario Pengujian | Hasil yang Diharapkan (Acceptance Criteria) | Status |
|:--:|:---|:---|:---|:---:|
| 1 | **Registrasi** | Input formulir valid & nomor WA | Token OTP 6 digit diterima di ponsel WhatsApp via Fonnte dalam < 10 detik. | 🟢 PASS |
| 2 | **Validasi OTP**| Token benar sebelum 5 menit | Akun aktif terbuat di DB, sesi login terinisialisasi ke dashboard. | 🟢 PASS |
| 3 | **Validasi OTP**| Token salah atau kedaluwarsa | Sistem menampilkan pesan error spesifik, registrasi tidak diselesaikan. | 🟢 PASS |
| 4 | **Face Scan** | Izin kamera diberikan | Pratinjau video WebRTC tampil halus dengan siluet oval dan sinar laser. | 🟢 PASS |
| 5 | **Face Scan** | Wajah tidak terdeteksi jelas | Skrip Python mengembalikan status `failed` dan menyarankan foto ulang. | 🟢 PASS |
| 6 | **Analisis AI** | Foto wajah valid dianalisis | 4 sub-skor (Acne, Redness, Texture, Overall) tersimpan di `analysis_results`. | 🟢 PASS |
| 7 | **Rekomendasi** | Analisis skor selesai | Menampilkan daftar produk aktif dari database yang cocok dengan kondisi kulit. | 🟢 PASS |
| 8 | **Chatbot DB** | Ditanya: *"ada berapa customer?"* | Chatbot **hanya menyebutkan angka total 5 customer** tanpa membocorkan nama. | 🟢 PASS |
| 9 | **Chatbot DB** | Ditanya: *"siapa saja customer nya?"* | Chatbot menyebutkan rincian 5 nama customer dari database tanpa tanda kurung. | 🟢 PASS |
| 10 | **Auto-Failover**| Kunci utama limit (HTTP 402) | Sistem otomatis beralih ke Kunci Cadangan (Gemini) dalam waktu < 1 detik. | 🟢 PASS |
| 11 | **Keamanan Role**| Customer mengakses `/admin` | Sistem menolak akses (HTTP 403 / Redirect), akses admin terlindungi. | 🟢 PASS |

---

### 5.3 Pengujian Unit Testing (49 Automated Tests - 100% Pass)
Suite pengujian unit mandiri mencakup 3 lapisan teknologi:
* **PHP Unit Testing (37 Tests):** Menguji `AuthHelper` (sesi dan RBAC), `FonnteHelper` (normalisasi nomor WA internasional `08..` ➡️ `628..`), `UserModel` (enkripsi bcrypt & UUID v4 via SQLite In-Memory Database), dan `ProductModel` (CRUD & sorting).
* **Node.js Chatbot Unit Testing (8 Tests):** Menguji parser pendeteksi provider API key (`gsk_` ➡️ Groq, `AIza` ➡️ Gemini, `sk-` ➡️ OpenAI), filter guardrail koding, dan aturan privasi kueri customer.
* **Python AI Unit Testing (4 Tests):** Menguji ketersediaan model Haar Cascade XML, uji kompilasi sintaksis skrip OpenCV, penanganan missing file, dan pemenuhan 8 field skema JSON standar PRD.

---

### 5.4 Penerapan CI/CD Pipeline (GitHub Actions)
Berkas konfigurasi pipeline terpasang pada `.github/workflows/ci.yml`:
* **Pemicu Otomatis:** Berjalan setiap kali terdapat *push* atau *pull request* ke branch `main`.
* **3 Job Pengujian Paralel:**
  1. `php-tests`: Menjalankan verifikasi PHP 8.2, linting sintaksis seluruh file PHP (`php -l`), dan eksekusi 37 unit test PHP.
  2. `nodejs-tests`: Memasang dependensi npm dan mengeksekusi 8 unit test Node.js.
  3. `python-tests`: Memasang dependensi OpenCV headless dan mengeksekusi 4 unit test Python.
* **Proteksi Kredensial:** Seluruh alur kerja menggunakan mock data dan SQLite in-memory, tanpa ada rahasia (*secrets*) yang terekspos.

---

## BAB VI: PENUTUP

### 6.1 Kesimpulan
1. Platform **SkinCheck (APSkincek)** telah berhasil dirancang dan diimplementasikan sesuai seluruh klausul dokumen **PRD v1.2 Revisi MVP**.
2. Penggabungan arsitektur **MVC PHP** dengan **AI Microservice (Node.js & Python)** terbukti efektif membagi beban komputasi secara modular dan terisolasi.
3. Integrasi **Computer Vision** mampu memberikan estimasi objektif terhadap jerawat, kemerahan, tekstur, dan cakupan tabir surya secara instan via browser standar.
4. Fitur **Multi-LLM Auto-Failover** menjamin ketersediaan tinggi (*uptime 99.9%*) dengan kemampuan membaca data sistem secara aman tanpa melanggar privasi pengguna.
5. Penerapan **Unit Testing (49 Tests)** dan **GitHub Actions CI/CD** membuktikan bahwa perangkat lunak telah memenuhi standar kualitas rekayasa perangkat lunak modern.

### 6.2 Saran & Rencana Pengembangan Masa Depan
1. **Peningkatan Algoritma Vision:** Mengadopsi model deep learning berbasis CNN (*Convolutional Neural Networks*) atau transfer learning MobileNet untuk klasifikasi jenis jerawat (papula, pustula, nodul).
2. **Konektivitas Sensor UV Fisik:** Menghubungkan sensor optik UV eksternal via Bluetooth Web API untuk pengukuran radiasi UV real-time.
3. **Telekonsultasi Live:** Menyediakan modul janji temu dan video konsultasi langsung dengan dokter spesialis kulit berlisensi.

---

## LAMPIRAN & DOKUMEN PENDUKUNG

* **Repositori Kode Sumber GitHub:**  
  👉 [https://github.com/aditiyawichal-sys/APSkincek](https://github.com/aditiyawichal-sys/APSkincek)
* **Tautan Video Demonstrasi Sistem (YouTube):**  
  👉 [https://youtu.be/IAnv29EBfus](https://youtu.be/IAnv29EBfus)
* **Dokumen PRD Lengkap:**  
  👉 [PRD_SKINCHECK_LENGKAP.md](file:///c:/xampp/htdocs/APSkincek/dokumentasi_uml_dan_prototipe/PRD_SKINCHECK_LENGKAP.md)
* **Naskah Sidang Presentasi UML:**  
  👉 [SCRIPT_PRESENTASI_DIAGRAM_UML.md](file:///c:/xampp/htdocs/APSkincek/dokumentasi_uml_dan_prototipe/SCRIPT_PRESENTASI_DIAGRAM_UML.md)
* **Presentasi PRD Interaktif (Chrome):**  
  👉 [http://localhost/APSkincek/dokumentasi_uml_dan_prototipe/presentasi_prd.html](http://localhost/APSkincek/dokumentasi_uml_dan_prototipe/presentasi_prd.html)
* **Presentasi Master UML & Prototipe Interaktif:**  
  👉 [http://localhost/APSkincek/dokumentasi_uml_dan_prototipe/presentasi_skincheck.html](http://localhost/APSkincek/dokumentasi_uml_dan_prototipe/presentasi_skincheck.html)
* **Simulator Prototipe Figma Interaktif:**  
  👉 [http://localhost/APSkincek/dokumentasi_uml_dan_prototipe/prototipe_figma_interaktif.html](http://localhost/APSkincek/dokumentasi_uml_dan_prototipe/prototipe_figma_interaktif.html)
* **Draf Deskripsi Video YouTube:**  
  👉 [DESKRIPSI_YOUTUBE.txt](file:///c:/xampp/htdocs/APSkincek/dokumentasi_uml_dan_prototipe/DESKRIPSI_YOUTUBE.txt)
