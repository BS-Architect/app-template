<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use BsArchitect\ModuleAdmin\Infrastructure\Menu\MenuRegistry;

final class AdminMenuCommand extends Command
{
    protected $signature = 'admin:menu';
    protected $description = 'List admin menu items collected from module plugins';

    public function handle(MenuRegistry $menu): int
    {
        $items = $menu->all();

        if ($items === []) {
            $this->info('No admin menu items registered.');
            return self::SUCCESS;
        }

        foreach ($items as $item) {
            $this->line(sprintf('%d | %s -> %s', $item->order, $item->label, $item->route));
        }

        return self::SUCCESS;
    }
}
