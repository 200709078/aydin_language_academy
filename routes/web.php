<?php

use App\Http\Controllers\cont_declarations;
use App\Http\Controllers\cont_exercises;
use App\Http\Controllers\cont_levels;
use App\Http\Controllers\cont_questions;
use App\Http\Controllers\cont_reviews;
use App\Http\Controllers\cont_sub_levels;
use App\Http\Controllers\cont_themes;
use App\Http\Controllers\cont_user_main;
use App\Http\Controllers\AdminContactMessageController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\ExerciseAttemptController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProgramFinderController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\ThemeListController;
use App\Http\Controllers\ExerciseAttemptReviewController;
use App\Http\Controllers\PlacementTestAttemptController;
use App\Http\Controllers\PlacementTestLevelController;
use App\Http\Controllers\PlacementTestQuestionController;
use App\Http\Controllers\PlacementTestQuestionContentController;
use App\Http\Controllers\PlacementTestReviewController;
use App\Http\Middleware\isAdmin_middle;
use App\Support\FrontendReturnRoutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', isAdmin_middle::class], 'prefix' => 'admin'], function () {
    Route::view('/', 'dashboard')->name('admin');
    Route::view('profile', 'profile.show')->name('admin.profile.show');

    Route::get('placement-test/levels', [PlacementTestLevelController::class, 'index'])
        ->name('placement_test_levels_list');
    Route::get('placement-test/levels/{placementTestLevel}/edit', [PlacementTestLevelController::class, 'edit'])
        ->whereNumber('placementTestLevel')
        ->name('placement_test_levels_edit');
    Route::put('placement-test/levels/{placementTestLevel}', [PlacementTestLevelController::class, 'update'])
        ->whereNumber('placementTestLevel')
        ->name('placement_test_levels_update');

    Route::get('placement-test/question-contents', [PlacementTestQuestionContentController::class, 'index'])
        ->name('placement_test_question_contents_list');
    Route::get('placement-test/question-contents/create', [PlacementTestQuestionContentController::class, 'create'])
        ->name('placement_test_question_contents_create');
    Route::post('placement-test/question-contents', [PlacementTestQuestionContentController::class, 'store'])
        ->name('placement_test_question_contents_store');
    Route::get('placement-test/question-contents/{placementTestQuestionContent}/edit', [PlacementTestQuestionContentController::class, 'edit'])
        ->whereNumber('placementTestQuestionContent')
        ->name('placement_test_question_contents_edit');
    Route::put('placement-test/question-contents/{placementTestQuestionContent}', [PlacementTestQuestionContentController::class, 'update'])
        ->whereNumber('placementTestQuestionContent')
        ->name('placement_test_question_contents_update');
    Route::delete('placement-test/question-contents/{placementTestQuestionContent}', [PlacementTestQuestionContentController::class, 'destroy'])
        ->whereNumber('placementTestQuestionContent')
        ->name('placement_test_question_contents_destroy');
    Route::get('placement-test/question-contents/{placementTestQuestionContent}/media', [PlacementTestQuestionContentController::class, 'media'])
        ->whereNumber('placementTestQuestionContent')
        ->name('placement_test_question_contents_media');

    Route::get('placement-test/questions', [PlacementTestQuestionController::class, 'index'])
        ->name('placement_test_questions_list');
    Route::get('placement-test/questions/create', [PlacementTestQuestionController::class, 'create'])
        ->name('placement_test_questions_create');
    Route::post('placement-test/questions', [PlacementTestQuestionController::class, 'store'])
        ->name('placement_test_questions_store');
    Route::get('placement-test/questions/{placementTestQuestion}/edit', [PlacementTestQuestionController::class, 'edit'])
        ->whereNumber('placementTestQuestion')
        ->name('placement_test_questions_edit');
    Route::put('placement-test/questions/{placementTestQuestion}', [PlacementTestQuestionController::class, 'update'])
        ->whereNumber('placementTestQuestion')
        ->name('placement_test_questions_update');
    Route::delete('placement-test/questions/{placementTestQuestion}', [PlacementTestQuestionController::class, 'destroy'])
        ->whereNumber('placementTestQuestion')
        ->name('placement_test_questions_destroy');

    Route::get('placement-test/attempts', [PlacementTestReviewController::class, 'index'])
        ->name('placement_test_attempts_list');
    Route::get('placement-test/attempts/{placementTest}/contents/{placementTestLevelResultContent}/media', [PlacementTestReviewController::class, 'media'])
        ->whereNumber('placementTest')
        ->whereNumber('placementTestLevelResultContent')
        ->name('placement_test_attempts_media');
    Route::get('placement-test/attempts/{placementTest}', [PlacementTestReviewController::class, 'show'])
        ->whereNumber('placementTest')
        ->name('placement_test_attempts_show');
    Route::put('placement-test/attempts/{placementTest}/approve', [PlacementTestReviewController::class, 'approve'])
        ->whereNumber('placementTest')
        ->name('placement_test_attempts_approve');

    Route::get('exercise-attempts', [ExerciseAttemptReviewController::class, 'index'])
        ->name('admin.exercise-attempts.index');
    Route::get('exercise-attempts/{exerciseAttempt}', [ExerciseAttemptReviewController::class, 'show'])
        ->whereNumber('exerciseAttempt')
        ->name('admin.exercise-attempts.show');

    Route::get('levels_list', [cont_user_main::class, 'levels_list'])->name('levels_list');
    Route::get('sub_levels_list', [cont_user_main::class, 'sub_levels_list'])->name('sub_levels_list');
    Route::get('themes_list', [cont_user_main::class, 'themes_list'])->name('themes_list');
    Route::get('reviews_list', [cont_reviews::class, 'index'])->name('reviews_list');
    Route::get('review/{review_id}/edit', [cont_reviews::class, 'edit'])->whereNumber('review_id')->name('review_edit');
    Route::put('review/{review_id}/update', [cont_reviews::class, 'update'])->whereNumber('review_id')->name('review_update');
    Route::get('messages', [AdminContactMessageController::class, 'index'])->name('admin.messages.index');
    Route::get('messages/{message}', [AdminContactMessageController::class, 'show'])
        ->whereNumber('message')
        ->name('admin.messages.show');
    Route::put('messages/{message}/status', [AdminContactMessageController::class, 'updateStatus'])
        ->whereNumber('message')
        ->name('admin.messages.status.update');
    Route::post('messages/{message}/replies', [AdminContactMessageController::class, 'reply'])
        ->whereNumber('message')
        ->name('admin.messages.replies.store');
    Route::get('themes/{theme_id}/declarations_list', [cont_user_main::class, 'declarations_list'])->name('declarations_list');
    Route::get('themes/{theme_id}/exercises_list', [cont_user_main::class, 'exercises_list'])->name('exercises_list');
    Route::get('exercise/{exercise_id}/questions_list', [cont_user_main::class, 'questions_list'])->name('questions_list');
    Route::get('level/create', [cont_levels::class,'create'])->name('level_create');
    Route::get('sub_level/create', [cont_sub_levels::class,'create'])->name('sub_level_create');
    Route::get('theme/create', [cont_themes::class,'create'])->name('theme_create');
    Route::get('declaration/{theme_id}/create', [cont_declarations::class,'create'])->whereNumber('theme_id')->name('declaration_create');
    Route::get('exercise/{theme_id}/create', [cont_exercises::class,'create'])->whereNumber('theme_id')->name('exercise_create');
    Route::get('question/{exercise_id}/create', [cont_questions::class,'create'])->whereNumber('exercise_id')->name('question_create');

    Route::post('level', [cont_levels::class, 'store'])->name('level_store');
    Route::post('sub_level', [cont_sub_levels::class, 'store'])->name('sub_level_store');
    Route::post('theme', [cont_themes::class, 'store'])->name('theme_store');
    Route::post('declaration/{theme_id}/store', [cont_declarations::class, 'store'])->whereNumber('theme_id')->name('declaration_store');
    Route::post('exercise/{theme_id}/store', [cont_exercises::class, 'store'])->whereNumber('theme_id')->name('exercise_store');
    Route::post('question/{exercise_id}/store', [cont_questions::class, 'store'])->whereNumber('exercise_id')->name('question_store');
    
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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/basarilarimiz', 'frontend.achievements')->name('frontend.achievements');
Route::view('/kampanyalarimiz', 'frontend.campaigns')->name('frontend.campaigns');
Route::get('/yorumlar', [ReviewController::class, 'index'])->name('frontend.reviews');
Route::view('/yorumlarim', 'frontend.my-reviews')
    ->middleware('auth')
    ->name('frontend.my-reviews');
