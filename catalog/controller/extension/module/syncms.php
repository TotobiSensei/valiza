<?php
class ControllerExtensionModuleSyncMS extends Controller {

  private $cron;
  private $redirectURL;
  private $logText;
  private $version;

  private function SetVersion() {
    if (preg_match('/^2.3.*/', VERSION)) {
      $this->version = "2.3";
    } else {
      $this->version = "3";
    }
  }

  private function GetStartVars($settingsKey = null)
  {
    $this->SetVersion();
    if ($this->version == '2.3') {
      $config = "syncms_";
      $token = "token";
    } else {
      $config = "module_syncms_";
      $token = "user_token";
    }

    $startVars = [];
    if ($this->cron) {
      $startVars['redirectURL'] = '';
    } else {
      $startVars['redirectURL'] = $this->config->get($config . 'admin_url') . 'index.php?route=extension/module/syncms&' . $token . '=' . $this->session->data[$token];
    }
    
    $this->redirectURL = $startVars['redirectURL'];

    $startVars['status'] = $this->config->get($config . 'status');

    if ($startVars['status'] == 0) {
      $this->Output('Ошибка! Модуль выключен', 'error_warning');
      exit();
    }

    if ($this->config->get($config . 'token') != '') {
      $startVars['headers'] = array("Authorization:Bearer " . $this->config->get($config . 'token'), "Content-Type: application/json");
    } else {
      $startVars['headers'] = array("Authorization:Basic " . base64_encode($this->config->get($config . 'login') . ':' . $this->config->get($config . 'password')), "Content-Type: application/json");
    } 

    if ($settingsKey != null) {
      foreach ($settingsKey as $key) {
        if ($this->config->get($config . $key) != '') {
          $startVars[$key] = $this->config->get($config . $key);
        }
      }
    }
    
    return $startVars;
  }


  private function CheckResponse($response, $ch)
  {
    //Обработка ошибок curl
    if (isset($response['errors'])) {
      if ($response['errors'][0]['code'] == '1073' || $response['errors'][0]['code'] == '1049') {
        while (isset($response['errors'])) {
          sleep(3);
          $response = json_decode(curl_exec($ch), true);
        }
        return $response;

      } else {
        $this->Output($response['errors'][0]['error'], 'error_warning');
        exit();
      }
    }

    return $response;
  }


  private function GetDataMS($row, $needData, $dataName, $dataMS)
  {
    foreach ($dataName as $key => $value) {
      if (!isset($dataMS[$value])) {
        $dataMS[$value] = [];
      }
    }

    foreach ($needData as $key => $value) {
      if (isset($row[$value])) {
        switch (gettype($row[$value])) {
          case "string":
            if ($dataName[$key] == 'description') {
              array_push($dataMS[$dataName[$key]], str_replace('\n', '<br>', $this->db->escape($row[$value])));
              break;
            }
            array_push($dataMS[$dataName[$key]], $this->db->escape($row[$value]));
            break;
          case "integer":
            if ($dataName[$key] == 'price') {
               array_push($dataMS[$dataName[$key]], (float)$row[$value] / 100);
               break;
            }
            array_push($dataMS[$dataName[$key]], (int)$row[$value]);         
            break;
          case "double":
            if ($dataName[$key] == 'price') {
               array_push($dataMS[$dataName[$key]], (float)$row[$value] / 100);
               break;
            }
            array_push($dataMS[$dataName[$key]], (float)$row[$value]);
            break;
        }
      } else {
        if ($dataName[$key] != 'price' && $dataName[$key] != 'quantity' && $dataName[$key] != 'weight') {
          array_push($dataMS[$dataName[$key]], "");
        } else {
          array_push($dataMS[$dataName[$key]], 0);
        } 
      }
    }

    return $dataMS;
  }


  private function GetDataSQL($query, $needData)
  {
    foreach ($needData as $key => $value) {
      $dataSQL[$value] = [];
    }
    
    foreach ($query->rows as $key => $value) {
      $i = 0;
      foreach ($value as $key1 => $value1) {
        if (is_null($value1)) {
          $dataSQL[$needData[$i]][$key] = "";
          $i++;
          continue;
        }
        switch (gettype($value1)) {
          case "string":
            $dataSQL[$needData[$i]][$key] = $this->db->escape(htmlspecialchars_decode($value1));
            break;
          case "integer":
            $dataSQL[$needData[$i]][$key] = (int)htmlspecialchars_decode($value1);
            break;
          case "double":
            $dataSQL[$needData[$i]][$key] = (float)htmlspecialchars_decode($value1);
            break;
        }
        $i++;
      }
    }

    return $dataSQL;
  }


  private function GetCategoryFast($pathName, $responseCategory)
  {
    if ($pathName != '') {
      $categoryOffset = 0;
      for ($i = 0; $i < 20; $i++) { 
        $pos = strripos($pathName, '/', -1 * $categoryOffset);
        if ($pos === false) {
          $category = $pathName;
          break;
        }
        $subCategoryMS = substr($pathName, $pos + 1);
        $categoryOffset = (strlen($pathName) - $pos) + 1;
        if (strpos($responseCategory, '"name" : "' . $subCategoryMS . '"') != false) {
          $subCategoryMS = $subCategoryMS;
          $category = $subCategoryMS;
          break;
        }
      }
    } else {
      $category = "";
    }
    return $category;
  }


  private function GetMetaTag($tag, $replaceVar, $replaceData)
  {
    foreach ($replaceData as $key => $value) {
      $tag = str_replace(sprintf("[%s]", $replaceVar[$key]), $value, $tag);
    }
    
    return $tag;
  }


