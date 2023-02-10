<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiAuthController extends Controller
{
    
    function __construct(){
        //$this->middleware('auth:api')->only(['logout', 'getData']);
        $this->middleware('token')->only(['getData']);
    } 
   
    public function index()
    {
        //
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

    
    public function show($id)
    {
        //
    }

    
    public function edit($id)
    {
        //
    }

   
    public function update(Request $request, $id)
    {
        //
    }

    
    public function destroy($id)
    {
        //
    }
    
    
    // function login(Request $request) {
         
    //     $credentials = request(['email', 'password']);
    //         if (!Auth::attempt($credentials)) {
    //             return response()->json(['message' => 'Unauthorized'], 401);
    //     }
    //     $user = Auth::user();
    //     $tokenResult = $user->createToken('Access Token');
    //     $token = $tokenResult->token;
    //     $token->save();
    //     return response()->json([
    //         'access_token' => $tokenResult->accessToken,
    //         'token_type' => 'Bearer',
    //         'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
    //     ], 200);
    // }
    
    function logout(Request $request) {
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json(['message' => 'Logged out']);
    }
    
    function getData(Request $request){
        $lat = '37.16147109102704';
        $lng = '-3.5912354132361344';
        
        $date = Carbon::now()->format('Y-m-d');
        $url = sprintf('https://api.sunrise-sunset.org/json?lat=%s&lng=%s&date=%s', $lat, $lng, $date);
        
        $response = Http::get($url);
        
        $sunData = $response->json();
        
        $sunset = $sunData['results']['sunset'];
        $sunrise = $sunData['results']['sunrise'];
        $now = Carbon::now()->format('h:i:s A');
        
        
        
        
        //Coseno
        $diff = abs(strtotime($now) - strtotime($sunrise))/abs(strtotime($sunset) - strtotime($sunrise));
        
        $coseno = cos($diff);
        
        //Seno
        
        $seno = sin($diff);
        
        return response()->json(['cos' => $coseno, 'sen' => $seno, 'sensor1' => rand(0, 100) / 100,'sensor2' => rand(0, 100) / 100,'sensor3' => rand(0, 100) / 100,'sensor4' => rand(0, 100) / 100]);
    }
    
    
    
    function jwt(Request $request){
        
        $user = $request->input('user');
        $password = $request->input('password');
        
        if($user == 'admi@admin.es' && $password == '12345678'){
        
            $key = env('PRIVATE_KEY'); //Clave para cifrar y descifrar
            $payload = [
                'user' => 'admi@admin.es',
                'expiration_date' => Carbon::now()->addHour(),
             ];
            $jwt = JWT::encode($payload, $key, 'HS256');
            return response()->json(['token' => $jwt]);
        }
        return response()->json(['message' => 'User not valid'], 401);
    }
    
    function decode(Request $request){
       
        $key = env('PRIVATE_KEY');
        
        $token = $request->bearerToken();

        //$token = $request->input('token'); //Authorization Header, middleware, Bearer
        if($token){
            try{
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                if(!Carbon::now()->gt($decoded->expiration_date)){
                    return response()->json(['message' => 'Private data:' . $decoded->user,
                                                'expired' => $decoded->expiration_date]);
                }else{
                    return response()->json(['message' => 'Exprired token'], 401);
                }
                
            }catch(\Exception){
                
            }
            
        }
        return response()->json(['message' => 'Not authorized'], 401);
    }
    
    
    
    
    
    
    
    
    
    
}
