<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function sendEmail($data)
    {
    	Mail::send('emails.'.$data['template'], $data, function ($message) use ($data) {
            $message->from('venkatraman858@gmail.com','Kings App');
            $message->subject($data['mail_subject']);
            $message->to($data['to']);
        });
        if (Mail::failures()) {
            return false;
        }else{
            return true;
        }
    }
}
