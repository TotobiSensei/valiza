<?php
// HTTP
define('HTTP_SERVER', "https://develop.valliza-book.com.ua/");

// HTTPS
define('HTTPS_SERVER', "https://develop.valliza-book.com.ua/");


// DIR
define('DIR_APPLICATION',  '/home/valiza4/valliza-book.com.ua/develop/catalog/');
define('DIR_SYSTEM',  '/home/valiza4/valliza-book.com.ua/develop/system/');
define('DIR_IMAGE',  '/home/valiza4/valliza-book.com.ua/develop/image/');
define('DIR_STORAGE', '/home/valiza4/valliza-book.com.ua/storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'valiza4.mysql.tools');
define('DB_USERNAME', 'valiza4_book');
define('DB_PASSWORD', 'xJv~S%9s27');
define('DB_DATABASE', 'valiza4_book');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// ==================HELPERS=======================
function dump($var) {
    echo '<div style="background-color: #f3f4f6; border-left: 5px solid #9ca3af; padding: 20px; margin: 20px 0; font-family: Consolas, Monaco, monospace; font-size: 14px;">';
    echo '<pre style="margin: 0; padding: 10px; background-color: #1e293b; color: #e5e7eb; border-radius: 5px;">';
    var_dump($var);
    echo '</pre>';
    echo '</div>';
}

function dd($var) {
    echo '<div style="background-color: #f3f4f6; border-left: 5px solid #9ca3af; padding: 20px; margin: 20px 0; font-family: Consolas, Monaco, monospace; font-size: 14px;">';
    echo '<pre style="margin: 0; padding: 10px; background-color: #1e293b; color: #e5e7eb; border-radius: 5px;">';
    var_dump($var);
    echo '</pre>';
    echo '</div>';
    die();
}

function remove_by_key(&$array, $key) {
    if (array_key_exists($key, $array)) {
        unset($array[$key]);
    }
}