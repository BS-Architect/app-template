<?php

declare(strict_types=1);

namespace App\Support\Localization;

enum LocaleSource: string
{
    case Explicit = 'explicit'; // route param or subdomain
    case Cookie   = 'cookie';
    case Header   = 'header';   // Accept-Language
    case Default  = 'default';
}
