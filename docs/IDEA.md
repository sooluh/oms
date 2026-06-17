# Oh My School

## Product Requirement Document (PRD)

### Version

1.0

### Status

Draft

---

# 1. Product Overview

## Product Name

Oh My School

## Vision

Oh My School adalah platform Dynamic Student Information Lookup Engine yang memungkinkan sekolah membuat berbagai layanan pengecekan mandiri secara fleksibel tanpa perlu membuat aplikasi terpisah untuk setiap kebutuhan.

Sistem dirancang agar administrator dapat membuat layanan baru, menentukan struktur data, melakukan import data, serta menyediakan halaman publik untuk siswa melakukan pencarian informasi secara mandiri.

---

# 2. Background

Sebelumnya sekolah memiliki beberapa aplikasi pengecekan yang bersifat statis.

Contoh:

- Aplikasi Cek Kelulusan.
- Aplikasi Cek Nilai Ujian.
- Aplikasi Cek Hasil PPDB.

Setiap aplikasi memiliki keterbatasan:

- Hanya mendukung satu jenis layanan.
- Struktur field telah ditentukan di source code.
- Identifier pencarian bersifat tetap (contohnya hanya NIS).
- Ketika membutuhkan layanan baru, aplikasi harus di-fork dan dimodifikasi ulang.

Pendekatan tersebut menyebabkan:

- Duplikasi project.
- Maintenance menjadi sulit.
- Pengembangan menjadi lambat.
- Risiko inkonsistensi antar sistem.

---

# 3. Goals

Membangun satu platform yang dapat mendukung berbagai layanan pengecekan seperti:

- Kelulusan.
- Hasil ujian.
- Hasil seleksi PPDB.
- Kenaikan kelas.
- Pembagian kelas.
- Informasi administrasi.
- Layanan lainnya yang membutuhkan pencarian data berdasarkan identifier unik.

Semua layanan harus dapat dibuat melalui panel administrator tanpa perubahan source code.

---

# 4. Core Concepts

## 4.1 Service

Service adalah sebuah layanan informasi yang dapat diakses oleh siswa melalui halaman publik.

Contoh:

- Kelulusan Tahun 2026.
- Hasil Ujian Semester Genap.
- Hasil Seleksi PPDB 2026.

Setiap Service memiliki properti:

| Property                | Description                         |
| ----------------------- | ----------------------------------- |
| Name                    | Nama layanan                        |
| Slug                    | URL unik layanan                    |
| Icon                    | Nama icon dari Phosphor Icons       |
| Description             | Deskripsi singkat layanan           |
| Is Active               | Status aktif/nonaktif               |
| Start Date              | Tanggal mulai layanan tersedia      |
| End Date                | Tanggal berakhir layanan            |
| Sort Order              | Urutan tampilan pada halaman publik |
| Search Attempt Limit    | Maksimal percobaan pencarian        |
| Search Attempt Duration | Periode pembatasan percobaan        |

---

## 4.2 Field

Field adalah definisi struktur data dari sebuah Service.

Contoh Field untuk Service Kelulusan:

| Name       | Label            | Type      |
| ---------- | ---------------- | --------- |
| nis        | NIS              | Text      |
| nama       | Nama Siswa       | Text      |
| kelas      | Kelas            | Text      |
| status     | Status Kelulusan | Text      |
| keterangan | Pesan            | Long Text |

Setiap Field memiliki konfigurasi:

| Property   | Description                           |
| ---------- | ------------------------------------- |
| Name       | Nama internal field                   |
| Label      | Label yang ditampilkan                |
| Type       | Tipe data                             |
| Required   | Apakah wajib diisi saat import        |
| Visible    | Apakah ditampilkan ke pengguna publik |
| Sort Order | Urutan tampilan                       |

---

## 4.3 Identifier

Identifier adalah Field yang digunakan untuk proses pencarian data oleh siswa.

Contoh:

- NIS.
- NISN.
- Nomor Pendaftaran.
- Tanggal Lahir.

### Identifier Rules

- Setiap Service wajib memiliki minimal 1 identifier.
- Setiap Service dapat memiliki maksimal 3 identifier.
- Identifier dipilih dari Field yang telah dibuat.
- Identifier harus memiliki nilai unik dalam satu Service.
- Identifier mendukung dua mode pencarian:

### OR Mode

Pencarian berhasil apabila salah satu identifier cocok.

Contoh:

Identifier:

- NIS
- NISN

Tampilan publik:

```
Masukkan NIS atau NISN

[________________]
```

---

### AND Mode

Pencarian berhasil apabila seluruh identifier sesuai.

Contoh:

Identifier:

- NIS
- Tanggal Lahir

Tampilan publik:

```
NIS
[_______________]

Tanggal Lahir
[_______________]
```

Semua input wajib diisi.

---

## 4.4 Record

Record adalah data hasil import yang dimiliki sebuah Service.

Record menyimpan data dinamis dalam bentuk JSON.

Contoh:

