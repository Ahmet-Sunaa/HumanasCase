<?php
/**
 * Bu fonksiyon, kullanıcının login zamanları arasındaki ortalama farkı bulur
 * ve bu farkı son login zamanına ekleyerek bir sonraki login tahminini yapar.
 *
 * @param array $logins ISO 8601 formatında login zamanları
 * @return string Tahmin edilen bir sonraki login zamanı (ISO 8601 formatında)
 */
function predictNextLoginAverageInterval(array $logins): array
{
    // Eğer login sayısı 2'den azsa tahmin yapılamaz
    if (count($logins) < 2) {
        return ['prediction' => "Yetersiz veri", 'confidence' => 0];
    }

    // Login zamanlarını Unix timestamp (saniye) olarak dönüştür
    $timestamps = array_map(fn($time) => (new DateTime($time))->getTimestamp(), $logins);

    $diffs = [];

    // İki ardışık login arasındaki zaman farklarını hesapla (saniye cinsinden)
    for ($i = 1; $i < count($timestamps); $i++) {
        $diffs[] = $timestamps[$i] - $timestamps[$i - 1];
    }

    // Login aralıklarının ortalamasını hesapla
    $avg = array_sum($diffs) / count($diffs);

    // Login aralıklarının standart sapmasını hesapla (sapma ne kadar küçükse güven yüksek)
    $stdDev = sqrt(array_sum(array_map(fn($d) => pow($d - $avg, 2), $diffs)) / count($diffs));

    // Güven puanını hesapla: Sapmanın ortalamaya oranı ne kadar azsa güven o kadar yüksek (maks 100)
    $confidence = max(0, min(100, 100 - ($stdDev / $avg * 100)));

    // Ortalama aralığı tam sayıya yuvarla (DateTime::modify ondalık saniyeyi kabul etmez)
    $avg = round($avg);

    // Son login zamanına ortalama aralığı ekleyerek bir sonraki login zamanını tahmin et
    $predicted = (new DateTime(end($logins)))->modify("+$avg seconds");

    // Tahmini tarih ve güven puanını döndür
    return [
        'prediction' => $predicted->format(DateTime::ATOM), // ISO 8601 formatında tarih
        'confidence' => round($confidence, 1) // Yüzde olarak güven puanı
    ];
}

?>
