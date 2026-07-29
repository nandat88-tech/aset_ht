# PRD (Product Requirements Document)
## Sistem Monitoring Inventaris Handy Talky (HT) — Diskominfo

**Versi:** 1.0
**Tanggal:** 29 Juli 2026
**Status:** Draft awal (berdasarkan desain UI/UX terlampir)
**Pemilik Produk:** Diskominfo (Dinas Komunikasi dan Informatika)

---

## 1. Ringkasan Produk

Sistem ini adalah aplikasi web untuk mengelola dan memantau inventaris **Handy Talky (HT)** dan **charger** milik instansi, mencakup pendataan aset, peminjaman, pengembalian, kondisi aset, lokasi, serta pelaporan. Sistem menyediakan **halaman publik** (transparansi status aset ke masyarakat/pemangku kepentingan) dan **panel admin** (operasional internal untuk staf pengelola aset).

### 1.1 Latar Belakang
Diskominfo mengelola ratusan unit HT yang dipinjamkan ke berbagai bidang/OPD untuk kebutuhan operasional (event, siaga bencana, kegiatan lapangan). Pencatatan manual (Excel/buku) menyebabkan:
- Sulit melacak siapa yang sedang meminjam, berapa lama, dan kapan jatuh tempo.
- Tidak ada visibilitas kondisi aset (baik/rusak/perbaikan) secara real-time.
- Laporan bulanan/tahunan memakan waktu karena rekap manual.
- Tidak ada transparansi publik atas ketersediaan aset milik pemerintah.

### 1.2 Tujuan Produk
1. Digitalisasi pencatatan aset HT & charger (data master, kondisi, lokasi).
2. Mempercepat proses peminjaman & pengembalian dengan alur terstruktur (multi-step form).
3. Memberikan visibilitas real-time melalui dashboard (admin & publik).
4. Otomatisasi monitoring keterlambatan pengembalian dan notifikasi.
5. Menyediakan laporan siap ekspor (PDF/Excel) untuk kebutuhan audit & pertanggungjawaban.
6. Transparansi publik melalui halaman monitoring tanpa perlu login.

### 1.3 Ruang Lingkup (Scope)
**Termasuk dalam scope v1:**
- Manajemen data master HT, Charger, Lokasi, Pegawai/Peminjam.
- Modul transaksi Peminjaman (Borrowing) & Pengembalian (Returning).
- Dashboard admin (statistik, grafik, tabel monitoring live).
- Halaman publik (statistik ringkas tanpa login).
- Monitoring keterlambatan pengembalian + reminder.
- Modul laporan dengan filter & ekspor PDF/Excel.
- Manajemen pengguna & role (RBAC).
- Pusat notifikasi in-app.
- Pengaturan aplikasi dasar.

**Di luar scope v1 (dipertimbangkan untuk v2):**
- Integrasi barcode/QR-code scan fisik.
- Aplikasi mobile native.
- Integrasi SSO dengan sistem kepegawaian daerah.
- Pelacakan lokasi GPS real-time pada HT.
- Notifikasi WhatsApp/SMS gateway.

---

## 2. Target Pengguna & Role

| Role | Deskripsi | Akses Utama |
|---|---|---|
| **Publik / Guest** | Masyarakat umum atau pemangku kepentingan | Halaman monitoring publik (read-only, tanpa login) |
| **Peminjam (Borrower)** | Pegawai/OPD yang meminjam HT | Ajukan peminjaman, lihat status pinjaman sendiri |
| **Staf Operasional (Operator)** | Petugas pengelola aset harian | Kelola data master, proses peminjaman/pengembalian, lihat dashboard |
| **Admin** | Penanggung jawab sistem & aset | Semua akses operator + manajemen pengguna, role, laporan, pengaturan |
| **Super Admin** | Pengelola teknis sistem | Semua akses admin + activity log, konfigurasi sistem |

---

## 3. Struktur Halaman & Fitur (Mapping dari Desain UI)

### 3.1 Public Monitoring Page
**Tujuan:** Transparansi publik tanpa perlu login.
**Fitur:**
- Kartu ringkasan statistik: Total HT, Total Charger, HT Available, HT Borrowed, HT Under Repair, HT Damaged, Charger Available, Charger Damaged, Late Returns.
- Data bersifat read-only, auto-refresh berkala.
- Navigasi: Home, Live Monitoring, Statistics, About.

**Requirement fungsional:**
- FR-1.1: Sistem menampilkan data agregat aset tanpa memerlukan otentikasi.
- FR-1.2: Data diperbarui otomatis (polling/interval) atau saat halaman dimuat ulang.
- FR-1.3: Tidak menampilkan data pribadi peminjam di halaman publik.

