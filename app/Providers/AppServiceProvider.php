<?php
namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (File::exists(app_path('Helpers'))) {
            foreach (File::allFiles(app_path('Helpers')) as $file) {
                require_once $file->getPathname();
            }
        }
    }
}
