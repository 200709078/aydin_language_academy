Proje: ALA — Learn English With ALA

Bu repository mevcut çalışan ALA Laravel uygulamasını ve yayında olan yeni public dil kursu tanıtım sitesini içerir.

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
Public tanıtım sitesi resources/views/frontend/ ve public/frontend/ altında, mevcut uygulamadan izole geliştirilmiştir ve yayınlanmıştır.

Ana menü:
Ana Sayfa
Kurslarımız
Başarılarımız
Kampanyalarımız
Şubelerimiz
Seviye Tespit Sınavı
Dökümanlar

Erişim: İlk beş sayfa public'tir. Seviye Tespit Sınavı'nın tanıtım sayfası public olabilir; sınavı başlatma, sınav kaydı ve sonuçlar login gerektirir. Dökümanlar login gerektirir.

Kalıcı frontend kuralları:
Internal URL'lerde Laravel route helper kullan.
Asset'lerde asset() kullan.
Responsive davranışı ve yararlı animasyonları koru.
Onaysız yeni frontend framework veya gereksiz paket ekleme.
Kullanıcı istemedikçe metinlerin yerine yeni pazarlama metni uydurma.

ALA marka sloganları:
- Primary slogan: Türkçe `Dil öğren, dünyanı genişlet.` / İngilizce `Master language, empower success.`
- Secondary slogan: Türkçe `Sadece dil öğrenme. Onu yaşamaya başla.` / İngilizce `Don't just learn a language, start living it.`
- Ana sayfa hero alanında, kullanıcı aksini istemedikçe primary slogan kullanılır. Secondary slogan ancak açıkça istenen başka bir bağlamda kullanılır.

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
- Her seviyenin soru sayısı ve geçme yüzdesi admin tarafından değiştirilebilir. Soru sayıları kod içine hard-code edilmez; A1–C1 için question_count nullable tutulur. Sınav oluşturulurken o seviyedeki tüm aktif sorular atanır; rastgele ya da alt küme soru seçimi yapılmaz. Bu nedenle uygulama katmanı, tanımlı question_count ile aktif soru sayısının eşitliğini sınav başlamadan doğrulamalıdır. C2 istisnası has_exam=false, question_count=0 ve pass_percentage=NULL'dır.
- Her aktif master sorunun pozitif bir puanı admin tarafından girilir; puan otomatik üretilmez. Başarı formülü score_percentage = correct_points / total_points_snapshot * 100 şeklindedir. score_percentage, pass_percentage_snapshot değerine eşit ya da büyükse success, aksi durumda unsuccess olur. Geçme yüzdesi seviye ayarı olarak admin tarafından değiştirilebilir ve attempt başında snapshot alınır.
- Yanlış ve boş cevapların negatif puanı yoktur. Yarım bırakılan sınavda cevaplanmamış sorular blank sayılır; yeterli doğru varsa yine success mümkündür.
- Attempt, admin tarafından approved edilene kadar yeni attempt başlatılamaz. Bu iş kuralı ileride controller/service katmanında uygulanır; veri modeli bunu desteklemelidir.

Veri sözleşmesi:

- placement_test_levels: code ve sequence benzersiz; question_count negatif olmayacak unsigned türde; pass_percentage decimal; has_exam ve is_active alanları bulunur.
- placement_test_question_contents: Bir seviyeye ait, type değeri text, audio, image veya video olan ortak içerik grubudur. text için boş olmayan text_content; audio, image veya video için boş olmayan media_disk + media_path bilgisi içerir. Audio, image ve video yalnız sunucudaki dosya referanslarıyla saklanır; harici URL kullanılmaz.
- placement_test_questions: placement_test_level_id, nullable placement_test_question_content_id, nullable content_position, question_text, pozitif points ve is_active içerir. Bağımsız sorunun ortak içeriği ile content_position değeri NULL'dır. Ortak içeriğe bağlı sorunun content_position değeri pozitif ve aynı grup altında benzersizdir; böylece grup soruları kesin sıraya sahip olur ve grup hâlinde ardışık atanır. Bu koşullu content_position kuralı model/admin doğrulamasında zorunlu tutulur; MySQL foreign-key uyumluluğu için aynı sütunlarda CHECK kuralı kullanılmaz. Master soruda global order alanı bulunmaz. C2'ye soru veya ortak içerik atama uygulama katmanında engellenir.
- placement_test_question_options: placement_test_question_id, option_text, display_position ve is_correct içerir. Şıkların görüntüleme sırası sabittir; aynı soru altında display_position benzersizdir. Şık sayısı hard-code edilmez.
- placement_tests: user_id, status, nullable result_level_id, started_at, nullable submitted_at/approved_at ve nullable approved_by içerir. Status yalnız in_progress, pending_approval ve approved yaşam döngüsünü destekler; kullanıcı + status sorgusu için index bulunur.
- placement_test_level_results: her placement_test + level çifti için tek kayıttır. question_count_snapshot, pass_percentage_snapshot, pozitif total_points_snapshot, 0 ile toplam puan arasında correct_points, correct_count, wrong_count, blank_count, score_percentage, result, started_at ve nullable completed_at saklar. Result yalnız success veya unsuccess değerini destekler.
- placement_test_level_result_contents: Bir level result içindeki her ortak içerik grubu için yalnız bir kez içerik snapshot'ı saklar. Kendi placement_test_level_id değeri level result ile eşleşir; master ortak içerik bağlıyken onun seviyesinin de aynı olması model doğrulamasında zorunludur. Master ortak içerik silinirse nullable/nullOnDelete kaynak FK NULL olur; history için type/text/media snapshot'ı ile level result ilişkisi korunur.
- placement_test_level_questions: atanan soru için nullable master soru FK'si, nullable level-result ortak içerik snapshot FK'si, attempt içi display_position, question_text_snapshot, options_snapshot JSON, correct_option_snapshot, pozitif points_snapshot, nullable selected_option, answer_status ve nullable answered_at saklar. Aynı level result içinde display_position benzersizdir. Aynı ortak içerik snapshot'ına bağlı sorular ardışık display_position değerleriyle atanır. Snapshot'taki seçenekler en az position ve text bilgisini içerir; doğru/seçili seçenekler master option tablosuna ihtiyaç duymadan bu konumu tanımlar.

