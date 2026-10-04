# Known Issues & Aturan — Pertemuan 05

## Formula komisi

- Komisi = subtotal × rate snapshot.
- Setiap `tenant_order` menyimpan snapshot rate sehingga order lama tidak berubah saat komisi diganti.
- Versi komisi berlaku pada interval **[valid_from, valid_to)**: batas awal termasuk, batas akhir tidak termasuk. Versi lama berakhir tepat saat versi baru mulai.

## Temuan saat praktik

- Route `admin/dashboard` terdaftar dobel (`admin/admin/dashboard`) karena `PortalRoutes::admin()` terpasang di `bootstrap\app.php` dan di `routes\admin.php`. Diperbaiki dengan menghapus pembungkus di `routes\admin.php`.
- Trait `BelongsToTenant` belum terpasang di `MenuCategory`, sehingga `tenant_id` tidak terisi otomatis.
- `DemoCanteenSeeder` memanggil dirinya sendiri (rekursi), sehingga baris `user_canteen_roles` tidak pernah dibuat dan `/admin/tenants` memberi 403.
- Form tenant menolak spasi pada kode dan slug (aturan `alpha_dash`).
- `ParseError unexpected endif` di `tenants\edit.blade.php` pada test, teratasi dengan `php artisan view:clear`.