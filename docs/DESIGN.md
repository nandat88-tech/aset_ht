# DESIGN.md
## Design System — Sistem Monitoring Inventaris Handy Talky (Diskominfo)

**Versi:** 1.0
**Berdasarkan:** Mockup UI/UX terlampir (13 layar)

---

## 1. Prinsip Desain

1. **Kejelasan data dahulu (data-first clarity)** — Dashboard pemerintah harus mudah dibaca sekilas oleh pejabat maupun publik; gunakan kartu statistik besar & grafik sederhana.
2. **Konsistensi status berbasis warna** — Setiap kondisi aset (Available, Borrowed, Damaged, Under Repair) selalu direpresentasikan dengan warna & badge yang sama di seluruh halaman.
3. **Formal namun modern** — Nuansa institusional (biru sebagai warna korporat pemerintah) dipadukan dengan UI modern (rounded cards, soft shadow, whitespace cukup).
4. **Progressive disclosure** — Form kompleks (Borrowing) dipecah menjadi multi-step agar tidak membebani pengguna.
5. **Aksesibel untuk publik** — Halaman publik harus ringan, cepat, dan mudah dipahami tanpa pelatihan.

---

## 2. Palet Warna

### 2.1 Warna Primer
| Token | Hex | Penggunaan |
|---|---|---|
| `--primary` | `#2563EB` (blue-600) | Header, tombol utama, ikon aktif, link |
| `--primary-dark` | `#1E40AF` (blue-800) | Hover state, sidebar aktif |
| `--primary-light` | `#DBEAFE` (blue-100) | Background highlight, badge info |

### 2.2 Warna Status (Semantic)
| Status | Token | Hex | Konteks |
|---|---|---|---|
| Available / Good / Success | `--success` | `#16A34A` (green-600) | HT tersedia, kondisi baik, on-time |
| Borrowed / Info | `--info` | `#2563EB` (blue-600) | Sedang dipinjam |
| Under Repair / Warning | `--warning` | `#F59E0B` (amber-500) | Dalam perbaikan, mendekati jatuh tempo |
| Damaged / Late / Danger | `--danger` | `#DC2626` (red-600) | Rusak, terlambat, overdue |
| Neutral / Inactive | `--neutral` | `#6B7280` (gray-500) | Nonaktif, draft |

### 2.3 Warna Netral (UI Base)
| Token | Hex | Penggunaan |
|---|---|---|
| `--bg-page` | `#F5F7FA` | Background halaman |
| `--bg-card` | `#FFFFFF` | Background kartu/panel |
| `--border` | `#E5E7EB` | Border kartu, tabel |
| `--text-primary` | `#111827` | Judul, teks utama |
| `--text-secondary` | `#6B7280` | Label, teks sekunder |
| `--sidebar-bg` | `#1E293B` (slate-800) | Background sidebar admin |
| `--sidebar-text` | `#CBD5E1` (slate-300) | Teks menu sidebar (non-aktif) |

### 2.4 Palet Grafik (Chart Colors)
Digunakan berurutan untuk donut/bar/line chart agar konsisten:
`#2563EB` (biru), `#16A34A` (hijau), `#F59E0B` (kuning), `#DC2626` (merah), `#8B5CF6` (ungu), `#0EA5E9` (cyan)

---

## 3. Tipografi

| Elemen | Font | Ukuran | Weight |
|---|---|---|---|
| Font utama | Inter / Plus Jakarta Sans | — | — |
| H1 (Judul Halaman) | — | 24–28px | 700 (Bold) |
| H2 (Judul Kartu/Section) | — | 18–20px | 600 (Semibold) |
| Body / Tabel | — | 14px | 400 (Regular) |
| Statistik Angka Besar (kartu KPI) | — | 28–32px | 700 (Bold) |
| Label kecil / Caption | — | 12px | 500 (Medium), uppercase opsional |

---

## 4. Grid & Spacing

- **Grid dashboard:** 12-column responsive grid, gap 16–24px.
- **Kartu statistik (KPI card):** grid 4 kolom (desktop) → 2 kolom (tablet) → 1 kolom (mobile).
- **Kartu grafik:** grid 3 kolom (desktop) → 1 kolom (mobile).
- **Spacing scale:** 4 / 8 / 12 / 16 / 24 / 32 / 48 px (kelipatan 4, selaras Tailwind default).
- **Radius sudut (border-radius):** 12px untuk kartu, 8px untuk tombol/input, 999px untuk badge (pill shape).
- **Shadow:** shadow tipis (`0 1px 3px rgba(0,0,0,0.08)`) pada kartu, tanpa shadow berlebihan.

