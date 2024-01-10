<?php
$prefixes = [
    'PhpOffice\\PhpSpreadsheet\\' => __DIR__ . '/',
    'Psr\\SimpleCache\\' => __DIR__ . '/../simple-cache/src/',
    'ZipStream\\' => __DIR__ . '/../zipstream-php/src/',
    'MyCLabs\\Enum\\' => __DIR__ . '/../myclabs/php-enum/src/Enum/', // Оновлено
];

spl_autoload_register(function ($class) use ($prefixes) {
    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
