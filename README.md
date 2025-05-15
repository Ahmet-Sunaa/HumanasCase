# Humanas Kullanıcı Oturum Tahmin Sistemi

---

## Proje Hakkında

Bu proje, kullanıcıların sistemdeki oturum açma davranışlarını analiz ederek gelecekteki oturum açma zamanlarını tahmin etmeyi amaçlar.  
Tahminler için çeşitli algoritmalar (ortalama aralık, günlük/haftalık paternler, lineer regresyon) kullanılır. React tabanlı frontend ile veriler görselleştirilir ve PHP tabanlı backend API ile veriler sağlanır.

---

## İçindekiler

- [Teknolojiler](#teknolojiler)
- [Özellikler](#özellikler)
- [Kurulum](#kurulum)
- [Docker ile Çalıştırma](#docker-ile-çalıştırma)
- [API](#api)
- [React Frontend](#react-frontend)
- [Deploy Notları](#deploy-notları)
- [Lisans](#lisans)
- [İletişim](#iletişim)

---

## Teknolojiler

- **Backend:** PHP 8.2, Apache  
- **Frontend:** React.js  
- **Sunucu:** Docker container (opsiyonel)  
- **Veri Kaynağı:** Harici API (http://case-test-api.humanas.io)  

---

## Özellikler

- Kullanıcıların son oturum açma zamanını gösterir  
- Ortalama oturum açma aralığı tahmini  
- Günlük ve haftalık oturum açma paternlerini tahmin eder  
- Lineer regresyonla ileriye dönük tahmin yapar  
- Haftalık ortalama oturum sayısını hesaplar  
- Kullanıcı dostu, duyarlı ve karanlık mod destekli React arayüzü  
- API üzerinden dinamik veri çekme  
- Arama ve sıralama fonksiyonları  
- Responsive tasarım ile mobil uyumlu  

---

## Kurulum

1. Projeyi klonlayın:

    ```bash
    git clone https://github.com/Ahmet-Sunaa/HumanasCase.git
    cd HumanasCase/humanas-backend
    ```

2. PHP ve Apache çalıştırın veya Docker ile deploy edin (aşağıya bakınız).

3. React frontend dosyaları `build/` klasöründe bulunmaktadır ve backend köküne yerleştirilmiştir.

4. Gerekirse `index.php` dosyasının aşağıdaki gibi olduğundan emin olun:

    ```php
    <?php
    readfile(__DIR__ . '/index.html');
    ```

---

## Docker ile Çalıştırma

Docker yüklüyse, projenin backend kök dizininde aşağıdaki komutları kullanabilirsiniz:

```bash
docker build -t humanas-app .
docker run -p 8080:80 humanas-app
```

Ardından tarayıcınızda [http://localhost:8080](http://localhost:8080) adresine gidin.

---

## API

Backend PHP dosyaları `get_logins.php` üzerinden dış API’den veri çeker, analiz eder ve JSON olarak frontend’e sunar.

---

## React Frontend

- `build/` klasörü React’in üretim build çıktısını içerir.  
- Responsive ve karanlık mod desteklidir.  
- Kullanıcı adı filtreleme, tablo sıralama ve güven skorları gösterir.

---

## Deploy Notları

- Proje Render veya benzeri platformlara PHP ortamı seçilerek deploy edilmelidir.  
- `composer.json` dosyasının kök dizinde olması gerekir (içerik `{}` olabilir).  
- `.render.yaml` dosyasıyla servis ayarları yapılandırılabilir.  
- React build dosyaları ve PHP dosyaları aynı dizinde olmalıdır.  
- `.htaccess` dosyası React Router için uygun rewrite kuralları içermelidir.

---

## Lisans

Bu proje MIT Lisansı ile lisanslanmıştır. Detaylar için LICENSE dosyasına bakınız.

---

## İletişim

Proje hakkında soru veya önerileriniz için:

- GitHub: [https://github.com/Ahmet-Sunaa](https://github.com/Ahmet-Sunaa)
