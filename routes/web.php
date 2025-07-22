<?php

use App\Http\Controllers\cont_declarations;
use App\Http\Controllers\cont_exercises;
use App\Http\Controllers\cont_language;
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

    Route::get('level/create', [cont_levels::class,'create'])->name('level_create');
    Route::get('sub_level/create', [cont_sub_levels::class,'create'])->name('sub_level_create');
    Route::get('theme/create', [cont_themes::class,'create'])->name('theme_create');
    Route::get('declaration/{theme_id}/create', [cont_declarations::class,'create'])->whereNumber('theme_id')->name('declaration_create');
    Route::get('exercise/{theme_id}/create', [cont_exercises::class,'create'])->whereNumber('theme_id')->name('exercise_create');
    Route::get('question/{exercises_id}/create', [cont_questions::class,'create'])->whereNumber('exercises_id')->name('question_create');

    Route::post('level', [cont_levels::class, 'store'])->name('level_store');
    Route::post('sub_level', [cont_sub_levels::class, 'store'])->name('sub_level_store');
    Route::post('theme', [cont_themes::class, 'store'])->name('theme_store');
    Route::post('declaration/{theme_id}/store', [cont_declarations::class, 'store'])->whereNumber('theme_id')->name('declaration_store');
    Route::post('exercise/{theme_id}/store', [cont_exercises::class, 'store'])->whereNumber('theme_id')->name('exercise_store');
    Route::post('question/{exercises_id}/store', [cont_questions::class, 'store'])->whereNumber('exercises_id')->name('question_store');
    
    Route::get('level/{level_id}/edit', [cont_levels::class,'edit'])->name('level_edit');
    Route::get('sub_level/{sub_level_id}/edit', [cont_sub_levels::class,'edit'])->name('sub_level_edit');
    Route::get('theme/{theme_id}/edit', [cont_themes::class,'edit'])->name('theme_edit');
    Route::get('declaration/{declaration_id}/edit', [cont_declarations::class,'edit'])->name('declaration_edit');
    Route::get('exercise/{exercise_id}/edit', [cont_exercises::class,'edit'])->name('exercise_edit');
    Route::get('question/{question_id}/edit', [cont_questions::class,'edit'])->name('question_edit');

    Route::put('level/{level_id}/update', [cont_levels::class, 'update'])->name('level_update');
    Route::put('sub_level/{sub_level_id}/update', [cont_sub_levels::class, 'update'])->name('sub_level_update');
    Route::put('theme/{theme_id}/update', [cont_themes::class, 'update'])->name('theme_update');
    Route::put('declaration/{declaration_id}/update', [cont_declarations::class, 'update'])->name('declaration_update');
    Route::put('exercise/{exercise_id}/update', [cont_exercises::class, 'update'])->name('exercise_update');
    Route::put('question/{question_id}/update', [cont_questions::class, 'update'])->name('question_update');
});

Route::get('/', [cont_user_main::class, 'index'])->name('home');

Route::get('changeLanguage/{lang?}',[cont_language::class,'changeLanguage'])->name('changeLanguage');

Route::get('about', [cont_user_main::class, 'about'])->name('about');
Route::get('contact', [cont_user_main::class, 'contact'])->name('contact');
Route::post('/contactpost', [cont_user_main::class, 'contactpost'])->name('contactpost');
/* Route::post('exercises/{id}/result', [cont_user_main::class, 'exercises_result'])->name('exercises.result'); */

Route::get('tab1/{theme_id}', [cont_user_main::class, 'tab1'])->name('tab1');
Route::get('tab2/{theme_id}', [cont_user_main::class, 'tab2'])->name('tab2');

Route::get('{level_slug}/{sub_level_slug}', [cont_user_main::class, 'themes'])->name('themes');
Route::get('theme/{theme_id}/exercises', [cont_exercises::class, 'index'])->name('exercises');

