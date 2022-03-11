<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Midtrans\Config;
use App\Http\Controllers\Midtrans\CoreApi;
use App\Models\CustomerDetail;

class MidtransController extends Controller
{
    public function createBCAPayment(Request $request, $customerData, $items, $total){
        //return ($customerData);
        try{
            $transactionRequest = array(
                "payment_type" => "bank_transfer",
                "transaction_details" => [
                    "gross_amount" => $total,
                    "order_id" => date('Y-m-dHis')
                ],
                "customer_details" => [
                    "first_name" => $customerData["fname"],
                    "last_name" =>  $customerData["lname"],
                    "phone" => $customerData["phone"],
                    "email" => $customerData["email"]
                ],
                // "item_details" => array([
                //     "id" => "1388998298204",
                //     "price" => 200000,
                //     "quantity" => 1,
                //     "name" => "Front-End Dev"
                // ],[
                //     "id" => "1388998228204",
                //     "price" => 100000,
                //     "quantity" => 1,
                //     "name" => "UI/UX Design"
                // ]),
                "item_details" => array(),
                "bank_transfer" => [
                    "bank" => "bca",
                    "va_number" => "111111",
                ]
            );

            // Append the items array to the array of body request
            foreach($items as $item){
                $transactionRequest['item_details'] = $item;
            }

            // Send the transaction request body to the CoreApi class - charge method
            $charge = CoreApi::charge($transactionRequest);
            if(!$charge){
                return response()->json(['code' => 0, 'message' => 'Error occured']);
            }
            return response()->json(['code' => 1, 'message' => 'Success', 'result' => $charge]);
        } catch (\Exception $e){
            throw $e;
            return response()->json(['code' => 1, 'message' => 'Success', 'result' => $charge]);
        }
    }

    public function createGoPayPayment(Request $request, $customerData, $items, $total){
        try{
            $transactionRequest = array(
                "payment_type" => "gopay",
                "transaction_details" => [
                    "gross_amount" => $total,
                    "order_id" => date('Y-m-dHis')
                ],
                "customer_details" => [
                    "first_name" => $customerData["fname"],
                    "last_name" =>  $customerData["lname"],
                    "phone" => $customerData["phone"],
                    "email" => $customerData["email"]
                ],
                // "item_details" => array([
                //     "id" => "1388998298204",
                //     "price" => 200000,
                //     "quantity" => 1,
                //     "name" => "Front-End Dev"
                // ],[
                //     "id" => "1388998228204",
                //     "price" => 100000,
                //     "quantity" => 1,
                //     "name" => "UI/UX Design"
                // ]),
                "item_details" => array(),
                "gopay" => [
                    "enable_callback" => true,
                    "callback_url" => "",
                ]
            );

            // Append the items array to the array of body request
            foreach($items as $item){
                $transactionRequest['item_details'] = $item;
            }

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

    public function combinePayment(Request $request, $customerData){
        return "Hello";
    }
}
