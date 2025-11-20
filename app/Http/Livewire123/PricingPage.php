<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PricingPage extends Component
{
    public function render()
    {
        return view('livewire.pricing-page')
                ->layout('components.layouts.app'); // layout যুক্ত করুন; // Blade view এর path
    }
}
