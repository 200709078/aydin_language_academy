# AGENTS.md

## Proje: ALA — Learn English With ALA

Bu repository mevcut çalışan ALA Laravel uygulamasını ve yayındaki public tanıtım sitesini içerir.

Bu dosya:
1. Codex/agent için kalıcı proje kurallarını,
2. şu anda aktif olan geliştirme planını

tanımlar.

Tamamlanmış özelliklerin ayrıntılı geçmişi burada tutulmaz.

---

# 1. Mevcut sistem

- Laravel 12
- PHP 8.4+
- Composer
- Yerelde MariaDB, canlıda MySQL
- `DB_CONNECTION=mysql`
- Jetstream / Fortify authentication
- Livewire
- Çalışan admin alanı
- Çalışan üye alanı
- Çalışan public frontend

Mevcut controller, model, migration, factory, seeder, route, Blade, Livewire, authentication ve business logic yapısını çalışan mevcut uygulama olarak kabul et.

Kullanıcı açıkça istemedikçe mevcut ALA uygulamasını:
- bozma,
- yeniden tasarlama,
- refactor etme,
- yeniden adlandırma,
- taşıma,
- gereksiz yere değiştirme.

Mevcut çalışan özelliği yeniden yazmak yerine mevcut yapıyı genişlet.

---

# 2. Genel koruma kuralları

Açıkça istenmedikçe:
- Jetstream/Fortify'ı değiştirme.
- İkinci authentication sistemi oluşturma.
- İkinci `users` yapısı oluşturma.
- İkinci üye portalı oluşturma.
- İlgisiz kod temizliği yapma.
- Dependency güncelleme.
- Toplu formatlama yapma.
- Kapsam dışı refactor yapma.

Bir değişiklik mevcut uygulamaya dokunmayı gerektiriyorsa neden gerekli olduğunu kısa ve somut biçimde bildir.

Belirsiz bir teknik detayda tahmin etme; projedeki mevcut uygulamayı incele.

---

# 3. Public frontend

Public tanıtım sitesi ağırlıklı olarak:
- `resources/views/frontend/`
- `public/frontend/`

altındadır.

Kurallar:
- Internal URL'lerde Laravel route helper kullan.
- Asset'lerde mevcut proje yaklaşımını ve gerektiğinde `asset()` kullanımını koru.
- Responsive davranışı koru.
- Kullanıcı açıkça istemedikçe yeni frontend/CSS/UI framework ekleme.
- Gereksiz JavaScript paketi ekleme.
- Mevcut metinlerin yerine pazarlama metni uydurma.

ALA marka sloganları:
Site genelinde kullanıcı aksini istemedikçe veritabanındaki slogans tablosundaki sloganlar, mevcut sistemdeki rastgele kullanıma (Örnek: Ana sayfada slider satırının solunda) uygun olarak kullanılır.

---

# 4. Authentication ve kullanıcı türleri

Mevcut Jetstream/Fortify authentication sistemi kullanılır.

Temel kullanıcı türleri:
1. Login gerektirmeyen ziyaretçi
2. Login gerektiren üye
3. Admin kullanıcı

Yeni özelliklerde mevcut kullanıcı hesabı ve login akışı yeniden kullanılır.

Yeni bir login redirect davranışı uydurma; mevcut çalışan yönlendirmeyi koru.

---

# 5. UI ve tasarım standardı

Yeni frontend veya admin ekranlarında **mevcut ALA tasarım dili korunur**.

Önce projedeki mevcut örnekleri incele.

Varsa mevcut:
- button,
- card,
- form,
- input/select,
- checkbox/radio,
- table,
- pagination,
- badge,
- alert,
- modal,
- dropdown,
- tab/accordion,
- spacing,
- typography,
- renk,
- ikon,
- loading/empty state

desenlerini yeniden kullan.

Aynı amaca hizmet eden paralel ve farklı görünümlü ikinci bir UI bileşeni oluşturma.

Mevcut bileşen ihtiyacı karşılamıyorsa yeni bileşen oluşturulabilir; ancak mevcut ALA:
- renk,
- boyut,
- boşluk,
- tipografi,
- etkileşim,
- admin/üye alanı

desenleriyle uyumlu olmalıdır.

Kullanıcı açıkça istemedikçe yeni tasarım sistemi veya farklı görsel dil oluşturma.

Desktop ve mobil görünümü birlikte koru.

## 5.1 Onay modalı

Arşivleme, silme, kalıcı silme ve geri döndürülemez yönetici işlemlerinde tarayıcı `confirm()` veya Livewire `wire:confirm` kullanma.

Normal Blade/form akışlarında mevcut `x-action-confirmation-modal`, Livewire akışlarında mevcut `x-review-action-modal` veya aynı ALA görsel kabuğunu kullan.

Parola/2FA gerektiren mevcut güvenlik adımlarını kaldırma.

---

# 6. Route kuralları

Yeni route eklemeden veya değiştirmeden önce:
1. mevcut route'ları incele,
2. isimlendirme desenini belirle,
3. çakışmaları kontrol et,
4. yalnız istenen kapsamı değiştir.

Mevcut çalışan route'ları gereksiz yere yeniden adlandırma veya taşıma.

Internal linklerde mümkün olduğunca route helper kullan.

---

# 7. Veritabanı ve migration güvenliği

MariaDB/MySQL uyumluluğunu koru.

Kurallar:
- Mevcut migration dosyalarını değiştirme.
- Yeni özellik için yeni migration oluştur.
- Mevcut verileri silme.
- Gereksiz schema refactor yapma.
- Foreign key, unique constraint ve index'leri bilinçli tanımla.
- MySQL/MariaDB constraint/index isim sınırlarını dikkate al.
- Geçmiş/veri kaybına yol açabilecek cascade ilişkilerini dikkatle değerlendir.
- Projede soft delete yaklaşımı varsa önce onu incele.

Kullanıcı bir geliştirme adımını açıkça başlattığında o adımın kapsamındaki normal model/migration/write işlemleri için sürekli tekrar izin isteme.

Kullanıcı açıkça istemedikçe şu yıkıcı işlemleri yapma:
- `migrate:fresh`
- `migrate:reset`
- database drop
- toplu veri silme
- production database üzerinde doğrudan değişiklik
- gerçek veriyi geri döndürülemez biçimde değiştiren komutlar

Gerçek veri kaybı riski varsa işlemi yapma; bildir.

Testte mümkünse izole test veritabanı kullan.

---

# 8. Git ve secret kuralları

Dosya değiştirmeden önce `git status` kontrol et.

Repository'deki mevcut değişikliklerin bu göreve ait olduğunu varsayma.

Kullanıcı istemedikçe:
- discard,
- restore,
- reset,
- clean,
- stash

yapma ve mevcut kullanıcı değişikliklerinin üzerine yazma.

Açık talep olmadan:
- `git reset --hard`
- `git clean -fd`
- `git checkout -- .`

kullanma.

`.env`, parola, API key, token, secret, cache veya yerel database dosyalarını:
- gösterme,
- hard-code etme,
- frontend'e koyma,
- dokümana kopyalama,
- commit etme.

---

# 9. Çalışma yöntemi

Kapsamlı işlerde:
1. İncele.
2. Bulguları bildir.
3. Yalnız istenen adımın en küçük güvenli implementasyonunu uygula.
4. 10. bölüme göre değişikliğe uygun doğrulamayı yap.
5. Değişen dosyaları ve test sonuçlarını bildir.
6. Dur.

Kullanıcı yalnız analiz istiyorsa dosya değiştirme.

Kullanıcı belirli bir geliştirme adımını isterse **yalnız o adımı uygula**.

**Bir sonraki numaralı adıma kendiliğinden geçme.**

Aynı adım içindeki normal ve güvenli işlemler için sürekli tekrar izin isteme.

Kapsam dışı iyileştirme görürsen uygulama; kısa not olarak bildir.

---

# 10. Test standardı

Doğrulama kapsamını değişen davranışa ve hata riskine göre seç. Her değişiklikte bütün test paketini veya bütün ekranları kontrol etme.

