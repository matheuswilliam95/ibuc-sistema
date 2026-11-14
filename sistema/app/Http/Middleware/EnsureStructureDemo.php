<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureStructureDemo
{
    public function handle(Request $request, Closure $next): Response
    {
        $connection = config('database.default');
        abort_unless(
            app()->environment(['local', 'testing'])
            && config('structure.demo_enabled')
            && in_array($request->ip(), ['127.0.0.1', '::1'], true)
            && config("database.connections.$connection.driver") === 'pgsql'
            && in_array(config("database.connections.$connection.host"), ['127.0.0.1', '::1', 'localhost'], true)
            && in_array(config("database.connections.$connection.url"), [null, ''], true)
            && in_array(config("database.connections.$connection.database"), ['ibuc_structure_demo', 'ibuc_structure_test'], true),
            404,
        );

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
