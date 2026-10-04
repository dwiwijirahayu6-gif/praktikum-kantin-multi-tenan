<?php

namespace Tests\Feature\Auth;

use App\Models\Canteen;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserTenantRole;
use App\Support\Auth\LoginLockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Volt\Volt;
use Tests\TestCase;

/**
 * UC-19 Autentikasi Pengguna: pesan generik, penguncian 5x/10 menit selama 15 menit,
 * akun nonaktif ditolak, redirect dasbor per peran, dan sesi 8 jam.
 * Login di proyek ini adalah komponen Volt `pages.auth.login` (Breeze), bukan rute Fortify.
 */
class LoginLockoutTest extends TestCase
{
    use RefreshDatabase;

    private const GENERIC = 'Surel atau kata sandi tidak sesuai.';

    /**
     * Limiter per-IP (20/menit) memakai cache bersama; dibersihkan agar setiap skenario mandiri.
     */
    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('login-ip:127.0.0.1');
    }

    private function tryLogin(string $email, string $password)
    {
        return Volt::test('pages.auth.login')
            ->set('form.email', $email)
            ->set('form.password', $password)
            ->call('login');
    }

    public function test_wrong_password_and_unknown_email_get_the_same_generic_message(): void
    {
        $user = User::factory()->create();

        $this->tryLogin($user->email, 'salah-sandi')->assertHasErrors(['form.email'])->assertSee(self::GENERIC);
        $this->tryLogin('tidak-ada@kantin.test', 'apa-saja')->assertHasErrors(['form.email'])->assertSee(self::GENERIC);
        $this->assertGuest();
    }

    public function test_five_failures_within_ten_minutes_lock_the_account_even_with_correct_password(): void
    {
        $user = User::factory()->create();

        foreach (range(1, LoginLockout::MAX_FAILURES) as $ignored) {
            $this->tryLogin($user->email, 'salah-sandi');
        }

        $this->tryLogin($user->email, 'password')
            ->assertHasErrors(['form.email'])
            ->assertSee('Akun terkunci sementara');
        $this->assertGuest();
    }

    public function test_lock_expires_after_fifteen_minutes(): void
    {
        $user = User::factory()->create();
        foreach (range(1, LoginLockout::MAX_FAILURES) as $ignored) {
            $this->tryLogin($user->email, 'salah-sandi');
        }

        $this->travel(14)->minutes();
        $this->tryLogin($user->email, 'password')->assertHasErrors(['form.email']);

        $this->travel(2)->minutes();
        $this->tryLogin($user->email, 'password')->assertHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_failures_spread_beyond_the_ten_minute_window_do_not_lock(): void
    {
        $user = User::factory()->create();
        foreach (range(1, LoginLockout::MAX_FAILURES - 1) as $ignored) {
            $this->tryLogin($user->email, 'salah-sandi');
        }

        $this->travel(11)->minutes();
        $this->tryLogin($user->email, 'salah-sandi');

        $this->tryLogin($user->email, 'password')->assertHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_successful_login_resets_the_failure_counter(): void
    {
        $user = User::factory()->create();
        foreach (range(1, LoginLockout::MAX_FAILURES - 1) as $ignored) {
            $this->tryLogin($user->email, 'salah-sandi');
        }
        $this->tryLogin($user->email, 'password')->assertHasNoErrors();
        Auth::logout();

        $this->tryLogin($user->email, 'salah-sandi');
        $this->tryLogin($user->email, 'password')->assertHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_account_is_rejected_with_generic_message(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);

        $this->tryLogin($user->email, 'password')->assertHasErrors(['form.email'])->assertSee(self::GENERIC);
        $this->assertGuest();
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->tryLogin($admin->email, 'password')->assertRedirect(route('dashboard', absolute: false));
        $this->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_tenant_operator_is_redirected_to_own_tenant_dashboard(): void
    {
        $tenant = Tenant::factory()->create(['canteen_id' => Canteen::factory()->create()->id]);
        $operator = User::factory()->create(['role' => 'tenant', 'status' => 'active']);
        UserTenantRole::create(['user_id' => $operator->id, 'tenant_id' => $tenant->id, 'role' => 'operator']);

        $this->tryLogin($operator->email, 'password');
        $this->get(route('dashboard'))->assertRedirect(route('tenant.dashboard', ['tenant' => $tenant->slug]));
        $this->get(route('tenant.dashboard', ['tenant' => $tenant->slug]))->assertOk();
    }

    public function test_login_page_is_indonesian_and_states_the_policy(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk')
            ->assertSee('Kata sandi')
            ->assertSee('5× gagal dalam 10 menit → akun terkunci 15 menit.');
    }

    public function test_session_expires_after_eight_hours_of_inactivity(): void
    {
        $this->assertSame(480, config('session.lifetime'));
    }
}
