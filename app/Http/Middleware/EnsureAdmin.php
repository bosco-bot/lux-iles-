<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Vérifie que l'utilisateur connecté est administrateur.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentification requise.',
                ], 401);
            }

            return redirect()->guest(route('login'));
        }

        if (! $user->is_admin) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès réservé aux administrateurs.',
                ], 403);
            }

            return redirect()
                ->route('espace-client.index')
                ->with('error', 'Accès refusé. Cette section est réservée aux administrateurs LUXÎLES.');
        }

        return $next($request);
    }
}
