Proje: ALA — Learn English With ALA

Bu repository mevcut çalışan ALA Laravel uygulamasını ve geliştirilecek yeni public dil kursu tanıtım sitesini içerir.

Kullanıcı belirli bir değişikliği açıkça istemedikçe mevcut ALA uygulamasını bozma, yeniden tasarlama, refactor etme, yeniden adlandırma, taşıma veya değiştirme.

1. Mevcut sistem
Laravel 12
PHP 8.4+
Composer
Yerelde MariaDB, canlıda MySQL
DB_CONNECTION=mysql
Jetstream / Fortify authentication
Livewire

Çalışan admin ve üye alanı
Mevcut Authentication, Admin, Users, Levels, Sub Levels, Themes, Exercises, Questions, Results, controller, model, migration, factory, seeder, route, Blade ve Livewire kodlarını çalışan mevcut uygulama olarak kabul et.

2. Mevcut ALA'yı koru
Açıkça istenmedikçe mevcut controller/model/migration/route/Blade/Livewire/business logic/authentication/admin/üye alanını değiştirme, taşıma veya refactor etme. Jetstream/Fortify'ı değiştirme. İlgisiz kod temizliği, dependency güncellemesi veya toplu formatlama yapma.

Bir değişiklik mevcut uygulamaya dokunmayı gerektiriyorsa önce nedenini bildir.

3. Yeni public frontend
Yeni ziyaretçi sitesi mevcut uygulamadan izole geliştirilecektir.

Ana menü:
Ana Sayfa
Kurslarımız
Başarılarımız
Kampanyalarımız
Şubelerimiz
Seviye Tespit Sınavı
Dökümanlar

İlk beş sayfa public'tir. Seviye Tespit Sınavı'nın tanıtım sayfası public olabilir; sınavı başlatma, sınav kaydı ve sonuçlar login gerektirir. Dökümanlar login gerektirir.
Klinik Template yalnızca görsel/tasarım kaynağıdır. Mümkün olduğunca template ini eğitim template ine uyarla.

4. Authentication akışı
Public frontend
    ↓
Seviye Tespit Sınavını Başlat / Dökümanlar
    ↓
Giriş yapılmış mı?
    ├── Evet → belirlenen mevcut ALA üye sayfası
    └── Hayır → mevcut ALA login
                    ↓
                 login
                    ↓
              belirlenen mevcut ALA üye sayfası

İkinci authentication sistemi, ikinci users yapısı veya ikinci üye portalı oluşturma. Mevcut Jetstream/Fortify sistemini kullan.

Login sonrası kesin hedef sayfa daha sonra kullanıcı tarafından belirlenecek. Route/hedef uydurma.

Seviye Tespit Sınavı'nın kendisi ayrıca/daha sonra geliştirilecektir.

Seviye Tespit Sınavı için girişsiz ziyaretçiye yalnız tanıtım/yer tutucu içerik gösterilebilir; sınavı başlatma, sınav kaydı ve sonucu görüntüleme giriş gerektirir. Mevcut legacy exercise sistemi yeni sınav sisteminin çalışan altyapısı olarak kabul edilmez.

4.1 Seviye Tespit Sınavı — kalıcı domain ve veri katmanı kuralları
Bu özellik üyelik gerektirir ve mevcut legacy exercise sisteminden bağımsızdır. Kullanıcı açıkça istemedikçe bu aşamada yalnız migration, model, ilişki, index/constraint, seviye seed'i ve bunlarla doğrudan ilgili güvenli doğrulamalar yapılır; controller, route, frontend, admin ekranı, sınav ekranı, authorization değişikliği veya sınav başlatma servisi yapılmaz.

Temel seviye ve sonuç kuralları:

- CEFR seviyeleri A1 → A2 → B1 → B2 → C1 → C2 sırasındadır. Sınav yalnız A1, A2, B1, B2 ve C1 için vardır; C2 için sınav yoktur.
- C1 sınavından success alan kullanıcının nihai seviyesi C2 olur. Başarısız olunan ilk sınav seviyesi, kullanıcının nihai seviyesidir.
- Her seviyenin soru sayısı ve geçme yüzdesi admin tarafından değiştirilebilir. Soru sayıları kod içine hard-code edilmez; A1–C1 için question_count nullable tutulur. C2 istisnası has_exam=false, question_count=0 ve pass_percentage=NULL'dır.
- Başarı formülü score_percentage = correct_count / question_count_snapshot * 100 şeklindedir. score_percentage, pass_percentage_snapshot değerine eşit ya da büyükse success, aksi durumda unsuccess olur.
- Yanlış ve boş cevapların negatif puanı yoktur. Yarım bırakılan sınavda cevaplanmamış sorular blank sayılır; yeterli doğru varsa yine success mümkündür.
- Attempt, admin tarafından approved edilene kadar yeni attempt başlatılamaz. Bu iş kuralı ileride controller/service katmanında uygulanır; veri modeli bunu desteklemelidir.

