<?php

use App\Http\Controllers\cont_courses;
use App\Http\Controllers\cont_declarations;
use App\Http\Controllers\cont_exercises;
use App\Http\Controllers\cont_levels;
use App\Http\Controllers\cont_questions;
use App\Http\Controllers\cont_sub_levels;
use App\Http\Controllers\cont_themes;
use App\Http\Controllers\cont_user_main;
use App\Http\Middleware\isAdmin_middle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('exercises/detail/{slug}', [cont_user_main::class, 'exercises_detail'])->name('exercises.detail');
    Route::get('exercises/{slug}', [cont_user_main::class, 'exercises_join'])->name('exercises.join');
    Route::post('exercises/{slug}/result', [cont_user_main::class, 'exercises_result'])->name('exercises.result');
});

Route::group(['middleware' => ['auth', isAdmin_middle::class], 'prefix' => 'admin'], function () {
    Route::view('/', 'dashboard')->name('admin');

    Route::get('settings_list', [cont_user_main::class, 'settings_list'])->name('settings_list');

    Route::get('courses_list', [cont_user_main::class, 'courses_list'])->name('courses_list');
    Route::get('levels_list', [cont_user_main::class, 'levels_list'])->name('levels_list');
    Route::get('sub_levels_list', [cont_user_main::class, 'sub_levels_list'])->name('sub_levels_list');
    Route::get('themes_list', [cont_user_main::class, 'themes_list'])->name('themes_list');
    Route::get('themes/{theme_id}/declarations_list', [cont_user_main::class, 'declarations_list'])->name('declarations_list');
    Route::get('themes/{theme_id}/exercises_list', [cont_user_main::class, 'exercises_list'])->name('exercises_list');
    Route::get('exercise/{exercise_id}/questions_list', [cont_user_main::class, 'questions_list'])->name('questions_list');

    Route::get('exercise/{exercise_id}/question/{question_id}', [cont_questions::class, 'destroy'])->whereNumber('question_id')->name('question_destroy');

/*     Route::delete('course/{course_id}', [cont_courses::class, 'destroy'])->name('course_destroy');
    Route::delete('level/{level_id}', [cont_levels::class, 'destroy'])->name('level_destroy');
    Route::delete('sub_level/{sub_level_id}', [cont_sub_levels::class, 'destroy'])->name('sub_level_destroy');
    Route::delete('theme/{theme_id}', [cont_themes::class, 'destroy'])->name('theme_destroy');
    Route::delete('declaration/{declaration_id}', [cont_declarations::class, 'destroy'])->name('declaration_destroy');
    Route::delete('exercise/{exercise_id}', [cont_exercises::class, 'destroy'])->name('exercise_destroy');
 */
    Route::get('course/create', [cont_courses::class,'create'])->name('course_create');
    Route::get('level/create', [cont_levels::class,'create'])->name('level_create');
    Route::get('sub_level/create', [cont_sub_levels::class,'create'])->name('sub_level_create');
    Route::get('theme/create', [cont_themes::class,'create'])->name('theme_create');
    Route::get('declaration/{theme_id}/create', [cont_declarations::class,'create'])->whereNumber('theme_id')->name('declaration_create');
    Route::get('exercise/{theme_id}/create', [cont_exercises::class,'create'])->whereNumber('theme_id')->name('exercise_create');
    Route::get('question/{exercise_id}/create', [cont_questions::class,'create'])->whereNumber('exercise_id')->name('question_create');

    Route::post('course', [cont_courses::class, 'store'])->name('course_store');
    Route::post('level', [cont_levels::class, 'store'])->name('level_store');
    Route::post('sub_level', [cont_sub_levels::class, 'store'])->name('sub_level_store');
    Route::post('theme', [cont_themes::class, 'store'])->name('theme_store');
    Route::post('declaration/{theme_id}/store', [cont_declarations::class, 'store'])->whereNumber('theme_id')->name('declaration_store');
    Route::post('exercise/{theme_id}/store', [cont_exercises::class, 'store'])->whereNumber('theme_id')->name('exercise_store');
    Route::post('question/{exercise_id}/store', [cont_questions::class, 'store'])->whereNumber('exercise_id')->name('question_store');
    
    Route::get('course/{course_id}/edit', [cont_courses::class,'edit'])->name('course_edit');
    Route::get('level/{level_id}/edit', [cont_levels::class,'edit'])->name('level_edit');
    Route::get('sub_level/{sub_level_id}/edit', [cont_sub_levels::class,'edit'])->name('sub_level_edit');
    Route::get('theme/{theme_id}/edit', [cont_themes::class,'edit'])->name('theme_edit');
    Route::get('declaration/{declaration_id}/edit', [cont_declarations::class,'edit'])->name('declaration_edit');
    Route::get('exercise/{exercise_id}/edit', [cont_exercises::class,'edit'])->name('exercise_edit');
    Route::get('question/{question_id}/edit', [cont_questions::class,'edit'])->name('question_edit');

    Route::put('course/{course_id}/update', [cont_courses::class, 'update'])->name('course_update');
    Route::put('level/{level_id}/update', [cont_levels::class, 'update'])->name('level_update');
    Route::put('sub_level/{sub_level_id}/update', [cont_sub_levels::class, 'update'])->name('sub_level_update');
    Route::put('theme/{theme_id}/update', [cont_themes::class, 'update'])->name('theme_update');
    Route::put('declaration/{declaration_id}/update', [cont_declarations::class, 'update'])->name('declaration_update');
    Route::put('exercise/{exercise_id}/update', [cont_exercises::class, 'update'])->name('exercise_update');
    Route::put('question/{question_id}/update', [cont_questions::class, 'update'])->name('question_update');
});