İlişkiler, geçmiş ve silme politikası:

- PlacementTestLevel hasMany questions, questionContents ve levelResults; PlacementTestQuestion belongsTo level ve nullable questionContent'a, hasMany options; PlacementTestQuestionContent belongsTo level ve hasMany questions.
- PlacementTest belongsTo user, resultLevel ve approver; hasMany levelResults. PlacementTestLevelResult belongsTo placementTest ve level; hasMany levelQuestions ve contentSnapshots. PlacementTestLevelResultContent belongsTo levelResult, level'e ve kayıt hâlâ mevcutsa özgün/master questionContent'a; hasMany levelQuestions. PlacementTestLevelQuestion belongsTo levelResult, özgün/master soruya ve nullable contentSnapshot'a bağlanır. Composite foreign key'ler soru–ortak içerik, result–içerik snapshot ve soru snapshot–içerik snapshot sahipliklerini aynı seviye/result içinde tutar; nullable master içerik kaynak seviyesi model doğrulamasında korunur.
- Master soru/seçenek/ortak içerik değişse, pasife alınsa veya silinse bile geçmiş sınav sonucu değişmemeli ya da kaybolmamalıdır. Attempt soru ve ortak içerik snapshot kayıtları geçmişin asıl kaynağıdır; master bağları yalnız yardımcıdır. Medya dosyaları değişmez, benzersiz path'lerle saklanır ve geçmişte kullanılan dosya path'i üzerine yazılmaz veya silinmez.
- PlacementTestLevelQuestion içindeki master soru FK'si ile PlacementTestLevelResultContent içindeki master ortak içerik FK'si nullable/nullOnDelete olmalı; geçmiş snapshot kayıtlarını silmeyecek şekilde tasarlanmalıdır. Kullanıcı ve approver silinirse sınav geçmişi kalacak biçimde nullable/nullOnDelete ilişki kullanılır.
- Tarihçe zinciri ve master seviye/soru ilişkilerinde cascade delete kullanma. Kullanıcıları, sınav geçmişini veya sonuçları yanlışlıkla silebilecek ilişki kurma. Projede soft delete yaklaşımı varsa önce incele ve onunla uyumlu davran.
- Mevcut User modeline yalnız placementTests ve approvedPlacementTests gibi gerçekten gerekli ilişkileri ekle; fillable, auth ve kullanıcı türü davranışını değiştirme.
- PHP enum proje standardı yoksa yalnız bu özellik için enum mimarisi kurma. JSON snapshot alanlarına array, tarih/saat alanlarına datetime ve sayı/yüzde alanlarına uygun cast tanımla.
- Canlı MySQL sunucusu CHECK constraint'lerini zorlamalıdır (MySQL 8.0.16+). Daha eski MySQL sürümünde model/admin doğrulaması zorunlu koruma katmanıdır; canlıya geçmeden önce sürüm doğrulanır.

Seed kuralları:

- A1, A2, B1, B2, C1 ve C2 seviyelerini ayrı ve idempotent bir seeder oluşturur. Aynı seeder tekrar çalıştığında duplicate kayıt oluşturmaz veya adminin sonradan değiştirdiği ayarları ezmez.
- A1–C1: sequence sırasıyla 1–5, has_exam=true, is_active=true, question_count=NULL, pass_percentage=60. C2: sequence=6, has_exam=false, is_active=true, question_count=0, pass_percentage=NULL.
- Mevcut DatabaseSeeder'ı çalıştırma/değiştirme; soru veya seçenek içerikleri verilmedikçe soru/şık seed etme.