- Yalnız doküman değişikliğinde içerik, tutarlılık ve diff kontrolü yeterlidir; uygulama testlerini çalıştırma.
- Basit metin/çeviri veya küçük görsel değişikliklerde ilgili çıktıyı kontrol et. Geri alınabilir, düşük etkili değişiklikler için yeni otomatik test yazma.
- UI değişikliğinde etkilenen görünümü ve etkileşimi doğrula. Form validation, hata/başarı mesajları, linkler, asset'ler ve auth/authorization kontrollerini yalnız ilgili davranış değişiyorsa veya etkilenme riski varsa yap. Yeni ekranlarda bu ekranın sunduğu davranışları kapsa.
- Yerleşim/responsive değişikliğinde masaüstü ve mobilde temsili genişlikleri kontrol et. Tablet için ayrı davranış veya breakpoint varsa onu da kontrol et; her metin değişikliğinde bütün cihaz kontrollerini tekrarlama.
- Veritabanı değişikliğinde ilgili migration/schema, foreign key, unique, index, nullable/default ve silme davranışlarını doğrula. Model ilişkilerini yalnız model/ilişki kodu da değişiyorsa test et. MariaDB/MySQL'e özgü constraint veya kilitleme davranışını ilgili veritabanı motorunda doğrula.
- İş kuralı, sahiplik/yetki, veri görünürlüğü, silme veya eşzamanlılık değişikliğinde ilgili başarı, hata ve sınır durumlarını hedefli testlerle doğrula. Mevcut uygun testleri kullan; yeni testi anlamlı davranış veya regresyon kapsamı eksikse ekle. Uygulamayı satır satır tekrar eden test yazma.

İlgili kontroller geçince dur. Test kapsamını ancak yeni değişiklik, başarısızlık, çözülmemiş risk veya açık kullanıcı talebi varsa genişlet ya da tekrar çalıştır. Aynı kuralın bütün varyasyonlarını model, servis ve tarayıcı katmanlarında yeniden üretme; her katmanda o katmana ait davranışı ve bağlantıları doğrula.

Test için kapsam dışı model, servis, ekran veya özellik geliştirme.

Gerçek veritabanını resetleme.

Adım sonunda değişen dosyaları, yapılan doğrulamayı ve sonucunu kısa raporla. Gerekli olduğu halde yapılamayan kontrol veya açık teknik sorun varsa belirt; yapılmamış testi geçmiş gibi raporlama.

---

# 11. Aktif özellik: Bursluluk Başvuru ve Takip Sistemi

## 11.1 Amaç ve ilk sürüm kapsamı

Bu sistem **site üzerinden sınav yapmaz**. Sınavlar kurumda manuel/kağıt üzerinde yapılır.

Sistem, öğrencinin mevcut ALA hesabıyla sınav döneminde kendi başvurusunu oluşturduğu ve takip ettiği kayıt sistemi olarak çalışır.

İlk sürüm:
- sınav dönemlerini ve dönem genelinde başvuruları açma/kapatmayı,
- şubeleri, öğrencinin mevcut okul/sınıf bilgilerini ve ayrı sınav gruplarını,
- tarih, başlangıç/bitiş saati ve yapılacak sınavı içeren oturumları,
- oturum başına kontenjanı, askıya almayı ve arşivlemeyi,
- başvuru, admin onayı, değişiklik ve kalıcı silme işlemlerini,
- sınava katılımı, notları ve burs oranlarını,
- başvuru ve sınav/burs sonuçlarının ayrı yayınlanmasını,
- iki ayrı iletişim takibini ve admin tarafından başlatılan gerçek e-posta/WhatsApp gönderimlerini,
- üye **Başvurularım** alanını

yönetir.

Excel içe/dışa aktarma ve dönem sonrasında başvurusu bulunan oturumların kalıcı silinmesi ilk sürümde uygulanmaz; gelecek sürümler için tasarımda dikkate alınır.

Referans Google Form:
https://docs.google.com/forms/d/1MfPikJJN3-5ZKaYwjL8XU_D90uSh-tZt2_C_lq3rCr4/viewform?edit_requested=true&pli=1

Google Form'daki alanlar ve seçenekler bağlayıcı iş kuralı değildir. Gerekli ve tutarlı bilgiler yalnız örnek geliştirme seed'lerinde kullanılabilir; bu bölümde kararlaştırılan kurallar esas alınır.

Bursluluk sistemi mevcut online Seviye Tespit Sınavı domaininden bağımsızdır; placement-test tablolarını bursluluk altyapısı olarak kullanma.

---

## 11.2 Üyelik, öğrenci ve tek başvuru kuralı

- Başvuru yalnız giriş yapmış mevcut ALA kullanıcısı tarafından yapılır.
- Her öğrenci ayrı üye hesabı kullanır; aynı hesap üzerinden birden fazla öğrenci başvurusu yapılmaz.
- Mevcut üyelik kuralları korunur: e-posta benzersizdir; ad soyad ve telefon benzersiz değildir. Ayrı öğrenci hesapları farklı e-posta ve aynı veli telefonuyla oluşturulabilir.
- Öğrenci sonraki sınav dönemlerinde aynı hesabını kullanabilir.
- Başvuru iletişiminde ilgili User kaydındaki güncel e-posta ve telefon kullanılır; başvuru için ayrı iletişim e-postası/telefonu istenmez.
- İletişim bilgilerinin güncel ve kullanılabilir olması gerektiği uyarısı gösterilir; güncellemede mevcut profil akışı kullanılır.
- Mevcut uygun öğrenci/person yapısı varsa önce incelenir; yoksa yalnız gerekli en küçük hesap/öğrenci/başvuru ilişkisi tasarlanır. İkinci üyelik veya kimlik doğrulama sistemi kurulmaz.
- **Aynı hesap, aynı sınav döneminde aynı anda en fazla bir başvuruya sahip olabilir.** Kural şube, sınav grubu ve oturumdan bağımsızdır; kontrol hesap + sınav dönemi kapsamındadır.
- Bu kontrolün farklı hesapların aynı gerçek kişiye ait olduğunu kendiliğinden tespit ettiği varsayılmaz.
- İzin verilen koşullarda başvuru kalıcı silinirse aynı dönemde yeniden başvuru yapılabilir. Silme dönemlik başvuru hakkını tüketmez; bu amaçla geçmiş başvuru hakkı kaydı tutulmaz.
- Her yeni başvurunun benzersiz başvuru numarası olur. Başvuru düzenlendiğinde numarası korunur; silip yeniden başvurulduğunda yeni numara üretilir.

Duplicate ve sahiplik kontrolü yalnız frontend'e bırakılmaz. Eşzamanlı isteklerde de hesap başına dönemlik tek mevcut başvuru kuralı korunur.

Öğrencinin başvurudaki adı, mevcut okulu ve sınıf/durumu ilgili döneme ait bilgi olarak korunur. Profil güncellemeleri onaylı/kapalı başvurunun öğrenci bilgilerini dolaylı biçimde değiştirmemelidir; iletişim bilgileri mevcut User kaydından alınmaya devam eder. Kesin alan ve ilişki tasarımı 1B'dedir.

---

## 11.3 Şube, mevcut okul/sınıf, sınav grubu ve oturum

Birbirinden ayrı kavramlar:

1. **ALA şubesi:** Ortaca, Dalaman, Köyceğiz birer ALA okulu gibi ele alınır. Başvuruda şube seçilir.
2. **Öğrencinin mevcut okulu:** Öğrencinin halen öğrenim gördüğü okul başvuruda seçilir ve saklanır. Bu okul ALA şubesinden ayrıdır; okul listesi admin tarafından yönetilir.
3. **Öğrencinin mevcut sınıfı/durumu:** 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, Mezun, YKS vb. seçenekler ayrı listede yönetilir ve başvuruda saklanır.
4. **Sınav grubu:** Adminin tanımladığı başvuru seviyesidir. İlkokul, Ortaokul, Lise, Mezun veya ihtiyaca göre 9. Sınıf gibi adlar kullanılabilir. Admin grup ekleyebilir, düzenleyebilir ve pasifleştirebilir. Grup listesi koda sabitlenmez.
5. **Sınav oturumu:** Bir dönemde, bir şube ve sınav grubu için belirli tarih ve başlangıç/bitiş saatlerinde yapılacak sınavdır. Yapılacak sınavın adı/türü de tanımlanır.

Öğrencinin mevcut sınıfı/durumu ile sınav grubu farklı alanlardır. Mevcut sınıftan otomatik grup ataması veya sınıf–grup eşleşmesine dayalı başvuru engeli oluşturulmaz. Seçim başvuru sahibinin sorumluluğundadır; seçilen kayıtların mevcut ve başvuruya uygun olması doğrulanır.

