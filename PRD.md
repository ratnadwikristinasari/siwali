# 📋 Product Requirements Document (PRD)

# SiWali — Sistem Informasi Perwalian Politeknik Negeri Jember

---

> **Version:** 1.0  
> **Tanggal:** 2026-07-11  
> **Status:** Draft (Ready for Review)  
> **Author:** Tim Pengembang SiWali

---

## 1. Ringkasan Produk

### 1.1 Latar Belakang

Proses perwalian akademik di Politeknik Negeri Jember (Polije) selama ini dilakukan secara manual — mahasiswa menemui dosen wali secara langsung, mengisi formulir kertas, dan dokumen KHS (Kartu Hasil Studi) diproses secara fisik. Proses ini memiliki beberapa masalah:

- **Tidak terlacak:** Tidak ada sistem terpusat untuk memantau status perwalian mahasiswa
- **Lambat:** Tanda tangan fisik membutuhkan kehadiran langsung dosen wali dan kajur
- **Tidak efisien:** Kajur harus menandatangani ratusan dokumen satu per satu
- **Sulit dimonitor:** Kaprodi dan kajur kesulitan memantau progress perwalian mahasiswanya

### 1.2 Visi Produk

> **"Menyederhanakan dan mendigitalisasi proses perwalian akademik Polije secara end-to-end — dari pengajuan mahasiswa hingga tanda tangan digital kajur — sehingga semua pihak dapat melakukannya kapan saja dan di mana saja."**

### 1.3 Tujuan Produk

| Tujuan                    | Indikator Keberhasilan                                    |
| ------------------------- | --------------------------------------------------------- |
| Efisiensi waktu perwalian | Waktu proses KHS dari pengajuan → selesai < 3 hari kerja  |
| Adopsi pengguna           | ≥ 90% mahasiswa aktif menggunakan SiWali dalam 1 semester |
| Kepuasan pengguna         | Net Promoter Score (NPS) ≥ 7/10                           |
| Zero paperwork            | 100% perwalian diproses tanpa dokumen fisik               |

---

## 2. Pengguna (User Personas)

### 2.1 Mahasiswa (Student)

| Atribut             | Detail                                                                       |
| ------------------- | ---------------------------------------------------------------------------- |
| **Siapa**           | Mahasiswa aktif Polije semua prodi                                           |
| **Goals**           | Menyelesaikan kewajiban perwalian, mendapat masukan akademik dari dosen wali |
| **Pain Points**     | Susah menemui dosen wali, tidak tahu status perwalian, lupa waktu perwalian  |
| **Cara Penggunaan** | Akses lewat browser (mobile/desktop)                                         |

### 2.2 Orang Tua (Wali Mahasiswa)

| Atribut             | Detail                                                           |
| ------------------- | ---------------------------------------------------------------- |
| **Siapa**           | Orang tua / wali mahasiswa yang terdaftar                        |
| **Goals**           | Memantau perkembangan akademik anak                              |
| **Pain Points**     | Tidak mendapat informasi langsung tentang performa akademik anak |
| **Cara Penggunaan** | Akses lewat browser dengan akun khusus orang tua                 |

### 2.3 Dosen Wali (Lecturer)

| Atribut             | Detail                                                                                    |
| ------------------- | ----------------------------------------------------------------------------------------- |
| **Siapa**           | Dosen yang ditunjuk sebagai wali akademik mahasiswa                                       |
| **Goals**           | Menyelesaikan review perwalian, memberikan masukan akademik, menandatangani KHS           |
| **Pain Points**     | Banyaknya perwalian yang harus diproses, sulitnya tracking mahasiswa yang belum perwalian |
| **Cara Penggunaan** | Akses lewat browser, mendapat notifikasi email                                            |

### 2.4 Kaprodi (Kepala Program Studi)

| Atribut             | Detail                                                            |
| ------------------- | ----------------------------------------------------------------- |
| **Siapa**           | Kepala Program Studi yang bertanggung jawab atas prodi tertentu   |
| **Goals**           | Memantau progress perwalian semua mahasiswa di prodi              |
| **Pain Points**     | Tidak ada dashboard yang menyajikan data perwalian secara agregat |
| **Cara Penggunaan** | Akses dashboard dan laporan                                       |

### 2.5 Kajur (Ketua Jurusan)

| Atribut             | Detail                                                                           |
| ------------------- | -------------------------------------------------------------------------------- |
| **Siapa**           | Ketua Jurusan yang membawahi beberapa prodi                                      |
| **Goals**           | Menandatangani KHS mahasiswa secara massal (bulk sign), memantau semua perwalian |
| **Pain Points**     | Harus tanda tangan ratusan dokumen fisik, tidak bisa dilakukan remote            |
| **Cara Penggunaan** | Akses dashboard + fitur bulk sign via e-sign API                                 |

