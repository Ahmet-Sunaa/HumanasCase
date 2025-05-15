<?php
function predictAverageWeeklyLogins(array $logins): float {
    if (empty($logins)) return 0;

    $timestamps = array_map(fn($time) => (new DateTime($time))->getTimestamp(), $logins);
    sort($timestamps);

    // İlk ve son login zamanı
    $firstLogin = new DateTime("@".$timestamps[0]);
    $lastLogin = new DateTime("@".$timestamps[count($timestamps) - 1]);

    // İki tarih arasındaki hafta sayısı (en az 1 hafta olsun)
    $interval = $firstLogin->diff($lastLogin);
    $weeks = max(1, floor($interval->days / 7));

    // Ortalama haftalık giriş sayısı
    $average = count($logins) / $weeks;

    return round($average, 2); // Virgülden sonra 2 basamak
}
