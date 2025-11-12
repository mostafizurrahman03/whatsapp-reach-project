<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactInformationController extends Controller
{
        /**
     * Get contact information for the website
     */
    public function index(): JsonResponse
    {
        // latest contact info
        $contact = ContactInformation::latest()->first();

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'No contact information found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'email' => $contact->email,
                'phone' => $contact->phone,
                'address' => $contact->address,
                'business_hours' => $contact->business_hours,
            ],
        ]);
    }
}