Route::get('/seviye-tespit-sinavi', [PlacementTestAttemptController::class, 'landing'])
    ->name('frontend.placement-test');
Route::get('/sana-uygun-programi-bul', [ProgramFinderController::class, 'show'])
    ->name('frontend.program-finder');
Route::post('/sana-uygun-programi-bul', [ProgramFinderController::class, 'recommend'])
    ->middleware('throttle:20,1')
    ->name('frontend.program-finder.recommend');

Route::middleware('auth')->prefix('seviye-tespit-sinavi')->group(function (): void {
    Route::post('/baslat', [PlacementTestAttemptController::class, 'start'])
        ->name('frontend.placement-test.start');
    Route::get('/sinavlarim', [PlacementTestAttemptController::class, 'history'])
        ->name('frontend.placement-test.attempts');
    Route::get('/sinavlarim/{placementTest}/icerikler/{placementTestLevelResultContent}/medya', [PlacementTestAttemptController::class, 'historyMedia'])
        ->whereNumber('placementTest')
        ->whereNumber('placementTestLevelResultContent')
        ->name('frontend.placement-test.attempts.media');
    Route::get('/sinavlarim/{placementTest}', [PlacementTestAttemptController::class, 'showHistory'])
        ->whereNumber('placementTest')
        ->name('frontend.placement-test.attempts.show');
    Route::get('/sinav/{placementTest}', [PlacementTestAttemptController::class, 'resume'])
        ->whereNumber('placementTest')
        ->name('frontend.placement-test.exam');
    Route::get('/sinav/{placementTest}/sorular/{placementTestLevelQuestion}', [PlacementTestAttemptController::class, 'showQuestion'])
        ->whereNumber('placementTest')
        ->whereNumber('placementTestLevelQuestion')
        ->name('frontend.placement-test.question');
    Route::put('/sinav/{placementTest}/sorular/{placementTestLevelQuestion}', [PlacementTestAttemptController::class, 'saveAnswer'])
        ->whereNumber('placementTest')
        ->whereNumber('placementTestLevelQuestion')
        ->name('frontend.placement-test.answer');
    Route::get('/sinav/{placementTest}/tamamlandi', [PlacementTestAttemptController::class, 'completed'])
        ->whereNumber('placementTest')
        ->name('frontend.placement-test.completed');
    Route::get('/sinav/{placementTest}/icerikler/{placementTestLevelResultContent}/medya', [PlacementTestAttemptController::class, 'media'])
        ->whereNumber('placementTest')
        ->whereNumber('placementTestLevelResultContent')
        ->name('frontend.placement-test.media');
});

