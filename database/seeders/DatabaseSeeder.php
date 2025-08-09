<?php

namespace Database\Seeders;
use App\Models\model_courses;
use App\Models\model_declarations;
use App\Models\model_exercises;
use App\Models\model_questions;
use App\Models\model_results;
use App\Models\model_user_answers;
use App\Models\User;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use App\Models\model_themes;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $levels = ['GRAMMAR', 'VOCABULARY', 'LISTENING'];
        foreach ($levels as $level) {
            model_levels::insert([
                'name' => $level,
                'slug' => Str::slug($level),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $sub_levels = ['ELEMENTARY', 'INTERMEDIATE', 'PRE-INTERMEDIATE'];
        foreach ($sub_levels as $sub_level) {
            model_sub_levels::insert([
                'name' => $sub_level,
                'slug' => Str::slug($sub_level),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $courses_title_tr = [
            'Okul Öncesi Öğrenciler İçin İngilizce Eğitimi',
            'Seviye Tespit ile Kişiye Özel Eğitim',
            'Uzman Eğitmenler ve Oyun Bazlı Kaynaklar',
            'Grup veya Özel Ders Seçenekleri',
            'İlkokul Öğrencileri İçin İngilizce Eğitimi',
            'Kaliteli Kaynaklar ve Uzman Eğitmenler',
            'Yerli ve Native Öğretmenlerden Eğitim',
        ];
        $courses_title_en = [
            'English Language Education for Preschool Students',
            'Personalized Instruction with Level Assessment',
            'Expert Instructors and Game-Based Resources',
            'Group or Private Lesson Options',
            'English Language Education for Primary School Students',
            'Quality Resources and Expert Instructors',
            'Education from Native and Indigenous Teachers',
        ];
        $courses_slogan_tr = [
            'Çocuklarımız İçin Buradayız!!',
            'Eğitimlerimizle Hedefinize Bir Adım Daha Yaklaşın!',
            'TOEFL Bizim İşimiz.',
            'IELTS Bizim İşimiz.',
        ];
        $courses_slogan_en = [
            'We Are Here for Our Children!!',
            'Get One Step Closer to Your Goal with Our Education!',
            'TOEFL is Our Business.',
            'IELTS is Our Business.',
        ];

        $courses_description_tr = [
            'Programlarımızda, hem yerli hem de anadili İngilizce olan deneyimli öğretmenler görev alır. Bu sayede, dil öğrenme sürecinizi hem yerel ihtiyaçlarınıza hem de uluslararası standartlara uygun bir şekilde destekliyoruz.',
            'A1-A2 seviyelerinde toplamda 90-110 ders saati süren programlarla dil öğreniminde sağlam bir temel oluşturabilirsiniz.',
            'B seviyesinde 180 ders saati içeren kapsamlı bir eğitimle daha akıcı ve özgüvenli bir şekilde iletişim kurmayı öğrenebilirsiniz.',
            'Lise seviyesine uygun olarak seçilmiş akademik ve güncel kaynaklar, uzman eğitmenlerimizin rehberliğinde kullanılır. Öğrencilerimizin hedeflerine ulaşmalarını sağlayacak materyallerle öğrenim sürecini destekliyoruz.',
            'Her öğrencimiz, tarafımızca uygulanan seviye tespit sınavıyla değerlendirilir. Bu sınav sonuçlarına göre, öğrencilerimiz seviyelerine uygun sınıflara yerleştirilerek etkili bir dil eğitimi alır.',
        ];

        $courses_description_en = [
            'Our programs feature experienced teachers who are both native and native speakers of English. This allows us to support your language learning process in accordance with both local needs and international standards.',
            'You can build a solid foundation in language learning with programs totaling 90-110 hours of instruction at levels A1-A2.',
            'You can learn to communicate more fluently and confidently with comprehensive instruction at level B, which includes 180 hours of instruction.',
            'Academic and up-to-date resources selected for high school level are used under the guidance of our expert instructors. We support our students learning process with materials that will help them achieve their goals.',
            'Each student is evaluated with a placement test administered by us. Based on the results of this test, our students are placed in classes appropriate to their level and receive effective language instruction.',
        ];

        for ($i = 0; $i < 10; $i++) {
            model_courses::insert([
                'title_tr' => $courses_title_tr[rand(0,6)],
                'title_en' => $courses_title_en[rand(0,6)],
                'slogan_tr' => $courses_slogan_tr[rand(0,3)],
                'slogan_en' => $courses_slogan_en[rand(0,3)],
                'description_tr' => $courses_description_tr[rand(0,4)],
                'description_en' => $courses_description_en[rand(0,4)],
                'image' => rand(0, max: 9).'.jpg',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        User::insert([
            'name' => 'Adem VAROL',
            'email' => 'adem@learnenglishwithala.com',
            'email_verified_at' => now(),
            'type' => 'admin',
            //'password' => '$2y$12$Sj8XJ5vfQBpW6opvmlrgTuvnbOKWHBDvg.6o1ZTuKSJPuOzI7oEzu',
            'password' => '$2y$12$pVyMOcbht5SoAL1D1Kp3E.eocQcfKXufYbvOcOgQZ7S2RmbzBeXau',
            'remember_token' => Str::random(10),
        ]);

        User::factory(9)->create();
        model_themes::factory(50)->create();
        model_exercises::factory(100)->create();
        model_declarations::factory(100)->create();
        model_questions::factory(1000)->create();
        /*        model_courses::factory(1)->create();
                model_results::factory(1000)->create();
                model_user_answers::factory(1000)->create(); */
    }
}
