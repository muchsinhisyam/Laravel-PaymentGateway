<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        // Return the view and relay the data to the view
        return view('index', compact('data', 'items'));
    }
}
