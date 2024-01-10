<?php
require_once(DIR_SYSTEM . 'library/phpspreadsheet/autoload.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ModelExtensionExcel extends Model
{

    public function getProductToCategories($id, $notes = false)
    {
        $sql = "SELECT p.product_id FROM oc_product_to_category as ptc INNER JOIN oc_product as p on p.product_id = ptc.product_id  WHERE category_id = $id";

        // if ($notes) {
        //     $sql .= " AND p.notes = '$notes'";
        // }
        $result = $this->db->query($sql);
        
        $product_id = [];
        foreach ($result->rows as $row) {
            $product_id[$row["product_id"]] = $row['product_id'];
        }

        return $product_id;
    }

    public function getAllProducts()
    {
        $query = "SELECT product_id FROM " . DB_PREFIX . "product";

        $stmt = $this->db->query($query);

       $product_id = [];

       foreach ($stmt->rows as $product)
       {
            $product_id[$product["product_id"]] = $product["product_id"];
       }

        return $product_id;
    }

    public function notes()
    {
        $sql = "SELECT DISTINCT notes FROM oc_product WHERE notes IS NOT NULL";
        $result = $this->db->query($sql);
        $arr = [];
        foreach ($result->rows as $row) {
            $arr[] = $row['notes'];
        }
        return $arr;
    }

    public function atribute()
    {

        $atribute = $this->getAllAtribute();
        return $atribute;
    }
    public function getAllAtribute()
    {
        $sql = "SELECT a.attribute_id AS id, ad.name 
        FROM oc_attribute AS a 
        INNER JOIN oc_attribute_description AS ad ON a.attribute_id = ad.attribute_id";
        $result = $this->db->query($sql);
        foreach ($result->rows as $row) {
            $atribute[$row["id"]] = $row['name'];
        }
        return $atribute;
    }

    public function getAllCategories()
    {
        $sql = "SELECT c.category_id, cd.name, c.parent_id 
                FROM oc_category AS c 
                JOIN oc_category_description AS cd ON c.category_id = cd.category_id 
                WHERE c.status = 1 
                ORDER BY c.parent_id, c.sort_order, cd.name";

        $result = $this->db->query($sql);
        $categories = [];

        foreach ($result->rows as $row) {
            $categories[] = [
                'category_id' => $row['category_id'],
                'name' => $row['name'],
                'parent_id' => $row['parent_id']
            ];
        }

        return $this->buildCategoryTree($categories);
    }

    private function buildCategoryTree($categories, $parentId = 0)
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildCategoryTree($categories, $category['category_id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $branch[] = $category;
            }
        }

        return $branch;
    }
    public function listXML()
    {
        // Шлях до папки
        $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/excel';

        // Перевірка на існування папки, і в разі необхідності її створення
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Отримання списку файлів у папці
        $files = scandir($folderPath);
        $array = [];

        // Ітерація по файлам
        foreach ($files as $file) {
            // Виключення поточного і батьківського каталогів
            if ($file != "." && $file != "..") {
                // Отримання розширення файлу
                $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

                // Перевірка розширення файлу
                if (strtolower($fileExtension) == 'xlsx') {
                    $array[] = [
                        "name" => $file,
                        "href" => HTTP_CATALOG . "excel/" . $file
                    ];
                }
            }
        }

        return $array;
    }
    public function deliteFile($file)
    {

        $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/excel/';
        // Полный путь к файлу
        $filePath = $folderPath . $file;
        if (file_exists($filePath)) {
            // Попытка удаления файла
            if (unlink($filePath)) {
                return true;
            } else {
                return false;
            }
        } else {
            return null;
        }
    }
    public function getProductInformation($product_id = 875)
    {
        // Формування SQL-запиту для отримання інформації про товар
        $sql = "SELECT 
        p.product_id as 'ID товара', 
        pd.name as 'Название товара или услуги', 
        pd.description as 'Краткое описание', 
        p.status as 'Видимость на витрине', 
        GROUP_CONCAT(ptc.category_id SEPARATOR '||') as 'Категории',
        p.image as 'Головне зображення',
        p.sku as 'Артикул',
        p.upc as 'Штрих-код',
        p.price as 'Цена продажи',
        ps.price as 'Акционная цена',
        ps.date_end as 'Дата окончания акции',
        p.main_price as 'Цена закупки',
        p.quantity as 'Остаток',
        p.notes as 'Заметка'
    FROM 
        oc_product AS p 
    JOIN 
        oc_product_description AS pd ON p.product_id = pd.product_id
    LEFT JOIN 
        oc_product_to_category AS ptc ON p.product_id = ptc.product_id
    LEFT JOIN 
        oc_category_description AS cd ON ptc.category_id = cd.category_id
    LEFT JOIN 
        oc_product_special AS ps ON p.product_id = ps.product_id AND ps.customer_group_id = 1 AND ps.date_start <= CURDATE() AND (ps.date_end >= CURDATE() OR ps.date_end = '0000-00-00')
    WHERE 
        p.product_id = $product_id
    GROUP BY 
        p.product_id, pd.name, pd.description, p.status, p.image, p.sku, p.upc, p.price, ps.price, p.main_price, p.quantity
    ";

        // Виконання SQL-запиту
        $result = $this->db->query($sql);



        $products = []; // Ініціалізація масиву для зберігання даних товарів

        // Перебір результатів запиту
        foreach ($result->rows as $row) {


            $products['data'] = $row; // Зберігання даних товару
            $cats = explode("||", $row['Категории']);
            foreach ($cats as $cat) {
                $products['Категорії'][] = $cat; // Зберігання ID категорій для товару
            }
            // Запит для отримання додаткових зображень товару
            $additionalImagesResult = $this->db->query("SELECT image FROM oc_product_image WHERE product_id = {$row['ID товара']}");
            $images = [$row['Головне зображення']];

            foreach ($additionalImagesResult->rows as $imageRow) {
                $images[] = $imageRow['image'];
            }


            // Видалення дублікатів зображень
            $images = array_unique($images);

            // Формування URL для зображень
            foreach ($images as &$image) {
                $image = HTTP_CATALOG . "image/" . $image;
            }

            $products['data']['Изображения'] = implode(' ', $images);
        }


        // Обробка категорій для кожного товару
        foreach ($products['Категорії'] as $categoryId) {
            // Отримання повного шляху категорії за ID і додавання його до масиву
            $categoryPaths[] = $this->getCategoryParent($categoryId);
        }



        // Об'єднання шляхів категорій у рядок з роздільником ' ## '
        $products['data']['Размещение на сайте'] = implode(' ## ', $categoryPaths);
        $products['data']['Цена продажи'] = floatval($products['data']['Цена продажи']);
        if ($products['data']['Акционная цена']) {
            $products['data']['Акционная цена'] = floatval($products['data']['Акционная цена']);
        }
        if ($products['data']['Цена закупки']) {
            $products['data']['Цена закупки'] = floatval($products['data']['Цена закупки']);
        }
        $atributes = $this->atribute();


        foreach ($atributes as $key => $attr) {
            $products['data']["Параметр: ${attr}"] = $this->getAllAtributeForProduct($key, $product_id);
        }

        $products["data"]["ID Мой Склад"] = $this->getProductTo1c($product_id);

        // Видалення тепер непотрібних елементів з масиву продуктів
        unset($products['Категорії']);
        unset($products["data"]['category_id']);
        unset($products["data"]['Головне зображення']);
        unset($products['data']['Категории']);
        unset($products['data']['Параметр: Страна']);


        $product = $products["data"];


        return $product; // Повернення оброблених даних про товар
    }
    public function getProductTo1c($product_id)
    {

        $sql = "SELECT 1c_id FROM oc_product_to_1c WHERE product_id = '$product_id'";
        $result = $this->db->query($sql);
        foreach ($result->rows as $row) {
            $id = $row['1c_id'];
        }
        return $id;
    }
    public function getAllAtributeForProduct($attr_id, $produc_id)
    {
        // Приймаємо ідентифікатор атрибуту та ідентифікатор продукту як параметри
        $attr = $attr_id;
        $id = $produc_id;

        // Формування SQL-запиту для отримання тексту атрибуту продукту
        $sql = "SELECT text FROM oc_product_attribute WHERE product_id = $id AND attribute_id = $attr";

        // Виконання SQL-запиту
        $result = $this->db->query($sql);
        foreach ($result->rows as $row) {
            $text = $row['text'];
        }

        // Якщо результати запиту відсутні або текст атрибуту порожній, $text встановлюється як порожній рядок
        if (empty($text)) {
            $text = "";
        }

        // Повернення тексту атрибуту
        return $text;
    }
    public function getCategoryParent($id)
    {

        $sql = "SELECT parent_id FROM oc_category WHERE category_id = '$id'";
        $result = $this->db->query($sql);
        foreach ($result->rows as $row) {

            $parent_id = $row["parent_id"];

            if ($parent_id == "0") {
                // Якщо досягнуто кореневої категорії, повертаємо її назву
                return $this->getCategoryNameById($id);
            } else {
                // Рекурсивний виклик для батьківської категорії та додавання назви поточної категорії
                return $this->getCategoryParent($parent_id) . "/" . $this->getCategoryNameById($id);
            }
        }
    }
    public function getCategoryNameById($categoryId)
    {
        $sql = "SELECT name FROM oc_category_description WHERE category_id = $categoryId";

        $result = $this->db->query($sql);
        foreach ($result->row as $row) {


            return $row;
        }

        // Якщо категорію не знайдено, повертаємо порожній рядок або null
        return null;
    }
    public function createExcelFile($products)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Додавання заголовків
        $headers = array_keys($products[0]);
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column++ . '1', $header);
        }

        // Додавання даних
        $rowNumber = 2;
        foreach ($products as $row) {
            $column = 'A';
            foreach ($row as $cell) {
                // Використання setCellValueExplicit для встановлення значення як текст
                $sheet->setCellValueExplicit($column++ . $rowNumber, $cell, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
            $rowNumber++;
        }

        // Шлях до папки для зберігання файлу
        $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/excel';

        // Перевіряємо, чи існує папка, і якщо ні, то створюємо її
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Створення імені файлу
        $fileName = "products_" . date("y_m_d_h_m_s") . ".xlsx";
        $fullPath = $folderPath . '/' . $fileName;

        // Запис у файл Excel
        $writer = new Xlsx($spreadsheet);
        $writer->save($fullPath);

        $this->createLogsNot("\n **************** ФАЙЛИ EXPORT $fileName УСПІШНО СТВОРЕНИЙ ****************", true);
    }

    public function fileLogExist()
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/excel/log.txt'; // Шлях до файлу log.txt

        // Перевірка, чи файл існує
        if (file_exists($filePath)) {
            return true;
        } else {
            return false;
        }
    }

    //-------------------------IMPORT-----------------------------------------------
    public function import_doc($imp_data)
    {
        $product_values = $imp_data["product_values"];
        $product_document = $imp_data["product_document"];

        $doc_data = $this->get_doc_data($product_document);


        $this->update_product($product_values, $doc_data);
    }

    // Функция для установки product_id
    private function update_product($values, $data)
    {

        $special = [];
        // Перебор массива данных
        foreach ($data as $product) {
            // Получение ключевого значения из массива продукта
            $main_key = $product["ID Мой Склад"];
            // Если ключевое значение пустое, пропускаем итерацию
            if (empty($main_key)) {
                $this->createLogsNot("ERROR: У записизі табилці excel з product_id " . $product["product_id"] . " - " . $product["name"] . " не має коду 1с мій склад");
                continue;
            }
            $this->createLogsNot("\n **************** Обробка товару з ключем $main_key ****************", true);

            // Получение product_id, связанного с данным 1c_id
            $main_id = $this->getProductIdTo1c($main_key);

            // Если product_id не совпадает с ID в массиве продукта, пропускаем итерацию
            if ($main_id !== $product["product_id"]) {
                $this->createLogsNot("ERROR: У записі табилці excel з кодом мій склад - $main_key не співпадає поле product_id - {$product["product_id"]}. Товар з таким кодом 1с має значення product_id - $main_id");
                continue;
            } else {
                $this->createLogsNot("Товар з кодом мій склад {$product["ID Мой Склад"]} успішно знайдено товар з product_id " . $product["product_id"] . " - " . $product["name"]);
            }

           // Перебор значений
            foreach ($values as $value) {
                foreach ($value as $key => $item) {
                    // Перевірка, чи ключ існує в масиві $product
                    if (isset($product[$key])) {
                        if ($key == "special") {
                            // Створення масиву для спеціальної ціни
                            $special = [
                                $product["special"],
                                $product["date_end"],
                            ];

                            // Виклик функції для обробки спеціальної ціни
                            $this->sqlSelect($key, $main_id, $special);
                        } else {
                            // Стандартний виклик для інших ключів
                            $this->sqlSelect($key, $main_id, $product[$key]);
                        }
                    }
                }
            }
            $this->createLogsNot("\n **************** Кінець обробки товару з ключем $main_key ****************", true);
        }

    }

    private function check_category($value)
    {
        $categories_str = explode("##", $value);
        $categories = [];

        foreach ($categories_str as $category) {
            if (stripos($category, "/") !== false) {
                $sub_categories = explode("/", $category);
                $categories = array_merge($categories, array_map('trim', $sub_categories));
            } else {
                $categories[] = trim($category);
            }
        }

        $categories = array_unique($categories);

        $data = [];

        // dump($categories);

        foreach ($categories as $category) {
            $category = $this->db->escape(trim($category));

            $query = "SELECT category_id FROM " . DB_PREFIX . "category_description WHERE name = '$category'";

            $stmt = $this->db->query($query);

            if ($stmt->num_rows == 0) {
                $undefine_category[] = $category;
            } else {
                $data[] = $stmt->row;
            }
        };
        return $data;
    }

    private function update_category($product_id, $data)
    {
        $query = "SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '$product_id'";

        $stmt = $this->db->query($query);

        $categories_id = $stmt->rows;

        $updated_data = [];

        foreach ($data as $item) {
            $found = false;

            foreach ($categories_id as $category_id) {
                if ($item["category_id"] === $category_id["category_id"]) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $updated_data[$product_id]["category_id"] = $item["category_id"];
            }
        }

        foreach ($updated_data as $prod_id => $cats_id) {
            foreach ($cats_id as $key => $cat_id) {
                $query = "INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '$prod_id', category_id = '$cat_id'";
                $this->createLogsNot("До товару з product_id - '$prod_id' до дано категрію з category_id = $cat_id");
                $this->db->query($query);
            }
        }
    }

    private function check_image($product_id, $values)
    {
        // Встановлення параметрів для відображення помилок PHP
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Розділення вхідного рядка на масив за допомогою пробілу
        $images = explode(" ", $values);

        // Шлях, де будуть зберігатися завантажені зображення
        $img_path = "catalog/excel/";

        // Повний шлях до директорії, де будуть зберігатися зображення
        $upload_path = DIR_IMAGE . $img_path;

        // Створення директорії, якщо вона не існує
        if (!is_dir($upload_path)) {
            if (!mkdir($upload_path, 0776, true)) {
                echo "Ошибка при создании директории.";
                exit;
            }
        }

        // Ініціалізація змінної для збереження шляху кінцевого файлу
        $finish_path = "";

        // Масив для зберігання шляхів до завантажених зображень
        $upload_img = [];

        // Перебір зображень та їх завантаження
        foreach ($images as $key => $image) {
            // Скидання шляху кінцевого файлу для кожного нового зображення
            $finish_path = "";

            // Перевірка, чи валідне зображення
            if ($this->isValidImage($image)) {

                // Збереження URL зображення
                $url_image = $image;

                // Перевірка на існування файлу за вказаним шляхом
                $image = $this->checkIfFileExists($image);



                if ($image) {
                    // Формування шляху файлу зображення
                    $finish_path =  $upload_path . $image;
                } else {

                    // Отримання шляху файлу зображення
                    $filename = $this->pathfFileImg($url_image);

                    // Додавання шляху до масиву та продовження циклу
                    $upload_img[$product_id][] = $filename;

                    continue;
                }

                // Отримання змісту файлу
                $cheak_file =  file_get_contents($url_image);

                // Перевірка на успішне завантаження файлу
                if ($cheak_file !== false) {
                    // Запис файлу та додавання шляху до масиву
                    if (!file_put_contents($finish_path, $cheak_file)) {
                        dump("error");
                    }
                } else {
                    continue;
                }
            }
        }
        // Повернення масиву зі шляхами до завантажених зображень
        return $upload_img;
    }


    private function checkIfFileExists($url)
    {
        // Розбивка URL на масив за допомогою розділювача '/'
        $parts = explode('/', $url);

        // Взяття останнього елементу масиву, який є ім'ям файлу
        $filename = end($parts);
        // Шляхи до можливих директорій з файлом
        $paths = [
            DIR_IMAGE . 'catalog/excel/' . $filename,
            DIR_IMAGE . 'catalog/product/' . $filename
        ];

        // Перевірка існування файлу у кожній директорії
        foreach ($paths as $path) {
            if (file_exists($path)) {
                // Файл існує
                return false;
            }
        }

        // Файл не існує в жодній з директорій
        return $filename;
    }

    private function pathfFileImg($url)
    {
        // Розбивка URL на масив за допомогою розділювача '/'
        $parts = explode('/', $url);

        // Взяття останнього елементу масиву, який є ім'ям файлу
        $filename = end($parts);

        // Шляхи до можливих директорій з файлом
        $paths = [
            DIR_IMAGE . 'catalog/excel/' . $filename,
            DIR_IMAGE . 'catalog/product/' . $filename
        ];

        // Перевірка існування файлу у кожній директорії
        foreach ($paths as $path) {
            if (file_exists($path)) {
                // Файл існує
                return str_replace(DIR_IMAGE, "", $path);
            }
        }

        // Файл не існує в жодній з директорій
        return false;
    }



    private function isValidImage($image)
    {
        // Перевірка, чи не порожній рядок
        if (empty($image)) {
            return false;
        }

        // Перетворення рядка в нижній регістр для незалежності від регістру
        $image = strtolower($image);

        // Перевірка на наявність розширень .png, .jpg або .jpeg
        if (stripos($image, ".png") !== false || stripos($image, ".jpg") !== false || stripos($image, ".jpeg") !== false) {
            return true;
        }

        return false;
    }
    public function createLogsNot($note, $param = false)
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/excel/log.txt';

        // Створення файла, якщо він не існує
        if (!file_exists($filePath)) {
            file_put_contents($filePath, '');
        }

        // Перевірка розміру файлу
        if (filesize($filePath) > 5 * 1024 * 1024) { // 50 МБ
            file_put_contents($filePath, ''); // Очищення файла
        }

        // Отримання поточної дати та часу
        $currentDate = date('d/m/Y H:i');
        if (!$param) {
            // Формування рядка для запису
            $compareNote = $currentDate . ": " . $note . "\n";
        } else {
            $compareNote = $note . "\n";
        }

        // Додавання рядка до файла
        file_put_contents($filePath, $compareNote, FILE_APPEND);
    }

    public function formatLogContents()
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/excel/log.txt';

        // Перевірка, чи файл існує
        if (!file_exists($filePath)) {
            return 'Файл логів не знайдено.';
        }

        // Читання вмісту файлу
        $fileContents = file_get_contents($filePath);
        $lines = explode("\n", $fileContents);
        $formattedContent = '';

        // Перевірка кожного рядка на наявність слова "ERROR"
        foreach ($lines as $line) {
            if (strpos($line, 'ERROR') !== false) {
                // Якщо є ERROR, форматування рядка в червоний колір
                $formattedContent .= '<span style="color: red;">' . htmlspecialchars($line) . '</span><br>';
            } else if (strpos($line, ' ****************') !== false) {
                $formattedContent .= '<span style="color: green;font-size: 16px;font-weight: 600;text-align: center;">' . htmlspecialchars($line) . '</span><br>';
            } else if(strpos($line, ' $$$$$$$$$$$$$$') !== false) {
                $formattedContent .= '<span style="color: red;font-weight: 600;text-align: center;">' . htmlspecialchars($line) . '</span><br>';
                
            } else {
                // Інакше - в зелений колір
                $formattedContent .= '<span style="color: #000;">' . htmlspecialchars($line) . '</span><br>';
            }
        }

        return $formattedContent;
    }


    private function update_image($data, $product_id)
    {

        $query = "DELETE FROM " . DB_PREFIX . "product_image WHERE  product_id = '$product_id'";
        $this->createLogsNot("Видалякмо всі зображення у товара з product_id");
        // Виконання запиту
        $this->db->query($query);

        if (empty($data)) {
            $query = "UPDATE " . DB_PREFIX . "product SET image = '' WHERE product_id = '$product_id'";
            $this->createLogsNot("ERROR: У товара з product_id -  $product_id, не має зображень для запису на сайт");
            // Виконання запиту
            $this->db->query($query);
        }
        // Перебір масиву $data, де ключ - це ID продукту, а значення - масив зображень
        foreach ($data as $product_id => $images) {
            // Перебір масиву зображень для кожного продукту
            foreach ($images as $key => $image) {
                // Якщо це перше зображення у списку, оновлюємо головне зображення продукту
                if ($key === 0) {
                    // Формування запиту на оновлення головного зображення продукту
                    $query = "UPDATE " . DB_PREFIX . "product SET image = '$image' WHERE product_id = '$product_id'";
                    $this->createLogsNot("У товара з product_id -  $product_id, успішно додано основне зображення '$image'");
                    // Виконання запиту
                    $this->db->query($query);
                } else {
                    // Для інших зображень вставляємо їх як додаткові зображення продукту
                    // Формування запиту на вставку додаткового зображення
                    $query = "INSERT INTO " . DB_PREFIX . "product_image SET  product_id = '$product_id', image = '$image' ";
                    $this->createLogsNot("У товара з product_id -  $product_id, успішно додано додаткове зображення '$image'");
                    // Виконання запиту
                    $this->db->query($query);
                }
            }
        }
    }

    private function update_special($product_id, $value)
    {
        // Перевірка, чи значення $value порожнє або складається лише з пробілів
        if (empty($value[0])) {
            // Видалення всіх спеціальних цін для продукту, якщо $value порожнє
            $query = "DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '$product_id'";

            $this->db->query($query);
        } else {
            // Конвертація $value в ціле число
            $price = $value[0];
            $data = $value[1] ?? date("Y-m-d");

            // Перевірка існування спеціальних цін для продукту
            $query = "SELECT product_id FROM " . DB_PREFIX . "product_special WHERE product_id = '$product_id'";
            $stmt = $this->db->query($query);

            if ($stmt->num_rows > 0) {
                // Оновлення існуючих спеціальних цін
                $query = "UPDATE " . DB_PREFIX . "product_special SET price = $price, date_start = CURDATE(), date_end = '$data' WHERE product_id = '$product_id'";
                $this->db->query($query);
            } else {
                // Додавання нової спеціальної ціни
                $query = "INSERT INTO " . DB_PREFIX . "product_special (product_id, customer_group_id, price, date_start, date_end) VALUES ('$product_id', 1, $price, CURDATE(), '$data')";
                $this->db->query($query);
            }
        }
    }

    // Функция для выполнения SQL-запроса в зависимости от ключа
    private function sqlSelect($key, $product_id, $value)
    {
        is_array($value) ? $value : $value =  $this->db->escape($value);

        switch ($key) {
            case "sku":
                // Обновление поля sku в таблице oc_product
                $sql = "UPDATE oc_product SET sku='$value' WHERE `product_id`='$product_id'";
                $this->executeQueryAndLog($sql, $product_id, "sku");
                break;
            case 'name':

                $sql = "UPDATE oc_product_description SET name = '$value' WHERE `product_id`='$product_id'";

                $this->executeQueryAndLog($sql, $product_id, "name");
                break;
            case 'description':
                $sql = "UPDATE oc_product_description SET description = '$value'  WHERE `product_id`='$product_id'";
                $this->executeQueryAndLog($sql, $product_id, "description");
                break;
            case 'status':
                $sql = "UPDATE oc_product SET  status = '$value' WHERE `product_id`='$product_id'";
                $this->executeQueryAndLog($sql, $product_id, "status");
                break;
            case 'image':
                $data = $this->check_image($product_id, $value);
                $this->update_image($data, $product_id);
                break;
            case 'upc':
                $sql = "UPDATE oc_product SET upc ='$value' WHERE `product_id`='$product_id'";
                $this->executeQueryAndLog($sql, $product_id, "upc");
                break;
            case 'price':
                $sql = "UPDATE oc_product SET price ='$value' WHERE `product_id`='$product_id'";
                $this->executeQueryAndLog($sql, $product_id, "price");
                break;
            case 'quantity':
                $sql = "UPDATE oc_product SET quantity ='$value' WHERE `product_id`='$product_id'";
                $this->executeQueryAndLog($sql, $product_id, "quantity");
                break;
            case 'notes':
                $sql = "UPDATE oc_product SET notes ='$value' WHERE `product_id`='$product_id'";
                $this->executeQueryAndLog($sql, $product_id, "notes");
                break;
            case 'categories':
                $data = $this->check_category($value);
                $this->update_category($product_id, $data);
                // $this->db->query("UPDATE oc_product_to_category SET WHERE `product_id`='$product_id'");
                break;
            case 'special':
                $this->update_special($product_id, $value);

                break;
            case '1':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("1"));
                break;
            case '2':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("2"));
                break;
            case '3':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("3"));
                break;
            case '4':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("4"));
                break;
            case '5':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("5"));
                break;
            case '6':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("6"));
                break;
            case '7':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("7"));
                break;
            case '8':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("8"));
                break;
            case '9':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("9"));
                break;
            case '10':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("10"));
                break;
            case '11':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("11"));
                break;
            case '12':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("12"));
                break;
            case '13':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("13"));
                break;
            case '14':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("14"));
                break;
            case '15':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("15"));
                break;
            case '17':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("17"));
                break;
            case '18':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("18"));
                break;
            case '19':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("19"));
                break;
            case '20':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("20"));
                break;
            case '21':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("21"));
                break;
            case '22':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("22"));
                break;
            case '23':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("23"));
                break;
            case '24':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("24"));
                break;
            case '25':
                $sql = "UPDATE oc_product_attribute SET text = '$value' WHERE `product_id`='$product_id' AND attribute_id = $key";
                $this->executeQueryAndLog($sql, $product_id, $this->getAttributeNameById("25"));
                break;
        }
    }

    public function getAttributeNameById($attribute_id)
    {
        // Підготовка SQL запиту для отримання імені атрибута
        $query = $this->db->query("SELECT name FROM oc_attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");

        // Перевірка, чи існує запис у базі даних
        if ($query->num_rows) {
            // Повернення імені атрибута
            return $query->row['name'];
        } else {
            // Якщо запис не знайдено, повернути null або власне повідомлення про помилку
            return null;
        }
    }


    public function executeQueryAndLog($sql, $product_id, $field)
    {
        // Виконання запиту
        // Ця лінія відправляє SQL запит, який передається як аргумент методу, до бази даних.
        // $this->db - це екземпляр класу для роботи з базою даних, метод query використовується для виконання запиту.
        $result = $this->db->query($sql);

        // Перевірка результату запиту
        // Ця частина коду перевіряє, чи був SQL запит успішним.
        // Змінна $result буде містити результат виконання запиту, який може бути true (успішний) або false (якщо виникла помилка).
        if ($result) {
            // У випадку успішного запиту формується повідомлення про успіх.
            $logMessage = "Оновленно поле '$field' з product_id '$product_id'";
        } else {
            // У випадку помилки формується повідомлення про помилку.
            $logMessage = "ERROR: Помилка при виконанні запиту для поля '$field' з product_id '$product_id'";
        }

        // Запис у лог
        // Ця лінія викликає метод createLogsNot для запису $logMessage в лог-файл.
        // Таким чином, у лог-файлі буде збережено інформацію про кожну спробу виконання SQL запиту.
        $this->createLogsNot($logMessage);

        // Повернення результату
        // Метод повертає результат виконання SQL запиту (true або false),
        // це може бути корисно для подальшої обробки в коді, який викликає цей метод.
        return $result;
    }

    // Функция для получения product_id по коду из 1C
    public function getProductIdTo1c($code)
    {
        // Выполнение SQL-запроса для получения product_id
        $sql = "SELECT product_id FROM oc_product_to_1c WHERE 1c_id = '$code'";
        $result = $this->db->query($sql);

        // Извлечение product_id из результата запроса
        foreach ($result->rows as $row) {
            $id = $row['product_id'];
        }
        return $id;
    }

    private function get_doc_data($doc_data)
    {

        $tmp = $doc_data["file"]["tmp_name"];

        $spreadsheet = IOFactory::load($tmp);

        $sheet = $spreadsheet->getActiveSheet();

        $doc_data = [];

        $titles = [];

        $column_mapping = [
            "ID товара"                         => "product_id",
            "Название товара или услуги"        => "name",
            "Краткое описание"                  => "description",
            "Видимость на витрине"              => "status",
            "Артикул"                           => "sku",
            "Штрих-код"                         => "upc",
            "Цена продажи"                      => "price",
            "Акционная цена"                    => "special",
            "Дата окончания акции"              => "date_end",
            "Цена закупки"                      => "Цена закупки",
            "Остаток"                           => "quantity",
            "Заметка"                           => "notes",
            "Изображения"                       => "image",
            "Размещение на сайте"               => "categories",
            "Параметр: Автор"                   => "2",
            "Параметр: ISBN"                    => "3",
            "Параметр: Рік видання"             => "4",
            "Параметр: Видавництво"             => "5",
            "Параметр: Тип обкладинки"          => "6",
            "Параметр: Об'єм, сторінок"         => "7",
            "Параметр: Формат"                  => "8",
            "Параметр: Мітка"                   => "9",
            "Параметр: Серія"                   => "10",
            "Параметр: Мова"                    => "11",
            "Параметр: Тип товару"              => "12",
            "Параметр: Країна походження"       => "13",
            "Параметр: Описание для Rozetka"    => "14",
            "Параметр: Автор для Rozetka"       => "15",
            "Параметр: Каталог Rozetka"         => "16",
            "Параметр: Переплет Rozetka"        => "17",
            "Параметр: IdПереплет Rozetka"      => "18",
            "Параметр: IdИздательство Rozetka"  => "19",
            "Параметр: Раздел Rozetka"          => "20",
            "Параметр: ParamIdРаздел Rozetka"   => "21",
            "Параметр: Цена для Rozetka"        => "22",
            "Параметр: IdРаздел Rozetka"        => "23",
            "Параметр: Жанр Rozetka"            => "24",
            "Параметр: IdЖанр Rozetka"          => "25",
            "ID Мой Склад"                      => "ID Мой Склад",
        ];

        foreach ($sheet->getRowIterator() as $row) {
            if (empty($titles)) {
                $cell_iterator = $row->getCellIterator();
                $cell_iterator->setIterateOnlyExistingCells(false);
                foreach ($cell_iterator as $cell) {
                    if (!is_null($cell)) {
                        $originalTitle = $cell->getValue();
                        // Змінюємо назву стовпця, якщо вона є у відображенні
                        $titles[] = array_key_exists($originalTitle, $column_mapping) ? $column_mapping[$originalTitle] : $originalTitle;
                    }
                }
                continue;
            }

            $row_data = [];
            $cell_iterator = $row->getCellIterator();
            $cell_iterator->setIterateOnlyExistingCells(false);
            $column_index = 0;
            foreach ($cell_iterator as $cell) {
                if ($cell->getValue() === NULL || $cell->getValue() === '') {
                    $row_data[$titles[$column_index]] = "";
                } else {
                    $cell_value = $cell->getCalculatedValue();
            
                    // Перевірка, чи є значення клітинки об'єктом RichText
                    if ($cell_value instanceof PhpOffice\PhpSpreadsheet\RichText\RichText) {
                        $cell_text = '';
                        foreach ($cell_value->getRichTextElements() as $element) {
                            if ($element instanceof PhpOffice\PhpSpreadsheet\RichText\TextElement) {
                                $cell_text .= $element->getText();
                            }
                        }
                        $row_data[$titles[$column_index]] = $cell_text;
                    } else {
                        // Перевірка, чи клітинка містить дату
                        if ($cell->getDataType() === PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC && PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                            $date = PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cell->getValue());
                            $row_data[$titles[$column_index]] = $date->format('Y-m-d'); // Форматування дати
                        } else {
                            // Якщо значення клітинки не є об'єктом RichText або датою, просто додаємо його до масиву
                            $row_data[$titles[$column_index]] = $cell_value;
                        }
                    }
                }
                $column_index++;
            }

            $doc_data[] = $row_data;
        }

        return $doc_data;
    }
}


// Функція для виведення змінної у форматі var_dump з форматуванням
function dump($var)
{
    echo "<pre>"; // Використання тегу <pre> для кращого форматування
    var_dump($var); // Виведення інформації про змінну
    echo "</pre>";
}

// Функція для виведення змінної у форматі print_r з форматуванням
function dump_r($var)
{
    echo "<pre>"; // Використання тегу <pre> для кращого форматування
    print_r($var); // Виведення значення змінної
    echo "</pre>";
}

// Функція для виведення змінної і завершення скрипта
function dump_die($var)
{
    dump($var); // Виклик функції dump для виведення змінної
    die; // Завершення виконання скрипта
}

function dd($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    exit;
}
