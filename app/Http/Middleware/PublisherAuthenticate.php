<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublisherAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // To check that user is authenticated and user role shoule be 1(Publisher)
        if(auth()->user() && auth()->user()->role == 1){
            return $next($request);
        }
        return response()->json(['message'=>'You Are Not Elligible To Access This Resource'],403);
    }
}
