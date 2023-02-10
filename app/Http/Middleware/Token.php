<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;

class Token
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $key = env('PRIVATE_KEY');
        
        $token = $request->bearerToken();

        if($token){
            try{
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                if(!Carbon::now()->gt($decoded->expiration_date)){
                     return $next($request);
                }else{
                    return response()->json(['message' => 'Exprired token'], 401);
                }
                
            }catch(\Exception){
                
            }
            
        }
        return response()->json(['message' => 'Not authorized'], 401);
    }
}