Örnek: mevcut okulu Atatürk Ortaokulu, mevcut sınıfı 8 olan öğrenci Ortaca şubesindeki Ortaokul grubunun belirlenen tarihli 08.00–10.00 oturumunu seçebilir.

Temel oturum tanımı **sınav dönemi + şube + sınav grubu + yapılacak sınav + tarih + başlangıç/bitiş saati** bilgilerini içerir. Kesin alanlar, ilişkiler ve çakışma/unique kuralları 1B'de tasarlanır.

Farklı şubelerde aynı grup, tarih ve saatlerde ayrı oturumlar olabilir. Her oturum kendi kontenjanını taşır.

Fiziksel salon/sınıf ayrı kavramdır; ilk sürümde fiziksel salon kontenjanını gruplar arasında otomatik paylaştıran yapı kurulmaz. Aynı salona girecek grupların kontenjanlarını admin planlar.

Projede merkezi şube yapısı varsa yeniden kullanım için incelenir. Yoksa bütün siteyi Branch CMS'e dönüştürme; şube verisinin paylaşılmış mı bursluluğa özel mi olacağı teknik olarak 1A/1B'de belirlenir.

---

## 11.4 Sınav dönemi ve dönem genelinde başvurular

Admin sınav dönemi oluşturabilir. En az:
- başlık,
- başvuru başlangıç/bitiş zamanı,
- sınav tarih aralığı,
- açıklama,
- aktif/pasif durum,
- dönem genelinde başvuruları açma/kapatma kontrolü

desteklenir.

Sınav birden fazla güne yayılabilir. Oturumlar dönemin sınav tarih aralığı içinde tanımlanır; geçmiş oturumdan yeni başvuru başlatılmaz. Zaman dilimi ve tarih/saat doğrulamalarının teknik karşılığı 1B'de belirlenir.

Üye başvuru işlemleri için dönem aktif, başvuru tarih aralığı uygun ve dönem genelindeki başvurular açık olmalıdır. Admin tarih aralığı bitmeden de dönemin başvurularını kapatabilir. Tarih aralığı dışında yalnız açma anahtarı yeni üye başvurusuna izin vermez.

Dönemin başvuruları kapandığında üye mevcut başvurusunu düzenleyemez veya silemez; durumunu ve yayınlanmış bilgilerini görebilir. Admin gerekli yönetim işlemlerini yapabilir; sahiplik, veri bütünlüğü ve kapasite kuralları korunur.

Dönem genelinde başvuruları kapatma ile tek oturumu askıya alma ayrı işlemlerdir.

---

## 11.5 Oturum başına kontenjan ve askıya alma

**Admin kontenjanı her oturum için ayrı belirler.** Eşit veya farklı dağılım seçebilir. Şube/grup toplam kontenjanı ayrıca girilmez; ilgili oturumların kontenjanlarının toplamından hesaplanır. Ortak bir şube/grup kotası veya otomatik eşit dağıtım zorunluluğu yoktur.

Örnek: aynı dönemde Dalaman / Lise için:
- 08.00–10.00 → 15 kişi,
- 10.00–12.00 → 25 kişi,
- 16.00–19.00 → 20 kişi.

Toplam 60'tır; her oturum yalnız kendi doluluğuna göre başvuru kabul eder.

Kurallar:
- Başvuru oluşturulduğu anda seçilen oturumda yer ayrılır. Onay bekleyen ve onaylanan başvurular kontenjan tüketir; yayın durumu kontenjanı etkilemez.
- Oturum kendi kontenjanına ulaştığında yalnız o oturuma başvuru kapanır. Dolu oturum görünür, seçilemez ve Dolu olarak belirtilir. Diğer uygun oturumlar başvuru alabilir.
- Admin doluluğu örneğin 18 / 20 olarak görür.
- Admin kapasiteyi mevcut doluluğun altına indiremez. Dolu oturuma admin aktarımı için önce kapasite artırılmalıdır; admin kapasiteyi aşamaz.
- İzin verilen kalıcı başvuru silme işlemi ilgili oturumda yer açar.
- Oturum değişiminde eski yerin bırakılması ve yeni yerin ayrılması birlikte güvenli yapılır. Yeni oturum uygun değilse mevcut başvuru/yer kaybedilmez.
- Oluşturma, silme, aktarma ve kapasite güncellemede server-side doğrulama ve eşzamanlılık koruması uygulanır.

Her oturumun kendi aktif/pasif anahtarı vardır. Dalaman / Lise / 08.00–10.00 oturumunun askıya alınması aynı grubun diğer oturumlarını kapatmaz.

Askıya alınmış oturumda admin dışındaki kullanıcılar işlem yapamaz; yeni başvuru, mevcut başvuruyu değiştirme/başka oturuma taşıma ve silme engellenir. Mevcut başvurular korunur ve yer tutmaya devam eder.

Üyeye oturumun yönetici tarafından askıya alındığı açıklanır ve mevcut iletişim akışına yönlendirme gösterilir. Dolu, askıda, arşivli ve dönem başvuruları kapalı durumları birbirinden ayrılır. Arşivleme/silme politikası 11.18'dedir.

---

## 11.6 Başvuru yaşam döngüsü, değişiklik ve kalıcı silme

Başvuru karar durumları:
- admin onayı bekliyor,
- onaylandı/kabul edildi.

Admin red işlemi yapmaz; gerekli durumda başvuruyu kalıcı siler. Ayrı reddedildi veya iptal edildi durumlarıyla bu akış değiştirilmez. Kesin teknik status adları 1B'de belirlenir.

Üyenin başvurusunu değiştirebilmesi veya kalıcı silebilmesi için birlikte:
- başvurunun kendi hesabına ait olması,
- başvuru döneminin üye işlemlerine açık olması,
- başvurunun henüz onaylanmamış olması,
- mevcut oturumun askıda veya arşivde olmaması

gerekir.

Değişiklikte hedef şube/grup/oturum başvuruya açık ve müsait olmalıdır. Başvuru sahibi farklı grup veya seviyeyi seçebilir; örneğin 5. Sınıf grubundaki başvuruyu uygun 12. Sınıf grubuna taşımasına mevcut sınıf bilgisi üzerinden engel konulmaz.

Üye onaylanan başvuruyu **düzenleyemez ve silemez**; yalnız durumunu ve yayınlanan bilgileri görebilir. Bu kilit, başvuru sonucu henüz yayınlanmamış olsa da geçerlidir.

Onaylı başvuruyu admin değiştirebilir veya silebilir. Admin oturum değişikliğinde duplicate/kontenjan kurallarını korur. Onaylı başvurunun admin tarafından düzenlenmesi üye için yeniden düzenleme hakkı oluşturmaz.

Kalıcı silme sonrası, başvuruya açık bir oturum bulunuyorsa aynı hesap aynı dönemde yeniden başvurabilir. Önceki şube/grup/saat veya önceki başvuru sayısı yeni başvuruyu engellemez; aynı anda tek mevcut başvuru kuralı korunur.

Başvuru bilgilerinin admin tarafından değiştirilmesi ilgili başvuru iletişimini yeniden ulaşılmadı yapar. Yayın açıksa değişiklik üyeye hemen görünür; otomatik mesaj gönderilmez.

Silme ve kritik yönetim işlemlerinde mevcut ALA onay modalı kullanılır. İzin verilen kalıcı başvuru silme ile oturum arşivleme farklı işlemlerdir.

---

## 11.7 Başvuru sonucunu yayınlama

**Admin onayı ile üyeye yayınlama ayrı tutulur.** Her başvurunun kendi Başvuru sonucunu yayınla anahtarı bulunur; başlangıçta yayın kapalıdır.

Admin tek başvuruyu yayınlayabilir veya seçilen başvuruların anahtarlarını toplu açabilir. Bütün başvuruları yayınlamak da aynı kayıt bazlı anahtarları toplu açma işlemidir; dönem için bütün mevcut/gelecek kayıtları otomatik yayınlayan ortak bayrak kullanılmaz.

Üye duruma göre:
- Başvurunuz değerlendiriliyor / Admin onayı bekleniyor,
- Başvuru sonucu henüz yayınlanmadı,
- Başvurunuz onaylandı

bilgilerini görür. Onaylanmış fakat yayını kapalı başvurunun kabul kararı üyeye açıklanmaz.

