<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        "/*"
    ];
        //allow subdomain can access
        // "http://my-domain1.me/*",
        // "http://my-domain2.me/*",
        // "http://my-domain3.me/*",
        // "tenancy2.test.com/*",
        // "tenancy1.test.com/*",
        // "the-seagate.test.com/*",
}
