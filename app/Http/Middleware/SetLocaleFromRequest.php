<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Localization\PreferredLocaleResolver;
use App\Support\Localization\LocaleSource;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetLocaleFromRequest
{
    public function __construct(
        private readonly PreferredLocaleResolver $resolver,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        [$locale, $source] = $this->resolver->resolve($request);

        app()->setLocale($locale);

        /** @var Response $response */
        $response = $next($request);

        return $this->persistLocaleIfNeeded($request, $response, $locale, $source);
    }

    private function persistLocaleIfNeeded(Request $request, Response $response, string $locale, LocaleSource $source): Response
    {
        if (!in_array($source, [LocaleSource::Explicit, LocaleSource::Header], true)) {
            return $response;
        }

        $cookieName = (string) config('i18n.cookie.locale_name', 'locale');
        $minutes = (int) config('i18n.cookie.locale_minutes', 525600);

        $response->headers->setCookie(cookie(
            name: $cookieName,
            value: $locale,
            minutes: $minutes,
        ));

        return $response;
    }
}
