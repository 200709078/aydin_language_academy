# Bursluluk: admin ekranları öncesi altyapı

İş kurallarının kaynağı `AGENTS.md` bölüm 11'dir. Mevcut sekiz tablo, model ilişkileri, ortak işlem servisleri ve geliştirme seed'i bu kuralları uygular. Bu belge ekranların kullanacağı teknik sözleşmeyi tanımlar.

## Veri modeli

| Model | Amaç ve ilişkiler |
| --- | --- |
| `ScholarshipBranch` | ALA şubesi; oturumlarla ilişkili |
| `ScholarshipSchool` | Öğrencinin mevcut okulu; başvurularla ilişkili |
| `ScholarshipStudentLevel` | Mevcut sınıf/durum; başvurularla ilişkili |
| `ScholarshipExamGroup` | Bağımsız sınav grubu; oturumlarla ilişkili |
| `ScholarshipExamPeriod` | Başvuru ve sınav tarihleri, aktiflik ve başvuru açma anahtarı |
| `ScholarshipExamSession` | Dönem, şube, grup, sınav adı, tarih/saat, kapasite, askı ve arşiv |
| `ScholarshipApplication` | Hesap/dönem başvurusu, öğrenci bilgileri, onay, katılım, sonuçlar, iki yayın ve iki iletişim aşaması |
| `ScholarshipNotification` | Başvuruya bağlı kanal/aşama bazlı teknik gönderim kaydı |

Alan tipleri, varsayılanlar, indeksler ve CHECK/FK kısıtları `database/migrations/2026_09_07_100{000,100,200}_create_scholarship_*.php` dosyalarındadır. Bu migrationlar değiştirilmez; sonraki şema ihtiyaçları yeni migration ile karşılanır.

- Kullanıcı ile başvuru ilişkisi `User::scholarshipApplications()` üzerinden kurulur. Ayrı öğrenci hesabı tablosu yoktur.
- Başvuru numarası yeni kayıtta ULID olarak üretilir. `user_id + period_id` benzersizliği tek mevcut başvuruyu korur.
- Başvurudaki `session_id + period_id`, oturumun aynı döneme ait olmasını FK ile zorunlu kılar. Oturumun ve başvurunun dönem kimliği güncelleme servisinde değiştirilemez.
- Öğrenci adı, mevcut okul adı ve sınıf/durum adı dönemlik snapshot alanlarında korunur. İletişim adresleri güncel `User` kaydından okunur.
- Şube/sınıf/grup kodları benzersizdir. Okul adları benzersiz değildir; aynı isimli ayrı okullar olabilir.
- Dönem/oturum/ortak tanım silme işlemleri bağımlılık varsa reddedilir. Oturum arşivi başvuruları korur.
- Başvuru kalıcı silinince kendi teknik bildirim kayıtları silinir, kullanıcı ve ortak tanımlar korunur. Mevcut Jetstream hesap silme davranışı korunur: kullanıcı silinirse başvurunun `user_id` alanı NULL olur, dönemlik bilgiler kalır.

## Ortak yazma yolları

Controller, Livewire ve ilerideki import işlemleri başvuru/sonuç/katalog değişikliklerini aşağıdaki servislerden geçirir. Ekranlardan doğrudan `Model::update()`, `save()` veya toplu query update ile bu kurallar atlanmaz. Geliştirme seed'i yalnız örnek tanımları oluşturan ayrı bir kurulum yoludur.

Çağıran ekran, actor olarak `request()->user()` veya mevcut Livewire oturumunun kullanıcısını verir. Servisler hesabı ve gerektiğinde mevcut `type = admin` yetkisini tekrar kontrol eder. İstekten kullanıcı/yetki bilgisi türetilmez.

### `ScholarshipApplicationService`

| Metot | Sözleşme |
| --- | --- |
| `create(actor, data)` | Actor'ın kendi hesabına başvuru; `period_id`, `session_id`, `school_id`, `student_level_id`; isteğe bağlı `student_name` |
| `update(actor, id, data)` | `session_id`, `school_id`, `student_level_id`, `student_name`; üye sahipliği ve işlem kilitleri veya admin yetkisi |
| `delete(actor, id)` | Yetkili kalıcı silme, yer açma ve aynı dönemde yeniden başvuru imkânı |
| `approve(admin, id)` | Onay; yayın anahtarını kendiliğinden açmaz |
| `updateResult(admin, id, data)` | `attendance_status`, `score`, `correct_count`, `wrong_count`, `blank_count`, `scholarship_percentage` için ortak doğrulama |
| `publish(admin, ids, phase, published = true)` | `application` veya `result` aşamasında kayıt bazlı yayın; tek kayıt için tek elemanlı dizi |
| `markContact(admin, id, phase, reached)` | İlgili aşamanın manuel ulaşılma durumunu değiştirir |

`publish` sonucu `updated` ID listesi ve `skipped` ID/hata eşlemesidir. Eksik sonuç veya silinmiş kayıt diğer geçerli kayıtları durdurmaz. Seçim kapsamı ekranda gösterilir; toplu onay aynı `approve` metodunu her seçili kayda uygular.

Not, burs ve doğru/yanlış/boş girdileri veritabanına yazılmadan önce tam sayı olarak doğrulanır; ondalıklı PHP değeri veya `"12.0"` kabul edilmez. NULL ve gerçek sıfır ayrıdır. Katılmadı seçimi `0/0` atar. Bu seçimi geri almak otomatik sıfırları temizler; aynı istekte yeni not/burs verilebilir. Yayın için yalnız burs oranı gerekir; not ve sayaçlar NULL kalabilir. Yayındaki burs oranını temizlemek için önce sonuç yayını kapatılır. Katılmadı durumunda sayaçlar NULL / Uygulanamaz olur.

Başvuru bilgilerindeki gerçek değişiklikler başvuru iletişimini; katılım/not/doğru-yanlış-boş/burs değişiklikleri sonuç iletişimini sıfırlar. Aynı değeri tekrar kaydetmek veya yalnız yayın/iletişim anahtarını değiştirmek bu sıfırlamayı yapmaz. Oturumun bildirilen ayrıntıları, şube/grup adı veya dönem başlığı/açıklaması değişirse ilgili başvuruların başvuru iletişimi de sıfırlanır. Hiçbir komut kendiliğinden mesaj göndermez.

### `ScholarshipCatalogService`

- `savePeriod(admin, data, id = null)` / `deletePeriod(admin, id)`
- `saveSession(admin, data, id = null)` / `deleteSession(admin, id)`
- `archiveSession(admin, id, archived = true)`
- `saveDefinition(admin, type, data, id = null)` / `deleteDefinition(admin, type, id)`

Tanım türleri `branch`, `school`, `student_level`, `exam_group` değerleridir. Oturum askısı `saveSession(..., ['is_active' => false], id)` ile, dönem kapanışı `savePeriod(..., ['applications_open' => false], id)` ile uygulanır. Başvuru/sınav aralıkları ve oturumun dönem sınırları doğrulanır. Fiziksel salon, ortak toplam kota veya otomatik sınıf/grup eşlemesi yoktur.

## Zaman ve eşzamanlılık

