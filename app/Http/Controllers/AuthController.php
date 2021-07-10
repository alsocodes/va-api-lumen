<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
     

        //validate incoming request 
        // print_r($request->input());
        // exit;
        $this->validate($request, [
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        $credentials = $request->only(['client_id', 'client_secret']);
        // $credentials = ['client_id' => $request->input('client_id'),'client_id' => $request->input('client_secret')];

        // print_r(Auth::attempt($credentials));
        $x = Auth::attempt($credentials);
        var_dump($x);
        // if (! $token = Auth::attempt($credentials)) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }

        // return $this->respondWithToken($token);
    }

    public function token(Request $request){

        $auth_string    = $request->header('authorization');
        $auth_prefix    = explode(" ",$auth_string)[0];
        $auth_encode    = explode(" ",$auth_string)[1];
        $auth_decode    = base64_decode($auth_encode);
        $client_id      = explode(":", $auth_decode)[0];
        $client_secret  = explode(":", $auth_decode)[1];

        $grant_type = $request->input('grant_type');

        if($auth_prefix !== 'Basic'){
            $response = [
                "error_code" => "ESB-14-007",
                "error_message" => [
                  "indonesian" => "Timeout",
                  "english"=> "Timeout"
                ]
            ];

            return response()->json($response,504);
        }

        if($grant_type !== 'client_credentials') {
            $response = [
                'error_code'=> "ESB-14-008",
                "error_message"=> [
                    "indonesian"=> "client_id/client_secret/grant_type tidak valid",
                    "english"=> "Invalid client_id/client_secret/grant_type"
                ]
            ];
            
            return response()->json($response,401);
        }

        $credentials = ['client_id' => $client_id, 'client_secret' => $client_secret];

        if(!$token = Auth::attempt($credentials)){
            $response = [
                'error_code'=> "ESB-14-008",
                "error_message"=> [
                    "indonesian"=> "client_id/client_secret/grant_type tidak valid",
                    "english"=> "Invalid client_id/client_secret/grant_type"
                ]
            ];
            return response()->json($response,401);
        }
        
        $response = [
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => Auth::factory()->getTTL() * 60,
            'scope'         => 'resource.WRITE resource.READ'
        ];
        return response()->json($response,200);
        
        // echo 'client_id : '.$client_id;
        // echo 'client_secret : '.$client_secret;

        // print_r($request);
        // $encode = base64_encode("e3ba99dd-d8a3-40ed-b240-89add8542572:5a83dbec-4ebe-4d51-9c84-7d4a91804283");
        // echo $encode;
        // echo '<br>';
        // $decode = base64_decode($encode);
        // echo $decode;
    }

    public function signature(){
        $api_secret = 'e1376d34-8892-42ed-853e-7b37d4f6d4ae';
        $http_method = 'POST';
        $relative_url = '/va/bills';
        $access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6OTAwMFwvYXBpXC9vYXV0aFwvdG9rZW4iLCJpYXQiOjE2MjU5MDQ5MTMsImV4cCI6MTYyNTkwODUxMywibmJmIjoxNjI1OTA0OTEzLCJqdGkiOiJrMEFzbUlCaE5uUTZkS3J2Iiwic3ViIjoxLCJwcnYiOiJkOGUwMDFjODhkMTdiNDU2YjJiZjRjMzdkOGNlMzljNTJhOTI4OGI2In0.xCSHTJYiyzxxl2Nu87PdfQqexW-pFplCC3tCTRMQeWI';
        $time_stamp = '2021-07-10T13:00:00.234Z';
        $request_body = '{"CompanyCode":"13559","CustomerNumber":"123456789","RequestID":"201507131507262221400000001975","ChannelType":"6014","TransactionDate":"10\/07\/2021 10:07:40","AdditionalData":"lalala"}';
        
        $hash_body = strtolower(hash('sha256', $request_body));
        $string_to_sign = $http_method.":".$relative_url.":".$access_token.":".$hash_body.":".$time_stamp;
        $signature = hash_hmac('sha256', $string_to_sign, $api_secret);

        echo $signature;

        //hash('sha256',$string)
        //str2hex($string)

        // Signature = HMAC-SHA256(apiSecret, StringToSign)
        // StringToSign = HTTPMethod+":"+RelativeUrl+":"+AccessToken+":"+Lowercase(HexEncode(SHA-256(RequestBody)))+":"+Timestamp

    }
}
