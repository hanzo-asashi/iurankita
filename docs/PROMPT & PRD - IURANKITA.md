# IuranKita

## Sistem Administrasi dan Pembayaran Iuran Warga

**Tagline:**
**Bayar Iuran, Tertib Administrasi, Nyaman Bertetangga.**

---

# 1. IDENTITAS PROYEK

**Nama aplikasi:** IuranKita

**Deskripsi:**

IuranKita adalah aplikasi web untuk membantu pengelola kompleks/perumahan mengelola data KK/rumah, menghitung iuran rutin bulanan, mencatat iuran pembangunan satu kali, mencatat pembayaran, memantau tunggakan, serta menghasilkan laporan administrasi iuran warga.

---

# 2. TECH STACK

Gunakan stack berikut:

* PHP sesuai requirement Laravel 13
* Laravel 13
* Filament 5
* Livewire 4
* Alpine.js
* Tailwind CSS 4
* Vite
* MySQL
* Blade
* Pest
* Laravel Scheduler

Jangan mengganti framework utama.

Gunakan Filament untuk admin panel dan Blade + Tailwind + Alpine.js untuk landing page publik.

---

# 3. PRINSIP DEVELOPMENT

Bangun aplikasi yang:

1. sederhana;
2. mudah dipahami oleh pengelola;
3. mobile-friendly;
4. responsive;
5. modern;
6. ringan;
7. menggunakan Bahasa Indonesia pada UI;
8. menggunakan Eloquent;
9. menggunakan Service Layer untuk business logic;
10. menggunakan Policy/authorization;
11. menggunakan database transaction pada proses transaksi penting;
12. tidak menggunakan floating point untuk uang;
13. menggunakan integer untuk nominal Rupiah;
14. menghindari N+1 query;
15. tidak melakukan hard delete terhadap transaksi keuangan yang sudah memiliki histori;
16. tidak mengubah histori invoice ketika tarif master berubah.

---

# 4. ATURAN IURAN — VERSI FINAL

Sistem mempunyai **DUA JENIS IURAN**.

## A. Iuran Rutin Bulanan

Iuran rutin dibayarkan setiap bulan oleh setiap KK/rumah.

### Rumah sudah ditinggali

Tarif:

**Rp50.000 / bulan**

### Rumah belum ditinggali

Tarif:

**Rp35.000 / bulan**

Iuran rutin ini selalu masuk ke tagihan bulanan selama rumah aktif.

---

# 5. IURAN PEMBANGUNAN

Iuran pembangunan **BUKAN iuran bulanan**.

Iuran ini hanya dikenakan ketika seorang warga:

* membangun dapur;
* menambah bangunan;
* memperluas bangunan;
* melakukan pembangunan lain yang menurut aturan kompleks termasuk pembangunan yang dikenakan iuran.

Tarif:

**Rp100.000**

### Sifat pembayaran

Iuran pembangunan:

* dibayar **SATU KALI**;
* berlaku untuk **SATU kegiatan pembangunan**;
* dikenakan ketika kegiatan pembangunan dilakukan;
* tidak berulang setiap bulan;
* tidak masuk otomatis ke tagihan rutin bulanan;
* tidak dihitung berdasarkan jumlah bulan pembangunan;
* tidak menjadi Rp100.000 setiap bulan.

Contoh:

Warga A mulai membangun dapur.

Iuran pembangunan:

**Rp100.000 sekali bayar**

Walaupun pembangunan berlangsung:

* 1 bulan;
* 2 bulan;
* 3 bulan;
* 6 bulan;

tetap hanya:

**Rp100.000 satu kali untuk kegiatan tersebut.**

---

# 6. CONTOH PERHITUNGAN FINAL

## Skenario 1 — Rumah dihuni, tidak ada pembangunan

Iuran rutin:

Rp50.000

Total bulan tersebut:

**Rp50.000**

---

## Skenario 2 — Rumah belum dihuni

Iuran rutin:

Rp35.000

Total bulan tersebut:

**Rp35.000**

---

## Skenario 3 — Rumah dihuni dan mulai membangun dapur

Iuran rutin bulanan:

Rp50.000

Iuran pembangunan satu kali:

Rp100.000

Total kewajiban saat pembangunan didaftarkan:

**Rp150.000**

Namun Rp100.000 tersebut **BUKAN menjadi iuran bulan berikutnya**.

Bulan berikutnya:

**Rp50.000 saja**, selama tidak ada kegiatan pembangunan baru.

---

## Skenario 4 — Rumah belum dihuni dan mulai membangun

Iuran rutin:

Rp35.000

Iuran pembangunan satu kali:

Rp100.000

Total kewajiban:

**Rp135.000**

Bulan berikutnya:

**Rp35.000 saja**, selama pembangunan yang sama masih berlangsung.

---

# 7. CONTOH WAKTU PEMBANGUNAN

Warga A:

1 September 2026:

* mulai membangun dapur;
* dikenakan iuran pembangunan Rp100.000.

September:

* iuran rutin Rp50.000;
* pembangunan Rp100.000 satu kali.

Total kewajiban terkait September:

Rp150.000.

Oktober:

* iuran rutin Rp50.000;
* tidak ada iuran pembangunan tambahan.

November:

* iuran rutin Rp50.000;
* tidak ada iuran pembangunan tambahan.

Desember:

* pembangunan selesai;
* tidak ada iuran pembangunan tambahan.

Jadi:

**Rp100.000 tetap hanya satu kali.**

---

# 8. TUJUAN SISTEM

Sistem harus membantu pengelola:

* menyimpan data rumah;
* menyimpan data KK;
* menentukan status hunian;
* mengelola kegiatan pembangunan;
* membuat tagihan rutin bulanan;
* mencatat iuran pembangunan satu kali;
* mencatat pembayaran;
* memantau tunggakan;
* melihat histori;
* membuat laporan;
* mencetak kwitansi;
* mengetahui total pemasukan.

---

# 9. USER

## Administrator

Akses penuh:

* data rumah;
* KK;
* tarif;
* pembangunan;
* tagihan;
* pembayaran;
* laporan;
* pengaturan;
* user.

## Petugas

Dapat:

* melihat rumah;
* melihat tagihan;
* mencatat pembayaran;
* melihat pembangunan;
* melihat laporan.

Tidak dapat mengubah:

* tarif;
* pengaturan;
* user administrator.

## Warga

Untuk MVP:

**tidak perlu login warga.**

Portal warga dapat dibuat pada Phase 2.

---

# 10. MODUL

Buat modul:

1. Dashboard
2. KK / Rumah
3. Pembangunan
4. Tarif Iuran
5. Tagihan
6. Pembayaran
7. Laporan
8. Pengaturan
9. Pengguna
10. Landing Page Publik

---

# 11. MODEL HOUSEHOLD

Model:

`Household`

Field:

```text
id
house_code
block
house_number
head_of_family
kk_number
phone
address
occupancy_status
notes
is_active
created_at
updated_at
```

---

# 12. STATUS HUNIAN

Enum:

```text
occupied
unoccupied
```

Label:

```text
Dihuni
Belum Dihuni
```

Aturan:

* occupied → Rp50.000/bulan
* unoccupied → Rp35.000/bulan

Status hunian tidak boleh digunakan untuk menentukan apakah ada iuran pembangunan.

---

# 13. MODEL CONSTRUCTION PROJECT

Model:

`ConstructionProject`

Field:

```text
id
household_id
project_type
description
start_date
completion_date
status
one_time_fee
fee_invoice_id
created_by
notes
created_at
updated_at
```

---

# 14. JENIS PEMBANGUNAN

Enum:

```text
kitchen
building_addition
expansion
renovation
other
```

Label:

```text
Dapur
Penambahan Bangunan
Perluasan Bangunan
Renovasi
Lainnya
```

Jenis dapat dikembangkan sesuai kebutuhan kompleks.

---

# 15. STATUS PEMBANGUNAN

Gunakan:

```text
planned
active
completed
cancelled
```

Label:

```text
Direncanakan
Aktif
Selesai
Dibatalkan
```

---

# 16. ATURAN CONSTRUCTION PROJECT

Setiap kegiatan pembangunan adalah satu project.

Contoh:

Rumah A:

Project 1:
Membangun dapur

Iuran:
Rp100.000

Setelah selesai, kemudian pada tahun berikutnya pemilik menambah bangunan belakang.

Project 2:
Penambahan bangunan

Iuran:
Rp100.000

Ini adalah **kegiatan berbeda**, sehingga dikenakan lagi satu kali.

Artinya:

**Iuran pembangunan berlaku satu kali untuk setiap kegiatan pembangunan.**

Bukan satu kali seumur hidup untuk rumah.

---

# 17. ATURAN PEMBAYARAN PEMBANGUNAN

Ketika project pembangunan dibuat/diaktifkan:

sistem membuat kewajiban pembayaran:

**Rp100.000**

Kewajiban tersebut dapat:

* belum dibayar;
* sebagian dibayar;
* lunas.

Tetapi setelah pembayaran lunas:

**tidak ada tagihan pembangunan bulanan lagi.**

---

# 18. TARIF PEMBANGUNAN

Jangan hard-code Rp100.000 di seluruh aplikasi.

Gunakan FeeRate:

```text
construction_one_time
```

Nominal default:

```text
100000
```

Nama:

**Iuran Pembangunan Sekali Bayar**

Dengan demikian tarif dapat diubah di masa depan.

---

# 19. FEE RATE

Model:

`FeeRate`

Field:

```text
id
code
name
amount
effective_from
effective_until
is_active
description
created_at
updated_at
```

Jenis tarif:

```text
monthly_occupied
monthly_unoccupied
construction_one_time
```

Default:

```text
monthly_occupied = 50000
monthly_unoccupied = 35000
construction_one_time = 100000
```

---

# 20. PRINSIP HISTORI TARIF

Ketika tarif berubah:

tagihan lama tidak berubah.

Contoh:

2026:

Iuran penghuni:
Rp50.000

2027:

Iuran penghuni:
Rp55.000

Tagihan 2026:

tetap Rp50.000.

Invoice harus menyimpan snapshot harga ketika dibuat.

---

# 21. INVOICE

Gunakan model:

`Invoice`

Field:

```text
id
invoice_number
household_id
invoice_type
billing_period
issue_date
due_date
subtotal
total_amount
amount_paid
balance
status
construction_project_id
notes
created_at
updated_at
```

---

# 22. INVOICE TYPE

Gunakan enum:

```text
monthly
construction
```

Label:

```text
Iuran Bulanan
Iuran Pembangunan
```

---

# 23. INVOICE BULANAN

Invoice type:

`monthly`

Satu invoice untuk satu household untuk satu periode.

Unique:

```text
household_id + billing_period + invoice_type
```

Isi invoice:

### Rumah dihuni

Iuran bulanan:

Rp50.000

### Rumah belum dihuni

Iuran bulanan:

Rp35.000

Invoice bulan berikutnya dibuat kembali karena iuran ini memang rutin.

---

# 24. INVOICE PEMBANGUNAN

Invoice type:

`construction`

Invoice pembangunan:

* satu invoice untuk satu project;
* dibuat satu kali;
* nominal default Rp100.000;
* tidak dibuat ulang setiap bulan.

Tambahkan unique constraint:

```text
construction_project_id + invoice_type
```

Pastikan satu project tidak dapat menghasilkan lebih dari satu invoice pembangunan.

---

# 25. CONTOH INVOICE PEMBANGUNAN

Nomor:

`INV-2026-000125`

Jenis:

**Iuran Pembangunan**

Rumah:

A-01

Kepala keluarga:

Budi Santoso

Pembangunan:

Membangun dapur

Tanggal mulai:

1 September 2026

Total:

**Rp100.000**

Status:

Belum Lunas

Setelah dibayar:

Status:

**Lunas**

