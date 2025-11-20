<?php

// namespace App\Livewire;

// use Livewire\Component;
// use App\Models\ContactInformation;

// class ContactPage extends Component
// {
//     public $contact = [
//         'email' => '',
//         'phone' => '',
//         'address' => '',
//         'business_hours' => '',
//     ];

//     public function mount()
//     {
//         $data = ContactInformation::latest()->first();

//         if ($data) {
//             $this->contact = [
//                 'email' => $data->email,
//                 'phone' => $data->phone,
//                 'address' => $data->address,
//                 'business_hours' => $data->business_hours,
//             ];
//         }
//     }

//     public function render()
//     {
//         return view('livewire.contact-page')
//             ->layout('components.layouts.app');
//     }
// }


// namespace App\Livewire;

// use Livewire\Component;
// use App\Models\ContactInformation;
// use App\Models\ClientMessage; // messages table model
// use Mail;

// class ContactPage extends Component
// {
//     public $contact = [
//         'email' => '',
//         'phone' => '',
//         'address' => '',
//         'business_hours' => '',
//     ];

//     public $name, $email, $subject, $message;

//     public function mount()
//     {
//         $data = ContactInformation::latest()->first();

//         if ($data) {
//             $this->contact = [
//                 'email' => $data->email,
//                 'phone' => $data->phone,
//                 'address' => $data->address,
//                 'business_hours' => $data->business_hours,
//             ];
//         }
//     }

//     public function submitForm()
//     {
//         $this->validate([
//             'name' => 'required|string',
//             'email' => 'required|email',
//             'subject' => 'required|string',
//             'message' => 'required|string',
//         ]);

//         // Save message
//         ClientMessage::create([
//             'name'    => $this->name,
//             'email'   => $this->email,
//             'subject' => $this->subject,
//             'message' => $this->message,
//         ]);

//         // Optional: send mail here

//         $this->reset(['name','email','subject','message']);
        
//         session()->flash('message', 'Thank you! Your message has been sent successfully.');
//     }

//     public function render()
//     {
//         return view('livewire.contact-page')->layout('components.layouts.app');
//     }
// }



namespace App\Livewire;

use Livewire\Component;
use App\Models\ContactInformation;
use App\Models\ClientMessage;
use Mail;

class ContactPage extends Component
{
    public $contact = [
        'email' => '',
        'phone' => '',
        'address' => '',
        'business_hours' => '',
    ];

    public $name, $email, $subject, $message;
    public $isSubmitting = false; // For submit loading state

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

    public function submitForm()
    {
        $this->isSubmitting = true; // Start loading

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Save message
        ClientMessage::create([
            'name'    => $this->name,
            'email'   => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        // Reset form fields
        $this->reset(['name', 'email', 'subject', 'message']);

        // Flash success message
        session()->flash('message', 'Thank you! Your message has been sent successfully.');

        $this->isSubmitting = false; // Stop loading
    }

    public function render()
    {
        return view('livewire.contact-page')->layout('components.layouts.app');
    }
}

