# Known Issues & Aturan — Pertemuan 04

## Aturan bypass global scope

- Bypass hanya eksplisit: `withoutGlobalScope('tenant')`, tidak boleh `withoutGlobalScopes()` tersebar.
- Yang boleh bypass:
  - Kueri katalog publik (`PublicCatalogQuery`) — pengganti: filter `canteen_id`, tenant `active`, menu `is_available`.
  - Service admin kantin (Modul 5).
  - Job yang membawa `tenant_id` sendiri dan membentuk context di `handle()`.
- Yang tidak boleh: controller/Livewire tenant, dan kueri yang bergantung pada input request.
- Setiap bypass wajib berada di kelas khusus, punya filter pengganti, dan diuji.
- Review: `findstr /s /n /c:"withoutGlobalScope" app\*.php` — setiap hasil kode (bukan komentar) harus ada di kelas yang disetujui.

## Audit akses admin

- Akses admin lintas tenant harus dicatat ke `audit_logs` oleh service admin (`AuditLogger`) yang dibangun di Modul 5.
- Pada Pertemuan 04 hanya aturan yang dicatat; implementasi belum ada.

## Catatan untuk modul berikutnya

- Aksi Livewire yang menerima ID wajib memanggil `$this->authorize()` atau mencari data lewat relasi tenant aktif (Modul 7), karena nilai properti/parameter dapat dimanipulasi dari browser.
- `{canteen:slug}` binding menyusul Modul 6.

## Masalah yang ditemukan saat praktik

- ____ (isi bila ada, misalnya perbedaan slash path di Windows pada `ModuleConventionTest`)