Migration ve doğrulama güvenliği:

- Mevcut migration dosyalarını değiştirme; yalnız yeni migration dosyaları oluştur. Mevcut verileri silme ve MariaDB/MySQL uyumluluğunu koru. Uzun placement-table constraint adlarını MariaDB/MySQL'nin 64 karakter sınırını aşmayacak açık, kısa adlarla tanımla.
- Kullanıcı açıkça onaylamadıkça migrate, db, database reset veya drop database komutlarını çalıştırma. Onaylı migration yalnız kapsamındaki pending migration'ları çalıştırır; reset/drop veya mevcut veriyi silme kesinlikle yapılmaz.
- Doğrulamada güvenli migration syntax/schema kontrolü ve model syntax kontrolü kullan. Yerel MariaDB'yi sıfırlayan veya değiştiren test komutları çalıştırılmaz; bunun için izole bir test veritabanı gerekir.
- Bu kapsam tamamlandığında dosyaları, tabloları, önemli foreign key/unique constraint'leri, snapshot yapısını, status/result alanlarını, seed yaklaşımını, çalıştırılan doğrulamaları ve açık teknik sorunları kısa ve somut olarak raporla. Kapsam dışı özellik geliştirme ve burada dur.

5. Route kuralları
Yeni route eklemeden veya taşımadan önce mevcut route'ları incele, çakışmaları belirle, yapılacak değişikliği bildir ve yalnızca onaylanan değişikliği yap.

6. Veritabanı
Frontend/içerik işlerinde mevcut migration, seeder veya factory'leri değiştirme; migration yalnız açıkça istenen özellikler için oluşturulur. Sonraki DB değişikliklerinde MySQL/MariaDB uyumluluğunu koru.

7. Git disiplini
Dosya değiştirmeden önce git status kontrol et. Repository'deki mevcut değişikliklerin bu göreve ait olduğunu varsayma.

Kullanıcı istemedikçe mevcut değişiklikleri discard/restore/reset/clean/stash etme veya üzerlerine yazma.
Açık talep olmadan şu yıkıcı komutları kullanma:
git reset --hard
git clean -fd
git checkout -- .
.env, parola, secret, cache veya yerel database dosyalarını commit etme.

8. Secret'lar
.env içindeki database/SMTP parolası, API key veya diğer secret'ları gösterme, hard-code etme, dokümana veya frontend'e kopyalama ve commit etme.

9. Çalışma yöntemi
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

Onay ekranı standardı — [YAPILDI]:
- Kullanıcı başlatmalı arşivleme, silme, kalıcı silme ve geri döndürülemez yönetici işlemlerinin onayı, yorumlardaki sayfa içi ALA modalı ile aynı görünüm ve davranışta olmalıdır.
- Normal Blade/form akışları `x-action-confirmation-modal`, Livewire akışları `x-review-action-modal` veya aynı görsel kabuğu kullanır. Yeni paralel onay tasarımı, tarayıcı `confirm()` veya Livewire `wire:confirm` kullanılmaz.
- Mevcut eşdeğer Livewire silme modalları bu standardı zaten karşılar. Parola/2FA gibi güvenlik doğrulaması gereken akışlarda parola/ikinci doğrulama zorunluluğu korunur; yalnız modalın görsel kabuğu bu standarda uyar.

10. Tahmin etme
Belirsizse projeyi incele. Özellikle mevcut route isimleri, login redirect'i, authenticated home, mevcut frontend ve görsel kullanımını uydurma.

11. Kullanıcı türleri ve erişim
Sistemde üç tür kullanıcı var. 
1) Login gerektirmeyen kullanıcı:
Ana Sayfa, Başarılarımız, Kurslarımız, Kampanyalarımız, Şubelerimiz ve Seviye Tespit Sınavı'nın tanıtım sayfasına ulaşabilir.
2) Login gerektiren kullanıcı:
Ana Sayfa, Başarılarımız, Kurslarımız, Kampanyalarımız, Şubelerimiz sayfaları ile birlikte Seviye Tespit Sınavı'nı başlatma, sınav kayıtları ve sonuçlarına da ulaşabilir.
3) Admin Kullanıcı:
/admin ile admin panel de dahil bütün sayfalara ulaşabilir.

Mevcut Level/Sub Level/Theme/Exercise/Question alanlarını ayrıca istenmedikçe yeniden tasarlama.

12. Yorumlar (Reviews) Sistemi
Durum: [YAPILDI — yerel uygulama]. Canlıdaki eski/demo kayıtların temizliği ve SEO doğrulaması ayrı bir TODO'dur.

