<?php

class ControllerExtensionModuleSyncMs extends Controller {

  private $version;

  public function index() {  
    $this->SetVersion();
    $token = $this->GetTokenName();

    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
      if ($this->version == "2.3") {
       $this->model_setting_setting->editSetting('syncms', $this->request->post); 
      } else {
        $this->model_setting_setting->editSetting('module_syncms', $this->request->post); 
      }

      $this->session->data['success'] = 'Настройки сохранены';
      
      $this->response->redirect($this->url->link('extension/module/syncms', $token . '=' . $this->session->data[$token], true));
    }

    $data = array();

    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    } else {
      $data['success'] = "";
    }
    
    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->session->data['error_warning'])) {
      $data['error_warning'] = $this->session->data['error_warning'];
      unset($this->session->data['error_warning']);
    } else {
      $data['error_warning'] = "";
    }

    if ($this->ConfigGet('token') != '') {
      $headers = array("Authorization:Bearer " . $this->ConfigGet('token'), "Content-Type: application/json");
    } else {
      $headers = array("Authorization:Basic " . base64_encode($this->ConfigGet('login') . ':' . $this->ConfigGet('password')), "Content-Type: application/json");
    } 

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Типы цен
    $url = 'https://online.moysklad.ru/api/remap/1.2/context/companysettings/pricetype';
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = json_decode(curl_exec($ch), true);
    $data['sale_prices'] = array();
    $notAuth = "Авторизация не выполнена";
    if (!isset($response['errors'])) {
      foreach ($response as $key => $value) {
        $data['sale_prices'][] = $value['name'];
      }
    } else {
      $data['sale_prices'][] = $notAuth;
    }
    $data['sale_price_key'] = $this->ConfigGet('sale_price');

    // Склады
    curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/store');
    $response = json_decode(curl_exec($ch), true);
    $data['store'] = array();
    $notAuth = "Авторизация не выполнена";
    if (!isset($response['errors'])) {
      foreach ($response['rows'] as $key => $value) {
        $data['store'][$value['meta']['href']] = $value['name'];
        $data['stock_store'][$value['meta']['href']] = $value['name'];
      }
    } else {
      $data['store'][] = $notAuth;
      $data['stock_store'][] = $notAuth;
    }
    $data['store_key'] = $this->ConfigGet('store');
    $data['stock_store_key'] = $this->ConfigGet('stock_store');

    // Организации
    curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/organization');
    $response = json_decode(curl_exec($ch), true);
    $data['organization'] = array();
    $notAuth = "Авторизация не выполнена";
    if (!isset($response['errors'])) {
      foreach ($response['rows'] as $key => $value) {
        $data['organization'][$value['meta']['href']] = $value['name'];
      }
    } else {
      $data['organization'][] = $notAuth;
    }
    $data['organization_key'] = $this->ConfigGet('organization');

    // Product offset
    $url = 'https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle&limit=0';
    curl_setopt($ch, CURLOPT_URL, $url);

    $response = json_decode(curl_exec($ch), true);

    $data['product_offset'] = array();
    $notAuth = "Авторизация не выполнена";
    $numb = 0;
    
    if (!isset($response['errors'])) {
      $size = $response['meta']['size'];
      for ($i = 0; $i < floor($size / 1000); $i++) { 
        $data['product_offset'][$numb] = sprintf("%s - %s", $numb + 1, $numb + 1000);
        $numb += 1000;
      }
      $data['product_offset'][$numb] = sprintf("%s - %s", $numb + 1, $size);
      
    } else {
      $data['product_offset'][] = $notAuth;
    }
    $data['product_offset_key'] = $this->ConfigGet('product_offset');
    $data['image_offset_key'] = $this->ConfigGet('image_offset');

    // Category offset
    $url = 'https://online.moysklad.ru/api/remap/1.2/entity/productfolder?limit=0';
    curl_setopt($ch, CURLOPT_URL, $url);

    $response = json_decode(curl_exec($ch), true);

    $data['cat_offset'] = array();
    $notAuth = "Авторизация не выполнена";
    $numb = 0;
    
    if (!isset($response['errors'])) {
      $size = $response['meta']['size'];
      for ($i = 0; $i < floor($size / 1000); $i++) { 
        $data['cat_offset'][$numb] = sprintf("%s - %s", $numb + 1, $numb + 1000);
        $numb += 1000;
      }
      $data['cat_offset'][$numb] = sprintf("%s - %s", $numb + 1, $size);
      
    } else {
      $data['cat_offset'][] = $notAuth;
    }
    $data['cat_offset_key'] = $this->ConfigGet('cat_offset');

    // Variant offset
    $url = 'https://online.moysklad.ru/api/remap/1.2/entity/variant?limit=0';
    curl_setopt($ch, CURLOPT_URL, $url);

    $response = json_decode(curl_exec($ch), true);

    $data['attr_offset'] = array();
    $notAuth = "Авторизация не выполнена";
    $numb = 0;
    
    if (!isset($response['errors'])) {
      $size = $response['meta']['size'];
      for ($i = 0; $i < floor($size / 1000); $i++) { 
        $data['attr_offset'][$numb] = sprintf("%s - %s", $numb + 1, $numb + 1000);
        $numb += 1000;
      }
      $data['attr_offset'][$numb] = sprintf("%s - %s", $numb + 1, $size);
      
    } else {
      $data['attr_offset'][] = $notAuth;
    }
    $data['attr_offset_key'] = $this->ConfigGet('attr_offset');

    curl_close($ch);

    $languageID = $this->GetLanguageID();

    // Stock Status
    $query = $this->db->query("SELECT name, stock_status_id FROM " . DB_PREFIX . "stock_status WHERE language_id = $languageID");
    foreach ($query->rows as $key => $value) {
      $data['stock_status'][$value['stock_status_id']] = $value['name'];
    }
    $data['stock_status_key'] = $this->ConfigGet('stock_status');
    
    // Основные настройки
    $data['status'] = $this->ConfigGet('status');
    $data['login'] = $this->ConfigGet('login');
    $data['password'] = $this->ConfigGet('password');
    $data['token'] = $this->ConfigGet('token');

    // Лишние товары
    $data['absence_products'] = $this->ConfigGet('absence_products');

    // Добавление/обновление товаров
    $data['binding'] = $this->ConfigGet('binding');
    $data['binding_name'] = $this->ConfigGet('binding_name');
    $data['desc_update'] = $this->ConfigGet('desc_update'); 
    $data['name_update'] = $this->ConfigGet('name_update');
    $data['url_update'] = $this->ConfigGet('url_update');
    $data['cat_update'] = $this->ConfigGet('cat_update');
    $data['sku_update'] = $this->ConfigGet('sku_update');
    $data['weight_update'] = $this->ConfigGet('weight_update');
    $data['weight_unity'] = $this->ConfigGet('weight_unity');
    $data['manufacturer_update'] = $this->ConfigGet('manufacturer_update');
    $data['stock_status_update'] = $this->ConfigGet('stock_status_update');
    $data['manufacturer'] = $this->ConfigGet('manufacturer');
    $data['from_group'] = $this->ConfigGet('from_group');

    // Синхронизация заказов
    $data['order_prefix'] = $this->ConfigGet('order_prefix');
    $data['order_binding'] = $this->ConfigGet('order_binding');

    // Лог
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/catalog/controller/extension/module/syncms_log.txt')) {
      $data['log'] = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/catalog/controller/extension/module/syncms_log.txt');
    }

    // Синхронизация опций
    $data['sum_option'] = $this->ConfigGet('sum_option');
    $data['zero_option_price'] = $this->ConfigGet('zero_option_price');      

    // Мета товаров
    $data['meta_prod_update'] = $this->ConfigGet('meta_prod_update');
    $data['prod_meta_title'] = $this->ConfigGet('prod_meta_title');
    $data['prod_meta_desc'] = $this->ConfigGet('prod_meta_desc');
    $data['prod_meta_keyword'] = $this->ConfigGet('prod_meta_keyword');

    // Мета категорий
    $data['meta_cat_update'] = $this->ConfigGet('meta_cat_update');
    $data['cat_meta_title'] = $this->ConfigGet('cat_meta_title');
    $data['cat_meta_desc'] = $this->ConfigGet('cat_meta_desc');
    $data['cat_meta_keyword'] = $this->ConfigGet('cat_meta_keyword');

    $data += $this->load->language('extension/module/syncms');
    $this->document->setTitle($this->language->get('heading_title'));

    $data += $this->GetBreadCrumbs();

    $data['action'] = $this->url->link('extension/module/syncms', $token . '=' . $this->session->data[$token], true);

    if ($this->version == '2.3') {
      $data['cancel'] = $this->url->link('extension/extension', $token . '=' . $this->session->data[$token] . '&type=module', true);
    } else {
      $data['cancel'] = $this->url->link('marketplace/extension', $token . '=' . $this->session->data[$token] . '&type=module', true);
    }

    // Ссылки
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '') {
      $url = HTTPS_CATALOG . 'index.php?route=extension/module/syncms/';
      $data['admin_url'] = HTTPS_SERVER;
    } else {
      $url = HTTP_CATALOG . 'index.php?route=extension/module/syncms/';
      $data['admin_url'] = HTTP_SERVER;
    }

    $data['product_add_href'] =  $url . 'ProductAdd';
    $data['stock_update_href'] = $url . 'StockUpdate';
    $data['price_update_href'] = $url . 'PriceUpdate';
    $data['product_update_href'] = $url . 'ProductUpdate';
    $data['category_update_href'] = $url . 'CategoryUpdate';
    $data['image_sync_href'] = $url . 'SyncImage';
    $data['modification_sync_href'] = $url . 'SyncModification';
    $data['category_add_href'] = $url . 'CategoryAdd';
    $data['absence_product_href'] = $url . 'SyncAbsenceProducts';
    $data['order_add_href'] = $url . 'OrderAdd';
    $data['order_update_href'] = $url . 'OrderUpdate';
    $data['order_update_oc_href'] = $url . 'OrderUpdateOC';
    $data['log_clear_href'] = $url . 'LogClear';

    $data['cron_product_add'] = "/usr/bin/wget -O - '" . $url . 'ProductAdd&cron=true' . "'";
    $data['cron_stock_update'] = "/usr/bin/wget -O - '" . $url . 'StockUpdate&cron=true' . "'";
    $data['cron_price_update'] = "/usr/bin/wget -O - '" . $url . 'PriceUpdate&cron=true' . "'";
    $data['cron_product_update'] = "/usr/bin/wget -O - '" . $url . 'ProductUpdate&cron=true' . "'";
    $data['cron_category_update'] = "/usr/bin/wget -O - '" . $url . 'CategoryUpdate&cron=true' . "'";
    $data['cron_image_sync'] = "/usr/bin/wget -O - '" . $url . 'SyncImage&cron=true' . "'";
    $data['cron_modification_sync'] = "/usr/bin/wget -O - '" . $url . 'SyncModification&cron=true' . "'";
    $data['cron_category_add'] = "/usr/bin/wget -O - '" . $url . 'CategoryAdd&cron=true' . "'";
    $data['cron_sync_absence_products'] = "/usr/bin/wget -O - '" . $url . 'SyncAbsenceProducts&cron=true' . "'";
    $data['cron_order_add'] = "/usr/bin/wget -O - '" . $url . 'OrderAdd&cron=true' . "'";
    $data['cron_order_update'] = "/usr/bin/wget -O - '" . $url . 'OrderUpdate&cron=true' . "'";
    $data['cron_order_update_oc'] = "/usr/bin/wget -O - '" . $url . 'OrderUpdateOC&cron=true' . "'";

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/module/syncms', $data));
  }


  private function GetBreadCrumbs() {
    $token = $this->GetTokenName();

    $data = array(); $data['breadcrumbs'] = array();
    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_home'),
      'href' => $this->url->link('common/dashboard', $token . '=' . $this->session->data[$token], true));
    if ($this->version == '2.3') {
      $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_extension'),
      'href' => $this->url->link('extension/extension', $token . '=' . $this->session->data[$token] . '&type=module', true));
    } else {
      $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_extension'),
      'href' => $this->url->link('marketplace/extension', $token . '=' . $this->session->data[$token] . '&type=module', true));
    }
    
    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('extension/module/syncms', $token . '=' . $this->session->data[$token], true));
    return $data;
  }


  private function GetLanguageID() {
    $query = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = 'ru-ru' ");
    return $query->row['language_id'];
  }

  private function SetVersion() {
    if (preg_match('/^2.3.*/', VERSION)) {
      $this->version = "2.3";
    } else {
      $this->version = "3";
    }
  }

  private function GetTokenName() {
    if ($this->version == '2.3') {
      $token = "token";
    } else {
      $token = "user_token";
    }

    return $token;
  }

  private function ConfigGet($value) {
    if ($this->version == "2.3") {
      return $this->config->get("syncms_" . $value);
    } else {
      return $this->config->get("module_syncms_" . $value);
    }
  }
}