Veri sözleşmesi:

- placement_test_levels: code ve sequence benzersiz; question_count negatif olmayacak unsigned türde; pass_percentage decimal; has_exam ve is_active alanları bulunur.
- placement_test_questions: placement_test_level_id, question_text ve is_active içerir. Master soruda global order alanı bulunmaz; soru sırası her attempt'te belirlenir. C2'ye soru atama uygulama katmanında engellenir.
- placement_test_question_options: placement_test_question_id, option_text, display_position ve is_correct içerir. Şıkların görüntüleme sırası sabittir; aynı soru altında display_position benzersizdir. Şık sayısı hard-code edilmez.
- placement_tests: user_id, status, nullable result_level_id, started_at, nullable submitted_at/approved_at ve nullable approved_by içerir. Status yalnız in_progress, pending_approval ve approved yaşam döngüsünü destekler; kullanıcı + status sorgusu için index bulunur.
- placement_test_level_results: her placement_test + level çifti için tek kayıttır. question_count_snapshot, pass_percentage_snapshot, correct_count, wrong_count, blank_count, score_percentage, result, started_at ve nullable completed_at saklar. Result yalnız success veya unsuccess değerini destekler.
- placement_test_level_questions: atanan soru için nullable master soru FK'si, attempt içi display_position, question_text_snapshot, options_snapshot JSON, correct_option_snapshot, nullable selected_option, answer_status ve nullable answered_at saklar. Aynı level result içinde display_position benzersizdir. Snapshot'taki seçenekler en az position ve text bilgisini içerir; doğru/seçili seçenekler master option tablosuna ihtiyaç duymadan bu konumu tanımlar.

İlişkiler, geçmiş ve silme politikası:

- PlacementTestLevel hasMany questions ve levelResults; PlacementTestQuestion belongsTo level ve hasMany options; PlacementTestQuestionOption belongsTo question.
- PlacementTest belongsTo user, resultLevel ve approver; hasMany levelResults. PlacementTestLevelResult belongsTo placementTest ve level; hasMany levelQuestions. PlacementTestLevelQuestion belongsTo levelResult ve özgün/master soruya, kayıt hâlâ mevcutsa, bağlanır.
- Master soru/seçenek değişse, pasife alınsa veya silinse bile geçmiş sınav sonucu değişmemeli ya da kaybolmamalıdır. Snapshot kayıtları geçmişin asıl kaynağıdır; master soru bağı yalnız yardımcıdır.
- PlacementTestLevelQuestion içindeki master soru FK'si nullable/nullOnDelete olmalı; geçmiş snapshot kayıtlarını silmeyecek şekilde tasarlanmalıdır. Kullanıcı ve approver silinirse sınav geçmişi kalacak biçimde nullable/nullOnDelete ilişki kullanılır.
- Tarihçe zinciri ve master seviye/soru ilişkilerinde cascade delete kullanma. Kullanıcıları, sınav geçmişini veya sonuçları yanlışlıkla silebilecek ilişki kurma. Projede soft delete yaklaşımı varsa önce incele ve onunla uyumlu davran.
- Mevcut User modeline yalnız placementTests ve approvedPlacementTests gibi gerçekten gerekli ilişkileri ekle; fillable, auth ve kullanıcı türü davranışını değiştirme.
- PHP enum proje standardı yoksa yalnız bu özellik için enum mimarisi kurma. JSON snapshot alanlarına array, tarih/saat alanlarına datetime ve sayı/yüzde alanlarına uygun cast tanımla.

Seed kuralları:

- A1, A2, B1, B2, C1 ve C2 seviyelerini ayrı ve idempotent bir seeder oluşturur. Aynı seeder tekrar çalıştığında duplicate kayıt oluşturmaz veya adminin sonradan değiştirdiği ayarları ezmez.
- A1–C1: sequence sırasıyla 1–5, has_exam=true, is_active=true, question_count=NULL, pass_percentage=60. C2: sequence=6, has_exam=false, is_active=true, question_count=0, pass_percentage=NULL.
- Mevcut DatabaseSeeder'ı çalıştırma/değiştirme; soru veya seçenek içerikleri verilmedikçe soru/şık seed etme.

