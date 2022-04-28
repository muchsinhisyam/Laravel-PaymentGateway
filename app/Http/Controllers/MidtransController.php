<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

    public function combinePayment(Request $request, $customerData, $items, $total, $gopay_amount){
        try{
            $transactionRequest = array(
                "payment_type" => "gopay",
                "transaction_details" => [
                    // Gross amount = gopay balance because gopay need to paid first. 
                    // Means the total should be exact as gopay balance
                    "gross_amount" => $gopay_amount,
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
                ],
                "bank_transfer" => [
                    "bank" => "bca",
                    "va_number" => "111111",
                ]
            );

            // Set the item name as combination of all items name
            $itemsName = '';
            $loopIndex = 0;
            foreach($items['items'] as $item){
                if($loopIndex == 0){
                    $itemsName = $item['name'];
                }
                else{
                    $itemsName = $itemsName . ' + ' .$item['name'];
                }
                $loopIndex++;
            }
            $transactionRequest['item_details'] = array([
                "id" => "141334143143",
                "price" => $gopay_amount,
                "quantity" => 1,
                "name" => $itemsName
            ]);

            // Append the items array to the array of body request
            // foreach($items as $item){
            //     $transactionRequest['item_details'] = $item;
            // }

            // Make HTTP request of the API (POST method) with using Asynchronus of Go-Pay Payment 
            $gopayRequest = Http::async()->post('http://paymentgateway-laravel.test/gopay/payment', [$transactionRequest]);
            $gopayCharge = CoreApi::charge($transactionRequest);
            if(!$gopayCharge){
                return ['code' => 0, 'message' => 'Error occured'];
            }
            $gopayRequest->wait();

            // Change the payment method to BCA, and order_id in JSON request body for BCA Virtual Account HTTP request
            // To make it not conflict with previous JSON request (since order_id is unique, can't have same value)
            $transactionRequest['payment_type'] = "bank_transfer";
            $transactionRequest['transaction_details']['order_id'] = date('Y-m-dHis').'1';
            $totalSubtraction = $total - $gopay_amount;
            $transactionRequest['transaction_details']['gross_amount'] = $totalSubtraction;
            // Need to replace the Gopay item transaction to another item array
            $transactionRequest['item_details'] = array([
                "id" => "141334143143",
                "price" => $totalSubtraction,
                "quantity" => 1,
                "name" => $itemsName
            ]);
            
            // Make HTTP request of the API (POST method) with using Asynchronus of BCA VA Payment 
            $bcaRequest = Http::async()->post('http://paymentgateway-laravel.test/BCA/payment', [$transactionRequest]);
            $bcaCharge = CoreApi::charge($transactionRequest);
            if(!$bcaCharge){
                return ['code' => 0, 'message' => 'Error occured'];
            }

            // Convert JSON to array format
            $gopayResponse = json_decode(json_encode($gopayCharge), true);
            $bcaResponse = json_decode(json_encode($bcaCharge), true);
        
            return view('success', compact('gopayResponse', 'bcaResponse'));
            //return ['code' => 1, 'message' => 'Success','gopay_response' =>  $gopayCharge, 'bca_response' =>  $bcaCharge];
        }
        catch (\Exception $e){
            throw $e;
        }
    }

    public function combineOVOGopayPayment(Request $request, $customerData, $items, $total, $gopay_amount){
        try{
            $transactionRequest = array(
                "payment_type" => "gopay",
                "transaction_details" => [
                    // Gross amount = gopay balance because gopay need to paid first. 
                    // Means the total should be exact as gopay balance
                    "gross_amount" => $gopay_amount,
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
                ],
                "bank_transfer" => [
                    "bank" => "bca",
                    "va_number" => "111111",
                ]
            );

            // Set the item name as combination of all items name
            $itemsName = '';
            $loopIndex = 0;
            foreach($items['items'] as $item){
                if($loopIndex == 0){
                    $itemsName = $item['name'];
                }
                else{
                    $itemsName = $itemsName . ' + ' .$item['name'];
                }
                $loopIndex++;
            }
            $transactionRequest['item_details'] = array([
                "id" => "141334143143",
                "price" => $gopay_amount,
                "quantity" => 1,
                "name" => $itemsName
            ]);

            // Append the items array to the array of body request
            // foreach($items as $item){
            //     $transactionRequest['item_details'] = $item;
            // }

            // Make HTTP request of the API (POST method) with using Asynchronus of Go-Pay Payment 
            $gopayRequest = Http::async()->post('http://paymentgateway-laravel.test/gopay/payment', [$transactionRequest]);
            $gopayCharge = CoreApi::charge($transactionRequest);
            if(!$gopayCharge){
                return ['code' => 0, 'message' => 'Error occured'];
            }
            $gopayRequest->wait();

            // Change the payment method to BCA, and order_id in JSON request body for BCA Virtual Account HTTP request
            // To make it not conflict with previous JSON request (since order_id is unique, can't have same value)
            $transactionRequest['payment_type'] = "bank_transfer";
            $transactionRequest['transaction_details']['order_id'] = date('Y-m-dHis').'1';
            $totalSubtraction = $total - $gopay_amount;
            $transactionRequest['transaction_details']['gross_amount'] = $totalSubtraction;
            // Need to replace the Gopay item transaction to another item array
            $transactionRequest['item_details'] = array([
                "id" => "141334143143",
                "price" => $totalSubtraction,
                "quantity" => 1,
                "name" => $itemsName
            ]);
            
            // Make HTTP request of the API (POST method) with using Asynchronus of BCA VA Payment 
            $xendit = new XenditController;
            $ovoCharge = $xendit->createOVOPayment($request, $customerData, $items, $totalSubtraction);

            // Convert JSON to array format
            $gopayResponse = json_decode(json_encode($gopayCharge), true);
            $ovoResponse = json_decode(json_encode($ovoCharge), true);
        
            return view('success-combine', compact('gopayResponse', 'ovoResponse'));
            //return ['code' => 1, 'message' => 'Success','gopay_response' =>  $gopayCharge, 'bca_response' =>  $bcaCharge];
        }
        catch (\Exception $e){
            throw $e;
        }
    }
}
