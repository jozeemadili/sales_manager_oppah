<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendWhatsAppText(string $to, string $message): array
    {
        $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        $token = env('WHATSAPP_ACCESS_TOKEN');
        $version = env('WHATSAPP_API_VERSION', 'v25.0');

        $url = "https://graph.facebook.com/{$version}/{$phoneNumberId}/messages";

        try {
            $response = Http::withToken($token)
                ->post($url, [
                    "messaging_product" => "whatsapp",
                    "recipient_type" => "individual",
                    "to" => $to,
                    "type" => "text",
                    "text" => [
                        "preview_url" => false,
                        "body" => $message
                    ]
                ]);

            Log::info('WhatsApp Response', [
                'phone' => $to,
                'response' => $response->json()
            ]);

            if (!$response->successful()) {
                Log::error('WhatsApp API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('WhatsApp Exception', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}