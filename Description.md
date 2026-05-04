# 🧙 Hogwarts API — Project Documentation

## Deskripsi Project

**Hogwarts API** adalah aplikasi web berbasis **Laravel 12** yang mensimulasikan sistem akademik Sekolah Sihir Hogwarts. Aplikasi ini memiliki dua antarmuka:

1. **Web Interface** — UI berbasis Blade untuk interaksi langsung (login, dashboard, sorting hat, dll.)
2. **REST API** — Endpoint JSON dengan autentikasi JWT untuk integrasi eksternal

Sistem mendukung dua role pengguna:
- **Student** (`@student.hogwarts.ac.id`) — Membuat ramuan, melihat raport, mengelola inventori
- **Guru** (`@hogwarts.ac.id`) — Memvalidasi ramuan, mengedit raport, melihat daftar siswa

Data disimpan dalam file **JSON flat-file** (bukan database relasional), dikelola melalui `JsonService`.

---

## Arsitektur

```
Client (Browser / API Consumer)
        │
        ▼
  Laravel Router (routes/web.php & routes/api.php)
        │
        ├── Web Routes ──► Middleware (auth.session, role)
        │                        │
        │                        ▼
        │               Blade Controllers
        │               (AuthController, StudentDashboardController, dll.)
        │                        │
        │                        ▼
        │               Blade Views (resources/views/)
        │
        └── API Routes ──► Middleware (ApiAuthMiddleware / JWT)
                                 │
                                 ▼
                          ApiController
                                 │
                                 ▼
                    Services (JsonService, JwtService)
                                 │
                                 ▼
                    JSON Flat-File Storage (storage/data/*.json)
```

### Komponen Utama

| Komponen | Lokasi | Fungsi |
|---|---|---|
| `JsonService` | `app/Services/JsonService.php` | Baca/tulis file JSON sebagai database |
| `JwtService` | `app/Services/JwtService.php` | Generate & verifikasi JWT token (HS256, exp 1 jam) |
| `ApiAuthMiddleware` | `app/Http/Middleware/ApiAuthMiddleware.php` | Validasi Bearer token untuk API |
| `AuthTokenMiddleware` | `app/Http/Middleware/AuthTokenMiddleware.php` | Validasi session untuk Web |
| `RoleMiddleware` | `app/Http/Middleware/RoleMiddleware.php` | Otorisasi berdasarkan role (student/guru) |

---

## Struktur File

```
hogwarts-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ApiController.php          # Semua endpoint REST API
│   │   │   ├── AuthController.php         # Login, register, logout (Web)
│   │   │   ├── SortingHatController.php   # Kuis sorting hat & assign house
│   │   │   ├── StudentDashboardController.php
│   │   │   ├── GuruDashboardController.php
│   │   │   ├── PotionController.php       # CRUD ramuan (Web - Student)
│   │   │   ├── GuruPotionController.php   # Validasi ramuan (Web - Guru)
│   │   │   ├── InventoryController.php    # Inventori ramuan approved
│   │   │   ├── RaporController.php        # Raport akademik
│   │   │   └── UserController.php         # Profil & manajemen user
│   │   └── Middleware/
│   │       ├── ApiAuthMiddleware.php      # JWT auth untuk API
│   │       ├── AuthTokenMiddleware.php    # Session auth untuk Web
│   │       └── RoleMiddleware.php         # Role-based access control
│   ├── Models/
│   │   └── User.php
│   └── Services/
│       ├── JsonService.php                # Abstraksi baca/tulis JSON
│       └── JwtService.php                 # JWT generate & verify
│
├── routes/
│   ├── api.php                            # Semua route /api/*
│   └── web.php                            # Route web (Blade)
│
├── resources/views/
│   ├── auth/
│   │   ├── login.blade.php
│   │   ├── register.blade.php
│   │   ├── sorting-hat.blade.php          # Kuis 3 pertanyaan
│   │   └── sorting-result.blade.php       # Hasil house & wand
│   ├── student/
│   │   ├── dashboard.blade.php
│   │   ├── profile.blade.php
│   │   ├── inventory.blade.php
│   │   ├── rapor.blade.php
│   │   └── potions/ (index, create, show)
│   ├── guru/
│   │   ├── dashboard.blade.php
│   │   ├── users.blade.php
│   │   ├── potions/ (index, show)
│   │   └── rapor/ (index, edit)
│   └── layouts/app.blade.php
│
├── storage/data/                          # Flat-file "database"
│   ├── users.json
│   ├── potions.json
│   ├── rapor.json
│   └── wands.json
│
├── public/
│   ├── images/                            # Aset gambar (house, wand, dll.)
│   ├── sounds/                            # Suara sorting hat per house
│   └── uploads/avatars/                   # Foto profil user
│
└── .env                                   # Konfigurasi (JWT_SECRET, APP_KEY, dll.)
```

