<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class PlatformModulesCommand extends Command
{
    protected $signature = 'platform:modules';
    protected $description = 'List platform module service providers configured in config/platform.php';

    public function handle(): int
    {
        /** @var array<int, class-string> $providers */
        $providers = (array) config('platform.providers', []);

        if (count($providers) === 0) {
            $this->info('No module providers configured.');
            return self::SUCCESS;
        }

        $this->info('Configured module providers:');
        foreach ($providers as $i => $provider) {
            $this->line(sprintf('%d) %s', $i + 1, $provider));
        }

        return self::SUCCESS;
    }
}
