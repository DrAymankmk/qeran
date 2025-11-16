<?php

use App\Helpers\Constant;
use \Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\Setting;
use libphonenumber\PhoneNumberUtil;
use Propaganistas\LaravelPhone\PhoneNumber;

if (!function_exists('storeImage')) {
    function storeImage($options)
    {
        $image = $options['value'];
        if (isset($options['base64']) && $options['base64'] == true) {
            $extension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];

            $replace = substr($image, 0, strpos($image, ',') + 1);

            $image = str_replace($replace, '', $image);

            $image = base64_decode(str_replace(' ', '+', $image));

            $hubFile = [
                'bucket_name' => $options['folderName'],
                'extension' => $extension,
                'getMimeType' => 'image/' . $extension,
                'file_type' => $options['file_type'] ?? Constant::FILE_TYPE['Image'],
                'file_key' => $options['file_key'] ?? Constant::FILE_KEY['Not Main'],
            ];
        }

        $filename = time() . substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5) . (isset($options['extension']) ? '.' . $options['extension'] : '.webp');


        mediumImage($image, $options, $filename);

        originalImage($image, $options, $filename);

        if ($options['saveInDatabase'] == true) {
            if (!isset($options['base64'])) {
                $hubFile = [
                    'bucket_name' => $options['folderName'],
                    'original_name' => $options['value']->getClientOriginalName(),
                    'extension' => $options['value']->extension(),
                    'size' => $options['value']->getSize(),
                    'getMimeType' => $options['value']->getMimeType(),
                    'file_type' => $options['file_type'] ?? Constant::FILE_TYPE['Image'],
                    'file_key' => $options['file_key'] ?? Constant::FILE_KEY['Not Main'],

                ];
            }

            $options['model']->hubFiles()->updateOrCreate($hubFile + ['path' => $filename]);
        }

        return $options['folderName'] . '/' . $filename;
    }
}
function thumbnailImage($image, $options, $filename)
{
    $width = 170;
    $height = 130;

    $image = Image::make($image);

    $image->resize($width, $height)->encode('webp', 50);

    Storage::put('public/' . $options['folderName'] . '/thumbnail/' . $filename, $image);
}