---

## Alur Data

### 1. Registrasi & Sorting Hat (Student)

```
User ──POST /register──► AuthController::register()
        │
        ├── Validasi: name, email (@student.hogwarts.ac.id), password, role
        ├── Hash password (bcrypt)
        ├── Simpan ke users.json
        ├── Auto-generate 8 semester raport kosong (RaporController::generateForStudent)
        ├── Set session user_id, user_role
        │
        └──► Redirect ke /sorting-hat
                │
                ▼
        SortingHatController::questions()  ──► Tampil 3 pertanyaan
                │
        POST /sorting-hat (jawaban a/b/c/d)
                │
                ├── Tally votes per house (Gryffindor/Ravenclaw/Hufflepuff/Slytherin)
                ├── Assign house dengan votes terbanyak
                ├── Assign wand secara random dari wands.json
                ├── Update users.json (house + wand_id)
                ├── Set session user_house
                │
                └──► Redirect ke /sorting-hat/result ──► /student/dashboard
```

### 2. Login

```
User ──POST /login──► AuthController::login()
        │
        ├── Cari user di users.json by email
        ├── Verifikasi password (password_verify)
        ├── Set session (user_id, user_name, user_email, user_role, user_house)
        │
        ├── Student tanpa house? ──► /sorting-hat
        ├── Student dengan house? ──► /student/dashboard
        └── Guru?                ──► /guru/dashboard
```

### 3. Alur Ramuan (Potion)

```
Student ──POST /student/potions──► PotionController::store()
        │
        ├── Validasi semua field ramuan
        ├── Buat entry baru di potions.json (status: "pending")
        │
        └── Guru ──GET /guru/potions/{id}──► GuruPotionController::show()
                │
                POST /guru/potions/{id}/validate
                │
                ├── Set status: "approved" / "rejected"
                ├── Set rating (1-10), guru_comment, validated_by, validated_at
                │
                └── Jika approved ──► Muncul di Inventori student
```

### 4. Alur Raport

```
Register Student ──► RaporController::generateForStudent()
        │
        └── Buat 8 entry raport (Semester 1-8) dengan nilai 0 di rapor.json

Guru ──GET /guru/rapor/edit?student_id=&semester=──► Edit form
        │
        PUT /guru/rapor/{id}
        │
        ├── Update nilai per mata pelajaran
        ├── Hitung nilai_huruf (A/B+/B/C/D/E)
        └── Simpan ke rapor.json

Student ──GET /student/rapor──► Lihat raport per semester
```

### 5. Alur API (JWT)

```
Client ──POST /api/auth/login──► ApiController::login()
        │
        ├── Verifikasi credentials dari users.json
        └── Return JWT token (exp: 1 jam)

Client ──GET /api/* ──► ApiAuthMiddleware
        │
        ├── Ambil Bearer token dari header Authorization
        ├── JwtService::verify() ──► decode payload (sub, role, exp)
        ├── Inject $request->user = decoded payload
        └── Lanjut ke ApiController method
```

---

## API Endpoints

Base URL: `http://localhost:8000/api`

### Auth

#### POST `/api/auth/register`
Registrasi user baru.

**Request:**
```json
{
  "name": "Harry Potter",
  "email": "harry@student.hogwarts.ac.id",
  "password": "password123",
  "role": "student"
}
```

**Response 201:**
```json
{
  "message": "Registrasi berhasil.",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": "u2_1700000000",
    "name": "Harry Potter",
    "email": "harry@student.hogwarts.ac.id",
    "role": "student",
    "level": 1,
    "xp": 0,
    "max_xp": 100,
    "house": null,
    "wand_id": null,
    "created_at": "2024-01-01T00:00:00+00:00"
  }
}
```

**Response 422 (email sudah ada):**
```json
{ "message": "Email sudah terdaftar." }
```

**Aturan email:**
- Student: harus `@student.hogwarts.ac.id`
- Guru: harus `@hogwarts.ac.id`

---

#### POST `/api/auth/login`
Login dan dapatkan JWT token.

