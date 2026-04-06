<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     *　HTPPS
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        // クリックジャッキング
        $response->headers->set('X-Frame-Options', 'DENY');
        // MIMEスニッフィング
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // URL情報漏洩
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // CSP（Content Security Policy）
        $csp = "default-src 'self'; "
            . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.bunny.net https://fonts.googleapis.com http://localhost:5173 https://maps.googleapis.com; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5173 https://cdn.jsdelivr.net http://unpkg.com https://maps.googleapis.com https://maps.gstatic.com; "
            . "img-src 'self' data: blob: https://maps.googleapis.com https://maps.gstatic.com https:; "
            . "font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com data:; "
            . "connect-src 'self' ws://localhost:5173 http://localhost:5173 https://cdn.jsdelivr.net https://maps.googleapis.com; "
            . "frame-src https://www.google.com https://maps.googleapis.com; "
            . "frame-ancestors 'none';";

        $response->headers->set('Content-Security-Policy', $csp);
        return $response;
    }
}
