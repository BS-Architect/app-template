<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Localization\UrlLocaleSwitcher;
use App\Support\Localization\UrlLocaleSwitcherInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class LocalizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UrlLocaleSwitcherInterface::class, UrlLocaleSwitcher::class);
    }

    public function boot(): void
    {
        Route::pattern('locale', implode('|', config('i18n.supported', ['uk'])));
    }
}
