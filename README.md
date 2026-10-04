<p align="center">
  <img src="public/images/logo/49-dark.png" alt="Logo IuranKita" width="200">
</p>

<p align="center">
  <strong>Sistem Administrasi dan Pembayaran Iuran Warga</strong><br>
  <em>"Bayar Iuran, Tertib Administrasi, Nyaman Bertetangga."</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Filament-5.x-F59E0B?style=for-the-badge&logo=filament&logoColor=white" alt="Filament 5">
  <img src="https://img.shields.io/badge/TailwindCSS-4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.5">
  <img src="https://img.shields.io/badge/Pest-Coverage_100%25-green?style=for-the-badge" alt="Pest Tests">
</p>

---

## 📌 Ringkasan Proyek

**IuranKita** adalah aplikasi berbasis web modern yang dirancang khusus untuk mempermudah pengurus perumahan, kompleks residensial, RT/RW, dan paguyuban warga dalam mengelola administrasi hunian, kalkulasi dan penagihan iuran bulanan, pemantauan kegiatan pembangunan, pencatatan transaksi pembayaran, penagihan tunggakan, hingga penerbitan kwitansi dan pelaporan keuangan yang transparan.

Aplikasi ini diimplementasikan dan dikonfigurasi secara riil untuk kawasan perumahan **Del Mattappa Residence** yang berlokasi di Kabupaten Soppeng, Sulawesi Selatan, mencakup 32 kepala keluarga/unit rumah (Blok A, B, C, dan D) serta peta denah kawasan terintegrasi.

---

## 🏛️ Aturan Bisnis & Struktur Iuran

IuranKita menganut sistem iuran yang jelas, adil, dan transparan sesuai kesepakatan warga:

### 1. Iuran Rutin Bulanan
Dikenakan secara berkala setiap bulan untuk setiap rumah/kavling yang aktif:
* **Rumah Sudah Dihuni (`occupied`):** `Rp 50.000 / bulan`
* **Rumah Belum Dihuni / Kosong (`unoccupied`):** `Rp 35.000 / bulan`

Tagihan rutin diterbitkan otomatis setiap tanggal 1 awal bulan melalui Laravel Scheduler atau melalui fitur manual invoice generator oleh Administrator.

### 2. Iuran Pembangunan (One-Time / Sekali Bayar)
Bukan merupakan tagihan bulanan berkala. Iuran ini hanya dikenakan apabila seorang warga melakukan kegiatan pembangunan fisik:
* Membangun atau merenovasi dapur
* Menambah atau memperluas bangunan
* Renovasi fisik bangunan utama

**Ketentuan Iuran Pembangunan:**
* **Tarif:** `Rp 100.000` per kegiatan pembangunan.
* **Sifat Pembayaran:** **Hanya SATU KALI** bayar untuk seluruh durasi proyek tersebut (walaupun pengerjaan berlangsung 1 bulan, 3 bulan, atau lebih).
* **Tidak Berulang:** Tidak akan menjadi tagihan bulanan pada bulan-bulan berikutnya.

### 3. Prinsip Integritas Finansial
* **Zero Floating-Point:** Seluruh nominal uang disimpan dalam bentuk integer Rupiah murni untuk mencegah masalah pembulatan pecahan desimal.
* **Histori Terkunci (Snapshot):** Perubahan master tarif di masa depan tidak akan mengubah nominal invoice yang telah diterbitkan sebelumnya.
* **Pencegahan Penghapusan Sembarangan:** Mencegah *hard delete* terhadap rumah, tagihan, atau data yang telah memiliki transaksi keuangan berjalan.

---

## ✨ Fitur-Fitur Utama

### 🌐 Portal Publik & Warga (Landing Page)
* **Cek Tagihan Mandiri:** Warga dapat mencari dan mengecek tagihan aktif serta riwayat pembayaran secara mandiri hanya dengan memilih Blok dan Nomor Rumah atau memasukkan Nomor KK.
* **Denah Interaktif Del Mattappa Residence:** Visualisasi peta denah perumahan dengan fitur *lightbox viewer* (dukungan zoom, pan, dan layar penuh) untuk memudahkan pengenalan posisi kavling/blok.
* **Transparansi Informasi:** Menampilkan rekening bank resmi kas perumahan, saluran pembayaran QRIS, serta kontak pengurus rukun warga.

### 🛠️ Filament Admin Panel
* **Dashboard Finansial:**
  * Metrik pemasukan bulan berjalan, total tunggakan aktif, dan persentase kepatuhan bayar.
  * Grafik perbandingan penerimaan iuran rutin vs iuran pembangunan.
  * Ringkasan status hunian (dihuni vs belum dihuni).
* **Manajemen Data Warga (`Household`):**
  * Pencatatan nomor blok, nomor rumah, nama kepala keluarga, nomor KK, nomor telepon WhatsApp, dan status hunian.
  * Tombol akses cepat denah perumahan langsung dari daftar dan formulir warga.
* **Manajemen Proyek Pembangunan (`ConstructionProject`):**
  * Pelacakan jenis renovasi (dapur, penambahan bangunan, dsb.), tanggal mulai, tanggal estimasi selesai, dan status proyek.
  * Otomatisasi penerbitan invoice satu kali bayar (Rp 100.000) saat proyek didaftarkan, mencegah duplikasi invoice.
* **Manajemen Tagihan (`Invoice`):**
  * Nomor tagihan terstandar (`INV/YYYYMM/XXXX`).
  * Status tagihan yang dinamis (`Belum Dibayar`, `Sebagian`, `Lunas`, `Jatuh Tempo`, `Dibatalkan`).
  * Fasilitas *Mass Monthly Invoices Generator* untuk menerbitkan tagihan massal seluruh warga dengan satu klik.
