<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\admins_model;
use App\Models\messages_model;
use App\Models\pages_model;
use App\Models\questions_model;
use App\Models\settings_model;
use App\Models\types_model;
use App\Models\levels_model;
use App\Models\exams_model;
use Illuminate\Http\Request;
use Str;

class a_TestController extends Controller
{
    public function test_metodu()
    {
        /*         $types = ['Grammer', 'Vocabulary', 'Listening', 'Reading', 'Use of English', 'Writing', 'Exams'];
                foreach ($types as $type) {
                    types_model::create([
                        "name" => $type,
                        "slug" => str::slug($type)
                    ]);
                }

                $levels = ['Elementary (A1)', 'Pre-Intermediate (A2)', 'Intermediate (B1)', 'Upper-Intermediate (B1+)', 'Pre-Advanced (B2)'];
                foreach ($levels as $level) {
                    levels_model::create([
                        "name" => $level,
                        "slug" => str::slug($level)
                    ]);
                }

                $exams = ['Exam-01', 'Exam-02', 'Exam-03', 'Exam-04', 'Exam-05'];
                foreach ($exams as $exam) {
                    exams_model::create([
                        "name" => $exam,
                        "slug" => str::slug($exam),
                        "title" => (string) $exam . '-title',
                        "subtitle" => (string) $exam . '-subtitle'
                    ]);
                }

                $questions = ['1. Soru Kökü', '2. Soru Kökü', '3. Soru Kökü', '4. Soru Kökü', '5. Soru Kökü'];
                foreach ($questions as $question) {
                    questions_model::create([
                        "q_type" => 'choice',
                        "content" => (string) $question . '-Content',
                        "answer" => 'A-Doğru Cevap',
                        "select_1" => 'A-Seçeneği',
                        "select_2" => 'B-Seçeneği',
                        "select_3" => 'C-Seçeneği',
                        "select_4" => 'D-Seçeneği',
                        "select_5" => 'E-Seçeneği',
                        "type_id" => types_model::find(1)->id,
                        "level_id" => levels_model::find(2)->id,
                        "exam_id" => exams_model::find(3)->id
                    ]);
                }

                settings_model::create([
                    'title' => 'AYDIN LANGUAGE ACADEMY',
                    'logo_path' => '/img/logo.png',
                    'favicon_path' => '/img/favicon.png',
                    'instagram' => 'www.instagram.com',
                    'youtube' => 'www.youtube.com',
                    'facebook' => 'www.facebook.com',
                    'x' => 'www.x.com'
                ]);
                $pages = ['Vizyonumuz', 'Misyonumuz', 'Kadromuz', 'Çeşitli', 'Sağlık'];
                $que = 0;
                foreach ($pages as $page) {
                    pages_model::create([
                        "name" => $page,
                        "slug" => str::slug($page),
                        "logo_path" => '/' . str::slug($page) . '.png',
                        "content" => $page . '-content',
                        'queue' => $que
                    ]);
                    $que++;
                }

                admins_model::create([
                    'name' => 'Adem VAROL',
                    'email' => 'adem@mail.com',
                    'password' => '111'
                ]);
                messages_model::create([
                    'fullname' => 'Adem VAROL',
                    'email' => 'adem@mail.com',
                    'telephone' => '5326666549',
                    'subject' => 'DENEME KONU',
                    'message' => 'Selam millet.'
                ]); */
        //$silinecek = questions_model::find(1)->delete();

        $questions = questions_model::with('types', 'levels', 'exams')->get('*');
        dd($questions);
    }
}
