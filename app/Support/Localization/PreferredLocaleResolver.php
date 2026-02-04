<?php

declare(strict_types=1);

namespace App\Support\Localization;

use Illuminate\Http\Request;

final class PreferredLocaleResolver
{
    /**
     * Resolve locale with priority:
     * 1) Explicit (route {locale} or subdomain)
     * 2) Cookie
     * 3) Accept-Language header
     * 4) Default
     *
     * Returns [locale, source]
     *
     * @return array{0:string,1:LocaleSource}
     */
    public function resolve(Request $request): array
    {
        $supported = $this->supported();
        $default   = $this->defaultLocale();

        // 1) Explicit from route param (prefix strategy)
        $routeLocale = $request->route('locale');
        if (is_string($routeLocale) && $this->isSupported($routeLocale, $supported)) {
            return [$routeLocale, LocaleSource::Explicit];
        }

        // 1b) Explicit from subdomain (subdomain strategy)
        $subdomainLocale = $this->localeFromSubdomain($request, $supported);
        if ($subdomainLocale !== null) {
            return [$subdomainLocale, LocaleSource::Explicit];
        }

        // 2) Cookie
        $cookieName = (string) config('i18n.cookie.locale_name', 'locale');
        $cookieLocale = $request->cookie($cookieName);
        if (is_string($cookieLocale) && $this->isSupported($cookieLocale, $supported)) {
            return [$cookieLocale, LocaleSource::Cookie];
        }

        // 3) Accept-Language
        $headerLocale = $this->localeFromAcceptLanguage($request, $supported);
        if ($headerLocale !== null) {
            return [$headerLocale, LocaleSource::Header];
        }

        // 4) Default
        return [$default, LocaleSource::Default];
    }

    /**
     * @return array<int,string>
     */
    public function supported(): array
    {
        /** @var array<int,string> $supported */
        $supported = config('i18n.supported', []);
        return $supported;
    }

    public function defaultLocale(): string
    {
        return (string) config('i18n.default', 'uk');
    }

    /**
     * @param array<int,string> $supported
     */
    private function isSupported(string $locale, array $supported): bool
    {
        return in_array($locale, $supported, true);
    }

    /**
     * @param array<int,string> $supported
     */
    private function localeFromAcceptLanguage(Request $request, array $supported): ?string
    {
        $header = $request->header('Accept-Language');
        if (!is_string($header) || trim($header) === '') {
            return null;
        }

        $mode = (string) config('i18n.accept_language.mode', 'loose');

        // Parse like: "uk-UA,uk;q=0.9,en-US;q=0.8,en;q=0.7"
        $candidates = [];
        foreach (explode(',', $header) as $part) {
            $part = trim($part);
            if ($part === '') continue;

            $q = 1.0;
            $lang = $part;

            if (str_contains($part, ';')) {
                [$lang, $params] = array_map('trim', explode(';', $part, 2));
                if (preg_match('/q=([0-9.]+)/', $params, $m)) {
                    $q = (float) $m[1];
                }
            }

            $lang = strtolower($lang);

            $candidates[] = ['lang' => $lang, 'q' => $q];
        }

        usort($candidates, fn ($a, $b) => $b['q'] <=> $a['q']);

        foreach ($candidates as $c) {
            $lang = $c['lang'];

            if ($mode === 'strict') {
                if (in_array($lang, $supported, true)) {
                    return $lang;
                }
                continue;
            }

            // loose: match primary tag (uk-UA -> uk)
            $primary = explode('-', $lang, 2)[0];
            if (in_array($primary, $supported, true)) {
                return $primary;
            }

            // Also allow exact match if present
            if (in_array($lang, $supported, true)) {
                return $lang;
            }
        }

        return null;
    }

    /**
     * Subdomain strategy:
     * - If domain map set: "uk:site.ua,en:en.site.ua" -> host match
     * - Else base domain: uk.example.com -> locale = "uk"
     *
     * @param array<int,string> $supported
     */
    private function localeFromSubdomain(Request $request, array $supported): ?string
    {
        $strategy = (string) config('i18n.strategy', 'prefix');
        if ($strategy !== 'subdomain') {
            return null;
        }

        $host = strtolower((string) $request->getHost());

        // explicit map has highest priority
        $map = (string) config('i18n.subdomain.domain_map', '');
        $parsedMap = $this->parseDomainMap($map);
        if ($parsedMap !== []) {
            foreach ($parsedMap as $locale => $domain) {
                if ($host === $domain && in_array($locale, $supported, true)) {
                    return $locale;
                }
            }
            return null;
        }

        $base = strtolower((string) config('i18n.subdomain.base_domain', ''));
        if ($base === '') {
            return null;
        }

        // Expect {locale}.{base}
        foreach ($supported as $locale) {
            if ($host === "{$locale}.{$base}") {
                return $locale;
            }
        }

        return null;
    }

    /**
     * Parse "uk:site.ua,en:en.site.ua" => ['uk'=>'site.ua','en'=>'en.site.ua']
     *
     * @return array<string,string>
     */
    private function parseDomainMap(string $map): array
    {
        $out = [];
        foreach (explode(',', $map) as $pair) {
            $pair = trim($pair);
            if ($pair === '' || !str_contains($pair, ':')) continue;

            [$loc, $domain] = array_map('trim', explode(':', $pair, 2));
            $loc = strtolower($loc);
            $domain = strtolower($domain);

            if ($loc !== '' && $domain !== '') {
                $out[$loc] = $domain;
            }
        }
        return $out;
    }
}