* **Pencatatan Pembayaran & Kwitansi (`Payment`):**
  * Multi-metode pembayaran: Tunai (Cash), Transfer Bank, dan QRIS.
  * Penomoran kwitansi otomatis (`KWT/YYYYMM/XXXX`).
  * Kwitansi cetak digital siap print atau disimpan sebagai PDF dengan kop resmi pengurus perumahan.
* **Laporan Komprehensif:**
  * Laporan Penerimaan Pembayaran (filter periode & metode bayar).
  * Laporan Tagihan Bulanan.
  * Laporan Tunggakan Warga.
  * Laporan Iuran Pembangunan.
  * Ekspor data ke format cetak/tabel.
* **Pengaturan Fleksibel & Audit Trail:**
  * Pengaturan identitas perumahan, rekening bank, dan nomor darurat.
  * Master tarif dinamis.
  * Pencatatan log aktivitas pengelola (Audit Log).
* **Tampilan Form Fullwidth:**
  * Seluruh layout formulir di admin panel didesain dengan format *fullwidth* yang rapi, ergonomis, dan nyaman digunakan baik di layar monitor lebar maupun tablet.

---

## 🏗️ Tech Stack & Arsitektur

* **Framework:** Laravel 13 (PHP 8.5)
* **Admin Panel:** Filament v5
* **Frontend UI:** Tailwind CSS v4, Alpine.js, Blade Components
* **Database:** MySQL / SQLite
* **Testing:** Pest PHP (72 test case, 267 assertions, 100% lulus)
* **Code Standard:** Laravel Pint (PSR-12 / Laravel Code Style)
* **Arsitektur:**
  * Service Layer Pattern (`App\Services\Billing`, `App\Services\Payment`, `App\Services\Construction`, `App\Services\Receipt`)
  * Data Transfer Objects (`BillingResult`)
  * Model Policies & Role-Based Access Control (`admin`, `staff`)
  * Database Transactions untuk operasi multi-tabel

---

## 🚀 Panduan Instalasi & Menjalankan

### Persyaratan Sistem
* PHP >= 8.2 (Disarankan PHP 8.5)
* Composer
* Node.js & NPM
* Basis data MySQL atau SQLite

### Langkah-langkah Instalasi

1. **Clone repositori:**
   ```bash
   git clone https://github.com/hanzo-asashi/iurankita.git
   cd iurankita
   ```

2. **Instal dependensi PHP & JavaScript:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan konfigurasi database (DB_DATABASE, DB_USERNAME, DB_PASSWORD) di dalam berkas `.env`.*

4. **Jalankan Migrasi & Database Seeder:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Seeder ini otomatis mengisi data:*
   * Pengaturan instansi **Del Mattappa Residence**
   * Master tarif iuran resmi (Rp 50.000, Rp 35.000, dan Rp 100.000)
   * **32 Data Warga Riil** Del Mattappa Residence (Blok A, B, C, D)
   * Riwayat tagihan dan pelunasan September 2026 (termasuk Iuran Pembangunan Irwan C-10)
   * Tagihan aktif bulan berjalan Oktober 2026
   * Akun Administrator dan Petugas

5. **Build Aset Frontend:**
   ```bash
   npm run build
   # atau untuk mode pengembangan:
   npm run dev
   ```

6. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   *Buka browser dan akses:*
   * Landing Page Publik: [http://localhost:8000](http://localhost:8000)
   * Panel Administrasi: [http://localhost:8000/admin](http://localhost:8000/admin)

---

## 🔐 Kredensial Login Bawaan

| Peran (Role) | Email | Password | Hak Akses |
|---|---|---|---|
| **Administrator** | `admin@iurankita.test` | `password` | Akses penuh seluruh modul, tarif, pengaturan, & user |
| **Petugas (Staff)** | `petugas@iurankita.test` | `password` | Mengelola data warga, tagihan, pembayaran, & melihat laporan |

---

## ⏰ Jadwal Otomatisasi (Laravel Scheduler)

Aplikasi memiliki perintah terjadwal di `routes/console.php`:

* **`billing:generate-monthly`**: Dijalankan otomatis setiap tanggal 1 pukul 01:00 WITA untuk menerbitkan invoice rutin bulanan seluruh rumah aktif.
* **`invoices:check-overdue`**: Dijalankan setiap hari pukul 02:00 WITA untuk memperbarui status tagihan yang telah melewati batas jatuh tempo menjadi `overdue`.
* **`construction:check-active`**: Dijalankan setiap Senin pukul 03:00 WITA untuk memantau status proyek konstruksi yang melewati tanggal estimasi selesai.

Untuk menjalankan worker scheduler secara lokal:
```bash
php artisan schedule:work
```

---

## 🧪 Pengujian & Kualitas Kode

IuranKita dilengkapi pengujian otomatis menyeluruh menggunakan Pest:

```bash
# Menjalankan seluruh pengujian
php artisan test --compact

# Memeriksa dan merapikan standar gaya kode
vendor/bin/pint --format agent
```

---

## 👥 Pengembang & Hak Cipta

* **Pengembang:** [Hanzo Asashi](https://github.com/hanzo-asashi)
* **Kawasan Percontohan:** Paguyuban Warga **Del Mattappa Residence**, Kab. Soppeng
* **Lisensi:** Open-source di bawah lisensi [MIT License](LICENSE).
