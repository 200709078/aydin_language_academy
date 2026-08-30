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
Durum: Plan aşağıda karara bağlanmıştır; kullanıcı açıkça onay vermeden kodlamaya geçilmez.

Amaç:
Ana sayfadaki statik Reviews bölümünün gerçek verilere bağlanması. Üyeler yorum yazabilir, admin moderasyon yapar.

Veri modeli — reviews tablosu:
- id, nullable user_id (nullOnDelete; admin kendi de yorum girebilmeli), nullable branch_id (Ortaca/Dalaman/Köyceğiz şubesine bağlama; şube seçimi opsiyoneldir), content (düz metin, HTML yok), rating (unsigned tinyint 1–5, zorunlu), status (pending / approved / rejected), nullable approved_by (users FK, nullOnDelete), nullable approved_at, nullable display_order (admin sıralama/sabitleme), timestamps ve softDeletes.
- İndeksler: (status, created_at) ve (branch_id).
- Kullanıcı başına aynı anda en fazla bir pending yorum olabilir; kural uygulama katmanında zorlanır (spam koruması).
- Migration yalnız yeni dosya olarak eklenir; mevcut migration'lara dokunulmaz; constraint/index adları MariaDB/MySQL 64 karakter sınırını aşmayan kısa adlardır.

Yetki matrisi:
- Yorum yazma: login gerektirir; girişsiz ziyaretçi mevcut ALA login akışına yönlenir. Admin de yorum girebilir.
- Üye (kendi yorumu): pending iken düzenleyebilir ve silebilir (soft). Approved edildikten sonra düzenleyemez ama soft silebilir. Rejected yorumunu düzenleyip tekrar gönderebilir (yorum tekrar pending olur) ve silebilir.
- Admin: tüm yorumları görebilir; onaylayabilir (approved_by/approved_at dolar), reddedebilir, her durumda düzenleyebilir, soft silebilir, display_order ile görüntüleme sırasını yönetebilir.
- Public görünürlük: yalnız status=approved ve silinmemiş yorumlar.

Görüntüleme kuralları:
- Ana sayfa Reviews bölümü (public): üç kart. SOL = ilk yapılan yorum (en eski), ORTA = en son yapılan yorum, SAĞ = en sondan bir önceki yorum. Yeterli onaylı yorum yoksa eksik slot gizlenir; hiç onaylı yorum yoksa mevcut statik içerik gösterilir.
- Tüm Yorumlar sayfası (public, login gerekmez): /yorumlar — tüm onaylı yorumlar sayfalı olarak; şubeye göre filtre içerir. Ana sayfada bu sayfaya "Tümünü Gör" linki olur.

Yayın/SEO temizliği — ayrı onay bekleyen TODO:
- Yayındaki mevcut yorum kayıtları önce salt-okunur olarak incelenir; demo/test, pending, rejected ve gerçek approved yorumlar kesin olarak ayrılır.
- Ana sayfa ve /yorumlar public sorguları yalnız silinmemiş `status=approved` kayıtları döndürmelidir. Pending, rejected, demo/test kayıtları ve statik demo fallback'i public HTML çıktısında yer alamaz.
- Demo/test kayıtları, kullanıcı tarafından hedefleri onaylandıktan sonra tercihen soft-delete veya public olmayan duruma alınır; gerçek approved yorumlara dokunulmaz.
- Değişiklikten sonra girişsiz ziyaretçiyle ana sayfa, /yorumlar, sayfalama ve HTML kaynak çıktısı doğrulanır; demo/test/pending/rejected metinlerin hiçbiri görünmemelidir.
- Ana sayfa indekslenmeye devam eder; bu iş için ana sayfaya `noindex` veya robots.txt engeli eklenmez. Canlı çıktı temizlendikten sonra sitemap güncellemesi ve Search Console yeniden tarama isteği uygulanır.

Admin listeleme sıralaması:
1) Önce onaysızlar: pending, created_at ASC (ilk yapılan bekleyen yorum üstte).
2) Sonra onaylılar: approved, created_at DESC (en yeni onaylı üstte).
3) Rejected yorumlara varsayılan listede yer verilmez; filtre/sekme ile erişilir.

