<?php

function predictNextLoginLinearRegression(array $logins): array
{
    // Kullanıcının login zamanları yetersizse tahmin yapılamaz
    if (count($logins) < 2) {
        return ['prediction' => "Yetersiz veri", 'confidence' => 0];
    }

    // Login zamanlarını UNIX timestamp (saniye cinsinden) biçimine çevir
    $timestamps = array_map(fn($t) => (new DateTime($t))->getTimestamp(), $logins);

    $n = count($timestamps);          // Veri sayısı (login sayısı)
    $x = range(0, $n - 1);            // X değerleri: login sıralaması (0,1,2,...)
    $y = $timestamps;                 // Y değerleri: login zamanları (timestamp)

    // X ve Y'nin toplamlarını hesapla
    $sumX = array_sum($x);
    $sumY = array_sum($y);
    $sumXY = 0;                      // X*Y toplamı için başlangıç
    $sumX2 = 0;                      // X^2 toplamı için başlangıç

    // X, Y ve karelerinin toplamlarını döngü ile hesapla
    for ($i = 0; $i < $n; $i++) {
        $sumXY += $x[$i] * $y[$i];   // X * Y toplamı
        $sumX2 += $x[$i] * $x[$i];   // X^2 toplamı
    }

    // Doğrusal regresyonun eğim katsayısı 'a' ve kesişim noktası 'b' hesapla
    // Formül: y = a * x + b
    $a = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - pow($sumX, 2));
    $b = ($sumY - $a * $sumX) / $n;

    // Bir sonraki login sırası için (x = n) tahmini timestamp hesapla
    $predictedTimestamp = $a * $n + $b;

    // Modelin güvenilirliğini ölçmek için tahmin edilen y değerlerini hesapla
    $predictedY = array_map(fn($xi) => $a * $xi + $b, $x);

    // Tahminlerin ortalaması
    $avgY = array_sum($predictedY) / count($predictedY);

    // Tahminlerin standart sapmasını hesapla
    $stdDev = sqrt(array_sum(array_map(fn($yhat) => pow($yhat - $avgY, 2), $predictedY)) / count($predictedY));

    // Tahminlerin maksimum ve minimum aralığı (değişkenlik)
    $range = max($predictedY) - min($predictedY) ?: 1; // Bölme hatası olmaması için 0 ise 1 yap

    // Güven skoru: %100 - (standart sapma / aralık) * 100 ile normalize edilir
    // Sapma ne kadar küçükse güven o kadar yüksek olur
    $confidence = max(0, min(100, 100 - ($stdDev / $range * 100)));

    // Tahmini timestamp'i ISO 8601 tarih formatına çevir ve güvenle birlikte döndür
    return [
        'prediction' => (new DateTime("@".round($predictedTimestamp)))->format(DateTime::ATOM),
        'confidence' => round($confidence, 1)
    ];
}

?>
