<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserManage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $requestuest, Closure $next): Response
    {
        if(auth()->user() && auth()->user()->status == 1){
            return $next($requestuest);
        }
        return response()->json(['message'=>'Sorry You Are Temporary Disable by Admin']);
    }
}
