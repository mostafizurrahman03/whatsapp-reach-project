<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ContactInformation;

class ContactPage extends Component
{
    public $contact = [
        'email' => '',
        'phone' => '',
        'address' => '',
        'business_hours' => '',
    ];

    public function mount()
    {
        $data = ContactInformation::latest()->first();

        if ($data) {
            $this->contact = [
                'email' => $data->email,
                'phone' => $data->phone,
                'address' => $data->address,
                'business_hours' => $data->business_hours,
            ];
        }
    }

    public function render()
    {
        return view('livewire.contact-page')
            ->layout('components.layouts.app');
    }
}
