<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class cont_language extends Controller
{
    public function changeLanguage($lang)
    {
        session()->put('locale', $lang);
        //App::setLocale(session()->get('app_locale', 'en'));
        return redirect()->back();
    }
}
