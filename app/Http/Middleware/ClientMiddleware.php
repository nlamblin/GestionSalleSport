<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next)
    {
        $user = User::getUser(Auth::user()->id_utilisateur);

        if($user->estClient() || $user->estEmploye() || $user->estAdmin()) {
            return $next($request);
        }

        throw new AuthorizationException();
    }
}