Route::view('/uye-ol', 'frontend.auth.register')
    ->middleware('guest')
    ->name('frontend.register');

Route::view('/kurslarimiz/okul-oncesi', 'frontend.courses.preschool')->name('frontend.courses.preschool');
Route::view('/kurslarimiz/ilkokul', 'frontend.courses.primary-school')->name('frontend.courses.primary-school');
Route::view('/kurslarimiz/ortaokul', 'frontend.courses.middle-school')->name('frontend.courses.middle-school');
Route::view('/kurslarimiz/lise', 'frontend.courses.high-school')->name('frontend.courses.high-school');
Route::view('/kurslarimiz/genel-ingilizce', 'frontend.courses.general-english')->name('frontend.courses.general-english');

Route::view('/kurslarimiz/ielts', 'frontend.courses.ielts')->name('frontend.courses.ielts');

Route::view('/kurslarimiz/yks-dil', 'frontend.courses.yks-dil')->name('frontend.courses.yks-dil');
Route::view('/kurslarimiz/yds-yokdil', 'frontend.courses.yds-yokdil')->name('frontend.courses.yds-yokdil');
Route::view('/kurslarimiz/toefl', 'frontend.courses.toefl')->name('frontend.courses.toefl');
Route::view('/kurslarimiz/pte-academic', 'frontend.courses.pte-academic')->name('frontend.courses.pte-academic');
Route::view('/kurslarimiz/test-of-english', 'frontend.courses.test-of-english')->name('frontend.courses.test-of-english');
Route::view('/kurslarimiz/sat', 'frontend.courses.sat')->name('frontend.courses.sat');
Route::view('/kurslarimiz/konusma-kulupleri', 'frontend.courses.konusma-kulupleri')->name('frontend.courses.speaking-clubs');
Route::get('/temalar/{level_slug}/{sub_level_slug}', [ThemeListController::class, 'index'])
    ->middleware('auth')
    ->name('frontend.themes.list');