- Tarih/saatler mevcut `config('app.timezone') = Europe/Istanbul` yaklaşımını izler. DB tarih/saat alanları bu yerel saati taşır.
- Başvuru penceresinin iki ucu dahildir; aktiflik ve manuel açma anahtarı ayrıca gereklidir. Başlamış oturuma yeni başvuru alınmaz.
- Katalog servisleri `Y-m-d H:i:s` ve `H:i:s` alır; HTML `datetime-local` ve `HH:MM` değerlerini de normalize eder.
- Yazma servisleri transaction sınırını kendileri açar ve yönetir. **Servis çağrısını dış bir `DB::transaction()` içine sarmayın.** Eski snapshot veya kısmen geri alınmış işlem riski oluşturmadan bu kullanım `LogicException` ile reddedilir.
- Dönem satırı ilk kilittir; aynı dönemde başvuru, aktarım, kapasite ve kapanış işlemleri sıraya girer. Kapasite kontrolü güncel kilitli başvuru satırlarından yapılır. Geçici bir doluluk sayacı tutulmaz.
- Ortak tanım güncellemeleri dönemleri ID sırasıyla, sonra tanımı kilitler. Aktarım hedefte yer yoksa eski kaydı ve yeri korur. Deadlock için transaction katmanında sınırlı yeniden deneme vardır.

## Üyeye veri sunumu

`forMember(user, id)` ve `listForMember(user)` yalnız ilgili hesabın kayıtlarını, izin verilen alanlardan oluşan diziler olarak döndürür. Admin modeli doğrudan üye HTML/Livewire/API verisi yapılmaz.

Sonuç yayını kapalıyken `result` içinde yalnız `published: false` bulunur; not, burs, doğru/yanlış/boş ve sonuç katılım alanları eklenmez. Onay yayınlanana kadar kabul kararı döndürülmez. Onay yayını açılınca `arrival_at` oturumdan 30 dakika öncesini gösterir. `can_edit`, `can_delete` ve `restriction`, ortak üye işlem kurallarını yansıtır. Modelin hassas alanlarının ham JSON çıktısında gizlenmesi ek korumadır.

## Geliştirme verileri ve doğrulama

Alt adımlarda temel akış ve değişen kritik iş kuralları kısa, hedefli kontrollerle doğrulanır. Ekranın açılması ve temel kaydetme işlemi HTTP/şablon düzeyinde kontrol edilir; geçici tarayıcı test ortamı veya cihaz/dil matrisi hazırlanmaz. Mevcut testler korunur; aşağıdaki komutlar her değişiklikte çalıştırılması zorunlu bir liste değildir.

**2K admin temel kontrollerinin değerlendirilmesi, 3I temel responsive/kullanılabilirlik incelemesidir.** Admin ve üye geliştirmesinin tamamı bittikten sonra **3J — Genel sistem testi** aşamasında demo veri ve geçici tarayıcı ortamı hazırlanır; ayrıntılı TR/EN, cihaz, filtre/mesaj ve uçtan uca kontroller birlikte yapılır. Yetki, veri kaybı, kapasite/duplicate ve yayın görünürlüğü gibi değişen kritik kuralların temel kontrolleri ilgili alt adımda yapılmaya devam eder. Ertelenen ek kontrol ihtiyaçları aşağıdaki genel test planına kısa not edilir.

Mevcut demo seed'i korunur; yeni demo veri ekleme, seed genişletme veya çalıştırma işi 3J'ye bırakılır. Temel otomatik kontroller yalnız ihtiyaç duydukları en az sayıda geçici kaydı izole test veritabanında oluşturabilir; bunlar uygulamaya demo veri ekleme işlemi değildir.

2C ve 2D için aşağıda kayıtlı ayrıntılı sonuçlar daha önce tamamlanan kontrollerdir; sonraki alt adımlar için zorunlu kontrol şablonu değildir.

```bash
php artisan db:seed --class=ScholarshipDemoSeeder
php tests/Scholarship/run-mariadb.php
```

Seed yalnız `local/testing` ortamında ve henüz bursluluk dönemi yoksa çalışır. Üç şube, iki örnek okul, 14 sınıf/durum, dört sınav grubu, kapalı bir örnek dönem ve dokuz oturum oluşturur. Gerçek kullanıcı/başvuru üretmez; mevcut dönemi veya yönetici değişikliklerini yeniden oluşturmaz. Ana `DatabaseSeeder` içine otomatik bağlanmaz.

Test komutu yerel MariaDB araçlarıyla `/tmp` içinde geçici, ağ bağlantısı kapalı bir sunucu kurar. Sentetik kullanıcı şeması ve bursluluk migrationlarıyla yalnız bu modülü test eder, bitince sunucuyu ve test verilerini kaldırır. Uygulamanın yapılandırılmış veritabanına test yazısı göndermez. İki bağımsız PHP süreciyle son kontenjan, çift başvuru, aktarım ve kapasite yarışı doğrulanır.

## 3J: sistem tamamlandıktan sonraki genel test planı

Bu plan şimdi uygulanmaz. Admin 2B–2I'nin etkin kapsamı ve üye 3B–3I geliştirmeleri tamamlandıktan sonra uygulanır; 2K ve 3I bu plan için ayrı tarayıcı ortamı kurmaz. Kullanıcı kararıyla bekletilen WhatsApp entegrasyonu yeniden başlatılmadıkça genel test kapsamına tamamlanmış bir özellik olarak alınmaz.

1. **Demo veri ve ortam:** Mevcut seed'i izole test ortamında yeniden kullan; farklı hesaplar/dönemler, boş/dolu/askıda/arşivli oturumlar, bekleyen/onaylı başvurular, farklı katılım/yayın/iletişim durumları ve NULL/0 sonuçlar için eksik sentetik örnekleri tamamla. Ardından geçici tarayıcı test ortamını kur ve genel test boyunca kullan. Mevcut gerçek verileri değiştirme.
2. **Otomatik kontroller:** Mevcut bursluluk testlerini topluca çalıştır. Alt adımlardan kalan kapsam eksiklerini gözden geçir; somut eksikleri tamamla. Geçen ortak iş kurallarının bütün varyasyonlarını tarayıcıda yeniden üretme.
3. **Admin–üye akışı:** Üye başvurusu → admin onayı/yayını → üyede görünürlük → katılım/not/burs → sonuç yayını → üyede sonuç akışını tamamla. Düzenleme/aktarım, kalıcı silme ve yeniden başvuru, kapasite ve dönem/askı/arşiv kilitleri, hesap sahipliği, gizli sonuçların çıktıya sızmaması, yayın sonrası güncelleme ve geçmiş dönemlerin korunmasını kontrol et. Toplu işlemlerde seçili kayıtlar ile tüm filtre sonucunun kapsamını doğrula.
4. **Admin etkileşimleri ve görünüm:** Dönem/tanım/oturum yönetimi, başvuru onayı/aktarımı/silmesi; katılım seçimi, not/burs ve doğru/yanlış/boş temizleme, radio seçimi, yayın ve iletişim anahtarları ile modal onay/iptal akışlarını tarayıcıda incele. Kaydetme sonrasında filtre/sıralama/sayfa ve satır konumunu; ortak seçim formundaki onay/yayın/bildirim düğmelerini, çok sayfalı ve büyük toplu seçimleri kontrol et. Admin ve üye ekranlarında TR/EN, masaüstü/mobil ve ayrı davranış varsa tablet; filtre/arama, hata/boş durum mesajları ve sayfalama incelensin. 2C/2D'nin geçmiş tarayıcı sonuçları son bütünleşik görünümün test edildiği anlamına gelmez.
5. **E-posta ve iletişim:** İzole ortamda gerçek alıcılara teslim etmeyen posta yakalayıcı/test taşıyıcısı ve kuyruk işçisiyle tekli/toplu gönderim, teknik durum ve manuel Ulaşıldı ayrımı, güvenli tekrar deneme, belirsiz teslim, mükerrer gönderim koruması ve ulaşıldı kayıtlarının atlanmasını incele. Kuyrukta beklerken yayın/alıcı/içerik değişimi, eski değere dönüş, başvuru silme ve sayfalı teknik kayıtlar genel akışta kontrol edilsin. 2I'nin array taşıyıcıyla geçen kontrolleri canlı SMTP teslim testi olarak raporlanmaz; gerçek kişilere mesaj gönderme. WhatsApp kapalı kalır; yeniden geliştirilirse sağlayıcıya özgü kontroller o adımda belirlenir.
6. **Kapanış:** Bulunan hataları düzelt ve yalnız etkilenen kontrolleri tekrarla. Yapılan kontrolleri, sonuçlarını ve varsa açık sorunları raporla; geçici tarayıcı/test sunucularını kapat ve bu test ortamının geçici verilerini temizle. Excel ilk sürüm kapsamı dışındadır.