Teknik yaklaşım:
- Review modeli; user, branch ve approver ilişkileri. Mevcut User modeline yalnız gerçekten gereken ilişki (reviews) eklenir; fillable, auth ve kullanıcı türü davranışı değişmez.
- PHP enum proje standardı yoksa enum mimarisi kurulmaz; status string kolon olarak tutulur.
- Frontend yorum formu Livewire ile; admin tarafı mevcut cont_* controller + Livewire liste desenine uygun geliştirilir. Admin sol menüsünde Site Ayarları altındaki pasif Reviews öğesi aktifleştirilir.
- Yetki: Policy ile üye yalnız kendi yorumunda işlem yapar; admin işlemleri mevcut admin route grubu korumasını kullanır.
- Spam: yorum POST route'una throttle; Blade escape varsayılanı ile çıktı güvenliği.
- Route'lar: GET /yorumlar (public), POST /yorumlar (auth + throttle), GET /yorumlarim (auth, üyenin kendi yorumları), PATCH /yorumlar/{id} (auth + policy, yalnız pending), DELETE /yorumlar/{id} (auth + policy, soft delete); admin işlemleri /admin/reviews* altında mevcut admin grubunda.
- Metinler dictt çeviri dosyalarına tr/en eklenir.

Aşamalar (her aşama ayrıca onaylanarak ilerler):
1) Migration + Review modeli + ilişkiler + admin CRUD/onay/red/sil + admin menüsü aktifleştirme + listeleme sıralaması.
2) Ana sayfa üç kartı + /yorumlar public sayfası + Tümünü Gör linki.
3) Üye yorum formu + /yorumlarim + policy + throttle.

13. Public frontend geliştirme yol haritası
Durum: Aşağıdaki sıra, uygulanabilirliği nispeten kolay olandan zora doğru planlanmış TODO listesidir. Her aşama kullanıcı tarafından ayrıca onaylanmadan kodlanmaz; mevcut ALA uygulaması, admin, authentication ve legacy alanlar korunur.

Genel ilkeler:
- Gerçek olmayan öğrenci sayısı, başarı oranı, yorum, eğitmen veya görsel production'da kullanılmaz. Önce kaynak, güncellik ve gerekli açık izinler belirlenir.
- Yeni içerik yapıları yalnız gerçekten yönetim ihtiyacı oluştuğunda eklenir; onaysız genel CMS/refactor veya yeni frontend framework kurulmaz.
- Yeni public/admin route eklenmeden önce mevcut route'lar incelenir; internal linkler route helper, görseller asset()/mevcut güvenli medya yaklaşımı ile kullanılır.
- Reviews ile editoryal başarı hikâyeleri aynı veri modeli değildir. Reviews moderasyonlu kullanıcı içeriği olarak 12. bölümdeki kurallara; başarı hikâyeleri kurumun izinli/doğrulanmış editoryal içeriğine tabidir.

Aşamalar (kolaydan zora, her biri ayrı onayla):
1) İçerik ve izin envanteri — kodsuz hazırlık
   - Her şube için kullanılabilecek gerçek sınıf/etkinlik görselleri, eğitmen bilgileri, izinli öğrenci başarıları, güncel istatistikler ve sorumlu kişileri listelenir.
   - Hero, Başarılarımız, eğitmenler ve şube kartları için görsel oranı, minimum çözünürlük ve onaylanan kısa metin ihtiyacı netleştirilir.
   - 12. bölümdeki yayın/SEO yorum temizliği bu envanterden bağımsız öncelikli bakım işidir.

2) Statik güven ve yönlendirme bölümleri
   - Mevcut ALA kimliğini koruyarak CEFR yolculuğu (A1 → A2 → B1 → B2 → C1 → C2) ve “ALA’da eğitim nasıl ilerliyor?” dört adımlı açıklaması eklenebilir.
   - Bu alanlar önce onaylı, sabit içerik olarak yapılır; mevcut placement test sonuçları kullanılmaya başlandığında seviyeler için ikinci, çelişen bir kaynak oluşturulmaz.
   - Bu aşama gerçek eğitim/başarı vaadi uydurmaz; mevcut genel İngilizce ve seviye tespit tanıtımına bağlanır.

