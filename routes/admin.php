<?php

use Illuminate\Support\Facades\Route;

/*
 * Administrasi tenant/role/komisi/rekening (Modul 5): app/Modules/Admin/routes/admin.php.
 * Policy per-aksi (TenantPolicy) diperiksa di controller modul.
 */

Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
