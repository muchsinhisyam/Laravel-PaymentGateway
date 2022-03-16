<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

use App\Models\CustomerDetail;
use App\Models\ItemDetail;

class HomeController extends Controller
{
    public function homePage(Request $request){
        // Set data customer for payment
        $data = new CustomerDetail();
        $data->fname = "Muchsin";
        $data->lname = "Hisyam";
        $data->phone = "081231239821";
        $data->email = "muchsin.hisyam@gmail.com";

        // Set item for payment
        $items = array(
            "items" => array(
            [
                "id" => "123",
                "name" => "Front-End Dev",
                "price" => 200000,
                "quantity" => 1
            ],
            [
                "id" => "312",
                "name" => "UI/UX Design",
                "price" => 100000,
                "quantity" => 1
            ]),
        );

        // Get the data from API using GET method
        $response  = Http::get('http://paymentgateway-laravel.test/api/gopay/get-balance/1');

        // Convert JSON format to Array, but it cannot be fetch by $array->variable or $array["variable"]
        $gopay_data = json_decode($response);
        // Since $gopay_data still array of array so use $array[0]->variable
        $balance = $gopay_data[0]->balance;

        // Return the view and relay the data to the view
        return view('index', compact('data', 'items', 'balance'));
    }
}
