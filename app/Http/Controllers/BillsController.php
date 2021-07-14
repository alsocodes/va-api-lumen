<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
      
      $CompanyCode = $request->json('CompanyCode');
      $CustomerNumber = $request->json('CustomerNumber');
      $ChannelType = $request->json('ChannelType');
      $RequestID = $request->json('RequestID');
      $TransactionDate = $request->json('TransactionDate');
      $NoVirtualAccount = $CompanyCode.$CustomerNumber;
      // Ambil transaski di database;

      $transaksi = DB::select(
         "SELECT CONCAT(tp.IDTransaksiPesanan, ' (THEFAVORED-ONE)') no_transaksi, Nama nama, Nominal total_ammount 
         FROM transaksipesanan tp 
         WHERE tp.StatusPesanan = 'PENDING'
         AND tp.TglExpired > NOW()
         AND tp.CaraBayar = 'vabca' 
         AND tp.IDVirtualAccountBank  = '$NoVirtualAccount'
         ");

      $InquiryStatus = "01"; // error
      $InquiryReasonID = "Gagal";
      $InquiryReasonEN = "Failed";
      $CustomerName = "";
      $TotalAmmount = "0.00";
      if($transaksi) {
         $InquiryStatus = "00"; //berhasil
         $InquiryReasonID = "Sukses";
         $InquiryReasonEN = "Success";
         $CustomerName = $transaksi[0]->nama;
         $TotalAmmount = number_format($transaksi[0]->total_ammount,2,".","");
      }

      $CustomerName = $CustomerName ." - THEFAVORED-ONE";

      $response = [
            "CompanyCode" => $CompanyCode,
            "CustomerNumber" => $CustomerNumber,
            "RequestID" => $RequestID,
            "InquiryStatus" => $InquiryStatus,
            "InquiryReason" => [
               "Indonesian" => $InquiryReasonID,
               "English" => $InquiryReasonEN
            ],
            "CustomerName" => substr($CustomerName, 0, 30),
            "CurrencyCode" => "IDR",
            "PaidAmount"=> $TotalAmmount,
            "TotalAmount" => $TotalAmmount,
            "TransactionDate"=> $TransactionDate,
            "DetailBills"=> [],
            "FreeTexts"=> [
               [
                  "Indonesian"=> "Free Text 1",
                  "English"=> "Free Text 1"
               ],
            ],
            "AdditionalData"=> ""
        ];
      return response()->json($response,200);

        // echo "sudah melewati pengecekan access_token dan hmac, selanjutnya query detail transaksi";
      //   return response()->json($dummyResponse, 200);

        // print_r($request->json()->all());
        // $request_array = $request->json()->all();
        // $body_canonical = json_encode(array_map('trim',$request_array));
        // echo json_encode($request_array);
        // echo json_encode($request->input());
        // return response()->json(['user' => Auth::user()], 200);

    }
}