**Request:**
```json
{
  "email": "mione@student.hogwarts.ac.id",
  "password": "password123"
}
```

**Response 200:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": "u1",
    "name": "Hermione Granger",
    "email": "mione@student.hogwarts.ac.id",
    "role": "student",
    "level": 5,
    "xp": 420,
    "max_xp": 500,
    "house": "Gryffindor",
    "wand_id": "w1"
  }
}
```

**Response 401:**
```json
{ "message": "Email atau password salah." }
```

---

#### GET `/api/auth/me`
🔒 Requires: Bearer Token

**Response 200:**
```json
{
  "id": "u1",
  "name": "Hermione Granger",
  "email": "mione@student.hogwarts.ac.id",
  "role": "student",
  "level": 5,
  "xp": 420,
  "house": "Gryffindor",
  "wand_id": "w1"
}
```

---

### Users (Guru Only)

#### GET `/api/users`
🔒 Requires: Bearer Token (role: guru)

Mengembalikan semua student.

**Response 200:**
```json
[
  {
    "id": "u1",
    "name": "Hermione Granger",
    "email": "mione@student.hogwarts.ac.id",
    "role": "student",
    "level": 5,
    "house": "Gryffindor"
  }
]
```

---

#### GET `/api/users/{id}`
🔒 Requires: Bearer Token (role: guru)

**Response 200:**
```json
{
  "id": "u1",
  "name": "Hermione Granger",
  "email": "mione@student.hogwarts.ac.id",
  "role": "student",
  "level": 5,
  "xp": 420,
  "house": "Gryffindor",
  "wand_id": "w1"
}
```

**Response 404:**
```json
{ "message": "Not Found" }
```

---

### Potions (Ramuan)

#### GET `/api/potions`
🔒 Requires: Bearer Token

- Student: hanya melihat ramuan milik sendiri
- Guru: melihat semua ramuan

**Response 200:**
```json
[
  {
    "id": "p1",
    "student_id": "u1",
    "name": "Polyjuice Potion",
    "description": "A complex potion...",
    "ingredients": ["Lacewing flies", "Leeches"],
    "cara_pembuatan": "1. Rebus lacewing flies...",
    "tingkat_kesulitan": "Hard",
    "durasi_efek": "1 jam",
    "warna_ramuan": "Abu-abu keruh",
    "efek_samping": "Rasa tidak enak...",
    "kelemahan": "Tidak bekerja untuk transformasi hewan",
    "status": "approved",
    "rating": 9,
    "guru_comment": "Excellent work!",
    "validated_by": "g1",
    "created_at": "2024-10-05T10:00:00",
    "validated_at": "2024-10-06T14:00:00"
  }
]
```

---

#### POST `/api/potions`
🔒 Requires: Bearer Token (role: student)

Membuat ramuan baru (status awal: `pending`).

**Request:**
```json
{
  "name": "Veritaserum",
  "description": "Potion yang memaksa peminum untuk berkata jujur.",
  "ingredients": ["Biji Asphodel", "Akar Valerian", "Tetesan embun pagi"],
  "cara_pembuatan": "1. Campurkan semua bahan. 2. Didihkan selama 1 bulan.",
  "tingkat_kesulitan": "Hard",
  "durasi_efek": "30 menit",
  "warna_ramuan": "Bening seperti air",
  "efek_samping": "Tidak ada efek samping fisik",
  "kelemahan": "Bisa dilawan dengan Occlumency"
}
```

**Response 201:**
```json
{
  "id": "p3_1700000000",
  "student_id": "u1",
  "name": "Veritaserum",
  "status": "pending",
  "rating": null,
  "guru_comment": null,
  "created_at": "2024-01-01T00:00:00+00:00"
}
```

**Nilai `tingkat_kesulitan` yang valid:** `Easy`, `Medium`, `Hard`

---

#### GET `/api/potions/{id}`
🔒 Requires: Bearer Token

Student hanya bisa akses ramuan milik sendiri.

**Response 403 (bukan milik student):**
```json
{ "message": "Akses ditolak." }
```

---

#### DELETE `/api/potions/{id}`
🔒 Requires: Bearer Token (role: student)

Hanya bisa menghapus ramuan dengan status `pending`.

**Response 200:**
```json
{ "message": "Ramuan dihapus." }
```

**Response 422 (sudah divalidasi):**
```json
{ "message": "Hanya ramuan pending yang bisa dihapus." }
```

---

#### POST `/api/potions/{id}/validate`
🔒 Requires: Bearer Token (role: guru)

Validasi ramuan student.

**Request:**
```json
{
  "status": "approved",
  "rating": 8,
  "guru_comment": "Good work! Warna sudah tepat."
}
```

**Response 200:**
```json
{ "message": "Validasi berhasil." }
```

**Nilai `status` yang valid:** `approved`, `rejected`
**Nilai `rating`:** integer 1–10 (nullable)

---

### Inventory

#### GET `/api/inventory`
🔒 Requires: Bearer Token (role: student)

Mengembalikan ramuan milik student yang sudah `approved`.

**Response 200:**
```json
[
  {
    "id": "p1",
    "name": "Polyjuice Potion",
    "status": "approved",
    "rating": 9,
    "guru_comment": "Excellent work!"
  }
]
```

---

#### DELETE `/api/inventory/{id}`
🔒 Requires: Bearer Token (role: student)

Hapus item dari inventori (menghapus data potion).

**Response 200:**
```json
{ "message": "Dihapus dari inventori." }
```

---

### Raport

#### GET `/api/rapor`
🔒 Requires: Bearer Token

- Student: hanya raport milik sendiri (8 semester)
- Guru: semua raport semua student

**Response 200:**
```json
[
  {
    "id": "r_u1_semester1_1700000000",
    "student_id": "u1",
    "semester": "Semester 1",
    "mata_pelajaran": [
      {
        "nama": "Potion Making",
        "nilai": 85,
        "nilai_huruf": "A-",
        "guru_pengampu": "Prof. Snape",
        "keterangan": "Sangat baik"
      },
      {
        "nama": "Transfiguration",
        "nilai": 90,
        "nilai_huruf": "A",
        "guru_pengampu": "Prof. McGonagall",
        "keterangan": "Luar biasa"
      }
    ],
    "catatan": "Siswa menunjukkan perkembangan yang baik.",
    "updated_by": "g1",
    "updated_at": "2024-12-01T10:00:00+00:00"
  }
]
```

---

#### GET `/api/rapor/{id}`
🔒 Requires: Bearer Token

Student hanya bisa akses raport milik sendiri.

---

#### PUT `/api/rapor/{id}`
🔒 Requires: Bearer Token (role: guru)

Update nilai raport student.

**Request:**
```json
{
  "mata_pelajaran": [
    { "nilai": 88, "keterangan": "Sangat baik dalam praktikum" },
    { "nilai": 92, "keterangan": "Penguasaan teori sangat kuat" },
    { "nilai": 75, "keterangan": "Perlu lebih rajin" },
    { "nilai": 80, "keterangan": "Baik" },
    { "nilai": 85, "keterangan": "Konsisten" }
  ],
  "catatan": "Siswa menunjukkan kemajuan pesat di semester ini."
}
```

**Response 200:**
```json
{ "message": "Raport diperbarui." }
```

**Konversi nilai ke huruf:**
| Nilai | Huruf |
|---|---|
| ≥ 90 | A |
| ≥ 85 | A- |
| ≥ 80 | B+ |
| ≥ 75 | B |
| ≥ 70 | B- |
| ≥ 65 | C+ |
| ≥ 60 | C |
| ≥ 55 | C- |
| ≥ 50 | D |
| < 50 | E |

---

## Autentikasi API

Semua endpoint protected menggunakan **JWT Bearer Token**.

**Header yang diperlukan:**
```
Authorization: Bearer <token>
```

**JWT Payload:**
```json
{
  "iss": "hogwarts-api",
  "sub": "u1",
  "role": "student",
  "iat": 1700000000,
  "exp": 1700003600
}
```

Token berlaku selama **1 jam** sejak diterbitkan.

---

## Data Seed (Akun Default)

| Nama | Email | Password | Role | House |
|---|---|---|---|---|
| Hermione Granger | `mione@student.hogwarts.ac.id` | `password123` | student | Gryffindor |
| Prof. Snape | `snivellus@hogwarts.ac.id` | `password123` | guru | — |

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Framework | Laravel 12 (PHP 8.2+) |
| Auth API | firebase/php-jwt (HS256) |
| Auth Web | Laravel Session |
| Database | JSON Flat-file (`storage/data/`) |
| Frontend | Blade Templates + Vite |
| Testing | PHPUnit 11 |

---

## Cara Menjalankan

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Jalankan server
composer run dev
# atau
php artisan serve
```

Akses web: `http://localhost:8000`
Akses API: `http://localhost:8000/api`
