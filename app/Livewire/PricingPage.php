<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PricingPlan;

class PricingPage extends Component
{
    
     // Array to store all pricing plans
    public $plans = [];

    public function mount()
    {
        // Fetch all pricing plans ordered by sort_order
        $this->plans = PricingPlan::orderBy('sort_order', 'asc')->get()->toArray();
    }
    
    public function render()
    {
        return view('livewire.pricing-page')
                ->layout('components.layouts.app'); // path of blade view
    }
}

