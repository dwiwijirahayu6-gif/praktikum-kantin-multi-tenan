# Stage Checklist — Pertemuan 04

| Tahap | Checkpoint | Selesai |
|---|---|---|
| 1. Definisikan konteks dan aturan akses | Setiap bypass mempunyai filter pengganti dan alasan. | ☐ |
| 2. Implementasikan TenantContext | Dua request/job tidak berbagi state tenant. | ☐ |
| 3. Middleware resolver dan role check | Tenant yang suspended atau tanpa role mendapat 403. | ☐ |
| 4. Global scope dan model integration | Query biasa hanya mengembalikan tenant aktif. | ☐ |
| 5. Policy dan scoped route binding | Mengubah ID pada URL atau payload Livewire tidak membuka data lain. | ☐ |
| 6. Public query dan bypass terkontrol | Katalog hanya menampilkan tenant aktif dari canteen hasil QR. | ☐ |
| 7. Security tests dan commit | Seluruh matriks allow/deny hijau. | ☐ |

## Evidence Praktikum

☐ TenantContext request-scoped dan middleware resolver.
☐ Trait `BelongsToTenant` pada model tenant-owned.
☐ Policy dan scoped route binding untuk menu/order/withdrawal.
☐ Test matrix allow/deny tenant A vs tenant B.

## Exit Ticket

1. Mengapa global scope saja belum cukup?
   Jawaban: ____
2. Kapan bypass scope sah?
   Jawaban: ____
3. Bagaimana mencegah context bocor pada queue worker?
   Jawaban: ____