Amaç:
Ana sayfadaki Reviews bölümü gerçek, moderasyonlu üye yorumlarını gösterir. Üyeler yorum bırakır; admin onay, ret, düzenleme ve arşivleme işlemlerini yönetir.

Veri modeli ve görünürlük:
- `reviews`: nullable `user_id`, nullable `branch` (Ortaca/Dalaman/Köyceğiz), düz metin `content`, 1–5 `rating`, `pending / approved / rejected / archived` `status`, nullable `approved_by / approved_at`, nullable `display_order`, timestamps ve geçmişteki kayıt uyumluluğu için `softDeletes` içerir. İndeksler `(status, created_at)` ve `branch` üzerindedir.
- Kullanıcı başına aynı anda en fazla bir pending yorum kuralı uygulama katmanında zorlanır.
- Public sorgular yalnız silinmemiş `status=approved` yorumları döndürür. Pending, rejected, archived, demo/test ve statik demo fallback metinleri public HTML'de yer alamaz.

Yetki ve arşiv politikası:
- Yorum yazma ve “Yorumlarım” login gerektirir; girişsiz ziyaretçi mevcut ALA login akışına gider. Admin de yorum oluşturabilir.
- Üye pending/rejected yorumunu düzenleyebilir; approved yorumunu düzenleyemez. Kendi yorumundaki normal silme işlemi artık `archived` durumuna taşır.
- Admin aktif tüm yorumları onaylayabilir, reddedebilir, düzenleyebilir, sıralayabilir veya arşivleyebilir. Arşivlenen yorum düzenleme/onay/red ile yeniden aktifleşemez.
- Normal silme yoktur: yorum önce arşivlenir. Yalnız admin listesindeki Arşiv sekmesinden kalıcı silme yapılabilir. Önceki soft-delete kayıtları da Arşiv sekmesinde görünür ve aynı kalıcı silme akışıyla temizlenebilir.

Görüntüleme ve yönetim:
- Ana sayfa Reviews alanı üç gerçek onaylı kartı gösterir: en eski, en yeni ve en yeniden önceki. Yeterli kayıt yoksa eksik slot gizlenir; statik/demo fallback kullanılmaz.
- Public `/yorumlar` yalnız onaylı yorumları, şube filtresi ve sayfalama ile gösterir; ana sayfada “Tümünü Gör” bağlantısı vardır.
- `/yorumlarim` Livewire formu ve durum rozetleriyle üyeye ait aktif kayıtları gösterir. Admin listesi varsayılan olarak pending (eskiden yeniye) sonra approved (yeniden eskiye) gösterir; rejected ve archived için ayrı filtre/sekme vardır.
- Admin yorum düzenleme ekranında `İptal` ve sağında `Güncelle` düğmesi bulunur.

Teknik yaklaşım — [YAPILDI]:
- Review modeli `user`, `approver` ilişkileri; User modelinde yalnız gerekli `reviews` ilişkisi; mevcut auth/fillable davranışı korunur.
- Üye tarafı Livewire + policy + rate limit; admin tarafı mevcut `cont_*` controller ve Livewire liste deseni; admin menüsü aktiftir.
- Türkçe/İngilizce metinler `dictt` çeviri dosyalarındadır. Ayrı enum, ikinci auth veya ikinci yorum sistemi kurulmaz.

Yayın/SEO temizliği — [TODO — canlı veri denetimi gerekli]:
- Yayındaki kayıtlar salt-okunur incelenerek gerçek approved yorumlar; demo/test, pending, rejected ve archived kayıtlar kesin ayrılır. Gerçek approved yorumlar korunur.
- Demo/test kayıtları arşivlenir veya kalıcı silme için Arşiv'e alınır; public HTML kaynak çıktısında bunların hiçbir metni kalmaz.
- Girişsiz ziyaretçiyle ana sayfa, `/yorumlar`, şube filtresi, sayfalama ve HTML kaynak çıktısı doğrulanır. Ana sayfa `noindex` olmaz; temiz canlı çıktı sonrasında sitemap güncellenir ve Search Console yeniden tarama isteği yapılır.

13. Public frontend geliştirme yol haritası
Durum: Tamamlanan maddeler aşağıda `[YAPILDI]` olarak işaretlidir. Kalan her TODO, kullanıcı onayıyla ve mevcut ALA uygulaması korunarak ele alınır.

