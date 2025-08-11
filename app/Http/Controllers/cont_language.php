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
        return redirect()->back();
    }
}
