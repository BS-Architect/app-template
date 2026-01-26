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
        \BsArchitect\ModuleAdmin\Infrastructure\Providers\ModuleAdminServiceProvider::class,

        \BsArchitect\ModulePages\Infrastructure\Providers\ModulePagesServiceProvider::class,
        \BsArchitect\ModulePages\Admin\AdminServiceProvider::class,

        \BsArchitect\ModuleCatalog\Infrastructure\Providers\ModuleCatalogServiceProvider::class,
        \BsArchitect\ModuleCatalog\Admin\AdminServiceProvider::class,
    ],
];
