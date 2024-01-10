<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<?php
function dump($var){
    echo "<pre>";
    var_dump($var);
    echo "</pre>";

}
function dump_die($var){
    dump($var);
    die;
}

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include "config.php";

//Підключення до БД
$host   =  DB_HOSTNAME;
$db     =  DB_DATABASE;
$user   =  DB_USERNAME;
$psw    =  DB_PASSWORD;

$charset = 'utf8';


// Спроба підключення до бази даних з використанням PDO
try
{
    // Масив опцій для підключення до бази даних
    $options = [
        // Встановлення режиму виведення помилок як виключень
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        
        // Встановлення режиму виведення даних як асоціативного масиву
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // Вимкнення емуляції підготовлених запитів
        // Це забезпечує більш високий рівень безпеки
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    // Створення нового об'єкта PDO для підключення до бази даних
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $psw, $options);
}
// Якщо під час спроби підключення виникла помилка, вона буде виведена
catch(PDOException $e)
{
    die("Помилка:". $e->getMessage());
}
// Робимо константу з підключення до бази даних
    global $PDO;
    $PDO = $pdo;



// **********************************************************************************
// МЕТОДИ ПО РОБОТІ З ФОРМУВАННЯ МАСИВУ ДАНИХ З ТАБЛИЦІ
// **********************************************************************************

global $CATEGORY;
$CATEGORY = array();
$globalCategoryId = 1;

function bildCategoryArray($string) {
    global $CATEGORY, $globalCategoryId;

    $localCategories = [];  // Цей масив буде зберігати категорії для поточного виклику

    $data = explode("##", $string);
    $new_cat = [];

    foreach ($data as $categoryString) {
        $new_cat[] = explode("/", $categoryString);
    }

    foreach ($new_cat as $categories) {
        $parent_id = 0;

        foreach ($categories as $cat) {
            $cat = trim($cat);
            $existingCategory = null;
            
            foreach ($CATEGORY as $existing) {
                if ($existing['category_name'] == $cat && $existing['parrent_cat_id'] == $parent_id) {
                    $existingCategory = $existing;
                    break;
                }
            }

            if ($existingCategory) {
                $parent_id = $existingCategory['category_id'];
                $localCategories[$existingCategory['category_id']] = $existingCategory;
            } else {
                $newCategory = [
                    'category_id' => $globalCategoryId,
                    'category_name' => $cat,
                    "parrent_cat_id" => $parent_id
                ];

                $CATEGORY[$globalCategoryId] = $newCategory;
                $localCategories[$globalCategoryId] = $newCategory;

                $parent_id = $globalCategoryId;
                $globalCategoryId++;
            }
        }
    }

    return $localCategories;  // Повертаємо категорії, які були оброблені під час цього виклику
}

/**
 * Функція для перетворення рядка зображень у масив.
 * 
 * @param string $string Вхідний рядок зображень, розділених пробілами.
 * @return array Масив зображень.
 */
function bildImageArray($string) {
    // Розділення вхідного рядка за пробілами
    $data = explode(" ", $string);

    // Повертаємо отриманий масив зображень
    return $data;
}


/**
 * Функція для перетворення вхідного масиву в новий масив атрибутів.
 * 
 * @param array $array Вхідний масив.
 * @return array Масив атрибутів.
 */
function bildAtributeArray($array) {
    
    // Створюємо новий масив з потрібними нам атрибутами
    $attribute = [
        "Автор"                     => $array["Параметр: Автор"],
        "ISBN"                      => $array["Параметр: ISBN:"],
        "Рік видання"               => $array["Параметр: Рік видання"],
        "Видавництво"               => $array["Параметр: Видавництво"],
        "Тип обкладинки"            => $array["Параметр: Тип обкладинки"],
        "Об'єм, сторінок"           => $array["Параметр: Об'єм, сторінок"],
        "Формат"                    => $array["Параметр: Формат"],
        "Мітка"                     => $array["Параметр: Мітка"],
        "Серія"                     => $array["Параметр: Серія"],
        "Мова"                      => $array["Параметр: Мова"],
        "Тип товару"                => $array["Параметр: Тип товару"],
        "Країна походження"         => $array["Параметр: Країна походження"],
        "Описание для Rozetka"      => $array["Параметр: Описание для Rozetka"],
        "Цена для Rozetka"          => $array["Параметр: Цена для Rozetka"],
        "Автор для Rozetka"         => $array["Параметр: Автор для Rozetka"],
        "Каталог Rozetka"           => $array["Параметр: Каталог Rozetka"],
        "Переплет Rozetka"          => $array["Параметр: Переплет Rozetka"],
        "IdПереплет Rozetka"        => $array["Параметр: IdПереплет Rozetka"],
        "IdИздательство Rozetka"    => $array["Параметр: IdИздательство Rozetka"],
        "Раздел Rozetka"            => $array["Параметр: Раздел Rozetka"],
        "ParamIdРаздел Rozetka"     => $array["Параметр: ParamIdРаздел Rozetka"],
        "IdРаздел Rozetka"          => $array["Параметр: IdРаздел Rozetka"],
        "Жанр Rozetka"              => $array["Параметр: Жанр Rozetka"],
        "IdЖанр Rozetka"            => $array["Параметр: IdЖанр Rozetka"],

    ];
    
    // Повертаємо новий масив атрибутів
    return $attribute;
}