---

## 3. Fitur & Persyaratan Fungsional

### 3.1 Modul Autentikasi (Auth)

#### F-AUTH-01: SSO Login via SuperApp

- **Deskripsi:** Semua pengguna login menggunakan akun SuperApp Polije (OAuth2)
- **Aktor:** Semua role
- **Alur:**
    1. User klik "Login dengan SuperApp"
    2. Redirect ke halaman login SuperApp
    3. User autentikasi di SuperApp
    4. SuperApp redirect kembali ke SiWali dengan authorization code
    5. SiWali exchange code → token
    6. SiWali fetch data user dari SuperApp API
    7. User masuk ke dashboard sesuai role
- **Requirement:**
    - Login harus rate-limited (max 10x/menit per IP)
    - Token OAuth harus dienkripsi saat disimpan di DB
    - Session harus dienkripsi dan menggunakan HTTPS-only cookie

#### F-AUTH-02: Logout

- **Deskripsi:** User dapat logout dan session di SuperApp juga dihapus
- **Alur:** Logout dari SiWali → Redirect ke SuperApp logout

#### F-AUTH-03: Role-Based Access Control

- **Deskripsi:** Setiap route dilindungi berdasarkan role user
- **Role:** `student`, `orang_tua`, `lecturer`, `kaprodi`, `kajur`, `sekjur`
- **Requirement:** Role middleware wajib diaplikasikan di semua route terproteksi

---

### 3.2 Modul Perwalian KHS (gpa_advising)

#### F-KHS-01: Upload KHS

- **Deskripsi:** Mahasiswa upload file KHS (PDF) sebelum mengajukan perwalian
- **Aktor:** `student`
- **Requirement:**
    - Format: PDF only
    - Ukuran maksimal: 1 MB
    - File disimpan ke S3 bucket
    - Nama file di-sanitize (tidak boleh pakai nama asli dari client)
    - Hanya 1 file aktif per sesi; file lama dihapus saat upload baru

#### F-KHS-02: Pengajuan Perwalian KHS

- **Deskripsi:** Mahasiswa mengajukan perwalian dengan melampirkan keluhan dan data semester
- **Aktor:** `student`
- **Pre-condition:**
    - Mahasiswa sudah mengisi Form Evaluasi Dosen di JTIFORM
    - Mahasiswa belum mengajukan perwalian KHS untuk semester yang sama
    - Dosen wali akademik terdaftar di SuperApp
- **Requirement:**
    - Validasi: `type`, `keluhan`, `semester_id` wajib diisi untuk KHS
    - Cek JTIFORM status via API
    - Cek duplikasi (1 perwalian KHS per semester)
    - Dosen wali diambil otomatis dari data SuperApp
    - Status awal: `pending`

#### F-KHS-03: Review & Persetujuan Dosen Wali

- **Deskripsi:** Dosen wali mengisi masukan dan menyetujui perwalian, sistem generate PDF KHS dan e-sign
- **Aktor:** `lecturer`
- **Requirement:**
    - Dosen wali hanya bisa melihat perwalian mahasiswanya sendiri
    - Generate PDF KHS dengan DomPDF
    - E-sign oleh dosen wali via ESign API
    - Upload PDF bertanda tangan ke S3
    - Status: `pending` → `signed`
    - Notifikasi email ke mahasiswa via RabbitMQ

#### F-KHS-04: Tanda Tangan Kajur (Single & Bulk)

- **Deskripsi:** Kajur menandatangani KHS mahasiswa (satu per satu atau massal)
- **Aktor:** `kajur`
- **Requirement:**
    - Kajur hanya melihat perwalian dengan status `signed`
    - Proses sign dikerjakan secara async via Job queue
    - PDF di-regenerate dengan dual signature (dosen + kajur)
    - Status: `signed` → `done`
    - Notifikasi email ke mahasiswa
    - Bulk sign: mendukung selection dan sign banyak dokumen sekaligus

---

### 3.3 Modul Perwalian Non-KHS (non_gpa_advising)

#### F-NONKHS-01: Pengajuan Perwalian Konseling

- **Deskripsi:** Mahasiswa mengajukan perwalian konseling/non-akademik
- **Aktor:** `student`
- **Requirement:**
    - Tidak memerlukan upload KHS
    - Keluhan wajib diisi (minimal 1 karakter)
    - Semester dan prodi diambil otomatis dari data aktif mahasiswa
    - Status awal: `pending`

