<?php

namespace App\Services\Auth;

use App\Helpers\Constant;
use App\Http\Resources\User\StoreResource;
use App\Mail\ActivationMail;
use App\Models\User;
use App\Models\VerificationCode;
use App\Services\External\InfobipSMS;
use App\Services\External\TwilioSMS;
use App\Services\External\TwilioWhatsApp;
use Illuminate\Support\Facades\Mail;

class VerificationService
{

    /**
     * Verification Code User Account.
     * @param $user_id
     * @param $objective ['verify', 'reset']
     * @param $information_type ['phone', 'email']
     * @param $information
     */
    public static function verifyAccount($user_id, $objective, $information_type, $information, $country_code): void
    {
//        dd('trdt');
        $activation_code = rand(1000, 9999);
//        $activation_code = '1111';

//        try {
//            TwilioSMS::send([
//                'phone'             => $information,
//                'country_code'      => $country_code,
//                'verification_code' => $activation_code
//            ]);
//        } catch (\Exception $e) {
//            throw new \Exception();
//        }
//        try {
//            if($information_type==Constant::VERIFICATION_INFORMATION_TYPE['Phone']) {
//                UltraMessage::send($country_code . $information, $activation_code);
//            }
//            else{
//               Mail::to($information)->send(new ActivationMail(['code' => $activation_code]));
//
//            }
//            InfobipSMS::send([
//                'phone'             => $information,
//                'country_code'      => $country_code,
//                'verification_code' => $activation_code
//            ]);
//        } catch (\Exception $e) {
//            throw new \Exception();
//        }
        if($information_type==Constant::VERIFICATION_INFORMATION_TYPE['Phone']) {
            TwilioWhatsApp::sendLegacy($country_code . $information, $activation_code);
        }


        VerificationCode::updateOrInsert(
            [
                'user_id'           => $user_id,
                'objective'         => $objective,
                'information_type'  => $information_type,
                'information'       => $information,
                'country_code'      =>$country_code
            ],
            [
                'code'              => $activation_code,
                'used'              => Constant::VERIFICATION_USED['Not used'],
                'expired_at'        => now()->addHour()->toDateTimeString(),
                'created_at'        => now(),
            ]
        );
    }
}