3) Başarılarımız sayfasının editoryal dönüşümü
   - Mevcut /basarilarimiz URL'si korunur; izinli ve doğrulanmış öğrenci/veli başarı hikâyeleri, başlangıç–sonuç, yıl, ilgili program/şube ve izinli medya ile sunulur.
   - İlk sürüm içerik yeterliyse kontrollü Blade içeriğiyle başlayabilir; hemen yeni admin/CMS kurmak zorunlu değildir.
   - Kısa kullanıcı yorumları yerine geçmez ve otomatik olarak Reviews verisinden türetilmez.

4) “Sana uygun programı bul” — tamamlandı
   - Public `/sana-uygun-programi-bul` akışı, giriş/AI/kişisel veri kaydı gerektirmeyen 3–4 kısa seçimle uygun program kartını üretir.
   - Giriş yapmış kullanıcının en güncel onaylı placement test sonucu seviye girdisi olarak kullanılır. Sonuç yoksa, sınav yarım/pending ise veya ziyaretçi giriş yapmamışsa kullanıcı kendi seviyesini beyan eder.
   - Sonuç ekranında program kartı, uygun olduğunda alternatif program ve üç şubeye doğrudan görüşme bağlantıları bulunur; ana sayfa ve footer’dan erişilebilir.
   - Yeni/ikinci seviye tespit sınavı, soru havuzu, sınav motoru, ayrı admin sistemi veya veritabanı kaydı oluşturulmadı. Sonuç açıkça program önerisi olarak sunulur; resmî sınav sonucu ile karıştırılmaz.

5) Haberler MVP'si — ilk dinamik editoryal özellik
   - Bağımsız haber modeli/admin CRUD planlanır; mevcut kampanya veya legacy yapılara zorla bağlanmaz.
   - İlk alanlar: title, benzersiz slug, short summary, body, cover image, status veya is_active, published_at, nullable sort_order ve display_location.
   - display_location tek seçimdir: none (yalnız Haberler listesi/detayı), homepage veya hero. Admin arayüzünde segmented control/radio kullanılır; aynı haber iki öne çıkarma alanında aynı anda yer alamaz.
   - Ana sayfa için seçili haberler sort_order ASC, eşitse published_at DESC ile; Haberler listesi published_at DESC ile sıralanır. Hero'da en fazla 1–2 haber yer alır; haber yoksa mevcut sabit ALA/şube slaytları güvenli varsayılan olarak kalır.
   - Haber listesi/detayı, admin yönetimi, medya, slug, public görünürlük ve sitemap etkisi bu aşamanın kapsamındadır; route çakışması ve mevcut sitemap akışı önce incelenir.

6) Eğitmen profilleri
   - Yalnız gerçek ve izinli içerik hazır olduğunda bağımsız editoryal eğitmen profilleri eklenir: ad, görev/uzmanlık, kısa biyografi, fotoğraf, aktiflik, sıralama ve gerekirse şube ilişkisi.
   - Eğitmenler giriş yapan User hesaplarına bağlanmaz; authentication, user rolleri ve mevcut admin kullanıcıları değiştirilmez.
   - Önce ana sayfada sınırlı kartlar, ihtiyaç doğrulanırsa ayrıntı sayfası/admin yönetimi düşünülür.

7) Merkezi şube yönetimi (yalnız ihtiyaç oluşursa)
   - Ortaca, Dalaman ve Köyceğiz şubeleri şu anda statik yapı/route'larla çalışır; yalnız metin veya görsel güncellemesi için CMS'e taşınmaz.
   - Yeni şube açılması veya adres, telefon, harita, sosyal bağlantı, görsel ve istatistiklerin admin tarafından sık güncellenmesi gerektiğinde ayrı Branch CMS değerlendirilir.
   - Böyle bir geçiş header, footer, ana sayfa sliderı, iletişim, reviews ve mevcut şube linklerindeki hard-code kullanımları etkileyebileceğinden kapsam/route denetimiyle ayrı proje olarak ele alınır.
