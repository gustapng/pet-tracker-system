<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-API-KEY');

        if (!$token || $token !== env('PET_TRACKER_API_KEY')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acesso não autorizado. Chave de API inválida ou ausente.'
            ], 401);
        }

        return $next($request);
    }
}
