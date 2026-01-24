<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Platform Modules
    |--------------------------------------------------------------------------
    | List service providers of modules/bundles that should be registered.
    | This is the ONLY place where host app composes the product.
    |
    | Note: modules will be composer packages, their providers will be in
    | module packages Infrastructure layer (Laravel-first).
    */

    'providers' => [
        // Example (later):
        // \BsArchitect\ModulePages\Infrastructure\ServiceProvider::class,
        // \BsArchitect\ModuleCatalog\Infrastructure\ServiceProvider::class,
        // \BsArchitect\ModuleOrders\Infrastructure\ServiceProvider::class,
        //
        // Admin shell:
        // \BsArchitect\ModuleAdmin\Infrastructure\ServiceProvider::class,
    ],
];