#### F-NONKHS-02: Isian Masukan Dosen

- **Deskripsi:** Dosen wali mengisi masukan/catatan konseling
- **Aktor:** `lecturer`
- **Requirement:**
    - Tidak ada generate PDF atau e-sign
    - Status: `pending` → `done`
    - Notifikasi email ke mahasiswa (via mail queue)

---

### 3.4 Modul Dashboard

#### F-DASH-01: Dashboard Mahasiswa / Orang Tua

- **Deskripsi:** Menampilkan statistik perwalian dan IPK mahasiswa
- **Konten:**
    - Rata-rata IPK (dari riwayat perwalian)
    - Total perwalian KHS yang selesai
    - Total perwalian Non-KHS
    - Informasi IPK terbaru dari SuperApp API

#### F-DASH-02: Dashboard Dosen Wali

- **Deskripsi:** Dashboard operasional dosen wali
- **Konten:**
    - Statistik: total mahasiswa wali, belum perwalian, pending, done
    - Top 10 mahasiswa berdasarkan IPK (semester terpilih)
    - Filter per semester

#### F-DASH-03: Dashboard Kajur / Kaprodi

- **Konten:**
    - Analytics jurusan dari SuperApp (total mahasiswa, dosen, prodi)
    - Top 10 IPK dengan filter prodi
    - Data dapat difilter per prodi (kajur) atau per prodi sendiri (kaprodi)

---

### 3.5 Modul Monitoring

#### F-MON-01: Data Mahasiswa (Dosen Wali)

- **Deskripsi:** Dosen wali dapat melihat daftar mahasiswanya beserta status perwalian
- **Fitur:**
    - Pencarian, filter status akademik, filter status perwalian
    - Filter per session/semester
    - Kirim reminder email ke mahasiswa yang belum perwalian
    - Rate limit reminder: 1x per hari per mahasiswa
    - Preview GPA (PDF) untuk setiap mahasiswa

#### F-MON-02: Data Semua Mahasiswa (Kajur/Kaprodi)

- **Deskripsi:** Kajur/Kaprodi dapat melihat semua mahasiswa di lingkup mereka
- **Fitur:**
    - Kajur: semua mahasiswa di jurusan, dapat filter per prodi
    - Kaprodi: hanya mahasiswa di prodi sendiri
    - Ekspor data (nice-to-have)

#### F-MON-03: Data Dosen

- Daftar dosen di jurusan (untuk kajur/kaprodi)

#### F-MON-04: Riwayat Perwalian

- **Mahasiswa/Orang Tua:** Melihat riwayat perwalian milik mahasiswa
- **Dosen:** Melihat riwayat semua perwalian yang pernah diproses

---

### 3.6 Modul Notifikasi

#### F-NOTIF-01: Email via RabbitMQ

- Notifikasi dikirim ke RabbitMQ `notification_queue`, diproses oleh Notification Service terpisah
- Event yang didukung:
    - `advise-submission-to-advisor` — mahasiswa mengajukan perwalian
    - `advise-approved-by-advisor-for-student` — dosen menyetujui
    - `advise-signed-by-kajur-for-student` — kajur menandatangani
    - `advise-reminder-for-student` — reminder belum perwalian

---

### 3.7 Modul Biodata

#### F-BIO-01: Halaman Biodata

- Mahasiswa dapat melihat data diri, data akademik, dosen wali, riwayat semester
- Orang tua dapat melihat biodata anak

#### F-BIO-02: Preview KHS (PDF)

- Mahasiswa / orang tua dapat download preview PDF KHS per semester

---

## 4. Persyaratan Non-Fungsional

### 4.1 Keamanan (Security)

| Req    | Deskripsi                                            |
| ------ | ---------------------------------------------------- |
| SEC-01 | Semua komunikasi via HTTPS (TLS 1.2+)                |
| SEC-02 | Token OAuth tersimpan terenkripsi di database        |
| SEC-03 | Session dienkripsi, HTTP-only, Secure cookie         |
| SEC-04 | Rate limiting pada endpoint auth dan upload          |
| SEC-05 | RBAC: setiap route dilindungi role middleware        |
| SEC-06 | No `App_DEBUG=true` di production                    |
| SEC-07 | Tidak ada secret hardcode di codebase                |
| SEC-08 | SQL injection dicegah dengan ORM/parameterized query |
| SEC-09 | File upload validasi: hanya PDF, max 1MB             |
| SEC-10 | Nama file upload tidak menggunakan nama asli client  |

### 4.2 Performa (Performance)

