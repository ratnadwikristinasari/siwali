# AGENTS.md — SiWali (Sistem Informasi Perwalian)

> File ini adalah panduan konteks untuk AI agent (Antigravity, Claude, Cursor, Copilot, dsb.)
> yang membantu mengembangkan project ini.

---

## 🎯 Deskripsi Project

**SiWali** adalah sistem informasi perwalian akademik untuk Politeknik Negeri Jember (Polije).
Aplikasi ini mengelola proses perwalian mahasiswa (bimbingan akademik antara mahasiswa dan dosen wali),
termasuk pengajuan, persetujuan, tanda tangan digital (e-sign), dan notifikasi.

### Aktor / Role

| Role | Keterangan |
|------|-----------|
| `student` | Mahasiswa — dapat mengajukan perwalian |
| `orang_tua` | Orang tua mahasiswa — dapat melihat status perwalian |
| `lecturer` | Dosen wali — dapat menyetujui dan menandatangani perwalian |
| `kaprodi` | Kepala Program Studi — akses data semua mahasiswa di prodi |
| `kajur` | Ketua Jurusan — akses penuh, bulk sign, data semua prodi |
| `sekjur` | Sekretaris Jurusan — akses view detail mahasiswa |

### Tipe Perwalian

- `gpa_advising` — Perwalian berbasis KHS (Kartu Hasil Studi), memerlukan upload KHS
- `non_gpa_advising` — Perwalian umum/konseling, tanpa KHS

---

## 🏗️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Framework | Laravel 12 (PHP 8.2+) |
| Frontend | Blade + Livewire 4 + Vite |
| Server | FrankenPHP + Laravel Octane |
| Database | MySQL |
| Cache & Session | Redis (staging/production), Database (local) |
| Queue | Redis (staging/production), RabbitMQ (untuk notifikasi) |
| Storage | AWS S3-compatible (s3.jtinova.com) |
| Error Tracking | GlitchTip (Sentry-compatible) |
| Auth | OAuth2 via SuperApp internal (custom SSO) |

---

## 📁 Struktur Direktori Penting

```
app/
├── Dto/Auth/             # Data Transfer Object untuk auth
├── Helpers/              # Helper classes (stateless, static methods)
│   ├── AuthHelper.php    # Fetch user dari SuperApp API
│   ├── GlobalHelper.php  # Base HTTP request ke SuperApp
│   ├── MahasiswaHelper.php
│   ├── DosenHelper.php
│   ├── ESignApiHelper.php
│   ├── FileHelper.php    # Upload/delete file ke S3
│   ├── DashboardHelper.php
│   └── ...
├── Http/
│   ├── Controllers/
│   │   ├── Auth/OAuthController.php   # SSO OAuth callback
│   │   ├── dashboard/                 # Dashboard per role
│   │   ├── page/                      # Halaman utama (Perwalian, Mahasiswa, dll)
│   │   ├── history/                   # Riwayat perwalian
│   │   └── DropzoneController.php     # Upload KHS file
│   └── Middleware/
│       └── RoleMiddleware.php         # RBAC middleware
├── Jobs/
│   └── ProcessSignDocument.php       # Async job: generate & sign PDF KHS
├── Models/
│   ├── User.php          # User model + role helpers (hasRole, hasAnyRole, dll)
│   ├── Advise.php        # Model perwalian
│   └── StudentParent.php # Relasi mahasiswa-orang tua
├── Services/
│   └── NotificationPublisher.php     # Push ke RabbitMQ notification_queue
└── Providers/
    ├── AppServiceProvider.php         # Blade directives @role/@notrole, force HTTPS
    └── MenuServiceProvider.php
```

---

## 🔐 Sistem Autentikasi & Otorisasi

### Alur SSO OAuth2

```
User → /auth/login
  → Redirect ke SuperApp /oauth/authorize
  → SuperApp callback ke /auth/callback?code=xxx
  → Exchange code → token
  → Fetch user detail dari SuperApp /auth/me
  → updateOrCreate User di DB lokal
  → Auth::login($user)
  → Redirect ke dashboard
```

### Token Storage

- Token OAuth **disimpan di kolom `token` pada tabel `users`** (plain text di DB).
- Token digunakan di setiap request ke SuperApp API.

### Role Management

- Role disimpan sebagai JSON array di kolom `roles` pada tabel `users`.
- Helper method ada di `User` model: `hasRole()`, `hasAnyRole()`, `hasAllRoles()`, `hasNoRole()`.
- Middleware: `role:student|orang_tua` — dipisah `|` untuk OR, prefix `all,` untuk AND.
- Blade directive: `@role('lecturer')` / `@notrole('student')`.

---

## 🌊 Alur Bisnis Utama

### Perwalian KHS (gpa_advising)

1. Mahasiswa upload KHS → `DropzoneController@khs` → simpan ke S3
2. Mahasiswa submit form perwalian → `CPerwalian@store`
   - Validasi: cek JTIFORM (evaluasi dosen) sudah diisi
   - Cek sudah submit perwalian semester ini (1x per semester)
   - Cari dosen wali dari SuperApp
   - Simpan record `Advise` dengan status `Pending`
3. Dosen wali review → `CPerwalian@update`
   - Generate PDF KHS (DomPDF)
   - E-Sign dosen wali (ESignApiHelper)
   - Upload ke S3
   - Status → `signed`
   - Kirim notifikasi ke mahasiswa via RabbitMQ
