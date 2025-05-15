<?php
/**
 * Bu algoritma, kullanıcıların login oldukları haftanın günlerini analiz eder
 * ve en sık login olduğu günü tahmin eder.
 *
 * @param array $logins ISO 8601 formatında login zamanları
 * @return string Tahmin edilen bir sonraki login zamanı (ISO 8601 formatında)
 */
function predictNextLoginWeeklyPattern(array $logins): array
{
    // Eğer login sayısı 2'den azsa tahmin yapılamaz
    if (count($logins) < 2) {
        return ['prediction' => "Yetersiz veri", 'confidence' => 0];
    }

    // Haftanın 7 günü için login sayacı oluştur (0 = Pazar, 6 = Cumartesi)
    $dayCounts = array_fill(0, 7, 0);

    // Her login zamanının haftanın hangi günü olduğunu tespit edip ilgili sayacı artır
    foreach ($logins as $login) {
        $dt = new DateTime($login);
        $dayOfWeek = (int) $dt->format('w'); // 0–6 (0=Pazar)
        $dayCounts[$dayOfWeek]++;
    }

    // En çok login yapılan günü bul
    $mostFrequentDay = array_search(max($dayCounts), $dayCounts);

    // Güven skoru: en sık login yapılan günün toplam loginlere oranı (yüzde)
    $confidence = max($dayCounts) / count($logins) * 100;

    // Son login zamanını al
    $lastLogin = new DateTime(end($logins));

    // Son login gününün haftanın kaçıncı günü olduğunu al
    $currentDay = (int) $lastLogin->format('w');

    // Tahmin için, en çok login yapılan güne kaç gün eklememiz gerektiğini hesapla
    $daysToAdd = ($mostFrequentDay - $currentDay + 7) % 7;

    // Eğer aynı günse, tahmini bir hafta (7 gün) sonrasına ayarla
    $daysToAdd = $daysToAdd === 0 ? 7 : $daysToAdd;

    // Son login tarihinden tahmini günü oluştur
    $predicted = clone $lastLogin;
    $predicted->modify("+$daysToAdd days");

    // Tahmin saatini sabah 08:00 olarak ayarla
    $predicted->setTime(8, 0, 0);

    // Tahmini tarih ve güven skorunu döndür
    return [
        'prediction' => $predicted->format(DateTime::ATOM), // ISO 8601 formatı
        'confidence' => round($confidence, 1) // Yüzde olarak güven puanı
    ];
}

?>
