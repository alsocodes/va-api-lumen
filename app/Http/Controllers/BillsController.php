<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BillsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

   
    public function index(Request $request)
    {
        echo "sudah melewati pengecekan access_token dan hmac, selanjutnya query detail transaksi";

        // print_r($request->json()->all());
        // $request_array = $request->json()->all();
        // $body_canonical = json_encode(array_map('trim',$request_array));
        // echo json_encode($request_array);
        // echo json_encode($request->input());
        // return response()->json(['user' => Auth::user()], 200);

    }
}
