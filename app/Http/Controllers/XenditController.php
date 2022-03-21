<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Xendit\Xendit;

class XenditController extends Controller
{
    private $token = 'xnd_development_aMivdv4S87OLFAVortC0wQftkvIOGzR1w6h3FNnJwvoBdQxBwaX5B6VV5uwFiD';

    public function createOVOPayment(Request $request, $customerData, $items, $total){
        try{
            Xendit::setApiKey($this->token);
            $ewalletChargeParams = [
                'reference_id' => 'test-reference-id',
                'currency' => 'IDR',
                'amount' => (int)$total,
                'checkout_method' => 'ONE_TIME_PAYMENT',
                'channel_code' => 'ID_OVO',
                'channel_properties' => [
                    'mobile_number' => $customerData["phone"],
                    'success_redirect_url' => 'http://paymentgateway-laravel.test/',
                ],
                'metadata' => [
                    'meta' => 'data'
                ]
            ];
            
            $createEWalletCharge = \Xendit\EWallets::createEWalletCharge($ewalletChargeParams);
            //var_dump($createEWalletCharge);
    
            if(!$createEWalletCharge){
                return response()->json(['code' => 0, 'message' => 'Error occured']);
            }
            return response()->json(['code' => 1, 'message' => 'Success', 'result' => $createEWalletCharge]);
        } catch (\Exception $e){
            throw $e;
            return response()->json(['code' => 1, 'message' => 'Success', 'result' => $createEWalletCharge]);
        }   
    }
}