### 3.2 Live Dashboard & Table
**Tujuan:** Monitoring visual kondisi aset secara real-time (dapat diakses publik/semi-publik).
**Fitur:**
- Grafik donat: Kondisi HT, Kondisi Charger.
- Grafik tren: Peminjaman Bulanan (Monthly Borrowing Trend), Pengembalian Bulanan (Monthly Return Trend).
- Grafik batang: Tepat Waktu vs Terlambat (On Time vs Late Returns).
- Tabel Live Monitoring: Serial Number, Asset Type, Location, Status, Borrower, Return Due, Remaining Days.
- Badge status berwarna (Borrowed, Available, Damaged, dsb).

**Requirement fungsional:**
- FR-2.1: Tabel mendukung pencarian, filter, dan pagination.
- FR-2.2: Status ditampilkan dengan indikator warna konsisten (lihat Design.md).
- FR-2.3: Grafik dapat difilter berdasarkan rentang waktu.

### 3.3 Admin Dashboard (Sidebar Collapsible)
**Tujuan:** Pusat kendali admin dengan ringkasan menyeluruh.
**Struktur navigasi sidebar:**
- **Dashboard**
- **Master Data**: Handy Talky, Charger, Locations, Employees
- **Transaksi**: Borrowing, Returning
- **Reports**: Asset Condition, Late Returns, Borrowers/Users, Export PDF/Excel
- **Administration**: Users, Roles, Activity Log
- **Settings**

**Fitur dashboard:**
- Kartu statistik (sama seperti halaman publik + Damaged Assets, Assets Under Repair).
- Grafik: Asset Condition, Borrowing Per Month, Asset Status, Assets by Location, Return Trend.
- Sidebar dapat di-collapse/expand (icon-only mode).

**Requirement fungsional:**
- FR-3.1: Sidebar collapsible menyimpan preferensi user (localStorage/DB).
- FR-3.2: Menu ditampilkan sesuai role (RBAC) — menu Administration hanya untuk Admin/Super Admin.
- FR-3.3: Statistik dashboard difilter berdasarkan periode.

### 3.4 Master Data — Handy Talky
**Tujuan:** CRUD data induk aset HT.
**Kolom tabel:** Serial Number, Inventory Number, Brand, Model, Frequency, Location, Purchase Date, Action.
**Fitur:** Search, Filter, Sorting, Import/Export (Excel), tombol Add Data, status badge (Available/Borrowed/Damaged/Under Repair).

**Requirement fungsional:**
- FR-4.1: Serial Number & Inventory Number bersifat unik (validasi sistem).
- FR-4.2: Import data massal via template Excel dengan validasi baris error.
- FR-4.3: Ekspor data terfilter ke Excel.
- FR-4.4: Setiap perubahan data tercatat di Activity Log.

### 3.5 Asset Detail Page
**Tujuan:** Detail lengkap 1 unit aset + riwayat.
**Fitur:**
- Foto aset, Serial Number, Inventory Number, Brand, Model, Frequency, Location, Condition, Current Status.
- Tab riwayat: Borrow History, Repair History, Location History, Return History.
- Tabel riwayat: Borrower, Department, Serial Number, Asset Condition.

**Requirement fungsional:**
- FR-5.1: Upload/ganti foto aset (format jpg/png, maks ukuran ditentukan).
- FR-5.2: Riwayat bersifat append-only (tidak bisa dihapus, hanya ditambah oleh sistem transaksi).
- FR-5.3: QR/kode unik aset dapat digenerate untuk kebutuhan v2.

### 3.6 Borrowing Module (Multi-step Form)
**Tujuan:** Alur pengajuan/pencatatan peminjaman terstruktur.
**Langkah (steps):**
1. **Borrower Info** — Nama Peminjam, Employee ID, Departemen, Nomor HP.
2. **Select Assets** — Pilih HT (multiple selection), pilih Charger, Tanggal Pinjam, Rencana Tanggal Kembali.
3. **Terms & Upload** — Catatan, upload dokumen (Surat Tugas/Berita Acara), persetujuan syarat & ketentuan.

**Requirement fungsional:**
- FR-6.1: Aset yang sudah dipinjam tidak muncul di daftar pilihan (real-time availability check).
- FR-6.2: Validasi setiap step sebelum lanjut ke step berikutnya.
- FR-6.3: Setelah submit, status aset otomatis berubah menjadi "Borrowed" dan histori tercatat.
- FR-6.4: Sistem mengirim notifikasi konfirmasi peminjaman.
- FR-6.5: Dokumen pendukung disimpan dan dapat diunduh kembali dari Asset Detail.

