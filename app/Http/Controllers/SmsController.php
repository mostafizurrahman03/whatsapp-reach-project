<?php

// namespace App\Http\Controllers;

// class SmsController extends Controller
// {
//     public function send(Request $request,SmsSenderService $sms)
//     {
//         $request->validate(['to'=>'required','message'=>'required']);
//         $client = $request->client;

//         $numbers = is_array($request->to) ? $request->to : [$request->to];
//         $responses = $sms->sendBulk($client->client_api_key,$client->client_secret_key,'sms',$numbers,$request->message);

//         // deduct balance example
//         $cost = count($numbers)*$client->rate_per_sms;
//         $client->decrement('balance',$cost);

//         return response()->json($responses);
//     }
// }


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SmsSenderService;

class SmsController extends Controller
{
    public function sendSingle(Request $request, SmsSenderService $sms)
    {
        $validated = $request->validate([
            'client_api_key' => 'required',
            'client_secret'  => 'required',
            'service'        => 'required',
            'to'             => 'required',
            'message'        => 'required',
        ]);

        return $sms->sendSingle(
            $validated['client_api_key'],
            $validated['client_secret'],
            $validated['service'],
            $validated['to'],
            $validated['message']
        );
    }

    public function sendBulk(Request $request, SmsSenderService $sms)
    {
        $validated = $request->validate([
            'client_api_key' => 'required',
            'client_secret'  => 'required',
            'service'        => 'required',
            'numbers'        => 'required|array',
            'message'        => 'required',
        ]);

        return $sms->sendBulk(
            $validated['client_api_key'],
            $validated['client_secret'],
            $validated['service'],
            $validated['numbers'],
            $validated['message']
        );
    }
}