Onay yayınlandığında tarih, başlangıç/bitiş saati, şube, sınav grubu ve sınav bilgileriyle birlikte **en az 30 dakika önce hazır bulunma** uyarısı gösterilir.

Yayındaki başvuru bilgileri admin tarafından değiştirildiğinde yayın açık kalır ve güncel bilgiler hemen üyeye yansır. Başvuru sonucunun yayını ile sınav/burs sonucunun yayını birbirinden bağımsızdır.

---

## 11.8 Başvuru sonrası iletişim ve gerçek bildirimler

Gerçek e-posta ve WhatsApp gönderimi ilk sürümdedir. Gönderimler adminin her başvuru için ayrı düğmeye basması veya seçilen başvurular için toplu gönderim başlatmasıyla yapılır.

Başvuru oluşturma, onaylama, yayınlama veya değiştirme kendiliğinden e-posta/WhatsApp göndermez. Gönderim kararını ve gerekli kontrolleri admin verir.

Başvuru bildiriminde uygun olarak kabul bilgisi, tarih, başlangıç/bitiş saati, şube, sınav grubu ve gerekli sınav bilgileri yer alır. Alıcı bilgileri ilgili User kaydından alınır.

Başvuru iletişimi için ayrı alan:
- ulaşılmadı — varsayılan,
- ulaşıldı.

Admin sistem dışında e-posta gönderebilir veya telefonla görüşebilir; ardından ulaşıldı durumunu elle işaretleyebilir. İlgili aşamada ulaşıldı olan kayda tekrar sistem bildirimi gerekmez; toplu gönderim bu kayıtları atlar.

Başvuruya ait kullanıcıya bildirilen bilgiler admin tarafından değiştirildiğinde başvuru iletişimi tekrar ulaşılmadı olur. Bu değişiklik otomatik gönderim başlatmaz.

E-posta ve WhatsApp için teknik gönderim durumları ayrı takip edilir; en az bekliyor, gönderildi ve gönderilemedi durumları desteklenir. Teknik gönderildi ile adminin ulaşıldı kaydı aynı kavram değildir. İletişimin yeniden ulaşılmadı olması geçmişte başarılı olan gönderimi gönderilmedi olarak değiştirmez.

İletişim veya teknik gönderim durumunu güncellemek, kendi başına iletişimi tekrar ulaşılmadı yapan bir başvuru bilgisi değişikliği sayılmaz.

Gerçek sağlayıcılar 2I entegrasyon adımında mevcut altyapı incelenerek kullanıcıyla belirlenir. Tekli/toplu gönderim ve yeniden denemede aynı mesajın istenmeyen tekrarını önleyen teknik kurallar bu adımda uygulanır.

---

## 11.9 Sınava katılım

Admin katılım durumunu tutar:
- işaretlenmedi,
- katıldı,
- katılmadı.

Katılımı işaretlenmemiş veya katılmış fakat sonucu henüz girilmemiş öğrenciye başlangıçtan itibaren 0 not ve %0 burs atanmaz.

**Katılmadı olarak işaretlenen öğrenci için not 0 ve burs %0 / Burs Yok olur.** Katılım durumu ayrıca saklanır; böylece sınava katılıp gerçekten 0 alan öğrenci ile katılmayan öğrenci ayırt edilir.

Katılım/sonuç bilgisinin admin tarafından değiştirilmesi sınav/burs sonucu iletişimini yeniden ulaşılmadı yapar; otomatik bildirim göndermez. Katılım düzeltmelerinin mevcut not/bursla tutarlılığı 1B'de ortak doğrulama kurallarıyla tasarlanır.

---

## 11.10 Not

Admin notları liste üzerinden hızlı girebilir; filtreleme ve nota göre sıralama desteklenir.

Kurallar:
- Not **0–100 arasında tam sayı** olmalıdır; ondalıklı not kabul edilmez.
- Not girilmedi durumu NULL'dır; gerçek 0 puandan ayrıdır.
- Katılmadı için 11.9'daki 0 kuralı uygulanır.
- Yayındaki sonuç değişirse güncel not hemen üyeye yansır ve sonuç iletişimi tekrar ulaşılmadı olur.
- İlk sürümde Excel not importu yapılmaz.

---

## 11.11 Burs oranı

Öğrencinin **ilgili dönem başvurusuna ait tek burs oranı** tutulur. Aynı öğrenci sonraki dönemde farklı burs alabilir; eski dönem sonucu korunur. Arayüzde öğrencinin bursu olarak gösterilir.

Değerler:
- %100,
- %90,
- %80,
- %70,
- %60,
- %50,
- %40,
- %30,
- %20,
- %10,
- %0 / Burs Yok.

Burs belirlenmedi durumu NULL'dır; %0 / Burs Yok değerinden ayrıdır. Katılmadı için 11.9'daki %0 kuralı uygulanır.

Admin nota göre sıralı listede mevcut ALA tek seçim/radio yaklaşımını kullanır. Yayındaki burs değişirse güncel oran hemen üyeye yansır ve sonuç iletişimi yeniden ulaşılmadı olur.

---

## 11.12 Sınav/burs sonucunu yayınlama

Not/burs girilmesi tek başına üyeye görünürlük sağlamaz. Her başvurunun, başvuru sonucunun yayınından ayrı **Sınav/burs sonucunu yayınla** anahtarı bulunur; başlangıçta kapalıdır.

Admin anahtarı tekli veya toplu açabilir. Toplu yayın, seçilen kayıtların kendi anahtarlarını açar; gelecekte oluşacak başvuruları veya eksik sonuçları kendiliğinden yayınlayan dönem bayrağı kullanılmaz.

**Notu veya bursu henüz girilmemiş kayıt tekli veya toplu işlemde yayınlanamaz.** Eksik kayıtlar yayına açılmaz ve admine bildirilir. 0 not ve %0 burs geçerli değerlerdir; eksiklik kontrolü NULL üzerinden yapılır.

Yayın açıkken yapılacak düzenlemeler de eksik sonuç yayınlanmaması kuralını korumalıdır. Geçerli not/burs değiştiğinde yayın açık kalır; kullanıcıya güncel sonuç hemen gösterilir ve ilgili sonuç iletişimi ulaşılmadı olur.

Yayın öncesinde Sonuçlar hazırlanıyor / Sonuç henüz yayınlanmadı gibi durum gösterilir. Yayından sonra not ve burs oranı Başvurularım alanında görünür.

Yayınlanmamış not/burs verisi frontend, HTML/Livewire verisi veya API çıktısına sızmamalıdır.

---

## 11.13 Sınav/burs sonucu iletişimi

Başvuru iletişiminden bağımsız ikinci bir iletişim alanı bulunur:
- ulaşılmadı — varsayılan,
- ulaşıldı.

Başvuru kabulü için ulaşıldı olması, sınav/burs sonucu için de ulaşıldı sayılmaz.

Admin yayınlanan sonuç için gerçek e-posta/WhatsApp gönderimini tekli veya toplu başlatabilir; bildirim not, burs oranı ve varsa sonraki işlem açıklamasını içerir. Telefon veya sistem dışı e-postayla iletişim kurduysa sonuç için ulaşıldı durumunu elle işaretleyebilir.

Sonuç aşamasında ulaşıldı olan kayıtlar ilgili toplu gönderimden çıkarılır. Katılım, not veya burs sonucu admin tarafından değişirse yalnız ilgili sonuç iletişimi yeniden ulaşılmadı olur; yayın açık kalır ve otomatik mesaj gönderilmez.

E-posta/WhatsApp teknik durumları ve manuel iletişim alanının ayrımı için 11.8'deki kurallar uygulanır. İletişimin kendisini güncellemek tekrar ulaşılmadı sıfırlaması yapmaz.

---

## 11.14 Başvurularım

Üye ekranının adı **Başvurularım** olacaktır; Sınavlarım kullanılmaz. Üye yalnız kendi hesabının başvurularını ve geçmiş dönem sonuçlarını görebilir.

Uygun alanlar:
- başvuru numarası ve öğrenci,
- sınav dönemi,
- öğrencinin mevcut okulu ve mevcut sınıfı/durumu,
- ALA şubesi, sınav grubu ve yapılacak sınav,
- tarih, başlangıç/bitiş saati,
- kullanıcıya açıklanabilecek başvuru durumu ve başvuru yayın durumu,
- katılım bilgisi,
- sınav/burs sonucu yayın durumu,
- yalnız yayınlandıysa not ve burs oranı.