### 3.7 Returning Module
**Tujuan:** Pencatatan pengembalian aset dengan checklist kondisi.
**Fitur:**
- Checklist HT/Charger yang dikembalikan.
- Kolom: Serial Number, Return Date, Asset Condition (dropdown: Good/Damaged/Under Repair).
- Catatan (Notes) kondisi kerusakan.
- Upload dokumentasi foto pengembalian.

**Requirement fungsional:**
- FR-7.1: Kondisi aset yang dipilih otomatis memperbarui status master data aset.
- FR-7.2: Jika kondisi = Damaged, sistem otomatis membuat entri "Under Repair" opsional/menandai perlu tindak lanjut.
- FR-7.3: Return Date dibandingkan dengan Due Date untuk menentukan status "On Time"/"Late".
- FR-7.4: Bukti foto wajib diunggah jika kondisi = Damaged.

### 3.8 Late Return Monitoring
**Tujuan:** Memantau peminjaman yang melewati jatuh tempo.
**Kolom tabel:** Borrower, Department, Borrow Date, Due Date, Days Late, Status, Reminder (action).
**Fitur:** Badge status (Late/Overdue), tombol kirim pengingat (Reminder).

**Requirement fungsional:**
- FR-8.1: Sistem menghitung Days Late otomatis (harian, background job).
- FR-8.2: Tombol Reminder mengirim notifikasi ke peminjam (in-app, opsional email v2).
- FR-8.3: Daftar dapat difilter per departemen/rentang keterlambatan.

### 3.9 Reports Dashboard
**Tujuan:** Laporan interaktif untuk kebutuhan manajemen/audit.
**Fitur:**
- Filter: Bulan, Tahun, Departemen, Jenis Aset.
- Statistik: Total transaksi peminjaman (mis. Rp/jumlah), jumlah peminjam, distribusi aset per lokasi.
- Visualisasi statistik tambahan (grafik distribusi).
- Tombol Export PDF & Export Excel.

**Requirement fungsional:**
- FR-9.1: Laporan digenerate sesuai filter aktif secara real-time.
- FR-9.2: Ekspor PDF menghasilkan layout siap cetak (kop surat instansi).
- FR-9.3: Ekspor Excel menyertakan data mentah untuk diolah lebih lanjut.

### 3.10 User Management
**Tujuan:** Kelola akun pengguna sistem (bukan peminjam publik).
**Kolom tabel:** Name, Username, Role, Status (Active/Inactive), Action (Edit/Delete).
**Fitur:** Tombol Add User.

**Requirement fungsional:**
- FR-10.1: Hanya Admin/Super Admin dapat mengakses modul ini.
- FR-10.2: Password di-hash (bcrypt/argon2), tidak pernah ditampilkan plaintext.
- FR-10.3: Nonaktifkan (bukan hapus permanen) akun untuk menjaga jejak audit.

### 3.11 Settings Page
**Tujuan:** Konfigurasi dasar aplikasi.
**Fitur:** Nama aplikasi, logo instansi, informasi umum, preferensi notifikasi.

### 3.12 Notification Center
**Tujuan:** Pusat notifikasi in-app.
**Jenis notifikasi:**
- Late Returns
- Assets Damaged
- Assets Under Repair
- Upcoming Due Date
- Borrow Approved
- Return Completed

**Requirement fungsional:**
- FR-12.1: Notifikasi ditandai belum dibaca/sudah dibaca.
- FR-12.2: Notifikasi tersimpan minimal 90 hari (kebijakan retensi dapat disesuaikan).

---

## 4. Persyaratan Non-Fungsional

| Kategori | Persyaratan |
|---|---|
| **Performa** | Waktu muat dashboard < 2 detik untuk data hingga 1.000 aset. |
| **Keamanan** | Otentikasi berbasis token (JWT/session), RBAC per role, enkripsi password, HTTPS wajib. |
| **Skalabilitas** | Arsitektur mendukung penambahan jenis aset lain di masa depan (bukan hanya HT). |
| **Ketersediaan** | Target uptime 99% (sistem internal pemerintah, non-24/7 kritikal). |
| **Auditabilitas** | Semua perubahan data (create/update/delete) tercatat di Activity Log dengan timestamp & user. |
| **Aksesibilitas** | Kontras warna memenuhi WCAG AA, navigasi keyboard untuk form penting. |
| **Kompatibilitas** | Responsif desktop, tablet; mobile-friendly untuk halaman publik. |
| **Bahasa** | Bahasa Indonesia sebagai bahasa utama antarmuka. |
| **Backup** | Backup basis data harian otomatis, retensi minimal 30 hari. |

