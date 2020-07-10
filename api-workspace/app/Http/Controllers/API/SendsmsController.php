<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Nexmo\Laravel\Facade\Nexmo;

class SendsmsController extends Controller
{

    /*
    author:Phanith 
    created:16-01-2020
    */
    public function sendSms(Request $request)
    {
        csrf_field();
        // return $request;
      $message =   Nexmo::message()->send([
            'to' => '855' . $request->mobile,
            'from' => '855' . $request->from,
            'text' => $request->text,

        ]);
        if( $message == true){
            return 'successfully';
        }else{
            return 'failed';
        }
    }
}
