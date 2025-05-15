<?php
// CORS izinleri: Herhangi bir domain'den istek yapılabilir
header("Access-Control-Allow-Origin: *");

// Dönen verinin JSON formatında olduğunu belirt
header("Content-Type: application/json");

// Algoritma fonksiyonlarının dahil edilmesi
require_once 'algorithms/average_interval.php';
require_once 'algorithms/daily_pattern.php';
require_once 'algorithms/weekly_pattern.php';
require_once 'algorithms/weekly_login_average.php'; 
require_once 'algorithms/lineer_regression.php';   
// API'den kullanıcı verilerini çek
$url = 'http://case-test-api.humanas.io';
$response = file_get_contents($url);

// Eğer API'den veri alınamadıysa hata mesajı dön
if ($response === FALSE) {
    echo json_encode(['error' => 'API veri alınamadı']);
    exit;
}

// JSON formatındaki veriyi PHP dizisine dönüştür
$responseData = json_decode($response, true);

// Beklenen yapıdaki veriler yoksa hata dön
if (!isset($responseData['data']['rows']) || !is_array($responseData['data']['rows'])) {
    echo json_encode(['error' => 'Veri yapısı beklenenden farklı']);
    exit;
}

// Kullanıcı verileri
$users = $responseData['data']['rows'];

// Sonuçları tutacak dizi
$result = [];

// Her kullanıcı için tahminler yapılacak
foreach ($users as $user) {
    // Eğer kullanıcı adı veya login verisi eksik veya yetersizse atla
    if (!isset($user['name'], $user['logins']) || !is_array($user['logins']) || count($user['logins']) < 2) {
        continue;
    }
    // echo "<pre>";   
    // print_r($user);
    // echo "</pre>";
    // 1. Algoritma: Ortalama login aralığına göre tahmin
    $p1 = predictNextLoginAverageInterval($user['logins']);
    
    // 2. Algoritma: Günlük saat paternine göre tahmin
    $p2 = predictNextLoginDailyPattern($user['logins']);
    
    // 3. Algoritma: Haftalık gün paternine göre tahmin
    $p3 = predictNextLoginWeeklyPattern($user['logins']);

    // 4. Algoritma: Lineer regresyon ile tahmin
    $p4 = predictNextLoginLinearRegression($user['logins']);

    // 5. Algoritma: Ortalama haftalık giriş sayısı (daha farklı; sayı olarak döner)
    $p5 = predictAverageWeeklyLogins($user['logins']);
    
    // Son login zamanını al
    $lastLogin = end($user['logins']);

    // Sonuç dizisine kullanıcı adı, son login, 3 tahmin ve güven skorları, 
    // ayrıca ortalama haftalık giriş sayısı eklenir
    $result[] = [
        'name' => $user['name'],
        'lastLogin' => $lastLogin,
        'prediction1' => $p1['prediction'],
        'confidence1' => $p1['confidence'],
        'prediction2' => $p2['prediction'],
        'confidence2' => $p2['confidence'],
        'prediction3' => $p3['prediction'],
        'confidence3' => $p3['confidence'],
        'prediction4' => $p4['prediction'],
        'confidence4' => $p4['confidence'],
        'prediction5' => $p5,
    ];
}

// Sonuçları JSON formatında çıktı ver
echo json_encode($result);
