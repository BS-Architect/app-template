<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\Localization\PreferredLocaleResolver;
use App\Support\Localization\UrlLocaleSwitcherInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocaleRootRedirectController
{
    public function __construct(
        private readonly PreferredLocaleResolver $resolver,
        private readonly UrlLocaleSwitcherInterface $switcher,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        [$locale] = $this->resolver->resolve($request);

        $lastPathCookie = (string) config('i18n.cookie.last_path_name', 'last_path');
        $last = $request->cookie($lastPathCookie);

        $path = $this->sanitizeLastPath(is_string($last) ? $last : null);

        return redirect()->to($this->switcher->forLocale($locale, $path));
    }

    /**
     * Prevent open-redirect and ensure only local path.
     * Accepts "/" or "/something" (optionally with query).
     */
    private function sanitizeLastPath(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return '/';
        }

        // reject absolute URLs
        if (preg_match('#^https?://#i', $value)) {
            return '/';
        }

        // ensure starts with "/" (path?query)
        if ($value[0] !== '/') {
            return '/';
        }

        // collapse multiple slashes
        $value = preg_replace('#/+#', '/', $value) ?? $value;

        // avoid redirecting to locale roots already like "/uk/..."
        // because forLocale() will add locale again in prefix strategy.
        return $value;
    }
}
