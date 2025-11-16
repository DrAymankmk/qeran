<?php

namespace App\Services\External;

class UltraMessage
{
    static function send($phone, $activationCode ,$message="لقد تم تسجيل حسابك بنجاح كود التفعيل " , $referenceId="")
    {
        $message = $message." ".$activationCode;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.ultramsg.com/instance146398/messages/chat",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "token=6q8snleyad0ddlbl&to=$phone&body=$message&priority=10&referenceId=$referenceId",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $response=json_decode($response);
        return $response;
    }
}