// **********************************************************************************
// **********************************************************************************


// **********************************************************************************
// МЕТОДИ ПО РОБОТІ З ОНОВЛЕННЯ ТОВАРУ НА САЙТІ
// **********************************************************************************
/**
 * Функція перевіряє наявність продукту в базі даних за допомогою 1c_id.
 * 
 * @param string $id_1c ID продукту для перевірки.
 * @return int|bool product_id, якщо продукт існує, або false, якщо продукту немає.
 */
function issetProduct($id_1c)
{
    global $PDO;
    // Запит до БД з обмеженням результату до одного рядка
    $query = "SELECT product_id FROM oc_product_to_1c WHERE 1c_id = :id LIMIT 1";
    $stmt = $PDO->prepare($query);

    $stmt->bindValue(":id", $id_1c);

    $stmt->execute();
    $data = $stmt->fetch();

    // Повертаємо product_id або false
    return $data ? $data["product_id"] : false;
}


/**
 * Перевірка наявності опису продукту в базі даних.
 *
 * @param int $product_id - ідентифікатор продукту, для якого потрібно отримати опис.
 * 
 * @return string - повертає опис продукту, якщо він є, або порожній рядок, якщо опису немає.
 */
function checkDescription($product_id) {
    global $PDO;
    
    try {
        // Формуємо SQL-запит для отримання опису продукту за заданим ідентифікатором
        $query = "SELECT description FROM oc_product_description WHERE product_id = :id";
        
        // Підготовлюємо запит до виконання
        $stmt = $PDO->prepare($query);
        
        // Прив'язуємо значення ідентифікатора продукту до параметра запиту
        $stmt->bindValue(":id", $product_id);
        
        // Виконуємо запит
        $stmt->execute();
        
        // Отримуємо результат запиту
        $data = $stmt->fetch();
        
        // Повертаємо опис продукту, якщо він є, або порожній рядок, якщо опис відсутній
        return $data ? $data["description"] : "";
    } catch(PDOException $e) {
        // Виводимо повідомлення про помилку, якщо щось пішло не так
        echo "Помилка при отриманні опису: " . $e->getMessage();
        return "";
    }
}

/**
 * Оновлення опису продукту в базі даних.
 *
 * @param int $product_id - ідентифікатор продукту, для якого потрібно оновити опис.
 * @param string $description - новий опис продукту.
 * 
 * @return void
 */
function updateDescription($product_id, $description) {
    global $PDO;

    try {
        // Формуємо SQL-запит для оновлення опису продукту за заданим ідентифікатором
        $query = "UPDATE oc_product_description SET description = :description WHERE product_id = :id";
        
        // Підготовлюємо запит до виконання
        $stmt = $PDO->prepare($query);

        // Прив'язуємо значення до параметрів запиту
        $stmt->bindValue(":id", $product_id);
        $stmt->bindValue(":description", $description);

        // Виконуємо запит
        $stmt->execute();

        // Виводимо інформацію про успішне оновлення
        dump("У товара з ID {$product_id} - оновлено опис.");
    } catch(PDOException $e) {
        // Виводимо повідомлення про помилку
        dump_die("Сталась неочікувана помилка при оновленні товара з ID {$product_id}. Помилка: " . $e->getMessage());
    }
}

// function ensureCategoryExists($categories) {
//     global $PDO;
//     $lastCategoryId = 0;

