<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Request;
use Mail;

use App\Http\Requests;

class MailController extends Controller
{
    //

    public function index(){
        return response()->json(["message" => "great succcess"]);
    }

    public function basic_email() {
        $data = array('name'=>"Virat Gandhi");
     
        Mail::send(['text'=>'mail'], $data, function($message) {
           $message->to('abc@gmail.com', 'Tutorials Point')->subject
              ('Laravel Basic Testing Mail');
           $message->from('ebubeemeka19@gmail.com','Virat Gandhi');
        });
        return response()->json(["messgages"=>"Basic Email Sent. Check your inbox."]) ;
     }
}
