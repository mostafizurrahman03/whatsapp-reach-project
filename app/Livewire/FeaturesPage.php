<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Feature;

class FeaturesPage extends Component
{
    public $features; // Declare public property

    public function mount()
    {
        // Load active features from database
        $this->features = Feature::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    public function render()
    {
        // Return the Livewire view
        return view('livewire.features-page')
                ->layout('components.layouts.app'); // Optional layout
    }
}
