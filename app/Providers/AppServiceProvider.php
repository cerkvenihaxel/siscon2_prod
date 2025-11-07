<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Fix para tipos ENUM con Doctrine
        Schema::defaultStringLength(191);

        // Registrar tipos ENUM personalizados para Doctrine
        $platform = Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        // Registrar observers
        \App\Models\UpConsumo::observe(\App\Observers\UpConsumoObserver::class);
    }
}
