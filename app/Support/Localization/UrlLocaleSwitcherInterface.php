<?php

declare(strict_types=1);

namespace App\Support\Localization;

interface UrlLocaleSwitcherInterface
{
    public function currentLocale(): string;

    /** @return array<int,string> */
    public function supported(): array;

    public function root(string $locale): string;

    /**
     * Build URL to same page (or provided path) for a locale.
     * $pathWithoutLocale must start with "/" (e.g. "/about", "/").
     */
    public function forLocale(string $locale, ?string $pathWithoutLocale = null): string;

    /**
     * Get current request path WITHOUT leading locale (prefix strategy only).
     * Always returns "/" or "/something".
     */
    public function currentPathWithoutLocale(): string;
}
