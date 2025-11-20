<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientMessage;
use Illuminate\Support\Facades\Validator;

class ClientMessageController extends Controller
{
    public function submit(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Save to database
        $clientMessage = ClientMessage::create($request->only(['name', 'email', 'subject', 'message']));

        // Optional: send email notification here

        return response()->json([
            'status' => 'success',
            'message' => 'Your message has been submitted successfully!',
            'data' => $clientMessage
        ]);
    }
}