Migration ve doğrulama güvenliği:

- Mevcut migration dosyalarını değiştirme; yalnız yeni migration dosyaları oluştur. Mevcut verileri silme ve MariaDB/MySQL uyumluluğunu koru. Uzun placement-table constraint adlarını MariaDB/MySQL'nin 64 karakter sınırını aşmayacak açık, kısa adlarla tanımla.
- Kullanıcı açıkça onaylamadıkça migrate, db, database reset veya drop database komutlarını çalıştırma. Onaylı migration yalnız kapsamındaki pending migration'ları çalıştırır; reset/drop veya mevcut veriyi silme kesinlikle yapılmaz.
- Doğrulamada güvenli migration syntax/schema kontrolü ve model syntax kontrolü kullan. Yerel MariaDB'yi sıfırlayan veya değiştiren test komutları çalıştırılmaz; bunun için izole bir test veritabanı gerekir.
- Bu kapsam tamamlandığında dosyaları, tabloları, önemli foreign key/unique constraint'leri, snapshot yapısını, status/result alanlarını, seed yaklaşımını, çalıştırılan doğrulamaları ve açık teknik sorunları kısa ve somut olarak raporla. Kapsam dışı özellik geliştirme ve burada dur.

5. Yeni frontend'i izole tut

Önerilen nihai yapı:
resources/views/frontend/
├── layouts/
├── partials/
├── home.blade.php
├── achievements.blade.php
├── campaigns.blade.php
├── trainings.blade.php
└── branches.blade.php

public/frontend/
├── css/
├── js/
├── images/
├── fonts/
└── vendor/

Mevcut view'ları buraya taşıma veya üzerlerine yazma.

6. Frontend kaynak paketi

Kullanıcı yaklaşık şu yapıyı hazırlayacaktır:

ALA-FRONTEND/
├──ASSETS/
│   ├── achievements/
│   ├── branches/
│   ├── campaigns/
│   ├── home/
│   ├── logo/
│   └── trainings/
├── CONTENT/
│   ├── achievements.txt
│   ├── branches.txt
│   ├── campaigns.txt
│   ├── documents.txt
│   ├── home.txt
│   ├── placement-test.txt
│   └── trainings.txt
└── TEMPLATE/
    └── website-template/

TEMPLATE
Ham HTML template'i referanstır; nihai Laravel yapısı değildir. Dosyaları körlemesine public/ altına kopyalama. Önce gerçekten gereken asset/component'leri belirle. Eksik olan sayfaları template içerisindeki uygun olan sayfalardan seç.

CONTENT
Kullanıcının sayfa metinleridir. home.txt vb. içerik brief'i olarak kullanılır. Kullanıcı istemedikçe metinlerin yerine yeni pazarlama metni uydurma. .txt dosyalarını runtime CMS/veri kaynağına dönüştürme.

ASSETS
Logo, fotoğraf, kampanya, şube, başarı vb. kullanıcı görselleridir. Uygun kullanıcı görseli varsa stok template görseline tercih et. Nihai kullanılan asset'leri public/frontend/ altına düzenli biçimde taşı ve Blade'de asset() kullan. Eğer template görseline alternatif uygun bir görsel bulunamazsa o bölgeye uygun isimle boş bir image dosyası oluşturarak ekle yada orijinalini bırak. Daha sonra bu kısma içeriğe uygun görsel temin edilecektir.

7. Template dönüşüm kuralları
Tekrarlanan yapıları Blade layout/partial'larına ayır.
Responsive davranışı koru.
Yararlı animasyon ve etkileşimleri koru.
Kullanılmayan demo bölüm/asset'lerini taşıma.
Template markasını ALA ile değiştir.
Demo metinlerini kullanıcının CONTENT dosyalarıyla değiştir.
Kullanıcı görsellerini tercih et.
Internal URL'lerde Laravel route helper kullan.
Asset'lerde asset() kullan.
Onaysız yeni frontend framework veya gereksiz paket ekleme.
Demo backend/form davranışlarını körlemesine taşıma.
Gereksiz over-engineering yapma.

8. Route stratejisi
Hedef yaklaşık olarak:
/                       → yeni public ana sayfa
/basarilarimiz          → public
/kurslarimiz            → public
/kampanyalarimiz        → public
/subelerimiz            → public
/seviye-tespit-sinavi   → public tanıtım; sınavı başlatma/sonuç auth gerekli
/dokumanlar             → auth gerekli

