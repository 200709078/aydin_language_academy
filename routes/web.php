<?php

use App\Http\Controllers\cont_courses;
use App\Http\Controllers\cont_declarations;
use App\Http\Controllers\cont_exercises;
use App\Http\Controllers\cont_levels;
use App\Http\Controllers\cont_questions;
use App\Http\Controllers\cont_sub_levels;
use App\Http\Controllers\cont_themes;
use App\Http\Controllers\cont_user_main;
use App\Http\Controllers\PlacementTestAttemptController;
use App\Http\Controllers\PlacementTestLevelController;
use App\Http\Controllers\PlacementTestQuestionController;
use App\Http\Controllers\PlacementTestQuestionContentController;
use App\Http\Middleware\isAdmin_middle;
use App\Support\FrontendReturnRoutes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('exercises/detail/{slug}', [cont_user_main::class, 'exercises_detail'])->name('exercises.detail');
    Route::get('exercises/{slug}', [cont_user_main::class, 'exercises_join'])->name('exercises.join');
    Route::post('exercises/{slug}/result', [cont_user_main::class, 'exercises_result'])->name('exercises.result');
});

Route::group(['middleware' => ['auth', isAdmin_middle::class], 'prefix' => 'admin'], function () {
    Route::view('/', 'dashboard')->name('admin');

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
Route::get('/seviye-tespit-sinavi', [PlacementTestAttemptController::class, 'landing'])
    ->name('frontend.placement-test');

Route::middleware('auth')->prefix('seviye-tespit-sinavi')->group(function (): void {
    Route::post('/baslat', [PlacementTestAttemptController::class, 'start'])
        ->name('frontend.placement-test.start');
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
/*
|--------------------------------------------------------------------------
| Yeni Public Frontend — Üyelik Formu
|--------------------------------------------------------------------------
|
| /uye-ol, mevcut Fortify kayıt ekranıyla aynı üyelik görünümünü sunar.
| Form gönderimi mevcut /register rotası üzerinden yapılır.
|
*/
Route::view('/uye-ol', 'frontend.auth.register')
    ->middleware('guest')
    ->name('frontend.register');
/*
|--------------------------------------------------------------------------
| Yeni Public Frontend — Üyelik Formu Sonu
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
$sharedCourseImageText = 'Tüm gruplarda, eğitim süreçlerini daha verimli hale getirmek için uzman eğitmenlerimiz tarafından seçilen modern ve ilgi çekici kaynaklar kullanılır. Her yaş grubunun farklı ihtiyaçlarına yönelik olarak kişiye özel eğitim yöntemleri uygulanır. Öğrencilerin seviyeleri, ilgi alanları ve öğrenme hızlarına göre eğitim programları şekillendirilir. Bu sayede, her öğrenci en iyi şekilde gelişir ve dil becerileri en üst düzeye çıkarılır.';

Route::view('/kurslarimiz/okul-oncesi', 'frontend.placeholder', [
    'title' => 'Okul Öncesi',
    'imageBelowText' => $sharedCourseImageText,
    'placeholder' => <<<'TEXT'
Okul öncesi öğrencilerimiz için hazırlanan İngilizce eğitim programlarımız, dil öğrenme sürecini eğlenceli ve etkileşimli bir hale getirir. Bu yaş grubunun doğal öğrenme yetenekleri göz önünde bulundurularak, dil becerilerini oyunlar, şarkılar ve çeşitli yaratıcı etkinliklerle geliştirmeyi hedefliyoruz.
TEXT,
    'checkedSections' => [
        [
            'heading' => 'Seviye Tespit ile Kişiye Özel Eğitim',
            'text' => 'Okul öncesi öğrencilerimiz için seviye tespit sınavı uygulanmaz, çünkü bu yaş grubunda dil öğrenimi doğal bir süreç olarak ele alınır. Öğrenciler, yaşlarına uygun materyallerle eğlenceli bir şekilde dil becerilerini geliştirir. Ancak, öğrencinin dil gelişimi ve ihtiyaçları, öğretmenlerimiz tarafından sürekli izlenir ve her çocuğa özel eğitim yaklaşımı benimsenir.',
        ],
        [
            'heading' => 'Dil Becerilerini Eğlenceli Yöntemlerle Geliştirme',
            'text' => 'Okul öncesi programımız, İngilizceyi öğrenmeye yönelik temel becerileri geliştirmeye odaklanır. Çocuklar, şarkılar, hikayeler ve oyunlar aracılığıyla dilin temellerini öğrenirken, özellikle dinleme, konuşma ve kelime dağarcığını geliştirme üzerine yoğunlaşılır. Bu sayede, çocuklar doğal bir şekilde İngilizceyi günlük yaşamlarında kullanmaya başlarlar.',
        ],
        [
            'heading' => 'Uzman Eğitmenler ve Oyun Bazlı Kaynaklar',
            'text' => 'Eğitim materyallerimiz, uzman eğitmenlerimiz tarafından seçilen renkli ve eğlenceli içeriklerden oluşur. Hikayeler, şarkılar ve resimli kitaplar, çocukların dikkatini çekmek ve onların öğrenmesini desteklemek için kullanılmaktadır. Öğrenme süreci oyun tabanlı olduğundan, çocuklar doğal bir şekilde İngilizceyi keşfeder ve eğlenerek öğrenir.',
        ],
        [
            'heading' => 'Grup Dersleri',
            'text' => 'Çocukların sosyal becerilerini geliştirirken, dil öğrenme süreçlerine aktif katılımlarını sağlar.',
        ],
        [
            'heading' => 'Özel Dersler',
            'text' => 'Çocuğun bireysel hızına ve ihtiyaçlarına göre uyarlanmış kişisel bir öğrenme deneyimi sunar.',
        ],
    ],
])->name('frontend.trainings.preschool');
Route::view('/kurslarimiz/ilkokul', 'frontend.placeholder', [
    'title' => 'İlkokul',
    'imageBelowText' => $sharedCourseImageText,
    'placeholder' => 'İlkokul öğrencilerimize yönelik programlarımız, dil öğrenimini eğlenceli ve kolay bir süreç haline getirerek onların İngilizceye erken yaşta sevgiyle bağlanmasını hedefliyor. Derslerimiz, oyunlar, şarkılar ve görsel materyallerle zenginleştirilerek çocukların dil öğrenme becerilerini doğal bir şekilde geliştirmelerini sağlıyor.',
    'checkedSections' => [
        [
            'heading' => 'Seviye Tespit ile Kişiye Özel Yaklaşım',
            'text' => 'Her öğrencimizin dil seviyesini anlamak için bir seviye tespit sınavı uyguluyoruz. Bu sayede öğrencilerimizi ihtiyaçlarına ve mevcut seviyelerine uygun gruplara yerleştirerek daha etkili bir eğitim ortamı oluşturuyoruz.',
        ],
        [
            'heading' => 'Dil Becerilerini Eğlenceli Yöntemlerle Geliştirme',
            'text' => 'İlkokul öğrencilerine yönelik programımız, İngilizce öğrenimini dört temel beceri (dinleme, konuşma, okuma, yazma) çerçevesinde ele alır. Ancak, bu yaş grubunun ihtiyaçlarına uygun olarak özellikle konuşma ve dinleme becerilerine ağırlık verilir.',
        ],
        [
            'heading' => 'Uzman Eğitmenler ve Çocuk Dostu Kaynaklar',
            'text' => 'Ders materyallerimiz, uzman eğitmenlerimiz tarafından seçilen, renkli ve eğlenceli içeriklerden oluşur. Çocukların ilgisini çekecek hikayeler, görseller ve interaktif etkinliklerle öğrenme sürecini eğlenceli hale getiriyoruz.',
        ],
        [
            'heading' => 'Grup Dersleri',
            'text' => 'Çocukların sosyal becerilerini geliştirmelerine ve diğer öğrencilerle iletişim kurarak öğrenmelerine yardımcı olur.',
        ],
        [
            'heading' => 'Özel Dersler',
            'text' => 'Daha kişiselleştirilmiş bir yaklaşım sunarak çocuğun bireysel ihtiyaçlarına odaklanır.',
        ],
    ],
])->name('frontend.trainings.primary-school');
Route::view('/kurslarimiz/ortaokul', 'frontend.placeholder', [
    'title' => 'Ortaokul',
    'imageBelowText' => $sharedCourseImageText,
    'placeholder' => 'Ortaokul öğrencilerimize özel İngilizce programlarımız, akademik başarıyı desteklemekle birlikte öğrencilerimizin İngilizceye olan güvenlerini artırmayı ve dil becerilerini daha ileri bir seviyeye taşımayı amaçlıyor.',
    'checkedSections' => [
        [
            'heading' => 'Seviye Tespit ile Kişiselleştirilmiş Eğitim',
            'text' => 'Her öğrencinin mevcut İngilizce seviyesini belirlemek için bir seviye tespit sınavı gerçekleştiriyoruz. Bu sayede, öğrencilerimizi dil seviyelerine uygun gruplara yönlendirerek etkili bir öğrenme süreci sağlıyoruz.',
        ],
        [
            'heading' => 'Gelişmiş Dil Becerileri Odaklı Eğitim',
            'text' => 'Bu yaş grubunda, İngilizce eğitimi okuma, yazma, dinleme ve konuşma becerilerinin dengeli bir şekilde geliştirilmesine odaklanır. Özellikle akademik İngilizceyi destekleyen programlarımızla, öğrencilerin okuma-anlama ve yazılı ifade becerilerinin güçlenmesini sağlıyoruz.',
        ],
        [
            'heading' => 'Uzman Kadro ve Güncel Kaynaklar',
            'text' => 'Kullanılan tüm eğitim materyalleri, uzman eğitmenlerimiz tarafından seçilmiş ve öğrencilerin bu yaş grubundaki ihtiyaçlarına göre hazırlanmıştır. Modern içeriklerle öğrencilerimizin ilgisini çekerken, öğrenim süreçlerini hızlandırıyoruz.',
        ],
        [
            'heading' => 'Grup Dersleri',
            'text' => 'Takım çalışmasını teşvik eden ve sosyal öğrenmeyi destekleyen bir ortam sağlar.',
        ],
        [
            'heading' => 'Özel Dersler',
            'text' => 'Öğrencinin ihtiyaçlarına göre birebir ilgiyle hazırlanır.',
        ],
    ],
])->name('frontend.trainings.middle-school');
Route::view('/kurslarimiz/lise', 'frontend.placeholder', [
    'title' => 'Lise',
    'imageBelowText' => $sharedCourseImageText,
    'placeholder' => 'Lise öğrencilerimize yönelik İngilizce programlarımız, onların gelecekteki akademik ve profesyonel hayatlarına güçlü bir başlangıç yapmalarını sağlamak üzere tasarlanmıştır. İngilizceyi etkili bir şekilde öğrenen lise öğrencileri, hem sınav başarılarında hem de uluslararası platformlarda avantaj kazanır.',
    'checkedSections' => [
        [
            'heading' => 'Seviye Tespit ile Doğru Başlangıç',
            'text' => 'Her öğrencimiz, tarafımızca uygulanan seviye tespit sınavıyla değerlendirilir. Bu sınav sonuçlarına göre, öğrencilerimiz seviyelerine uygun sınıflara yerleştirilerek etkili bir dil eğitimi alır.',
        ],
        [
            'heading' => 'İleri Düzey Dil Becerileri Eğitimi',
            'text' => 'Lise öğrencilerine yönelik derslerimiz, akademik ve profesyonel İngilizce kullanımını geliştirmeye odaklanır. Derslerde okuma, yazma, dinleme ve konuşma becerileri üzerinde çalışılırken, özellikle sınav teknikleri, sunum yapma ve eleştirel düşünce gibi ileri düzey becerilere de yer verilir.',
        ],
        [
            'heading' => 'Kaliteli Kaynaklar ve Uzman Eğitmenler',
            'text' => 'Lise seviyesine uygun olarak seçilmiş akademik ve güncel kaynaklar, uzman eğitmenlerimizin rehberliğinde kullanılır. Öğrencilerimizin hedeflerine ulaşmalarını sağlayacak materyallerle öğrenim sürecini destekliyoruz.',
        ],
        [
            'heading' => 'Grup Dersleri',
            'text' => 'Akademik başarı ve grup etkileşimiyle öğrenmeyi teşvik eder.',
        ],
        [
            'heading' => 'Özel Dersler',
            'text' => 'Kişiselleştirilmiş bir programla öğrencinin hedeflerine odaklanır.',
        ],
    ],
])->name('frontend.trainings.high-school');
Route::view('/kurslarimiz/genel-ingilizce', 'frontend.placeholder', [
    'title' => 'Genel İngilizce',
    'imageBelowText' => $sharedCourseImageText,
    'placeholder' => 'İngilizce öğrenme yolculuğunuzda, uluslararası standartlara uygun eğitim programlarımızla yanınızdayız! Kurslarımız, Avrupa Dilleri Ortak Çerçeve Programı (CEFR) verilerine dayalı olarak tasarlanmıştır ve her seviyede ihtiyacınıza özel çözümler sunar.',
    'checkedSections' => [
        [
            'heading' => 'A1-A2 seviyeleri',
            'text' => 'Toplamda 90-110 ders saati süren programlarla dil öğreniminde sağlam bir temel oluşturabilirsiniz.',
        ],
        [
            'heading' => 'B seviyeleri',
            'text' => '180 ders saati içeren kapsamlı bir eğitimle daha akıcı ve özgüvenli bir şekilde iletişim kurmayı öğrenebilirsiniz.',
        ],
        [
            'heading' => 'Dört Temel Beceri Odaklı Eğitim',
            'text' => 'Derslerimiz, CEFR’in öngördüğü şekilde dört temel dil becerisini – reading, writing, listening ve speaking – geliştirmeye odaklanır. Bu sistem, hem teorik hem de pratik beceriler kazanmanıza olanak tanır.',
        ],
        [
            'heading' => 'Yerli ve Native Öğretmenlerden Eğitim',
            'text' => 'Programlarımızda, hem yerli hem de anadili İngilizce olan deneyimli öğretmenler görev alır. Bu sayede, dil öğrenme sürecinizi hem yerel ihtiyaçlarınıza hem de uluslararası standartlara uygun bir şekilde destekliyoruz.',
        ],
        [
            'heading' => 'Neden Biz?',
            'text' => 'İngilizce öğrenmek, sadece bir dil öğrenmek değil, aynı zamanda yeni fırsatlara kapı açmaktır. Seyahat, kariyer ya da kişisel gelişim hedefleriniz ne olursa olsun, size en uygun yöntemi sunuyor ve başarı yolculuğunuzda yanınızda oluyoruz.',
        ],
    ],
])->name('frontend.trainings.general-english');

$demoTrainingPages = [
    'ielts' => [
        'title' => 'IELTS Hazırlık',
        'placeholder' => 'IELTS sınavına yönelik demo hazırlık programımız, akademik İngilizce becerilerinizi ve sınav stratejilerinizi geliştirmeye odaklanır.',
        'checkedSections' => [
            ['heading' => 'Madde 1', 'text' => 'Madde 1 içeriği burada.'],
            ['heading' => 'Madde 2', 'text' => 'Madde 2 içeriği burada.'],
        ],
    ],
    'yks-dil' => [
        'title' => 'YKS-DİL Hazırlık',
        'placeholder' => 'YKS-DİL sınavına hazırlanan öğrenciler için kelime, dil bilgisi, okuma ve soru çözme becerilerini destekleyen demo program.',
        'checkedSections' => [
            ['heading' => 'Madde 1', 'text' => 'Madde 1 içeriği burada.'],
            ['heading' => 'Madde 2', 'text' => 'Madde 2 içeriği burada.'],
        ],
    ],
    'yds-yokdil' => [
        'title' => 'YDS / YÖKDİL',
        'placeholder' => 'YDS ve YÖKDİL sınavlarına hazırlık için demo içerik ve çalışma programı.',
        'checkedSections' => [
            ['heading' => 'Madde 1', 'text' => 'Madde 1 içeriği burada.'],
            ['heading' => 'Madde 2', 'text' => 'Madde 2 içeriği burada.'],
        ],
    ],
    'toefl' => [
        'title' => 'TOEFL',
        'placeholder' => 'TOEFL sınavının temel bölümlerine ve akademik iletişim becerilerine yönelik demo hazırlık içeriği.',
        'checkedSections' => [
            ['heading' => 'Madde 1', 'text' => 'Madde 1 içeriği burada.'],
            ['heading' => 'Madde 2', 'text' => 'Madde 2 içeriği burada.'],
        ],
    ],
    'pte-academic' => [
        'title' => 'PTE Academic',
        'placeholder' => 'PTE Academic sınavına hazırlık için konuşma, yazma, okuma ve dinleme becerilerini geliştiren demo içerik.',
        'checkedSections' => [
            ['heading' => 'Madde 1', 'text' => 'Madde 1 içeriği burada.'],
            ['heading' => 'Madde 2', 'text' => 'Madde 2 içeriği burada.'],
        ],
    ],
    'test-of-english' => [
        'title' => 'Test of English',
        'placeholder' => 'Test of English hedeflerinize uygun dil becerilerini ve sınav yaklaşımını geliştirmeye yönelik demo kurs içeriği.',
        'checkedSections' => [
            ['heading' => 'Madde 1', 'text' => 'Madde 1 içeriği burada.'],
            ['heading' => 'Madde 2', 'text' => 'Madde 2 içeriği burada.'],
        ],
    ],
    'sat' => [
        'title' => 'SAT',
        'placeholder' => 'Sat içeriği burada.',
        'checkedSections' => [
            ['heading' => 'Madde 1', 'text' => 'Madde 1 içeriği burada.'],
            ['heading' => 'Madde 2', 'text' => 'Madde 2 içeriği burada.'],
        ],
    ],
    'konusma-kulupleri' => [
        'title' => 'Konuşma Kulüpleri',
        'placeholder' => 'İngilizce konuşma pratiğini günlük ve ilgi çekici konular üzerinden geliştirmeye yönelik demo kulüp içeriği.',
        'checkedSections' => [
            ['heading' => 'Madde 1', 'text' => 'Madde 1 içeriği burada.'],
            ['heading' => 'Madde 2', 'text' => 'Madde 2 içeriği burada.'],
        ],
    ],
];

foreach ($demoTrainingPages as $slug => $course) {
    $routeName = $slug === 'konusma-kulupleri' ? 'speaking-clubs' : $slug;

    Route::view('/kurslarimiz/' . $slug, 'frontend.placeholder', [
        'imageBelowText' => $sharedCourseImageText,
        ...$course,
    ])->name('frontend.trainings.' . $routeName);
}
Route::view('/subelerimiz/ortaca', 'frontend.placeholder', [
    'title' => 'Ortaca',
    'imagePath' => 'frontend/images/branches/ala_ortaca.jpg',
    'placeholder' => <<<'TEXT'
Aydın Language Academy, Ortaca'da İngilizce öğrenmek isteyenler için mükemmel bir seçenektir. Kaliteli eğitim ve uzman öğretmenlerimizle, dil becerilerinizi geliştirmek için size yardımcı oluyoruz.

Kaliteli Eğitim:
Aydın Language Academy olarak, öğrencilere en iyi eğitimi sunmayı taahhüt ediyoruz. Nitelikli ve deneyimli öğretmenlerimiz, interaktif dersler ve modern öğretim materyalleriyle öğrencilerin İngilizce becerilerini hızla geliştirmelerini sağlıyor.

Geniş Kurs Seçenekleri:
Ortaca'daki Aydın Language Academy, farklı seviyelerde ve ihtiyaçlara uygun çeşitli kurs seçenekleri sunar. Genel İngilizce, İş İngilizcesi, Akademik İngilizce ve daha fazlası için bize katılın ve İngilizce becerilerinizi geliştirin!

Esnek Programlar:
Yoğun bir programınız varsa endişelenmeyin! Aydın Language Academy, esnek programlar sunarak öğrencilerin ihtiyaçlarına uyum sağlar. Sabah, öğle veya akşam dersleri arasından seçim yapabilir ve kendi hızınızda ilerleyebilirsiniz.

Mükemmel Konum:
Aydın Language Academy, Ortaca'nın merkezinde yer almaktadır. Ulaşım açısından oldukça elverişli olan merkezimiz, öğrencilere kolaylık sağlar. Ortaca'nın güzel doğası ve tarihi mirasını keşfederken İngbecerilerinizi geliştirin!

Ücretsiz Deneme Dersleri:
Hala karar veremediniz mi? Hiç sorun değil! Aydın Language Academy, tüm potansiyel öğrencilere ücretsiz deneme dersleri sunmaktadır. Kurslarımızı deneyin ve sizin için en uygun olanı seçin.

Ortaca'da İngilizce öğrenmek isteyen herkes için Aydın Language Academy mükemmel bir seçenektir. Bizimle iletişime geçin ve dil becerilerinizi geliştirmeye hemen başlayın!
TEXT,
    'contactCards' => [
        [
            'label' => 'Adresimiz',
            'value' => 'Merkez Mahallesi Muhammed Kundakçı Caddesi Eski PTT Karşısı No:10 Ortaca/Muğla',
            'icon' => 'fa-map-marker-alt',
        ],
        [
            'label' => 'Bizi Arayın',
            'value' => '(546) 828 4884',
            'href' => 'tel:+905468284884',
            'icon' => 'fa-phone-alt',
        ],
        [
            'label' => 'E-posta Gönderin',
            'value' => 'ortaca@learnenglishwithala.com',
            'href' => 'mailto:ortaca@learnenglishwithala.com',
            'icon' => 'fa-envelope-open',
        ],
        [
            'label' => 'WhatsApp ile İletişime Geçin',
            'value' => 'WhatsApp',
            'href' => 'https://wa.me/905468284884',
            'iconClass' => 'fab fa-whatsapp',
            'newTab' => true,
        ],
        [
            'label' => 'YouTube Kanalımız',
            'value' => 'YouTube',
            'href' => 'https://www.youtube.com/@Ayd%C4%B1nLanguageAcademy',
            'iconClass' => 'fab fa-youtube',
            'newTab' => true,
        ],
        [
            'label' => 'Instagram Hesabımız',
            'value' => 'Instagram',
            'href' => 'https://www.instagram.com/aydindilakademisidalaman?igsh=MTVjaXl2eDJ2MjJwYg==',
            'iconClass' => 'fab fa-instagram',
            'newTab' => true,
        ],
    ],
])->name('frontend.branches.ortaca');
Route::view('/subelerimiz/dalaman', 'frontend.placeholder', [
    'title' => 'Dalaman',
    'imagePath' => 'frontend/images/branches/ala_dalaman.jpg',
    'placeholder' => <<<'TEXT'
Aydın Language Academy, Dalaman'da İngilizce öğrenmek isteyenler için mükemmel bir seçenektir. Kaliteli eğitim ve uzman öğretmenlerimizle, dil becerilerinizi geliştirmek için size yardımcı oluyoruz.

Kaliteli Eğitim:
Aydın Language Academy olarak, öğrencilerimize en iyi eğitimi sunmayı hedefliyoruz. Uzman ve deneyimli öğretmenlerimiz, interaktif dersler ve modern öğretim materyalleri ile öğrencilerin dil becerilerini hızla geliştirmelerini sağlıyor.

Çeşitli Kurs Seçenekleri:
Aydın Language Academy, farklı seviyelerde ve ihtiyaçlara uygun çeşitli kurs seçenekleri sunar. Başlangıç ​​seviyesinden ileri seviyeye kadar herkes için bir kurs bulabilirsiniz. Genel İngilizce, İş İngilizcesi, Akademik İngilizce ve daha fazlası için bize katılın!

Esnek Programlar:
İşiniz veya günlük yaşamınız nedeniyle yoğun bir programınız varsa endişelenmeyin! Aydın Language Academy, esnek programlar sunarak öğrencilerin ihtiyaçlarına uyum sağlar. Sabah, öğle veya akşam dersleri arasından seçim yapabilir ve kendi hızınızda ilerleyebilirsiniz.

Mükemmel Konum:
Aydın Language Academy, Dalaman'ın merkezinde bulunmaktadır. Ulaşım açısından oldukça elverişli olan merkezimiz, öğrencilere kolaylık sağlar. Dalaman'ın tarihi ve doğal güzelliklerini keşfetmek için kurslar arasında keyifli bir mola verin!

Ücretsiz Deneme Dersleri:
Hala karar veremediniz mi? Hiç sorun değil! Aydın Language Academy, tüm potansiyel öğrencilere ücretsiz deneme dersleri sunmaktadır. Kurslarımızı deneyin ve kendiniz için mükemmel olanı seçin.

Dalaman'da İngilizce öğrenmek isteyen herkes için Aydın Language Academy mükemmel bir seçenektir. Bizimle iletişime geçin ve dil becerilerinizi geliştirmeye bugün başlayın!
TEXT,
    'contactCards' => [
        [
            'label' => 'Adresimiz',
            'value' => 'Karaçalı Mahallesi, Şehit Hamza Atakul Caddesi No:39/A Dalaman/Muğla',
            'icon' => 'fa-map-marker-alt',
        ],
        [
            'label' => 'Bizi Arayın',
            'value' => '(530) 828 4884',
            'href' => 'tel:+905308284884',
            'icon' => 'fa-phone-alt',
        ],
        [
            'label' => 'E-posta Gönderin',
            'value' => 'dalaman@learnenglishwithala.com',
            'href' => 'mailto:dalaman@learnenglishwithala.com',
            'icon' => 'fa-envelope-open',
        ],
        [
            'label' => 'WhatsApp ile İletişime Geçin',
            'value' => 'WhatsApp',
            'href' => 'https://wa.me/905308284884',
            'iconClass' => 'fab fa-whatsapp',
            'newTab' => true,
        ],
        [
            'label' => 'YouTube Kanalımız',
            'value' => 'YouTube',
            'href' => 'https://www.youtube.com/@Ayd%C4%B1nLanguageAcademy',
            'iconClass' => 'fab fa-youtube',
            'newTab' => true,
        ],
        [
            'label' => 'Instagram Hesabımız',
            'value' => 'Instagram',
            'href' => 'https://www.instagram.com/aydindilakademisidalaman?igsh=MTVjaXl2eDJ2MjJwYg==',
            'iconClass' => 'fab fa-instagram',
            'newTab' => true,
        ],
    ],
])->name('frontend.branches.dalaman');
Route::view('/subelerimiz/koycegiz', 'frontend.placeholder', [
    'title' => 'Köyceğiz',
    'imagePath' => 'frontend/images/branches/ala_koycegiz.jpg',
    'placeholder' => <<<'TEXT'
Aydın Language Academy, Köyceğiz'de İngilizce öğrenmek isteyenler için mükemmel bir seçenektir. Kaliteli eğitim ve uzman öğretmenlerimizle, dil becerilerinizi geliştirmek için size yardımcı oluyoruz.

Kaliteli Eğitim
Aydın Language Academy olarak, öğrencilerimize en iyi eğitimi sunmayı hedefliyoruz. Uzman ve deneyimli öğretmenlerimiz, interaktif dersler ve modern öğretim materyalleri ile öğrencilerin dil becerilerini hızla geliştirmelerini sağlıyor.

Çeşitli Kurs Seçenekleri
Aydın Language Academy, farklı seviyelerde ve ihtiyaçlara uygun çeşitli kurs seçenekleri sunar. Başlangıç ​​seviyesinden ileri seviyeye kadar herkes için bir kurs bulabilirsiniz. Genel İngilizce, İş İngilizcesi, Akademik İngilizce ve daha fazlası için bize katılın!

Esnek Programlar
İşiniz veya günlük yaşamınız nedeniyle yoğun bir programınız varsa endişelenmeyin! Aydın Language Academy, esnek programlar sunarak öğrencilerin ihtiyaçlarına uyum sağlar. Sabah, öğle veya akşam dersleri arasından seçim yapabilir ve kendi hızınızda ilerleyebilirsiniz.

Mükemmel Konum
Aydın Language Academy, Dalaman'ın merkezinde bulunmaktadır. Ulaşım açısından oldukça elverişli olan merkezimiz, öğrencilere kolaylık sağlar. Dalaman'ın tarihi ve doğal güzelliklerini keşfetmek için kurslar arasında keyifli bir mola verin!

Ücretsiz Deneme Dersleri
Hala karar veremediniz mi? Hiç sorun değil! Aydın Language Academy, tüm potansiyel öğrencilere ücretsiz deneme dersleri sunmaktadır. Kurslarımızı deneyin ve kendiniz için mükemmel olanı seçin.

Köyceğiz'de İngilizce öğrenmek isteyen herkes için Aydın Language Academy mükemmel bir seçenektir. Bizimle iletişime geçin ve dil becerilerinizi geliştirmeye bugün başlayın!
TEXT,
    'contactCards' => [
        [
            'label' => 'Adresimiz',
            'value' => 'Ulucamii İbrahim Koç Sokak Köyceğiz Lokantası Yanı Köyceğiz/Muğla',
            'icon' => 'fa-map-marker-alt',
        ],
        [
            'label' => 'Bizi Arayın',
            'value' => '(540) 828 4884',
            'href' => 'tel:+905408284884',
            'icon' => 'fa-phone-alt',
        ],
        [
            'label' => 'E-posta Gönderin',
            'value' => 'koycegiz@learnenglishwithala.com',
            'href' => 'mailto:koycegiz@learnenglishwithala.com',
            'icon' => 'fa-envelope-open',
        ],
        [
            'label' => 'WhatsApp ile İletişime Geçin',
            'value' => 'WhatsApp',
            'href' => 'https://wa.me/905408284884',
            'iconClass' => 'fab fa-whatsapp',
            'newTab' => true,
        ],
        [
            'label' => 'YouTube Kanalımız',
            'value' => 'YouTube',
            'href' => 'https://www.youtube.com/@Ayd%C4%B1nLanguageAcademy',
            'iconClass' => 'fab fa-youtube',
            'newTab' => true,
        ],
        [
            'label' => 'Instagram Hesabımız',
            'value' => 'Instagram',
            'href' => 'https://www.instagram.com/aydindilakademisidalaman?igsh=MTVjaXl2eDJ2MjJwYg==',
            'iconClass' => 'fab fa-instagram',
            'newTab' => true,
        ],
    ],
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

    $returnRoute = FrontendReturnRoutes::resolve($request->query('return'));

    if ($returnRoute) {
        $request->session()->put('url.intended', route($returnRoute, absolute: false));
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

