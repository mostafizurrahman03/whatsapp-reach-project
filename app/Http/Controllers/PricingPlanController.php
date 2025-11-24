<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PricingPlan;

class PricingController extends Controller
{
    // Single Pricing plan show
    public function show($id)
    {
        $pricing = PricingPlan::findOrFail($id);
        return view('pricing.show', compact('pricing'));
    }

    // All Pricing plans list
    public function index()
    {
        $pricings = PricingPlan::all(); 
        return view('pricing.index', compact('pricings'));
    }
}