Route::redirect('/dashboard', '/admin')
    ->middleware(['auth', isAdmin_middle::class])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Yeni Public Frontend Rotaları
|--------------------------------------------------------------------------
|
| Yeni ziyaretçi sitesi için public rotalar. /yeni-site, geçiş sırasında
| doğrudan önizleme bağlantısını korur.
|
*/
Route::view('/', 'frontend.home')->name('home');
Route::view('/basarilarimiz', 'frontend.achievements')->name('frontend.achievements');
Route::view('/kampanyalarimiz', 'frontend.campaigns')->name('frontend.campaigns');
/*
|--------------------------------------------------------------------------
| Yeni Public Frontend — Üyelik Formu Önizlemesi
|--------------------------------------------------------------------------
|
| Bu rota yalnızca yeni üyelik formunun görsel önizlemesini sunar.
| Form gönderimi ve kullanıcı verisi kaydı bu aşamada etkin değildir.
|
*/
Route::view('/uye-ol', 'frontend.register-preview')->name('frontend.register.preview');
/*
|--------------------------------------------------------------------------
| Yeni Public Frontend — Üyelik Formu Önizlemesi Sonu
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Yeni Public Frontend — Giriş Formu Önizlemesi
|--------------------------------------------------------------------------
|
| Bu rota yalnızca yeni giriş formunun görsel önizlemesini sunar.
| Mevcut /login ve Fortify giriş işlemi bu aşamada değiştirilmez.
|
*/
Route::view('/giris-onizleme', 'frontend.login-preview')->name('frontend.login.preview');
/*
|--------------------------------------------------------------------------
| Yeni Public Frontend — Giriş Formu Önizlemesi Sonu
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Yeni Public Frontend — Geçici Alt Sayfa Rotaları
|--------------------------------------------------------------------------
|
| Kurslarımız ve Şubelerimiz menülerindeki alt sayfalar, içerikleri
| hazırlanırken ortak geçici görünümü kullanır.
|
*/
Route::view('/kurslarimiz/okul-oncesi', 'frontend.placeholder', [
    'title' => 'Okul Öncesi',
    'placeholder' => 'BURADA OKUL ÖNCESİ OLACAK.',
])->name('frontend.trainings.preschool');
Route::view('/kurslarimiz/ilkokul', 'frontend.placeholder', [
    'title' => 'İlkokul',
    'placeholder' => 'BURADA İLKOKUL OLACAK.',
])->name('frontend.trainings.primary-school');
Route::view('/kurslarimiz/ortaokul', 'frontend.placeholder', [
    'title' => 'Ortaokul',
    'placeholder' => 'BURADA ORTAOKUL OLACAK.',
])->name('frontend.trainings.middle-school');
Route::view('/kurslarimiz/lise', 'frontend.placeholder', [
    'title' => 'Lise',
    'placeholder' => 'BURADA LİSE OLACAK.',
])->name('frontend.trainings.high-school');
Route::view('/kurslarimiz/yetiskin', 'frontend.placeholder', [
    'title' => 'Yetişkin',
    'placeholder' => 'BURADA YETİŞKİN OLACAK.',
])->name('frontend.trainings.adults');
Route::view('/subelerimiz/ortaca', 'frontend.placeholder', [
    'title' => 'Ortaca',
    'placeholder' => 'BURADA ORTACA OLACAK.',
])->name('frontend.branches.ortaca');
Route::view('/subelerimiz/dalaman', 'frontend.placeholder', [
    'title' => 'Dalaman',
    'placeholder' => 'BURADA DALAMAN OLACAK.',
])->name('frontend.branches.dalaman');
Route::view('/subelerimiz/koycegiz', 'frontend.placeholder', [
    'title' => 'Köyceğiz',
    'placeholder' => 'BURADA KÖYCEĞİZ OLACAK.',
])->name('frontend.branches.koycegiz');
/*
|--------------------------------------------------------------------------
| Yeni Public Frontend — Geçici Alt Sayfa Rotaları Sonu
|--------------------------------------------------------------------------
*/
Route::view('/yeni-site', 'frontend.home')->name('frontend.preview.home');

