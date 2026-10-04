# Quality Gate — Pertemuan 05

Tanggal: ____
Commit: ____

| Gate | Perintah | Hasil |
|---|---|---|
| Test suite | `php artisan test --compact` | ____ passed (____ assertions) |
| Test admin | `php artisan test --compact --filter=AdminManagementTest` | 9 passed (23 assertions) |
| Test batas komisi | `php artisan test --compact tests\Feature\CommissionScheduleBoundaryTest.php` | ____ |
| Code style | `vendor\bin\pint --test` | ____ |
| Build aset | `npm run build` | ____ |

Bukti: `test-results\phpunit-admin.txt`, `screenshots\`