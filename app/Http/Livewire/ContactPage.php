<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ContactPage extends Component
{
    public function render()
    {
        return view('livewire.contact-page')
               ->layout('components.layouts.app'); // layout যুক্ত করুন
    }
}
