<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Gopay;
use App\Http\Resources\GopayResource;

class GopayController extends Controller
{
    public function get_balance($id)
    {
        $data = Gopay::find($id);
        if (is_null($data)) {
            return response()->json('Data not found', 404); 
        }
        return response()->json([new GopayResource($data)]);
    }

    public function update_balance(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'balance' => 'required|integer'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $data = Gopay::find($id);
        if (is_null($data)) {
            return response()->json('Data not found', 404); 
        }

        $data->balance = $request->balance;
        $data->save();
        
        return response()->json(['Gopay balance updated successfully.', new GopayResource($data)]);
    }

}
