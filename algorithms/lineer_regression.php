<?php

function predictNextLoginLinearRegression(array $logins): array
{
    if (count($logins) < 2) {
        return ['prediction' => "Yetersiz veri", 'confidence' => 0];
    }

    // Timestamp'e çevir
    $timestamps = array_map(fn($t) => (new DateTime($t))->getTimestamp(), $logins);

    $n = count($timestamps);
    $x = range(0, $n - 1);
    $y = $timestamps;

    $sumX = array_sum($x);
    $sumY = array_sum($y);
    $sumXY = 0;
    $sumX2 = 0;

    for ($i = 0; $i < $n; $i++) {
        $sumXY += $x[$i] * $y[$i];
        $sumX2 += $x[$i] * $x[$i];
    }

    $a = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - pow($sumX, 2));
    $b = ($sumY - $a * $sumX) / $n;

    // Tahmini timestamp hesapla (x = n için)
    $predictedTimestamp = $a * $n + $b;

    // Güven hesabı: tahmin edilen değerlerin standart sapması
    $predictedY = array_map(fn($xi) => $a * $xi + $b, $x);
    $avgY = array_sum($predictedY) / count($predictedY);
    $stdDev = sqrt(array_sum(array_map(fn($yhat) => pow($yhat - $avgY, 2), $predictedY)) / count($predictedY));
    $range = max($predictedY) - min($predictedY) ?: 1; // 0 bölmeye karşı önlem

    // Güven = %100 - (sapma / değişkenlik aralığı) * 100
    $confidence = max(0, min(100, 100 - ($stdDev / $range * 100)));

    // Sonuç olarak tarih ve güven döndür
    return [
        'prediction' => (new DateTime("@".round($predictedTimestamp)))->format(DateTime::ATOM),
        'confidence' => round($confidence, 1)
    ];
}



?>