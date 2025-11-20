<?php

namespace App\Http\Livewire;

use Livewire\Component;

class FeaturesPage extends Component
{
    public function render()
    {
        return view('livewire.features-page')
                ->layout('components.layouts.app'); // layout যুক্ত করুন;
    }
}
