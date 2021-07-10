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
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function token(Request $request){

        print_r($request);

        // print_r($request);
        // $encode = base64_encode("e3ba99dd-d8a3-40ed-b240-89add8542572:5a83dbec-4ebe-4d51-9c84-7d4a91804283");
        // echo $encode;
        // echo '<br>';
        // $decode = base64_decode($encode);
        // echo $decode;
    }
}
