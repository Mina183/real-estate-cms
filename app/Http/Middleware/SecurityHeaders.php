<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Extract storage origin for CSP connect-src (allows direct browser-to-S3 uploads).
        // R2 presigned URLs use virtual-hosted style: https://{bucket}.{account}.r2.cloudflarestorage.com
        // so we need a wildcard subdomain (*.r2.cloudflarestorage.com) not just the endpoint host.
        $storageEndpoint = config('filesystems.disks.private.endpoint', '');
        $storageOrigin   = '';
        if ($storageEndpoint) {
            $parsed = parse_url($storageEndpoint);
            $scheme = $parsed['scheme'] ?? 'https';
            $host   = $parsed['host'] ?? '';
            $parts  = explode('.', $host);
            if (count($parts) > 2) {
                array_shift($parts); // strip first label to get parent domain
                $storageOrigin = $scheme . '://*.' . implode('.', $parts);
            } else {
                $storageOrigin = $scheme . '://' . $host;
            }
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com https://cdn.jsdelivr.net https://code.jquery.com https://cdn.tiny.cloud https://challenges.cloudflare.com; " .
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://cdn.jsdelivr.net https://cdn.tiny.cloud; " .
            "font-src 'self' https://fonts.bunny.net https://cdn.tiny.cloud data:; " .
            "img-src 'self' data: blob: https://cdn.tiny.cloud https://sp.tinymce.com; " .
            "connect-src 'self' https://cdn.tiny.cloud https://sp.tinymce.com https://challenges.cloudflare.com" . ($storageOrigin ? " $storageOrigin" : '') . "; " .
            "frame-src https://challenges.cloudflare.com; " .
            "frame-ancestors 'none';"
        );

        return $response;
    }
}
