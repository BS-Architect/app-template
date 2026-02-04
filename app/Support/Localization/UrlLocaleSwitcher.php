<?php

declare(strict_types=1);

namespace App\Support\Localization;

use Illuminate\Http\Request;

final class UrlLocaleSwitcher implements UrlLocaleSwitcherInterface
{
    public function __construct(
        private readonly Request $request,
    ) {}

    public function currentLocale(): string
    {
        return (string) app()->getLocale();
    }

    public function supported(): array
    {
        /** @var array<int,string> $supported */
        $supported = config('i18n.supported', []);
        return $supported;
    }

    public function root(string $locale): string
    {
        return $this->forLocale($locale, '/');
    }

    public function currentPathWithoutLocale(): string
    {
        $strategy = (string) config('i18n.strategy', 'prefix');
        $path = $this->request->getPathInfo();
        $path = $path === '' ? '/' : $path;

        if ($strategy !== 'prefix') {
            // In subdomain strategy, path has no locale prefix
            return $this->normalizePath($path);
        }

        return $this->stripLeadingLocale($this->normalizePath($path));
    }

    public function forLocale(string $locale, ?string $pathWithoutLocale = null): string
    {
        $supported = $this->supported();
        if (!in_array($locale, $supported, true)) {
            $locale = (string) config('i18n.default', 'uk');
        }

        $strategy = (string) config('i18n.strategy', 'prefix');

        $path = $pathWithoutLocale ?? $this->currentPathWithoutLocale();
        $path = $this->normalizePath($path);

        $query = $this->request->getQueryString();
        $queryPart = $query ? ('?' . $query) : '';

        if ($strategy === 'subdomain') {
            $host = $this->hostForLocale($locale);
            $scheme = $this->request->getScheme();
            return "{$scheme}://{$host}{$path}{$queryPart}";
        }

        // prefix
        $prefixed = $path === '/' ? "/{$locale}" : "/{$locale}{$path}";
        return url($prefixed) . $queryPart;
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        $path = preg_replace('#/+#', '/', $path) ?? $path; // collapse // -> /
        return $path === '' ? '/' : rtrim($path, '/') ?: '/';
    }

    private function stripLeadingLocale(string $path): string
    {
        $supported = $this->supported();
        $segments = explode('/', ltrim($path, '/')); // ['uk','about']
        $first = $segments[0] ?? '';

        if ($first !== '' && in_array($first, $supported, true)) {
            array_shift($segments);
        }

        $rest = '/' . implode('/', array_filter($segments, fn ($s) => $s !== ''));
        return $this->normalizePath($rest);
    }

    private function hostForLocale(string $locale): string
    {
        $map = (string) config('i18n.subdomain.domain_map', '');
        $parsed = $this->parseDomainMap($map);
        if (isset($parsed[$locale])) {
            return $parsed[$locale];
        }

        $base = (string) config('i18n.subdomain.base_domain', '');
        if ($base === '') {
            // fallback to current host (not ideal but avoids crashes)
            return (string) $this->request->getHost();
        }

        return "{$locale}." . ltrim($base, '.');
    }

    /**
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