//     foreach ($categories as $categoryPath) {
//         $parentId = 0;
//         $allCategoryIds = []; // Зберігаємо всі ID категорій для поточного шляху

//         foreach ($categoryPath as $categoryName) {
//             $categoryName = trim($categoryName); // Видалення небажаних пробілів

//             $stmt = $PDO->prepare("SELECT category_id FROM oc_category_description WHERE name = :name");
//             $stmt->bindValue(':name', $categoryName);
//             $stmt->execute();
//             $category = $stmt->fetch();

//             if (!$category) {
//                 // Додавання нової категорії
//                 $stmt = $PDO->prepare("INSERT INTO oc_category (parent_id, date_modified, date_added) VALUES (:parentId, NOW(), NOW())");
//                 $stmt->bindValue(':parentId', $parentId);
//                 $stmt->execute();
//                 $categoryId = $PDO->lastInsertId();

//                 // Додавання опису нової категорії
//                 $stmt = $PDO->prepare("INSERT INTO oc_category_description (category_id, language_id, name) VALUES (:categoryId, 1, :name)");
//                 $stmt->bindValue(':categoryId', $categoryId);
//                 $stmt->bindValue(':name', $categoryName);
//                 $stmt->execute();

//                 $allCategoryIds[] = $categoryId; // Додаємо ID нової категорії до списку

//                 $parentId = $categoryId;
//                 $lastCategoryId = $categoryId;
//             } else {
//                 $allCategoryIds[] = $category['category_id'];
//                 $parentId = $category['category_id'];
//                 $lastCategoryId = $parentId;
//             }
//         }

//         // Формування зв'язків між категоріями
//         for ($i = 0; $i < count($allCategoryIds); $i++) {
//             for ($j = 0; $j <= $i; $j++) {
//                 // Перевірка на дублювання перед вставкою
//                 $stmtCheck = $PDO->prepare("SELECT * FROM oc_category_path WHERE category_id = :categoryId AND path_id = :pathId AND level = :level");
//                 $stmtCheck->bindValue(':categoryId', $allCategoryIds[$i]);
//                 $stmtCheck->bindValue(':pathId', $allCategoryIds[$j]);
//                 $stmtCheck->bindValue(':level', $j);
//                 $stmtCheck->execute();
//                 $exists = $stmtCheck->fetch();

//                 // Якщо запису не існує, то додаємо його
//                 if (!$exists) {
//                     $stmt = $PDO->prepare("INSERT INTO oc_category_path (category_id, path_id, level) VALUES (:categoryId, :pathId, :level)");
//                     $stmt->bindValue(':categoryId', $allCategoryIds[$i]);
//                     $stmt->bindValue(':pathId', $allCategoryIds[$j]);
//                     $stmt->bindValue(':level', $j);
//                     $stmt->execute();
//                 }
//             }
//         }
//     }
//     return $lastCategoryId;
// }


/**
 * Перевірка наявності зв'язку продукту з категорією.
 *
 * @param int $product_id - ідентифікатор продукту.
 * 
 * @return bool - true, якщо зв'язок існує; false - в іншому випадку.
 */
function hasCategoryLink($product_id) {
    global $PDO;

    try {
        $query = "SELECT * FROM oc_product_to_category WHERE product_id = :id";
        $stmt = $PDO->prepare($query);
        $stmt->bindValue(":id", $product_id);
        $stmt->execute();

        $data = $stmt->fetch();

        return !empty($data);
    } catch(PDOException $e) {
        echo "Помилка при перевірці зв'язку продукту з категорією: " . $e->getMessage();
        return false;
    }
}



// **********************************************************************************
// ОТРИМУЄМО ДАНІ З ТАБЛИЦІ ДЛЯ ОНОВЛЕННЯ В БАЗІ САЙТУ
// **********************************************************************************

$url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTJ_W0xjZTJHzKw38tDwSiDIFsjPvDQHEMqQHV3QdVm0XEYKzX-MMzhH1qU-JtL-cBRg72aSiUJnOa5/pub?gid=0&single=true&output=csv';

$data = file_get_contents($url);

$rows = explode("\n", $data);

$headers = str_getcsv(array_shift($rows));

$csvData = [];

$nonMatchingRows = [];