Genel ilkeler:
- Gerçek olmayan öğrenci sayısı, başarı oranı, yorum, eğitmen veya görsel production'da kullanılmaz. Önce kaynak, güncellik ve gerekli açık izinler belirlenir.
- Yeni içerik yapıları yalnız gerçek yönetim ihtiyacı oluştuğunda eklenir; onaysız genel CMS/refactor veya yeni frontend framework kurulmaz.
- Yeni public/admin route eklemeden önce mevcut route'lar incelenir; internal URL'ler route helper, görseller `asset()` veya mevcut güvenli medya yaklaşımıyla kullanılır.
- Reviews, yıllık kazanan listeleri ve editoryal başarı hikâyeleri üç ayrı içerik türüdür; birbirlerinden otomatik üretilmez veya aynı veri modeline zorlanmaz.

Tamamlanan özellikler:
1) “Sana uygun programı bul” — [YAPILDI]
   - Public `/sana-uygun-programi-bul` akışı giriş/AI/kişisel veri kaydı gerektirmeden 3–4 kısa seçimle program kartı üretir.
   - Giriş yapmış kullanıcının en güncel onaylı placement test sonucu seviye girdisidir. Sonuç yoksa, sınav yarım/pending ise veya ziyaretçi giriş yapmamışsa kullanıcı kendi seviyesini beyan eder.
   - Sonuçta program, uygunsa alternatif program ve üç şubeyle görüşme bağlantıları vardır. Yeni bir sınav motoru veya ayrı veri sistemi kurulmadı.

2) Haberler MVP'si — [YAPILDI — canlıya alma doğrulaması bekliyor]
   - `media_assets`, `news` ve `news_content_blocks` ile bağımsız editoryal haber yapısı; admin CRUD, taslak/yayın/zamanlama/arşiv, kalıcı silme, slug ve sitemap desteği vardır.
   - Haber içeriği sıralı düz metin, görsel, ses, video, dosya ve HTTPS haricî bağlantı blokları içerebilir. Yüklenen medya korumalı uygulama endpoint'i üzerinden sunulur.
   - `display_location` tek seçimdir: `none` (yalnız Haberler listesi/detayı), `homepage` veya `hero`. Public görünürlük yayın/zaman aralığına bağlıdır. Ana sayfadaki haberler uygun sıralama ile; hero haberleri en çok iki öğe olarak mevcut ALA/şube slaytlarından önce gelir.
   - Public `/haberler` liste/detay sayfaları, ana sayfa kart kaydırıcısı ve hero entegrasyonu tamamlandı. Canlıya çıkarken migration, private medya ve sitemap ayrıca doğrulanır.

Kalan aşamalar — kolaydan zora, her biri ayrı onayla:
3) İçerik ve izin envanteri — [TODO, kodsuz hazırlık]
   - Her şube için kullanılabilecek gerçek sınıf/etkinlik görselleri, eğitmen bilgileri, güncel istatistikler ve sorumlu kişiler; ayrıca başarı verileri için kanıt ve izinler listelenir.
   - Hero, Başarılarımız ve eğitmen kartlarının görsel oranı, minimum çözünürlüğü ve onaylı kısa metni netleştirilir.
   - 12. bölümdeki yorumların canlı veri/SEO temizliği bu envanterden bağımsız öncelikli bakım işidir.

4) Statik güven ve yönlendirme bölümleri — [TODO]
   - ALA kimliğini koruyarak CEFR yolculuğu (A1 → A2 → B1 → B2 → C1 → C2) ve “ALA’da eğitim nasıl ilerliyor?” dört adımlı açıklaması eklenebilir.
   - Önce onaylı sabit içerik uygulanır. Placement test sonuçları kullanılmaya başladığında seviyeler için ikinci, çelişen bir kaynak oluşturulmaz.

5) Başarılarımız: veri, izin ve yıllık kayıt sözleşmesi — [TODO]
   - Mevcut `/basarilarimiz` URL'si korunur. Verinin asıl kaynağı tasarım görselleri değil veritabanıdır; `public/frontend/images/achievements/` altındaki 2024/2025/2026 JPEG'leri yalnız görsel referans/şablondur ve public sayfada kullanılmaz.
   - Yıllık “Başarı Tabloları” ile seçilmiş editoryal başarı hikâyeleri ayrı tutulur. Kısa Reviews yorumları bunların yerine geçmez veya onlardan türetilmez.
   - Her öğrencinin gerçek adı private tutulur. İsim yayın izni en az `unknown / granted / denied`, izin tarihi ve iç notu ile kaydedilir. İzin yoksa veya belirsizse public sayfa, SEO metası, sitemap ve paylaşılabilir dışa aktarımlarda anonim gösterim kullanılır.
   - İsim izni; başarı hikâyesi metni, fotoğraf, video veya ses için ayrıca izin alındığı anlamına gelmez. Bu izinler ayrı alan/kanıt olarak değerlendirilir.
   - 2024 de diğer yıllar gibi öğrenci bazlı, isimli kaynak kayıtlarla girilecektir. 2026 için daha sonra iletilecek kişi bilgileri şu an eklenmez.

