<?php


namespace App\View\Composers;


use App\Helpers\Constant;
use App\Models\Setting;
use Illuminate\View\View;

class HeaderComposer
{

    public function compose(View $view)
    {
        $color=Setting::where('key',Constant::SETTINGS_KEY['Color'])->first()->content;
        $preloader=Setting::where('key','Preloader')->first()->content;

        $view->with([
            'preloader'     => $preloader ,
            'color'         => $color,
        ]);
    }
}