foreach ($rows as $row)
{
    $rowData = str_getcsv($row);

    if (count($headers) != count($rowData)) {
        // Якщо кількість не співпадає, додамо пустий рядок для відсутніх даних
        $diff = count($headers) - count($rowData);
        for ($i = 0; $i < $diff; $i++) {
            $rowData[] = ""; // Додати порожні значення
        }
    }

    $csvData = array_combine($headers, $rowData);
    $table[] = [
        '1c_code' => $csvData["Дополнительное поле: ID Мой Склад"],
        'description' => $csvData["Полное описание"],
        'category' => bildCategoryArray($csvData["Размещение на сайте"]),
        'image' => bildImageArray($csvData["Изображения"]),
        'atribute' => bildAtributeArray($csvData)
    ];
}


// **********************************************************************************
// **********************************************************************************


// **********************************************************************************
// СТВОРЮЄМО КАТЕГОРІЇ ТА ЗВ'ЯЗКІВ МІЖ СОБОЮ
// **********************************************************************************




/**
 * Визначає шлях (ланцюг категорій) для заданої категорії.
 *
 * @param int $categoryId ID категорії, для якої потрібно отримати шлях.
 * @return array Масив ID категорій, що складають шлях.
 */

function getCategoryPath($categoryId) {
    global $CATEGORY; // Використання глобального масиву категорій
    $path = []; // Масив для зберігання шляху
    $currentId = $categoryId;

    // Цикл для побудови шляху до категорії
    while ($currentId) {
        $path[] = $currentId;
        $currentId = $CATEGORY[$currentId]['parrent_cat_id'];
    }

    return array_reverse($path); // Повертаємо масив у зворотньому порядку (від "батька" до "дитини")
}


/**
 * Перевіряє та забезпечує наявність категорій в базі даних.
 * Якщо категорія відсутня, функція створює її.
 */
function ensureCategoryExists() {
    global $PDO, $CATEGORY;

    // Обробка кожної категорії з масиву $CATEGORY
    foreach ($CATEGORY as $categoryData) {

        // Перевірка існування категорії в базі даних
        $stmt = $PDO->prepare("SELECT category_id FROM oc_category_description WHERE category_id = :id");
        $stmt->bindValue(':id', $categoryData['category_id']);
        $stmt->execute();
        $existingCategory = $stmt->fetch();

        // Якщо такої категорії немає:
        if (!$existingCategory) {
            // Вставка нової категорії в oc_category
            $stmt = $PDO->prepare("INSERT INTO oc_category (category_id, parent_id, date_modified, date_added, status, top, sort_order) VALUES (:id, :parentId, NOW(), NOW(), 1, 0, 999)");
            $stmt->bindValue(':id', $categoryData['category_id']);
            $stmt->bindValue(':parentId', $categoryData['parrent_cat_id']);
            $stmt->execute();

            // Вставка опису нової категорії в oc_category_description
            $stmt = $PDO->prepare("INSERT INTO oc_category_description (category_id, language_id, name, meta_title, meta_keyword) VALUES (:id, 2, :name, :metaTitle, :metaKeyword)");
            $stmt->bindValue(':id', $categoryData['category_id']);
            $stmt->bindValue(':name', $categoryData['category_name']);
            $stmt->bindValue(':metaTitle', $categoryData['category_name']);
            $stmt->bindValue(':metaKeyword', $categoryData['category_name']);
            $stmt->execute();

            // Вставка даних в oc_category_to_layout
            $stmtLayout = $PDO->prepare("INSERT INTO oc_category_to_layout (category_id, store_id, layout_id) VALUES (:id, 0, 0)");
            $stmtLayout->bindValue(':id', $categoryData['category_id']);
            $stmtLayout->execute();

            // Вставка даних в oc_category_to_store
            $stmtStore = $PDO->prepare("INSERT INTO oc_category_to_store (category_id, store_id) VALUES (:id, 0)");
            $stmtStore->bindValue(':id', $categoryData['category_id']);
            $stmtStore->execute();

            // Отримання та запис "шляху" до категорії в oc_category_path
            $path = getCategoryPath($categoryData['category_id']);
            foreach ($path as $index => $pathId) {
                $stmtPath = $PDO->prepare("INSERT INTO oc_category_path (category_id, path_id, level) VALUES (:categoryId, :pathId, :level)");
                $stmtPath->bindValue(':categoryId', $categoryData['category_id']);
                $stmtPath->bindValue(':pathId', $pathId);
                $stmtPath->bindValue(':level', $index);
                $stmtPath->execute();
            }
            
        }
    }
}