---

## 5. Komponen UI

### 5.1 KPI / Statistic Card
- Ikon bulat berwarna pastel di kanan (sesuai kategori), judul kecil di atas, angka besar bold di bawah.
- Contoh: "Total HT" (ikon perangkat, biru), "HT Damaged" (ikon peringatan, merah).

### 5.2 Sidebar Admin (Collapsible)
- State **expanded**: logo + nama app, menu dengan label teks, grouping (Master Data, Transactions, Reports, Administration).
- State **collapsed**: hanya ikon, tooltip saat hover.
- Item aktif: background `--primary-light` dengan teks/ikon `--primary`.
- Toggle collapse di pojok atas sidebar.

### 5.3 Tabel Data
- Header tabel background abu muda (`#F9FAFB`), teks bold kecil uppercase.
- Baris zebra opsional atau border bawah tipis antar baris.
- Kolom "Status/Kondisi" selalu berupa **badge berwarna** (pill), bukan teks polos.
- Kolom Action: ikon edit (pensil) dan hapus (tempat sampah), warna `--text-secondary`, hover jadi warna status (biru/merah).
- Fitur bawaan: search bar, filter dropdown, sort per kolom, pagination bawah tabel.

### 5.4 Badge Status
| Status | Warna Background | Warna Teks |
|---|---|---|
| Available / Good | `#DCFCE7` | `#16A34A` |
| Borrowed | `#DBEAFE` | `#2563EB` |
| Under Repair | `#FEF3C7` | `#B45309` |
| Damaged / Late | `#FEE2E2` | `#DC2626` |

### 5.5 Multi-step Form (Borrowing Module)
- Stepper horizontal di atas form: lingkaran bernomor + label (1. Borrower Info → 2. Select Assets → 3. Terms & Upload).
- Step aktif: lingkaran solid `--primary`; step selesai: centang hijau; step belum tercapai: outline abu-abu.
- Tombol navigasi "Back" (outline) & "Next/Submit" (solid primary) di bagian bawah form.
- Validasi inline per field, pesan error warna `--danger` di bawah input.

### 5.6 Grafik (Charts)
- **Donut chart**: untuk proporsi kondisi/status (mis. Asset Condition), dengan legenda warna di bawah.
- **Bar chart**: untuk perbandingan per kategori/waktu (Borrowing Per Month).
- **Line chart**: untuk tren waktu (Return Trend).
- Semua chart menggunakan palet di §2.4, tooltip on-hover, judul kartu di atas chart.

### 5.7 Upload File / Foto
- Dropzone dengan border dashed, ikon upload, teks "Klik atau seret file ke sini".
- Preview thumbnail setelah upload, tombol hapus (x) di pojok preview.

### 5.8 Notification Center
- List item dengan ikon kategori (warna sesuai jenis: merah untuk damaged, kuning untuk due date, hijau untuk completed).
- Item belum dibaca: background sedikit ter-highlight (`--primary-light` transparan) + dot indikator.
- Timestamp relatif (mis. "2 hari lalu") di kanan setiap item.

### 5.9 Tombol (Buttons)
| Varian | Style |
|---|---|
| Primary | Background `--primary`, teks putih, radius 8px |
| Secondary/Outline | Border `--primary`, teks `--primary`, background transparan |
| Danger | Background `--danger`, teks putih (untuk aksi hapus) |
| Ghost/Icon-only | Tanpa background, hover background abu muda |

---

## 6. Struktur Layout per Halaman

### 6.1 Halaman Publik (Public Monitoring)
- Header sederhana: logo + nama app kiri, navigasi (Home, Live Monitoring, Statistics, About) kanan.
- Grid kartu statistik 3–4 kolom di bawah header, tanpa sidebar.

### 6.2 Live Dashboard & Table
- Header sama seperti publik + tombol aksi (mis. "Live" indicator).
- Grid 3 kartu chart di baris atas, tabel live monitoring full-width di bawah.