Onay/yayın/askı/arşiv/dönem kapanışı durumuna uygun açıklamalar gösterilir. Onaylanmış başvuru, kapalı dönem veya askıda/arşivli oturum için üyeye düzenleme/silme izni verilmez. Uygun başvuruda değişiklik ve kalıcı silme 11.6'ya göre yapılır.

Admin-only operasyon alanları üyeye gösterilmez. Başvuru ve sonuç yayın kuralları liste, detay ve bütün veri çıktılarında aynı şekilde uygulanır.

---

## 11.15 Admin filtreleri ve toplu işlemler

Admin filtreleri en az:
- sınav dönemi,
- şube,
- sınav grubu ve sınav/oturum,
- tarih ve başlangıç/bitiş saati,
- öğrencinin mevcut okulu ve mevcut sınıfı/durumu,
- başvuru onay durumu,
- katılım durumu,
- başvuru iletişimi ve sonuç iletişimi ayrı ayrı,
- burs oranı,
- başvuru yayını ve sınav/burs sonucu yayını ayrı ayrı,
- oturumun açık/askıda/arşivli durumu.

Aramada başvuru numarası, öğrenci/kullanıcı adı, User telefonu ve User e-postası kullanılabilir.

Toplu işlemler:
- başvuru onayı,
- başvuru sonuçlarını yayınlama,
- sınav/burs sonuçlarını yayınlama,
- başvuru veya sonuç aşaması için ayrı e-posta/WhatsApp gönderimi.

Seçili kayıtlar ile filtreye uyan bütün kayıtların kapsamı admin arayüzünde açık gösterilir. Tekli işlemdeki yetki, eksik sonuç ve iletişim kuralları toplu işlemlerde de uygulanır. Kritik toplu işlemler mevcut ALA onay modalını kullanır.

İlk sürümde Excel içe/dışa aktarma işlemleri eklenmez.

---

## 11.16 Gelecekte Excel içe/dışa aktarma

**Excel import ve export ilk sürüm kapsamında değildir.** Başvuru/sonuç dışa aktarma ekranı, indirme düğmesi, not importu veya sırf bunlar için paket eklenmez.

Veri modeli ve ortak iş kuralları gelecekte aktarımı destekleyecek şekilde tasarlanır:
- benzersiz başvuru numarası,
- dönem, öğrenci/hesap, okul, mevcut sınıf/durum, şube, sınav grubu ve oturum ilişkileri,
- not/burs NULL ve 0 ayrımı,
- tek başvuru, kapasite, yayın ve iletişim doğrulamalarının bütün yazma yollarında kullanılabilmesi.

Gelecekteki ayrı bir geliştirmede başvuru/sonuç .xlsx exportu ve not importu değerlendirilebilir. Kolonlar, dosya doğrulama, ön izleme, eşleştirme, duplicate ve satır bazlı hata davranışları o adımda belirlenir. Mevcut Excel altyapısı varsa önce incelenir.

---

## 11.17 Gelecekte şube bazlı yetki

İlk sürüm mevcut admin yetkileriyle çalışır. Veri modeli ve sorgular ileride yalnız Dalaman, Ortaca veya Köyceğiz erişimine engel olmayacak şekilde tasarlanır.

Kullanıcı açıkça istemeden mevcut global authorization sistemi değiştirilmez.

---

## 11.18 Arşivleme, silme ve işlem geçmişi

- Başvurusu bulunan oturum ilk sürümde silinemez; admin tarafından arşive alınabilir.
- Arşivleme mevcut başvuruları, notları ve burs sonuçlarını silmez. Üye kendi başvurusunun izin verilen durum/yayın bilgilerini görmeye devam eder; arşivli oturuma yeni üye başvurusu ve üyeden değişiklik/silme yapılamaz.
- Başvuru ve sınav dönemi bittikten sonra oturumların kalıcı silinmesi gelecekte ayrı geliştirme olabilir. İlişkiler, bağımlılıklar ve silme politikası 1B'de buna engel olmayacak şekilde değerlendirilir; ilk sürümde başvurusu olan oturum için silme akışı uygulanmaz.
- Bu oturum politikası, 11.6'daki yetkili üye/admin kalıcı başvuru silme akışını ortadan kaldırmaz. Başvurunun bağlı verilerinin silinme davranışı 1B'de açıkça tanımlanır; başka başvurular, User hesabı ve ortak tanımlar etkilenmez.
- Başvuru veya sonuç kaydında adminin yaptığı değişikliklerin tarihçesi, önceki değerleri veya değiştiren adminin kimliği saklanmaz. Bursluluk için audit tablosu/framework'ü kurulmaz.
- Mevcut uygulamanın genel log altyapısı değiştirilmez. E-posta/WhatsApp teknik gönderim durumları, admin değişiklik tarihçesinden ayrı ihtiyaçtır ve takip edilir.

---

# 12. Bursluluk sistemi geliştirme planı

Admin öncesi hazırlık (1A–1E ve 2A) tamamlandı. Sıradaki uygulama adımı **2B — Sınav dönemi yönetimi**. Mevcut servislerin kullanım sözleşmesi, kilitleme düzeni ve doğrulama komutu [docs/scholarship-foundation.md](docs/scholarship-foundation.md) dosyasındadır; ekranlar bu ortak altyapıyı kullanır.

Kurallar:
- Kullanıcı hangi adımı isterse yalnız o adımı uygula.
- Adım sonunda raporla ve dur; sonraki adıma otomatik geçme.
- Bütün uygulama adımlarında 10. bölümdeki doğrulama ölçütlerini kullan. Aşağıdaki test listeleri ilgili özelliğin doğrulama kapsamını tanımlar; her alt adımda baştan çalıştırılacak ortak bir kontrol listesi değildir.
- 1A, 1B, 2A, 3A analiz adımlarıdır; kod değiştirmez.
- 1B yalnız veri sözleşmesi tasarlar; migration oluşturmaz.
- 11. maddede kesinleşmiş iş kurallarını yeniden karar konusu yapma; teknik ayrıntıları mevcut uygulamayı inceleyerek ilgili adımda belirle.
- Ortak domain kuralları 1C'de kurulur, 1E'de doğrulanır; admin ve üye ekranları aynı kuralları kullanır.
- Üye ekranları geliştirilene kadar görünürlük, sahiplik ve üye işlem sınırları ortak domain/sunum kuralları üzerinden test edilir. Gerçek üye HTML/Livewire/API çıktısı ve uçtan uca akışlar ilgili 3.x adımlarında doğrulanır; test gerekçesiyle sonraki adıma erken geçilmez.
- Excel içe/dışa aktarma ilk sürümde uygulanmaz; 2J sonraki sürüm için ayrılmıştır.

Örnek Codex promptu:

`AGENTS.md dosyasını oku ve yalnız 1A adımını uygula. Bir sonraki adıma geçme.`

---

## 1. Veri Modeli

### 1A — Mevcut yapıyı analiz et
Kod değiştirme.

İncele:
- mevcut `users`, profil, üyelik ve iletişim bilgileri; benzersiz e-posta ve benzersiz olmayan telefon kullanımı,
- bir hesabın tek öğrenciyi temsil etmesi için yeniden kullanılabilecek öğrenci/person yapıları,
- şube verisinin mevcut kullanımı ve öğrencinin öğrenim gördüğü okul yapısı,
- mevcut sınıf/durum seçenekleri ile sınav gruplarını ayrı tutmaya uygun yapılar,
- admin mimarisi, model/controller/route isimlendirmesi ve CRUD/List/Livewire desenleri,
- status/enum, arşivleme, soft delete ve ilişkili kayıt silme yaklaşımları,
- notification/mail altyapısı ve mevcut log davranışı.

Yeniden kullanılabilecek yapıları ve riskleri bildir. Yeni auth sistemi, çok öğrencili hesap akışı veya merkezi Branch CMS oluşturma. Başvuru/sonuç değişiklik geçmişi ve değiştiren kişi kaydı bu modülün kapsamı değildir; mevcut genel log altyapısını değiştirme.

İleride Excel desteğini engelleyecek mevcut bir kısıt varsa bildir; ilk sürüm için Excel entegrasyonu tasarlama veya paket ekleme.

1B'nin teknik karar noktalarını çıkar ve dur.

### 1B — Veri sözleşmesini tasarla
Kod değiştirme.

