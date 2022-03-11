<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Midtrans\Config;
use App\Http\Controllers\Midtrans\CoreApi;

class MidtransController extends Controller
{
    public function createBCAPayment(Request $request){
        try{
            $transactionRequest = array(
                "payment_type" => "bank_transfer",
                "transaction_details" => [
                    "gross_amount" => 300000,
                    "order_id" => date('Y-m-dHis')
                ],
                "customer_details" => [
                    "email" => "muchsin.hisyam@gmail.com",
                    "first_name" => "Muchsin",
                    "last_name" => "Hisyam",
                    "phone" => "+6281 1234 1234"
                ],
                "item_details" => array([
                    "id" => "1388998298204",
                    "price" => 200000,
                    "quantity" => 1,
                    "name" => "Front-End Dev"
                ],[
                    "id" => "1388998228204",
                    "price" => 100000,
                    "quantity" => 1,
                    "name" => "UI/UX Design"
                ]),
                "bank_transfer" => [
                    "bank" => "bca",
                    "va_number" => "111111",
                ]
            );

            // Send the transaction request body to the CoreApi class - charge method
            $charge = CoreApi::charge($transactionRequest);
            if(!$charge){
                return response()->json(['code' => 0, 'message' => 'Error occured']);
            }
            return response()->json(['code' => 1, 'message' => 'Success', 'result' => $charge]);
        } catch (\Exception $e){
            // throw $e;
            dd($e);
            return response()->json(['code' => 1, 'message' => 'Success', 'result' => $charge]);
        }
    }

    public function createGoPayPayment(Request $request){
        try{
            $transactionRequest = array(
                "payment_type" => "gopay",
                "transaction_details" => [
                    "gross_amount" => 400000,
                    "order_id" => date('Y-m-dHis')
                ],
                "customer_details" => [
                    "email" => "muchsin.hisyam@gmail.com",
                    "first_name" => "Muchsin",
                    "last_name" => "Hisyam",
                    "phone" => "+6281 1234 1234"
                ],
                "item_details" => array([
                    "id" => "1388998298204",
                    "price" => 250000,
                    "quantity" => 1,
                    "name" => "Front-End Dev"
                ],[
                    "id" => "1388998228204",
                    "price" => 150000,
                    "quantity" => 1,
                    "name" => "UI/UX Design"
                ]),
                "gopay" => [
                    "enable_callback" => true,
                    "callback_url" => "",
                ]
            );

            // Send the transaction request body to the CoreApi class - charge method
            $charge = CoreApi::charge($transactionRequest);
            if(!$charge){
                return ['code' => 0, 'message' => 'Error occured'];
            }
            return ['code' => 1, 'message' => 'Success', 'result' => $charge];
        } catch (\Exception $e){
            // throw $e;
            dd($e);
            return ['code' => 1, 'message' => 'Success', 'result' => $charge];
        }
    }

    public function combinePayment(Request $request){
        return "Hello";
    }
}
