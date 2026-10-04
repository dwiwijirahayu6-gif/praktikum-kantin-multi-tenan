# Security Verification — Pertemuan 05

| Bukti | Cara verifikasi | Test | Lulus |
|---|---|---|---|
| Admin tanpa peran kantin mendapat 403 | Buka `/admin/tenants` tanpa baris `user_canteen_roles` → 403 (terlihat saat praktik) | `test_only_canteen_manager_can_see_tenant_index` | ☐ |
| Nomor rekening tidak tampil mentah | Tersimpan terenkripsi, tampil hanya 4 digit terakhir | `test_bank_account_is_stored_encrypted_with_last4` | ☐ |
| Audit tidak memuat nomor rekening | Log audit hanya berisi data tersamar | `test_audit_log_is_written_on_sensitive_action` | ☐ |
| Owner terakhir terlindungi | Penghapusan owner terakhir ditolak | `test_cannot_remove_last_owner` | ☐ |
| Hanya satu rekening primary | Rekening primary kedua ditolak | `test_only_one_primary_bank_account_per_tenant` | ☐ |
| Dua skema komisi terbuka ditolak DB | Guard di database | `test_two_open_commission_schemes_rejected_by_db_guard` | ☐ |

Screenshot: `screenshots\` (403 di `/admin/tenants`, halaman rekening yang hanya menampilkan 4 digit terakhir)