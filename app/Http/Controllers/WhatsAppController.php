<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function send(Request $request, WhatsAppService $whatsAppService)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'required',
        ]);

        return response()->json(
            $whatsAppService->sendWhatsAppText(
                $request->phone,
                $request->message
            )
        );
    }
     public function callBack(Request $request)
    {
        // Log everything coming in
        Log::info('WhatsApp request received', [
            'request_data' => $request->all(),
            
        ]);
        return "";
    }

    public function sendWhatsAppTextIsTyping($messageId)
    {
    
        $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        $token = env('WHATSAPP_ACCESS_TOKEN');
        $version = env('WHATSAPP_API_VERSION', 'v25.0');

        $url = "https://graph.facebook.com/{$version}/{$phoneNumberId}/messages";

        try
        {
            $response = Http::withToken($token)
            ->post($url, [
                "messaging_product" => "whatsapp",
                "status" => "read",
                "message_id" => $messageId,
                "typing_indicator" => array("type" => "text")
            ]);

            Utils::saveLogs("sendWhatsAppTextResponse : ".$messageId, $response->json());

            return $response->json();
               
        }
        catch(Exception $e)
        {
            Utils::saveLogs("sendWhatsAppTextException", $e->getMessage());
            Utils::saveLogs("sendWhatsAppTextException", $e);
        }
    }
//     public function returnChallenge(Request $request)
// {
//       try
//       {
//             $valid_token = "BIMATECH2026";

//             $data = $request->all();

//             Utils::saveLogs("WhatsAppGatewayIncomingD", $data);

//             $verify_token   = $data['hub_verify_token'] ?? null;
//             $challenge      = $data['hub_challenge'] ?? null;

//             if($valid_token != $verify_token)
//             {
//                 return response('Unauthorized', '401', []);
//             }

//             return $challenge;
//       }
//       catch(Exception $e)
//       {
//          Utils::saveLogs("WhatsAppGatewayIncomingExceptionC", $e->getMessage());
//          return "Sorry ! We have issue in service. Kindly try again shortly.";
//       }

// }
    
}