1A'ya ve 11. maddedeki kararlara göre şu yapıları tasarla:
- sınav dönemi, başvuru tarih aralığı ve dönem genelinde başvuruları kapatma,
- ALA şubesi ve öğrencinin öğrenim gördüğü okulun ayrı ilişkileri,
- mevcut kullanıcı hesabına bağlı tek öğrenci ve sonraki dönemlerde aynı hesabın kullanımı,
- öğrencinin mevcut sınıf/durumu ile admin tanımlı sınav grubunun ayrı ilişkileri,
- sınav türü/başlığı, oturum tarihi, başlangıç/bitiş saati,
- oturumun bağımsız kontenjanı, doluluğu, askıya alınması ve arşivlenmesi,
- benzersiz başvuru numarası ve hesap + dönem başına en fazla bir mevcut başvuru,
- onay bekleyen/onaylanan başvuru, ayrı başvuru sonucu yayını,
- kullanıcı/admin düzenlemesi, kalıcı silme ve yeniden başvuru,
- hesaptaki iletişim bilgilerinin kullanımı; başvuru ve sonuç iletişimi için ayrı ulaşılma durumları,
- kanal ve aşama bazında teknik gönderim kayıtları; ilgili bilgi değişince iletişimin sıfırlanması,
- katılım, nullable tam sayı not, nullable burs oranı ve ayrı sonuç yayını.

Her yapı için alan, veri tipi, nullable/default, index, unique, foreign key, ilişki, silme politikası ve validation/business rule öner.

Özellikle açıkla:
- MariaDB/MySQL uyumluluğu; eşzamanlı başvuru ve oturum aktarımında transaction/locking yaklaşımı,
- bekleyen/onaylanan başvuruların yer tutması, silmede yer açılması ve kontenjanın doluluğun altına indirilememesi,
- aynı hesaba aynı dönemde ikinci mevcut başvurunun veritabanında da engellenmesi; kalıcı silmeden sonra yeni başvurunun mümkün olması,
- onaylı başvuruların, dönemi başvurulara kapanmış kayıtların ve askıya/arşive alınmış oturumlardaki başvuruların üyeye salt okunur olması,
- `not girilmedi != 0` ve `burs belirlenmedi != %0`; notun 0–100 tam sayı olması ve katılmayan öğrenci için `0/0` davranışı,
- not veya burs boşken tekli/toplu sonuç yayınının engellenmesi; yayınlanmamış verinin üye çıktılarından korunması,
- yayınlanmış bilgi değişince hemen görünmesi ve yalnız ilgili iletişim aşamasının tekrar `ulaşılmadı` olması; otomatik mesaj gönderilmemesi,
- kalıcı başvuru silmenin ilişkili kayıtlara etkisi; başvurusu bulunan oturumun silinememesi ve arşivlenebilmesi,
- başvuru/sınav dönemi sonrasında ileride eklenebilecek oturum silme ve Excel işlemlerini engellemeyen ilişkiler.

Ayrı toplam kontenjan alanı, red/iptal durumları, başvuru hakkını kalıcı tüketen kayıt veya admin değişiklik tarihçesi tasarlama. Mevcut auth/user davranışını koru. Gelecekteki silme ve Excel işlemlerini bu adımda uygulama kapsamına alma.

Migration/model oluşturma; dur.

### 1C — Veri modelini ve ortak domain kurallarını uygula
Yalnız onaylanan 1B sözleşmesini uygula:
- yeni migration, model, ilişki, cast, index, unique ve foreign key,
- hesap/dönem başına tek mevcut başvuru ve kalıcı silmeden sonra yeniden başvuru,
- oturum kapasitesi, güvenli aktarım, silmede yer açılması ve doluluğun altına kapasite indirilememesi,
- başvuru zamanı, onay, dönem kapanışı, oturum askısı/arşivi ve kullanıcı işlem sınırları,
- katılım, 0–100 tam sayı not, burs seçenekleri ve boş/0 ayrımları,
- ayrı yayın durumları ve eksik sonuçta tekli/toplu yayın engeli,
- ilgili başvuru/sonuç bilgileri değişince ilgili iletişim durumunun sıfırlanması,
- arşivleme ve güvenli silme ilişkileri.

Admin/üye ekranlarının çağıracağı ortak kuralları mevcut proje desenleriyle kur. Gerçek bildirim sağlayıcısını bağlama veya mesaj gönderme.

Mevcut migration/auth/fillable davranışını bozma. Controller/route/admin/frontend geliştirme. Yıkıcı DB komutu kullanma.

Raporla ve dur.

### 1D — Seed/geliştirme verileri
Gerekliyse idempotent geliştirme seed'leri oluştur:
- Ortaca, Dalaman, Köyceğiz şubeleri,
- birden fazla örnek öğrenci okulu,
- örnek mevcut sınıf/durum seçenekleri: 1–12, Mezun, YKS vb.,
- bunlardan ayrı örnek sınav grupları: İlkokul, Ortaokul, Lise, Mezun vb.,
- örnek sınav dönemi ve sınav türü/başlığı,
- aynı şube/grupta farklı başlangıç/bitiş saatlerine ve eşit veya farklı kontenjanlara sahip oturumlar.

Google Form'un yalnız gerekli ve tutarlı bilgileri örnek veri olarak kullanılabilir; form iş kurallarının kaynağı değildir. Seçenekleri kodda sabitleme. Gerçek production verisi uydurma veya admin değişikliklerini ezme.

Dur.

### 1E — Veri modeli ve ortak kuralların doğrulanması
1C/1D'deki doğrulama kapsamını gözden geçir. Aşağıdaki kurallarda eksik kalan testleri tamamla; daha önce geçen kontrolleri yalnız 10. bölümdeki tekrar koşulları oluşursa yeniden çalıştır:
- migration/schema, ilişkiler, index, unique, foreign key ve silme politikaları,
- bursluluk başvurusunun mevcut kullanıcı hesabıyla ilişkisi; farklı e-posta ve aynı telefonla ayrı hesapların başvurabilmesi,
- hesap/dönem başına tek mevcut başvuru; farklı sınav/şube seçerek ikinci başvuru yapılamaması,
- izin verilen kalıcı silmeden sonra aynı dönemde yeni başvuru ve sonraki dönemde aynı hesabın kullanımı,
- öğrenci okulu, mevcut sınıf/durumu ve sınav grubunun bağımsızlığı,
- oturumların bağımsız doluluğu, bekleyen/onaylanan başvuruların yer tutması ve toplamların oturumlardan hesaplanması,
- eşzamanlı kapasite ve duplicate koruması; aktarım ve silmede doğru doluluk,
- doluluğun altına kontenjan indirilememesi; başvurulu oturumun silinememesi ve arşivde kayıtların korunması,
- onay, dönem kapanışı, oturum askısı/arşivi nedeniyle kullanıcı değişiklik/silme engelleri,
- notun 0–100 tam sayı aralığı, nullable/0 not ve nullable/%0 burs, katılmama için `0/0`,
- iki bağımsız yayın durumu; not veya burs boşken tekli/toplu sonuç yayınının engellenmesi,
- yayın sonrası değişikliklerin görünürlüğü ve doğru iletişim aşamasının sıfırlanması; otomatik gönderim olmaması.

Gerçek DB resetlenmez. Raporla ve dur.

---

## 2. Admin Ekranları

### 2A — Mevcut admin yapısını analiz et
Kod değiştirme.

Admin layout/menu, controller/Livewire, table/filter/pagination, form/validation, modal/button/card/badge/alert ve authorization desenlerini incele.

Bursluluk modülünün, tanım ekranları dâhil, mevcut admin mimarisine nasıl eklenmesi gerektiğini raporla ve dur.

### 2B — Sınav dönemi yönetimi
Bursluluk sınav dönemi admin CRUD/yönetimini mevcut ALA admin tasarımına uygun uygula.

Başvuru başlangıç/bitişi, sınav tarih aralığı, aktif/pasif durum ve dönem genelinde başvuruları manuel kapatma desteklensin. Dönem kapanışıyla üyelerin yeni başvuru, düzenleme ve silme işlemleri kapansın; mevcut kayıtları görüntülenebilsin.

İlişkili verileri silen kontrolsüz cascade işlemleri oluşturma. Diğer admin alt ekranlarına geçme.

Dur.

