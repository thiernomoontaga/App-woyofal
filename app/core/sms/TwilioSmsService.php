<?php
// namespace App\Core\Sms;

// use Twilio\Rest\Client;

// class TwilioSmsService {
//     private Client $twilio;
//     private string $from;

//     public function __construct(string $sid, string $token, string $from)
//     {
//         $this->twilio = new Client($sid, $token);
//         $this->from = $from;
//     }

//     public function send(string $to, string $message): void
//     {
//         try {
//             $this->twilio->messages->create($to, [
//                 'from' => $this->from,
//                 'body' => $message,
//             ]);
//         } catch (\Exception $e) {
//             // var_dump('Erreur Twilio: ' . $e->getMessage());
//             // die;
//         }
//     }
    
// }