## 2A: mevcut admin yapısıyla bağlantı

- `routes/web.php` içindeki mevcut `auth` ve `isAdmin_middle` grubunu kullanın. Yeni bursluluk route'ları için `admin.scholarship.*` ad alanı mevcut `admin.news.*` / `admin.campaigns.*` deseniyle uyumludur.
- `AdminSloganController` / `AdminCampaignController` ve `resources/views/admin/slogans` / `admin/campaigns` CRUD örnekleridir. Bursluluk controller'ları yukarıdaki servisleri çağırmalıdır.
- `x-app-layout`, mevcut card/table/form/button, sayfalama, validation çıktısı ve `modalSuccessTitle` / `modalSuccessContent` başarı akışı kullanılabilir.
- Silme/arşiv/toplu kritik işlemde Blade için `x-action-confirmation-modal`, Livewire için `x-review-action-modal` kullanılır.
- Genel liste/filtre işleri mevcut controller/Blade deseninden; hızlı giriş ve etkileşim gerektiğinde mevcut Livewire liste desenlerinden genişletilebilir. İlk ekran dönem yönetimidir (2B).
- Üye ekranlarının gerçek çıktıları 3.x'te doğrulanır. Sağlayıcı, gerçek gönderim ve yeniden deneme entegrasyonu 2I kapsamındadır; mevcut otomatik admin onay bildirim servisi bursluluk komutlarına bağlanmaz. Excel ilk sürüm kapsamı dışındadır.

## Sınav dönemi admin ekranı (2B)

Admin menüsünde **Bursluluk → Sınav Dönemleri**, `admin.scholarship.periods.*` route'larına bağlanır. `AdminScholarshipPeriodController` listeleme, oluşturma, düzenleme, bağımlılığı olmayan dönemi onay modalıyla silme ve dönem genelinde başvuru iznini değiştirme işlemlerini sunar. Bütün yazmalar `ScholarshipCatalogService` üzerinden yapılır.

Aktiflik ile manuel başvuru izni ayrı alanlardır. Listedeki anahtar manuel izni, yanındaki durum ise aktiflik ve tarih aralığını da dikkate alan güncel başvuru durumunu gösterir. Tarih/saat formu saniyeleri korur; yeni dönem varsayılan olarak pasif ve başvurulara kapalıdır. Oturum veya başvurusu bulunan dönemin silinmesi engellenir.

Yalnız bu ekranın HTTP bağlantılarını izole MariaDB üzerinde doğrulamak için:

```bash
php tests/Scholarship/run-mariadb.php --filter ScholarshipPeriodAdminTest
```

Bu kapsam; admin yetkisi, TR/EN görünüm ve form çıktıları, sayfalama, tarih doğrulaması, oluşturma/düzenleme/silme, manuel başvuru kapanışının ortak üye kurallarına yansıması ve ilişkili kayıtların korunmasıdır. Tanım ve oturum yönetimi 2C'dedir.

## Tanım, oturum ve kontenjan admin ekranları (2C)

- `admin.scholarship.definitions.*`: URL'deki `type` yalnız `branch`, `school`, `student_level`, `exam_group` olabilir. Her liste kendi kayıtlarını, kullanım sayısını, sıralamasını ve aktifliğini gösterir; ada ve aktiflik durumuna göre filtrelenir. Mevcut okul/sınıf seçenekleri sınav gruplarından bağımsızdır. Şube adresi başvuru detayında kullanılan şube bilgisidir.
- `admin.scholarship.sessions.*`: dönem, şube, grup, oturum durumu ve sınav adına göre filtreleme; tarih/saat sırasıyla sayfalama; oturum bazında `başvuru / kapasite` gösterimi. Üstteki toplamlar sayfalama öncesindeki bütün filtre sonucundan hesaplanır; arşivli kayıtları dahil etmek veya dışarıda bırakmak durum filtresine bağlıdır.
- Oturum formunda dönem, şube, grup, sınav adı/türü, tarih, başlangıç/bitiş saati, kapasite ve aktiflik bulunur. Kayıtlı oturumun dönemi değiştirilemez. Tarih dönemin sınav aralığında kalır, saatlerde saniyeler korunur. Mevcut doluluğun altına kapasite kaydedilemez.
- Oturumun aktiflik anahtarı yalnız o oturumun askısını yönetir. Dolu işareti doluluktan hesaplanır; ayrı bir durum kaydı veya toplam kota yoktur. Arşivleme ve arşivden çıkarma onay modalıyla yapılır; arşivden çıkarma önceki aktiflik/askı seçimini değiştirmez. Arşivde düzenleme kaydı kendiliğinden arşivden çıkarmaz.
- Referans verilen tanımlar ve başvurusu bulunan oturumlar silinemez. Boş kayıtların silinmesi onay modalından ve ortak servis kontrolünden geçer. Başvurular, öğrenci bilgileri ve sonuçlar arşivlemede korunur; başvuru yönetimi bu ekranlara dahil değildir.

`AdminScholarshipDefinitionController` ve `AdminScholarshipSessionController` bütün yazmaları `ScholarshipCatalogService` üzerinden yapar. Yeni ekran metinleri `lang/tr/scholarship.php` ve `lang/en/scholarship.php` içindedir. Mevcut dönem ekranından dönemin oturumlarına, admin menüsünden bütün tanım ve oturum listelerine erişilir.

Yalnız 2C HTTP doğrulamaları:

```bash
php tests/Scholarship/run-mariadb.php --filter 'Scholarship(Definition|Session)AdminTest'
```

2C tamamlandı. Doğrulama sonuçları:

- İzole MariaDB üzerinde 13 HTTP testi, 566 doğrulama geçti: admin erişimi, tanım/oturum işlemleri, filtreler ve sayfalama toplamları, kapasite sınırı, askı/arşiv ve ilişkili kayıtların silinme korumaları.
- Chromium'da 390, 820 ve 1440 px genişliklerde tanım/oturum liste ve formları kontrol edildi. Mobilde mevcut daraltılabilir menü ve tablo içi yatay kaydırma kullanılıyor; sayfa taşması görülmedi.
- Sentetik verilerle form kaydı, tarih sınırları, hatada alanların korunması, filtre/aktiflik işlemleri, arşivleme/geri alma ve boş kayıt silme doğrulandı. Kritik işlemlerde mevcut onay modalı; başarı ve hatalarda ortak admin toast bileşeni çalışıyor. TR/EN görünüm, yerel asset yüklemeleri ve tarayıcı JavaScript kontrolleri geçti.
- `lang/tr/validation.php` içinde sınav tarihi, başlangıç/bitiş saati, kontenjan ve sıralama alanlarının eksik Türkçe mesajları tamamlandı. On mesaj ve İngilizce çıktının korunması veritabanı bağlantısı kurmadan doğrulandı.

## Başvuru admin ekranları (2D)