### 2C — Tanımlar, oturum ve kontenjan yönetimi
Mevcut ALA UI ve şube kullanımını genişleterek şu yönetimleri tamamla:
- ALA şubelerinin burslulukta kullanımı,
- öğrencinin öğrenim gördüğü okulların eklenmesi, düzenlenmesi ve pasifleştirilmesi,
- öğrencinin mevcut sınıf/durum seçeneklerinin yönetimi,
- bunlardan ayrı admin tanımlı sınav gruplarının yönetimi,
- sınav türü/başlığı ve dönem + şube + grup + tarih + başlangıç/bitiş saatiyle oturum oluşturma,
- her oturum için bağımsız maksimum kontenjan, askıya alma ve arşivleme.

Yeni bir merkezi Branch CMS, UI framework veya paralel yönetim sistemi oluşturma. Tanım değişikliklerinde mevcut başvuru ilişkilerini koru; öğrencinin sınıfına göre otomatik sınav grubu kısıtlaması ekleme.

Doluluk örneğin `18 / 20` olarak görülsün. Ayrı toplam kontenjan girilmesin; toplamlar ilgili oturumların kapasitelerinden hesaplansın. Oturumun dolması yalnız o oturuma yeni başvuruyu kapatsın; dolu, askıda ve arşivde durumları ayrı gösterilsin.

Ortak domain kurallarıyla kontenjanın mevcut doluluğun altına indirilmesini engelle. Başvurusu bulunan oturum silinemesin, arşivlenebilsin ve ilişkili kayıtlar korunsun. Dönem sonunda kalıcı oturum silme işlemini ilk sürümde ekleme.

Dur.

### 2D — Başvuru yönetimi
Admin listeleme, arama, filtreleme, detay, tekli/toplu onay, kalıcı silme ve oturum değiştirme işlemlerini yapabilsin. Başvuru numarası ve hesaptaki iletişim bilgilerini görüntüleyebilsin.

Red veya iptal durumu oluşturma. Kalıcı silmede mevcut ALA onay modalını kullan ve kontenjanı serbest bırak; aynı hesabın uygun koşullarda yeniden başvurabilmesi korunsun.

Admin onaylı başvuruyu değiştirebilsin. Ortak duplicate/kapasite kurallarını kullan; dolu oturuma aktarım için önce kontenjanın artırılması gereksin. Yayın açıkken değişiklik üyeye hemen yansısın; ilgili başvuru iletişimi `ulaşılmadı` olsun ve otomatik mesaj gönderilmesin.

Başvuru/sonuç değişiklik tarihçesi ve değiştiren kişi kaydı ekleme.

Dur.

### 2E — Katılım
Hızlı katılım ekranı `işaretlenmedi`, `katıldı`, `katılmadı` durumlarını desteklesin.

Şube, tarih, saat, sınav grubu ve öğrencinin mevcut sınıf/durumu ayrı filtrelenebilsin. Katılmama için belirlenen `0/0` davranışını ve ilgili sonuç iletişimi sıfırlamasını ortak domain kurallarıyla uygula; işaretlenmemiş katılımı otomatik `0/0` sayma.

Not/burs yönetim ekranlarına geçme. Dur.

### 2F — Not
Liste üzerinden hızlı not girişi uygula:
- 0–100 arasında tam sayı validation,
- `NULL` ile gerçek `0` ayrımı,
- nota göre sıralama ve filtreler,
- yayınlanmış not değişikliğinin hemen görünmesi ve sonuç iletişiminin `ulaşılmadı` olması.

Ortak yayın kuralları korunsun; eksik sonuç yayında kalmasın. İlk sürümde Excel import/export ekleme.

Dur.

### 2G — Burs oranı
Burs seçim ekranı `%100, %90, …, %10, %0 / Burs Yok` değerlerini tek seçimle desteklesin. `Belirlenmedi` ayrı kalsın; nota göre sıralama ve filtreler bulunsun.

Burs ilgili dönem başvurusuna ait olsun; sonraki dönem önceki sonucu değiştirmesin. Yayınlanmış burs değişikliği hemen görünsün ve sonuç iletişimi `ulaşılmadı` olsun. Ortak eksik sonuç/yayın kuralları korunsun.

Mevcut ALA radio/form/table stilini kullan. Dur.

### 2H — Yayınlama
Her başvuru için iki bağımsız yayın anahtarı uygula:
- Başvuru sonucunu yayınla.
- Sınav/burs sonucunu yayınla.

Admin bunları kayıt bazında veya toplu açabilsin. Toplu işlem aynı kayıt anahtarlarını değiştirsin; ayrı bir dönem yayını mantığı oluşturma. Başvuru onayı ve başvuru yayını ayrı kalsın.

Not veya burs boşken sonuç yayın anahtarı tekli/toplu hiçbir yoldan açılamasın. Toplu işlemde eksik kayıtlar için admini bilgilendir ve bu kayıtların yayını kapalı kalsın. `0` not ve `%0` burs eksik sonuç sayılmasın.

Yayınlanmış veride sonraki düzeltme hemen görünsün; yeniden yayın zorunlu olmasın. Yayın işlemi otomatik bildirim göndermesin. Toplu kritik işlemde mevcut ALA onay modalını kullan.

Yayınlanmamış kabul kararı, not ve bursun görünürlüğünü ortak sunum kuralları üzerinden doğrula. Gerçek üye HTML/Livewire/API çıktıları ilgili 3.x adımlarında kontrol edilir. Dur.

### 2I — İletişim/bildirim
Başvuru sonucu iletişimi ve sınav/burs sonucu iletişimini ayrı yönet:
- Her aşamada varsayılan `ulaşılmadı` ve adminin elle seçebildiği `ulaşıldı` durumu.
- Hesabın kayıtlı e-postası/telefonuna adminin tekli veya toplu butonla başlattığı gerçek e-posta/WhatsApp gönderimi.
- Kanal ve aşama bazında ayrı teknik gönderim durumu: bekliyor, gönderildi, gönderilemedi.

Admin telefonla veya sistem dışından iletişim kurarak `ulaşıldı` işaretleyebilsin; ilgili aşamada bu kayıt için ayrıca e-posta/WhatsApp gönderilmesi gerekmesin. Teknik `gönderildi` bilgisiyle `ulaşıldı` durumunu tek alanda birleştirme.

İlgili başvuru bilgisi değişince başvuru iletişimi; katılım/not/burs sonucu değişince sonuç iletişimi tekrar `ulaşılmadı` olsun. Ulaşılma durumunun kendisini düzenlemek bu sıfırlamayı tetiklemesin. Onay, yayın ve veri değişiklikleri otomatik mesaj göndermesin; gönderimi admin başlatsın.

Mevcut altyapıyı incele ve sağlayıcıyı bu adımda kullanıcıyla belirle. Yeniden denemelerde kontrolsüz mükerrer gönderimi önle. Testlerde gerçek kişilere mesaj gönderme.

Dur.

### 2J — Excel import/export — sonraki sürüm
Bu adım ilk sürüm kapsamı dışındadır ve ilk sürüm uygulama sırasında atlanır. İçe/dışa aktarma ekranı, dosya işleme akışı veya paket ekleme.

Veri modeli benzersiz başvuru numaraları ve açık ilişkilerle ileride aktarımı desteklemelidir. Kullanıcı bu adımı ileride ayrıca başlatırsa başvuru/sonuç `.xlsx` export ve not import kapsamını, kolonları ve mevcut altyapıyı o zaman netleştir.

İlerideki import; dosya/kolon doğrulama, başvuru eşleştirme, duplicate kontrolü, satır bazlı hata ve güvenli ön izleme/onay içermeli; hatalı dosya kontrolsüz veri değiştirmemelidir.

Bu adımın ilk sürümde uygulanmaması sonraki ilk sürüm adımlarını engellemez.

### 2K — Admin uçtan uca kontrol
Admin ekranlarının ortak iş kurallarıyla bağlantısını uçtan uca doğrula. 1E ve 2.x adımlarında geçen testlerin bütün varyasyonlarını tekrarlamadan şu bağlantılardaki eksikleri tamamla:
- dönem ve oturum ayarlarının başvuru yönetimine yansıması,
- liste/detay üzerinden onay, aktarım ve silmenin ortak kapasite ve veri koruma kurallarına uyması,
- tekli/toplu yayın ve bildirim işlemlerinde kayıt kapsamı, eksik sonuç engeli ve ayrı iletişim aşamalarının korunması,
- admin erişim yetkileri ve kritik işlemlerin mevcut ALA onay modalından geçmesi.