---

## 5. Model Data Utama (High-Level Entities)

- **Asset (HandyTalky)**: id, serialNumber, inventoryNumber, brand, model, frequency, locationId, condition, status, photoUrl, purchaseDate
- **Charger**: id, serialNumber, inventoryNumber, condition, status, assetId (relasi opsional ke HT)
- **Location**: id, name, description
- **Employee/Borrower**: id, name, employeeId, department, phone
- **BorrowTransaction**: id, borrowerId, assetIds[], chargerIds[], borrowDate, dueDate, notes, documentUrl, status
- **ReturnTransaction**: id, borrowTransactionId, returnDate, conditionPerAsset[], notes, documentationUrl, isLate
- **User (system account)**: id, name, username, passwordHash, role, status
- **Role**: id, name, permissions[]
- **ActivityLog**: id, userId, action, entity, entityId, timestamp
- **Notification**: id, userId (nullable=broadcast), type, message, isRead, createdAt

Skema lengkap tersedia pada `prisma/schema.prisma` di paket proyek.

---

## 6. User Stories Prioritas (v1)

1. Sebagai **Operator**, saya ingin mendaftarkan aset HT baru agar tercatat di sistem.
2. Sebagai **Operator**, saya ingin memproses peminjaman lewat form bertahap agar data lengkap dan tervalidasi.
3. Sebagai **Operator**, saya ingin mencatat pengembalian dan kondisi aset agar status aset selalu akurat.
4. Sebagai **Admin**, saya ingin melihat dashboard ringkas agar dapat mengambil keputusan cepat.
5. Sebagai **Admin**, saya ingin mengekspor laporan bulanan agar dapat dilaporkan ke pimpinan.
6. Sebagai **Publik**, saya ingin melihat status ketersediaan aset tanpa login untuk transparansi.
7. Sebagai **Admin**, saya ingin menerima notifikasi keterlambatan agar dapat menindaklanjuti peminjam.
8. Sebagai **Super Admin**, saya ingin mengatur hak akses pengguna agar keamanan data terjaga.

---

## 7. Metrik Keberhasilan (KPI)

- Waktu proses peminjaman turun dari (manual) rata-rata 15 menit menjadi < 3 menit.
- Tingkat keterlambatan pengembalian turun minimal 30% dalam 6 bulan setelah rilis (berkat reminder otomatis).
- 100% aset memiliki data master lengkap dan histori terlacak.
- Laporan bulanan dapat dihasilkan dalam < 1 menit (vs manual berjam-jam).

---

## 8. Rekomendasi Tumpukan Teknologi (Tech Stack)

| Layer | Rekomendasi |
|---|---|
| Frontend | Next.js 14 (App Router) + TypeScript + Tailwind CSS + shadcn/ui |
| Grafik | Recharts |
| Backend/API | Next.js Route Handlers (atau NestJS bila ingin backend terpisah) |
| ORM & Database | Prisma ORM + PostgreSQL |
| Otentikasi | NextAuth.js (credentials + RBAC) |
| Penyimpanan File | Local storage (dev) → S3-compatible (mis. MinIO) untuk produksi |
| Ekspor PDF | `@react-pdf/renderer` atau Puppeteer |
| Ekspor Excel | `exceljs` |
| Deployment | Docker + VPS/Server pemerintah, atau Vercel (frontend) + managed Postgres |

Detail konfigurasi awal tersedia dalam paket proyek (`package.json`, `prisma/schema.prisma`, `.env.example`, `README.md`).

---

## 9. Asumsi & Batasan

- Sistem digunakan dalam jaringan intranet/internet instansi; pengguna admin memiliki akun terverifikasi.
- Satu unit HT hanya dapat dipinjam oleh satu peminjam pada satu waktu (tidak ada peminjaman paralel untuk unit yang sama).
- Foto/dokumen disimpan maksimal berukuran wajar (misal 5MB per file) — dikonfigurasi di Settings.
- Perhitungan "Days Late" menggunakan hari kalender, dapat disesuaikan menjadi hari kerja pada v2.

---

## 10. Roadmap Bertahap

| Fase | Fokus |
|---|---|
| **Fase 1 (MVP)** | Master Data, Borrowing, Returning, Dashboard Admin dasar, Auth & RBAC |
| **Fase 2** | Public Monitoring Page, Live Dashboard grafik lengkap, Late Return Monitoring + reminder |
| **Fase 3** | Reports Dashboard + Export PDF/Excel, Notification Center, Activity Log |
| **Fase 4** | Optimasi UX, aksesibilitas, audit keamanan, dokumentasi serah terima |