Admin menüsündeki **Bursluluk → Bursluluk Başvuruları**, `admin.scholarship.applications.*` route'larını kullanır. `AdminScholarshipApplicationController` liste, detay, düzenleme, tekli/toplu onay ve kalıcı silme işlemlerini sunar. Bütün yazmalar mevcut `ScholarshipApplicationService` üzerinden yapılır; yeni tablo veya migration eklenmedi.

- Listede dönem, şube, grup/oturum, tarih/saat, okul/sınıf, onay, katılım, iki ayrı iletişim ve yayın durumu, burs oranı ve oturum durumu filtrelenebilir. Arama başvuru numarası, döneme ait öğrenci adı ve hesaptaki güncel ad, telefon/e-postayı kapsar. Boş burs ile `%0` ayrıdır. Filtreler sayfalamada korunur.
- Detayda döneme ait öğrenci/okul/sınıf bilgileri ve güncel hesap iletişimi birlikte gösterilir. Hesabı silinmiş başvurular dönemlik bilgileriyle görüntülenmeye devam eder. Katılım, not, burs, yayın ve iletişim bilgileri bu adımda salt okunurdur; yönetimleri sonraki adımlardadır.
- Düzenlemede yalnız öğrenci adı, okul, sınıf/durum ve aynı dönemin oturumu değiştirilebilir. Hesap, dönem, başvuru numarası, onay ve sonuç alanları istekten değiştirilmez. Dolu hedef oturum seçilemez; kapasite kontrolü serviste de uygulanır. Admin onaylı, kapalı dönemli, askıda/arşivli başvurularda ortak kurallarla işlem yapabilir. Onay/yayın korunur; gerçek başvuru değişikliği yalnız başvuru iletişimini sıfırlar.
- Tekli onay ve kalıcı silme mevcut ALA onay modalından geçer. Onay yayın açmaz veya mesaj göndermez. Silme kontenjanı ve hesap/dönem başvuru hakkını serbest bırakır; kullanıcı ve ortak tanımlar korunur.
- Toplu onayda bu sayfadan seçilen başvurular veya filtreye uyan bütün sayfalardaki bekleyen kayıtlar ön izlenir. Kayıt sayısı ve ilk 20 kayıt gösterilir. ID listesi, yönetici ve tek kullanımlık onay anahtarı yalnız oturumda, 30 dakika geçerli geçici onay bilgisi olarak tutulur; işlem tarihçesi değildir. Yeni eşleşen kayıtlar onaya dahil edilmez. Sonradan silinen kayıtlar atlanır ve işlem özeti gösterilir; süresi dolan, başka yöneticiye ait veya eski ön izleme yeniden kullanılamaz.

```bash
php tests/Scholarship/run-mariadb.php --filter ScholarshipApplicationAdminTest
```

2D doğrulaması: izole MariaDB üzerinde **9 test, 218 doğrulama** geçti. Kapsam; admin erişimi, TR/EN ve kaçışlı HTML, silinmiş hesap, filtre/arama, NULL–0 ayrımı, onay ve otomatik gönderim olmaması, kontenjan/dönem sınırı, onaylı başvuru aktarımı, sabit toplu seçim, ön izleme sahipliği/süresi ve kalıcı silmeden sonra yeniden başvurudur.

Chromium'da liste, detay ve düzenleme 390/820/1440 px genişliklerde; filtre açma, sayfa seçimi, seçili/tüm filtre sonucunun ön izlemesi, modal iptali/onayı, tekli/toplu onay, aktarım, silme, boş sonuç ve toast bildirimleri kontrol edildi. TR/EN ekranlarında tarayıcı JavaScript hatası veya başarısız yerel asset isteği görülmedi. Bu kontroller sentetik ve izole test verileriyle yapıldı.

## Katılım yönetimi (2E)

Admin menüsündeki **Bursluluk → Katılım Yönetimi**, `admin.scholarship.attendance.index` ve `admin.scholarship.attendance.update` route'larını kullanır. `AdminScholarshipAttendanceController` ve `resources/views/admin/scholarship/attendance/index.blade.php`, öğrenci satırında İşaretlenmedi / Katıldı / Katılmadı seçimi ve kaydetme sunar.

- Dönem, şube, sınav grubu, mevcut sınıf/durum, tarih, başlangıç/bitiş saati ve katılım durumu ayrı filtrelenir. Başvuru numarası, öğrenci/hesap adı, güncel telefon ve e-posta aranabilir. Liste öğrenci adına göre sıralanır; kaydetmede filtre ve sayfa korunur.
- Yalnız katılım alanı `updateResult` servisine iletilir. Hesap, oturum, onay, not/burs ve yayın alanları istekten değiştirilemez. Katılmadı seçiminin ortak kural gereği atadığı 0/0 bu sınırdan bağımsızdır.
- Katılmadı seçimine geçiş mevcut ALA onay modalından geçer. Servis notu/bursu 0 yapar, doğru/yanlış/boş sayaçlarını NULL yapar ve yalnız sonuç iletişimini sıfırlar. Bu seçim geri alındığında otomatik sıfırlar temizlenir; yayında eksik sonuç oluşturacak değişiklik engellenir ve önce sonuç yayınının kapatılması gerektiği gösterilir. İşaretlenmedi/Katıldı durumuna kendiliğinden 0/0 atanmaz. Aynı katılımı tekrar kaydetmek iletişimi sıfırlamaz; otomatik mesaj gönderilmez.
- Yeni tablo, migration veya demo veri eklenmedi. TR/EN ekran metinleri eklendi; ortak eksik sonuç/yayın hatası çeviri dosyasına taşındı. Not/burs ve yayın yönetim ekranları bu adıma dahil değildir.

Kısa doğrulama: `php tests/Scholarship/run-mariadb.php --filter ScholarshipAttendanceAdminTest` ile izole MariaDB üzerinde **4 test, 65 doğrulama** geçti. Temel sayfa/filtre/kaydetme bağlantısı, admin erişimi, izin verilen alanlar, katılmama ve yayın engeli, ilgili iletişim sıfırlaması kontrol edildi. Sözdizimi, hedefli biçim ve diff kontrolleri geçti.

**3J'ye bırakılanlar:** Tarayıcıda katılım seçimi ve modal onay/iptal akışı, kaydetme sonrası filtre/sayfa konumu, ayrıntılı filtre/arama varyasyonları, TR/EN ve cihaz görünümleri. Bu adımda tarayıcı ortamı kurulmadı ve demo seed çalıştırılmadı.

## Not yönetimi (2F)

Admin menüsündeki **Bursluluk → Not Yönetimi**, `admin.scholarship.scores.index` ve `admin.scholarship.scores.update` route'larını kullanır. `AdminScholarshipScoreController` ve `resources/views/admin/scholarship/scores/` altındaki görünümler, öğrenci satırında hızlı not ve doğru/yanlış/boş girişi ile kaydetme sunar.

