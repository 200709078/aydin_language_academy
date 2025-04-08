<?php

use App\Http\Controllers\cont_declarations;
use App\Http\Controllers\cont_exercises;
use App\Http\Controllers\cont_levels;
use App\Http\Controllers\cont_questions;
use App\Http\Controllers\cont_sub_levels;
use App\Http\Controllers\cont_themes;
use App\Http\Controllers\cont_user_main;
use App\Http\Middleware\isAdmin_middle;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('exercises/detail/{slug}', [cont_user_main::class, 'exercises_detail'])->name('exercises.detail');
    Route::get('exercises/{slug}', [cont_user_main::class, 'exercises_join'])->name('exercises.join');
    Route::post('exercises/{slug}/result', [cont_user_main::class, 'exercises_result'])->name('exercises.result');
});

Route::group(['middleware' => ['auth', isAdmin_middle::class], 'prefix' => 'admin'], function () {
    Route::get('levels_list', [cont_user_main::class, 'levels_list'])->name('levels_list');
    Route::get('sub_levels_list', [cont_user_main::class, 'sub_levels_list'])->name('sub_levels_list');
    Route::get('themes_list', [cont_user_main::class, 'themes_list'])->name('themes_list');
    Route::get('themes/{theme_id}/declarations_list', [cont_user_main::class, 'declarations_list'])->name('declarations_list');
    Route::get('themes/{theme_id}/exercises_list', [cont_user_main::class, 'exercises_list'])->name('exercises_list');
    Route::get('exercise/{exercise_id}/questions_list', [cont_user_main::class, 'questions_list'])->name('questions_list');


    Route::get('exercise/{exercise_id}/question/{question_id}', [cont_questions::class, 'destroy'])->whereNumber('question_id')->name('question_destroy');
    Route::get('exercise/{exercise_id}', [cont_exercises::class, 'destroy'])->whereNumber('exercise_id')->name('exercises_destroy');
    Route::get('declaration/{declaration_id}', [cont_declarations::class, 'destroy'])->whereNumber('declaration_id')->name('declaration_destroy');
    Route::get('theme/{theme_id}', [cont_themes::class, 'destroy'])->whereNumber('theme_id')->name('theme_destroy');
    Route::get('level/{level_id}', [cont_levels::class, 'destroy'])->whereNumber('level_id')->name('level_destroy');
    Route::get('sub_level/{sub_level_id}', [cont_sub_levels::class, 'destroy'])->whereNumber('sub_level_id')->name('sub_level_destroy');

    /*Route::get('exercises/{id}/details', [cont_exercises::class, 'show'])->whereNumber('id')->name('exercises.details');

         Route::resource('exercises', cont_exercises::class);
        Route::resource('levels', cont_levels::class);
        Route::resource('sub_levels', cont_sub_levels::class);
        Route::resource('themes', cont_themes::class);
        Route::resource('declarations', cont_declarations::class);
        Route::resource('exercises/{exercises_id}/questions', cont_questions::class); */
});

Route::get('/', [cont_user_main::class, 'index'])->name('home');
Route::get('about', [cont_user_main::class, 'about'])->name('about');
Route::get('contact', [cont_user_main::class, 'contact'])->name('contact');
Route::post('/contactpost', [cont_user_main::class, 'contactpost'])->name('contactpost');
Route::post('exercises/{id}/result', [cont_user_main::class, 'exercises_result'])->name('exercises.result');

Route::get('tab1/{theme_id}', [cont_user_main::class, 'tab1'])->name('tab1');
Route::get('tab2/{theme_id}', [cont_user_main::class, 'tab2'])->name('tab2');

Route::get('{level_slug}/{sub_level_slug}', [cont_user_main::class, 'themes'])->name('themes');
Route::get('theme/{theme_id}/exercises', [cont_exercises::class, 'index'])->name('exercises');