Invoice tersebut tetap tersimpan sebagai histori dan tidak dibuat lagi pada Oktober, November, dan seterusnya.

---

# 26. INVOICE ITEMS

Model:

`InvoiceItem`

Field:

```text
id
invoice_id
fee_type
description
quantity
unit_price
total
metadata
created_at
updated_at
```

Untuk invoice bulanan:

```text
Iuran Air & Lingkungan
Rp50.000
```

atau:

```text
Iuran Air & Lingkungan
Rp35.000
```

Untuk invoice pembangunan:

```text
Iuran Pembangunan
Rp100.000
```

---

# 27. PAYMENT

Model:

`Payment`

Field:

```text
id
invoice_id
receipt_number
payment_date
amount
payment_method
reference_number
proof_path
notes
received_by
created_at
updated_at
```

Payment berlaku baik untuk:

* invoice bulanan;
* invoice pembangunan.

---

# 28. PAYMENT METHOD

Enum:

```text
cash
transfer
qris
other
```

Label:

```text
Tunai
Transfer
QRIS
Lainnya
```

---

# 29. STATUS INVOICE

Gunakan:

```text
unpaid
partial
paid
overdue
cancelled
```

Aturan:

Jika amount_paid = 0:

`unpaid` atau `overdue`.

Jika:

```text
amount_paid > 0
amount_paid < total_amount
```

maka:

`partial`

Jika:

```text
amount_paid >= total_amount
```

maka:

`paid`

---

# 30. PAYMENT RULE

Jumlah pembayaran tidak boleh lebih besar dari saldo invoice.

Gunakan transaction:

```text
Create Payment
        ↓
Calculate amount_paid
        ↓
Calculate balance
        ↓
Update invoice status
        ↓
Generate receipt
```

Semua harus dilakukan dalam database transaction.

---

# 31. BILLING CALCULATOR

Buat:

`BillingCalculator`

Fungsi utama:

```php
calculateMonthlyInvoice(
    Household $household,
    CarbonInterface $period
): BillingResult
```

Fungsi ini **HANYA menghitung iuran rutin bulanan**.

### Algoritma

Jika:

```text
occupied
```

→ Rp50.000

Jika:

```text
unoccupied
```

→ Rp35.000

Tidak boleh ada logic:

```text
construction_active ? +100000
```

dalam monthly billing.

Ini sangat penting.

---

# 32. CONSTRUCTION BILLING SERVICE

Buat service terpisah:

```text
ConstructionFeeService
```

Contoh:

```php
createConstructionInvoice(
    ConstructionProject $project
): Invoice
```

Service ini:

1. mengambil tarif construction_one_time;
2. membuat invoice pembangunan;
3. menyimpan project ID;
4. menyimpan nominal aktual;
5. memastikan invoice belum pernah dibuat;
6. membuat invoice hanya satu kali.

---

# 33. PEMISAHAN LOGIC

Jangan mencampur:

```text
Monthly Billing
```

dengan:

```text
Construction Billing
```

Arsitektur:

```text
MonthlyInvoiceGenerator
ConstructionInvoiceGenerator
```

atau:

```text
InvoiceGenerator
    ├── generateMonthly()
    └── generateConstruction()
```

Tujuannya agar mudah dipahami dan menghindari kesalahan bahwa pembangunan dianggap biaya bulanan.

---

# 34. GENERATE TAGIHAN BULANAN

Buat Filament Page:

`GenerateMonthlyInvoices`

Input:

* bulan;
* tahun.

Preview:

* total rumah;
* rumah dihuni;
* rumah belum dihuni;
* total iuran rutin.

Contoh:

```text
Total Rumah       50
Dihuni            42
Belum Dihuni       8

Iuran Dihuni      Rp2.100.000
Iuran Belum Dihuni Rp280.000

Total             Rp2.380.000
```

Button:

**Generate Tagihan Bulanan**

Idempotent.

Jika invoice bulan tersebut sudah ada:

jangan membuat duplikat.

---

# 35. PEMBUATAN INVOICE PEMBANGUNAN

Pembangunan tidak ikut generate monthly invoices.

Flow:

Admin:

Data Pembangunan

→ Tambah Pembangunan

→ Pilih rumah

→ Jenis pembangunan

→ tanggal mulai

→ status Active

→ Simpan

Setelah project berhasil dibuat:

Sistem membuat:

**1 invoice pembangunan**

sebesar tarif aktif.

---

# 36. KAPAN INVOICE PEMBANGUNAN DIBUAT?

Saat project berubah menjadi:

`active`

buat invoice satu kali.

Untuk project:

`planned`

belum perlu membuat invoice.

Saat menjadi:

`active`

baru dibuat.

Jika:

`cancelled`

sebelum aktif:

tidak ada invoice.

---

# 37. PROJECT COMPLETION

Ketika pembangunan selesai:

ubah status:

`active → completed`

Masukkan:

`completion_date`

Tidak ada penghapusan invoice.

Invoice pembangunan tetap menjadi histori.

Sistem **tidak membuat invoice baru maupun biaya baru** ketika status berubah menjadi completed.

---

# 38. PROJECT CANCELLATION

Jika project dibatalkan sebelum invoice dibuat:

tidak ada invoice.

Jika project sudah aktif dan invoice pembangunan sudah dibuat:

project dapat diubah menjadi cancelled sesuai policy pengelola.

Jangan otomatis menghapus invoice/payment.

Gunakan status:

`cancelled`

dan pertahankan histori.

---

# 39. SATU RUMAH DAPAT MEMILIKI BANYAK PROJECT

Contoh:

Rumah A:

2026:
Membangun dapur
Rp100.000

2028:
Menambah lantai belakang
Rp100.000

2030:
Membangun pagar/struktur tambahan yang termasuk aturan pembangunan
Rp100.000

Masing-masing adalah project terpisah.

Masing-masing mempunyai invoice pembangunan sendiri.

---

# 40. ATURAN SATU PROJECT

Setiap project:

* memiliki satu fee;
* memiliki maksimal satu invoice construction;
* dapat memiliki banyak payment jika sistem mendukung cicilan;
* setelah lunas tidak akan menghasilkan invoice tambahan.

---

# 41. DASHBOARD

Dashboard harus memisahkan statistik:

## Iuran Bulanan

* Tagihan bulan berjalan
* Sudah lunas
* Belum lunas
* Total pembayaran
* Total tunggakan

## Iuran Pembangunan

* Pembangunan aktif
* Invoice pembangunan belum lunas
* Total iuran pembangunan yang diterbitkan
* Total iuran pembangunan yang sudah dibayar

Jangan memasukkan iuran pembangunan sebagai recurring monthly revenue.

---

# 42. DASHBOARD FINANSIAL

Tampilkan dua kategori:

### Iuran Rutin

Contoh:

```text
Tagihan bulan September
Rp2.380.000
```

### Iuran Pembangunan

Contoh:

```text
Iuran pembangunan diterbitkan bulan ini
Rp500.000
```

Jangan menggabungkan keduanya tanpa label.

Jika ada total keseluruhan, jelaskan:

```text
Total seluruh tagihan diterbitkan
Rp2.880.000
```

---

# 43. CHART

### Chart 1 — Pembayaran Iuran Bulanan

6 bulan terakhir.

### Chart 2 — Iuran Pembangunan

Tampilkan berdasarkan bulan invoice pembangunan diterbitkan.

Jangan menghitung pembangunan sebagai recurring fee.

---

# 44. DATA HOUSEHOLD RESOURCE

Buat:

`HouseholdResource`

Table:

* kode rumah;
* blok;
* nomor;
* kepala keluarga;
* status hunian;
* jumlah pembangunan;
* pembangunan aktif;
* nomor telepon;
* aktif.

Filter:

* dihuni;
* belum dihuni;
* memiliki pembangunan aktif;
* tidak aktif.

---

# 45. CONSTRUCTION RESOURCE

Table:

* rumah;
* kepala keluarga;
* jenis pembangunan;
* tanggal mulai;
* tanggal selesai;
* status;
* iuran pembangunan;
* status pembayaran.

Contoh:

```text
A-01
Budi Santoso
Dapur
01 Sep 2026
Aktif
Rp100.000
Belum Lunas
```

---

# 46. ACTION PEMBANGUNAN

Action:

**Mulai Pembangunan**

Ketika dipilih:

1. ubah status menjadi active;
2. simpan tanggal mulai;
3. buat invoice construction;
4. tampilkan nomor invoice;
5. tampilkan notification.

Jika invoice construction sudah ada:

jangan membuat invoice kedua.

---

# 47. ACTION SELESAIKAN PEMBANGUNAN

Action:

**Tandai Selesai**

Form:

* tanggal selesai;
* catatan.

Setelah selesai:

* status = completed;
* completion_date terisi.

Tidak membuat invoice baru.

---

# 48. FEE RATE RESOURCE

Tampilkan:

| Jenis              |                Nominal |
| ------------------ | ---------------------: |
| Rumah Dihuni       |         Rp50.000/bulan |
| Rumah Belum Dihuni |         Rp35.000/bulan |
| Pembangunan        | Rp100.000 sekali bayar |

Gunakan label yang sangat jelas:

**Pembangunan — Sekali Bayar**

Jangan gunakan label yang dapat membuat user mengira tarif tersebut bulanan.

---

# 49. INVOICE RESOURCE

Table:

* nomor invoice;
* jenis;
* tanggal;
* periode;
* rumah;
* kepala keluarga;
* total;
* dibayar;
* sisa;
* status.

Filter:

* jenis invoice;
* periode;
* status;
* rumah;
* blok.

Jenis:

* Bulanan;
* Pembangunan.

---

# 50. INVOICE DETAIL

Header harus jelas.

Contoh:

```text
TAGIHAN IURAN BULANAN
September 2026
```

atau:

```text
TAGIHAN IURAN PEMBANGUNAN
Pembangunan Dapur
```

Jangan menyatukan keduanya dengan label yang ambigu.

---

# 51. PEMBAYARAN IURAN PEMBANGUNAN

Saat admin membuka invoice pembangunan:

tampilkan:

```text
Iuran Pembangunan
Rp100.000
```

Info:

```text
Kegiatan:
Membangun Dapur

Mulai:
1 September 2026

Sifat:
Sekali Bayar
```

Status:

`Belum Lunas`

Action:

**Catat Pembayaran**

---

# 52. PRINT RECEIPT

Bukti pembayaran harus mencantumkan jenis transaksi.

Contoh:

```text
Jenis Pembayaran:
Iuran Pembangunan

Kegiatan:
Pembangunan Dapur

Rumah:
A-01

Kepala Keluarga:
Budi Santoso

Nominal:
Rp100.000

Keterangan:
Pembayaran iuran pembangunan satu kali
```

Untuk iuran bulanan:

```text
Jenis Pembayaran:
Iuran Bulanan

Periode:
September 2026
```

---

# 53. LAPORAN TAGIHAN

Buat report:

**Rekap Iuran Bulanan**

Menampilkan:

* rumah;
* kepala keluarga;
* status hunian;
* periode;
* tagihan;
* pembayaran;
* sisa;
* status.

---

# 54. LAPORAN PEMBANGUNAN

Buat:

**Rekap Iuran Pembangunan**

Kolom:

* nomor invoice;
* rumah;
* kepala keluarga;
* jenis pembangunan;
* tanggal mulai;
* tanggal selesai;
* nominal;
* dibayar;
* sisa;
* status invoice;
* status pembangunan.

---

# 55. LAPORAN PENERIMAAN

Pisahkan:

### Penerimaan Iuran Bulanan

dan:

### Penerimaan Iuran Pembangunan

Tambahkan filter:

* tanggal;
* jenis invoice;
* metode pembayaran;
* petugas.

---

# 56. LAPORAN TUNGGAKAN

Tampilkan:

## Tunggakan Iuran Bulanan

Contoh:

```text
A-01
September 2026
Rp50.000
```