- Not 0–100 arasında tam sayı olmalıdır. Boş kaydetme NULL / Girilmedi durumudur; 0 gerçek nottur. İstekte not alanının hiç bulunmaması kaydı temizlemez, doğrulama hatası verir. Mevcut not veya sayaçların temizlenmesi ALA onay modalından geçer.
- Varsayılan sıralama yüksek nottan düşüğedir; düşükten yükseğe veya öğrenci adına göre sıralama seçilebilir. Nota göre iki yönde de notu girilmemiş kayıtlar sonda kalır. Not girildi/girilmedi ve alt/üst not sınırı filtreleri 0 ile NULL'ı ayırır.
- Dönem, şube, grup, oturum, okul, mevcut sınıf/durum, tarih/saat ve katılım filtreleri; başvuru numarası, öğrenci/hesap adı ve güncel iletişim bilgileriyle arama bulunur. Kaydetmede filtre, sıralama ve sayfa korunur.
- Yalnız `score`, `correct_count`, `wrong_count` ve `blank_count` alanları ortak `updateResult` servisine iletilir. Hesap, oturum, katılım, onay, burs ve yayın alanları istekten değiştirilemez. Katılmadı durumunda 0 gösterilir ve katılım yönetimine bağlantı sunulur; doğrudan istekte de sıfır dışı not ortak servis tarafından reddedilir.
- Yayındaki geçerli not değişikliği ortak üye sunumuna hemen yansır ve yalnız sonuç iletişimi Ulaşılmadı olur. Aynı notu kaydetmek iletişimi sıfırlamaz. Son karara göre yayındaki not ve doğru/yanlış/boş sayıları temizlenebilir; yayın için yalnız burs oranı gereklidir. Not girmek yayın veya otomatik bildirim başlatmaz.

Kısa doğrulama: `php tests/Scholarship/run-mariadb.php --filter ScholarshipScoreAdminTest` ile izole MariaDB üzerinde **5 test, 68 doğrulama** geçti. Sayfa/kaydetme, not sıralaması ve NULL–0 ayrımı, admin erişimi, tam sayı/aralık kontrolü, değiştirilebilir alan sınırı, katılmama ve yayın engeli ile ilgili iletişim sıfırlaması kontrol edildi. Hedefli biçim, sözdizimi ve diff kontrolleri geçti.

**3J'ye bırakılanlar:** Tarayıcıda not girişi, not temizleme modalının onay/iptali, sıralama ve sayfa değişiminde satır konumu, ayrıntılı filtre/arama varyasyonları, TR/EN ve cihaz görünümleri. Tarayıcı ortamı kurulmadı, demo veri/seed veya migration eklenmedi. Burs oranı ve yayın yönetimi ekranları bu adımın dışındadır; Excel ilk sürüm kapsamında değildir.

## Burs oranı yönetimi (2G)

Admin menüsündeki **Bursluluk → Burs Oranı Yönetimi**, `admin.scholarship.awards.index` ve `admin.scholarship.awards.update` route'larını kullanır. `AdminScholarshipAwardController` ve `resources/views/admin/scholarship/awards/index.blade.php`, nota göre sıralı listede öğrenci satırından burs seçimi ve kaydetme sunar.

- Her satırın radio grubunda %100–%0 arasında onar puanlık tek oran seçilir. %0 / Burs Yok ile NULL / Belirlenmedi ayrı seçeneklerdir. Mevcut oranı Belirlenmedi yapmak ALA onay modalından geçer; alanın istekte bulunmaması kaydı temizlemez, doğrulama hatası verir.
- Not ekranının filtre görünümü yeniden kullanılır; burs ekranı kendi filtre adresini ve ek burs oranı/Belirlenmedi filtresini verir. Dönem, şube, grup, oturum, okul, mevcut sınıf/durum, tarih/saat, katılım, not aralığı ve arama desteklenir. Varsayılan sıralama not azalandır; not artan veya öğrenci adı seçilebilir. Notu girilmemiş kayıtlar nota göre sıralamada sonda kalır. Kaydetmede filtre, sıralama ve sayfa korunur.
- Yalnız `scholarship_percentage` ortak `updateResult` servisine iletilir. Not, katılım, hesap, dönem, oturum, onay ve yayın alanları istekten değiştirilemez. Aynı öğrencinin başka dönem başvurularının bursları etkilenmez. Katılmadı durumunda burs %0 kalır ve katılım yönetimine bağlantı gösterilir; sıfır dışı oran doğrudan istekte de engellenir.
- Yayındaki geçerli burs değişikliği ortak üye sunumuna hemen yansır; yalnız sonuç iletişimi Ulaşılmadı olur. Aynı oranı tekrar kaydetmek bu alanı sıfırlamaz. Yayındaki bursun Belirlenmedi yapılması engellenir; önce sonuç yayını kapatılmalıdır. Burs kaydı yayın veya otomatik bildirim başlatmaz.

Kısa doğrulama: `php tests/Scholarship/run-mariadb.php --filter 'ScholarshipAwardAdminTest|ScholarshipScoreAdminTest::test_score_list_sorts_and_filters_zero_separately_from_missing_scores'` ile izole MariaDB üzerinde **6 test, 87 doğrulama** geçti. Beş yeni kontrol burs ekranı/tek seçim/kaydetme, NULL–0 ve dönem ayrımı, admin erişimi, geçerli oranlar, katılmama/yayın engeli ve iletişim davranışını kapsar; bir mevcut not listesi kontrolü ortak filtre kullanımını doğrular. Sözdizimi, hedefli biçim ve diff kontrolleri geçti.

**3J'ye bırakılanlar:** Tarayıcıda radio seçimi ve oran temizleme modalının onay/iptali, kaydetmede filtre/sıralama/sayfa konumu, ayrıntılı filtre/arama varyasyonları, TR/EN ve cihaz görünümleri. Tarayıcı ortamı kurulmadı; demo veri/seed, migration veya Excel işlemi eklenmedi.

## Yayınlama yönetimi (2H)

**Bursluluk → Bursluluk Başvuruları** listesi ve başvuru detayında iki ayrı yayın anahtarı bulunur. `AdminScholarshipApplicationController` içindeki `updatePublication`, `previewPublication` ve `publishBulk` işlemleri, `admin.scholarship.applications.publication.*` route'ları üzerinden ortak `publish` servisini kullanır. Görünümler `resources/views/admin/scholarship/applications/` altındadır.

- Başvuru yayını, onay durumunu değiştirmez. Kabul kararı ancak onaylı ve başvuru yayını açık kayıtta ortak üye sunumuna açılır; 30 dakika erken gelme bilgisi korunur. Sınav/burs yayını ayrı anahtardır. Tekli açma/kapatma diğer yayını, onayı, not/bursu veya iletişim durumlarını değiştirmez ve bildirim göndermez.
- Bursu NULL olan kaydın sonuç anahtarı kapalı ve devre dışı gösterilir. Doğrudan istekte de ortak servis yayınlamayı engeller; %0 burs geçerlidir. Not ve doğru/yanlış/boş sayıları yayın için zorunlu değildir. Yayını kapatmak saklanan sonucu silmez, üye sunumundan gizler.
- Listedeki seçim kutuları artık onaylı başvuruları da kapsar. Toplu onay ve yayın aynı seçili kayıtlar / tüm filtre sonucu kapsamını kullanır; onay işlemi yine yalnız bekleyenleri işler. Yayın ön izlemesinde aşama, açma/kapatma işlemi, toplam kayıt ve o anda uygun kayıt sayısı açıkça gösterilir. Son uygulama ALA onay modalından geçer.
- Ön izlemenin kayıt ID'leri, aşaması, yayın değeri ve filtreleri; yöneticiye bağlı, 30 dakika geçerli, tek kullanımlık geçici oturum bilgisi olarak saklanır. Sonradan eklenen kayıtlar dahil edilmez; onay isteğinden gönderilen farklı ID/aşama/yayın değerleri kullanılmaz. Bu bilgi bir değişiklik tarihçesi değildir.
- Onay anında her kayıt yeniden doğrulanır. Eksik sonuçlar veya silinen kayıtlar atlanır; diğerleri işlenir. Büyük seçimler ortak servisin 1000 kayıt sınırına uygun parçalara ayrılır. Sonuç özeti uygulanan/atlanmış sayısını gösterir; ilk 20 atlanan kaydın numarası ve nedeni listede bildirilir. Silinmiş kayıt için ID gösterilir. Zaten istenen yayın durumunda olan kayıt da ayarın uygulandığı sayıya dahildir.

