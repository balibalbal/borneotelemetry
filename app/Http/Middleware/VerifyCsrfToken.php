<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'modem/alarm',
        'modem/information',
        'modem/traccar',
        'mobile/traccar',
        'modem/transmission',
        'modem/check-imei', 
    ];
}