## Tunggakan Iuran Pembangunan

Contoh:

```text
B-02
Penambahan Dapur
Rp100.000
```

Jangan mencampur jenis tunggakan tanpa label.

---

# 57. HOUSEHOLD DETAIL

Halaman detail rumah sebaiknya menampilkan:

### Informasi Rumah

* kode;
* blok;
* nomor;
* alamat.

### Informasi KK

* kepala keluarga;
* nomor KK;
* telepon.

### Status Hunian

* Dihuni / Belum Dihuni.

### Pembangunan

Tampilkan seluruh histori:

```text
2026
Membangun dapur
Rp100.000
Lunas
Selesai

2028
Perluasan belakang
Rp100.000
Belum Lunas
Aktif
```

### Histori Tagihan

Tampilkan invoice bulanan dan pembangunan dengan badge berbeda.

---

# 58. APP SETTINGS

Buat:

`AppSetting`

Field:

```text
app_name
complex_name
address
phone
email
logo
favicon
tagline
footer_text
primary_color
```

Tambahkan konfigurasi:

```text
monthly_due_day
```

Jangan tambahkan:

`construction_monthly_due`

karena iuran pembangunan bukan recurring monthly.

---

# 59. SCHEDULED BILLING

Scheduler hanya bertanggung jawab pada:

**invoice rutin bulanan.**

Contoh:

```text
php artisan billing:generate
```

Command:

```text
billing:generate --period=2026-10
```

Tidak membuat invoice pembangunan.

Invoice pembangunan dibuat berdasarkan event/status project.

---

# 60. ARTISAN COMMAND

Buat:

```bash
php artisan billing:generate
```

Fungsinya:

* membuat invoice monthly;
* mengambil semua household aktif;
* menentukan occupied/unoccupied;
* menerapkan tarif sesuai effective date;
* mencegah duplicate.

---

# 61. COMMAND PEMBANGUNAN

Tidak wajib membuat scheduler pembangunan.

Pembangunan adalah event yang dipicu oleh admin.

Jika membutuhkan command maintenance, dapat dibuat:

```bash
php artisan construction:check
```

untuk memeriksa project aktif/inconsistent data.

---

# 62. SERVICE LAYER

Gunakan:

```text
App\Services\Billing\MonthlyBillingCalculator
App\Services\Billing\MonthlyInvoiceGenerator
App\Services\Billing\ConstructionInvoiceGenerator
App\Services\Payment\PaymentRecorder
App\Services\Receipt\ReceiptNumberGenerator
```

Opsional:

```text
App\Services\Construction\ConstructionProjectService
```

---

# 63. BILLING FLOW

## Monthly

```text
Household
    ↓
Check occupancy
    ↓
Get monthly fee
    ↓
Generate monthly invoice
```

## Construction

```text
Construction Project
    ↓
Status = Active
    ↓
Get one-time fee
    ↓
Create construction invoice
    ↓
Payment
    ↓
Paid
```

Tidak boleh:

```text
Active construction
    ↓
Every month
    ↓
+100000
```

Itu adalah aturan yang salah.

---

# 64. DATABASE RELATIONSHIP

```text
Household
 ├── invoices
 ├── construction_projects
 └── payments through invoices

ConstructionProject
 ├── household
 └── construction invoice

Invoice
 ├── household
 ├── construction_project nullable
 ├── invoice_items
 └── payments

FeeRate
 └── used by billing services

User
 ├── payments
 └── created projects
```

---

# 65. DATABASE CONSTRAINTS

Invoice monthly:

```text
unique(
    household_id,
    invoice_type,
    billing_period
)
```

Construction:

```text
unique(
    construction_project_id,
    invoice_type
)
```

Jika `construction_project_id` nullable, implementasikan unique strategy yang kompatibel dengan MySQL.

---

# 66. SOFT DELETE

Untuk master:

* Household boleh memakai soft delete bila diperlukan.
* Construction project boleh memakai soft delete bila kebijakan bisnis memungkinkan.

Untuk transaksi:

* Invoice jangan dihapus permanen setelah diterbitkan.
* Payment jangan dihapus permanen tanpa mekanisme audit.

Gunakan:

`cancelled`

bila transaksi perlu dibatalkan.

---

# 67. AUDIT LOG

Audit minimal untuk:

* membuat pembangunan;
* mengaktifkan pembangunan;
* menyelesaikan pembangunan;
* membatalkan pembangunan;
* membuat invoice;
* pembayaran;
* perubahan tarif;
* pembatalan invoice.

Simpan:

* user;
* action;
* model;
* record id;
* timestamp.

---

# 68. ROLE & AUTHORIZATION

### Admin

Full access.

### Petugas

Boleh:

* melihat rumah;
* membuat/update pembangunan sesuai permission;
* membuat pembayaran;
* melihat invoice;
* melihat laporan.

Tidak boleh:

* mengubah tarif;
* menghapus user admin;
* mengubah pengaturan sistem.

Authorization harus server-side menggunakan Policy/Gate.

---

# 69. VALIDATION

## Household

* house code wajib;
* unique;
* kepala keluarga wajib;
* occupancy status wajib.

## Construction

* household wajib;
* project type wajib;
* start date wajib;
* completion date >= start date;
* one-time fee integer >= 0.

## Payment

* invoice wajib;
* amount > 0;
* amount <= balance;
* payment date wajib;
* payment method wajib.

---

# 70. CONSTRUCTION FEE VALIDATION

Saat membuat project:

ambil tarif aktif:

```text
construction_one_time
```

Snapshot nilai ke:

`one_time_fee`

Contoh:

Tarif saat project dibuat:

Rp100.000

Walaupun tarif kemudian diubah menjadi Rp125.000:

project lama tetap:

**Rp100.000**

Project baru:

**Rp125.000**

Jika administrator memang menentukan tarif baru efektif sejak tanggal tertentu.

---

# 71. LANDING PAGE

Route:

```text
/
```

Gunakan:

* Blade;
* Tailwind CSS 4;
* Alpine.js;
* Vite.

