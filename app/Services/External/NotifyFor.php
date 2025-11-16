<?php

namespace App\Services\External;
use Pusher\PushNotifications\PushNotifications;

class NotifyFor
{
    public static function send($interest,$data){
//        dd(config('services.Beams.Beams_Instance_Id'),config('services.Beams.Beams_Secret_key'));
        $beamsClient = new PushNotifications(array(

            "instanceId" => config('services.Beams.Beams_Instance_Id'),
            "secretKey" => config('services.Beams.Beams_Secret_key')
        ));
        if($interest!='all') {
            $publishResponse = $beamsClient->publishToInterests(
                array($interest),
                array(
                    "fcm" => array(
                        "data" => $data,
                    ),
                    "apns" => array("aps" => array(
                        "alert" =>
                            $data
                        ,
                        "sound" => "default",
                        "badge" => 0,

                        "type" => $data['type'],


                    )),
                ));
        }
        else{
            $publishResponse = $beamsClient->publishToInterests(
                array('users'),
                array(
                    "fcm" => array(
                        "data" => $data,

                    ),
                    "apns" => array("aps" => array(
                        "alert" =>
                            $data
                          ,
                        "sound" => "default",
                        "badge" => 0,

                        "type" => $data['type'],


                    )),
                ));
            $publishResponse = $beamsClient->publishToInterests(
                array('providers'),
                array(
                    "fcm" => array(
                        "data" => $data,

                    ),
                    "apns" => array("aps" => array(
                        "alert" =>
                            $data

                        ,
                        "sound" => "default",
                        "badge" => 0,

                        "type" => $data['type'],


                    )),
                ));

        }
        return $publishResponse;

    }
}
