<?php

namespace App\Service;

use Exception;
use Illuminate\Support\Facades\Http;

class EnviarWhatsApp
{




    public function sendMessage($recipientId, $messageText)
    {



        try {

            $token = 'EAANd0V6azu8BOZCv4FnrZAMotmaTm7pAn1p3ByfXA7IEMkjx0ulk54mzMPdcJ4sYKBas7tbtAZCZB2enZCUd9qhbZBqxuZBZBeLWZA8zy8ZCrv6LIdOdiiaeVKdW6iTaTIQCOPZCZAIZAWfwW9IyVV61adzxoZAVhgHHP3Xgs41RJC1SR9KOs7N9nFBlvZAQwa8aRyKE81vZArbJkGZBsIZA2V62KmhmAZD';
            $url = 'https://graph.facebook.com/v18.0/285887497932786/messages';


            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $recipientId,
                'type' => 'text',
                'text' => [
                    'body' => $messageText
                ]


            ];
            $message = Http::withToken($token)->post($url, $payload)->throw()->json();

            return response()->json([
                'success' => true,
                'data' => $message,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    // -H 'Authorization: Bearer EAANd0V6azu8BOZCv4FnrZAMotmaTm7pAn1p3ByfXA7IEMkjx0ulk54mzMPdcJ4sYKBas7tbtAZCZB2enZCUd9qhbZBqxuZBZBeLWZA8zy8ZCrv6LIdOdiiaeVKdW6iTaTIQCOPZCZAIZAWfwW9IyVV61adzxoZAVhgHHP3Xgs41RJC1SR9KOs7N9nFBlvZAQwa8aRyKE81vZArbJkGZBsIZA2V62KmhmAZD' `
    // -H 'Content-Type: application/json' `
    // -d '{ \"messaging_product\": \"whatsapp\", \"to\": \"528131144377\", \"type\": \"template\", \"template\": { \"name\": \"hello_world\", \"language\": { \"code\": \"en_US\" } } }'
}


// $headers = [
//     'Authorization: Bearer ' . $this->token,
//     'Content-Type: application/json'
// ];

// $mensaje = ''
//     . '{'
//     . '"messaging_product":"whatsapp",'
//     . '"to":"' . $recipientId . '",'
//     . '"type":"text",'
//     . '"text": '
//     . '{'
//     . '"preview_url":true,'
//     . '  "body": "' . $messageText . '",'
//     . '}'
//     . '}';




// $ch = curl_init($url);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_POSTFIELDS, $mensaje);
// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// $response = curl_exec($ch);
// curl_close($ch);

// return $response;