### 6.3 Admin Dashboard
- Layout 2 kolom: sidebar kiri (fixed, collapsible) + konten utama kanan.
- Topbar konten: search bar, ikon notifikasi (dengan badge jumlah), avatar & nama user.
- Konten: grid KPI card (4 kolom) → grid chart (3 kolom).

### 6.4 Master Data / Tabel Manajemen
- Topbar aksi: search, filter, sorting, Import/Export, tombol "+ Add Data" (primary, kanan atas).
- Tabel full-width dengan pagination di bawah.

### 6.5 Asset Detail
- Layout 2 kolom: kolom kiri foto & data spesifikasi aset (card), kolom kanan tab riwayat + tabel riwayat.

### 6.6 Multi-step Form (Borrowing)
- Card terpusat (max-width ~700–800px), stepper di atas, form di tengah, tombol navigasi di bawah.

### 6.7 Returning Module
- Card checklist per unit yang dikembalikan, form kondisi per item, area upload dokumentasi di bawah.

### 6.8 Reports Dashboard
- Panel filter horizontal di atas (Month, Year, Department, Asset Type) + tombol Export PDF/Excel di kanan.
- Grid statistik ringkas + grafik distribusi di bawah filter.

### 6.9 Notification Center
- Panel/drawer kanan (dapat berupa dropdown dari ikon lonceng di topbar atau halaman penuh), list vertikal item notifikasi.

---

## 7. Ikonografi

- Gunakan **satu ikon set konsisten** (rekomendasi: [Lucide Icons](https://lucide.dev), sudah tersedia sebagai `lucide-react`).
- Ikon perangkat (radio/walkie-talkie) untuk representasi HT — gunakan ikon `radio` sebagai fallback bila tidak ada ikon walkie-talkie native.
- Ukuran ikon konsisten: 16px (inline tabel), 20px (tombol), 24px (kartu KPI/sidebar).

---

## 8. Navigasi & Informasi Arsitektur

```
Public Site
├─ Home (Public Monitoring)
├─ Live Monitoring
├─ Statistics
└─ About

Admin Panel (setelah login)
├─ Dashboard
├─ Master Data
│  ├─ Handy Talky
│  ├─ Charger
│  ├─ Locations
│  └─ Employees
├─ Transactions
│  ├─ Borrowing
│  └─ Returning
├─ Reports
│  ├─ Asset Condition
│  ├─ Late Returns
│  ├─ Borrowers/Users
│  └─ Export (PDF/Excel)
├─ Administration
│  ├─ Users
│  ├─ Roles
│  └─ Activity Log
└─ Settings
```

---

## 9. Responsif

| Breakpoint | Perilaku |
|---|---|
| **Desktop (≥1280px)** | Sidebar expanded default, grid penuh (3–4 kolom) |
| **Tablet (768–1279px)** | Sidebar auto-collapse ke icon-only, grid 2 kolom |
| **Mobile (<768px)** | Sidebar menjadi drawer/off-canvas, grid 1 kolom, tabel scroll horizontal atau berubah jadi card list |

---

## 10. Aksesibilitas

- Kontras teks minimal rasio 4.5:1 (WCAG AA) — hindari teks abu terang di atas putih.
- Semua ikon aksi (edit/hapus) memiliki `aria-label`.
- Form multi-step dapat dinavigasi via keyboard (Tab/Enter), fokus terlihat jelas (focus ring `--primary`).
- Badge status tidak hanya mengandalkan warna — sertakan teks label (mis. "Damaged", bukan hanya warna merah).

---

## 11. Tokens Siap Pakai (Tailwind / CSS Variables)

```css
:root {
  --primary: #2563EB;
  --primary-dark: #1E40AF;
  --primary-light: #DBEAFE;
  --success: #16A34A;
  --warning: #F59E0B;
  --danger: #DC2626;
  --info: #2563EB;
  --neutral: #6B7280;
  --bg-page: #F5F7FA;
  --bg-card: #FFFFFF;
  --border: #E5E7EB;
  --text-primary: #111827;
  --text-secondary: #6B7280;
  --sidebar-bg: #1E293B;
  --sidebar-text: #CBD5E1;
  --radius-card: 12px;
  --radius-control: 8px;
  --radius-pill: 999px;
}
```

Token ini sudah dipetakan ke `tailwind.config.ts` pada paket proyek agar dapat langsung dipakai sebagai class (`bg-primary`, `text-danger`, dsb).