| Req                   | Target                                                    |
| --------------------- | --------------------------------------------------------- |
| Response time halaman | < 2 detik (P95)                                           |
| PDF generation        | Async via queue, tidak blocking request                   |
| DB query              | Semua kolom yang di-filter memiliki index                 |
| Cache                 | Badge count dan data infrequently-changing di-cache Redis |
| Concurrent users      | Mampu handle 100 concurrent users tanpa degradasi         |

### 4.3 Ketersediaan (Availability)

| Req                     | Target                                     |
| ----------------------- | ------------------------------------------ |
| Uptime                  | ≥ 99.5% (staging), ≥ 99.9% (production)    |
| Recovery Time Objective | < 1 jam                                    |
| Error monitoring        | Sentry/GlitchTip terpasang dan alert aktif |

### 4.4 Skalabilitas (Scalability)

- Aplikasi dijalankan dalam container Docker
- Stateless application server (session di Redis)
- File storage di S3 (bukan local disk)
- Worker queue dapat di-scale horizontal

### 4.5 Maintainability

- Code coverage test minimal 60% untuk logic kritis
- Semua file helper stateless (static methods)
- Konvensi penamaan diikuti (controller prefix `C`, helper suffix `Helper`)
- Laravel Pint digunakan untuk code formatting

---

## 5. Arsitektur Teknis

### 5.1 Komponen Sistem

```
┌─────────────────────────────────────────────────────────────────┐
│                        BROWSER / CLIENT                         │
└────────────────────────────┬────────────────────────────────────┘
                             │ HTTPS
┌────────────────────────────▼────────────────────────────────────┐
│              FrankenPHP + Laravel Octane                        │
│              (Reverse Proxy + PHP App Server)                   │
├─────────────────────────────────────────────────────────────────┤
│  Controllers → Helpers → Models → Services                      │
└──────┬─────────┬────────────┬──────────────┬────────────────────┘
       │         │            │              │
┌──────▼──┐ ┌───▼───┐ ┌──────▼──┐ ┌────────▼──────────┐
│  MySQL  │ │ Redis  │ │   S3   │ │  RabbitMQ          │
│  (Data) │ │(Cache/ │ │(Files) │ │  (Notification     │
│         │ │ Queue/ │ │        │ │   Events)          │
│         │ │ Session│ │        │ │                    │
└─────────┘ └───────┘ └────────┘ └──────────┬─────────┘
                                             │
┌────────────────────────────────────────────▼────────────────────┐
│                    Notification Service                         │
│                    (Proses email/push notif)                    │
└─────────────────────────────────────────────────────────────────┘
       │
┌──────▼──────────────────────────────────────────────────────────┐
│                      SuperApp API                               │
│              (Source of truth: User, Student, Grade data)       │
└─────────────────────────────────────────────────────────────────┘
```

### 5.2 Data Flow Perwalian KHS

```
Mahasiswa                  SiWali                    External Services
    │                         │                              │
    │── Upload KHS ──────────►│── Store to S3 ──────────────►│
    │                         │                              │
    │── Submit Perwalian ────►│── Check JTIFORM ────────────►│
    │                         │◄─ OK ────────────────────────│
    │                         │── Find Dosen Wali ──────────►│ SuperApp
    │                         │◄─ Dosen data ────────────────│
    │                         │── Create Advise (pending) ──►│ DB
    │                         │                              │
Dosen ── Review & Approve ──►│── Generate PDF ─────────────►│ DomPDF
    │                         │── E-Sign ───────────────────►│ ESign API
    │                         │── Store PDF ────────────────►│ S3
    │                         │── Update status: signed ────►│ DB
    │                         │── Send Notif ───────────────►│ RabbitMQ
    │                         │                              │
Kajur ── Bulk Sign ─────────►│── Dispatch Job ─────────────►│ Queue
    │                         │                              │
    │                [Queue Worker]                          │
    │                         │── Regenerate PDF ───────────►│
    │                         │── Dual E-Sign ──────────────►│
    │                         │── Store PDF ────────────────►│ S3
    │                         │── Update status: done ──────►│ DB
    │                         │── Send Notif ───────────────►│ RabbitMQ
```

---

## 6. Batasan & Asumsi

### 6.1 Batasan Saat Ini (v1.0)

| Item                 | Keterangan                                |
| -------------------- | ----------------------------------------- |
| Jumlah perwalian KHS | 1x per mahasiswa per semester             |
| Format file KHS      | Hanya PDF, max 1MB                        |
| E-sign provider      | Hanya via ESign API internal Polije       |
| Notifikasi           | Hanya via email (melalui RabbitMQ)        |
| Bahasa               | Hanya Bahasa Indonesia                    |
| Platform             | Web browser (tidak ada mobile app native) |

