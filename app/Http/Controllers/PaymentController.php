<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MidtransController;

class PaymentController extends Controller
{
    public function redirectPayments(Request $request){
        try{
            // Get data from Radio button of payment form
            $payment = $request['payment_method'];
            // Initiate Midtrans controller to call their function
            $midtrans = new MidtransController;
            if($payment == 'bca'){
                return $midtrans->createBCAPayment($request);
            }
            if($payment == 'gopay'){
                return $midtrans->createGoPayPayment($request);
            }
        } catch (\Exception $e){
            // throw $e;
            dd($e);
            return ['code' => 1, 'message' => $e, 'result' => $charge];
        }
    }
}
