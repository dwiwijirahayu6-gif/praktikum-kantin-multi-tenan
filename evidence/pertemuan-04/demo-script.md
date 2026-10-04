# Demo Script — Pertemuan 04 (± 5 menit)

1. Tampilkan route tenant dan middleware-nya:
   `php artisan route:list --path=tenant -v`
   Tunjukkan `tenant/{tenant:slug}`, middleware `SetTenantContext`, dan scoped binding.
2. Tampilkan route menu modul Catalog:
   `php artisan route:list --path=menus`
3. Login sebagai operator Tenant A, buka `/tenant/{slug-A}/menus` → hanya menu tenant A.
4. Ganti ID menu di URL dengan ID menu Tenant B → **404**.
5. Coba buka `/tenant/{slug-B}/dashboard` sebagai operator Tenant A → **403**.
6. Tunjukkan tenant suspended → **403**.
7. Jalankan test isolasi:
   `php artisan test --compact tests\Feature\TenantIsolationTest.php`
8. Tunjukkan satu-satunya bypass scope:
   `findstr /s /n /c:"withoutGlobalScope" app\*.php`
9. Jelaskan empat lapis: TenantContext, global scope + `BelongsToTenant`, resolver + membership, policy + scoped binding (plus composite FK/unique dari Modul 3).