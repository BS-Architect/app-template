<?php

declare(strict_types=1);

return [
    /**
     * Supported locales (lowercase).
     */
    'supported' => array_values(array_filter(
        array_map('trim', explode(',', env('APP_SUPPORTED_LOCALES', 'uk,en')))
    )),

    /**
     * Default locale (must exist in supported).
     */
    'default' => env('APP_LOCALE', 'uk'),

    /**
     * Strategy:
     * - prefix:   example.com/{locale}/...
     * - subdomain: {locale}.example.com/...
     */
    'strategy' => env('APP_LOCALE_STRATEGY', 'prefix'),

    /**
     * Cookies.
     */
    'cookie' => [
        'locale_name' => env('APP_LOCALE_COOKIE', 'locale'),
        'locale_minutes' => (int) env('APP_LOCALE_COOKIE_MINUTES', 60 * 24 * 365),

        // Stores last visited "path without locale", for redirect from "/"
        'last_path_name' => env('APP_LAST_PATH_COOKIE', 'last_path'),
        'last_path_minutes' => (int) env('APP_LAST_PATH_COOKIE_MINUTES', 60 * 24 * 30),
    ],

    /**
     * Accept-Language matching:
     * - "strict": only exact matches (uk, en)
     * - "loose": match by primary tag (uk-UA -> uk, en-US -> en)
     */
    'accept_language' => [
        'mode' => env('APP_ACCEPT_LANGUAGE_MODE', 'loose'),
    ],

    /**
     * Subdomain settings (only used when strategy=subdomain).
     * Option A: base_domain = example.com => locale.example.com
     * Option B: explicit map via APP_LOCALE_DOMAIN_MAP
     */
    'subdomain' => [
        'base_domain' => env('APP_BASE_DOMAIN', ''), // e.g. "example.com"
        // explicit map: "uk:site.ua,en:en.site.ua"
        'domain_map' => env('APP_LOCALE_DOMAIN_MAP', ''),
    ],
];