```json
{
    "nis": "12345",
    "nama": "Ahmad Fauzi",
    "kelas": "XII IPA 1",
    "status": "LULUS"
}
```

Untuk kebutuhan performa pencarian, setiap Record juga memiliki maksimal tiga kolom identifier terpisah:

```
identifier_1
identifier_2
identifier_3
```

Kolom tersebut digunakan untuk indexing database dan tidak menggantikan data JSON utama.

---

# 5. Public User Flow

## Landing Page

Halaman utama menampilkan daftar Service yang tersedia.

Setiap Service menampilkan:

- Icon.
- Nama.
- Deskripsi singkat.

Ketika dipilih, pengguna diarahkan ke:

```
/services/{slug}
```

---

## Service Page

Halaman pencarian menyesuaikan konfigurasi Service.

Contoh OR Mode:

```
Masukkan NIS atau NISN

[____________]

[ Cari ]
```

Contoh AND Mode:

```
NIS

[____________]

Tanggal Lahir

[____________]

[ Cari ]
```

---

## Result Page

Apabila data ditemukan, sistem menampilkan hanya Field yang memiliki status Visible.

Contoh:

```
Nama:
Ahmad Fauzi

Kelas:
XII IPA 1

Status:
LULUS
```

---

# 6. Data Import

Administrator dapat melakukan import file Excel.

Proses import harus melakukan validasi:

- File harus memiliki format Excel yang didukung.
- Header kolom harus sesuai dengan Field yang telah didefinisikan.
- Field Required wajib tersedia.
- Data identifier wajib tersedia.
- Tidak boleh terdapat duplicate identifier dalam satu Service.
- Tipe data harus sesuai dengan konfigurasi Field.

Apabila terdapat kesalahan, sistem harus memberikan laporan error yang jelas beserta nomor baris data.

---

# 7. QR Code

Setiap Service memiliki QR Code otomatis berdasarkan URL Service.

Contoh:

```
https://domain.com/services/kelulusan-2026
```

QR Code dapat digunakan untuk:

- Banner sekolah.
- Surat pengumuman.
- Grup komunikasi orang tua.

---

# 8. Analytics

Sistem harus mencatat aktivitas pencarian.

Data analytics meliputi:

- Total pencarian.
- Jumlah pencarian berhasil.
- Jumlah pencarian gagal.
- Jumlah pengunjung unik.
- Statistik pencarian harian.
- Waktu pencarian paling ramai.

---

# 9. Security

Sistem harus memiliki perlindungan terhadap pencarian berlebihan.

Fitur keamanan:

- Rate limiting berdasarkan IP address.
- Maksimal percobaan pencarian dapat diatur untuk setiap Service.
- Pemblokiran sementara setelah batas pencarian tercapai.
- Pencatatan aktivitas pencarian untuk kebutuhan audit.

---

# 10. Export Data

Administrator dapat melakukan export Record.

Format yang didukung:

- Excel (.xlsx).
- CSV.

Export dapat dilakukan berdasarkan:

- Seluruh data.
- Filter tertentu.
- Rentang waktu import.

---

# 11. Database Design

## services

Menyimpan konfigurasi layanan.

```
id
name
slug
icon
description
is_active
start_date
end_date
search_mode (OR / AND)
max_attempt
attempt_duration
sort_order
created_at
updated_at
```

---

## service_fields

Menyimpan schema Field.

```
id
service_id
name
label
type
required
visible
sort_order
created_at
updated_at
```

---

## service_identifiers

Mapping identifier yang digunakan Service.

```
id
service_id
field_id
identifier_order
created_at
```

Maximum record: 3 per Service.

---

## service_records

Menyimpan data siswa.

```
id
service_id

identifier_1
identifier_2
identifier_3

data JSON

created_at
updated_at
```

Recommended index:

```
INDEX(service_id, identifier_1)
INDEX(service_id, identifier_2)
INDEX(service_id, identifier_3)
```

---

## search_logs

Menyimpan histori pencarian.

```
id
service_id
input_value
is_success
ip_address
user_agent
created_at
```

---

# 12. Non Functional Requirements

## Performance

- Pencarian harus memiliki response time kurang dari 1 detik untuk penggunaan normal.
- Identifier wajib menggunakan database index.
- Sistem harus mampu menangani ratusan ribu hingga jutaan Record.

## Scalability

Sistem harus mampu menambah Service baru tanpa perubahan source code.

## Maintainability

Perubahan schema Service tidak boleh membutuhkan migration database.

---

# 13. Future Possibilities

Fitur yang dapat dikembangkan pada versi berikutnya:

- Custom template halaman hasil.
- API integration dengan sistem akademik.
- Notification atau pengumuman otomatis.
- Import data terjadwal dari sistem eksternal.

---

# 14. Product Philosophy

One Platform. Unlimited School Checking Services.

Oh My School bukan aplikasi cek kelulusan atau cek nilai, tetapi sebuah engine yang memungkinkan sekolah membangun berbagai layanan informasi siswa secara dinamis.