6) Yıllık başarı kayıtları ve admin manuel girişi — [TODO]
   - Önerilen `achievement_years`: benzersiz yıl, public başlık/açıklama, `draft / published / archived`, sıralama, yayın ve denetim alanları.
   - Önerilen `achievement_entries`: `achievement_year_id`, private gerçek ad, public-güvenli gösterim erişicisi, isim izin durumu/tarihi/notu, üniversite, bölüm/program, yerleştirme/başarı türü, burs/sonuç notu, isteğe bağlı şube/program etiketi, doğrulama, sıralama ve durum alanları.
   - İlk sürüm adminin tek tek eklemesi, düzenlemesi, taslak ön izlemesi ve yayınlamasıyla başlar. Branch yapısı bugün statik olduğundan bu aşamada zorunlu bir `branches` FK'sı kurulmaz.
   - Private gerçek ad ve izin notu public Blade, meta, sitemap, public API veya paylaşılabilir broşür çıktısına asla doğrudan verilmez.

7) Başarılarımız public listesi — [TODO]
   - Yalnız published yıl ve published/doğrulanmış kayıtlar gösterilir; yıl filtresi, responsive HTML listesi/kartları ve tek bir anonimleştirme kuralı kullanılır.
   - Liste için özgün, okunabilir HTML tercih edilir; mevcut JPEG şablonları sayfaya doğrudan görsel olarak basılmaz.

8) Seçilmiş editoryal başarı hikâyeleri — [TODO]
   - `success_stories` yıllık kazanan girdisine isteğe bağlı bağlanabilir ancak ayrı draft/published/featured yaşam döngüsü, başlangıç–sonuç metni, yıl/şube/program alanları ve ayrı hikâye/medya izinleri taşır.
   - Bir kazananın public kaydı otomatik hikâyeye dönüşmez; hikâye ve fotoğraf yalnız açık izinle, gerektiğinde insan onayıyla yayınlanır.
   - Public sayfanın ana omurgası yıl bazlı, doğrulanmış başarı listesidir. Başarı hikâyeleri bu listenin yerine geçen ya da otomatik üretilen içerikler değil; yalnız seçilmiş az sayıdaki öğrenci için gösterilen ayrı “Öne Çıkan Başarı Hikâyeleri” kartları ve gerekirse detaylarıdır.
   - Hikâye, öğrencinin başlangıç/hedef/süreç/sonuç anlatımı, izinli kısa alıntısı ve yalnız ayrı izin varsa görsel, video veya ses içerebilir. Gerçek olmayan başarı vaadi, doldurma amacıyla hikâye, izinsiz öğrenci bilgisi veya statik demo kullanılmaz.
   - İsim gösterme izni; hikâye metni, alıntı, fotoğraf, video, ses, sosyal paylaşım metası veya broşür izni anlamına gelmez. Her yayın türü için kaynak/izin, izin tarihi, sürümü ve iç denetim notu ayrı saklanır; izin yoksa hikâye taslakta kalır veya anonim/medyasız biçimde ayrıca onaylanır.
   - Uygulama sırası: önce onaylı hikâye içerik/izin envanteri; sonra `success_stories` veri sözleşmesi ve admin taslak-yayın akışı; ardından Başarılarımız sayfasındaki kart/liste görünümü; en son gerekli görülürse public hikâye detayı, paylaşım metaları ve izinli medya. Her aşama ayrı onay gerektirir.

9) Broşür ve dışa aktarımlar — [TODO]
   - “Broşür hazırla / dışa aktar” yalnız veritabanındaki seçili yıl/kayıtlardan üretilir. Public/paylaşılabilir Excel, PDF ve JPG aynı public-güvenli gösterim/anonimleştirme kuralını kullanır.
   - Adminin iç kullanım Excel çıktısında gerçek ad ve izin takibi ancak yetkili yönetici için ayrıca değerlendirilebilir; bu çıktı public broşürle karıştırılmaz.
   - Uygulama sırası: önce Excel, sonra tarayıcıya uygun yazdırılabilir PDF, en son uzun liste/şablon yerleşimi doğrulandıktan sonra tasarımlı JPG/PDF. JPEG referansları yalnız tasarım yönü verir.

10) Toplu kazanan içe aktarma — [TODO]
   - Önce Excel/CSV alan eşleştirme, satır bazlı hata/çakışma ön izlemesi, duplicate kontrolü ve admin onayından sonra taslak kayıt oluşturma yapılır; dosya doğrudan yayın yapmaz.
   - PDF/OCR alma sonraki aşamadır: yalnız taslak/staging üretir, insan doğrulaması ve izin kontrolü olmadan public kayda dönüşmez.