Kısa doğrulama: `ScholarshipPublicationAdminTest` ile mevcut başvuru ekranı ve toplu onayın iki ilgili testi birlikte, izole MariaDB üzerinde **7 test, 172 doğrulama** ile geçti. Kapsam; admin erişimi, bağımsız tekli yayınlar ve üye sunumu, NULL–0 yayın engeli, toplu işlemde güncel sonuç kontrolü, sabit seçim/sonradan eklenen kayıt, silinen kayıt bildirimi, onay sahibinin/sürenin/tokenın kontrolü ve otomatik gönderim olmamasıdır. Hedefli biçim, sözdizimi ve diff kontrolleri geçti.

**3J'ye bırakılanlar:** Tarayıcıda yayın anahtarları, ortak seçim ve iki farklı toplu işlem düğmesi, yayın ön izleme/modal onay-iptal akışı, çok sayfalı ve büyük toplu seçimler, ayrıntılı filtre/mesaj varyasyonları, TR/EN ve cihaz görünümleri. Gerçek üye ekranlarının HTML/Livewire/API çıktıları üye geliştirmesi sonrası doğrulanacaktır. Bu adımda tarayıcı ortamı veya demo veri hazırlanmadı; migration eklenmedi.

## İletişim ve bildirim yönetimi (2I — e-posta/manuel iletişim tamamlandı; WhatsApp beklemede)

Başvuru listesi ve detayında başvuru/sonuç için ayrı **Ulaşıldı** anahtarları bulunur. `contact.update`, mevcut `markContact` servisini kullanır; yalnız istenen aşama değişir ve mesaj gönderilmez. Bilgi değişikliklerinde ilgili iletişimin sıfırlanması mevcut ortak servislerde korunur.

Başvuru detayından tek kayıt; listedeki ortak seçimden seçili başvurular veya filtreye uyan bütün kayıtlar için e-posta ön izlenebilir. `notification.preview` ve `notification.send`, mevcut ALA onay modalıyla gönderimi başlatır. Ön izleme ilk 20 kaydın alıcısını ve içeriğini veya engel nedenini gösterir. Kapsam/aşama/kanal, yöneticiye bağlı 30 dakikalık tek kullanımlık oturum kaydında sabittir; sonradan eklenen başvurular ve onay isteğindeki değiştirilmiş alanlar kapsamı genişletmez. Onayda güncel veriler kullanılır; atlanan ilk 20 kaydın nedeni ve toplam işlem sayıları gösterilir.

`ScholarshipNotificationService::enqueue` ve `SendScholarshipNotification`, mevcut posta ve kuyruk altyapısını kullanır. Yerel yapılandırma SMTP ve veritabanı kuyruğudur; gerçek gönderim için mevcut kuyruk işleyicisinin çalışması gerekir. Bu geliştirmede `.env` veya genel mail/queue yapılandırması değiştirilmedi, gerçek alıcıya mesaj gönderilmedi. Log veya log'a düşebilen failover posta ayarı gerçek gönderim sayılmaz; array yalnız izole test ortamında kabul edilir. Hesapta kalıcı dil tercihi olmadığından bildirim metni Türkçedir; admin ekranları mevcut TR/EN çevirilerini kullanır.

- Başvuru e-postası için onay ve başvuru yayını; sonuç e-postası için burs oranı girilmiş ve yayınlanmış sonuç gerekir. Ulaşıldı kayıtları gönderilmez. Silinmiş hesap/geçersiz e-posta atlanır. Alıcı User üzerindeki güncel e-postadır. Başvuru mesajı oturum/şube/grup ve en az 30 dakika önce gelme bilgisini, sonuç mesajı katılım/not/burs ve doğru/yanlış/boş sayılarını içerir; girilmeyen alanlar sıfır yapılmaz.
- Kuyruk işçisi gönderimi üstlenmeden hemen önce yayın, iletişim, alıcı ve içeriği yeniden kontrol eder. Değişen kayıt gönderilmez; yönetici güncel bilgiyi ön izleyerek tekrar başlatabilir. Gönderim üstlenildikten sonra yapılan değişiklik, dış posta sistemine aktarılmaya başlayan mesajı geri çağıramaz.
- Aynı başvuru/aşama/kanalın son teknik kaydındaki alıcı/içerik değişmediyse tekrarlanan düğme ve paralel istek mevcut kayıt/kimliği kullanır. İçerik değişirse yeni kimlik açılır, eski bekleyen ve henüz üstlenilmemiş mesajlar geçersiz kılınır. A → B → A değişimi son B'den farklı yeni bir bildirimdir; tarihsel A kaydı bunu engellemez. Dönem → başvuru → bildirim kilidi korunur. Gönderim hakkı dış işlemden önce kalıcı alınır; yinelenen kuyruk işi aynı mesajı göndermez. Harici gönderim, tekrar çalıştırılabilen veritabanı transaction'ının dışında yapılır.
- Sağlayıcı çağrısı başlamadan başarısız olan kayıt aynı kimlikle yeniden kuyruğa alınabilir. SMTP kabulünden sonra bağlantı veya işçi kesilmesinde teslim kesin olarak bilinemeyebilir: otomatik yeniden deneme yapılmaz, aynı mesajın tekrar başlatılması engellenir. Yönetici alıcı/posta sağlayıcısından kontrol edip telefonla veya sistem dışında iletişimi tamamlayabilir. Bu tasarım tam bir “kesin teslim” garantisi vermez. Ham sağlayıcı istisnası/secret hata kaydına yazılmaz.
- Detaydaki sayfalı teknik kayıtlar aşama, kanal, alıcı, bekliyor/gönderildi/gönderilemedi-doğrulanamadı ve zaman bilgisini gösterir. Gönderildi yalnız posta hizmetinin kabulüdür; Ulaşıldı otomatik değişmez. Önceki başarılı kayıtlar veri/iletişim değişiminde korunur. İletişim geçmişi/audit tablosu veya yeni migration eklenmedi.

**Kullanıcı kararıyla bekletilen:** Kullanıcı WhatsApp bağlantısını şimdilik erteledi. Sağlayıcı seçilmedi; Meta Cloud API önerisi kabul edilmiş sayılmaz. WhatsApp seçeneği kapalı, doğrudan istek de gönderim yapmaz. Paylaşılan normal WhatsApp numarası yapılandırmaya eklenmedi; hesap veya numara üzerinde işlem yapılmadı. Sağlayıcı bağlantısı, gerekiyorsa onaylı mesaj şablonları ve kanalın hedefli kontrolleri yeniden istendiğinde ele alınacak. E-posta ve manuel iletişim işleri tamamlandı; WhatsApp tamamlanmış olarak raporlanmaz ve diğer geliştirme adımlarını engellemez.

Kısa doğrulama: `php tests/Scholarship/run-mariadb.php --filter 'ScholarshipNotificationAdminTest|ScholarshipConcurrencyTest::test_notification_requests_and_redelivered_jobs_only_send_one_message'` ile izole MariaDB üzerinde **7 test, 121 doğrulama** geçti. Yetki, ayrı iletişim yazmaları, sabit tekli/toplu seçim ve token/sahip/süre, yayın engelleri, güncel alıcı ve içerik, geçmiş değere dönüş, gönderim/ulaşılma ayrımı, güvenli tekrar deneme ve eşzamanlı istek/işçi çalışması kapsandı. Ayrıca mevcut bağımsız yayın anahtarları ve otomatik mesaj gönderilmemesi testi bu değişiklik sırasında geçti. Gerçek posta yerine bellekteki array taşıyıcı kullanıldı.