Tidak perlu Filament untuk public landing.

---

# 72. BRANDING

Nama:

**IuranKita**

Tagline:

**Bayar Iuran, Tertib Administrasi, Nyaman Bertetangga.**

Tone:

* ramah;
* profesional;
* modern;
* sederhana;
* dekat dengan warga.

---

# 73. COLOR THEME

Primary:

```text
#0F766E
```

Secondary:

```text
#0284C7
```

Background:

```text
#F8FAFC
```

Text:

```text
#0F172A
```

Muted:

```text
#64748B
```

Gunakan warna secukupnya.

---

# 74. TYPOGRAPHY

Gunakan:

**Plus Jakarta Sans**

atau:

**Inter**

Heading:

700–800

Body:

400–500

Financial numbers:

700

---

# 75. NAVBAR

Logo:

**IuranKita**

Menu:

* Beranda
* Tentang
* Cara Kerja
* Jenis Iuran
* FAQ

Button:

**Login Pengelola**

Mobile:

hamburger menu menggunakan Alpine.js.

---

# 76. HERO

Headline:

**Kelola Iuran Warga Lebih Mudah.**

Text:

**IuranKita membantu pengelola perumahan mengelola iuran bulanan, iuran pembangunan, pembayaran, tunggakan, dan laporan dalam satu sistem yang sederhana.**

CTA:

**Masuk ke Sistem**

Secondary:

**Pelajari Cara Kerja**

---

# 77. HERO DASHBOARD MOCKUP

Tampilkan ilustrasi dashboard:

```text
Iuran Bulanan
Rp12.450.000

Sudah Dibayar
Rp9.850.000

Tunggakan
Rp2.600.000
```

Dan:

```text
Pembangunan Aktif
3

Iuran Pembangunan
Rp300.000
```

Pastikan pada visual jelas bahwa:

**Iuran pembangunan adalah transaksi satu kali**, bukan recurring monthly.

---

# 78. FEATURE CARDS

### Iuran Bulanan

Kelola tagihan rutin berdasarkan status hunian.

### Iuran Pembangunan

Catat biaya pembangunan satu kali setiap kali ada kegiatan pembangunan.

### Pembayaran

Catat pembayaran dan simpan histori dengan rapi.

### Laporan

Pantau pemasukan, tunggakan, dan histori transaksi.

---

# 79. SECTION JENIS IURAN

Tampilkan:

## Rumah Dihuni

**Rp50.000 / bulan**

## Rumah Belum Dihuni

**Rp35.000 / bulan**

## Pembangunan

**Rp100.000 / kegiatan**

Gunakan wording:

**“Sekali Bayar”**

secara mencolok.

Jangan menulis:

`Rp100.000/bulan`

untuk pembangunan.

---

# 80. SECTION CONTOH PERHITUNGAN

### Rumah Dihuni

Iuran bulanan:

Rp50.000

Total bulan:

**Rp50.000**

---

### Rumah Belum Dihuni

Iuran bulanan:

Rp35.000

Total bulan:

**Rp35.000**

---

### Rumah Dihuni + Mulai Pembangunan

Iuran bulanan:

Rp50.000

Iuran pembangunan:

Rp100.000 sekali bayar

Total kewajiban awal:

**Rp150.000**

Bulan berikutnya:

**Rp50.000**

---

# 81. SECTION PENJELASAN PEMBANGUNAN

Buat section khusus:

### Satu kegiatan, satu kali iuran pembangunan.

Text:

**Ketika warga mulai membangun dapur atau menambah bangunan, sistem akan mencatat iuran pembangunan sebesar tarif yang berlaku. Iuran tersebut hanya dikenakan satu kali untuk kegiatan tersebut, meskipun proses pembangunan berlangsung beberapa bulan.**

Ini harus menjadi salah satu pesan utama landing page.

---

# 82. CARA KERJA

### 1. Data Rumah

Data KK dan status rumah disimpan.

### 2. Tagihan Bulanan

Sistem membuat tagihan rutin sesuai status hunian.

### 3. Pembangunan

Ketika warga mulai pembangunan, pengelola mencatat kegiatan tersebut.

### 4. Iuran Pembangunan

Sistem membuat satu invoice pembangunan.

### 5. Pembayaran

Petugas mencatat pembayaran.

### 6. Laporan

Pengelola dapat memantau semuanya.

---

# 83. FAQ

### Apakah iuran pembangunan dibayar setiap bulan?

Tidak.

Iuran pembangunan hanya dibayar satu kali untuk setiap kegiatan pembangunan.

### Berapa iuran pembangunan?

Default Rp100.000 untuk satu kegiatan pembangunan.

### Bagaimana jika pembangunan berlangsung beberapa bulan?

Tidak ada tambahan Rp100.000 setiap bulan. Tarif tersebut tetap hanya dikenakan satu kali untuk kegiatan pembangunan tersebut.

### Jika beberapa tahun kemudian membangun lagi bagaimana?

Kegiatan pembangunan baru akan dicatat sebagai project baru dan dapat dikenakan iuran pembangunan satu kali lagi sesuai tarif yang berlaku.

### Apakah iuran bulanan tetap dibayar saat rumah sedang dibangun?

Ya. Iuran bulanan tetap mengikuti status hunian rumah. Iuran pembangunan merupakan kewajiban terpisah.

### Apakah tarif dapat berubah?

Ya, administrator dapat mengubah tarif berdasarkan kebijakan kompleks.

---

# 84. LANDING CTA

Headline:

**Administrasi Iuran Lebih Tertib, Sederhana, dan Mudah Dipantau.**

Button:

**Masuk ke Sistem**

---

# 85. FOOTER

Tampilkan:

**IuranKita**

`Sistem Administrasi Iuran Warga`

Nama kompleks.

Alamat.

Kontak.

Copyright:

```blade
© {{ now()->year }} IuranKita. Semua hak dilindungi.
```

---

# 86. FORMAT RUPIAH

Gunakan:

```text
Rp50.000
Rp35.000
Rp100.000
Rp1.250.000
```

Database menggunakan integer.

Tidak menggunakan float.

---

# 87. FORMAT DATE

UI:

```text
30 September 2026
```

Periode:

```text
September 2026
```

Database:

format standar Laravel/MySQL.

---

# 88. EMPTY STATE

Jika belum ada rumah:

**Belum ada data rumah**

Button:

**Tambah Data Rumah**

Jika belum ada pembangunan:

**Belum ada kegiatan pembangunan**

Button:

**Tambah Pembangunan**

Jika belum ada invoice pembangunan:

**Belum ada iuran pembangunan**

---

# 89. NOTIFICATION

Contoh sukses:

**Pembangunan berhasil dicatat. Invoice iuran pembangunan telah dibuat sebesar Rp100.000.**

Contoh selesai:

**Pembangunan telah ditandai selesai. Tidak ada tagihan pembangunan tambahan.**

Contoh pembayaran:

**Pembayaran Rp100.000 berhasil dicatat.**

---

# 90. ERROR MESSAGE

Gunakan Bahasa Indonesia.

Contoh:

**Pembayaran tidak dapat melebihi saldo tagihan.**

**Pembangunan ini sudah memiliki invoice. Invoice baru tidak dapat dibuat.**

**Rumah ini masih memiliki pembangunan aktif.**

**Tarif aktif untuk jenis ini tidak ditemukan.**

---

# 91. TESTING

Gunakan Pest.

## Monthly billing

Test:

1. occupied = Rp50.000;
2. unoccupied = Rp35.000;
3. satu household satu invoice per periode;
4. tariff snapshot.

## Construction billing

Test:

1. project active membuat invoice;
2. nominal default Rp100.000;
3. satu project hanya menghasilkan satu invoice;
4. project active tidak membuat invoice bulanan;
5. project berlangsung 6 bulan tetap hanya satu invoice;
6. completion tidak membuat invoice baru;
7. cancelled before active tidak menghasilkan invoice;
8. project kedua menghasilkan invoice baru.

## Payment

Test:

1. payment normal;
2. partial;
3. paid;
4. overpayment ditolak;
5. receipt unique.

---

# 92. TEST CASE YANG WAJIB

Test skenario berikut:

```text
Household: occupied
Construction: active
Project duration: 3 months
```

Hasil harus:

September:
Monthly Rp50.000
Construction Rp100.000

Oktober:
Monthly Rp50.000
Construction Rp0

November:
Monthly Rp50.000
Construction Rp0

Total construction:

**Rp100.000**

Bukan:

**Rp300.000**

---

# 93. TEST CASE LAIN

```text
Household: unoccupied
Construction: active
Project duration: 5 months
```

Hasil:

Bulan pertama:
Rp35.000 + Rp100.000

Bulan kedua:
Rp35.000

Bulan ketiga:
Rp35.000

Bulan keempat:
Rp35.000

Bulan kelima:
Rp35.000

Total construction:

**Rp100.000**

---

# 94. EXAMPLE DATABASE

## Household

```text
A-01
Budi Santoso
occupied
```

## Construction

```text
household_id: A-01
type: kitchen
start_date: 2026-09-01
status: active
one_time_fee: 100000
```

## Invoice monthly

```text
type: monthly
period: 2026-09
total: 50000
```

## Invoice construction

```text
type: construction
construction_project_id: 1
total: 100000
```

Oktober:

```text
monthly: 50000
```

Tidak membuat:

```text
construction: 100000
```

---

# 95. FILAMENT NAVIGATION

## Dashboard

* Dashboard

## Data Master

* KK / Rumah
* Tarif Iuran
* Pengaturan

## Operasional

* Pembangunan
* Tagihan
* Pembayaran

## Laporan

* Iuran Bulanan
* Iuran Pembangunan
* Pembayaran
* Tunggakan

## Sistem

* Pengguna

---

# 96. PROJECT STRUCTURE

```text
app/
├── Enums/
│   ├── OccupancyStatus.php
│   ├── ConstructionStatus.php
│   ├── ConstructionType.php
│   ├── InvoiceType.php
│   ├── InvoiceStatus.php
│   ├── PaymentMethod.php
│   └── UserRole.php
│
├── Models/
│   ├── Household.php
│   ├── ConstructionProject.php
│   ├── FeeRate.php
│   ├── Invoice.php
│   ├── InvoiceItem.php
│   ├── Payment.php
│   ├── AppSetting.php
│   └── AuditLog.php
│
├── Services/
│   ├── Billing/
│   │   ├── MonthlyBillingCalculator.php
│   │   ├── MonthlyInvoiceGenerator.php
│   │   └── ConstructionInvoiceGenerator.php
│   │
│   ├── Construction/
│   │   └── ConstructionProjectService.php
│   │
│   ├── Payment/
│   │   └── PaymentRecorder.php
│   │
│   └── Receipt/
│       └── ReceiptNumberGenerator.php
│
└── Filament/
    ├── Resources/
    ├── Pages/
    └── Widgets/
```

---

# 97. DO NOT DO

Jangan:

* mengenakan Rp100.000 setiap bulan selama pembangunan;
* menambahkan Rp100.000 ke monthly invoice;
* menggunakan scheduler untuk membuat construction invoice bulanan;
* membuat construction invoice setiap bulan;
* menghapus invoice pembangunan ketika project selesai;
* mengubah nominal invoice lama ketika tarif berubah.

---

# 98. HARUS DILAKUKAN

Harus:

* monthly fee terpisah dari construction fee;
* construction fee hanya sekali per project;
* setiap project dapat memiliki satu construction invoice;
* project baru dapat menghasilkan construction fee baru;
* monthly billing tetap berjalan terlepas dari project;
* historical amount disimpan pada invoice;
* laporan memisahkan monthly dan construction.

---

# 99. NO OVER-ENGINEERING

Jangan membuat:

* React;
* Vue;
* SPA;
* microservices;
* API terpisah;
* payment gateway untuk MVP;
* WhatsApp API untuk MVP;
* mobile application;
* multi-tenancy;
* accounting enterprise.

Gunakan:

**Laravel Monolith + Filament + Livewire + Blade.**

---

# 100. PHASE 2

Fitur yang dapat ditambahkan kemudian:

* portal warga;
* notifikasi WhatsApp;
* upload bukti transfer;
* QRIS;
* payment gateway;
* reminder tunggakan;
* dashboard analitik;
* export Excel/PDF;
* mobile app.

---

# 101. ACCEPTANCE CRITERIA

MVP dianggap selesai apabila:

### Data rumah

* dapat dibuat;
* dapat diubah;
* status occupied/unoccupied berjalan.

### Iuran bulanan

* occupied menghasilkan Rp50.000;
* unoccupied menghasilkan Rp35.000;
* invoice dibuat satu kali per periode;
* invoice tidak duplikat.

### Pembangunan

* dapat dibuat;
* dapat diaktifkan;
* menghasilkan SATU invoice pembangunan;
* nominal sesuai tarif;
* tidak muncul lagi pada bulan berikutnya;
* project dapat ditandai selesai.

### Pembayaran

* dapat dicatat;
* saldo dihitung;
* status otomatis berubah;
* kwitansi dapat dicetak.

### Laporan

* monthly;
* construction;
* payment;
* outstanding.

### Landing

* modern;
* responsive;
* clean;
* mobile-friendly;
* menjelaskan dengan jelas bahwa iuran pembangunan adalah **sekali bayar**.

---

# 102. DEFINITION OF DONE

Sebelum menyatakan aplikasi selesai:

* migration berhasil;
* seeder berhasil;
* login berhasil;
* household berhasil;
* fee rate berhasil;
* construction berhasil;
* monthly billing berhasil;
* construction billing berhasil;
* payment berhasil;
* receipt berhasil;
* dashboard berhasil;
* reports berhasil;
* landing page berhasil;
* authorization berhasil;
* tests berhasil;
* npm build berhasil;
* tidak ada duplicate invoice;
* tidak ada construction fee recurring.

---

# 103. MASTER INSTRUCTION UNTUK ANTIGRAVITY

Implementasikan aplikasi **IuranKita** berdasarkan seluruh PRD ini.

Jangan hanya membuat mockup.

Buat aplikasi Laravel yang benar-benar berjalan dengan MySQL.

Prioritaskan:

1. database;
2. model;
3. enum;
4. relationship;
5. business logic;
6. service layer;
7. authorization;
8. Filament resources;
9. monthly billing;
10. construction billing satu kali;
11. payment;
12. receipt;
13. reports;
14. dashboard;
15. landing page;
16. testing;
17. QA.

---

# 104. PERATURAN BISNIS PALING PENTING

Implementasi harus mematuhi aturan berikut tanpa interpretasi lain:

### RULE 1

Rumah dihuni:

**Rp50.000 per bulan**

### RULE 2

Rumah belum dihuni:

**Rp35.000 per bulan**

### RULE 3

Warga membangun dapur/menambah bangunan:

**Rp100.000 sekali bayar untuk satu kegiatan pembangunan**

### RULE 4

Durasi pembangunan tidak mempengaruhi nominal.

Satu bulan:

Rp100.000

Enam bulan:

Rp100.000

### RULE 5

Iuran pembangunan bukan recurring fee.

### RULE 6

Iuran pembangunan tidak dimasukkan sebagai item otomatis ke invoice bulanan.

### RULE 7

Satu project pembangunan maksimal mempunyai satu invoice construction.

### RULE 8

Jika warga melakukan pembangunan baru di kemudian hari, itu menjadi project baru dan dapat dikenakan Rp100.000 lagi.

### RULE 9

Iuran bulanan tetap berjalan berdasarkan status hunian.

### RULE 10

Menyelesaikan pembangunan tidak membuat biaya baru dan tidak mengubah histori invoice.

---

# 105. BRAND COPY FINAL

## Nama

**IuranKita**

## Tagline

**Bayar Iuran, Tertib Administrasi, Nyaman Bertetangga.**

## Hero

**Kelola Iuran Warga Lebih Mudah.**

## Description

**IuranKita membantu pengelola kompleks mengelola iuran bulanan, iuran pembangunan, pembayaran, tunggakan, dan laporan warga dalam satu sistem yang sederhana.**

## Construction message

**Satu kegiatan pembangunan, satu kali iuran.**

---

# 106. REKOMENDASI NAMA

Nama utama:

**IuranKita**

Alternatif:

* IuranWarga
* IuranKomplek
* IuranRumah
* TertibIuran
* WargaTertib
* KasWarga
* IuranHub

Rekomendasi:

**IuranKita**

karena mudah diingat, tidak terlalu sempit, dan tetap relevan apabila aplikasi nantinya mempunyai fitur tambahan.

---

# 107. FINAL PROJECT STRUCTURE

Public:

```text
/
├── Hero
├── Tentang
├── Jenis Iuran
├── Cara Kerja
├── FAQ
└── Login
```

Admin:

```text
/admin
├── Dashboard
├── KK / Rumah
├── Pembangunan
├── Tarif Iuran
├── Tagihan
├── Pembayaran
├── Laporan
├── Pengaturan
└── Pengguna
```

---

# 108. FINAL EXPECTED BEHAVIOR

Contoh lengkap:

## September

Rumah A:

Status:
Dihuni

Pembangunan:
Membangun dapur

Monthly invoice:

Rp50.000

Construction invoice:

Rp100.000

Total invoice diterbitkan September:

Rp150.000

---

## October

Monthly invoice:

Rp50.000

Construction invoice:

**Rp0**

Total:

**Rp50.000**

---

## November

Monthly invoice:

Rp50.000

Construction:

**Rp0**

Total:

**Rp50.000**

---

## December

Jika pembangunan selesai:

Monthly invoice:

Rp50.000

Construction:

**Rp0**

Total:

**Rp50.000**

---

# END OF PRD
