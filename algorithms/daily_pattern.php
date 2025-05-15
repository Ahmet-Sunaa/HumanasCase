<?php
/**
 * Bu algoritma, kullanıcı login saatlerini analiz eder
 * ve en sık login olduğu saati tahmin eder.
 *
 * @param array $logins ISO 8601 formatında login zamanları
 * @return string Tahmin edilen bir sonraki login zamanı (ISO 8601 formatında)
 */function predictNextLoginDailyPattern(array $logins): array
{
    // Eğer login sayısı 2'den azsa tahmin yapılamaz
    if (count($logins) < 2) {
        return ['prediction' => "Yetersiz veri", 'confidence' => 0];
    }

    // 0'dan 23'e kadar (24 saat) olan bir dizi oluştur ve her saat için login sayısını 0 yap
    $hourCounts = array_fill(0, 24, 0);

    // Her login zamanının saat bilgisini al ve ilgili saat indeksindeki sayacı artır
    foreach ($logins as $login) {
        $dt = new DateTime($login);
        $hour = (int) $dt->format('G'); // Saat (0-23)
        $hourCounts[$hour]++; // O saatte kaç login olmuş?
    }

    // En çok login yapılan saati bul
    $mostFrequentHour = array_search(max($hourCounts), $hourCounts);

    // Güven skoru: en sık login yapılan saatin toplam loginlere oranı (yüzde olarak)
    $confidence = max($hourCounts) / count($logins) * 100;

    // Son login zamanını al
    $lastLogin = new DateTime(end($logins));

    // Tahmin: Son login tarihinden 1 gün sonraya geç, saat olarak en çok login yapılan saati ayarla
    $predicted = clone $lastLogin;
    $predicted->modify('+1 day');
    $predicted->setTime($mostFrequentHour, 0, 0);

    // Tahmini tarih ve güven skorunu döndür
    return [
        'prediction' => $predicted->format(DateTime::ATOM), // ISO 8601 formatı
        'confidence' => round($confidence, 1) // Yüzde olarak güven puanı
    ];
}

?>