### 6.2 Asumsi

- SuperApp adalah sistem otoritatif untuk data user, mahasiswa, dosen, dan nilai
- ESign API tersedia dan reliable dengan uptime ≥ 99%
- RabbitMQ dan Notification Service tersedia
- Redis tersedia untuk session, cache, dan queue
- S3-compatible storage tersedia di `s3.jtinova.com`
- Semua pengguna memiliki akun SuperApp aktif

---

## 7. Roadmap Fitur

### v1.0 — MVP (Current)

- [x] SSO Login via SuperApp
- [x] Perwalian KHS (pengajuan, review dosen, tanda tangan kajur)
- [x] Perwalian Non-KHS (pengajuan, isian dosen)
- [x] Dashboard per role
- [x] Data mahasiswa (monitoring dosen & kajur)
- [x] Notifikasi email
- [x] Riwayat perwalian
- [x] Biodata mahasiswa
- [x] Bulk sign kajur

### v1.1 — Security & Stability (Next)

- [ ] Token encryption di database
- [ ] Fix static state Octane
- [ ] PDF generation async (move to Job)
- [ ] Re-enable NonKHS dengan role middleware yang benar
- [ ] Unit test untuk alur kritis

### v1.2 — Feature Completion

- [ ] Re-enable Upload KHS dengan role middleware proper
- [ ] Notifikasi untuk kajur saat perwalian disetujui dosen
- [ ] Notifikasi pengajuan perwalian ke dosen wali
- [ ] Filter dan ekspor laporan perwalian (CSV/Excel)
- [ ] Dashboard parent dengan chart IPK trend

### v2.0 — Enhancement

- [ ] Pengingat otomatis (scheduled job) untuk mahasiswa yang belum perwalian
- [ ] Progressive Web App (PWA) untuk akses mobile yang lebih baik
- [ ] Integrasi dengan sistem akademik untuk input nilai secara langsung
- [ ] Multi-language support

---

## 8. Metrics Keberhasilan

| Metrik            | Target v1.0                                 | Cara Ukur                  |
| ----------------- | ------------------------------------------- | -------------------------- |
| Uptime            | ≥ 99.5%                                     | GlitchTip monitoring       |
| Error rate        | < 0.1% dari total request                   | Sentry error rate          |
| Adopsi mahasiswa  | ≥ 70% mahasiswa aktif dalam 1 bulan pertama | Jumlah Advise ter-create   |
| Waktu proses KHS  | < 3 hari dari submit → done                 | `created_at` vs updated_at |
| P95 response time | < 2 detik                                   | Server logs / APM          |
| Kajur bulk sign   | 100 dokumen dapat diproses < 10 menit       | Queue processing time      |

---

## 9. Risiko & Mitigasi

| Risiko                       | Probabilitas | Dampak | Mitigasi                                         |
| ---------------------------- | ------------ | ------ | ------------------------------------------------ |
| SuperApp API down            | Medium       | Tinggi | Error handling + user-friendly message           |
| ESign API timeout            | Medium       | Tinggi | Move PDF signing ke Job queue                    |
| S3 storage penuh             | Rendah       | Tinggi | Monitor storage usage, auto-cleanup file lama    |
| Token expired saat perwalian | Medium       | Medium | Re-auth flow, handle 401 response                |
| Queue worker crash           | Rendah       | Tinggi | `--tries=3`, monitor via GlitchTip               |
| Redis down                   | Rendah       | Tinggi | Fallback ke database driver untuk session        |
| Data race di Octane          | Medium       | Medium | Fix static state, gunakan proper request binding |

---

## 10. Glosarium

| Istilah    | Definisi                                                    |
| ---------- | ----------------------------------------------------------- |
| Perwalian  | Bimbingan akademik antara mahasiswa dan dosen wali          |
| KHS        | Kartu Hasil Studi — dokumen nilai akademik per semester     |
| Dosen Wali | Dosen yang ditugaskan sebagai pembimbing akademik mahasiswa |
| Kajur      | Ketua Jurusan                                               |
| Kaprodi    | Kepala Program Studi                                        |
| E-Sign     | Tanda tangan digital yang sah secara hukum                  |
| JTIFORM    | Sistem evaluasi dosen oleh mahasiswa di Polije              |
| SuperApp   | Sistem informasi terpadu Polije (sumber data otoritatif)    |
| SSO        | Single Sign-On — satu akun untuk semua sistem               |