**3J'ye bırakılanlar:** Tarayıcıda iletişim anahtarları, tekli/toplu ön izleme ve modal onay/iptal, sayfalı gönderim kayıtları, ayrıntılı filtre/mesaj/TR-EN/cihaz kombinasyonları ve bütün admin–üye akışı. Bu adımda tarayıcı ortamı veya demo seed hazırlanmadı.

## Admin temel kontrol değerlendirmesi (2K)

**Tamamlandı.** 2B–2I'nin etkin kapsamındaki mevcut testler, yukarıda kayıtlı önceki sonuçlar, admin route/menu bağlantıları ve controller'ların ortak servisleri kullanması gözden geçirildi. Bu incelemede yeni bir kritik kontrol eksikliği veya açık hata saptanmadı. Bu sonuç genel sistem testi veya canlı teslim doğrulaması değildir.

| Adım | Gözden geçirilen temel güvence | Mevcut kontrol |
| --- | --- | --- |
| 2B | Admin yetkisi, dönem kaydı, tarih sınırları, manuel kapanış, bağımlı kayıtların korunması | `ScholarshipPeriodAdminTest` |
| 2C | Ayrı tanımlar, oturum kaydı, kapasite sınırı, askı/arşiv, başvurulu oturumun silinememesi | `ScholarshipDefinitionAdminTest`, `ScholarshipSessionAdminTest` |
| 2D | Yetkili onay/aktarım/silme, kontenjan ve dönem sınırı, onaylı başvurunun korunması, sabit toplu seçim | `ScholarshipApplicationAdminTest` |
| 2E–2G | Katılmadı için 0/0, tam sayı not, burs seçenekleri, NULL–0 ayrımı, yalnız ilgili alan ve iletişimin değişmesi | `ScholarshipAttendanceAdminTest`, `ScholarshipScoreAdminTest`, `ScholarshipAwardAdminTest` |
| 2H | Bağımsız yayınlar, eksik sonucun tekli/toplu engellenmesi, üye sunumundan gizleme, onay/token/süre kontrolü | `ScholarshipPublicationAdminTest` |
| 2I | Ayrı manuel iletişim, yayın/alıcı kontrolü, tekli/toplu seçim, tekrar gönderim ve eşzamanlı işçi koruması | `ScholarshipNotificationAdminTest`, `ScholarshipConcurrencyTest` içindeki bildirim yarışı |

Önceden geçen testler yeniden çalıştırılmadı ve yeni test eklenmedi. Bu adım yalnız `AGENTS.md` ve bu belgeyi güncelledi; içerik/tutarlılık ve diff kontrolü yapıldı. Tarayıcı ortamı, demo veri, migration veya gerçek mesaj gönderimi yapılmadı. Önceki adımlardan kalan ayrıntılı etkileşim/görünüm ve bildirim kontrol ihtiyaçları yukarıdaki **3J genel test planında** bir araya getirildi. WhatsApp kullanıcı kararıyla beklemede; Excel ilk sürüm dışında.

2K değerlendirmesi sırasında 3A başlatılmadı ve üye ekranı geliştirilmedi. Sonraki üye adımlarının durumu aşağıdadır.

## Mevcut üye alanı analizi (3A)

Mevcut `x-frontend-profile-layout`, masaüstü/mobil frontend menüleri ve Blade card/liste desenleri yeniden kullanılır. Erişim mevcut `auth` middleware ve Jetstream/Fortify hesabıyla sağlanır; giriş yönlendirmeleri değişmez. E-posta ve telefon güncellemesi `profile.show` üzerinden sürer. Öğrenci başvurusu için ikinci üyelik, ayrı iletişim kaydı veya çok öğrencili hesap kurulmaz.

Sınav listesi `/bursluluk-sinavlari` altında, ilerideki **Başvurularım** alanı `/basvurularim` altında planlandı. Liste, form ve başvuru yönetimi kendi numaralı adımlarında geliştirilir. Başvuruların üye çıktısı ortak servisin sahiplik ve yayın kurallarını kullanmalıdır; ham admin modeli üyeye aktarılmaz. 3A yalnız analizdi; bu kararlar 3B sonunda dokümana işlendi.

## Üye sınav listesi (3B)

`frontend.scholarship.exams.index` (`GET /bursluluk-sinavlari`), `Frontend\ScholarshipExamController` ve `resources/views/frontend/scholarship/exams/index.blade.php` üzerinden salt okunur liste sunar. Mevcut üye layout'u ve TR/EN çevirileri kullanılır; giriş yapmış kullanıcıların masaüstü/mobil menüsüne **Bursluluk Sınavları** bağlantısı eklendi.

- Aktif dönemlerin henüz başlamamış, arşivlenmemiş oturumları; şube ve sınav grubu da aktifse listelenir. Başvuru tarihleri ile dönem anahtarı ayrı durumlarla açıklanır; tarih uygunluğu mevcut `acceptsApplications` metodundan alınır. Zaman hesabında uygulamanın İstanbul zaman dilimi kullanılır.
- Her oturumda şube, sınav grubu, sınav adı, tarih/saat, kapasite ve kalan kontenjan gösterilir. Bekleyen ve onaylı başvurular birlikte sayılır; ortak şube/grup kotası kullanılmaz. Dolu ve askıdaki oturumlar listede kalır. Askıda, mevcut iletişim sayfasına yönlendirme bulunur. Geçmiş/başlamış ve arşivli oturumlar yeni başvuru listesinden çıkarılır; mevcut başvuruların tarihçesi bu ekranın kapsamı değildir.
- Aynı hesabın o dönemde mevcut başvurusu varsa genel bir bilgi gösterilir ve oturum başvuruya açık olarak sunulmaz. Sorgu yalnız giriş yapan hesabın başvuru varlığını denetler; başka öğrencilerin bilgileri veya herhangi bir başvurunun onay/not/burs/iletişim alanları çıktıya eklenmez. Görünüm yalnız izin verilen dönem/oturum alanlarını alır; açıklamalar HTML olarak çalıştırılmaz.
- Bu adım form, başvuru başlatma düğmesi veya yazma endpoint'i eklemez. Gösterilen kapasite anlık bilgidir; sonraki yazma adımı mevcut ortak servisle uygunluğu ve kapasiteyi işlem anında yeniden doğrulamalıdır.

Kısa doğrulama: `php tests/Scholarship/run-mariadb.php --filter ScholarshipExamCatalogTest` ile izole MariaDB üzerinde **3 test, 55 doğrulama** geçti. Gerçek Blade/layout çıktısı, giriş zorunluluğu, kendi başvurusunun varlığı ve diğer hesapların bilgilerinin gizliliği, dönem kapanışı/tarihleri, oturum doluluğu/askısı, geçmiş/arşivli/pasif kayıtların listeden çıkarılması kontrol edildi. Hedefli biçim, PHP sözdizimi ve diff kontrolleri geçti. Gerçek veriler değişmedi; demo veri veya tarayıcı ortamı hazırlanmadı.

**3J'ye bırakılanlar:** Üye menüsünün ve oturum kartlarının tarayıcıda TR/EN, masaüstü/mobil görünümü; uzun metin, çok dönemli sayfalama ve sonraki başvuru formuna geçişin bütünleşik kontrolü. Genel test planının üye görünüm/akış kapsamına dahildir.

3B sonunda sıradaki adım 3C olarak belirlendi; tamamlanan formun kapsamı aşağıdadır.


## Üye başvuru formu (3C)

