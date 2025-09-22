<?php

namespace App\Http\Middleware;

use Closure;

class RateLimitByKey
{
    public function handle($request, Closure $next){
        $key = $request->header('X-Plugin-Key') ?? $request->ip();
        $max = 100; // per hour
        $count = cache()->increment("rate:{$key}");
        // if($count === 1) cache()->put("rate:{$key}:ts", now(), 3600);
        // if($count > $max){
        //     return response()->json(['error'=>'rate_exceeded'],429);
        // }
        return $next($request);
    }
};