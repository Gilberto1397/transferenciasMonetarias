<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckQueueDriver
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('queue.default') === 'sync') {
            return response()->json(
                ['message' => 'Fila assíncrona não configurada', 'error' => true],
                500
            );
        }
        return $next($request);
    }
}