function mediumImage($image, $options, $filename)
{
    $width = 256;
    $height = 170;

    if (isset($options['model']) && class_basename($options['model']) == 'Blog') {
        $width = 338;
        $height = 254;
    }

    if (isset($options['model']) && class_basename($options['model']) == 'Country') {
        $width = 445;
        $height = 230;
    }

    $image = Image::make($image);
    if (isset($options['watermark'])) {
        $imageWidth = $image->width();
        $watermarkSource = Image::make(public_path('logo-watermark.png'));

        $watermarkSize = round(10 * $imageWidth / 50);
        $watermarkSource->resize($watermarkSize, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $image->insert($watermarkSource, 'bottom-left', 30, 30);
    }

    $image->resize($width, $height)->encode('webp', 90);

    Storage::put('public/' . $options['folderName'] . '/medium/' . $filename, $image);
}

function originalImage($image, $options, $filename)
{
    $width = 1024; // your max width
    $height = 1024; // your max height

    $image = Image::make($image);

    $image->height() > $image->width() ? $width = null : $height = null;

    if (isset($options['watermark'])) {
        $imageWidth = $image->width();
        $watermarkSource = Image::make(public_path('logo-watermark.png'));

        $watermarkSize = round(10 * $imageWidth / 50);
        $watermarkSource->resize($watermarkSize, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $image->insert($watermarkSource, 'bottom-left', 30, 30);
    }

    $image->resize($width, $height, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    })->encode('webp');

    Storage::put('public/' . $options['folderName'] . '/' . $filename, $image);
}

if (!function_exists('settings')) {
    function settings($key)
    {
        cache()->remember('settings', 60 * 60 * 24, function () {
            return new Setting();
        });

        return cache()->get('settings')->where('key', $key)->with('translations')->get();
    }
}
if (!function_exists('storeVideo')) {
    function storeVideo($options)
    {
        if ($options['value']) {

            $filename = time() . substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5) . '.mp4';

            Storage::putFileAs('public/' . $options['folderName'], $options['value'], $filename);

            $options['model']->hubFiles()->updateOrCreate([
                'bucket_name' => $options['folderName'],
                'original_name' => $options['value']->getClientOriginalName(),
                'path' => $filename,
                'extension' => $options['value']->extension(),
                'size' => $options['value']->getSize(),
                'getMimeType' => ($options['value']->getMimeType() == 'video/quicktime' ? 'video/mp4' : $options['value']->getMimeType()),
                'file_type' => $options['file_type'] ?? Constant::FILE_TYPE['Video'],
                'file_key' => $options['file_key'] ?? Constant::FILE_KEY['Not Main'],

            ]);
        }
    }
}
if (!function_exists('storeGif')) {
    function storeGif($options)
    {
        if ($options['value']) {

            $filename = time() . substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5) . '.gif';

            Storage::putFileAs('public/' . $options['folderName'], $options['value'], $filename);

            $options['model']->hubFiles()->updateOrCreate([
                'bucket_name' => $options['folderName'],
                'original_name' => $options['value']->getClientOriginalName(),
                'path' => $filename,
                'extension' => $options['value']->extension(),
                'size' => $options['value']->getSize(),
                'getMimeType' => $options['value']->getMimeType(),
                'file_type' => $options['file_type'] ?? Constant::FILE_TYPE['Gif'],
                'file_key' => $options['file_key'] ?? Constant::FILE_KEY['Not Main'],

            ]);
        }
    }
}
if (!function_exists('storeAudio')) {
    function storeAudio($options)
    {
        if ($options['value']) {

            $filename = time() . substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5) . '.mp3';

            Storage::putFileAs('public/' . $options['folderName'], $options['value'], $filename);

            $options['model']->hubFiles()->updateOrCreate([
                'bucket_name' => $options['folderName'],
                'original_name' => $options['value']->getClientOriginalName(),
                'path' => $filename,
                'extension' => $options['value']->extension(),
                'size' => $options['value']->getSize(),
                'getMimeType' => ($options['value']->getMimeType() == $options['value']->getMimeType()),
                'file_type' => $options['file_type'] ?? Constant::FILE_TYPE['Image'],
                'file_key' => $options['file_key'] ?? Constant::FILE_KEY['Not Main'],

            ]);
        }
    }
}
if (!function_exists('deleteImage')) {
    function deleteImage($path, $record = null)
    {
        Storage::disk('public')->delete($path);
        if ($record) {
            $record->delete();
        }
    }
}
if (!function_exists('getVisIpAddr')) {

    function getVisIpAddr()
    {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}

if (!function_exists('phoneByIpLocation')) {
    function phoneByIpLocation($phone)
    {
        $clientIP = getVisIpAddr();
        $apiURL = 'ipinfo.io/' . $clientIP;
        $curl = curl_init($apiURL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $ipDetails = json_decode($response, true);
        if (!empty($ipDetails)) {
            $phoneNumberUtilInstance = PhoneNumberUtil::getInstance();
//dd($ipDetails['country']);
            $data['country_code'] = $phoneNumberUtilInstance->getCountryCodeForRegion($ipDetails['country']);
            $phone = new PhoneNumber($phone, $ipDetails['country']);
//            dd($phone->formatForMobileDialingInCountry($ipDetails['country']));

            $data['phone'] = substr($phone->formatForMobileDialingInCountry($ipDetails['country']), 1);
            return $data;
        }
    }
}
if (!function_exists('slug')) {
    function slug($string, $separator = '-')
    {
        if (is_null($string)) {
            return "";
        }

        // Remove spaces from the beginning and from the end of the string
        $string = trim($string);

        // Lower case everything
        // using mb_strtolower() function is important for non-Latin UTF-8 string | more info: https://www.php.net/manual/en/function.mb-strtolower.php
        $string = mb_strtolower($string, "UTF-8");

        // Make alphanumeric (removes all other characters)
        // this makes the string safe especially when used as a part of a URL
        // this keeps latin characters and arabic charactrs as well
        $string = preg_replace("/[^a-z0-9_\s\-ءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى]#u/", "", $string);

        // Remove multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);

        // Convert whitespaces and underscore to the given separator
        $string = preg_replace("/[\s_]/", $separator, $string);

        return $string;
    }
}

if (!function_exists('checkPackageCount')) {
    function checkPackageCount($invitation,$type='checkUsersCount',$count=0)
    {
        $adminInvitationCount      = $invitation->admins()->sum('invitation_user.invitation_count');
        $package_invitation_count = 0;



        $countOfAdditionalPackages = $invitation->packages?->sum('count') +
                                     $invitation->packages?->sum('free_invitations_count');

//        if(isset($additionalPackages))
//        {
//            foreach ($additionalPackages as $additionalPackage)
//            {
//                $countOfAdditionalPackages += $additionalPackage->package?->count + $additionalPackage->package?->free_invitations_count;
//            }
//        }


        if (count($invitation->paidPackages)>0) {
            foreach ($invitation->paidPackages as $package) {

                $package_invitation_count += ($package->package->count + $package->package->free_invitations_count);
            }
        }

        if($type=='checkUsersCount') {
            if ($invitation->users()->sum('invitation_count') >= (($package_invitation_count + $invitation->count)-$adminInvitationCount)) {
                return false;
            }
            return true;
        }
        elseif($type=='checkAllUsersInvitationCountForEachUser') {
            if ($invitation->users()->sum('invitation_count')+$count > (($package_invitation_count + $invitation->count)-$adminInvitationCount)) {
                return false;
            }
            return true;
        }
        elseif ($type=='checkAdminInvitationCount'){
            $userInvitationCount = $invitation->users()->sum('invitation_count');

            if($count>(($package_invitation_count)-($adminInvitationCount+$userInvitationCount))){
                return false;
            }
            return true;

        }
        elseif ($type=='checkAllUsersInvitationCount'){
           return $countOfAdditionalPackages - $adminInvitationCount;
        }

        elseif ($type=='checkAllInvitationPackagesCount'){
            $package_invitation = 0;
            $userInvitationCount = $invitation->users()->where('invited_by', auth()->id())->sum('invitation_count');


            $invitationPackages = $invitation->invitationPackages;


            if (count($invitationPackages) > 0) {
                foreach ($invitationPackages as $invitationPackage) {
    
                    $package_invitation += ($invitationPackage->package->count + $invitationPackage->package->free_invitations_count + $invitationPackage->count );
                }
            }

            $availableInvitationCount = $package_invitation - ($adminInvitationCount+$userInvitationCount);


        
            if($availableInvitationCount < $count){
                return false;
            }
            return true;


        }
        else{
           $admin= $invitation->admins()->where('user_id',auth()->id())->first();
            //invitedToUsers
            if($admin && $admin->pivot->invitation_count <=$admin->invitedToUsers()->where('invitation_id',$invitation->id)->count()){
                return false;

            }
            return true;


        }
    }
}

if(!function_exists('checkPackageCountForUser')){
    function checkPackageCountForUser($invitation,$count, $user_id){
        $package_invitation = 0;
        $userInvitationCount = $invitation->users()->whereNotIn('user_id', [$user_id])->sum('invitation_count');
        $adminInvitationCount      = $invitation->admins()->sum('invitation_user.invitation_count');



        $invitationPackages = $invitation->invitationPackages;


        if (count($invitationPackages) > 0) {
            foreach ($invitationPackages as $invitationPackage) {

                $package_invitation += ($invitationPackage->package->count + $invitationPackage->package->free_invitations_count + $invitationPackage->count );
            }
        }


        if($package_invitation < ($adminInvitationCount+$userInvitationCount+$count)){
            return false;
        }
        return true;
        
    }
}

if(!function_exists('checkPackageCountForAdmin')){
    function checkPackageCountForAdmin($invitation,$count, $admin_id , $type = 'check' , $user_id = null){

        if($type == 'check'){
        $totalInvitationCount = $invitation->admins()->where('user_id', $admin_id)->first()?->pivot?->invitation_count;
        $userInvitationCount = $invitation->users()->where('invited_by', $admin_id)->sum('invitation_count');

       
        if($totalInvitationCount >= ($userInvitationCount+$count)){
            return true;
        }
        return false;
    }
    elseif($type == 'update'){
        $totalInvitationCount = $invitation->admins()->where('user_id', $admin_id)->first()?->pivot?->invitation_count;
        $userInvitationCount = (int) $invitation->users()->where('invited_by', $admin_id)->whereNot('user_id', [$user_id])->sum('invitation_count');

        if($totalInvitationCount >= ($userInvitationCount + (int) $count)){
            return true;
        }

       
        return false;
       
    }
    }
}


if (!function_exists('checkCountryCode')) {
    function checkCountryCode($phone)
    {
        $data=[];
        $countryCodes = array('+2', '+966','+20','+972','+971');
        foreach($countryCodes as $countryCode) {
            if (substr($phone, 0, strlen($countryCode)) == $countryCode) {
                $data['country_code']=($countryCode=='+2')?'+20':$countryCode;
                $data['phone']=(int)str_replace($countryCode, "",$phone);
                break;
            }else{
                $data['country_code']=auth()->user()?->country_code;
                $data['phone']=(int)str_replace($countryCode, "",$phone);
            }
        }
        return $data;


    }
}
if (!function_exists('getImage')) {
    function getImage($value)
    {
        if ($value) {
            return asset('storage/' . $value);
        }
    }
}

