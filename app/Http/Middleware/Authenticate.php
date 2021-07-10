<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        
        if ($this->auth->guard($guard)->guest()) {
            return response('Unauthorized.', 401);
        }

        // $api_secret     = $this->auth->user()->getApiSecret(); dari db
        $api_secret     = env('X_BCA_API_KEY') === $request->header('X-BCA-Key') ? env('X_BCA_API_SECRET') : '';
        $http_method    = $request->method();
        $access_token   = $request->bearerToken();
        $request_body   = json_encode($request->json()->all());
        $hash_body      = strtolower(hash('sha256', $request_body));
        $time_stamp     = $request->header('X-BCA-Timestamp');
        $xbca_signature = $request->header('X-BCA-Signature');
        $relative_url   = str_replace(url('/'), "", $request->url());
        
        $string_to_sign = $http_method.":".$relative_url.":".$access_token.":".$hash_body.":".$time_stamp;
        $calc_signature = hash_hmac('sha256', $string_to_sign, $api_secret);

        if($xbca_signature !== $calc_signature){
            return response()->json([
                'errorCode' => '...',
                'errorMessage'=>[
                    'Indonesian' => 'HMAC tidak cocok',
                    'English' => 'HMAC mismatch'
                ]
            ], 400);
        }

        // echo $xbca_signature;
        
        // echo $calc_signature;
        

        // $relative_url = '/va/bills';
        

        
        
        return $next($request);
    }
}
