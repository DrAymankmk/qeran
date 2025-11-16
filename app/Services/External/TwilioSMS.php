<?php
namespace App\Services\External;

use Twilio\Rest\Client;



class TwilioSMS
{
    static function send($data)
    {
        // Your Account SID and Auth Token from twilio.com/console
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $client = new Client($sid, $token);

        // Use the client to do fun stuff like send text messages!
        $client->messages->create(
        // the number you'd like to send the message to
            $data['country_code'].$data['phone'],
            [
                // A Twilio phone number you purchased at twilio.com/console
                'from' => "+12706379193",
                // the body of the text message you'd like to send
                'body' => 'Verification code: '.$data['verification_code']
            ]
        );
    }

    static function sendWithTemplate($data) {
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $client = new Client($sid, $token);

        $client->messages->create(
            $data['country_code'].$data['phone'],
            [
                'from' => "+12706379193",
                'body' => $data['message']
            ]
        );

    }
}
