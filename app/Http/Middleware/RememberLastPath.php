<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Localization\UrlLocaleSwitcherInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RememberLastPath
{
    public function __construct(
        private readonly UrlLocaleSwitcherInterface $switcher,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (!$this->shouldRemember($request, $response)) {
            return $response;
        }

        $path = $this->switcher->currentPathWithoutLocale(); // "/" or "/about"
        $query = $request->getQueryString();
        $value = $query ? ($path . '?' . $query) : $path;

        $cookieName = (string) config('i18n.cookie.last_path_name', 'last_path');
        $minutes = (int) config('i18n.cookie.last_path_minutes', 43200);

        $response->headers->setCookie(cookie(
            name: $cookieName,
            value: $value,
            minutes: $minutes,
        ));

        return $response;
    }

    private function shouldRemember(Request $request, Response $response): bool
    {
        if (!$request->isMethod('GET')) return false;
        if ($request->expectsJson()) return false;

        // Remember only successful HTML pages
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) return false;

        // Do not remember assets or internal endpoints
        $path = $request->getPathInfo();
        if (preg_match('#\.(css|js|map|png|jpg|jpeg|webp|gif|svg|ico|txt|xml)$#i', $path)) return false;

        return true;
    }
}
