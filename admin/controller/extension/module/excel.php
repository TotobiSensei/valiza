<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class ControllerExtensionModuleExcel extends Controller
{
    public function index()
    {
        // Підключаємо переклад excel
        $this->load->language('extension/module/excel');
        // Задаємо назву модуля та тайтлу
        $this->document->setTitle($this->language->get('heading_title'));
        // Завантажуємо модель "налаштувань"
        $this->load->model('setting/module');
        // Завантажуємо модель excel
        $this->load->model('extension/excel');
        // Створюємо флагову зміну
        $data["erorr"] = false;
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            if (isset($this->request->post["export"])) {
                
                if ((!isset($this->request->post["selected_categories"]) || empty($this->request->post["selected_categories"])) && (!isset($this->request->post["all_products"]))) {
                    $data["erorr"] = "Ви не вибрали категорії для експорту";
                } else {
                    $notes = false;
                    if (!empty($this->request->post["notes"])) {
                        $notes = $this->request->post["notes"];
                    }

                    if(isset($this->request->post["all_products"]) && $this->request->post["all_products"] == "true")
                    {
                        $product_data = true;
                    }
                    else
                    {
                        $product_data = $this->request->post["selected_categories"];
                    }

                    $this->startBildfile($product_data, $notes);
                    $this->endFunction();
                }
            }
            if (isset($this->request->post["import"])) {
                if (!isset($this->request->post["values"]) || empty($this->request->post["values"])) {
                    $data["erorr"] = "Ви не вибрали поля для Імпорту";
                } else {
                    if (empty($this->request->files["file"]['name'])) {
                        $data["erorr"] .= "Ви не додали файл для Імпорту";
                    } else {
                        $this->import_doc();
                        $this->endFunction();
                    }
                }
            }
            // $test = $this->model_extension_excel->getProductInformation();

        }
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/excel', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/excel', 'user_token=' . $this->session->data['user_token'], true);
        $data['delite'] = $this->url->link('extension/module/excel/deliteFile', 'user_token=' . $this->session->data['user_token'], true);


        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $сategories = $this->model_extension_excel->getAllCategories();
        $data['сategories'] = $this->renderCategories($сategories);

        $data["notes"] = $this->renderSelectNotes();

        $data['attributes'] = $this->model_extension_excel->atribute();
        unset($data['attributes']["Страна"]);
        $data['list_excel'] = $this->model_extension_excel->listXML();
        if ($this->model_extension_excel->fileLogExist()) {
            $data["logs"] = $this->model_extension_excel->formatLogContents();
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['link_log'] = HTTP_CATALOG . 'excel/log.txt';

        $this->response->setOutput($this->load->view('extension/module/excel', $data));
    }
    public function renderSelectNotes()
    {
        $this->load->model('extension/excel');

        $notes = $this->model_extension_excel->notes();
        $html = ''; // Ініціалізуємо $html як порожній рядок
        if (!empty($notes)) {
            $html .= '<div><select name="notes" id="notes">'; // Використовуємо одинарні лапки для HTML

            foreach ($notes as $note) {
                $html .= "<option value=\"$note\">$note</option>"; // Інтерполяція змінної в подвійних лапках
            }

            $html .= '</select></div>'; // Закриваємо HTML
        }

        return $html;
    }
    public function renderCategories($categories, $level = 0)
    {
        $html = '';
        foreach ($categories as $category) {
            $indentation = str_repeat('&nbsp;&nbsp;', $level * 2);
            $html .= "<div><label>{$indentation}<input name='selected_categories[]' type='checkbox' value='{$category['category_id']}'> {$category['name']}</label></div>";

            if (!empty($category['children'])) {
                $html .= $this->renderCategories($category['children'], $level + 1);
            }
        }
        return $html;
    }
    public function deliteFile()
    {
        // Завантажуємо модель excel
        $this->load->model('extension/excel');

        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data["file_name"])) {
            $file = $data["file_name"];
        }

        if (empty($file)) {
            echo json_encode("Ви не вибрали файл");
            return;
        } else {
            $delite = $this->model_extension_excel->deliteFile($file);
            if($file !="log.txt"){
                $this->model_extension_excel->createLogsNot("\n $$$$$$$$$$$$$$$$$$$$$$$$$$$$ Файл $file видаленний $$$$$$$$$$$$$$$$$$$$$$$$$$$$",true);
            }
        }
        if ($delite) {


            $text['success'] = "Файл " . $file . " усішно видалений";
            echo json_encode($text);
            return;
        } else {
            $text["erorr"] = "Файл " . $file . " НЕ видалений\n зверніться до адміністратора";
            echo json_encode($text);
            return;
        }
    }
    public function startBildfile($array, $notes = false) // видалений параметр 
    {
        // Завантаження моделі 'extension/excel' для роботи з Excel-файлами
        $this->load->model('extension/excel');

        $products = array();
        $result = null;
        // Перебір масиву ID категорій і збір ID товарів, що входять до цих категорій
        if (!is_array($array))
        {
            $array = $this->model_extension_excel->getAllProducts();

            foreach ($array as $item)
            {
                $products[$item] = $item;
            }
        }
        else
        {
            foreach ($array as $id) {
            
                foreach ($this->model_extension_excel->getProductToCategories($id, $notes) as $product_id) {
                    // Додавання ID товару до масиву
                    $products[$product_id] = $product_id;
                }
            };
        }
        
        $excel = array();
        // Отримання інформації про кожен товар і додавання її до масиву Excel
        foreach ($products as $product_id) {
            $excel[] = $this->model_extension_excel->getProductInformation($product_id);
        }

        // // Створення Excel-файлу зі зібраною інформацією
        $result = $this->model_extension_excel->createExcelFile($excel);
        // Перевірка результату створення файлу та повернення відповідного повідомлення
        if ($result) {

            return "Файл створенний"; // У випадку успіху

        } else {
            return "Сталася помилка"; // У випадку помилки
        }
    }

    private function import_doc()
    {

        $values = $this->request->post["values"];

        $product_columns = [];

        foreach ($values as $value) {

            if (stripos($value, "attribute_") === FALSE) {

                if (isset($value)) {
                    if ($value === "name") {
                        $product_columns["description"][$value] = NULL;
                    } else if ($value === "description") {
                        $product_columns["description"][$value] = NULL;
                    } else if ($value === "") {
                        $product_columns["product"][$value] = NULL;
                    } else if ($value === "status") {
                        $product_columns["product"][$value] = NULL;
                    } else if ($value === "image") {
                        $product_columns["product"][$value] = NULL;
                    } else if ($value === "sku") {
                        $product_columns["product"][$value] = NULL;
                    } else if ($value === "upc") {
                        $product_columns["product"][$value] = NULL;
                    } else if ($value === "price") {
                        $product_columns["product"][$value] = NULL;
                    } else if ($value === "price") {
                        $product_columns["product"][$value] = NULL;
                    } else if ($value === "quantity") {
                        $product_columns["product"][$value] = NULL;
                    } else if ($value === "notes") {
                        $product_columns["product"][$value] = NULL;
                    } else if ($value === "categories") {
                        $product_columns["categories"][$value] = NULL;
                    } else if ($value === "special") {
                        $product_columns["special"][$value] = NULL;
                    } else {
                        $product_columns["other"][$value] = NULL;
                    }
                }
            } else {
                $parts = explode("_", $value);
                // dd($value);
                if (count($parts) === 2) {
                    $attribute_id = $parts[1]; // Отримуємо ідентифікатор атрибута

                    // Додаємо ідентифікатор атрибута до підмасиву "attribute_id"
                    $product_columns["attribute_id"][$attribute_id] = NULL;
                }
            }
        }

        $imp_data["product_values"] = $product_columns;

        $imp_data["product_document"] = $this->request->files;

        $this->model_extension_excel->import_doc($imp_data);
        // if(!isset($this->request->post["values"]) || empty($this->request->post["values"])){
        //     $data["erorr"] = "Ви не вибрали поля синхронізації";
        // }
    }
    private function endFunction()
    {
        $url = $this->url->link('extension/module/excel', 'user_token=' . $this->session->data['user_token'], true);
        $this->response->redirect($url);
    }
}