Kullanıcı final geçişi açıkça onaylamadan mevcut / route'unu veya mevcut home route'unu değiştirme.
Geliştirme sırasında gerekirse /yeni-site/... gibi geçici route prefix'i kullan.
Route değişikliğinden önce mevcut route'ları incele, çakışmaları belirle, yapılacak değişikliği bildir ve yalnızca onaylanan değişikliği yap.

9. Veritabanı
Yeni tanıtım frontend'i başlangıçta database schema değişikliği gerektirmemelidir. Kampanya/şube/başarı vb. içerikler admin yönetimine alınması açıkça istenmedikçe migration oluşturma.

Frontend işi için mevcut migration, seeder veya factory'leri değiştirme. Sonraki DB değişikliklerinde MySQL/MariaDB uyumluluğunu koru.

10. Git disiplini
Dosya değiştirmeden önce git status kontrol et. Repository'deki mevcut değişikliklerin bu göreve ait olduğunu varsayma.

Kullanıcı istemedikçe mevcut değişiklikleri discard/restore/reset/clean/stash etme veya üzerlerine yazma.
Açık talep olmadan şu yıkıcı komutları kullanma:
git reset --hard
git clean -fd
git checkout -- .
.env, parola, secret, cache veya yerel database dosyalarını commit etme.

11. Secret'lar
.env içindeki database/SMTP parolası, API key veya diğer secret'ları gösterme, hard-code etme, dokümana veya frontend'e kopyalama ve commit etme.

12. Çalışma yöntemi
Kapsamlı işlerde:
İncele.
Bulguları bildir.
En küçük implementasyon adımını öner.
Yalnızca istenen/onaylanan kapsamı uygula.
Test et.
Değişen dosyaları ve test sonuçlarını bildir.
Dur.
Kullanıcı yalnızca analiz istiyorsa dosya değiştirme, write operation yapma, migration oluşturma veya route değiştirme.

Frontend'de en az sayfanın hatasız render edilmesini, CSS/JS/görselleri, desktop/mobil görünümü, internal linkleri ve korumalı linklerin mevcut auth akışını kullanmasını test et.

13. Tahmin etme
Belirsizse projeyi incele. Özellikle mevcut route isimleri, login redirect'i, authenticated home, mevcut frontend, seçilen template, içerik-sayfa eşleşmesi ve görsel kullanımını uydurma.
Kaynak paketi ile repository çelişirse çalışan mevcut uygulamayı koru ve çelişkiyi bildir.

14. Mevcut öncelik
1. Ana Sayfa
2. Başarılarımız
3. Kurslarımız
4. Kampanyalarımız
5. Şubelerimiz
6. Seviye Tespit Sınavları - giriş/auth akışı
7. Dökümanlar - giriş/auth akışı

Sayfaları teker teker oluştur ve oluşturulan sayfanın kullanıcı tarafından teyit edilmesini bekle. Teyit edildikten sonra kullanıcı onay vermeden veya talep etmeden diğer sayfayı oluşturmaya geçme.
Mevcut Level/Sub Level/Theme/Exercise/Question alanlarını ayrıca istenmedikçe yeniden tasarlama.

Sistemde üç tür kullanıcı var. 
1) Login gerektirmeyen kullanıcı:
Ana Sayfa, Başarılarımız, Kurslarımız, Kampanyalarımız, Şubelerimiz ve Seviye Tespit Sınavı'nın tanıtım sayfasına ulaşabilir.
2) Login gerektiren kullanıcı:
Ana Sayfa, Başarılarımız, Kurslarımız, Kampanyalarımız, Şubelerimiz sayfaları ile birlikte Seviye Tespit Sınavı'nı başlatma, sınav kayıtları ve sonuçlarına da ulaşabilir.
3) Admin Kullanıcı:
/admin ile admin panel de dahil bütün sayfalara ulaşabilir.

15. Başarı kriterleri
Mevcut ALA ve admin paneli eskisi gibi çalışır.
Authentication mevcut sistemi kullanır.
Yeni public frontend bağımsız ve modern olur.
Kullanıcının sağladığı metin/görseller doğru kullanılır.
Public sayfalar login olmadan çalışır.
Korumalı girişler mevcut login akışını kullanır.
Login sonrası belirlenen mevcut üye sayfasına gidilebilir.
Mevcut business logic gereksiz yere değiştirilmez.
Yeni frontend yalnızca kullanıcı final geçişi onayladığında ana public route'lara alınır.