Route::get('/giris', function (Request $request) {
    if ($request->user()?->type === 'admin') {
        return redirect()->route('admin');
    }

    if ($request->user()) {
        return redirect()->route('home');
    }

    $returnRoute = $request->query('return');
    $publicRoutes = [
        'home' => 'home',
        'frontend.achievements' => 'frontend.achievements',
        'frontend.campaigns' => 'frontend.campaigns',
        'frontend.register.preview' => 'frontend.register.preview',
        'frontend.login.preview' => 'frontend.login.preview',
        'frontend.trainings.preschool' => 'frontend.trainings.preschool',
        'frontend.trainings.primary-school' => 'frontend.trainings.primary-school',
        'frontend.trainings.middle-school' => 'frontend.trainings.middle-school',
        'frontend.trainings.high-school' => 'frontend.trainings.high-school',
        'frontend.trainings.adults' => 'frontend.trainings.adults',
        'frontend.branches.ortaca' => 'frontend.branches.ortaca',
        'frontend.branches.dalaman' => 'frontend.branches.dalaman',
        'frontend.branches.koycegiz' => 'frontend.branches.koycegiz',
        'frontend.preview.home' => 'frontend.preview.home',
    ];

    if (is_string($returnRoute) && array_key_exists($returnRoute, $publicRoutes)) {
        $request->session()->put('url.intended', route($publicRoutes[$returnRoute], absolute: false));
    }

    return redirect()->route('login');
})->name('frontend.login');
/*
|--------------------------------------------------------------------------
| Yeni Public Frontend Rotaları Sonu
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Eski Public Frontend - Yeni Ana Sayfaya Yönlendirmeler
|--------------------------------------------------------------------------
|
| Eski public görünüm ve form akışları artık yayınlanmaz. Bu rotalar,
| eski içeriği göstermeden yeni ana sayfaya döner.
|
*/
Route::redirect('/course/{course_id}', '/')->name('course_detail');
Route::redirect('changeLanguage/{lang?}', '/')->name('changeLanguage');
Route::redirect('about', '/')->name('about');
Route::redirect('contact', '/')->name('contact');
Route::post('/contactpost', fn () => redirect()->route('home'))->name('contactpost');
Route::redirect('tab1/{theme_id}', '/')->name('tab1');
Route::redirect('tab2/{theme_id}', '/')->name('tab2');
Route::redirect('{level_slug}/{sub_level_slug}', '/')->name('themes');
/*
|--------------------------------------------------------------------------
| Eski Public Frontend - Yeni Ana Sayfaya Yönlendirmeler Sonu
|--------------------------------------------------------------------------
*/

// Route::get('theme/{theme_id}/exercises', [cont_exercises::class, 'index'])->name('exercises');
// Route::post('exercises/{id}/result', [cont_user_main::class, 'exercises_result'])->name('exercises.result'); 

