<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperadminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // To check that user is authenticated and user role shoule be 2(Super-admin)
        if(auth()->user() && auth()->user()->role == 2){
            return $next($request);
        }
        return response()->json(['message'=>'You Are Not Elligible To Access This Resource'],403);
    }
}