// **********************************************************************************
// **********************************************************************************


/**
 * Функція для додавання продукту до вказаної категорії.
 * 
 * @param int $product_id - ID продукту, який потрібно додати.
 * @param array $category - масив із даними категорії (включає "category_id").
 * 
 * Глобальна змінна $PDO - це об'єкт для роботи із базою даних.
 */
function add_product_category($product_id, $categories)
{
    global $PDO;

    // Отримуємо всі поточні категорії для даного продукта
    $query = "SELECT category_id FROM oc_product_to_category WHERE product_id = :prod_id";
    $stmt = $PDO->prepare($query);
    $stmt->bindValue(":prod_id", $product_id);
    $stmt->execute();
    $existingCategories = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    foreach ($categories as $category) {
        $category_id = $category["category_id"];

        // Якщо дана категорія відсутня в списку існуючих, додаємо її
        if (!in_array($category_id, $existingCategories)) {
            try {
                $query = "INSERT INTO oc_product_to_category SET product_id = :prod_id, category_id = :cat_id";
                $stmt = $PDO->prepare($query);
                $stmt->bindValue(":prod_id", $product_id);
                $stmt->bindValue(":cat_id", $category_id);
                $stmt->execute();

            } catch (PDOException $e) {
                echo $e;
            }
        }
    }
}

// **********************************************************************************
// **********************************************************************************


function uploadProductImages($prod_id, $links) {
    for($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        $path = "image/catalog/product/" . $prod_id . "_" . ($i + 1) . ".png";

        if (!file_exists($path)) {
            $data = file_get_contents($link);
            file_put_contents($path, $data);
        }
    }
}


// **********************************************************************************
// **********************************************************************************


function setProdImg($id)
{
    global $PDO;

    // Створюємо масив для зберігання зображень продуктів
    $products = [];

    $folderPath = 'image/catalog/product/'; // Змініть на шлях до вашої папки з зображеннями продуктів
    $files = scandir($folderPath);

    // Видаляємо '.' та '..' з результатів
    $files = array_diff($files, array('.', '..'));

    foreach ($files as $file) {
        // Розбиваємо назву файлу на частини за допомогою регулярного виразу
        if (preg_match('/^(\d+)_(\d+)\.png$/', $file, $matches)) {
            $productId = $matches[1];  // Отримуємо ідентифікатор продукту
            $imageNumber = $matches[2]; // Отримуємо порядковий номер зображення
            // Додаємо зображення до відповідного продукту у масиві
            $products[$productId][] = "catalog/product/" . $file;
        }
    }

    if (isset($products[$id])) {
        $prod_img = $products[$id];

        $main_img = array_shift($prod_img);

        try {
            // Перевіряємо, чи існує вже головне зображення для продукту
            $query = "SELECT image FROM oc_product WHERE product_id = :id";
            $stmt = $PDO->prepare($query);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            $main_data = $stmt->fetch();

            if (empty($main_data)) {
                // Вставляємо головне зображення, якщо воно відсутнє
                $query = "INSERT INTO oc_product SET image = :img WHERE product_id = :id";
                $stmt = $PDO->prepare($query);
                $stmt->bindValue(":id", $id);
                $stmt->bindValue(":img", $main_img);
                $stmt->execute();
            } else {
                // Оновлюємо головне зображення, якщо воно вже існує
                $query = "UPDATE oc_product SET image = :img WHERE product_id = :id";
                $stmt = $PDO->prepare($query);
                $stmt->bindValue(":id", $id);
                $stmt->bindValue(":img", $main_img);
                $stmt->execute();
            }

            // Вставляємо додаткові зображення продукту
            $query_insert = "INSERT INTO oc_product_image SET product_id = :id, image = :img";
            $stmt_insert = $PDO->prepare($query_insert);

            foreach ($prod_img as $img) {
                // Перевіряємо, чи існує вже запис з таким product_id та image
                $query_check = "SELECT COUNT(*) FROM oc_product_image WHERE product_id = :id AND image = :img";
                $stmt_check = $PDO->prepare($query_check);
                $stmt_check->bindValue(":id", $id);
                $stmt_check->bindValue(":img", $img);
                $stmt_check->execute();
                $row_count = $stmt_check->fetchColumn();

                if ($row_count == 0) {
                    // Вставка, якщо запис відсутній
                    $stmt_insert->bindValue(":id", $id);
                    $stmt_insert->bindValue(":img", $img);
                    $stmt_insert->execute();
                }
            }
        } catch (PDOException $e) {
            echo 'Помилка виконання запиту: ' . $e->getMessage();
        }
    }
}


