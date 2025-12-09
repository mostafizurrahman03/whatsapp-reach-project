<?php

// namespace App\Services;

// use Illuminate\Support\Facades\Http;

// class SmsSenderService
// {
//     private $url = "https://smpp.revesms.com:8080/api/v2/SendSMS";
//     private $apiKey = "17d8d40bed48e942";
//     private $secretKey = "10567c07";
//     private $senderId = "WeTechHub";

//     public function send($to, $message)
//     {
//         $payload = [
//             'apikey'         => $this->apiKey,
//             'secretkey'      => $this->secretKey,
//             'callerID'       => $this->senderId,
//             'toUser'         => $to,
//             'messageContent' => $message,
//         ];

//         return Http::post($this->url, $payload)->json();
//     }

//     public function sendBulk(array $numbers, $message)
//     {
//         $responses = [];
//         foreach ($numbers as $num) {
//             $responses[$num] = $this->send($num, $message);
//         }
//         return $responses;
//     }
// }


// namespace App\Services;

// use Illuminate\Support\Facades\Http;
// use App\Models\ClientConfiguration;
// use App\Models\VendorConfiguration;

// class SmsSenderService
// {
//     public function sendSingle(
//         string $clientApiKey,
//         string $clientSecret,
//         string $service,
//         string $to,
//         string $message
//     ): array {

//         // Step 1: validate client
//         $client = ClientConfiguration::where('client_api_key', $clientApiKey)
//                 ->where('client_secret_key', $clientSecret)
//                 ->where('is_active', true)
//                 ->firstOrFail();

//         // Step 2: vendor routing
//         $routing = $client->service_routing ?? [];

//         if (!isset($routing[$service])) {
//             throw new \Exception("No vendor configured for service: $service");
//         }

//         $vendorName = $routing[$service];

//         // Step 3: fetch vendor
//         $vendor = VendorConfiguration::where('vendor_name', $vendorName)
//                     ->where('is_active', true)
//                     ->firstOrFail();

//         $senderId = $vendor->extra_config['default_from'] ?? 'WeTechHub';
//         $url = $vendor->base_url;

//         // Step 4: payload
//         $payload = [
//             'apikey'         => $vendor->api_key,
//             'secretkey'      => $vendor->secret_key,
//             'callerID'       => $senderId,
//             'toUser'         => $to,
//             'messageContent' => $message,
//             'format'         => 'json',
//         ];

//         // Step 5: HTTP call with error handling
//         try {

//             $response = Http::post($url, $payload);

//             return [
//                 'status'   => $response->status(),
//                 'success'  => $response->successful(),
//                 'response' => $response->json()
//             ];

//         } catch (\Exception $e) {
//             return [
//                 'status'   => 500,
//                 'success'  => false,
//                 'error'    => $e->getMessage(),
//             ];
//         }
//     }

//     public function sendBulk(
//         string $clientApiKey,
//         string $clientSecret,
//         string $service,
//         array $numbers,
//         string $message
//     ): array {

//         $responses = [];

//         foreach ($numbers as $num) {
//             $responses[$num] = $this->sendSingle(
//                 $clientApiKey,
//                 $clientSecret,
//                 $service,
//                 $num,
//                 $message
//             );
//         }

//         return $responses;
//     }
// }








// namespace App\Services;

// use Illuminate\Support\Facades\Http;
// use App\Models\ClientConfiguration;
// use App\Models\VendorConfiguration;

// class SmsSenderService
// {
//     /**
//      * Resolve vendor for specific service
//      */
//     private function resolveVendor(ClientConfiguration $client, string $service)
//     {
//         $routing = $client->service_routing ?? [];

//         if (!isset($routing[$service])) {
//             throw new \Exception("No vendor configured for service: $service");
//         }

//         return VendorConfiguration::where('vendor_name', $routing[$service])
//                     ->where('is_active', true)
//                     ->firstOrFail();
//     }

//     /**
//      * Send a single SMS (REVE Format)
//      */
//     public function sendSingle(
//         string $clientApiKey,
//         string $clientSecret,
//         string $service,
//         string $to,
//         string $message
//     ): array {

//         // 1. Validate client
//         $client = ClientConfiguration::where('client_api_key', $clientApiKey)
//                 ->where('client_secret_key', $clientSecret)
//                 ->where('is_active', true)
//                 ->firstOrFail();

//         // 2. Resolve vendor
//         $vendor = $this->resolveVendor($client, $service);

//         $senderId = $vendor->extra_config['default_from'] ?? 'WeTechHub';
//         $url = $vendor->base_url;

//         // OPTIONAL HASH (if REVE requires)
//         $hash = md5($vendor->api_key . $vendor->secret_key . $to);

//         // 3. Prepare payload
//         $payload = [
//             'apikey'         => $vendor->api_key,
//             'secretkey'      => $vendor->secret_key,
//             'callerID'       => $senderId,
//             'toUser'         => $to,
//             'messageContent' => $message,
//             'hash'           => $hash
//         ];

//         // 4. Send HTTP Request
//         $response = Http::asJson()->post($url, $payload);

//         return $response->json();
//     }

//     /**
//      * Bulk Send
//      */
//     public function sendBulk(
//         string $clientApiKey,
//         string $clientSecret,
//         string $service,
//         array $numbers,
//         string $message
//     ): array {

//         $responses = [];

//         foreach ($numbers as $item) {
//             $num = is_array($item) ? ($item['number'] ?? null) : $item;

//             if (!$num) continue;

//             $responses[$num] = $this->sendSingle(
//                 $clientApiKey,
//                 $clientSecret,
//                 $service,
//                 $num,
//                 $message
//             );
//         }

//         return $responses;
//     }
// }

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\ClientConfiguration;
use App\Models\VendorConfiguration;

class SmsSenderService
{
    public function sendSingle(string $clientApiKey, string $clientSecret, string $service, string $to, string $message): array
    {
        $client = ClientConfiguration::where('client_api_key', $clientApiKey)
            ->where('client_secret_key', $clientSecret)
            ->where('is_active', true)
            ->firstOrFail();

        $routing = $client->service_routing ?? [];
        if (!isset($routing[$service])) {
            throw new \Exception("No vendor configured for service: $service");
        }

        $vendorName = $routing[$service];

        $vendor = VendorConfiguration::where('vendor_name', $vendorName)
            ->where('is_active', true)
            ->firstOrFail();

        $senderId = $vendor->extra_config['default_from'] ?? 'WeTechHub';

        $payload = [
            'apikey'         => $vendor->api_key,
            'secretkey'      => $vendor->secret_key,
            'callerID'       => $senderId,
            'toUser'         => $to,
            'messageContent' => $message,
        ];

        $response = Http::post($vendor->base_url, $payload);

        return $response->json();
    }

    public function sendBulk(string $clientApiKey, string $clientSecret, string $service, array $numbers, string $message): array
    {
        $responses = [];

        foreach ($numbers as $num) {
            $responses[$num] = $this->sendSingle(
                $clientApiKey,
                $clientSecret,
                $service,
                $num,
                $message
            );
        }

        return $responses;
    }
}
