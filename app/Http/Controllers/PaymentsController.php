<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
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
        $PaidAmount = intVal($request->json('PaidAmount'));
        // Ambil transaski di database;

        $IDTr = DB::select(
            "SELECT tp.IDTransaksiPesanan, Nama nama
            FROM transaksipesanan tp 
            WHERE tp.StatusPesanan = 'PENDING'
            AND tp.TglExpired > NOW()
            AND tp.CaraBayar = 'vabca' 
            AND tp.IDVirtualAccountBank  = '$NoVirtualAccount'
            AND tp.Nominal = $PaidAmount
            ");


        $Success = false;
        if($IDTr){
            $IDTRansaksiPesanan = $IDTr[0]->IDTransaksiPesanan;
            //DB::beginTransaction();
    			// set pembayaran diterima, tandai statuspesanan PAID dan tglPembayaran
            $update = DB::update("
                UPDATE transaksipesanan 
                SET StatusPesanan = 'PAID', 
                    TglPembayaran = NOW()
                WHERE IDTRansaksiPesanan = ?
            ",[$IDTRansaksiPesanan]);
            if($update) $Success = true;

        }

        
        $PaymentFlagStatus = "01"; // error
        $PaymentFlagReasonID = "Gagal";
        $PaymentFlagReasonEN = "Failed";
        $CustomerName = "";
        $TotalAmmount = "0.00";
        if($Success) {
            $PaymentFlagStatus = "00"; //berhasil
            $PaymentFlagReasonID = "Sukses";
            $PaymentFlagReasonEN = "Success";
            $CustomerName = $IDTr[0]->nama;
            $TotalAmmount = number_format($PaidAmount,2,".","");
        }

        $CustomerName = $CustomerName ." - THEFAVORED-ONE";

        

        $response = [
                "CompanyCode" => $CompanyCode,
                "CustomerNumber" => $CustomerNumber,
                "RequestID" => $RequestID,
                "PaymentFlagStatus" => $PaymentFlagStatus,
                "PaymentFlagReason" => [
                "Indonesian" => $PaymentFlagReasonID,
                "English" => $PaymentFlagReasonEN
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

        // print_r($request->json()->all());
        // $request_array = $request->json()->all();
        // $body_canonical = json_encode(array_map('trim',$request_array));
        // echo json_encode($request_array);
        // echo json_encode($request->input());
        // return response()->json(['user' => Auth::user()], 200);

    }
}
