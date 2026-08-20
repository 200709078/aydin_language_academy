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

İlk dört sayfa public'tir. Seviye Tespit Sınavı ve Dökümanlar login gerektirir.
Klinik Template yalnızca görsel/tasarım kaynağıdır. Mümkün olduğunca template ini eğitim template ine uyarla.

4. Authentication akışı
Public frontend
    ↓
Seviye Tespit Sınavı / Dökümanlar
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
/seviye-tespit-sinavi   → auth gerekli
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
6. Seviye Tespit Sınavı - giriş/auth akışı
7. Dökümanlar - giriş/auth akışı

Mevcut Level/Sub Level/Theme/Exercise/Question alanlarını ayrıca istenmedikçe yeniden tasarlama.

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