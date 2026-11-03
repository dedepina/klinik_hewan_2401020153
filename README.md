# Sistem Pasien Klinik Hewan (Paket 2 Tipe D)

Nama: Devina Rindumasha | NIM: 2401020153

Aplikasi CRUD Laravel dengan PDO untuk data pasien klinik hewan.

## Cara menjalankan
1. `composer install`
2. Salin `.env.example` menjadi `.env`, lalu `php artisan key:generate`
3. Jalankan `database/sql/struktur_klinik.sql` dan `database/sql/data_klinik.sql` di MySQL
4. `php artisan config:clear` lalu `php artisan serve`
5. Buka `http://127.0.0.1:8000/pasien-hewan`

## Route
GET/POST `/pasien-hewan`, GET `/pasien-hewan/{id}/edit`, PUT/DELETE `/pasien-hewan/{id}`