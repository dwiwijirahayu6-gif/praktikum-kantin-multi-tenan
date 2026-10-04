# Security Verification — Akses Lintas Tenant

Tenant A = aktor (member). Tenant B = tenant lain. Hasil diambil dari test, bukan asumsi.

| Skenario | Test | Hasil yang diharapkan | Lulus |
|---|---|---|---|
| Menu index hanya milik tenant sendiri | `test_menu_index_returns_only_own_tenant` | 200, hanya menu tenant A | ☐ |
| Show menu milik tenant B lewat URL tenant A | `test_scoped_binding_blocks_cross_tenant_menu` | **404** | ☐ |
| Update menu sendiri | `test_member_can_toggle_own_menu` | 200, `is_available` berubah | ☐ |
| Policy update menu tenant B | `test_policy_denies_cross_tenant_update` | ditolak (`false`) | ☐ |
| Global scope aktif | `test_global_scope_returns_only_active_tenant_rows` | hanya baris tenant aktif | ☐ |
| Tanpa context, scope tidak diterapkan | `test_without_context_scope_is_not_applied` | semua baris terbaca | ☐ |
| Context tidak bocor antar request | `test_context_is_reset_between_requests` | `has()` false setelah request | ☐ |
| Auto-fill `tenant_id` | `test_tenant_id_is_autofilled_on_create_within_context` | `tenant_id` = tenant aktif | ☐ |
| Katalog publik tidak bocor lintas canteen/tenant nonaktif | `test_public_catalog_query_no_cross_canteen_or_inactive_leak` | hanya menu tenant aktif satu canteen | ☐ |
| Job membentuk dan membersihkan context | `test_job_sets_and_clears_context_per_tenant` | dua job berurutan terisolasi | ☐ |

## Hasil 403 / 404 di portal

| Kondisi | Status | Test |
|---|---|---|
| Tenant tidak ada | 404 | ____ |
| Tenant suspended (meski user anggota) | 403 | `test_suspended_tenant_is_forbidden_even_for_member` |
| User bukan anggota tenant | 403 | `test_non_member_is_forbidden_on_tenant_and_admin_contexts` |
| User tanpa role | 403 | `test_user_without_role_is_forbidden_on_internal_contexts` |
| Tamu | redirect ke login | `test_guest_is_redirected_to_login_on_tenant_and_admin` |

Catatan: assertion juga memastikan data tenant B tidak berubah dan tidak tampil pada response.