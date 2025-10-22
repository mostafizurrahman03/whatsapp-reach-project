<?php

namespace App\Filament\User\Resources\LeadResource\Pages;

use App\Filament\User\Resources\LeadResource;
use App\Models\Lead; 
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;
    
    protected function handleRecordCreation(array $data): Lead
    {
        // Non-admin safeguard
        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        // return Lead::create($data);
          $phones = [];

        // From TagsInput
        if (!empty($data['phone'])) {
            $phones = array_merge($phones, explode(',', $data['phone']));
        }

        // // From CSV
        // if (isset($data['phone_csv']) && $data['phone_csv'] !== null) {
        //     $csvPath = storage_path('app/public/' . $data['phone_csv']);
        //     if (file_exists($csvPath)) {
        //         $csvData = array_map('str_getcsv', file($csvPath));
        //         foreach ($csvData as $row) {
        //             if (!empty($row[0])) {
        //                 $phones[] = trim($row[0]);
        //             }
        //         }
        //     }
        // }
        // From CSV
        
        if (isset($data['phone_csv']) && $data['phone_csv'] !== null) {
            $csvPath = storage_path('app/public/' . $data['phone_csv']);

            if (file_exists($csvPath)) {
                $csvData = array_map('str_getcsv', file($csvPath));

                // Remove header row (first row)
                if (!empty($csvData)) {
                    $header = array_shift($csvData);
                }

                $phones = [];

                foreach ($csvData as $row) {
                    // Skip empty rows
                    if (isset($row[0]) && trim($row[0]) !== '') {
                        $phones[] = trim($row[0]);
                    }
                }

                // Optional: remove duplicate numbers
                $phones = array_unique($phones);

                // Now $phones array contains all numbers from CSV, without header or empty rows
                // You can save them to database or pass to TagsInput
            }
        }


        // Remove duplicates and empty values
        $phones = array_filter(array_unique($phones));

        $lastLead = null;

        // Insert each phone as a separate Lead row
        foreach ($phones as $phone) {
            $lastLead = Lead::create([
                'user_id' => $data['user_id'],
                'name' => $data['name'] ?? null,
                'source' => $data['source'] ?? null,
                'status' => $data['status'],
                'phone' => $phone,
            ]);
        }

        // Return the last created Lead for Filament
        return $lastLead;
    }
}
