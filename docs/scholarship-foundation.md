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
| `updateResult(admin, id, data)` | `attendance_status`, `score`, `scholarship_percentage` için ortak doğrulama |
| `publish(admin, ids, phase, published = true)` | `application` veya `result` aşamasında kayıt bazlı yayın; tek kayıt için tek elemanlı dizi |
| `markContact(admin, id, phase, reached)` | İlgili aşamanın manuel ulaşılma durumunu değiştirir |

`publish` sonucu `updated` ID listesi ve `skipped` ID/hata eşlemesidir. Eksik sonuç veya silinmiş kayıt diğer geçerli kayıtları durdurmaz. Seçim kapsamı ekranda gösterilir; toplu onay aynı `approve` metodunu her seçili kayda uygular.

Not ve burs girdileri veritabanına yazılmadan önce tam sayı olarak doğrulanır; ondalıklı PHP değeri veya `"12.0"` kabul edilmez. NULL ve gerçek sıfır ayrıdır. Katılmadı seçimi `0/0` atar. Bu seçimi geri almak otomatik sıfırları temizler; aynı istekte yeni not/burs verilebilir. Yayında eksik sonuç bırakan düzeltme reddedilir; eksik kaydetmek için önce sonuç yayını kapatılır.

Başvuru bilgilerindeki gerçek değişiklikler başvuru iletişimini; katılım/not/burs değişiklikleri sonuç iletişimini sıfırlar. Aynı değeri tekrar kaydetmek veya yalnız yayın/iletişim anahtarını değiştirmek bu sıfırlamayı yapmaz. Oturumun bildirilen ayrıntıları, şube/grup adı veya dönem başlığı/açıklaması değişirse ilgili başvuruların başvuru iletişimi de sıfırlanır. Hiçbir komut kendiliğinden mesaj göndermez.

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

Sonuç yayını kapalıyken `result` içinde yalnız `published: false` bulunur; not, burs ve sonuç katılım alanları eklenmez. Onay yayınlanana kadar kabul kararı döndürülmez. Onay yayını açılınca `arrival_at` oturumdan 30 dakika öncesini gösterir. `can_edit`, `can_delete` ve `restriction`, ortak üye işlem kurallarını yansıtır. Modelin hassas alanlarının ham JSON çıktısında gizlenmesi ek korumadır.

## Geliştirme verileri ve doğrulama

```bash
php artisan db:seed --class=ScholarshipDemoSeeder
php tests/Scholarship/run-mariadb.php
```

Seed yalnız `local/testing` ortamında ve henüz bursluluk dönemi yoksa çalışır. Üç şube, iki örnek okul, 14 sınıf/durum, dört sınav grubu, kapalı bir örnek dönem ve dokuz oturum oluşturur. Gerçek kullanıcı/başvuru üretmez; mevcut dönemi veya yönetici değişikliklerini yeniden oluşturmaz. Ana `DatabaseSeeder` içine otomatik bağlanmaz.

Test komutu yerel MariaDB araçlarıyla `/tmp` içinde geçici, ağ bağlantısı kapalı bir sunucu kurar. Sentetik kullanıcı şeması ve bursluluk migrationlarıyla yalnız bu modülü test eder, bitince sunucuyu ve test verilerini kaldırır. Uygulamanın yapılandırılmış veritabanına test yazısı göndermez. İki bağımsız PHP süreciyle son kontenjan, çift başvuru, aktarım ve kapasite yarışı doğrulanır.

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
