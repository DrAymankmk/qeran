<?php

namespace App\Services\External;

use Pusher\PushNotifications\PushNotifications;

class NotifyTo
{
    public static function send($userType, $id, $data)
    {
//        dd( array(
//            "data" => $data,
//            'title'=>$data['title'],
//            'body'=>$data['body']
//
//        ));
        $beamsClient = new PushNotifications(array(

            "instanceId" => config('services.Beams.Beams_Instance_Id'),
            "secretKey" => config('services.Beams.Beams_Secret_key')
        ));
        $userID = $userType . '-' . $id;
        $publishResponse = $beamsClient->publishToUsers(
            array($userID),
            array(
                "fcm" => array(
                    "data" => $data,
                    "notification" => $data,
                    'title'=>$data['title'],
                    'body'=>$data['body']

                ),
                "apns" => array("aps" => array(
                    "alert" => $data,
                    "sound" => $data['sound'],
                    "badge" => 0,

                    "type" => $data['type'],
                    "target_id" => $data['target_id'],


                )),
                "web" => array(
                    "time_to_live" => 3600,
                    "notification" => $data + [
                            //"icon" => "logo.png",
                           // "badge" => "logo.png",
//                            "deep_link" => "#",
                           // "sound" => "default",
                           // "hide_notification_if_site_has_focus" => true
                        ])
            ));
        return $publishResponse;

    }
}
