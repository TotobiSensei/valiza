<?php 
// HTTP a
define('HTTP_SERVER', "https://" . $_SERVER["HTTP_HOST"] . '/admin/');
define('HTTP_CATALOG', "https://" . $_SERVER["HTTP_HOST"] . '/');

// HTTPS
define('HTTPS_SERVER', "https://" . $_SERVER["HTTP_HOST"] . '/admin/');
define('HTTPS_CATALOG', "https://" . $_SERVER["HTTP_HOST"] . '/');
// DIR
define('DIR_APPLICATION',  $_SERVER["DOCUMENT_ROOT"].'admin/');
define('DIR_SYSTEM',  $_SERVER["DOCUMENT_ROOT"].'system/');
define('DIR_IMAGE',  $_SERVER["DOCUMENT_ROOT"].'image/');
define('DIR_STORAGE', '/home/valiza4/valliza-book.com.ua/storage/');
define('DIR_CATALOG',  $_SERVER["DOCUMENT_ROOT"].'catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
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

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');

date_default_timezone_set('Europe/Kiev');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);