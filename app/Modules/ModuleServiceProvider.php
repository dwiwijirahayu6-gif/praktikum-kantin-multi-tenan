<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use ReflectionClass;

abstract class ModuleServiceProvider extends ServiceProvider
{
    abstract protected function moduleAlias(): string;

    public function boot(): void
    {
        $path = $this->modulePath();

        // 1. Registrasi namespace tampilan Blade (contoh: admin::dashboard)
        $this->loadViewsFrom($path.'/resources/views', $this->moduleAlias());

        // 2. Registrasi komponen Livewire / Volt jika folder livewire tersedia
        $livewirePath = $path.'/resources/views/livewire';
        if (is_dir($livewirePath)) {
            if (method_exists(Livewire::getFacadeRoot(), 'addNamespace')) {
                Livewire::addNamespace(
                    namespace: $this->moduleAlias(),
                    viewPath: $livewirePath,
                );
            } elseif (class_exists(Volt::class)) {
                Volt::mount([$livewirePath]);
            }
        }

        // 3. Registrasi berkas rute otomatis milik modul
        foreach (glob($path.'/routes/*.php') ?: [] as $routeFile) {
            $this->loadRoutesFrom($routeFile);
        }
    }

    protected function modulePath(): string
    {
        return dirname((string) (new ReflectionClass($this))->getFileName());
    }
}