4. Kajur bulk sign → `CNeedSign@signBulk`
   - Dispatch `ProcessSignDocument` job (queue)
   - Job: regenerate PDF, dual e-sign (dosen + kajur), upload S3
   - Status → `done`
   - Notifikasi mahasiswa

### Perwalian Non-KHS (non_gpa_advising)

1. Mahasiswa submit form → `CperwalianNonKHS@storekhs`
2. Dosen wali isi masukan → status `done`
3. Tidak ada generate PDF atau e-sign

---

## ⚠️ Aturan Pengembangan (WAJIB DIIKUTI)

### Konvensi Penamaan

- **Controller**: prefix `C` + nama halaman, CamelCase (contoh: `CMahasiswa`, `CPerwalian`)
- **Helper**: suffix `Helper` (contoh: `MahasiswaHelper`, `FileHelper`)
- **Job**: prefix `Process` (contoh: `ProcessSignDocument`)
- **View**: kebab-case di bawah `resources/views/content/`

### Aturan Coding

1. **Selalu gunakan validation** di controller sebelum memproses request
2. **Jangan pernah expose `$request->all()`** ke model secara langsung — gunakan array eksplisit
3. **Token OAuth jangan di-log** — token sensitif
4. **HTTP Client ke SuperApp** selalu lewat `GlobalHelper::requestWithToken()` kecuali ada alasan khusus
5. **File upload** selalu lewat `FileHelper::storeFile()` → stored ke S3, bukan local
6. **Role check** wajib menggunakan RoleMiddleware di route — jangan hardcode di controller
7. **Perubahan status Advise** harus terurut: `Pending` → `signed` → `done`

### Hal yang DILARANG

- ❌ Jangan simpan password atau secret ke dalam kode
- ❌ Jangan gunakan `Http::withoutVerifying()` di production (hanya boleh di local)
- ❌ Jangan gunakan `dd()` atau `var_dump()` di production code
- ❌ Jangan commit file `.env`, `.env.staging`, `.env.production`
- ❌ Jangan bypass RoleMiddleware untuk route yang butuh otorisasi
- ❌ Jangan gunakan raw SQL tanpa parameterized query

---

## 🔌 Integrasi Eksternal

### SuperApp API

- Base URL: `config('app.super_app_url')` (public) / `config('app.super_app_url_internal')` (internal)
- Auth: Bearer token dari kolom `users.token`
- Endpoint utama: `/auth/me`, `/supervisor-lectures/by-lecture`, `/students/*`, `/grades/*`

### JTIFORM

- URL: `config('app.jtiform_service_url')`
- Digunakan untuk cek apakah mahasiswa sudah isi evaluasi dosen
- Helper: `CheckJtiformStatusHelper::check()`

### ESign API

- Helper: `ESignApiHelper`
- Digunakan untuk sign PDF (dosen wali + kajur dual signature)

### RabbitMQ Notification

- Service: `NotificationPublisher`
- Queue: `notification_queue`
- Event types: `advise-submission-to-advisor`, `advise-approved-by-advisor-for-student`, `advise-signed-by-kajur-for-student`, `advise-reminder-for-student`

---

## 🐳 Deployment

### Staging / Production Docker Build

```bash
docker build -t siwali-staging -f Dockerfile.staging .
docker run -d --name siwali-staging -p 8003:8000 --restart unless-stopped siwali-staging
docker run -d --name siwali-staging-worker --restart unless-stopped siwali-staging php artisan queue:work --sleep=3 --tries=3
```

### Konfigurasi Production Wajib di `.env.production`

```env
APP_ENV=production
APP_DEBUG=false
SESSION_DRIVER=redis
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
CACHE_STORE=redis
QUEUE_CONNECTION=redis
LOG_LEVEL=error
OCTANE_HTTPS=true
SENTRY_TRACES_SAMPLE_RATE=0.2
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Pest langsung
./vendor/bin/pest

# Clear cache sebelum test
php artisan config:clear && php artisan test
```

---

## 📝 Catatan Penting untuk AI Agent

1. **Jangan ubah alur autentikasi OAuth** tanpa memahami flow SuperApp SSO
2. **Token mahasiswa/dosen tersimpan di DB** — query ke SuperApp selalu butuh token user yang login
3. **PDF generation (DomPDF) berjalan synchronous** di `CPerwalian@update` — pertimbangkan pindah ke Job jika ada timeout
4. **`static $counts = null`** di `AppServiceProvider` menggunakan state statis — **berbahaya di Octane** karena worker persistent, harus di-flush setiap request
5. **Semua upload file ke S3**, bukan local storage (kecuali environment local)
6. **`Http::withoutVerifying()`** masih ada di `AuthHelper.php` — HARUS dihapus sebelum production
7. **`.env.staging` berisi secret nyata** dan sudah diignore oleh git — jangan pernah commit
8. **Route `/form-perwalian` dan `/upload`** tidak memiliki role middleware eksplisit — hanya `auth`
9. **`MahasiswaHelper::getMahasiswa()`** melakukan dua kali request HTTP (ada bug duplikat pemanggilan) — perlu diperbaiki
10. **`static $counts`** di `AppServiceProvider` tidak pernah di-reset per request di Octane — harus gunakan `Request::flush` atau hindari static
