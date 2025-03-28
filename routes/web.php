<?php

use App\Http\Controllers\cont_exercises;
use App\Http\Controllers\cont_questions;
use App\Http\Controllers\user_cont_main;
use App\Http\Middleware\isAdmin_middle;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('dashboard', [user_cont_main::class, 'dashboard'])->name('dashboard');
    Route::get('exercises/detail/{slug}', [user_cont_main::class, 'exercises_detail'])->name('exercises.detail');
    Route::get('exercises/{slug}', [user_cont_main::class, 'exercises_join'])->name('exercises.join');
    Route::post('exercises/{slug}/result', [user_cont_main::class, 'exercises_result'])->name('exercises.result');
});

Route::group(['middleware' => ['auth', isAdmin_middle::class], 'prefix' => 'admin'], function () {
    Route::get('exercises/{id}', [cont_exercises::class, 'destroy'])->whereNumber('id')->name('exercises.destroy');
    Route::get('exercises/{id}/details', [cont_exercises::class, 'show'])->whereNumber('id')->name('exercises.details');
    Route::get('exercises/{exercises_id}/questions/{id}', [cont_questions::class, 'destroy'])->whereNumber('id')->name('questions.destroy');
    Route::resource('exercises', cont_exercises::class);
    Route::resource('exercises/{exercises_id}/questions', cont_questions::class);
});

Route::get('/', [user_cont_main::class, 'index'])->name('home');
Route::get('about', [user_cont_main::class, 'about'])->name('about');
Route::get('contact', [user_cont_main::class, 'contact'])->name('contact');
Route::post('/contactpost', [user_cont_main::class, 'contactpost'])->name('contactpost');
Route::post('exercises/{id}/result', [user_cont_main::class, 'exercises_result'])->name('exercises.result');

Route::get('tab1/{theme_id}', [user_cont_main::class, 'tab1'])->name('tab1');
Route::get('tab2/{theme_id}', [user_cont_main::class, 'tab2'])->name('tab2');

Route::get('test/{theme_id}', [user_cont_main::class, 'qq']);

Route::get('{level_slug}/{sub_level_slug}', [user_cont_main::class,'themes'])->name('themes');
Route::get('theme/{theme_id}/exercises', [cont_exercises::class,'index'])->name('exercises');