  private function CurlInit($headers)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    return $ch;
  }


  private function Translit($s) {
    $s = (string) $s;
    $s = strip_tags($s);
    $s = str_replace(array("\n", "\r"), " ", $s);
    $s = preg_replace("/\s+/", ' ', $s);
    $s = trim($s);
    $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
    $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
    $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s);
    $s = str_replace(" ", "-", $s);
    return $s;
  }


  private function InitialDataMS($needData)
  {
    $dataMS = [];
    foreach ($needData as $key => $value) {
      $dataMS[$value] = [];
    }
    return $dataMS;
  }


  private function CurlRedirExec($ch) {
    static $curl_loops = 0;
    static $curl_max_loops = 20;
    if ($curl_loops >= $curl_max_loops) {
      $curl_loops = 0;
      return false;
    }
   
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    $dd = explode("\r\n\r\n", $data);
    $header = $dd[0];
    $data = @$dd[1];
    
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($http_code == 301 || $http_code == 302) {
      $matches = [];
      preg_match('/Location:(.*?)\n/', $header, $matches);
      $url = @parse_url(trim(array_pop($matches)));
      if (!$url) {
        $curl_loops = 0;
        return $data;
      }
      
      $last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
      if (empty($url['scheme'])) {
        $url['scheme'] = $last_url['scheme'];
      }
      if (empty($url['host'])) {
        $url['host'] = $last_url['host'];
      }
      if (empty($url['path'])) {
        $url['path'] = $last_url['path'];
      }
      $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query'] ? '?' . $url['query'] : '');
      return $new_url;
    } else {
      $curl_loops = 0;
      return $data;
    }
  }


  private function GetBundleQuantity($dataMS, $ch)
  {
    //Занесение количества товаров в массив
    foreach ($dataMS['quantity'] as $key => $value) {
      if (gettype($value) == 'string') {
        curl_setopt($ch, CURLOPT_URL, $value);
        $responseComponents = json_decode(curl_exec($ch), true);
        $responseComponents = $this->CheckResponse($responseComponents, $ch);
        $ratio = [];
        foreach ($responseComponents['rows'] as $value1) {
          $componentID = $value1['assortment']['meta']['href'];
          $componentID = strrchr($componentID, '/');
          $componentID = substr($componentID, 1);
          $componentQuantity = $value1['quantity'];
          $componentIndex = array_search($componentID, $dataMS['id'], true);
          if ($componentIndex === false) {
            curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=id=' . $componentID);
            $responseProd = json_decode(curl_exec($ch), true);
            $responseProd = $this->CheckResponse($responseProd, $ch);
            if (isset($responseProd['rows'][0]['quantity'])) {
              if ($responseProd['rows'][0]['quantity'] < 0) {
                array_push($ratio, 0);
              } else {
                array_push($ratio, floor($responseProd['rows'][0]['quantity'] / $componentQuantity));
              }
            } else {
              array_push($ratio, 0);
            }   
            
          } else {
            if ($dataMS['quantity'][$componentIndex] < 0) {
              array_push($ratio, 0);
            } else {
              array_push($ratio, floor($dataMS['quantity'][$componentIndex] / $componentQuantity));
            }
            
          }
        }
        $dataMS['quantity'][$key] = min($ratio);
      }
    }

    return $dataMS;
  }


  private function GetLanguageID()
  {
    $query = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = 'ru-ru' ");
    return $query->row['language_id'];
  }
  

  private function DuplicateCheck($data, $type, $bindingName=null)
  {
    $duplicate = '';
    if (count($data[0]) != count(array_unique($data[0]))) {
      if ($bindingName == 0 || $type != 'product') {
        // Только binding или категория
        foreach (array_count_values($data[0]) as $key => $value) {
          if ($value > 1) {
            $duplicate .= $key . '; ';
          }
        }
      } else {
        // Binding + наименование
        foreach (array_count_values($data[0]) as $key => $value) {
          $names = [];
          foreach (array_keys($data[0], $key) as $value1) {
            if (in_array($data[1][$value1], $names)) {
              $duplicate .= sprintf("%s %s; ", $data[0][$value1], $data[1][$value1]);
              break;
            } else {
              array_push($names, $data[1][$value1]);
            }
          }
        }
      }
    }

    return $duplicate;
  }

  private function Output($text, $type)
  {
    if ($type == 'error_warning') {
      $this->logText .= $text;
      $this->LogWrite();
    }
    if ($this->cron) {
      echo $text;
    } else {
      $this->session->data[$type] = $text;
      $this->response->redirect($this->redirectURL); 
    }
  }


  //=========================Категории=========================

  public function CategoryAdd() {
    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }

    $this->logText = date('H:i:s d.m.Y') . ' Добавление категорий' . PHP_EOL;

    $this->db->query("set session wait_timeout=28800");
    $startVars = $this->GetStartVars(["cat_meta_title", "cat_meta_desc", "cat_meta_keyword", "cat_offset", "meta_cat_update", "from_group"]);

    if ($startVars['meta_cat_update'] == 1) {
      $metaTitle = $this->db->escape($startVars['cat_meta_title']);
      $metaDesc = $this->db->escape($startVars['cat_meta_desc']);
      $metaKeyword = $this->db->escape($startVars['cat_meta_keyword']);
    }
    
    $languageID = $this->GetLanguageID();

    $dataMS = $this->InitialDataMS(['name']);

    $ch = $this->CurlInit($startVars['headers']);

    if ($this->cron) {
      $offset = 0;
    } else {
      $offset = $startVars['cat_offset'];
    }

    do {
      //Получение категорий по curl

      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/productfolder?offset=' . $offset);

      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);
      
      $responseCategory = curl_exec($ch);
      $responseCategory = $this->CheckResponse($responseCategory, $ch);

      //Занесение данных в массивы
      foreach ($response['rows'] as $key => $row) {

        // Синхронизация категорий и товаров из группы
        if (isset($startVars['from_group'])) {
          $groups = explode('/', $row['pathName']);
          foreach ($groups as $key1 => $value) {
            if ($groups[$key1] != '' && strpos($responseCategory, '"name" : "' . $groups[$key1] . '"') === false) {
              $groups[$key1+1] = $groups[$key1] . '/' . $groups[$key1+1];
              unset($groups[$key1]);
            }
          }

          if ($row['name'] != $startVars['from_group'] 
            && in_array($startVars['from_group'], $groups) === false) {
            continue;
          }
        }

        $dataMS = $this->GetDataMS($row, ['name'], ['name'], $dataMS);
      }

      if (!$this->cron) {
        break;
      }

      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    curl_close($ch);

    // Проверка на дубликаты
    $duplicate = $this->DuplicateCheck([$dataMS['name']], 'category');
    if ($duplicate != '') {
      $this->Output('Ошибка! В Моем Складе есть категории-дубликаты: ' . $duplicate, 'error_warning');
      exit();
    }

    //Добавление в массивы имен и id всех категорий в БД
    $query = $this->db->query("SELECT name FROM " . DB_PREFIX . "category_description");

    $dataSQL = $this->GetDataSQL($query, ['name']);

    //Удаление категорий, которые уже есть в БД
    foreach ($dataMS['name'] as $key => $value) {
      $find = array_search($value, $dataSQL['name'], true);
      if ($find !== false) {
        unset($dataMS['name'][$key]);
      }
    }

    $dataMS['name'] = array_values($dataMS['name']);

    //Формирование запросов
    $query = $this->db->query("SELECT MAX(category_id) FROM " . DB_PREFIX . "category");
    $lastID = $query->row['MAX(category_id)'];
    $date_added = date('Y-m-d H:i:s');
    $insertCategory = [];
    $insertCategoryDescription = [];
    $insertCategoryPath = [];
    $insertCategoryToStore = [];
    $insertSeoUrl = [];
    $insertWhere = [];
    $added = [];
    foreach ($dataMS['name'] as $key => $value) {
      $lastID++;
      array_push($insertWhere, $lastID);
      $keyword = $this->Translit($value);
      array_push($insertCategory, "('$lastID', '1', '1', '1', '1', '$date_added', '$date_added')");
      
      if ($startVars['meta_cat_update'] == 1) {
        $metaTitleNew = $this->GetMetaTag($metaTitle, ['name'], [$value]);
        $metaDescNew = $this->GetMetaTag($metaDesc, ['name'], [$value]);
        $metaKeywordNew = $this->GetMetaTag($metaKeyword, ['name'], [$value]);
      } else {
        $metaTitleNew = "";
        $metaDescNew = "";
        $metaKeywordNew = "";
      }

      array_push($insertCategoryDescription, sprintf("('%s', '%s', '%s', '%s', '%s', '%s')", $lastID, $languageID, htmlspecialchars($value), htmlspecialchars($metaTitleNew), htmlspecialchars($metaDescNew), htmlspecialchars($metaKeywordNew)));
      array_push($added, $value);
      array_push($insertCategoryPath, "('$lastID', '$lastID', '0')");
      array_push($insertCategoryToStore, "('$lastID', '0')");
      if ($this->version == "2.3") {
        array_push($insertSeoUrl, "('category_id=$lastID', '$keyword')");
      } else {
        array_push($insertSeoUrl, "('0', '$languageID', 'category_id=$lastID', '$keyword')"); 
      }
    }

    //Отправление запросов
    $categoryAddedNum = count($insertCategory);
    $insertCategory = implode(', ', $insertCategory);
    $insertCategoryDescription = implode(', ', $insertCategoryDescription);
    $insertCategoryPath = implode(', ', $insertCategoryPath);
    $insertCategoryToStore = implode(', ', $insertCategoryToStore);
    $insertSeoUrl = implode(', ', $insertSeoUrl);

    if ($insertCategory != "") {
      $this->db->query("INSERT INTO " . DB_PREFIX . "category (`category_id`, `top`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES $insertCategory");
      $this->db->query("INSERT INTO " . DB_PREFIX . "category_description (`category_id`, `language_id`, `name`, `meta_title`, `meta_description`, `meta_keyword`) VALUES $insertCategoryDescription");
      $this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store (`category_id`, `store_id`) VALUES $insertCategoryToStore");

      if ($this->version == "2.3") {
        $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias (`query`, `keyword`) VALUES $insertSeoUrl");
      } else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url (`store_id`, `language_id`, `query`, `keyword`) VALUES $insertSeoUrl");
      }
      
      $this->db->query("INSERT INTO " . DB_PREFIX . "category_path (`category_id`, `path_id`, `level`) VALUES $insertCategoryPath"); 
    }

    $this->logText .= 'Добавлено категорий: ' . $categoryAddedNum . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    $this->LogWrite();
    $this->Output('Успешно. Категорий добавлено: ' . $categoryAddedNum, 'success');
  }


  public function CategoryUpdate() {
    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }
    
    $this->logText = date('H:i:s d.m.Y') . ' Обновление категорий' . PHP_EOL;
    $this->db->query("set session wait_timeout=28800");
    $startVars = $this->GetStartVars(["meta_cat_update", "cat_meta_title", "cat_meta_desc", "cat_meta_keyword", "cat_offset", "from_group"]);

    if ($startVars['meta_cat_update'] == 1) {
      $metaTitle = $this->db->escape($startVars['cat_meta_title']);
      $metaDesc = $this->db->escape($startVars['cat_meta_desc']);
      $metaKeyword = $this->db->escape($startVars['cat_meta_keyword']);
    }

    $dataMS = $this->InitialDataMS(['name', 'pathName', 'category', 'skip']);

    $ch = $this->CurlInit($startVars['headers']);

    if ($this->cron) {
      $offset = 0;
    } else {
      $offset = $startVars['cat_offset'];
    }

    $key = 0;
    do {
      //Получение категорий через curl
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/productfolder?offset=' . $offset);

      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);

      $responseCategory = curl_exec($ch);      
      $responseCategory = $this->CheckResponse($responseCategory, $ch);

      //Занесение данных Мой Склад в массивы
      foreach ($response['rows'] as $row) {
        $dataMS['skip'][$key] = false;
        // Синхронизация категорий и товаров из группы
        if (isset($startVars['from_group'])) {
          $groups = explode('/', $row['pathName']);
          foreach ($groups as $key1 => $value) {
            if ($groups[$key1] != '' && strpos($responseCategory, '"name" : "' . $groups[$key1] . '"') === false) {
              $groups[$key1+1] = $groups[$key1] . '/' . $groups[$key1+1];
              unset($groups[$key1]);
            }
          }

          if ($row['name'] != $startVars['from_group'] 
            && in_array($startVars['from_group'], $groups) === false) {
            $dataMS['skip'][$key] = true;
          }
        }

        $dataMS = $this->GetDataMS($row, ['name', 'pathName'], ['name', 'pathName'], $dataMS);
        $dataMS['category'][$key] = $this->GetCategoryFast($dataMS['pathName'][$key], $responseCategory);

        $key++;
      }

      if (!$this->cron) {
        break;
      }

      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    curl_close($ch);

    $duplicate = $this->DuplicateCheck([$dataMS['name']], 'category');
    if ($duplicate != '') {
      $this->Output('Ошибка! В Моем Складе есть категории-дубликаты: ' . $duplicate, 'error_warning');
      exit();
    }

    $languageID = $this->GetLanguageID();
    //Занесение в массивы имен и id всех категорий в БД
    $query = $this->db->query("SELECT category_id, name FROM " . DB_PREFIX . "category_description WHERE `language_id` = $languageID");

    $dataSQL = $this->GetDataSQL($query, ['category_id', 'name']);

    $duplicate = $this->DuplicateCheck([$dataSQL['name']], 'category');
    if ($duplicate != '') {
      $this->Output('Ошибка! В Опенкарт есть категории-дубликаты: ' . $duplicate, 'error_warning');
      exit();
    }

    //Определение id категорий в БД 
    //если категория нет в БД, то в запрос не включается
    $dataMS['id'] = [];
    foreach ($dataMS['name'] as $key => $value) {
      $keySQL = array_search($value, $dataSQL['name'], true);
      if (is_int($keySQL)) {
        array_push($dataMS['id'], $dataSQL['category_id'][$keySQL]);
      } else {
        unset($dataMS['name'][$key]);
        unset($dataMS['category'][$key]);
        unset($dataMS['skip'][$key]);
      }
    }

    $dataMS['name'] = array_values($dataMS['name']);
    $dataMS['category'] = array_values($dataMS['category']);
    $dataMS['skip'] = array_values($dataMS['skip']);

    //Определение id родительской категории в БД
    $dataMS['parent_id'] = [];
    foreach ($dataMS['category'] as $value) {
      $key = array_search($value, $dataSQL['name'], true);
      if ($key !== false) {
        array_push($dataMS['parent_id'], $dataSQL['category_id'][$key]);
      } else {
        array_push($dataMS['parent_id'], 0);
      }
    }

    //Получение id родительских категорий в БД
    $implodeIdMS = "'" . implode("', '", $dataMS['id']) . "'";
    $query = $this->db->query("SELECT parent_id,
      " . DB_PREFIX . "category_description.meta_title AS meta_title,
      " . DB_PREFIX . "category_description.meta_description AS meta_description,
      " . DB_PREFIX . "category_description.meta_keyword AS meta_keyword
      FROM " . DB_PREFIX . "category
      INNER JOIN " . DB_PREFIX . "category_description USING (`category_id`)  
      WHERE `category_id` IN ($implodeIdMS) ORDER BY FIELD (category_id, $implodeIdMS)");

    //Составление запросов
    $updateCategoryCase1 = '';
    $updateCategoryCase2 = '';
    $updateCategoryDescriptionCase1 = '';
    $updateCategoryDescriptionCase2 = '';
    $updateCategoryDescriptionCase3 = '';
    $whereDescription = [];
    $updateWhere = [];
    $dateModified = date("Y-m-d H:i:s");
    $insertCategoryPath = [];
    $categoryUpdatedNum = [];
    $added = [];

    foreach ($query->rows as $key => $value) {

      if ($dataMS['skip'][$key])
        continue;

      //Таблица category
      if ($value['parent_id'] != $dataMS['parent_id'][$key]) {
        $updateCategoryCase1 .= sprintf("WHEN `category_id` = %s THEN '%s' ", $dataMS['id'][$key], $dataMS['parent_id'][$key]);
        $updateCategoryCase2 .= sprintf("WHEN `category_id` = %s THEN '%s' ", $dataMS['id'][$key], $dateModified);
        array_push($updateWhere, $dataMS['id'][$key]);
        array_push($categoryUpdatedNum, $dataMS['id'][$key]);
        array_push($added, $dataMS['name'][$key]);

        //Таблица category_to_path
        $resultParent = [];
        $resultLevel = [];
        $parentId = $dataMS['parent_id'][$key];
        $level = 0;
        array_push($resultParent, $dataMS['id'][$key]);
      
        while ($parentId != 0) {
          array_push($resultParent, $parentId);
          array_push($resultLevel, $level);
          $level++;
          $parentId = $dataMS['parent_id'][array_search($parentId, $dataMS['id'], true)];
        }
        array_push($resultLevel, $level++);
        rsort($resultLevel);
      
        foreach ($resultParent as $key1 => $value1) {
          array_push($insertCategoryPath, sprintf("(%s, %s, %s)", $resultParent[0], $value1, $resultLevel[$key1]));
        }
      }

      if ($startVars['meta_cat_update'] == 1) {
        $metaTitleNew = $this->GetMetaTag($metaTitle, ['name'], [$dataMS['name'][$key]]);
        $metaDescNew = $this->GetMetaTag($metaDesc, ['name'], [$dataMS['name'][$key]]);
        $metaKeywordNew = $this->GetMetaTag($metaKeyword, ['name'], [$dataMS['name'][$key]]);

        $value['meta_title'] = $this->db->escape(htmlspecialchars_decode($value['meta_title']));
        $value['meta_description'] = $this->db->escape(htmlspecialchars_decode($value['meta_description']));
        $value['meta_keyword'] = $this->db->escape(htmlspecialchars_decode($value['meta_keyword']));

        if ($value['meta_title'] != $metaTitleNew || $value['meta_description'] != $metaDescNew ||
        $value['meta_keyword'] != $metaKeywordNew) {
          $updateCategoryDescriptionCase1 .= sprintf("WHEN `category_id` = '%s' THEN '%s' ", $dataMS['id'][$key], htmlspecialchars($metaTitleNew));
          $updateCategoryDescriptionCase2 .= sprintf("WHEN `category_id` = '%s' THEN '%s' ", $dataMS['id'][$key], htmlspecialchars($metaDescNew));
          $updateCategoryDescriptionCase3 .= sprintf("WHEN `category_id` = '%s' THEN '%s' ", $dataMS['id'][$key], htmlspecialchars($metaKeywordNew));
          
          array_push($whereDescription, $dataMS['id'][$key]);
          array_push($categoryUpdatedNum, $dataMS['id'][$key]);
          array_push($added, $dataMS['name'][$key]);
        }
      }
    }

    //Отправка запросов
    if ($updateCategoryCase1 != "") {
      $insertCategoryPath = implode(", ", $insertCategoryPath);
      $updateWhere = implode(", ", $updateWhere);
      $this->db->query("UPDATE " . DB_PREFIX . "category SET 
      parent_id = CASE " . $updateCategoryCase1 . "END,
      date_modified = CASE " . $updateCategoryCase2 . "END
      WHERE `category_id` IN ($updateWhere)");

      $this->db->query("DELETE FROM " . DB_PREFIX . "category_path WHERE `category_id` IN ($updateWhere)");
      $this->db->query("INSERT INTO " . DB_PREFIX . "category_path (category_id, path_id, level) VALUES $insertCategoryPath");
    }

    if ($updateCategoryDescriptionCase1 != '') {
      $whereDescription = implode(", ", $whereDescription);
      $this->db->query("UPDATE " . DB_PREFIX . "category_description SET 
      meta_title = CASE " . $updateCategoryDescriptionCase1 . "END,
      meta_description = CASE " . $updateCategoryDescriptionCase2 . "END,
      meta_keyword = CASE " . $updateCategoryDescriptionCase3 . "END
      WHERE `category_id` IN ($whereDescription)");
    }

    $categoryUpdatedNum = array_unique($categoryUpdatedNum);
    $added = array_unique($added);
    
    $this->logText .= 'Обновлено категорий: ' . count($categoryUpdatedNum) . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    
    $this->LogWrite();
    $this->Output('Успешно. Категорий обновлено: ' . count($categoryUpdatedNum), 'success');
  }


  //=================Товары================


  public function ProductAdd() {

    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }

    $this->logText = date('H:i:s d.m.Y') . ' Добавление товаров' . PHP_EOL;

    $this->db->query("set session wait_timeout=28800");
    $startVars = $this->GetStartVars(["binding", "sale_price", "product_offset", "binding_name", "prod_meta_title", "prod_meta_desc", "prod_meta_keyword", "meta_prod_update", "url_update", "cat_update", "desc_update", "stock_store", "sku_update", "weight_update", "weight_unity", "manufacturer_update", "manufacturer", "stock_status", "stock_status_update", "from_group"]);

    if (!isset($startVars['manufacturer'])) {
      $startVars['manufacturer'] = '';
    }

    if ($startVars['meta_prod_update'] == 1) {
      $metaTitle = (isset($startVars['prod_meta_title']) ? $this->db->escape($startVars['prod_meta_title']) : '');
      $metaDesc = (isset($startVars['prod_meta_desc']) ? $this->db->escape($startVars['prod_meta_desc']) : '');
      $metaKeyword = (isset($startVars['prod_meta_keyword']) ? $this->db->escape($startVars['prod_meta_keyword']) : '');
    }

    $languageID = $this->GetLanguageID();

    $dataMS = $this->InitialDataMS(['binding', 'name', 'id', 'description', 'quantity', 'pathName', 'price', 'category', 'sku', 'weight', 'manufacturer', 'manufacturer_id']);

    $ch = $this->CurlInit($startVars['headers']);

    if ($this->cron) {
      $offset = 0;
    } else {
      $offset = $startVars['product_offset'];
    }

    $bindingOC = substr($startVars['binding'], 0, strpos($startVars['binding'], '_'));
    $bindingMS = substr($startVars['binding'], strpos($startVars['binding'], '_') + 1);

    $key = 0;
    do {
      if (!isset($startVars['stock_store'])) {
        curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle&offset=' . $offset);
      } else {
        $url = "https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle&offset=" . $offset . "&filter=";
        foreach ($startVars['stock_store'] as $storeUrl) {
          $url .= "stockStore=" . $storeUrl . ";";
        }
        curl_setopt($ch, CURLOPT_URL, $url);
      }

      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);

      //Получение категорий по curl
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/productfolder');
      $responseCategory = curl_exec($ch);
      $responseCategory = $this->CheckResponse($responseCategory, $ch);
      

      //Занесение данных Мой Склад в массивы
      foreach ($response['rows'] as $row) {
        
        // Синхронизация категорий и товаров из группы
        if (isset($startVars['from_group'])) {
          $groups = explode('/', $row['pathName']);
          foreach ($groups as $key1 => $value) {
            if ($groups[$key1] != '' && strpos($responseCategory, '"name" : "' . $groups[$key1] . '"') === false) {
              $groups[$key1+1] = $groups[$key1] . '/' . $groups[$key1+1];
              unset($groups[$key1]);
            }
          }

          if (in_array($startVars['from_group'], $groups) === false) {
            continue;
          }
        }

        $dataMS = $this->GetDataMS($row, [$bindingMS, 'name', 'id', 'description', 'quantity', 'pathName', 'article', 'weight'], ['binding', 'name', 'id', 'description', 'quantity', 'pathName', 'sku', 'weight'], $dataMS);

        if (isset($row['components'])) {
          $dataMS['quantity'][$key] = $this->db->escape($row['components']['meta']['href']);
        }

        if (isset($row['salePrices'][$startVars['sale_price']])) {
          $dataMS = $this->GetDataMS($row['salePrices'][$startVars['sale_price']], ['value'], ['price'], $dataMS);
        } else {
          $dataMS = $this->GetDataMS($row['salePrices'][0], ['value'], ['price'], $dataMS);
        }
        $dataMS['category'][$key] = $this->GetCategoryFast($dataMS['pathName'][$key], $responseCategory);

        // Производители
        if (isset($startVars['manufacturer_update'])) {
          $dataMS['manufacturer'][$key] = '';
          if (isset($row['attributes'])) {
            foreach ($row['attributes'] as $key1 => $value1) {
              if ($value1['name'] == $startVars['manufacturer']) {
                $dataMS['manufacturer'][$key] = $this->db->escape($value1['value']);
                break;
              }
            }
          }
        }

        $key++;
      }

      if (!$this->cron) {
        break;
      } 

      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    $dataMS = $this->GetBundleQuantity($dataMS, $ch);

    curl_close($ch);

    // Проверка на дубликаты
    $duplicate = $this->DuplicateCheck([$dataMS['binding'], $dataMS['name']], 'product', $startVars['binding_name']);
    if ($duplicate != '') {
      $this->Output('Ошибка! В Моем Складе есть товары-дубликаты: ' . $duplicate, 'error_warning');
      exit();
    }

    // Удаление товаров, которые уже есть в БД
    $bindingMSImplode = htmlspecialchars("'" . implode("', '", $dataMS['binding']) . "'");
    $query = $this->db->query("SELECT name, $bindingOC FROM " . DB_PREFIX . "product 
      INNER JOIN " . DB_PREFIX . "product_description USING (`product_id`) 
      WHERE `$bindingOC` IN ($bindingMSImplode)");

    $dataSQL = $this->GetDataSQL($query, ['name', 'binding']);

    if ($startVars['binding_name'] == 1) {
      // Модель + наименование
      foreach ($dataMS['binding'] as $key => $value) {
        foreach (array_keys($dataSQL['binding'], $value) as $value1) {
           if ($dataMS['name'][$key] == $dataSQL['name'][$value1]) {
            unset($dataMS['binding'][$key], $dataMS['name'][$key], $dataMS['description'][$key], 
              $dataMS['quantity'][$key], $dataMS['price'][$key], $dataMS['category'][$key], $dataMS['sku'][$key], $dataMS['weight'][$key], $dataMS['manufacturer'][$key]);
            break;
           }
        } 
      }
    } else {
      // Только модель
      foreach ($dataMS['binding'] as $key => $value) {
        if (in_array($value, $dataSQL['binding'], true)) {
          unset($dataMS['binding'][$key], $dataMS['name'][$key], $dataMS['description'][$key], 
              $dataMS['quantity'][$key], $dataMS['price'][$key], $dataMS['category'][$key], $dataMS['sku'][$key], $dataMS['weight'][$key], $dataMS['manufacturer'][$key]);
        }
      }
    }
    
    $dataMS['binding'] = array_values($dataMS['binding']);
    $dataMS['name'] = array_values($dataMS['name']);
    $dataMS['description'] = array_values($dataMS['description']);
    $dataMS['quantity'] = array_values($dataMS['quantity']);
    $dataMS['price'] = array_values($dataMS['price']);
    $dataMS['category'] = array_values($dataMS['category']);
    $dataMS['sku'] = array_values($dataMS['sku']);
    $dataMS['weight'] = array_values($dataMS['weight']);
    $dataMS['manufacturer'] = array_values($dataMS['manufacturer']);

    // Получение всех имен категорий в БД
    $query = $this->db->query("SELECT category_id, name  FROM " . DB_PREFIX . "category_description");
    $dataSQL = $this->GetDataSQL($query, ['category_id', 'name']);

    //Определение id категорий в БД
    foreach ($dataMS['category'] as $key => $value) {
      $sqlKey = array_search($value, $dataSQL['name'], true);
      if (is_int($sqlKey)) {
        $dataMS['categoryID'][$key] = $dataSQL['category_id'][$sqlKey];
      } else {
        $dataMS['categoryID'][$key] = '';
      }
    }

    // Производители
    $query = $this->db->query("SELECT manufacturer_id, name  FROM " . DB_PREFIX . "manufacturer");
    $manufacturerDataSQL = $this->GetDataSQL($query, ['manufacturer_id', 'name']);

    // Формирование запросов
    $query = $this->db->query("SELECT MAX(product_id) FROM " . DB_PREFIX . "product");
    $lastID = $query->row['MAX(product_id)'];
    $date_added = date('Y-m-d H:i:s');
    $stockStatusID = $startVars['stock_status'];
    $insertProduct = [];
    $insertProductDescription = [];
    $insertProductToCategory = [];
    $insertProductToStore = [];
    $insertSeoUrl = [];
    $added = [];
    $manufacturerAdded = ['id' => [], 'name' => []];
    $insertManufacturer = [];
    $insertManufacturerToStore = [];
    $insertManufacturerURL = [];

    // Проверка существования таблицы manufacturer_description
    if (isset($startVars['manufacturer_update'])) {
      $query = $this->db->query("SHOW TABLES FROM `" . DB_DATABASE . "` LIKE '" . DB_PREFIX . "manufacturer_description'");
      if (count($query->rows) != 0) {
        $ocstore = true;
        $insertManufacturerDesc = [];
      } else {
        $ocstore = false;
      }
    }

    if (!isset($startVars['weight_update'])) {
      $startVars['weight_unity'] = 0;
    }

    if (count($manufacturerDataSQL['manufacturer_id']) == 0) {
      $lastManufacturerId = 0;
    } else {
      $lastManufacturerId = max($manufacturerDataSQL['manufacturer_id']);
    }

    // Проверка существования столбца main_category
    $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_to_category` LIKE 'main_category'");
    if (isset($query->row['Field'])) {
      $mainCategory = true;
    } else {
      $mainCategory = false;
    }

    foreach ($dataMS['binding'] as $key => $value) {
      $lastID++;
      $keyword = $this->Translit($dataMS['name'][$key]);
      if (!isset($startVars['sku_update'])) {
        $dataMS['sku'][$key] = '';
      }
      if (!isset($startVars['weight_update'])) {
        $dataMS['weight'][$key] = 0;
      }
      if (!isset($startVars['stock_status_update'])) {
        $stockStatusID = 0;
      }

      // Производители
      if (isset($startVars['manufacturer_update']) && $dataMS['manufacturer'][$key] != '') {
        $manufacturerKey = array_search($dataMS['manufacturer'][$key], $manufacturerDataSQL['name'], true);
        $manufacturerAddedKey = array_search($dataMS['manufacturer'][$key], $manufacturerAdded['name'], true);
        if ($manufacturerKey !== false) {
          $dataMS['manufacturer_id'][$key] = $manufacturerDataSQL['manufacturer_id'][$manufacturerKey];
        } else {
          if ($manufacturerAddedKey !== false) {
            $dataMS['manufacturer_id'][$key] = $manufacturerAdded['id'][$manufacturerAddedKey];
          } else {
            $lastManufacturerId++;
            $dataMS['manufacturer_id'][$key] = $lastManufacturerId;
            array_push($manufacturerAdded['id'], $lastManufacturerId);
            array_push($manufacturerAdded['name'], $dataMS['manufacturer'][$key]);
            array_push($insertManufacturer, sprintf("('%s', '%s', '0')", $lastManufacturerId, $dataMS['manufacturer'][$key]));
            array_push($insertManufacturerToStore, sprintf("('%s', '0')", $lastManufacturerId));
            $manufacturerKeyword = $this->Translit($dataMS['manufacturer'][$key]);

            if ($this->version == '2.3') {
              array_push($insertManufacturerURL, "('manufacturer_id=$lastManufacturerId', '$manufacturerKeyword')");
            } else {
              array_push($insertManufacturerURL, "('0', '$languageID', 'manufacturer_id=$lastManufacturerId', '$manufacturerKeyword')");
            }
            
            if ($ocstore) {
              if ($this->version == '2.3') {
                array_push($insertManufacturerDesc, sprintf("('%s', '%s', '%s')", $lastManufacturerId, $languageID, $dataMS['manufacturer'][$key]));
              } else {
                array_push($insertManufacturerDesc, sprintf("('%s', '%s')", $lastManufacturerId, $languageID));
              }
            }
          }
        }
      } else {
        $dataMS['manufacturer_id'][$key] = 0;
      }

      // Товары
      if ($bindingOC != 'sku') {
        array_push($insertProduct, sprintf("('%s', '%s', '%s', '%s', '1', '%s', '%s', '%s', '%s', '%s', '%s', '$stockStatusID')", $lastID, htmlspecialchars($value), $dataMS['quantity'][$key], $dataMS['price'][$key], $date_added, $date_added, $dataMS['sku'][$key], $dataMS['weight'][$key], $startVars['weight_unity'], $dataMS['manufacturer_id'][$key]));
      } else {
        array_push($insertProduct, sprintf("('%s', '%s', '%s', '%s', '1', '%s', '%s', '%s', '%s', '%s', '$stockStatusID')", $lastID, htmlspecialchars($value), $dataMS['quantity'][$key], $dataMS['price'][$key], $date_added, $date_added, $dataMS['weight'][$key], $startVars['weight_unity'], $dataMS['manufacturer_id'][$key]));
      }
      
      
      if ($startVars['meta_prod_update'] == 1) {
        $metaTitleNew = $this->GetMetaTag($metaTitle, ['name', 'price'], [$dataMS['name'][$key], $dataMS['price'][$key]]);
        $metaDescNew = $this->GetMetaTag($metaDesc, ['name', 'price'], [$dataMS['name'][$key], $dataMS['price'][$key]]);
        $metaKeywordNew = $this->GetMetaTag($metaKeyword, ['name', 'price'], [$dataMS['name'][$key], $dataMS['price'][$key]]);
      } else {
        $metaTitleNew = "";
        $metaDescNew = "";
        $metaKeywordNew = "";
      }

      if (!isset($startVars['desc_update'])) {
        $dataMS['description'][$key] = '';
      }

      array_push($insertProductDescription, sprintf("('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $lastID, $languageID, htmlspecialchars($dataMS['name'][$key]), htmlspecialchars($dataMS['description'][$key]), htmlspecialchars($metaTitleNew), htmlspecialchars($metaDescNew), htmlspecialchars($metaKeywordNew)));

      if (isset($startVars['cat_update'])) {
        if ($mainCategory) {
          array_push($insertProductToCategory, sprintf("('%s', '%s', '1')", $lastID, $dataMS['categoryID'][$key]));
        } else {
          array_push($insertProductToCategory, sprintf("('%s', '%s')", $lastID, $dataMS['categoryID'][$key]));
        }
      } 

      array_push($insertProductToStore, "('$lastID')");
      if (isset($startVars['url_update'])) {
        if ($this->version == "2.3") {
          array_push($insertSeoUrl, "('product_id=$lastID', '$keyword')");
        } else {
          array_push($insertSeoUrl, "('0', '$languageID', 'product_id=$lastID', '$keyword')");
        }
      }

      array_push($added, sprintf('%s %s', $value, $dataMS['name'][$key]));
    }

    //Отправление запросов
    $productAddedNum = count($insertProduct);
    $insertProduct = implode(', ', $insertProduct);
    $insertProductDescription = implode(', ', $insertProductDescription);
    $insertProductToStore = implode(', ', $insertProductToStore);
    $insertProductToCategory = implode(', ', $insertProductToCategory);
    $insertSeoUrl = implode(', ', $insertSeoUrl);

    if (count($insertManufacturer) != 0) {
      $insertManufacturer = implode(", ", $insertManufacturer);
      $insertManufacturerToStore = implode(", ", $insertManufacturerToStore);
      $insertManufacturerURL = implode(", ", $insertManufacturerURL);
      $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer (`manufacturer_id`, `name`, `sort_order`) VALUES $insertManufacturer");
      $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store (`manufacturer_id`, `store_id`) VALUES $insertManufacturerToStore");

      if ($this->version == '2.3') {
        $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias (`query`, `keyword`) VALUES $insertManufacturerURL");
      } else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url (`store_id`, `language_id`, `query`, `keyword`) VALUES $insertManufacturerURL");
      }
      
      if ($ocstore) {
        $insertManufacturerDesc = implode(", ", $insertManufacturerDesc);
        if ($this->version == '2.3') {
          $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description (`manufacturer_id`, `language_id`, `name`) VALUES $insertManufacturerDesc");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description (`manufacturer_id`, `language_id`) VALUES $insertManufacturerDesc");
        }
      }
    }

    if ($insertProduct != "") {
      if ($bindingOC != 'sku') {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product (`product_id`, `$bindingOC`, `quantity`, `price`, `status`, `date_added`, `date_modified`, `sku`, `weight`, `weight_class_id`, `manufacturer_id`, `stock_status_id`) VALUES $insertProduct");
      } else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "product (`product_id`, `$bindingOC`, `quantity`, `price`, `status`, `date_added`, `date_modified`, `weight`, `weight_class_id`, `manufacturer_id`, `stock_status_id`) VALUES $insertProduct");
      }
      
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_description (`product_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`) VALUES $insertProductDescription");
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store (`product_id`) VALUES $insertProductToStore");
      if (isset($startVars['cat_update'])) {
        if ($mainCategory) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category (`product_id`, `category_id`, `main_category`) VALUES $insertProductToCategory");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category (`product_id`, `category_id`) VALUES $insertProductToCategory");
        }
      }
      if (isset($startVars['url_update'])) {
        if ($this->version == "2.3") {
          $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias (`query`, `keyword`) VALUES $insertSeoUrl");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url (`store_id`, `language_id`, `query`, `keyword`) VALUES $insertSeoUrl");
        }    
      }
    }
    
    $added = array_unique($added);
    
    $this->logText .= 'Добавлено товаров: ' . $productAddedNum . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    
    $this->LogWrite();
    $this->Output('Успешно. Товаров добавлено: ' . $productAddedNum, 'success');
  }


  public function ProductUpdate() {
    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }

    $this->logText = date('H:i:s d.m.Y') . ' Обновление товаров' . PHP_EOL;
    $this->db->query("set session wait_timeout=28800");
    $startVars = $this->GetStartVars(["binding", "sale_price", "product_offset", "binding_name", "prod_meta_title", "prod_meta_desc", "prod_meta_keyword", "meta_prod_update", "desc_update", "name_update", "url_update", "cat_update", "sku_update", "weight_update", "weight_unity", "manufacturer_update", "manufacturer", "stock_status", "stock_status_update", "from_group"]);

    if (!isset($startVars['manufacturer'])) {
      $startVars['manufacturer'] = '';
    }
    
    $bindingOC = substr($startVars['binding'], 0, strpos($startVars['binding'], '_'));
    $bindingMS = substr($startVars['binding'], strpos($startVars['binding'], '_') + 1);

    $languageID = $this->GetLanguageID();

    $dataMS = $this->InitialDataMS(['binding', 'name', 'description', 'pathName', 'price', 'category', 'sku', 'weight', 'manufacturer', 'manufacturer_id', 'product_id', 'category_id']);

    if ($startVars['meta_prod_update'] == 1) {
      $metaTitle = (isset($startVars['prod_meta_title']) ? $this->db->escape($startVars['prod_meta_title']) : '');
      $metaDesc = (isset($startVars['prod_meta_desc']) ? $this->db->escape($startVars['prod_meta_desc']) : '');
      $metaKeyword = (isset($startVars['prod_meta_keyword']) ? $this->db->escape($startVars['prod_meta_keyword']) : '');
    }

    $ch = $this->CurlInit($startVars['headers']);

    if ($this->cron) {
      $offset = 0;
    } else {
      $offset = $startVars['product_offset'];
    }

    $key = 0;
    do {
      //Получение товаров по curl
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle&offset=' . $offset);
      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);

      //Получение категорий по curl
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/productfolder');
      $responseCategory = curl_exec($ch);
      $responseCategory = $this->CheckResponse($responseCategory, $ch);
      
      //Занесение данных Мой Склад в массивы
      foreach ($response['rows'] as $row) {
        
        // Синхронизация категорий и товаров из группы
        if (isset($startVars['from_group'])) {
          $groups = explode('/', $row['pathName']);
          foreach ($groups as $key1 => $value) {
            if ($groups[$key1] != '' && strpos($responseCategory, '"name" : "' . $groups[$key1] . '"') === false) {
              $groups[$key1+1] = $groups[$key1] . '/' . $groups[$key1+1];
              unset($groups[$key1]);
            }
          }

          if (in_array($startVars['from_group'], $groups) === false) {
            continue;
          }
        }

        $dataMS = $this->GetDataMS($row, [$bindingMS, 'name', 'description', 'pathName', 'article', 'weight'], ['binding', 'name', 'description', 'pathName', 'sku', 'weight'], $dataMS);
        if (isset($row['salePrices'][$startVars['sale_price']])) {
          $dataMS = $this->GetDataMS($row['salePrices'][$startVars['sale_price']], ['value'], ['price'], $dataMS);
        } else {
          $dataMS = $this->GetDataMS($row['salePrices'][0], ['value'], ['price'], $dataMS);
        }

        $dataMS['category'][$key] = $this->GetCategoryFast($dataMS['pathName'][$key], $responseCategory);

        // Производители
        if (isset($startVars['manufacturer_update'])) {
          $dataMS['manufacturer'][$key] = '';
          if (isset($row['attributes'])) {
            foreach ($row['attributes'] as $key1 => $value1) {
              if ($value1['name'] == $startVars['manufacturer']) {
                $dataMS['manufacturer'][$key] = $this->db->escape($value1['value']);
                break;
              }
            }
          }
        }

        $key++;
      }
      
      if (!$this->cron) {
        break;
      }

      $offset += 1000;

    } while (isset($response['meta']['nextHref']));

    curl_close($ch);

    $duplicate = $this->DuplicateCheck([$dataMS['binding'], $dataMS['name']], 'product', $startVars['binding_name']);
    if ($duplicate != '') {
      $this->Output('Ошибка! В Моем Складе есть товары-дубликаты: ' . $duplicate, 'error_warning');
      exit();
    }

    $implodeBindingMS = htmlspecialchars("'" . implode("', '", $dataMS['binding']) . "'");
    if (isset($startVars['cat_update'])) {
      $query = $this->db->query("SELECT product_id, stock_status_id, manufacturer_id, $bindingOC, price, category_id, " . ($bindingOC != 'sku' ? "sku, " : "") . "weight, weight_class_id, pd.name AS product_name, pd.meta_title AS meta_title, pd.meta_description AS meta_description, pd.meta_keyword AS meta_keyword, pd.description AS product_description, 
      " . DB_PREFIX . "manufacturer.name AS manufacturer_name, 
      " . DB_PREFIX . "category_description.name AS category_name 
      FROM " . DB_PREFIX . "product 
      INNER JOIN " . DB_PREFIX . "product_description pd USING (`product_id`) 
      LEFT JOIN " . DB_PREFIX . "product_to_category USING (`product_id`) 
      LEFT JOIN " . DB_PREFIX . "category_description USING (`category_id`)
      LEFT JOIN " . DB_PREFIX . "manufacturer USING (`manufacturer_id`)  
      WHERE `$bindingOC` IN ($implodeBindingMS) AND pd.language_id = '$languageID' ORDER BY FIELD ($bindingOC, $implodeBindingMS)
      ");
    } else {
      $query = $this->db->query("SELECT product_id, stock_status_id, manufacturer_id, $bindingOC, price, " . ($bindingOC != 'sku' ? "sku, " : "") . "weight, weight_class_id, pd.name AS product_name, pd.meta_title AS meta_title, pd.meta_description AS meta_description, pd.meta_keyword AS meta_keyword, pd.description AS product_description, 
      " . DB_PREFIX . "manufacturer.name AS manufacturer_name 
      FROM " . DB_PREFIX . "product 
      INNER JOIN " . DB_PREFIX . "product_description pd USING (`product_id`) 
      LEFT JOIN " . DB_PREFIX . "manufacturer USING (`manufacturer_id`)  
      WHERE `$bindingOC` IN ($implodeBindingMS) AND pd.language_id = '$languageID' ORDER BY FIELD ($bindingOC, $implodeBindingMS)
      ");
    }

    $needFields = ['product_id', 'stock_status_id', 'manufacturer_id', 'binding', 'price', 'category_id', 'sku', 'weight', 'weight_class_id', 'name', 'meta_title', 'meta_description', 'meta_keyword', 'product_description', 'manufacturer_name', 'category_name'];
    if ($bindingOC == 'sku') {
      unset($needFields[array_search('sku', $needFields)]);
    }
    if (!isset($startVars['cat_update'])) {
      unset($needFields[array_search('category_id', $needFields)]);
      unset($needFields[array_search('category_name', $needFields)]);
    }

    $needFields = array_values($needFields);

    $dataSQL = $this->GetDataSQL($query, $needFields);
    
    $duplicate = $this->DuplicateCheck([$dataSQL['binding'], $dataSQL['name']], 'product', $startVars['binding_name']);
    if ($duplicate != '') {
      $this->Output('Ошибка! В Опенкарт есть товары-дубликаты: ' . $duplicate, 'error_warning');
      exit();
    }

    $query = $this->db->query("SELECT category_id, name FROM " . DB_PREFIX . "category_description");
    $categoryDataSQL = $this->GetDataSQL($query, ['category_id', 'name']);

    if ($this->version == '2.3') {
      $query = $this->db->query("SELECT query, keyword FROM " . DB_PREFIX . "url_alias");
    } else {
      $query = $this->db->query("SELECT query, keyword FROM " . DB_PREFIX . "seo_url");
    }
    
    $urlDataSQL = $this->GetDataSQL($query, ['query', 'keyword']);

    $query = $this->db->query("SELECT manufacturer_id, name FROM " . DB_PREFIX . "manufacturer");
    $manufacturerDataSQL = $this->GetDataSQL($query, ['manufacturer_id', 'name']);
    if (count($manufacturerDataSQL['manufacturer_id']) == 0) {
      $lastManufacturerId = 0;
    } else {
      $lastManufacturerId = max($manufacturerDataSQL['manufacturer_id']);
    }

    $dateModified = date("Y-m-d H:i:s");

    // Проверка существования столбца main_category
    $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_to_category` LIKE 'main_category'");
    if (isset($query->row['Field'])) {
      $mainCategory = true;
    } else {
      $mainCategory = false;
    }

    $insertManufacturer = [];
    $insertManufacturerURL = [];
    $insertManufacturerToStore = [];
    $manufacturerAdded = ['id' => [], 'name' => []];

    // Проверка существования таблицы manufacturer_description
    if (isset($startVars['manufacturer_update'])) {
      $query = $this->db->query("SHOW TABLES FROM `" . DB_DATABASE . "` LIKE '" . DB_PREFIX . "manufacturer_description'");
      if (count($query->rows) != 0) {
        $ocstore = true;
        $insertManufacturerDesc = [];
      } else {
        $ocstore = false;
      }
    }

    $updateSKU = '';
    $updateDateModified = [];
    $updateWeight = '';
    $updateWeightUnity = '';
    $updateManufacturer = "";
    $updateStockStatus = "";
    $updateWhereProduct = [];

    $updateName = '';
    $updateDescription = '';
    $updateMetaTitle = '';
    $updateMetaDescription = '';
    $updateMetaKeyword = '';
    $updateWhereDescription = [];

    $updateURL = '';
    $updateWhereSeo = [];
    $insertSeoUrl = [];

    $insertProductToCategory = [];
    $deleteProductToCategory = [];

    $added = [];
    $productUpdatedNum = [];

    $keyMS = [];
    $keySQL = [];
    if ($startVars['binding_name'] == 1) {
      // Модель + Наименование
      foreach ($dataMS['binding'] as $key => $value) {
        $find = false;
        foreach (array_keys($dataSQL['binding'], $value) as $value1) {
          if ($dataMS['name'][$key] == $dataSQL['name'][$value1]) {
            $find = true;
            $dataMS['product_id'][$key] = $dataSQL['product_id'][$value1];
            array_push($keyMS, $key);
            array_push($keySQL, $value1);
            break;
          }
        }
        if (!$find) {
          unset($dataMS['binding'][$key], $dataMS['name'][$key], $dataMS['description'][$key], $dataMS['price'][$key], $dataMS['category'][$key], $dataMS['sku'][$key], $dataMS['weight'][$key], $dataMS['manufacturer'][$key]);
        }
      }
    } else {
      // Только модель
      foreach ($dataMS['binding'] as $key => $value) {
        $value1 = array_search($value, $dataSQL['binding'], true);
        if (is_int($value1)) {
          $dataMS['product_id'][$key] = $dataSQL['product_id'][$value1];
          array_push($keyMS, $key);
          array_push($keySQL, $value1);
        } else {
          unset($dataMS['binding'][$key], $dataMS['name'][$key], $dataMS['description'][$key], $dataMS['price'][$key], $dataMS['category'][$key], $dataMS['sku'][$key], $dataMS['weight'][$key], $dataMS['manufacturer'][$key]);
        }
      }
    }

    foreach ($keyMS as $key => $value) {
      // Обновлять статус при отсутствии на складе
      if (isset($startVars['stock_status_update'])) {
        if ($dataSQL['stock_status_id'][$keySQL[$key]] != $startVars['stock_status']) {
          $updateStockStatus .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], $startVars['stock_status']);
          array_push($updateDateModified, sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], $dateModified));
          array_push($updateWhereProduct, $dataMS['product_id'][$value]);
          array_push($productUpdatedNum, $dataMS['product_id'][$value]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$value], $dataMS['name'][$value]));
        } 
      }

      // Обновлять URL
      if (isset($startVars['url_update'])) {
        $keyword = $this->Translit($dataMS['name'][$value]);
        $valueURL = array_search('product_id=' . $dataMS['product_id'][$value], $urlDataSQL['query'], true);
        if ($valueURL !== false) {
          if ($urlDataSQL['keyword'][$valueURL] != $keyword) {
            $updateURL .= sprintf("WHEN `query` = 'product_id=%s' THEN '%s' ", $dataMS['product_id'][$value], $keyword);
            array_push($updateWhereSeo, sprintf("'product_id=%s'", $dataMS['product_id'][$value]));

            array_push($productUpdatedNum, $dataMS['product_id'][$value]);
            array_push($added, sprintf('%s %s', $dataMS['binding'][$value], $dataMS['name'][$value])); 
          }
        } else {
          if ($this->version == "2.3") {
            array_push($insertSeoUrl, sprintf("('product_id=%s', '%s')", $dataMS['product_id'][$value], $keyword));
          } else {
            array_push($insertSeoUrl, sprintf("('0', '$languageID', 'product_id=%s', '%s')", $dataMS['product_id'][$value], $keyword));
          }
           
          array_push($productUpdatedNum, $dataMS['product_id'][$value]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$value], $dataMS['name'][$value])); 
        }
      }

      // Обновлять артикул
      if (isset($startVars['sku_update']) && $bindingOC != 'sku') {
        if ($dataSQL['sku'][$keySQL[$key]] != $dataMS['sku'][$value]) {
          $updateSKU .= sprintf("WHEN `product_id` = '%s' THEN '%s'", $dataMS['product_id'][$value], htmlspecialchars($dataMS['sku'][$value]));
          array_push($updateDateModified, sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], $dateModified));
          array_push($updateWhereProduct, $dataMS['product_id'][$value]);
          array_push($productUpdatedNum, $dataMS['product_id'][$value]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$value], $dataMS['name'][$value])); 
        }
      }

      // Обновлять вес
      if (isset($startVars['weight_update'])) {
        if ($dataSQL['weight'][$keySQL[$key]] != $dataMS['weight'][$value]
        || $dataSQL['weight_class_id'][$keySQL[$key]] != $startVars['weight_unity']) {
          array_push($updateDateModified, sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], $dateModified));
          $updateWeight .= sprintf("WHEN `product_id` = '%s' THEN '%s'", $dataMS['product_id'][$value], $dataMS['weight'][$value]);
          $updateWeightUnity .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], $startVars['weight_unity']);

          array_push($updateWhereProduct, $dataMS['product_id'][$value]);
          array_push($productUpdatedNum, $dataMS['product_id'][$value]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$value], $dataMS['name'][$value])); 
        }
      }

      // Обновлять производителя
      if (isset($startVars['manufacturer_update'])) {
        if ($dataSQL['manufacturer_name'][$keySQL[$key]] != $dataMS['manufacturer'][$value]) {
          $manufacturerKey = array_search($dataMS['manufacturer'][$value], $manufacturerDataSQL['name'], true);
          $manufacturerAddedKey = array_search($dataMS['manufacturer'][$value], $manufacturerAdded['name'], true);
          if ($manufacturerKey !== false) {
            $dataMS['manufacturer_id'][$value] = $manufacturerDataSQL['manufacturer_id'][$manufacturerKey];
          } elseif ($dataMS['manufacturer'][$value] == '') {
            $dataMS['manufacturer_id'][$value] = 0;
          } else {
            if ($manufacturerAddedKey !== false) {
              $dataMS['manufacturer_id'][$value] = $manufacturerAdded['id'][$manufacturerAddedKey];
            } else {
              $lastManufacturerId++;
              $dataMS['manufacturer_id'][$value] = $lastManufacturerId;
              array_push($manufacturerAdded['id'], $lastManufacturerId);
              array_push($manufacturerAdded['name'], $dataMS['manufacturer'][$value]);

              array_push($insertManufacturer, sprintf("('%s', '%s', '0')", $lastManufacturerId, $dataMS['manufacturer'][$value]));
              array_push($insertManufacturerToStore, sprintf("('%s', '0')", $lastManufacturerId));
              $manufacturerKeyword = $this->Translit($dataMS['manufacturer'][$value]);
              array_push($insertManufacturerURL, "('0', '$languageID', 'manufacturer_id=$lastManufacturerId', '$manufacturerKeyword')");
              if ($ocstore) {
                if ($this->version == '2.3') {
                  array_push($insertManufacturerDesc, sprintf("('%s', '%s', '%s')", $lastManufacturerId, $languageID, $dataMS['manufacturer'][$key]));
                } else {
                  array_push($insertManufacturerDesc, sprintf("('%s', '%s')", $lastManufacturerId, $languageID));
                }
              }
            }
          }

          array_push($updateDateModified, sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], $dateModified));
          $updateManufacturer .= sprintf("WHEN `product_id` = '%s' THEN '%s'", $dataMS['product_id'][$value], $dataMS['manufacturer_id'][$value]);
          array_push($updateWhereProduct, $dataMS['product_id'][$value]);
          array_push($productUpdatedNum, $dataMS['product_id'][$value]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$value], $dataMS['name'][$value])); 
        }
      }
        
      // Обновлять Meta
      if ($startVars['meta_prod_update'] == 1) {
        $metaTitleNew = $this->GetMetaTag($metaTitle, ['name', 'price'], [$dataMS['name'][$value], $dataMS['price'][$value]]);
        $metaDescNew = $this->GetMetaTag($metaDesc, ['name', 'price'], [$dataMS['name'][$value], $dataMS['price'][$value]]);
        $metaKeywordNew = $this->GetMetaTag($metaKeyword, ['name', 'price'], [$dataMS['name'][$value], $dataMS['price'][$value]]);

        if ($dataSQL['meta_title'][$keySQL[$key]] != $metaTitleNew 
        || $dataSQL['meta_description'][$keySQL[$key]] != $metaDescNew 
        || $dataSQL['meta_keyword'][$keySQL[$key]] != $metaKeywordNew) {
          $updateMetaTitle .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], htmlspecialchars($metaTitleNew));
          $updateMetaDescription .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], htmlspecialchars($metaDescNew));
          $updateMetaKeyword .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], htmlspecialchars($metaKeywordNew));
          array_push($updateWhereDescription, $dataMS['product_id'][$value]);
          array_push($productUpdatedNum, $dataMS['product_id'][$value]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$value], $dataMS['name'][$value])); 
        }
      }

      // Обновлять Описания
      if (isset($startVars['desc_update'])) {
        if ($dataSQL['product_description'][$keySQL[$key]] != $dataMS['description'][$value]) {
          $updateDescription .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], htmlspecialchars($dataMS['description'][$value]));
          array_push($updateWhereDescription, $dataMS['product_id'][$value]);
          array_push($productUpdatedNum, $dataMS['product_id'][$value]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$value], $dataMS['name'][$value]));
        } 
      }

      // Обновлять Наименования
      if (isset($startVars['name_update'])) {
        if ($dataSQL['name'][$keySQL[$key]] != $dataMS['name'][$value]) {
          $updateName .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$value], htmlspecialchars($dataMS['name'][$value]));
          array_push($updateWhereDescription, $dataMS['product_id'][$value]);
          array_push($productUpdatedNum, $dataMS['product_id'][$value]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$value], $dataMS['name'][$value]));
        } 
      }

      // Обновлять категорию
      if (isset($startVars['cat_update'])) {
        if ($dataSQL['category_name'][$keySQL[$key]] != $dataMS['category'][$value]) { 
          $sqlKey = array_search($dataMS['category'][$value], $categoryDataSQL['name'], true);
          if ($sqlKey !== false) {
            $dataMS['category_id'][$value] = $categoryDataSQL['category_id'][$sqlKey];
            if ($mainCategory) {
              array_push($insertProductToCategory, sprintf("('%s', '%s', '1')", $dataMS['product_id'][$value], $dataMS['category_id'][$value]));
            } else {
              array_push($insertProductToCategory, sprintf("('%s', '%s')", $dataMS['product_id'][$value], $dataMS['category_id'][$value]));
            }
          } else {
            $dataMS['category_id'][$value] = '';
          } 

          array_push($deleteProductToCategory, $dataMS['product_id'][$value]); 
          array_push($productUpdatedNum, $dataMS['product_id'][$value]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$value], $dataMS['name'][$value]));
        }
      }
    }

    $dataMS['product_id'] = array_values($dataMS['product_id']);
    $dataMS['binding'] = array_values($dataMS['binding']);
    $dataMS['name'] = array_values($dataMS['name']);
    $dataMS['description'] = array_values($dataMS['description']);
    $dataMS['price'] = array_values($dataMS['price']);
    $dataMS['category'] = array_values($dataMS['category']);
    $dataMS['sku'] = array_values($dataMS['sku']);
    $dataMS['weight'] = array_values($dataMS['weight']);
    $dataMS['manufacturer'] = array_values($dataMS['manufacturer']);

    if (count($updateWhereDescription) != 0) {
      $updateWhereDescription = array_unique($updateWhereDescription);
      $updateWhereDescription = implode(", ", $updateWhereDescription);
      $this->db->query(str_replace(", WHERE", " WHERE", "UPDATE " . DB_PREFIX . "product_description SET "
        . ($updateName != '' ? "name = CASE " . $updateName . " ELSE `name` END, " : '')
        . ($updateDescription != '' ? "description = CASE " . $updateDescription . " ELSE `description` END, " : '')
        . ($updateMetaTitle != '' ? "meta_title = CASE " . $updateMetaTitle . " ELSE `meta_title` END, " : '')
        . ($updateMetaDescription != '' ? "meta_description = CASE " . $updateMetaDescription . " ELSE `meta_description` END, " : '')
        . ($updateMetaKeyword != '' ? "meta_keyword = CASE " . $updateMetaKeyword . " ELSE `meta_keyword` END, " : '') .
        "WHERE `product_id` IN ($updateWhereDescription)"));
    }

    if (count($updateWhereSeo) != 0) {
      $updateWhereSeo = implode(", ", $updateWhereSeo);
      if ($this->version == '2.3') {
        $this->db->query("UPDATE " . DB_PREFIX . "url_alias SET 
        keyword = CASE " . $updateURL . "END
        WHERE `query` IN ($updateWhereSeo)");
      } else {
        $this->db->query("UPDATE " . DB_PREFIX . "seo_url SET 
        keyword = CASE " . $updateURL . "END
        WHERE `query` IN ($updateWhereSeo)");
      }  
    }

    if (count($insertSeoUrl) != 0) {
      $insertSeoUrl = implode(", ", $insertSeoUrl);
      if ($this->version == "2.3") {
        $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias (`query`, `keyword`) VALUES $insertSeoUrl");
      } else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url (`store_id`, `language_id`, `query`, `keyword`) VALUES $insertSeoUrl");
      }
    }

    if (count($insertManufacturer) != 0) {
      $insertManufacturer = implode(", ", $insertManufacturer);
      $insertManufacturerToStore = implode(", ", $insertManufacturerToStore);
      $insertManufacturerURL = implode(", ", $insertManufacturerURL);
      $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer (`manufacturer_id`, `name`, `sort_order`) VALUES $insertManufacturer");
      $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store (`manufacturer_id`, `store_id`) VALUES $insertManufacturerToStore");

      if ($this->version == '2.3') {
        $this->db->query("INSERT INTO " . DB_PREFIX . "url_alias (`query`, `keyword`) VALUES $insertManufacturerURL");
      } else {
        $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url (`store_id`, `language_id`, `query`, `keyword`) VALUES $insertManufacturerURL");
      }
      
      if ($ocstore) {
        $insertManufacturerDesc = implode(", ", $insertManufacturerDesc);
        if ($this->version == '2.3') {
          $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description (`manufacturer_id`, `language_id`, `name`) VALUES $insertManufacturerDesc");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description (`manufacturer_id`, `language_id`) VALUES $insertManufacturerDesc");
        }
      }
    }

    if (count($updateWhereProduct) != 0) {
      $updateWhereProduct = array_unique($updateWhereProduct);
      $updateDateModified = array_unique($updateDateModified);
      $updateDateModified = implode(" ", $updateDateModified);
      $updateWhereProduct = implode(", ", $updateWhereProduct);
      $this->db->query("UPDATE " . DB_PREFIX . "product SET " 
      . ($updateSKU != '' ? "sku = CASE " . $updateSKU . " ELSE `sku` END, " : '')
      . ($updateManufacturer != '' ? "manufacturer_id = CASE " . $updateManufacturer . " ELSE `manufacturer_id` END, " : '')
      . ($updateWeight != '' ? "weight = CASE " . $updateWeight . " ELSE `weight` END, " : '')
      . ($updateStockStatus != '' ? "stock_status_id = CASE " . $updateStockStatus . " ELSE `stock_status_id` END, " : '')
      . ($updateWeightUnity != '' ? "weight_class_id = CASE " . $updateWeightUnity . " ELSE `weight_class_id` END, " : '') .
      "date_modified = CASE " . $updateDateModified . " ELSE `date_modified` END
      WHERE `product_id` IN ($updateWhereProduct)");
    }

    if (count($deleteProductToCategory) != 0) {
      $insertProductToCategory = implode(", ", $insertProductToCategory);
      $deleteProductToCategory = "'" . implode("', '", $deleteProductToCategory) . "'";
      $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE `product_id` IN ($deleteProductToCategory)");

      if ($insertProductToCategory != '') {
        if ($mainCategory) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category (`product_id`, `category_id`, `main_category`) VALUES $insertProductToCategory");
        } else {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category (`product_id`, `category_id`) VALUES $insertProductToCategory");
        }
      }
    }

    $productUpdatedNum = array_unique($productUpdatedNum);

    $added = array_unique($added);
    $this->logText .= 'Обновлено товаров: ' . count($productUpdatedNum) . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    
    $this->LogWrite();
    $this->Output("Успешно. Товаров обновлено: " . count($productUpdatedNum), 'success');
  }


  public function StockUpdate() {
    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }
    $this->logText = date('H:i:s d.m.Y') . ' Обновление остатков товаров' . PHP_EOL;

    $this->db->query("set session wait_timeout=28800");
    $startVars = $this->GetStartVars(['binding', 'product_offset', 'binding_name', 'stock_store', 'sum_option', 'from_group']);

    $bindingOC = substr($startVars['binding'], 0, strpos($startVars['binding'], '_'));
    $bindingMS = substr($startVars['binding'], strpos($startVars['binding'], '_') + 1);

    $dataMS = $this->InitialDataMS(['binding', 'name', 'id', 'quantity']);
    $ch = $this->CurlInit($startVars['headers']);

    // Суммирование остатков опций
    if ($startVars['sum_option'] == 1) {
      $query = $this->db->query("SELECT product_id, quantity, option_id FROM " . DB_PREFIX . "product_option_value");
      $dataOptionSQL = $this->GetDataSQL($query, ['product_id', 'quantity', 'option_id']);
    }

    if ($this->cron) {
      $offset = 0;
    } else {
      $offset = $startVars['product_offset'];
    }

    if (isset($startVars['from_group'])) {
      //Получение категорий по curl
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/productfolder');
      $responseCategory = curl_exec($ch);
    }

    $key = 0;
    do {
      if (!isset($startVars['stock_store'])) {
        curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle&offset=' . $offset);
      } else {
        $url = "https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle&offset=" . $offset . "&filter=";
        foreach ($startVars['stock_store'] as $storeUrl) {
          $url .= "stockStore=" . $storeUrl . ";";
        }
        curl_setopt($ch, CURLOPT_URL, $url);
      }

      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);

      //Занесение данных Мой Склад в массивы
      foreach ($response['rows'] as $row) {
        
        // Синхронизация категорий и товаров из группы
        if (isset($startVars['from_group'])) {
          $groups = explode('/', $row['pathName']);
          foreach ($groups as $key1 => $value) {
            if ($groups[$key1] != '' && strpos($responseCategory, '"name" : "' . $groups[$key1] . '"') === false) {
              $groups[$key1+1] = $groups[$key1] . '/' . $groups[$key1+1];
              unset($groups[$key1]);
            }
          }

          if (in_array($startVars['from_group'], $groups) === false) {
            continue;
          }
        }

        $dataMS = $this->GetDataMS($row, [$bindingMS, 'name', 'id', 'quantity'], ['binding', 'name', 'id', 'quantity'], $dataMS);
        if (isset($row['components'])) {
          $dataMS['quantity'][$key] = $this->db->escape($row['components']['meta']['href']);
        }

        $key++;
      }
      
      if (!$this->cron) {
        break;
      }
      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    $dataMS = $this->GetBundleQuantity($dataMS, $ch);
    
    curl_close($ch);

    $duplicate = $this->DuplicateCheck([$dataMS['binding'], $dataMS['name']], 'product', $startVars['binding_name']);
    if ($duplicate != '') {
      $this->Output('Ошибка! В Моем Складе есть товары-дубликаты: ' . $duplicate, 'error_warning');
      exit();
    }

    $languageID = $this->GetLanguageID();

    // Получение количества товаров из БД
    $implodeBindingMS = htmlspecialchars("'" . implode("', '", $dataMS['binding']) . "'");
    $query = $this->db->query("SELECT product_id, $bindingOC, name, quantity 
    FROM " . DB_PREFIX . "product 
    INNER JOIN " . DB_PREFIX . "product_description pd USING (`product_id`) 
    WHERE `$bindingOC` IN ($implodeBindingMS) AND pd.language_id = $languageID ORDER BY FIELD ($bindingOC, $implodeBindingMS)");

    $dataSQL = $this->GetDataSQL($query, ['product_id', 'binding', 'name', 'quantity']);

    $duplicate = $this->DuplicateCheck([$dataSQL['binding'], $dataSQL['name']], 'product', $startVars['binding_name']);
    if ($duplicate != '') {
      $this->Output('Ошибка! В Опенкарт есть товары-дубликаты: ' . $duplicate, 'error_warning');
      exit();
    }

    $updateQuantity = '';
    $updateDateModified = '';
    $updateWhere = [];
    $dateModified = date("Y-m-d H:i:s");
    $added = [];
    $dataMS['product_id'] = [];

    if ($startVars['binding_name'] == 1) {
      // Модель + Наименование
      foreach ($dataMS['binding'] as $key => $value) {
        $find = false;
        foreach (array_keys($dataSQL['binding'], $value) as $value1) {
          if ($dataMS['name'][$key] == $dataSQL['name'][$value1]) {
            $find = true;
            $dataMS['product_id'][$key] = $dataSQL['product_id'][$value1];

            // Суммирование остатков опций
            if ($startVars['sum_option'] == 1) {
              $optionSum = 0;
              $optionID = [];
              foreach (array_keys($dataOptionSQL['product_id'], $dataMS['product_id'][$key]) as $optionKey) {
                array_push($optionID, $dataOptionSQL['option_id'][$optionKey]);
                $optionSum += $dataOptionSQL['quantity'][$optionKey];
              }

              if (count($optionID) != 0) {
                $optionSum /= count(array_unique($optionID));
                $dataMS['quantity'][$key] = $optionSum;
              }
            }

            // Сравнение количества
            if ($dataMS['quantity'][$key] != $dataSQL['quantity'][$value1]) {
            $updateQuantity .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $dataMS['quantity'][$key]);
            $updateDateModified .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $dateModified);
            array_push($updateWhere, $dataMS['product_id'][$key]);
            array_push($added, sprintf('%s %s', $value, $dataMS['name'][$key]));
            }

            break;
          }
        } 
        if (!$find) {
          unset($dataMS['binding'][$key]);
          unset($dataMS['name'][$key]);
          unset($dataMS['quantity'][$key]);
        }
      }
    } else {
      // Только модель
      foreach ($dataMS['binding'] as $key => $value) {
        $value1 = array_search($value, $dataSQL['binding'], true);
        if (is_int($value1)) {
          $dataMS['product_id'][$key] = $dataSQL['product_id'][$value1];

          // Суммирование остатков опций
          if ($startVars['sum_option'] == 1) {
            $optionSum = 0;
            $optionID = [];
            foreach (array_keys($dataOptionSQL['product_id'], $dataMS['product_id'][$key]) as $optionKey) {
              array_push($optionID, $dataOptionSQL['option_id'][$optionKey]);
              $optionSum += $dataOptionSQL['quantity'][$optionKey];
            }

            if (count($optionID) != 0) {
              $optionSum /= count(array_unique($optionID));
              $dataMS['quantity'][$key] = $optionSum;
            }
          }

          // Сравнение количества
          if ($dataMS['quantity'][$key] != $dataSQL['quantity'][$value1]) {
            $updateQuantity .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $dataMS['quantity'][$key]);
            $updateDateModified .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $dateModified);
            array_push($updateWhere, $dataMS['product_id'][$key]); 
            array_push($added, sprintf('%s %s', $value, $dataMS['name'][$key]));
          }
        } else {
          unset($dataMS['binding'][$key]);
          unset($dataMS['name'][$key]);
          unset($dataMS['quantity'][$key]);
        }
      }
    }

    $dataMS['product_id'] = array_values($dataMS['product_id']);
    $dataMS['binding'] = array_values($dataMS['binding']);
    $dataMS['name'] = array_values($dataMS['name']);
    $dataMS['quantity'] = array_values($dataMS['quantity']);
    
    $stockUpdatedNum = count($updateWhere);

    // Отправление SQL запроса
    if ($updateQuantity != '') {
      $updateWhere = implode(", ", $updateWhere);
      $this->db->query("UPDATE " . DB_PREFIX . "product SET 
        quantity = CASE " . $updateQuantity . "END,
        date_modified = CASE " . $updateDateModified . "END
        WHERE `product_id` IN ($updateWhere)");
    }

    $added = array_unique($added);
    
    $this->logText .= 'Товаров с обновленными остатками: ' . $stockUpdatedNum . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    
    $this->LogWrite();
    $this->Output('Успешно. Количество Товаров с обновленными остатками: ' . $stockUpdatedNum, 'success');
  }


  public function PriceUpdate() {
    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }

    $this->db->query("set session wait_timeout=28800");

    $this->logText = date('H:i:s d.m.Y') . ' Обновление цен товаров' . PHP_EOL;

    $startVars = $this->GetStartVars(['binding', 'product_offset', 'binding_name', 'sale_price', 'from_group']);

    $bindingOC = substr($startVars['binding'], 0, strpos($startVars['binding'], '_'));
    $bindingMS = substr($startVars['binding'], strpos($startVars['binding'], '_') + 1);

    $dataMS = $this->InitialDataMS(['binding', 'name', 'price']);

    $ch = $this->CurlInit($startVars['headers']);

    if ($this->cron) {
      $offset = 0;
    } else {
      $offset = $startVars['product_offset'];
    }

    if (isset($startVars['from_group'])) {
      //Получение категорий по curl
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/productfolder');
      $responseCategory = curl_exec($ch);
    }

    do {
      // Получение данных по curl
      
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle&offset=' . $offset);

      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);

      //Занесение данных Мой Склад в массивы
      foreach ($response['rows'] as $row) {
        
        // Синхронизация категорий и товаров из группы
        if (isset($startVars['from_group'])) {
          $groups = explode('/', $row['pathName']);
          foreach ($groups as $key1 => $value) {
            if ($groups[$key1] != '' && strpos($responseCategory, '"name" : "' . $groups[$key1] . '"') === false) {
              $groups[$key1+1] = $groups[$key1] . '/' . $groups[$key1+1];
              unset($groups[$key1]);
            }
          }

          if (in_array($startVars['from_group'], $groups) === false) {
            continue;
          }
        }

        $dataMS = $this->GetDataMS($row, [$bindingMS, 'name'], ['binding', 'name'], $dataMS);

        if (isset($row['salePrices'][$startVars['sale_price']])) {
          $dataMS = $this->GetDataMS($row['salePrices'][$startVars['sale_price']], ['value'], ['price'], $dataMS);
        } else {
          $dataMS = $this->GetDataMS($row['salePrices'][0], ['value'], ['price'], $dataMS);
        }
      }

      if (!$this->cron) {
        break;
      }
      $offset += 1000;
    } while (isset($response['meta']['nextHref']));
  
    curl_close($ch);

    $duplicate = $this->DuplicateCheck([$dataMS['binding'], $dataMS['name']], 'product', $startVars['binding_name']);
    if ($duplicate != '') {
      $this->Output('Ошибка! В Моем Складе есть товары-дубликаты: ' . $duplicate, 'error_warning');
      exit();
    }

    $languageID = $this->GetLanguageID();
    // Получение количества товаров из БД
    $implodeBindingMS = htmlspecialchars("'" . implode("', '", $dataMS['binding']) . "'");
    $query = $this->db->query("SELECT product_id, $bindingOC, name, price 
      FROM " . DB_PREFIX . "product 
      INNER JOIN " . DB_PREFIX . "product_description pd USING (`product_id`) 
      WHERE `$bindingOC` IN ($implodeBindingMS) AND pd.language_id = $languageID ORDER BY FIELD ($bindingOC, $implodeBindingMS)");

    $dataSQL = $this->GetDataSQL($query, ['product_id', 'binding', 'name', 'price']);

    $duplicate = $this->DuplicateCheck([$dataSQL['binding'], $dataSQL['name']], 'product', $startVars['binding_name']);
    if ($duplicate != '') {
      $this->Output('Ошибка! В Опенкарт есть товары-дубликаты: ' . $duplicate, 'error_warning');
      exit();
    }

    $updatePrice = '';
    $updateDateModified = '';
    $updateWhere = [];
    $dateModified = date("Y-m-d H:i:s");
    $added = [];
    $dataMS['product_id'] = [];

    if ($startVars['binding_name'] == 1) {
      // Модель + Наименование
      foreach ($dataMS['binding'] as $key => $value) {
        $find = false;
        foreach (array_keys($dataSQL['binding'], $value) as $value1) {
          if ($dataMS['name'][$key] == $dataSQL['name'][$value1]) {
            $find = true;
            $dataMS['product_id'][$key] = $dataSQL['product_id'][$value1];
            if ($dataMS['price'][$key] != $dataSQL['price'][$value1]) {
              $updatePrice .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $dataMS['price'][$key]);
              $updateDateModified .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $dateModified);
              array_push($added, sprintf('%s %s', $value, $dataMS['name'][$key]));
              array_push($updateWhere, $dataMS['product_id'][$key]); 
            }
            break;
          }
        } 
        if (!$find) {
          unset($dataMS['binding'][$key]);
          unset($dataMS['name'][$key]);
          unset($dataMS['price'][$key]);
        }
      }
    } else {
      // Только модель
      foreach ($dataMS['binding'] as $key => $value) {
        $value1 = array_search($value, $dataSQL['binding'], true);
        if (is_int($value1)) {
          $dataMS['product_id'][$key] = $dataSQL['product_id'][$value1];
          if ($dataMS['price'][$key] != $dataSQL['price'][$value1]) {
            $updatePrice .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $dataMS['price'][$key]);
            $updateDateModified .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $dateModified);
            array_push($added, sprintf('%s %s', $value, $dataMS['name'][$key]));
            array_push($updateWhere, $dataMS['product_id'][$key]); 
          }
        } else {
          unset($dataMS['binding'][$key]);
          unset($dataMS['name'][$key]);
          unset($dataMS['price'][$key]);
        }
      }
    }

    $dataMS['product_id'] = array_values($dataMS['product_id']);
    $dataMS['binding'] = array_values($dataMS['binding']);
    $dataMS['name'] = array_values($dataMS['name']);
    $dataMS['price'] = array_values($dataMS['price']);
    
    $priceUpdatedNum = count($updateWhere);

    // Отправление SQL запроса
    if ($updatePrice != '') {
      $updateWhere = implode(", ", $updateWhere);
      $this->db->query("UPDATE " . DB_PREFIX . "product SET 
        price = CASE " . $updatePrice . "END,
        date_modified = CASE " . $updateDateModified . "END
        WHERE `product_id` IN ($updateWhere)");
    }

    $this->logText .= 'Товаров с обновленными ценами: ' . $priceUpdatedNum . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    
    $this->LogWrite();
    $this->Output('Успешно. Количество Товаров с обновленными ценами: ' . $priceUpdatedNum, 'success');
  }


  public function SyncAbsenceProducts()
  {
    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }

    $this->logText = date('H:i:s d.m.Y') . ' Обновление/удаление лишних товаров' . PHP_EOL;

    $this->db->query("set session wait_timeout=28800");
    $startVars = $this->GetStartVars(['binding', 'absence_products', 'binding_name']);

    $bindingOC = substr($startVars['binding'], 0, strpos($startVars['binding'], '_'));
    $bindingMS = substr($startVars['binding'], strpos($startVars['binding'], '_') + 1);

    $dataMS = $this->InitialDataMS(['binding', 'name']);

    $ch = $this->CurlInit($startVars['headers']);

    // Получение данных по curl
    $offset = 0;
    do { 
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle&offset=' . $offset);
      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);

      foreach ($response['rows'] as $key => $row) {
        $dataMS = $this->GetDataMS($row, [$bindingMS, 'name'], ['binding', 'name'], $dataMS);
      }

      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    curl_close($ch);

    $updateQuantity = '';
    $updateDateModified = '';
    $updateWeight = '';
    $updateWhere = [];
    $absenceNum = 0;
    $dateModified = date("Y-m-d H:i:s");
    $deleteSeoUrl = [];
    $added = [];
    $query = $this->db->query("SELECT product_id, $bindingOC, name, quantity, stock_status_id FROM " . DB_PREFIX . "product 
      INNER JOIN " . DB_PREFIX . "product_description USING (`product_id`)");

    $dataSQL = $this->GetDataSQL($query, ['product_id', 'binding', 'name', 'quantity', 'stock_status_id']);

    if ($startVars['binding_name'] == 1) {
      foreach ($dataSQL['binding'] as $key => $value) {
        $find = false;
        foreach (array_keys($dataMS['binding'], $value) as $value1) {
          if ($dataSQL['name'][$key] == $dataMS['name'][$value1]) {
            $find = true;
            break;
          }
        }
        if (!$find) {
          if ($startVars['absence_products'] == 0) {
            $absenceNum++;
            array_push($updateWhere, $dataSQL['product_id'][$key]);
            array_push($added, sprintf('%s %s', $value, $dataSQL['name'][$key]));
            array_push($deleteSeoUrl, sprintf("product_id=%s", $dataSQL['product_id'][$key]));
          } else if ($startVars['absence_products'] == 1) {
            if ($dataSQL['quantity'][$key] != 0) {
                $updateQuantity .= sprintf("WHEN `product_id` = '%s' THEN '0' ", $dataSQL['product_id'][$key]);
                $updateWeight .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataSQL['product_id'][$key], $dateModified);
                $absenceNum++;
                array_push($added, sprintf('%s %s', $value, $dataSQL['name'][$key]));
                array_push($updateWhere, $dataSQL['product_id'][$key]);
            }
          } else {
            if ($dataSQL['quantity'][$key] != 0 || $dataSQL['stock_status_id'][$key] != 5) {
              $updateQuantity .= sprintf("WHEN `product_id` = '%s' THEN '0' ", $dataSQL['product_id'][$key]);
              $updateDateModified .= sprintf("WHEN `product_id` = '%s' THEN '5' ", $dataSQL['product_id'][$key]);
              $updateWeight .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataSQL['product_id'][$key], $dateModified);
              $absenceNum++;
              array_push($added, sprintf('%s %s', $value, $dataSQL['name'][$key]));
              array_push($updateWhere, $dataSQL['product_id'][$key]);
            }
          }
        } 
      }
    } else {
      foreach ($dataSQL['binding'] as $key => $value) {
        $value1 = array_search($value, $dataMS['binding'], true);
        if ($value1 === false) {
          if ($startVars['absence_products'] == 0) {
            $absenceNum++;
            array_push($updateWhere, $dataSQL['product_id'][$key]);
            array_push($added, sprintf('%s %s', $value, $dataSQL['name'][$key]));
            array_push($deleteSeoUrl, sprintf("product_id=%s", $dataSQL['product_id'][$key]));
          } else if ($startVars['absence_products'] == 1) {
            if ($dataSQL['quantity'][$key] != 0) {
                $updateQuantity .= sprintf("WHEN `product_id` = '%s' THEN '0' ", $dataSQL['product_id'][$key]);
                $updateWeight .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataSQL['product_id'][$key], $dateModified);
                $absenceNum++;
                array_push($added, sprintf('%s %s', $value, $dataSQL['name'][$key]));
                array_push($updateWhere, $dataSQL['product_id'][$key]);
            }
          } else {
            if ($dataSQL['quantity'][$key] != 0 || $dataSQL['stock_status_id'][$key] != 5) {
              $updateQuantity .= sprintf("WHEN `product_id` = '%s' THEN '0' ", $dataSQL['product_id'][$key]);
              $updateDateModified .= sprintf("WHEN `product_id` = '%s' THEN '5' ", $dataSQL['product_id'][$key]);
              $updateWeight .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataSQL['product_id'][$key], $dateModified);
              $absenceNum++;
              array_push($added, sprintf('%s %s', $value, $dataSQL['name'][$key]));
              array_push($updateWhere, $dataSQL['product_id'][$key]);
            }
          }
        }
      }
    }
    
    if ($startVars['absence_products'] == 0) {
      $updateWhere = "'" . implode("', '", $updateWhere) . "'";
      $deleteSeoUrl = "'" . implode("', '", $deleteSeoUrl) . "'";
       $this->db->query("DELETE " . DB_PREFIX . "product, 
        " . DB_PREFIX . "product_description,
        " . DB_PREFIX . "product_to_store,
        " . DB_PREFIX . "product_to_category,
        " . DB_PREFIX . "product_attribute, 
        " . DB_PREFIX . "product_image 
        FROM " . DB_PREFIX . "product
        LEFT JOIN " . DB_PREFIX . "product_description USING (`product_id`)
        LEFT JOIN " . DB_PREFIX . "product_to_store USING (`product_id`)
        LEFT JOIN " . DB_PREFIX . "product_to_category USING (`product_id`)
        LEFT JOIN " . DB_PREFIX . "product_attribute USING (`product_id`)
        LEFT JOIN " . DB_PREFIX . "product_image USING (`product_id`)
        WHERE `product_id` IN ($updateWhere)");

       if ($this->version == '2.3') {
        $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE `query` IN ($deleteSeoUrl)");
       } else {
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `query` IN ($deleteSeoUrl)");
       }
       
      
    } else {
      $this->db->query("UPDATE " . DB_PREFIX . "product SET `stock_status_id` = '0'");

      if ($updateQuantity != '') {
        $updateWhere = "'" . implode("', '", $updateWhere) . "'";
        if ($startVars['absence_products'] == 1) {
          $this->db->query("UPDATE " . DB_PREFIX . "product SET 
          quantity = CASE " . $updateQuantity . "END,
          date_modified = CASE " . $updateWeight . "END
          WHERE `product_id` IN ($updateWhere)");
        } else {
          $this->db->query("UPDATE " . DB_PREFIX . "product SET 
          quantity = CASE " . $updateQuantity . "END,
          stock_status_id = CASE " . $updateDateModified . "END,
          date_modified = CASE " . $updateWeight . "END
          WHERE `product_id` IN ($updateWhere)");
        }
      }
    }
    
    $this->logText .= 'Удалено/обновлено товаров: ' . $absenceNum . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    
    $this->LogWrite();

    if ($startVars['absence_products'] == 0) {
      $this->Output('Успешно. Удалено товаров: ' . $absenceNum, 'success');
    } else {
      $this->Output('Успешно. Товаров с обновленными остатками: ' . $absenceNum, 'success');
    } 
  }


  //=================Атрибуты================


  public function SyncModification() {
    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }

    $this->logText = date('H:i:s d.m.Y') . ' Добавление/обновление модификаций товаров' . PHP_EOL;
    $this->db->query("set session wait_timeout=28800");
    $startVars = $this->GetStartVars(['binding', 'attr_offset', 'binding_name', 'sale_price', 'sum_option', 'zero_option_price', 'from_group']);

    $bindingOC = substr($startVars['binding'], 0, strpos($startVars['binding'], '_'));
    $bindingMS = substr($startVars['binding'], strpos($startVars['binding'], '_') + 1);

    $languageID = $this->GetLanguageID();

    // Объявление массивов для хранения данных Мой Склад
    $dataMS = $this->InitialDataMS(['name', 'value', 'product_name', 'binding', 'price', 'quantity', 'description']);

    // Товары
    $ch = $this->CurlInit($startVars['headers']);
    $offset = 0;
    $productsMS = $this->InitialDataMS(['id', 'name', 'binding', 'price']);

    if (isset($startVars['from_group'])) {
      //Получение категорий по curl
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/productfolder');
      $responseCategory = curl_exec($ch);
    }

    do {
      // Получение данных по curl
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle&offset=' . $offset);
      $response = json_decode(curl_exec($ch), true);
          $response = $this->CheckResponse($response, $ch);
      foreach ($response['rows'] as $row) {

        // Синхронизация категорий и товаров из группы
        if (isset($startVars['from_group'])) {
          $groups = explode('/', $row['pathName']);
          foreach ($groups as $key1 => $value) {
            if ($groups[$key1] != '' && strpos($responseCategory, '"name" : "' . $groups[$key1] . '"') === false) {
              $groups[$key1+1] = $groups[$key1] . '/' . $groups[$key1+1];
              unset($groups[$key1]);
            }
          }

          if (in_array($startVars['from_group'], $groups) === false) {
            continue;
          }
        }

        $productsMS = $this->GetDataMS($row, [$bindingMS, 'name', 'id'], ['binding', 'name', 'id'], $productsMS);
        if (isset($row['salePrices'][$startVars['sale_price']])) {
          $productsMS = $this->GetDataMS($row['salePrices'][$startVars['sale_price']], ['value'], ['price'], $productsMS);
        } else {
          $productsMS = $this->GetDataMS($row['salePrices'][0], ['value'], ['price'], $productsMS);
        }
      }

      $offset += 1000;
    } while (isset($response['meta']['nextHref']));   

    
    // Модификации
    if ($this->cron) {
      $offset = 0;
    } else {
      $offset = $startVars['attr_offset'];
    }

    $prodKey = 0;
    do {
      // Получение данных по curl
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=variant&offset=' . $offset);
      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);
      foreach ($response['rows'] as $key => $value) {
        $productId = substr($value['product']['meta']['href'], strrpos($value['product']['meta']['href'], '/') + 1);
        $productSearchKey = array_search($productId, $productsMS['id']);

        if ($productSearchKey === false) {
          continue;
        }

        foreach ($value['characteristics'] as $key1 => $chValue) {
          $dataMS = $this->GetDataMS($value, ['description'], ['description'], $dataMS);
          $dataMS = $this->GetDataMS($chValue, ['name', 'value'], ['name', 'value'], $dataMS);

          $dataMS['product_name'][$prodKey] = $productsMS['name'][$productSearchKey];
          $dataMS['binding'][$prodKey] = $productsMS['binding'][$productSearchKey];
          $dataMS['quantity'][$prodKey] = $value['quantity'];

          if (isset($value['salePrices'][$startVars['sale_price']])) {
            $dataMS = $this->GetDataMS($value['salePrices'][$startVars['sale_price']], ['value'], ['price'], $dataMS);
          } else {
            $dataMS = $this->GetDataMS($value['salePrices'][0], ['value'], ['price'], $dataMS);
          }

          // Если цены равны, то цена опции = 0
          if ($startVars['zero_option_price'] == 1) {
            if ($productsMS['price'][$productSearchKey] == $dataMS['price'][$prodKey]) {
              $dataMS['price'][$prodKey] = 0;
            }
          }
          
          $prodKey++;
        }
      }

      if (!$this->cron) {
        break;
      }

      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    curl_close($ch);

    // Определение всех товаров, имеющихся в БД
    $implodeBindingMS = htmlspecialchars("'" . implode("', '", $dataMS['binding']) . "'");
    $query = $this->db->query("SELECT product_id, $bindingOC, name FROM " . DB_PREFIX . "product 
      INNER JOIN " . DB_PREFIX . "product_description USING (`product_id`)
      WHERE `$bindingOC` IN ($implodeBindingMS) ORDER BY FIELD ($bindingOC, $implodeBindingMS)");

    $dataSQL = $this->GetDataSQL($query, ['product_id', 'binding', 'name']);

    $dataMS['product_id'] = [];
    if ($startVars['binding_name'] == 1) {
      // binding + name
      foreach ($dataMS['binding'] as $key => $value) {
        $find = false;
        foreach (array_keys($dataSQL['binding'], $value) as $value1) {
          if ($dataMS['product_name'][$key] == $dataSQL['name'][$value1]) {
            $find = true;
            $dataMS['product_id'][$key] = $dataSQL['product_id'][$value1];
            break;
          }
        } 
        if (!$find) {
          unset($dataMS['binding'][$key]);
          unset($dataMS['product_name'][$key]);
          unset($dataMS['name'][$key]);
          unset($dataMS['value'][$key]);
          unset($dataMS['price'][$key]);
          unset($dataMS['quantity'][$key]);
          unset($dataMS['description'][$key]);
        }
      }
    } else {
      // Только модель
      foreach ($dataMS['binding'] as $key => $value) {
        $value1 = array_search($value, $dataSQL['binding'], true);
        if (is_int($value1)) {
          $dataMS['product_id'][$key] = $dataSQL['product_id'][$value1];
        } else {
          unset($dataMS['binding'][$key]);
          unset($dataMS['product_name'][$key]);
          unset($dataMS['name'][$key]);
          unset($dataMS['value'][$key]);
          unset($dataMS['price'][$key]);
          unset($dataMS['quantity'][$key]);
          unset($dataMS['description'][$key]);
        }
      }
    }
    
    $dataMS['binding'] = array_values($dataMS['binding']);
    $dataMS['product_name'] = array_values($dataMS['product_name']);
    $dataMS['name'] = array_values($dataMS['name']);
    $dataMS['value'] = array_values($dataMS['value']);
    $dataMS['price'] = array_values($dataMS['price']);
    $dataMS['quantity'] = array_values($dataMS['quantity']);
    $dataMS['description'] = array_values($dataMS['description']);
    $dataMS['product_id'] = array_values($dataMS['product_id']);

    // Добавление группы атрибутов
    $query = $this->db->query("SELECT attribute_group_id, name FROM " . DB_PREFIX . "attribute_group_description");

    $dataSQL = $this->GetDataSQL($query, ['attribute_group_id', 'name']);

    // Определение id группы атрибутов
    $groupIndex = array_search("Мой Склад", $dataSQL['name'], true);
    if (is_int($groupIndex)) {
      $groupId = $dataSQL['attribute_group_id'][$groupIndex];
    } else {
      if (count($dataSQL['attribute_group_id']) > 0) {
        $groupId = max($dataSQL['attribute_group_id']) + 1;
      } else {
        $groupId = 1;
      }
      
      // Добавление группы атрибутов если она не добавлена
      $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group (`attribute_group_id`, `sort_order`) VALUES ('$groupId', '0')");
      $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description (`attribute_group_id`, `language_id`, `name`) VALUES ('$groupId', '$languageID', 'Мой Склад')");
    }

    // Определение id последнего атрибута
    $query = $this->db->query("SELECT attribute_id, name FROM " . DB_PREFIX . "attribute_description");
    $dataSQLAttr = $this->GetDataSQL($query, ['attribute_id', 'name']);

    if (count($dataSQLAttr['attribute_id']) == 0) {
      $lastAttrID = 0;
    } else {
      $lastAttrID = max($dataSQLAttr['attribute_id']);
    }

    // Определение id последнего значения опции
    $query = $this->db->query("SELECT option_id, name FROM " . DB_PREFIX . "option_description");
    $dataSQLOpt = $this->GetDataSQL($query, ['option_id', 'name']);

    if (count($dataSQLOpt['option_id']) == 0) {
      $lastOptID = 0;
    } else {
      $lastOptID = max($dataSQLOpt['option_id']);
    }

    // Определение id последнего значения опции
    $query = $this->db->query("SELECT option_value_id, option_id, ovd.name AS value_name, od.name AS option_name
      FROM " . DB_PREFIX . "option_value_description ovd 
      INNER JOIN " . DB_PREFIX . "option_description od USING (`option_id`)");
    $dataSQLOptVal = $this->GetDataSQL($query, ['option_value_id', 'option_id', 'value_name', 'option_name']);

    if (count($dataSQLOptVal['option_value_id']) == 0) {
      $lastOptValID = 0;
    } else {
      $lastOptValID = max($dataSQLOptVal['option_value_id']);
    }

    // Если атрибута нет в БД, то добавление/обновление атрибутов не происходит
    $attrNameAdded = [];
    $insertAttr = [];
    $insertAttrDescription = [];
    $attrId = [];

    $optNameAdded = [];
    $insertOpt = [];
    $insertOptDescription = [];
    $optId = [];
    $optValNameAdded = [];
    $insertOptVal = [];
    $insertOptValDescription = [];
    $optValId = [];
    $added = [];

    foreach ($dataMS['name'] as $key => $value) {
      // Формирование запросов на добавление опции (если опция не добавлена)
      if ($dataMS['description'][$key] != 'Атрибут') {
        // option
        $keySQL = array_search($value, $dataSQLOpt['name'], true);
        if (is_bool($keySQL)) {
          if (!isset($optNameAdded[$value])) {
            $lastOptID++;
            $optNameAdded[$value] = $lastOptID;
            array_push($optId, $lastOptID);
            array_push($insertOpt, "('$lastOptID', 'select', '0')");
            array_push($insertOptDescription, "('$lastOptID', '$languageID', '$value')");
          } else {
            array_push($optId, $optNameAdded[$value]);
          }
        } else {
          array_push($optId, $dataSQLOpt['option_id'][$keySQL]);
        }

        // option_value
        $find = false;
        foreach (array_keys($dataSQLOptVal['value_name'], $dataMS['value'][$key]) as $value1) {
          if ($value == $dataSQLOptVal['option_name'][$value1]) {
            $find = true;
            array_push($optValId, $dataSQLOptVal['option_value_id'][$value1]);
            break;
          }
        }
        if (!$find) {
          if (!isset($optValNameAdded[sprintf("%s_%s", $value, $dataMS['value'][$key])])) {
            $lastOptValID++;
            $optValNameAdded[sprintf("%s_%s", $value, $dataMS['value'][$key])] = $lastOptValID;
            array_push($optValId, $lastOptValID);
            array_push($insertOptVal, sprintf("('%s', '%s', '0')", $lastOptValID, $optId[$key]));
            array_push($insertOptValDescription, sprintf("('%s', '$languageID', '%s', '%s')", $lastOptValID, $optId[$key], $dataMS['value'][$key]));
          } else {
            array_push($optValId, $optValNameAdded[sprintf("%s_%s", $value, $dataMS['value'][$key])]);
          }
        }
        array_push($attrId, 0);

      } else {
        // Формирование запросов на добавление атрибутов (если атрибут не добавлен)
        $keySQL = array_search($value, $dataSQLAttr['name'], true);
        if (is_bool($keySQL)) {
          if (!isset($attrNameAdded[$value])) {
            $lastAttrID++;
            $attrNameAdded[$value] = $lastAttrID;
            array_push($attrId, $lastAttrID);
            array_push($insertAttr, "('$lastAttrID', '$groupId', '0')");
            array_push($insertAttrDescription, "('$lastAttrID', '$languageID', '$value')");
          } else {
            array_push($attrId, $attrId[array_search($value, $attrNameAdded, true)]);
          }
        } else {
          array_push($attrId, $dataSQLAttr['attribute_id'][$keySQL]);
        }
        array_push($optId, 0);
        array_push($optValId, 0);
      } 
    }

    // Отправление запросов на добавление атрибутов
    if (count($insertAttr) != 0) {
      $insertAttr = implode(", " , $insertAttr);
      $insertAttrDescription = implode(", " , $insertAttrDescription);
      
      $this->db->query("INSERT INTO " . DB_PREFIX . "attribute (`attribute_id`, `attribute_group_id`, `sort_order`) VALUES $insertAttr");
      $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description (`attribute_id`, `language_id`, `name`) VALUES $insertAttrDescription");
    }

    // Отправление запросов на добавление опций
    if (count($insertOpt) != 0) {
      $insertOpt = implode(", " , $insertOpt);
      $insertOptDescription = implode(", " , $insertOptDescription);

      $this->db->query("INSERT INTO " . DB_PREFIX . "option (`option_id`, `type`, `sort_order`) VALUES $insertOpt");
      $this->db->query("INSERT INTO " . DB_PREFIX . "option_description (`option_id`, `language_id`, `name`) VALUES $insertOptDescription");
    }

    if (count($insertOptVal) != 0) {
      $insertOptVal = implode(", " , $insertOptVal);
      $insertOptValDescription = implode(", " , $insertOptValDescription);

      $this->db->query("INSERT INTO " . DB_PREFIX . "option_value (`option_value_id`, `option_id`, `sort_order`) VALUES $insertOptVal");
      $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description (`option_value_id`, `language_id`, `option_id`, `name`) VALUES $insertOptValDescription");
    }

    // Определение product_option_id
    $query = $this->db->query("SELECT product_option_id, product_id, option_id FROM " . DB_PREFIX . "product_option");

    $allProductOpt = [];
    $allProductOptId = [];
    foreach ($query->rows as $key => $value) {
      array_push($allProductOpt, sprintf("%s_%s", $value['product_id'], $value['option_id']));
      array_push($allProductOptId, $this->db->escape($value['product_option_id']));
    }

    if (count($allProductOptId) == 0) {
      $lastProductOptId = 0;
    } else {
      $lastProductOptId = max($allProductOptId);
    }

    $productOptId = [];
    $insertProductOpt = [];
    $prodOptAdded = [];

    foreach ($optId as $key => $value) {
      if ($value == 0) {
        array_push($productOptId, 0);
        continue;
      }

      if (!isset($prodOptAdded[sprintf("%s_%s", $dataMS['product_id'][$key], $value)])) {
        $keySQL = array_search(sprintf("%s_%s", $dataMS['product_id'][$key], $value), $allProductOpt, true);
        if ($keySQL !== false) {
          array_push($productOptId, $allProductOptId[$keySQL]);
        } else {
          $lastProductOptId++;
          $prodOptAdded[sprintf("%s_%s", $dataMS['product_id'][$key], $value)] = $lastProductOptId;
          array_push($productOptId, $lastProductOptId);
          array_push($insertProductOpt, sprintf("('%s', '%s', '%s', '', '1')", $lastProductOptId, $dataMS['product_id'][$key], $value));
        }
      } else {
        array_push($productOptId, $prodOptAdded[sprintf("%s_%s", $dataMS['product_id'][$key], $value)]);
      }
    }

    if (count($insertProductOpt) != 0) {
      $insertProductOpt = implode(", " , $insertProductOpt);
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_option (`product_option_id`, `product_id`, `option_id`, `value`, `required`) VALUES $insertProductOpt");
    }

    // Определение всех значений и id атрибутов и id товаров, которые имеются в БД
    $implodeAttrId = "'" . implode($attrId) . "'";
    $implodeProductId = "'" . implode($dataMS['product_id']) . "'";

    $query = $this->db->query("SELECT product_id, attribute_id, text FROM " . DB_PREFIX . "product_attribute");

    $allProductAttr = [];
    $allTextAttr = [];
    foreach ($query->rows as $key => $value) {
      array_push($allProductAttr, sprintf("%s_%s", $value['product_id'], $value['attribute_id']));
      array_push($allTextAttr, $this->db->escape($value['text']));
    }

    // Определение всех значений и id опций и id товаров, которые имеются в БД
    $implodeOptId = "'" . implode($optId) . "'";
    $query = $this->db->query("SELECT product_id, option_value_id, quantity, price FROM " . DB_PREFIX . "product_option_value");

    $allProductOptValue = [];
    $allPriceOpt = [];
    $allQuantOpt = [];
    foreach ($query->rows as $key => $value) {
      array_push($allProductOptValue, sprintf("%s_%s", $value['product_id'], $value['option_value_id']));
      array_push($allPriceOpt, $this->db->escape($value['price']));
      array_push($allQuantOpt, $this->db->escape($value['quantity']));
    }

    // Формирование запросов для обноваления/добавления записей 
    // в таблицу product_attribute/product_option_value
    $modifUpdatedNum = [];
    $updateProductAttrCase = "";
    $insertProductAttr = [];
    $prodAndAttrId = [];

    $updateProductOptCase1 = "";
    $updateProductOptCase2 = "";
    $insertProductOpt = [];
    $prodAndOptId = [];
    $added = [];

    foreach ($dataMS['value'] as $key => $value) {
      if ($attrId[$key] == 0) {
        if (in_array(sprintf("%s_%s", $dataMS['product_id'][$key], $optValId[$key]), $prodAndOptId, true)) {
          $copyKey = array_search(sprintf("%s_%s", $dataMS['product_id'][$key], $optValId[$key]), $prodAndOptId, true);
          if ($dataMS['price'][$key] == $dataMS['price'][$copyKey]) {
            $dataMS['quantity'][$copyKey] = $dataMS['quantity'][$key] + $dataMS['quantity'][$copyKey];
          }
        }
        array_push($prodAndOptId, sprintf("%s_%s", $dataMS['product_id'][$key], $optValId[$key]));
      }
    }

    $prodAndOptId = [];

    // Сумиирование опций
    $prodQuantityID = [];
    $prodQuantity = [];

    foreach ($dataMS['value'] as $key => $value) {
      // Опции
      if ($attrId[$key] == 0) {
        $prodValKey = array_search(sprintf("%s_%s", $dataMS['product_id'][$key], $optValId[$key]), $prodAndOptId, true);
        if ($prodValKey !== false && $dataMS['price'][$key] == $dataMS['price'][$prodValKey]) {
          continue;
        }

        $prodAndOptId[$key] = sprintf("%s_%s", $dataMS['product_id'][$key], $optValId[$key]);
        $keySQL = array_search(sprintf("%s_%s", $dataMS['product_id'][$key], $optValId[$key]), $allProductOptValue, true);
        if (is_int($keySQL)) {
          if ($dataMS['price'][$key] != $allPriceOpt[$keySQL] || $dataMS['quantity'][$key] != $allQuantOpt[$keySQL]) {
            
            if ($startVars['sum_option'] == 1 && $dataMS['quantity'][$key] != $allQuantOpt[$keySQL]) {
              array_push($prodQuantityID, $dataMS['product_id'][$key]);
            }

            $updateProductOptCase1 .= sprintf("WHEN `product_id` = '%s' AND `option_value_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $optValId[$key], $dataMS['price'][$key]);
            $updateProductOptCase2 .= sprintf("WHEN `product_id` = '%s' AND `option_value_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $optValId[$key], $dataMS['quantity'][$key]);
            
            array_push($modifUpdatedNum, $dataMS['product_name'][$key]);
            array_push($added, sprintf('%s %s', $dataMS['binding'][$key], $dataMS['product_name'][$key]));
          }
        } else {
          if ($startVars['sum_option'] == 1) {
            array_push($prodQuantityID, $dataMS['product_id'][$key]);
          }

          array_push($insertProductOpt, sprintf("('%s', '%s', '%s', '%s', '%s', '1', '%s', '+', '0', '+', '0', '+')", $productOptId[$key], $dataMS['product_id'][$key], $optId[$key], $optValId[$key], $dataMS['quantity'][$key], $dataMS['price'][$key]));
          array_push($modifUpdatedNum, $dataMS['product_name'][$key]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$key], $dataMS['product_name'][$key]));
        }

      // Атрибуты
      } else {
        if (in_array(sprintf("%s_%s", $dataMS['product_id'][$key], $attrId[$key]), $prodAndAttrId, true)) {
          continue;
        }
        array_push($prodAndAttrId, sprintf("%s_%s", $dataMS['product_id'][$key], $attrId[$key]));
        $keySQL = array_search(sprintf("%s_%s", $dataMS['product_id'][$key], $attrId[$key]), $allProductAttr, true);
        if (is_int($keySQL)) {
          if ($dataMS['value'][$key] != $allTextAttr[$keySQL]) {
            $updateProductAttrCase .= sprintf("WHEN `product_id` = '%s' AND `attribute_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $attrId[$key], $dataMS['value'][$key]);
            array_push($modifUpdatedNum, $dataMS['product_name'][$key]);
            array_push($added, sprintf('%s %s', $dataMS['binding'][$key], $dataMS['product_name'][$key]));
          }
        } else {
          array_push($insertProductAttr, sprintf("('%s', '%s', '$languageID', '%s')", $dataMS['product_id'][$key], $attrId[$key], $dataMS['value'][$key]));
          array_push($modifUpdatedNum, $dataMS['product_name'][$key]);
          array_push($added, sprintf('%s %s', $dataMS['binding'][$key], $dataMS['product_name'][$key]));
        }
      }
    }

    // Отправление запросов для обновления/добавления записей 
    // в таблицу product_attribute и product_option_value
    $insertProductAttr = implode(",", $insertProductAttr);

    if ($updateProductAttrCase != '') {
      $this->db->query("UPDATE " . DB_PREFIX . "product_attribute SET 
      text = CASE " . $updateProductAttrCase . "ELSE `text` END");
    }

    if ($insertProductAttr != '') {
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute (`product_id`, `attribute_id`, `language_id`, `text`) VALUES $insertProductAttr");
    }

    $insertProductOpt = implode(",", $insertProductOpt);
    if ($updateProductOptCase1 != '') {
      $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET 
      price = CASE " . $updateProductOptCase1 . "ELSE `price` END,
      quantity = CASE " . $updateProductOptCase2 . "ELSE `quantity` END");
    }

    if ($insertProductOpt != '') {
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value (`product_option_id`, `product_id`, `option_id`, `option_value_id`, `quantity`, `subtract`, `price`, `price_prefix`, `points`, `points_prefix`, `weight`, `weight_prefix`) VALUES $insertProductOpt");
    }

    // Суммирование остатков опций
    $prodQuantityID = array_unique($prodQuantityID);
    if ($startVars['sum_option'] == 1 && count($prodQuantityID) != 0) {
      $updateProductQuantity = '';
      $implodeProdQuantityID = implode(', ', $prodQuantityID);
      $query = $this->db->query("SELECT product_id, quantity, option_id FROM " . DB_PREFIX . "product_option_value WHERE `product_id` IN ($implodeProdQuantityID)");
      $dataOptionSQL = $this->GetDataSQL($query, ['product_id', 'quantity', 'option_id']);

      foreach ($prodQuantityID as $key => $value) {
        $optionSum = 0;
        $optionID = [];
        foreach (array_keys($dataOptionSQL['product_id'], $value) as $optionKey) {
          array_push($optionID, $dataOptionSQL['option_id'][$optionKey]);
          $optionSum += $dataOptionSQL['quantity'][$optionKey];
        }
        $optionSum /= (count($optionID) == 0) ? 1 : count(array_unique($optionID));
        $prodQuantity[$key] = $optionSum;
        $updateProductQuantity .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $value, $prodQuantity[$key]);
      }

      if ($updateProductQuantity != '') {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET 
        quantity = CASE " . $updateProductQuantity . "END WHERE `product_id` IN ($implodeProdQuantityID)");
      }
    }

    $modifUpdatedNum = array_unique($modifUpdatedNum);
    $added = array_unique($added);
    
    $this->logText .= 'Товаров с добавленными/обновленными модификациями: ' . count($modifUpdatedNum) . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    
    $this->LogWrite();
    $this->Output('Успешно. Количество товаров с обновленными модификациями: ' . count($modifUpdatedNum), 'success');
  }


  //=================Изображения================


  public function SyncImage() { 
    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }

    $this->logText = date('H:i:s d.m.Y') . ' Добавление/обновление изображений товаров' . PHP_EOL;

    $this->db->query("set session wait_timeout=28800");
    $startVars = $this->GetStartVars(['binding', 'image_offset', 'binding_name', 'from_group']);

    $bindingOC = substr($startVars['binding'], 0, strpos($startVars['binding'], '_'));
    $bindingMS = substr($startVars['binding'], strpos($startVars['binding'], '_') + 1);

    // Создание директории для изображений, если она не создана
    if (!is_dir(DIR_IMAGE . 'catalog/demo/syncms')) {
      mkdir(DIR_IMAGE . 'catalog/demo/syncms');
    }

    $dataMS = $this->InitialDataMS(['binding', 'name', 'image', 'image_few', 'binding_few', 'name_few']);

    if ($this->cron) {
      $offset = 0;
    } else {
      $offset = $startVars['image_offset'];
    }

    if (isset($startVars['from_group'])) {
      $ch = $this->CurlInit($startVars['headers']);
      //Получение категорий по curl
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/productfolder');
      $responseCategory = curl_exec($ch);
      curl_close($ch);
    }

    do {
      $ch = $this->CurlInit($startVars['headers']);
      curl_setopt($ch, CURLOPT_URL, 'https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle&offset=' . $offset);

      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);

      curl_close($ch);      

      foreach ($response['rows'] as $row) {
        if (isset($row['images'])) {

          // Синхронизация категорий и товаров из группы
          if ($row['images']['meta']['size'] != 0 && isset($startVars['from_group'])) {
            $groups = explode('/', $row['pathName']);
            foreach ($groups as $key1 => $value) {
              if ($groups[$key1] != '' && strpos($responseCategory, '"name" : "' . $groups[$key1] . '"') === false) {
                $groups[$key1+1] = $groups[$key1] . '/' . $groups[$key1+1];
                unset($groups[$key1]);
              }
            }

            if (in_array($startVars['from_group'], $groups) === false) {
              continue;
            }
          }

          // Если товар имеет одно изображение
          if ($row['images']['meta']['size'] == 1) {
            $ch = $this->CurlInit($startVars['headers']);
            curl_setopt($ch, CURLOPT_URL, $row['images']['meta']['href']);
            $responseImages = json_decode(curl_exec($ch), true);
            $responseImages = $this->CheckResponse($responseImages, $ch);

            $dataMS = $this->GetDataMS($row, [$bindingMS, 'name'], ['binding', 'name'], $dataMS);
            if (isset($responseImages['rows'][0]['filename'])) {
              $imgName = $this->db->escape($responseImages['rows'][0]['filename']);
            } else {
              $imgName = $this->db->escape($responseImages['rows'][0]['meta']['downloadHref']) . '.jpg';
            }
            $imgName = str_replace(array(":", "/", "\\", "%", "'"), "", $imgName);
            $sizeMS = $responseImages['rows'][0]['size'];
            $path = DIR_IMAGE . 'catalog/demo/syncms/' . $imgName;
            array_push($dataMS['image'], "catalog/demo/syncms/" . $imgName);
            if (!file_exists($path) || filesize($path) != $sizeMS) {
              curl_setopt($ch, CURLOPT_URL, $responseImages['rows'][0]['meta']['downloadHref']);
              $resultHref = $this->CurlRedirExec($ch);
    
              $file = file_get_contents($resultHref);
              file_put_contents($path, $file);
            }
            curl_close($ch);

          // Если товар имеет несколько изображений
          } else if ($row['images']['meta']['size'] > 1) {
            $ch = $this->CurlInit($startVars['headers']);
            curl_setopt($ch, CURLOPT_URL, $row['images']['meta']['href']);
            $responseImages = json_decode(curl_exec($ch), true);
            $responseImages = $this->CheckResponse($responseImages, $ch);

            foreach ($responseImages['rows'] as $key => $valueImage) {
              if (isset($valueImage['filename'])) {
                $imgName = $this->db->escape($valueImage['filename']);
              } else {
                $imgName = $this->db->escape($valueImage['meta']['downloadHref']) . '.jpg';
              }
              $imgName = str_replace(array(":", "/", "\\", "%", "'"), "", $imgName);
              $sizeMS = $valueImage['size'];
              $path = DIR_IMAGE . 'catalog/demo/syncms/' . $imgName;
              if ($key == 0) {
                $dataMS = $this->GetDataMS($row, [$bindingMS, 'name'], ['binding', 'name'],$dataMS);
                array_push($dataMS['image'], "catalog/demo/syncms/" . $imgName);

              } else {
                $dataMS = $this->GetDataMS($row, [$bindingMS, 'name'], ['binding_few', 'name_few'], $dataMS);
                array_push($dataMS['image_few'], "catalog/demo/syncms/" . $imgName);
              }
              
              if (!file_exists($path) || filesize($path) != $sizeMS) {
                curl_setopt($ch, CURLOPT_URL, $valueImage['meta']['downloadHref']);
                $resultHref = $this->CurlRedirExec($ch);
                
                $file = file_get_contents($resultHref);
                file_put_contents($path, $file);
              } 
            }
            curl_close($ch);
          }
        }
      }

      if (!$this->cron) {
        break;
      }

      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    // Формирование запроса на обновление изображения
    $implodeBindingMS = htmlspecialchars("'" . implode("', '", $dataMS['binding']) . "'");
    $query = $this->db->query("SELECT name, $bindingOC, image, product_id FROM " . DB_PREFIX . "product
     INNER JOIN " . DB_PREFIX . "product_description USING (`product_id`) 
     WHERE `$bindingOC` IN ($implodeBindingMS) ORDER BY FIELD (`$bindingOC`, $implodeBindingMS)");

    $dataSQL = $this->GetDataSQL($query, ['name', 'binding', 'image', 'product_id']);

    $imageUpdatedNum = [];
    $updateProductCase = '';
    $updateWhere = [];
    $dataMS['product_id'] = [];
    $dataMS['product_id_few'] = [];
    $added = [];

    // binding + name
    if ($startVars['binding_name'] == 1) {
      // image
      foreach ($dataMS['binding'] as $key => $value) {
        $find = false;
        foreach (array_keys($dataSQL['binding'], $value) as $value1) {
          if ($dataMS['name'][$key] == $dataSQL['name'][$value1]) {
            $find = true;
            $dataMS['product_id'][$key] = $dataSQL['product_id'][$value1];

            if ($dataMS['image'][$key] != $dataSQL['image'][$value1]) {
              $updateProductCase .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $dataMS['image'][$key]);
              array_push($updateWhere, $dataMS['product_id'][$key]);
              array_push($added, sprintf('%s %s', $value, $dataMS['name'][$key]));
            }
            break;
          }
        } 
        if (!$find) {
          unset($dataMS['binding'][$key]);
          unset($dataMS['name'][$key]);
          unset($dataMS['image'][$key]);
        }
      }

      // image_few
      foreach ($dataMS['binding_few'] as $key => $value) {
        $find = false;
        foreach (array_keys($dataSQL['binding'], $value) as $value1) {
           if ($dataMS['name_few'][$key] == $dataSQL['name'][$value1]) {
            $find = true;
            $dataMS['product_id_few'][$key] = $dataSQL['product_id'][$value1];
            break;
           }
        } 
        if (!$find) {
          unset($dataMS['binding_few'][$key]);
          unset($dataMS['name_few'][$key]);
          unset($dataMS['image_few'][$key]);
        }
      }

    } else {
      // Только модель
      // image
      foreach ($dataMS['binding'] as $key => $value) {
        $value1 = array_search($value, $dataSQL['binding'], true);
        if (is_int($value1)) {
          $dataMS['product_id'][$key] = $dataSQL['product_id'][$value1];
          
          if ($dataMS['image'][$key] != $dataSQL['image'][$value1]) {
            $updateProductCase .= sprintf("WHEN `product_id` = '%s' THEN '%s' ", $dataMS['product_id'][$key], $dataMS['image'][$key]);
            array_push($updateWhere, $dataMS['product_id'][$key]);
            array_push($added, sprintf('%s %s', $value, $dataMS['name'][$key]));
          }
        } else {
          unset($dataMS['binding'][$key]);
          unset($dataMS['name'][$key]);
          unset($dataMS['image'][$key]);
        }
      }

      // image_few
      foreach ($dataMS['binding_few'] as $key => $value) {
        $value1 = array_search($value, $dataSQL['binding'], true);
        if (is_int($value1)) {
          $dataMS['product_id_few'][$key] = $dataSQL['product_id'][$value1];
        } else {
          unset($dataMS['binding_few'][$key]);
          unset($dataMS['name_few'][$key]);
          unset($dataMS['image_few'][$key]);
        }
      }
    }

    $dataMS['product_id'] = array_values($dataMS['product_id']);
    $dataMS['binding'] = array_values($dataMS['binding']);
    $dataMS['name'] = array_values($dataMS['name']);
    $dataMS['image'] = array_values($dataMS['image']);

    $dataMS['product_id_few'] = array_values($dataMS['product_id_few']);
    $dataMS['binding_few'] = array_values($dataMS['binding_few']);
    $dataMS['name_few'] = array_values($dataMS['name_few']);
    $dataMS['image_few'] = array_values($dataMS['image_few']);

    // Отправление запроса на обновление изображения
    if ($updateProductCase != '') {
      $imageUpdatedNum = $updateWhere;
      $updateWhere = "'" . implode("', '", $updateWhere) . "'";
      $this->db->query("UPDATE " . DB_PREFIX . "product SET 
      image = CASE " . $updateProductCase . "END
      WHERE `product_id` IN ($updateWhere)");
    }

    // Определение всех путей изображений и id товаров, имеющихся в БД
    $query = $this->db->query("SELECT image, product_id FROM " . DB_PREFIX . "product_image");
    
    $dataSQL = $this->GetDataSQL($query, ['image', 'product_id']);

    // Формирование запроса для добавления нескольких изображений
    $insertProductImage = [];
    $insertWhere = [];
    
    foreach ($dataMS['image_few'] as $key => $value) {
      $find = false;
      $index = array_keys($dataSQL['image'], $value);
      if (count($index) == 0) {
          array_push($insertProductImage, sprintf("('%s', '%s')", $dataMS['product_id_few'][$key], $dataMS['image_few'][$key]));
          array_push($insertWhere, $dataMS['product_id_few'][$key]);
          array_push($added, sprintf('%s %s', $dataMS['binding_few'][$key], $dataMS['name_few'][$key]));
          continue;
      }
      foreach ($index as $value1) {
          if ($dataSQL['product_id'][$value1] == $dataMS['product_id_few'][$key]) {
              $find = true;
              break;
          }
      }
      if (!$find) {
          array_push($insertProductImage, sprintf("('%s', '%s')", $dataMS['product_id_few'][$key], $dataMS['image_few'][$key]));
          array_push($insertWhere, $dataMS['product_id_few'][$key]);
          array_push($added, sprintf('%s %s', $dataMS['binding_few'][$key], $dataMS['name_few'][$key]));
      }
    }

    // Отправление запроса на добавление нескольких изображений
    if (count($insertProductImage) != 0) {
      $insertWhere = array_unique($insertWhere);
      $imageUpdatedNum = array_merge($imageUpdatedNum, $insertWhere);
      $imageUpdatedNum = array_unique($imageUpdatedNum);
      $insertWhere = implode(", ", $insertWhere);
      $insertProductImage = implode(", ", $insertProductImage);
      $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE `product_id` IN ($insertWhere)");
      $this->db->query("INSERT INTO " . DB_PREFIX . "product_image (`product_id`, `image`) VALUES $insertProductImage");
    }
    
    $added = array_unique($added);
    
    $this->logText .= 'Товаров с добавленными/обновленными изображениями: ' . count($imageUpdatedNum) . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    
    $this->LogWrite();
    $this->Output('Успешно. Количество товаров с обновленными изображениями: ' . count($imageUpdatedNum), 'success');
  }


  public function OrderAdd() {
    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }

    $this->db->query("set session wait_timeout=28800");

    $this->logText = date('H:i:s d.m.Y') . ' Добавление заказов' . PHP_EOL;

    $startVars = $this->GetStartVars(['organization', 'store', 'binding_name', 'order_prefix', 'binding', 'order_binding']);

    if ($startVars['order_binding'] == 'number')
      $orderBinding = 'name';
    else
      $orderBinding = 'description';

    $bindingOC = substr($startVars['binding'], 0, strpos($startVars['binding'], '_'));
    $bindingMS = substr($startVars['binding'], strpos($startVars['binding'], '_') + 1);

    if (!isset($startVars['order_prefix'])) {
      $startVars['order_prefix'] = '';
    }
    $dataMS = $this->InitialDataMS(['name', 'order_id', 'agent_name', 'agent_href', 'description']);

    $ch = $this->CurlInit($startVars['headers']);

    // Заказы
    $offset = 0;
    do {
      if ($startVars['order_prefix'] != '') {
        curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/customerorder?filter=" . $orderBinding . "~=" . urlencode($startVars['order_prefix']) . "&offset=" . $offset);
      } else {
        curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/customerorder?offset=" . $offset);
      }
      
      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);

      foreach ($response['rows'] as $key => $value) {
        $dataMS = $this->GetDataMS($value, ['name', 'description'], ['name', 'description'], $dataMS);
      }
      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    // Организация
    $metaOrg = ['href' => $startVars['organization'], 'type' => 'organization', 'mediaType' => 'application/json'];

    // Склад
    $metaStore = ['href' => $startVars['store'], 'type' => 'store', 'mediaType' => 'application/json'];

    // Контрагент
    $offset = 0;
    do {
      curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/counterparty?offset=" . $offset);
      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);
      foreach ($response['rows'] as $key => $value) {
        $dataMS = $this->GetDataMS($value, ['name', 'email', 'phone'], ['agent_name', 'agent_email', 'agent_phone'], $dataMS);
        $dataMS = $this->GetDataMS($value['meta'], ['href'], ['agent_href'], $dataMS);
      }
      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    // Доставка
    $serviceDataMS = $this->InitialDataMS(['service_name', 'service_href']);
    $offset = 0;
    do {
      curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/service?offset=" . $offset);
      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);
      foreach ($response['rows'] as $key => $value) {
        $serviceDataMS = $this->GetDataMS($value, ['name'], ['service_name'], $serviceDataMS);
        $serviceDataMS = $this->GetDataMS($value['meta'], ['href'], ['service_href'], $serviceDataMS);
      }
      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    // Статусы
    $statusDataMS = $this->InitialDataMS(['name', 'href']);
    curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/customerorder/metadata");
    $response = json_decode(curl_exec($ch), true);
    $response = $this->CheckResponse($response, $ch);
    foreach ($response['states'] as $key => $value) {
      $statusDataMS = $this->GetDataMS($value, ['name'], ['name'], $statusDataMS);
      $statusDataMS = $this->GetDataMS($value['meta'], ['href'], ['href'], $statusDataMS);
    }

    $languageID = $this->GetLanguageID();

    $agentAdded = ['phone' => [], 'email' => [], 'name' => [], 'href' => []];
    $serviceAdded = ['name' => [], 'href' => []];
    $statusAdded = ['name' => [], 'href' => []];

    $added = [];

    $query = $this->db->query("SELECT MAX(order_id) FROM `" . DB_PREFIX . "order`");
    $maxOrderID = $query->row['MAX(order_id)'];

    $step = 1000;
    $limitDown = 0;
    $limitUp = $limitDown + $step;

    $posDataMS['product_href'] = [];
    $posDataMS['product_binding'] = [];

    while ($limitDown < $maxOrderID) {
      curl_setopt($ch, CURLOPT_POST, 0);

      // Позиции заказа
      if ($bindingOC != 'sku') {
        $query = $this->db->query("SELECT name, model, price, quantity, order_id, order_product_id FROM " . DB_PREFIX . "order_product 
          INNER JOIN `" . DB_PREFIX . "order` USING (`order_id`) 
          WHERE `order_status_id` != '0' AND `order_id` > '$limitDown' AND `order_id` <= '$limitUp'");
        $posDataSQL = $this->GetDataSQL($query, ['name', 'binding', 'price', 'quantity', 'order_id', 'order_product_id']);
      } else {
        $query = $this->db->query("SELECT name, op.price, op.quantity, order_id, order_product_id, p.sku 
          FROM " . DB_PREFIX . "order_product op 
          INNER JOIN " . DB_PREFIX . "product p USING (`product_id`)
          INNER JOIN `" . DB_PREFIX . "order` USING (`order_id`) 
          WHERE `order_status_id` != '0' AND `order_id` > '$limitDown' AND `order_id` <= '$limitUp'");
        $posDataSQL = $this->GetDataSQL($query, ['name', 'price', 'quantity', 'order_id', 'order_product_id', 'binding']);
      }

      $query = $this->db->query("SELECT order_product_id, name, value FROM " . DB_PREFIX . "order_option WHERE `order_id` > '$limitDown' AND `order_id` <= '$limitUp'");
      $optionDataSQL = $this->GetDataSQL($query, ['order_product_id', 'name', 'value']);
      
      foreach ($posDataSQL['name'] as $key => $value) {
        if (in_array($startVars['order_prefix'] . $posDataSQL['order_id'][$key], $dataMS[$orderBinding]) === false) {

          // Не искать товар, если он уже был найден
          $addedKey = array_search($posDataSQL['binding'][$key] . '_' . $value, $posDataMS['product_binding']);
          if ($addedKey !== false) {
            $posDataMS['product_href'][$key] = $posDataMS['product_href'][$addedKey];
            continue;
          }

          if (in_array($posDataSQL['order_product_id'][$key], $optionDataSQL['order_product_id']) !== false) {
              $optionValue = array_keys($optionDataSQL['order_product_id'], $posDataSQL['order_product_id'][$key]);

              $url = "https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product";
              if ($startVars['binding_name'] == 1) {  
                $url = $url . sprintf(";%s=%s;%s=%s", $bindingMS, urlencode($posDataSQL['binding'][$key]), "name", urlencode($value));
              } else {
                $url = $url . sprintf(";%s=%s", $bindingMS, urlencode($posDataSQL['binding'][$key]));
              }

              curl_setopt($ch, CURLOPT_URL, $url);
              $response = json_decode(curl_exec($ch), true);
              $response = $this->CheckResponse($response, $ch);
              if ($response['meta']['size'] != 0) {
                $url = 'https://online.moysklad.ru/api/remap/1.2/entity/variant?filter=productid=' . $response['rows'][0]['id'] . '&search=';
                $url = $url . urlencode($optionDataSQL['value'][$optionValue[0]]);
                curl_setopt($ch, CURLOPT_URL, $url);
                $response = json_decode(curl_exec($ch), true);
                $response = $this->CheckResponse($response, $ch);

                if ($response['meta']['size'] != 0) {

                  if ($response['meta']['size'] == 1) {
                    $posDataMS['product_href'][$key] = $response['rows'][0]['meta']['href'];
                    $posDataMS['product_binding'][$key] = $posDataSQL['binding'][$key] . '_' . $value;
                  } else {
                    $optionValues = [];
                    foreach ($optionValue as $key2 => $value2) {
                      array_push($optionValues, $optionDataSQL['value'][$value2]);
                    }

                    $posDataMS['product_href'][$key] = '';
                    foreach ($response['rows'] as $key1 => $value1) {
                      $flag = false;
                      foreach ($value1['characteristics'] as $key2 => $value2) {
                        if (!in_array($value2['value'], $optionValues, true)) {
                          $flag = true;
                        }
                      }

                      if (!$flag) {
                        $posDataMS['product_href'][$key] = $value1['meta']['href'];
                        $posDataMS['product_binding'][$key] = $posDataSQL['binding'][$key] . '_' . $value;
                        break;
                      }
                    }
                  }
                } else {
                  $posDataMS['product_href'][$key] = '';
                }
                
              } else {
                $posDataMS['product_href'][$key] = '';
              }

          } else {
            $url = "https://online.moysklad.ru/api/remap/1.2/entity/assortment?filter=type=product;type=bundle";
            if ($startVars['binding_name'] == 1) {  
              $url = $url . sprintf(";%s=%s;%s=%s", $bindingMS, urlencode($posDataSQL['binding'][$key]), "name", urlencode($value));
            } else {
              $url = $url . sprintf(";%s=%s", $bindingMS, urlencode($posDataSQL['binding'][$key]));
            }

            curl_setopt($ch, CURLOPT_URL, $url);
            $response = json_decode(curl_exec($ch), true);
            $response = $this->CheckResponse($response, $ch);
            if ($response['meta']['size'] != 0) {
              $posDataMS['product_href'][$key] = $response['rows'][0]['meta']['href'];
              $posDataMS['product_binding'][$key] = $posDataSQL['binding'][$key] . '_' . $value;
            } else {
              $posDataMS['product_href'][$key] = '';
            }
          }
        }
      }
      

      // Заказ
      $query = $this->db->query("SELECT order_id, firstname, lastname, shipping_address_1, date_added, shipping_method, email, telephone, shipping_city, shipping_zone, comment, total, order_status_id, 
        " . DB_PREFIX . "order_status.name AS status_name
        FROM `" . DB_PREFIX . "order`
        LEFT JOIN " . DB_PREFIX . "order_status USING (`order_status_id`) 
        WHERE `order_status_id` != '0' AND " . DB_PREFIX . "order_status.language_id = $languageID AND `order_id` > '$limitDown' AND `order_id` <= '$limitUp'");
      
      $dataSQL = $this->GetDataSQL($query, ['order_id', 'firstname', 'lastname', 'shipping_address_1', 'date_added', 'shipping_method', 'email', 'telephone', 'shipping_city', 'shipping_zone', 'comment', 'total', 'order_status_id', 'status_name']);
          
      $query = $this->db->query("SELECT order_id,  code, value
        FROM " . DB_PREFIX . "order_total WHERE `code` = 'shipping' AND `order_id` > '$limitDown' AND `order_id` <= '$limitUp'");
      $dataSQLTotal = $this->GetDataSQL($query, ['order_id', 'code', 'value']);
      
      $dataSQL['value'] = [];
      foreach ($dataSQL['order_id'] as $key => $value) {
        $keyTotal = array_search($value, $dataSQLTotal['order_id'], true);
        if ($keyTotal !== false) {
            $dataSQL['value'][$key] = $dataSQLTotal['value'][$keyTotal];
        } else {
            $dataSQL['value'][$key] = '';
        }
      }

      $postData = [];

      foreach ($dataSQL['order_id'] as $key => $value) {
        if (!isset($dataMS[$orderBinding]) || in_array($startVars['order_prefix'] . $dataSQL['order_id'][$key], $dataMS[$orderBinding], true) === false) {

          $skip = false;
          // Основные данные
          $postData[$key]['moment'] = $dataSQL['date_added'][$key];
          $postData[$key][$orderBinding] = $startVars['order_prefix'] . $dataSQL['order_id'][$key];
          $postData[$key]['organization'] = ['meta' => $metaOrg];
          $postData[$key]['store'] = ['meta' => $metaStore];
          $postData[$key]['shipmentAddressFull']['addInfo'] = sprintf("%s, %s, %s", $dataSQL['shipping_zone'][$key],  $dataSQL['shipping_city'][$key], $dataSQL['shipping_address_1'][$key]);
          $postData[$key]['shipmentAddressFull']['comment'] = $dataSQL['comment'][$key];
          
          // Статус
          $statusKey = array_search($dataSQL['status_name'][$key], $statusDataMS['name'], true);
          if ($statusKey !== false) {
            $metaStatus = ['href' => $statusDataMS['href'][$statusKey], 'type' => 'state', 'mediaType' => 'application/json'];
          } else {
            $addedSearch = array_search($dataSQL['status_name'][$key], $statusAdded['name'], true);
            if ($addedSearch !== false) {
              $metaStatus = ['href' => $statusAdded['href'][$addedSearch], 'type' => 'state', 'mediaType' => 'application/json'];
            } else {
              $postDataStatus = ['name' => $dataSQL['status_name'][$key], 'color' => 15106326, 'stateType' => 'Regular'];
              $postDataStatus = json_encode($postDataStatus, JSON_UNESCAPED_SLASHES);
              curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/states");
              curl_setopt($ch, CURLOPT_POST, 1);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataStatus);
              $response = json_decode(curl_exec($ch), true);
              $response = $this->CheckResponse($response, $ch);
              $metaStatus = ['href' => $response['meta']['href'], 'type' => 'state', 'mediaType' => 'application/json'];

              $statusAdded['name'][] = $dataSQL['status_name'][$key];
              $statusAdded['href'][] = $response['meta']['href'];
            }
          }

          $postData[$key]['state'] = ['meta' => $metaStatus];

          // Контрагент
          $agentName = trim($dataSQL['firstname'][$key] . ' ' . $dataSQL['lastname'][$key]);
          if ($dataSQL['telephone'][$key] != '') {
            $agentSearch = array_search($dataSQL['telephone'][$key], $dataMS['agent_phone']);
            if ($agentSearch === false) {
              $addedSearch = array_search($dataSQL['telephone'][$key], $agentAdded['phone']);
              if ($addedSearch === false && $dataSQL['email'][$key] != '') {
                $agentSearch = array_search($dataSQL['email'][$key], $dataMS['agent_email']);
                if ($agentSearch === false)
                  $addedSearch = array_search($dataSQL['email'][$key], $agentAdded['email']);
              }
            }
          } elseif ($dataSQL['email'][$key] != '') {
            $agentSearch = array_search($dataSQL['email'][$key], $dataMS['agent_email']);
            if ($agentSearch === false)
              $addedSearch = array_search($dataSQL['email'][$key], $agentAdded['email']);
          } else {
            $agentSearch = false;
            foreach (array_keys($dataMS['agent_name'], $agentName) as $value1) {
              if ($dataMS['agent_phone'][$value1] == '' && $dataMS['agent_email'][$value1] == '') {
                $agentSearch = $value1;
                break;
              }
            }

            if ($agentSearch === false)
              $addedSearch = array_search($agentName, $agentAdded['name']);
          }

          if ($agentSearch !== false) {
            $metaAgent = ['href' => $dataMS['agent_href'][$agentSearch], 'type' => 'counterparty', 'mediaType' => 'application/json'];
          } elseif ($addedSearch !== false) {
            $metaAgent = ['href' => $agentAdded['href'][$agentSearch], 'type' => 'counterparty', 'mediaType' => 'application/json'];
          } else {
            $postDataAgent = ['name' => $agentName, 'phone' => $dataSQL['telephone'][$key], 'email' => $dataSQL['email'][$key]];
            $postDataAgent = json_encode($postDataAgent, JSON_UNESCAPED_SLASHES);
            curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/counterparty");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataAgent);
            $response = json_decode(curl_exec($ch), true);
            $response = $this->CheckResponse($response, $ch);
            $metaAgent = ['href' => $response['meta']['href'], 'type' => 'counterparty', 'mediaType' => 'application/json'];

            $agentAdded['phone'][$key] = $dataSQL['telephone'][$key];
            $agentAdded['email'][$key] = $dataSQL['email'][$key];
            $agentAdded['href'][$key] = $response['meta']['href'];
            if ($dataSQL['telephone'][$key] == '' && $dataSQL['email'][$key] == '') {
              $agentAdded['name'][$key] = $agentName;
              $agentAdded['href'][$key] = $response['meta']['href'];
            }  
          }

          $postData[$key]['agent'] = ['meta' => $metaAgent];

          // Позиции
          $positions = [];
          
          foreach (array_keys($posDataSQL['order_id'], $value) as $key1 => $value1) {
            if ($posDataMS['product_href'][$value1] != '' && $posDataSQL['quantity'][$value1] > 0) {
              $positions[$key1]['quantity'] = (float)$posDataSQL['quantity'][$value1];
              $positions[$key1]['reserve'] = (float)$posDataSQL['quantity'][$value1];
              $positions[$key1]['price'] = (float)$posDataSQL['price'][$value1] * 100;
              if (strpos($posDataMS['product_href'][$value1], 'product') !== false) {
                $metaPos = ['href' => $posDataMS['product_href'][$value1], 'type' => 'product', 'mediaType' => 'application/json'];
              } elseif (strpos($posDataMS['product_href'][$value1], 'bundle') !== false) {
                $metaPos = ['href' => $posDataMS['product_href'][$value1], 'type' => 'bundle', 'mediaType' => 'application/json'];
              } else {
                $metaPos = ['href' => $posDataMS['product_href'][$value1], 'type' => 'variant', 'mediaType' => 'application/json'];
              }
              $positions[$key1]['assortment'] = ['meta' => $metaPos];
            } else {
              unset($postData[$key]);
              $skip = true;
              break;
            }
          }
          
          if ($skip) {
            continue;
          }

          // Доставка
          if ($dataSQL['value'][$key] != '') {
             $dataSQL['shipping_method'][$key] = strip_tags($dataSQL['shipping_method'][$key]);
             $search = array_search($dataSQL['shipping_method'][$key], $serviceDataMS['service_name'], true);
              $lenPos = count($positions);
              if ($search !== false) {
                $positions[$lenPos]['quantity'] = 1;
                $positions[$lenPos]['price'] = (float)$dataSQL['value'][$key] * 100;
                $metaPos = ['href' => $serviceDataMS['service_href'][$search], 'type' => 'service', 'mediaType' => 'application/json'];
                $positions[$lenPos]['assortment'] = ['meta' => $metaPos];
              } else {
                $positions[$lenPos]['quantity'] = 1;
                $positions[$lenPos]['price'] = (float)$dataSQL['value'][$key] * 100;
    
                $addedSearch = array_search($dataSQL['shipping_method'][$key], $serviceAdded['name'], true);
                if ($addedSearch !== false) {
                  $metaPos = ['href' => $serviceAdded['href'][$addedSearch], 'type' => 'service', 'mediaType' => 'application/json'];
                  $positions[$lenPos]['assortment'] = ['meta' => $metaPos];
                } else {
                  $postDataService = ['name' => $dataSQL['shipping_method'][$key]];
                  $postDataService = json_encode($postDataService, JSON_UNESCAPED_SLASHES);
                  curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/service");
                  curl_setopt($ch, CURLOPT_POST, 1);
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataService);
                  $response = json_decode(curl_exec($ch), true);
                  $response = $this->CheckResponse($response, $ch);
                  $metaPos = ['href' => $response['meta']['href'], 'type' => 'service', 'mediaType' => 'application/json'];
                  $positions[$lenPos]['assortment'] = ['meta' => $metaPos];
                }
                
                $serviceAdded['name'][] = $dataSQL['shipping_method'][$key];
                $serviceAdded['href'][] = $response['meta']['href'];
              }
          }

          $postData[$key]['positions'] = $positions;
          array_push($added, $dataSQL['order_id'][$key]);
        }
      }

      $postData = array_values($postData);
      if (count($postData) > 0) {
        $postData = json_encode($postData, JSON_UNESCAPED_SLASHES);
        curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/customerorder");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $response = json_decode(curl_exec($ch), true);
        foreach ($response as $value) {
          $value = $this->CheckResponse($value, $ch);
        }  
      }

      $limitDown += $step;
      $limitUp += $step;
    }
    
    $this->logText .= 'Добавлено заказов: ' . count($added) . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    
    $this->LogWrite();    
    $this->Output('Успешно. Заказов добавлено: ' . count($added), 'success');
  }


  public function OrderUpdate()
  {
    if (isset($this->request->get['cron'])) {
      $this->cron = true;
    } else {
      $this->cron = false;
    }

    $this->logText = date('H:i:s d.m.Y') . ' Обновление заказов' . PHP_EOL;    

    $this->db->query("set session wait_timeout=28800");
    $startVars = $this->GetStartVars(['order_prefix', 'order_binding']);

    $ch = $this->CurlInit($startVars['headers']);

    if (!isset($startVars['order_prefix'])) {
      $startVars['order_prefix'] = '';
    }

    if ($startVars['order_binding'] == 'number')
      $orderBinding = 'name';
    else
      $orderBinding = 'description';

    $dataMS = $this->InitialDataMS(['name', 'description', 'state_href', 'order_id']);

    $languageID = $this->GetLanguageID();

    // Заказы
    $offset = 0;
    do {
      if ($startVars['order_prefix'] != '') {
        curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/customerorder?filter=" . $orderBinding . "~=" . urlencode($startVars['order_prefix']) . "&offset=" . $offset);
      } else {
        curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/customerorder?offset=" . $offset);
      }
      $response = json_decode(curl_exec($ch), true);
      $response = $this->CheckResponse($response, $ch);
      foreach ($response['rows'] as $key => $value) {
        $dataMS = $this->GetDataMS($value, ['name', 'description' , 'id'], ['name', 'description', 'id'], $dataMS);
        $dataMS = $this->GetDataMS($value['state']['meta'], ['href'], ['state_href'], $dataMS);
      }
      $offset += 1000;
    } while (isset($response['meta']['nextHref']));

    // Статусы
    $statusDataMS = $this->InitialDataMS(['state', 'state_href']);
    curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/customerorder/metadata");
    $response = json_decode(curl_exec($ch), true);
    $response = $this->CheckResponse($response, $ch);
    foreach ($response['states'] as $key => $value) {
      $statusDataMS = $this->GetDataMS($value, ['name'], ['state'], $statusDataMS);
      $statusDataMS = $this->GetDataMS($value['meta'], ['href'], ['state_href'], $statusDataMS);
    }

    // Заказ
    $query = $this->db->query("SELECT order_id, order_status_id, 
      " . DB_PREFIX . "order_status.name AS status_name
      FROM `" . DB_PREFIX . "order`
      LEFT JOIN " . DB_PREFIX . "order_status USING (`order_status_id`) 
      WHERE `order_status_id` != '0' AND " . DB_PREFIX . "order_status.language_id = $languageID");
    
    $dataSQL = $this->GetDataSQL($query, ['order_id', 'order_status_id', 'status_name']);

    $orderUpdatedNum = 0;
    $added = [];
    $statusAdded = [];
    foreach ($dataMS[$orderBinding] as $key => $value) {   
      $postData = [];
      $postDataStatus = [];

      $dataMS['order_id'][$key] = str_replace($startVars['order_prefix'], '', $dataMS[$orderBinding][$key]);
      $orderKey = array_search($dataMS['order_id'][$key], $dataSQL['order_id']);
      $stateKeyMS = array_search($dataMS['state_href'][$key], $statusDataMS['state_href']);

      if ($orderKey !== false && $statusDataMS['state'][$stateKeyMS] != $dataSQL['status_name'][$orderKey]) {
        $orderUpdatedNum++;
        array_push($added, $dataMS['order_id'][$key]);

        // Статус
        $stateKeySQL = array_search($dataSQL['status_name'][$orderKey], $statusDataMS['state']);
        if ($stateKeySQL !== false) {
          $postData['state'] = ['meta' => ['href'=> $statusDataMS['state_href'][$stateKeySQL], 'type' => 'state', 'mediaType' => 'application/json']];
        } else {
          $addedSearch = array_search($dataSQL['status_name'][$orderKey], $statusAdded, true);
          if ($addedSearch !== false) {
            $postData['state'] = ['meta' => ['href'=> $addedSearch, 'type' => 'state', 'mediaType' => 'application/json']];
          } else {
            $postDataStatus = ['name' => $dataSQL['status_name'][$orderKey], 'color' => 15106326, 'stateType' => 'Regular'];
            $postDataStatus = json_encode($postDataStatus, JSON_UNESCAPED_SLASHES);
            curl_setopt($ch, CURLOPT_URL,"https://online.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/states");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataStatus);
            $response = json_decode(curl_exec($ch), true);
            $response = $this->CheckResponse($response, $ch);
            $postData['state'] = ['meta' => ['href'=> $response['meta']['href'], 'type' => 'state', 'mediaType' => 'application/json']];
            $statusAdded[$response['meta']['href']] = $dataSQL['status_name'][$orderKey];
          }
        }

        $postData = json_encode($postData, JSON_UNESCAPED_SLASHES);
        curl_setopt($ch, CURLOPT_URL, "https://online.moysklad.ru/api/remap/1.2/entity/customerorder/" . $dataMS['id'][$key]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST , "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $response = json_decode(curl_exec($ch), true);
        $response = $this->CheckResponse($response, $ch);
      }
    }

    curl_close($ch);

    $this->logText .= 'Обновлено заказов: ' . $orderUpdatedNum . PHP_EOL;
    foreach ($added as $key => $value) {
      $this->logText .= $value . '; ';
    }
    
    $this->LogWrite();    
    $this->Output('Успешно. Заказов обновлено: ' . $orderUpdatedNum, 'success');
  }


  private function LogWrite()
  {
    $resultText = '========================================================================' . PHP_EOL;
    $resultText .= $this->logText;
    $resultText .= PHP_EOL . PHP_EOL;
    file_put_contents(DIR_APPLICATION . 'controller/extension/module/syncms_log.txt', $resultText, FILE_APPEND);
  }

  public function LogClear()
  {
    $startVars = $this->GetStartVars();
    file_put_contents(DIR_APPLICATION . 'controller/extension/module/syncms_log.txt', '');
    $this->session->data['success'] = 'Лог успешно очищен';
    $this->response->redirect($startVars['redirectURL']);
  }
}