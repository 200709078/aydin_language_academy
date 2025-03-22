<?php

use App\Http\Controllers\exams_cont_admin;
use App\Http\Controllers\main_cont_user;
use App\Http\Controllers\questions_cont_admin;
use App\Http\Controllers\user_cont_exercises;
use App\Http\Controllers\user_cont_main;
use App\Http\Controllers\user_cont_themes;
use App\Http\Controllers\user_cont_themes_details;
use App\Http\Middleware\isAdmin_middle;
use Illuminate\Support\Facades\Route;

Route::get('/', [user_cont_main::class, 'index'])->name('home');
Route::get('about', [user_cont_main::class, 'about'])->name('about');
Route::get('contact', [user_cont_main::class, 'contact'])->name('contact');
Route::get('tab1/{theme_id}', [user_cont_main::class, 'tab1'])->name('tab1');
Route::get('tab2/{declaration_id}', [user_cont_main::class, 'tab2'])->name('tab2');
//Route::get('theme/detail/{id}', [user_cont_main::class, 'theme_detail'])->name('theme.detail');

Route::resource('/{level_id}/{sub_level_id}', user_cont_themes::class)->name('index', 'levels');
Route::resource('/theme/{theme_id}/exercises', user_cont_exercises::class);

/* Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
}); */

Route::group(['middleware' => 'auth'], function () {
    Route::get('dashboard', [main_cont_user::class, 'dashboard'])->name('dashboard');
    Route::get('exam/detail/{slug}', [main_cont_user::class, 'exam_detail'])->name('exam.detail');
    Route::get('exam/{slug}', [main_cont_user::class, 'exam_join'])->name('exam.join');
    Route::post('exam/{slug}/result', [main_cont_user::class, 'exam_result'])->name('exam.result');
});

Route::group(['middleware' => ['auth', isAdmin_middle::class], 'prefix' => 'admin'], function () {
    Route::get('exams/{id}', [exams_cont_admin::class, 'destroy'])->whereNumber('id')->name('exams.destroy');
    Route::get('exams/{id}/details', [exams_cont_admin::class, 'show'])->whereNumber('id')->name('exams.details');
    Route::get('exam/{exam_id}/questions/{id}', [questions_cont_admin::class, 'destroy'])->whereNumber('id')->name('questions.destroy');
    Route::resource('exams', exams_cont_admin::class);
    Route::resource('exam/{exam_id}/questions', questions_cont_admin::class);
});