`frontend.scholarship.applications.create` (`GET /bursluluk-sinavlari/{period}/basvuru`), mevcut `ScholarshipExamController::createApplication` üzerinden `frontend/scholarship/applications/create.blade.php` görünümünü açar. Sınav listesindeki uygun oturum kartı formu ilgili seçimle açar; başlık menüsünün aktif durumu formda da korunur. Liste ve form, aynı oturum sorgusunu ve izin verilen alan sunumunu kullanır.

- Öğrenci adı, e-posta ve telefon yalnız giriş yapan hesaptan gelir; başka hesap veya ayrı başvuru iletişimi seçilmez. İletişim alanları salt okunurdur; eksik telefon açıklanır ve mevcut profil sayfası yeni sekmede açılabilir. Okul ve mevcut sınıf/durum, adminin aktif tanımlarından ayrı ayrı seçilir. Sınıf ile sınav grubu arasında uygunluk eşlemesi yoktur.
- Mevcut ALA form stilleriyle şube, sınav grubu, sınav ve tarih/saatli oturum seçilir. `public/frontend/js/scholarship-application-form.js`, bir üst seçim değiştiğinde alt seçimleri ve gösterilen tarih/saat/kontenjanı sıfırlar. Dolu ve askıdaki oturum seçenekleri devre dışıdır; askıda iletişim bağlantısı gösterilir. Görünen kapasite bilgisi yer ayırmaz.
- Form açılışında dönemin güncel başvuru uygunluğu ve hesabın mevcut başvurusu kontrol edilir. Uygun oturum kalmadıysa açıklamayla listeye dönülür. URL'deki dolu/askıda/arşivli/geçmiş veya bu döneme ait olmayan oturum otomatik seçilmez; başka uygun seçim istenir. Pasif okul/sınıf seçenekleri, pasif şube/gruplar ve arşivli/geçmiş oturumlar form verisine alınmaz.
- Bu adım yalnız form hazırlığıdır: gönder düğmesi devre dışı, form gönderimi engelli ve POST route'u yoktur. Kullanıcıya gönderimin henüz açılmadığı açıklanır. **3D** sırasında bu geçici gönderim engeli kaldırılmalı, mevcut ortak servisle kayıt oluşturma bağlanmalı; hesap, dönem ve oturum uygunluğu işlem anında tekrar doğrulanmalıdır. Şube/grup/sınav alanları filtre içindir; sunucuda yetkili oturum ilişkileri esas alınmalıdır.

Kısa doğrulama: izole MariaDB üzerinde `ScholarshipExamCatalogTest` **5 test, 104 doğrulama** ile geçti; 3B'nin üç testi, ortak sorgu ve liste bağlantısı değiştiği için bu çalışmaya dahildir. İki yeni test formun gerçek HTTP/Blade çıktısını, hesap bilgilerini, ayrı seçim alanlarını, aktif tanımları, uygun oturum ön seçimini ve dolu/askıda/arşivli oturum engellerini, kapalı/başvurulu dönemde form açılmamasını ve kayıt oluşturulmamasını kapsar. `node --test tests/Scholarship/member-form.test.mjs` ile **1 kısa JavaScript testi** geçti; şube değişiminde alt seçimlerin sıfırlanması ve dolu/askıda oturumların yeniden etkinleşmemesi doğrulandı. Hedefli biçim, PHP/JavaScript sözdizimi ve diff kontrolleri geçti.

**3J'ye bırakılanlar:** Formun gerçek tarayıcıda klavye ve seçim etkileşimleri, profil sekmesinden dönüş, uzun seçenek adları, TR/EN ve cihaz görünümleri; 3D sonrası gönderim ve doğrulama hatasından dönüşün bütünleşik kontrolü. Tarayıcı ortamı, demo veri, migration veya gerçek bildirim eklenmedi.

Sıradaki adım **3D — Başvuru iş kurallarını üye akışına bağla**; henüz başlatılmadı.


## 3D öncesi ek geliştirme: doğru/yanlış/boş ve yayın koşulu

Kullanıcı kararıyla `scholarship_applications` tablosuna `correct_count`, `wrong_count`, `blank_count` eklendi. Alanlar nullable `UNSIGNED SMALLINT` (0–65535); başlangıç NULL, gerçek sıfır 0'dır. Birbirinden bağımsız boş bırakılabilir. Toplam soru sayısı tanımı, otomatik not/net hesaplaması veya yeni sonuç tablosu eklenmedi. Yönetici mevcut Not Yönetimi ekranında notla birlikte bu değerleri manuel kaydeder; başvuru detayında da gösterilir.

**Yeni yayın kuralı:** Yalnız `scholarship_percentage IS NOT NULL` gerekir; %0 geçerlidir. Not ve sayaçlar olmadan tekli/toplu yayın, üye sonucu sunumu ve sonuç e-postası çalışır. Yayında not/sayaç temizleme mümkündür; burs oranı temizlenemez. Eksik değerler sıfır yerine Girilmedi gösterilir. Yeni sayaçlar yayın öncesinde üye çıktısına alınmaz ve ham model JSON'unda gizlidir. Not/sayaç değişikliği yalnız sonuç iletişimini sıfırlar, yayını kapatmaz ve mesaj göndermez. E-postada yeni alanların bulunması, mevcut içerik karşılaştırmasının sayaç değişikliklerini de yakalamasını sağlar.

Katılmadı seçimi not/bursu 0 yapar ve sayaçları NULL / Uygulanamaz olarak temizler. Yönetici onay modalı bunu açıklar. Katılmadı kaydına sayaç girilemez; hem servis hem veritabanı bu tutarlılığı korur. Katılım düzeltmesinde mevcut otomatik 0/0 temizleme kuralı sürer.

`2026_09_20_100000_add_answer_counts_to_scholarship_applications.php` yeni migration'ı eski migrationları değiştirmeden alanları ve kısıtları günceller. MySQL için `DROP CHECK`, MariaDB için `DROP CONSTRAINT` kullanır. Tek ALTER ile `sch_app_result_complete_ck` yerine burs koşulunu tutan `sch_app_result_award_ck`, ayrıca katılmayanların sayaçları için `sch_app_absent_counts_ck` kurulur. Geri almada notsuz yayınlanmış sonuç varsa önce yayın kapatılmalıdır; migration kendiliğinden not atamaz veya yayını değiştirmez. Migration yerel MariaDB'ye uygulandı; canlı veritabanında işlem yapılmadı.

Doğrulama, izole MariaDB üzerinde **12 hedefli test** kapsamında tamamlandı: yeni sayaç/şema testleri; mevcut not ekranı testleri; tekli/toplu yayın, sonuç bildirimi, katılım ve yayın görünürlüğünün ilgili testleri. İlk çalışmadaki testler arası posta taklidi taşınması yeni testin teardown'unda düzeltildi; etkilenen üç test yeniden çalıştırılıp geçti. Sayaç tipi/NULL/0, yalnız yönetici yazması, notsuz %0 burs yayını, burs NULL engeli, yayınlı not temizleme, gizlilik, sonuç iletişiminin sıfırlanması, katılmadı tutarlılığı ve migration geri alma/yeniden uygulama doğrulandı. PHP sözdizimi, hedefli Pint ve diff kontrolleri geçti. Gerçek alıcılara mesaj gönderilmedi.

**3J'ye bırakılanlar:** Dört giriş alanının mobil/TR–EN yerleşimi, birden fazla alanı temizlerken ALA modalının onay/iptali ve burs girilmiş fakat not/sayaç girilmemiş sonucun tamamlanacak üye ekranlarındaki görünümü. Tarayıcı ortamı ve demo veri hazırlanmadı.

Bu ek geliştirme 3D'yi başlatmaz. Sıradaki adım **3D — Başvuru iş kurallarını üye akışına bağla** olarak kalır.
