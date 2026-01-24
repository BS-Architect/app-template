<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class PlatformServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /** @var array<int, class-string> $providers */
        $providers = (array) config('platform.providers', []);

        foreach ($providers as $providerClass) {
            if (!is_string($providerClass) || $providerClass === '') {
                continue;
            }

            $this->app->register($providerClass);
        }
    }
}