Excel ilk sürüm kontrolünün parçası değildir. Yeni özellik ekleme. Raporla ve dur.

---

## 3. Frontend — Başvurularım

### 3A — Mevcut üye alanını analiz et
Kod değiştirme.

Üye layout/navigation, mevcut profil/iletişim güncelleme akışı, auth middleware, Blade/Livewire yaklaşımı, card/list/form UI ve responsive davranışı incele.

Tek öğrenci hesabıyla dönem başvurularını yönetecek `Başvurularım` alanının nereye/nasıl ekleneceğini öner. Çok öğrencili hesap veya ikinci üyelik akışı tasarlama. Dur.

### 3B — Başvuruya açık sınavlar
Login olmuş üyeye başvuruya açık bursluluk dönemlerini/sınavlarını mevcut üye tasarımında göster.

Başvuru tarihleri dışında veya admin tarafından başvuruları kapatılmış dönemde yeni başvuru başlatılamasın. Dolu, askıda ve arşivlenmiş oturumlardan başvuru başlatılamasın; askı durumunda yöneticiyle iletişim uyarısı gösterilsin.

Form aşamasına geçme. Dur.

### 3C — Başvuru formu
Form şu bilgileri ayrı alanlarla desteklesin:
- ilgili hesabın öğrencisi,
- öğrencinin öğrenim gördüğü okul,
- öğrencinin mevcut sınıf/durumu,
- ALA şubesi,
- başvurulan sınav grubu ve sınav,
- uygun tarih, başlangıç/bitiş saati ve oturum,
- oturum kontenjanı,
- hesabın kayıtlı telefonu/e-postası, iletişim uyarısı ve mevcut profil güncelleme bağlantısı.

Aynı hesabın ikinci öğrenci kaydını oluşturma veya başvuruya ayrı iletişim bilgisi isteme. Mevcut sınıf/durum ile sınav grubunu birbirine bağlayan otomatik uygunluk engeli ekleme; seçim başvuranın sorumluluğundadır.

Dolu, askıda veya arşivlenmiş oturum seçilemesin. Mevcut ALA form stilini kullan. Dur.

### 3D — Başvuru iş kurallarını üye akışına bağla
1C'de kurulan ortak domain kurallarını kullan; aynı kuralları ikinci kez yazma.

Server-side doğrula:
- login, sahiplik ve hesabın öğrenci ilişkisi,
- hesap/dönem başına tek mevcut başvuru,
- başvuru tarih aralığı ve dönemin başvurulara açık olması,
- oturumun başvuruya uygun, askıda/arşivde olmaması,
- seçilen oturumun bağımsız kontenjanı ve eşzamanlı kapasite koruması.

Farklı sınav, grup veya şube seçmek aynı dönemde ikinci mevcut başvuru hakkı sağlamasın. Başarıda benzersiz başvuru numarasını göster; onay beklerken de kontenjan ayrılsın.

Dur.

### 3E — Başvurularım listesi
Üye `Başvurularım` ekranında en az başvuru no, öğrenci, dönem/sınav, şube, sınav grubu, tarih/başlangıç-bitiş saati, kullanıcıya açık başvuru durumu ve sonuç yayın durumunu görsün.

Kullanıcı yalnız kendi kayıtlarını ve önceki dönem başvurularını görebilsin. Başvuru onayı ile yayınını ayır; yayınlanmamış kabul kararı, not ve bursu gösterme.

Dur.

### 3F — Başvuru detayı
Duruma göre şunları göster:
- başvurunun değerlendirildiği veya başvuru sonucunun henüz yayınlanmadığı bilgisi,
- yayınlanmış onay ve sınav bilgileri; en az 30 dakika önce hazır bulunma uyarısı,
- öğrencinin okulu/mevcut sınıfı ile şube/sınav grubunun ayrı bilgileri,
- oturum askısı, arşivi ve kapanmış başvuru döneminde işlem yapılamadığı bilgisi,
- sonuç henüz yayınlanmadı veya yayınlanmış sonuç durumu.

Red/iptal durumu veya admin-only operasyon alanları gösterme. Onaylı başvuruda düzenleme/silme sunma. Dur.

### 3G — Başvuru değişikliği ve kalıcı silme
Kullanıcı yalnız başvurular açıkken, başvurusu henüz onaylanmamışken ve mevcut oturumu askıda/arşivde değilken başvurusunu düzenleyebilsin veya kalıcı silebilsin.

Ortak kurallarla hedef oturumun uygunluğunu, duplicate ve kapasiteyi yeniden doğrula. Farklı şube, grup, sınıf/durum veya sınav seçimine otomatik eğitim seviyesi kısıtı ekleme. İşlem başarısızsa mevcut başvuru ve yeri korunsun.

Silmede ilgili oturumun kontenjanı açılsın. Aynı dönemde uygun başka bir seçimle yeniden başvuru yapılabilsin; kalıcı bir başvuru hakkı tüketme kaydı oluşturma.

Onaylanan, dönemi başvurulara kapanmış veya oturumu askıya/arşive alınmış başvuruda hem düzenleme hem silmeyi server-side engelle. Kritik işlemde mevcut ALA onay modalını kullan.

Dur.

### 3H — Not ve burs sonucu
Sonuç yayını açıldıktan sonra ilgili dönem başvurusunun notunu ve burs oranını göster. Yayınlanmadan önce veri sızdırma.

`%0 / Burs Yok` ile belirlenmemiş/yayınlanmamış sonucu ayır; katılmadı işaretlenen öğrencinin `0/0` sonucunda katılım bilgisini koru. Not veya burs boşken sonuç yayınlanamasın.

Yayınlanmış not/burs admin tarafından değiştirildiğinde yeniden yayın beklemeden güncel değerler görünsün. Önceki dönem sonuçları yeni dönem başvurusundan etkilenmesin.

Dur.

### 3I — Responsive/kullanılabilirlik
Üye ekranlarında henüz doğrulanmamış veya değişmiş yerleşim ve etkileşimleri 10. bölümdeki cihaz kapsamına göre kontrol et. Önceki adımlarda doğrulanan ve etkilenmeyen görünümleri tekrar kontrol etme.

Formlar, okul/sınıf/grup ayrımı, oturum seçimleri, listeler/kartlar, badge'ler, hata/başarı mesajları, başvuru detayı, askı/arşiv uyarıları ve işlem yapılamayan durumlar mevcut ALA tasarımına uygun olsun.

Yeni paralel tasarım oluşturma. Dur.

### 3J — Kullanıcı akışı uçtan uca test
Üye ekranlarının ortak kuralları doğru çağırdığını ve gerçek çıktıları doğru sunduğunu uçtan uca doğrula. Önceki domain testlerini tarayıcıda yeniden üretmeden şu akışlardaki eksik kontrolleri tamamla:
- mevcut hesapla başvuru oluşturma, Başvurularım üzerinden takip, izin verilen düzenleme/silme ve yeniden başvuru,
- duplicate, dolu/askıda/arşivli oturum, dönem kapanışı ve onay kilidi gibi engellerin üye isteklerinde uygulanması; başarısız işlemde mevcut başvuru ve yerin korunması,
- ziyaretçi ve başka hesap erişiminin liste, detay ve yazma isteklerinde engellenmesi,
- onay/yayın ayrımı ile yayınlanmamış kabul kararı, not ve bursun gerçek HTML/Livewire/API çıktılarında korunması; yayın sonrası güncellemenin ve geçmiş dönem sonuçlarının doğru görünmesi.

Yeni özellik ekleme. Raporla ve dur.

---

# 13. Uygulama sırası

İlk sürüm için varsayılan sıra:

`1A → 1B → 1C → 1D → 1E → 2A → 2B → 2C → 2D → 2E → 2F → 2G → 2H → 2I → 2K → 3A → 3B → 3C → 3D → 3E → 3F → 3G → 3H → 3I → 3J`

`2J — Excel import/export` sonraki sürüme ertelenmiştir. İlk sürümde uygulanmaz; kullanıcı ileride ayrıca istediğinde başlatılır. Mevcut adım kimlikleri korunur.

Kullanıcı açıkça değiştirmedikçe bu sıra korunur. Her adım ayrı kullanıcı talebiyle başlatılır. Bir adım tamamlanınca bir sonraki adıma otomatik geçilmez.
