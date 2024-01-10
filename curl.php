<?php
// Ініціалізація сесії cURL
$ch = curl_init();

// Опції для авторизаційного запиту
// Замініть 'your_username' та 'your_password' на ваші дані для входу
curl_setopt($ch, CURLOPT_URL, 'https://valiza.david-freedman.com.ua/admin/index.php?route=common/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "username=fb392521_valiza&password=fb392521_valiza");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

// Виконання авторизаційного запиту
$output = curl_exec($ch);

// Виведення відповіді для відлагодження
echo "Авторизація: $output\n";

// Перевірка на наявність токену в відповіді
preg_match('/user_token=([a-zA-Z0-9]+)/', $output, $matches);

// Отримання токену
$user_token = $matches[1] ?? '';

// Виведення отриманого токену
echo "Отриманий токен: $user_token\n";

// Якщо токен отримано, виконання наступного запиту
if ($user_token) {
    // Параметри для запиту виконання скрипту
    curl_setopt($ch, CURLOPT_URL, 'https://valiza.david-freedman.com.ua/admin/index.php?route=extension/module/ms_integration/importProducts&user_token=' . $user_token);
    curl_setopt($ch, CURLOPT_HTTPGET, 1);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Виконання запиту
    $output = curl_exec($ch);

    // Виведення відповіді для відлагодження
    echo "Виконання скрипту: $output\n";

    // Перевірка на наявність помилок cURL
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
    }
}

// Закриття сесії cURL
curl_close($ch);
?>
