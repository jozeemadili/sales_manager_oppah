<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TIRAClient\Scripts\Classes\Utils;
class WhatsAppGatewayController extends Controller
{
    public function replyCustomer(Request $request)
{
    try {

        $data = $request->all();

        $from        = data_get($data, 'entry.0.changes.0.value.contacts.0.wa_id');
        $contactName = $data['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'] ?? "Dear";
        $message     = data_get($data, 'entry.0.changes.0.value.messages.0', []);

        $messageId   = $message['id'] ?? null;
        $messageType = $message['type'] ?? 'text';

        if (!$from || !$messageId) {
            return response()->json(['status'=>'ignored'],200);
        }

        // ===== SAFE EXTRACTION =====
        [$messageBodyRaw, $messageBody, $imageId] = $this->extractMessage($message, $messageType);

        // ===== DUPLICATE CHECK =====
        if ($this->isDuplicate($messageId)) {
            return response()->json(['status'=>'duplicate'],200);
        }

        $sessionKey = "wa_session_" . $from;
        $session    = Cache::get($sessionKey, ['step'=>'menu']);

        if ($messageBody == "menu") {
            Cache::forget($sessionKey);
            $session = ['step'=>'menu'];
        }

        switch ($session['step']) {

            case "menu":
                $this->sendWhatsAppText($from,
                    "Hi ".$contactName.".\n*Welcome to NBC BimaTech* 🚗\n".
                    "1. Buy Motor Insurance\n".
                    "2. Buy Non Motor Insurance\n".
                    "3. Other Services\n\n".
                    "- Incase I Delay to ANSWER for 30 seconds, Kindly type anything I'll respond quickly.\n"
                );
                $session['step'] = "select_product";
                break;

            case "select_product":
                if ($messageBody == "1") {
                    $this->sendWhatsAppText($from,"Enter Vehicle Registration Number or Chassis Number:\nExample: T123ABC");
                    $session['step'] = "vehicle_identifier";
                } else {
                    $this->sendWhatsAppText($from,"Ooh ! Sorry. This Service is Coming Soon ! Please Choose option 1.");
                }
                break;

            case "customer_email":

                if(!$this->validateEmail($from,$session,$messageBody,$messageBodyRaw)){
                    break;
                }

                $this->sendWhatsAppText($from,"Enter Referral (Phone/Email) or 0 to skip:");
                $session["step"]="referral";
                break;

                Cache::forget($sessionKey);
                return response()->json(['status'=>'done'],200);
        }

        Cache::put($sessionKey,$session,now()->addMinutes(30));
        return response()->json(['status'=>'ok'],200);

    } catch (\Throwable $e) 
    {
        Utils::saveLogs("WhatsAppErrorFullTrace", [
                                                                    "message" => $e->getMessage(),
                                                                    "file"    => $e->getFile(),
                                                                    "line"    => $e->getLine(),
                                                                    "trace"   => $e->getTraceAsString()
                                                                ]);
        return response()->json(['status'=>'error'],200);
    }
}


    public function sendWhatsAppText($to, $message)
    {
        $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID', '444254935429035');
        $token = env('WHATSAPP_ACCESS_TOKEN', 'EAALUYPQDXasBQZCy73ZBQH8njVYUIBRy8DM2YWqdA9iFMTAWZApPmpRJCGrZCuOlkBlNcDbr6TZBDs9TJGflb9iYinucZBv1rfGZBoTSe5T4HuZAq8ckInn7mV7tr9a5QsY8Eq3tN8ohXwv48BcWL1a4MB6oZCj1yHYKeoHIT0vxz1xRu3D4zd9ZBQqmK6hXjeVkqsfgZDZD');
        $version = env('WHATSAPP_API_VERSION', 'v25.0');

        $url = "https://graph.facebook.com/{$version}/{$phoneNumberId}/messages";

        try
        {
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

            Utils::saveLogs("sendWhatsAppTextResponse : ".$to, $response->json());
            Utils::saveLogs("actaulMessageSent : ", $message);

            return $response->json();
               
        }
        catch(Exception $e)
        {
            Utils::saveLogs("sendWhatsAppTextException", $e->getMessage());
            Utils::saveLogs("sendWhatsAppTextException", $e);
        }
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
    public function returnChallenge(Request $request)
{
      try
      {
            $valid_token = "TULI2026";

            $data = $request->all();

            Utils::saveLogs("WhatsAppGatewayIncomingD", $data);

            $verify_token   = $data['hub_verify_token'] ?? null;
            $challenge      = $data['hub_challenge'] ?? null;

            if($valid_token != $verify_token)
            {
                return response('Unauthorized', '401', []);
            }

            return $challenge;
      }
      catch(Exception $e)
      {
         Utils::saveLogs("WhatsAppGatewayIncomingExceptionC", $e->getMessage());
         return "Sorry ! We have issue in service. Kindly try again shortly.";
      }

}


}