Route::get('/tema/{theme_id}', [ThemeListController::class, 'show'])
    ->whereNumber('theme_id')
    ->middleware('auth')
    ->name('frontend.themes.detail');
Route::middleware('auth')->prefix('alistirmalar')->group(function (): void {
    Route::post('/{exercise}/baslat', [ExerciseAttemptController::class, 'start'])
        ->whereNumber('exercise')
        ->name('frontend.exercise-attempts.start');
    Route::get('/{exercise}/denemeler', [ExerciseAttemptController::class, 'history'])
        ->whereNumber('exercise')
        ->name('frontend.exercise-attempts.index');
    Route::get('/{exercise}/denemeler/{exerciseAttempt}', [ExerciseAttemptController::class, 'show'])
        ->whereNumber('exercise')
        ->whereNumber('exerciseAttempt')
        ->name('frontend.exercise-attempts.show');
    Route::put('/{exercise}/denemeler/{exerciseAttempt}/sorular/{question}', [ExerciseAttemptController::class, 'saveAnswer'])
        ->whereNumber('exercise')
        ->whereNumber('exerciseAttempt')
        ->whereNumber('question')
        ->name('frontend.exercise-attempts.answer');
    Route::post('/{exercise}/denemeler/{exerciseAttempt}/tamamla', [ExerciseAttemptController::class, 'complete'])
        ->whereNumber('exercise')
        ->whereNumber('exerciseAttempt')
        ->name('frontend.exercise-attempts.complete');
});
Route::view('/dokumanlar', 'frontend.documents')->name('frontend.documents');
Route::view('/subelerimiz/ortaca', 'frontend.branches.ortaca')->name('frontend.branches.ortaca');
Route::view('/subelerimiz/dalaman', 'frontend.branches.dalaman')->name('frontend.branches.dalaman');
Route::view('/subelerimiz/koycegiz', 'frontend.branches.koycegiz')->name('frontend.branches.koycegiz');
Route::get('/iletisim/{branch?}', [ContactController::class, 'show'])
    ->where('branch', 'ortaca|dalaman|koycegiz')
    ->name('frontend.contact');
Route::post('/iletisim/{branch?}', [ContactController::class, 'submit'])
    ->where('branch', 'ortaca|dalaman|koycegiz')
    ->middleware('throttle:5,1')
    ->name('frontend.contact.submit');

Route::get('/giris', function (Request $request) {
    if ($request->user()?->type === 'admin') {
        return redirect()->route('admin');
    }

    if ($request->user()) {
        return redirect()->route('home');
    }

    $returnRoute = FrontendReturnRoutes::resolve($request->query('return'));

    if ($returnRoute) {
        $request->session()->put('url.intended', route($returnRoute, absolute: false));
    }

    return redirect()->route('login');
})->name('frontend.login');

Route::get('changeLanguage/{lang}', [cont_user_main::class, 'changeLanguage'])->name('changeLanguage');
Route::redirect('contact', '/iletisim')->name('contact');
