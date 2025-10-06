<?php

namespace App\Services;

use App\Mail\NotifyEmail;
use Illuminate\Support\Facades\Mail;

class EmailServices
{
    public function sendJobFinished($to, $mensaje)
    {
        Mail::to($to)->send(new NotifyEmail($mensaje));
    }

}