# Demo Script — SOP Onboarding Tenant (Pertemuan 05)

1. Login sebagai admin kantin (`admin@kantin.test`).
2. Buka `/admin/tenants`.
3. Klik **+ Buat Tenant**.
4. Isi identitas dan komisi:
   - Nama tampilan: `Mie Ayam Enak`
   - Kode: `MIE-AYAM` (tanpa spasi)
   - Slug: `mie-ayam` (tanpa spasi)
   - Komisi (%): `15`
5. Klik **Simpan**.
6. Cek halaman detail: komisi aktif tampil dan tenant tercatat.
7. Jalankan `php artisan test --compact --filter=AdminManagementTest`.