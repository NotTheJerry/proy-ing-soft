<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register PDF facade
        $this->app->singleton('pdf', function ($app) {
            return new \Barryvdh\DomPDF\PDF($app['dompdf.options'], $app['config'], $app['files'], $app['view']);
        });
        
        // Register aliases
        $this->app->alias('PDF', \Barryvdh\DomPDF\Facade\Pdf::class);
        $this->app->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrap();
        
        // Registrar el middleware de roles
        $router = $this->app['router'];
        $router->aliasMiddleware('role', \App\Http\Middleware\CheckRole::class);
    }
}
