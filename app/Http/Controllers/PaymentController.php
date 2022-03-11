<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MidtransController;
use App\Models\CustomerDetail;

class PaymentController extends Controller
{
    public function redirectPayments(Request $request){
        try{
            $validatedData  =  $request->validate([
                'payment_method'  => 'required',
            ]);

            // Get data from Radio button of payment form
            $reqData = $request->all();
            // Parse the string to json array
            $customerData = json_decode($reqData['data'], true);
            $items = json_decode($reqData['items'], true);
            $total = $reqData['total'];
            $payment = $reqData['payment_method'];
            $gopay_amount = $reqData['gopay_amount'];

            // Initiate Midtrans controller to call their function
            $midtrans = new MidtransController;
            if($payment == 'bca'){
                return $midtrans->createBCAPayment($request, $customerData, $items, $total);
            }
            if($payment == 'gopay'){
                return $midtrans->createGoPayPayment($request, $customerData, $items, $total);
            }
            if($payment == 'combine' && $gopay_amount != null){
                return $midtrans->combinePayment($request, $customerData);
            }
            else{
                return redirect('/')->with('error', 'You need to fill gopay amount!');
            }
        } catch (\Exception $e){
            throw $e;
            // dd($e);
            return ['code' => 1, 'message' => $e];
        }
    }
}