// **********************************************************************************
// **********************************************************************************


function setAttribute($id, $attributes)
{
    global $PDO; // Використовуємо глобальну змінну $PDO для доступу до бази даних.

    try
    {
        // Запит для отримання усіх описів атрибутів.
        $query = "SELECT * FROM oc_attribute_description";

        // Підготовка та виконання запиту.
        $stmt = $PDO->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Зберігаємо результат запиту.

        $prod_attributes = []; // Ініціалізація масиву для зберігання атрибутів продукту.
;

        // Проходимо через передані атрибути.
        foreach ($attributes as $key => $value)
        {
            // Порівнюємо кожен атрибут з отриманими з БД.
            foreach ($data as $attribute) {
                if (trim($key) === trim($attribute["name"])) {

                    if(empty($value)) continue;

                    $prod_attributes[$attribute["attribute_id"]] = [
                        "product_id" => $id,
                        "attribute_id" => $attribute["attribute_id"],
                        "text" => $value
                    ];

                }
            }
        }

        // Запит для перевірки, чи існує атрибут у продукту.
        $query_check = "SELECT COUNT(*) FROM oc_product_attribute WHERE product_id = :prod_id AND attribute_id = :attr_id";
        $stmt_check = $PDO->prepare($query_check);

        // Запит на вставку нового атрибуту.
        $query_insert = "INSERT INTO oc_product_attribute SET product_id = :prod_id, attribute_id = :attr_id, language_id = 2, text = :text";
        $stmt_insert = $PDO->prepare($query_insert);

        // Проходимося по всім знайденим атрибутам.
        foreach ($prod_attributes as $attrs) {
            // Перевіряємо, чи існує вже такий атрибут для продукту.
            $stmt_check->bindValue(":prod_id", $attrs["product_id"]);
            $stmt_check->bindValue(":attr_id", $attrs["attribute_id"]);
            $stmt_check->execute();
            $row_count = $stmt_check->fetchColumn();

            // Якщо атрибут не існує, вставляємо його.
            if ($row_count == 0) {
                $stmt_insert->bindValue(":prod_id", $attrs["product_id"]);
                $stmt_insert->bindValue(":attr_id", $attrs["attribute_id"]);
                $stmt_insert->bindValue(":text", $attrs["text"]);
                $stmt_insert->execute();
            }
        }
        
    }
    catch(PDOException $e)
    {
        echo $e; // Вивід помилки, якщо сталася помилка PDO.
    }
}




// **********************************************************************************
// **********************************************************************************

foreach($table as $item){
    $id = issetProduct($item["1c_code"]);

    
    if($id){
        if(checkDescription($id) !== $item["description"]){
            updateDescription($id,$item["description"]);
        }
        // else{
        //     dump("У товара з ID {$product_id} - опис не потребує в оновленні.");
        // }

        ensureCategoryExists();

        add_product_category($id, $item["category"]);
        
        // uploadProductImages($id, $item["image"]);

        setProdImg($id);

        setAttribute($id, $item["atribute"]);
        // $pc++;
    }
    else{
        dump("Не знайденно товар з кодом {$item['1c_code']}");
        writeToLog("Не знайденно товар з кодом {$item['1c_code']}");
    }
}


function writeToLog($message) {
    $documentRoot = $_SERVER["DOCUMENT_ROOT"]; // Отримуємо кореневу директорію документів з серверної змінної
    $logFilePath = $documentRoot . '/errorProduct.log'; // Встановлюємо шлях до файлу логу

    // Створюємо строку логу з часовою міткою
    $logEntry = "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL;

    // Відкриваємо файл для додавання та блокуємо його, щоб уникнути одночасного запису
    $fileHandle = fopen($logFilePath, "a");
    if ($fileHandle) {
        // Блокуємо файл
        flock($fileHandle, LOCK_EX);

        // Записуємо строку у файл логу
        fwrite($fileHandle, $logEntry);

        // Розблоковуємо і закриваємо файл
        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);
    } else {
        // Якщо файл не відкрився, виводимо помилку
        error_log("Не можливо відкрити файл логу: $logFilePath");
    }
}



// echo $pc;