11) Eğitmen profilleri — [TODO]
   - Yalnız gerçek ve izinli içerik hazır olduğunda bağımsız editoryal eğitmen profilleri eklenir: ad, görev/uzmanlık, kısa biyografi, fotoğraf, aktiflik, sıralama ve gerekirse şube ilişkisi.
   - Eğitmenler giriş yapan User hesaplarına bağlanmaz; authentication, user rolleri ve mevcut admin kullanıcıları değişmez.

12) Merkezi şube yönetimi — [TODO, yalnız ihtiyaç oluşursa]
   - Ortaca, Dalaman ve Köyceğiz bugün statik yapı/route'larla çalışır; yalnız metin veya görsel değişikliği için CMS'e taşınmaz.
   - Yeni şube veya sık adres/iletişim/harita/görsel/istatistik güncellemesi ihtiyacı oluşursa ayrı kapsam ve route denetimiyle Branch CMS değerlendirilir.

14. Public SEO ve arama görünürlüğü
Durum: [TODO — içerik onayı ve ayrı uygulama adımları gerekli]

İlkeler:
- `meta name="keywords"` Google Search tarafından sıralama veya indeksleme için kullanılmaz. Anahtar kelime doldurma, keyword stuffing veya sayfa başına anahtar kelime listesi üretme işi yapılmaz.
- Eski public/guest Blade head'lerindeki boş ya da dolu tüm `meta name="keywords"` etiketleri, ayrı ve düşük riskli bir SEO bakım adımında kaldırılır. Bu işlem anlamlı title, description veya robots etiketlerini değiştirmez.
- SEO metinleri gerçek ALA içeriğine, mevcut hizmete, doğrulanmış şube bilgilerine ve seçili dile dayanır; yalnız arama trafiği için pazarlama metni uydurulmaz.
- Giriş, kayıt, kullanıcı profili, üye alanı, admin ve sınavın kişisel sonuç/kayıt ekranları indexlenmez; public sayfaların index kuralları bunlardan ayrı değerlendirilir.

Uygulama sırası — her adım ayrıca onaylanır:
1) Metadata envanteri ve keyword temizliği — [TODO]
   - Tüm indexlenebilir public route'lar için mevcut `title`, `meta description`, canonical, robots ve sosyal paylaşım metaları envanteri çıkarılır.
   - `meta name="keywords"` etiketleri kaldırılır; yalnız Google açısından etkisiz olan bu etiketler yerine yeni bir meta etiketi eklenmez.
   - Mevcut `robots.txt`, sitemap üretimi, dil URL'leri ve auth gerektiren URL'ler salt-okunur olarak denetlenir; noindex kararları açıkça doğrulanır.

2) Sayfa bazlı title ve description — [TODO]
   - Ana Sayfa, Kurslarımız ve tekil kurslar, Şubelerimiz ve tekil şubeler, Başarılarımız, Kampanyalarımız, Haberler ve haber detayı için özgün, açıklayıcı, locale uyumlu title/description metinleri hazırlanır.
   - Metinler içerik sahibi tarafından onaylanır; her sayfaya aynı açıklama veya anahtar kelime dizisi kopyalanmaz.
   - Haber detayındaki mevcut dinamik title/description yaklaşımı korunur; yeni içerik yapılarıyla çelişen ikinci bir kaynak oluşturulmaz.

3) Canonical ve paylaşım görünümü — [TODO]
   - Her indexlenebilir public sayfa kendi route helper tabanlı canonical URL'sine sahip olur; query/filter veya dil varyasyonları önce mevcut route yapısı incelenerek ele alınır.
   - Open Graph/Twitter paylaşım başlığı, açıklaması ve görseli eklenir. Varsayılan ALA görseli ile haberlerin izinli kapak görseli ayrılır; private medya ya da izinsiz öğrenci görseli paylaşım metasına verilmez.

4) Yapılandırılmış veri ve yerel görünürlük — [TODO, doğrulanmış veri şart]
   - Kurum ve şube bilgileri kesinleştikten sonra `Organization` ve uygun olduğunda her şube için `LocalBusiness` yapılandırılmış verisi değerlendirilir.
   - Adres, telefon, çalışma saati, harita URL'si, logo ve sosyal bağlantılar doğrulanmadan schema eklenmez. Yorum, puan veya başarı verisi gerçek ve yayın izni açık olmadıkça schema ile işaretlenmez.

5) Canlı doğrulama — [TODO]
   - Yayın sonrası rendered HTML, canonical/robots, sitemap, mobil görünüm ve sosyal paylaşım ön izlemesi kontrol edilir.
   - Search Console URL Denetimi ve sitemap raporu ile indekslenebilir public sayfalar doğrulanır; demo, pending, rejected, archived veya üyeye özel içeriklerin indekslenmediği yeniden kontrol edilir.

15. KVKK, gizlilik ve yayın izinleri
Durum: [TODO — hukukî metin ve işleme şartları kurumun KVKK danışmanı/avukatı tarafından onaylanmadan uygulanmaz]

Temel ayrım:
- `KVKK Aydınlatma Metni`, kişisel veri toplanırken yapılan zorunlu bilgilendirmedir; veri sorumlusu, amaç, aktarım, toplama yöntemi/hukukî sebep ve ilgili kişi haklarını ALA'nın gerçek uygulamasına göre açıklar.
- Açık rıza yalnız gerçekten rıza gerektiren belirli ve isteğe bağlı işlemler için alınır. Aydınlatma metni ile aynı checkbox/metin yapılmaz; hizmet, üyelik veya iletişim talebi gereksiz pazarlama/yayın rızasına bağlanmaz.
- İsim, yorum, başarı kaydı, hikâye, fotoğraf, video, ses ve ticari ileti izni birbirinin yerine geçmez. Öğrenci reşit değilse veli/vasi doğrulama ve izin süreci hukukî olarak ayrıca belirlenir.

Yerleşim ve kapsam:
1) Kalıcı erişim — [TODO]
   - Public footer'a `KVKK Aydınlatma Metni`, `KVKK Başvuru / İlgili Kişi Hakları` ve hukuken/onaylı şekilde gerekli ise `Gizlilik Politikası` ile `Çerez Politikası` bağlantıları eklenir.
   - Metinlerin sorumlu kişi/iletişim kanalı, güncelleme tarihi ve sürümü kurumca doğrulanır. Genel footer bağlantısı, form anındaki bağlamsal aydınlatmanın yerine geçmez.

2) Form bazlı aydınlatma — [TODO]
   - İletişim formunda gönder düğmesinin yanında/üstünde ad, iletişim bilgisi, şube ve mesajın talebi yanıtlamak amacıyla işlendiğini açıklayan kısa metin ve ilgili sayfaya link bulunur.
   - Üyelik, profil fotoğrafı ve hesap yönetimi akışlarında hesap/güvenlik/iletişim verileri için kayıt öncesi veya veri elde edilirken bağlamsal aydınlatma eklenir. Mevcut Jetstream/Fortify akışı hukukî metin onayı olmadan değiştirilmez.
   - Yorum formu, onaylanan yorumun public HTML'de ve arama motorlarında görünür olabileceğini açıkça bildirir. Moderasyon, yorum sahibinin adı veya başka kişisel bilgisinin yayın izni değildir.
   - Seviye tespit başlatılmadan önce cevap/sonuç geçmişinin eğitim değerlendirmesi amacıyla saklanmasına ilişkin bağlamsal aydınlatma verilir. Mevcut sınav/sonuç verisinin yayınlanması veya pazarlama amacıyla kullanılması için ayrı dayanak gerekir.
   - “Sana uygun programı bul” bugün kalıcı kişisel veri kaydetmez; gelecekte danışmanlık lead'i, geri arama veya iletişim alanı eklenirse aynı form bazlı aydınlatma ve gerekirse ayrı rıza akışı kurulmadan veri saklanmaz.

3) Ayrı rıza ve kanıt kaydı — [TODO]
   - İsteğe bağlı e-posta/SMS/WhatsApp/arama pazarlaması için zorunlu olmayan, önceden işaretlenmemiş, amaç kanalı açık ayrı rıza alınır. İletişim talebini yanıtlamak için gereken veri işleme ile pazarlama izni birleştirilmez.
   - Başarılarımız, başarı hikâyeleri, haber görselleri ve sosyal paylaşım için her isim, alıntı, fotoğraf, video, ses ve dışa aktarım kullanımının izin durumu/kanıtı/tarihi/metin sürümü saklanır. İzin bilinmiyorsa public, SEO, sitemap ve paylaşılabilir çıktı anonim veya kapalı kalır.
   - Analytics, reklam pikseli veya zorunlu olmayan üçüncü taraf çerezleri eklenirse, bunlar için ayrı çerez değerlendirmesi ve gerekiyorsa rıza yönetimi yapılır. Bugün böyle bir sistem olduğu varsayılmaz.

4) Uygulama ve denetim sırası — [TODO]
   - Önce mevcut veri envanteri, saklama süreleri, veri sorumlusu bilgisi, aktarım noktaları ve hukukî dayanaklar kurum tarafından doğrulanır; metinler hukukî danışman tarafından onaylanır.
   - Ardından public politika/başvuru sayfaları, footer bağlantıları ve form bazlı kısa aydınlatmalar tasarlanır; gerekli olan açık rızalar bağımsız checkbox ve kanıt kaydıyla uygulanır.
   - Son olarak girişsiz, üye ve admin görünümü; veri gönderimi; izin geri çekme/başvuru kanalı; public HTML/SEO/sitemap görünürlüğü test edilir. Yeni bir alan veya medya türü eklenince bu envanter ve izin kapsamı yeniden değerlendirilir.
