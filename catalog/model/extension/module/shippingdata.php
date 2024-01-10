<?php
class ModelModuleShippingData extends Model {
	public function getNovaPoshtaCities($region, $search = '') {

        if (!$region && !$search) {
            return $this->getDefaultCities();
        }

        require_once(DIR_SYSTEM . 'helper/novaposhta.php');

        $novaposhta = new NovaPoshta($this->registry);

		$sql = "SELECT `" . $novaposhta->description_field . "` AS `description` FROM `" . DB_PREFIX . "novaposhta_cities` WHERE 1";

		if ($region) {
		    $sql .= " AND `Area` = '" . $this->db->escape($region) . "'";
        }

		if ($search) {
			$sql .= " AND (`Description` LIKE '" . $this->db->escape($search) . "%' OR `DescriptionRu` LIKE '" . $this->db->escape($search) . "%')";
		}
		
		$sql .= " ORDER BY  `" . $novaposhta->description_field . "`";

		return $this->db->query($sql)->rows;
	}
	
	public function getNovaPoshtaDepartments($city, $search = '') {
        require_once(DIR_SYSTEM . 'helper/novaposhta.php');

        $novaposhta = new NovaPoshta($this->registry);

        if (version_compare(VERSION, '3', '>=')) {
            $settings = $this->config->get('shipping_novaposhta');
        } else {
            $settings = $this->config->get('novaposhta');
        }

		$sql = "SELECT `" . $novaposhta->description_field . "` AS `description` FROM `" . DB_PREFIX . "novaposhta_warehouses` WHERE (`CityDescription` = '" . $this->db->escape($city) . "' OR `CityDescriptionRu` = '" . $this->db->escape($city) . "')";

        if ($settings['shipping_methods']['poshtomat']['status']) {
            $sql .= " AND `CategoryOfWarehouse` <> 'Postomat'";
        }
			
        if ($search) {
            $sql .= " AND (`Description` LIKE '%" . $this->db->escape($search) . "%' OR `DescriptionRu` LIKE '%" . $this->db->escape($search) . "%')";
        }

		if (isset($settings['shipping_methods']['warehouse']['warehouse_types'])) {
			foreach ($settings['shipping_methods']['warehouse']['warehouse_types'] as $k => $v) {
                $settings['shipping_methods']['warehouse']['warehouse_types'][$k] = "'" . $v . "'";
			}

			$sql .= " AND `TypeOfWarehouse` IN (" . implode(',', $settings['shipping_methods']['warehouse']['warehouse_types']) . ")";
		}
		
		if (isset($this->session->data['shippingdata']['cart_weight']) && $settings['shipping_methods']['warehouse']['warehouses_filter_weight']) {
			$sql .= " AND (`TotalMaxWeightAllowed` >= '" . $this->session->data['shippingdata']['cart_weight'] . "' OR (`TotalMaxWeightAllowed` = 0 AND (`PlaceMaxWeightAllowed` >= '" . $this->session->data['shippingdata']['cart_weight'] . "' OR `PlaceMaxWeightAllowed` = 0)))";
		}

		$sql .= " ORDER BY `Number`+0";
				
		return $this->db->query($sql)->rows;	
	}

    public function getNovaPoshtaPoshtomats($city, $search = '') {
        require_once(DIR_SYSTEM . 'helper/novaposhta.php');

        $novaposhta = new NovaPoshta($this->registry);

        $sql = "SELECT `" . $novaposhta->description_field . "` AS `description` FROM `" . DB_PREFIX . "novaposhta_warehouses` WHERE `CategoryOfWarehouse` = 'Postomat' AND (`CityDescription` = '" . $this->db->escape($city) . "' OR `CityDescriptionRu` = '" . $this->db->escape($city) . "')";

        if ($search) {
            $sql .= " AND (`Description` LIKE '%" . $this->db->escape($search) . "%' OR `DescriptionRu` LIKE '%" . $this->db->escape($search) . "%')";
        }

        $sql .= " ORDER BY `Number`+0";

        return $this->db->query($sql)->rows;
    }

    public function getJustinCities($region, $search = '') {
        if (!$region && !$search) {
            return $this->getDefaultCities();
        }

        require_once(DIR_SYSTEM . 'helper/justin.php');

        $justin = new Justin($this->registry);

        if ($region) {
            $sql = "SELECT `descr` AS `description` FROM `" . DB_PREFIX . "justin_cities` WHERE `region_uuid` = '" . $this->db->escape($region) . "'";
        } else {
            $sql = "SELECT CONCAT(`descr`, ' (', `region_descr`, ' обл.)') AS `description` FROM `" . DB_PREFIX . "justin_cities` WHERE 1";
        }

        if ($search) {
            $sql .= " AND `descr` LIKE '" . $this->db->escape($search) . "%'";
        } else {
            $sql .= " AND `language` = '" . $justin->lang . "'";
        }

        $sql .= " GROUP BY `uuid` ORDER BY `descr` ";

        return $this->db->query($sql)->rows;
    }

    public function getJustinDepartments($city, $search = '') {
        require_once(DIR_SYSTEM . 'helper/novaposhta.php');

        $justin = new Justin($this->registry);

        if (version_compare(VERSION, '3', '>=')) {
            $settings = $this->config->get('shipping_justin');
        } else {
            $settings = $this->config->get('justin');
        }

        $t_array = explode('(', $city);

        $sql = "SELECT `descr` AS `description` FROM `" . DB_PREFIX . "justin_departments` WHERE `city_descr` = '" . $this->db->escape(trim($t_array[0])) . "'";

        if ($search) {
            $sql .= " AND `descr` LIKE '%" . $this->db->escape($search) . "%'";
        }

        if (isset($settings['shipping_methods']['department']['department_types'])) {
            foreach ($settings['shipping_methods']['department']['department_types'] as $k => $v) {
                $settings['shipping_methods']['department']['department_types'][$k] = "'" . $v . "'";
            }

            $sql .= " AND `branchType_uuid` IN (" . implode(',', $settings['shipping_methods']['department']['department_types']) . ")";
        }

        if (isset($settings['shipping_methods']['department']['department_statuses'])) {
            $sql .= " AND `StatusDepart` IN (" . implode(',', $settings['shipping_methods']['department']['department_statuses']) . ")";
        }

        if (isset($this->session->data['shippingdata']['cart_weight']) && $settings['shipping_methods']['department']['departments_filter_weight']) {
            $sql .= " AND `weight_limit` >= '" . $this->session->data['shippingdata']['cart_weight'] . "'";
        }

        $sql .= " ORDER BY `departNumber`+0";

        return $this->db->query($sql)->rows;
    }

	private function getDefaultCities() {
        $data = array();

        if ($this->language->get('code') == 'ru' || $this->language->get('code') == 'ru-ru') {
            $description = 'description_ru';
        } else {
            $description = 'description';
        }

        $cities = array(
            array(
                'description'   => 'Київ',
                'description_ru' => 'Киев'
            ),
            array(
                'description'   => 'Харків',
                'description_ru' => 'Харьков'
            ),
            array(
                'description'   => 'Дніпро',
                'description_ru' => 'Днепр'
            ),
            array(
                'description'   => 'Одеса',
                'description_ru' => 'Одесса'
            ),
            /* Delivery is temporarily not carried out
            array(
                'description'   => 'Донецьк',
                'description_ru' => 'Донецк'
            ),
            */
            array(
                'description'   => 'Запоріжжя',
                'description_ru' => 'Запорожье'
            ),
            array(
                'description'   => 'Львів',
                'description_ru' => 'Львов'
            ),
            array(
                'description'   => 'Кривий Ріг',
                'description_ru' => 'Кривой Рог'
            ),
            array(
                'description'   => 'Миколаїв',
                'description_ru' => 'Николаев'
            ),
            array(
                'description'   => 'Маріуполь',
                'description_ru' => 'Мариуполь'
            ),
            /* Delivery is temporarily not carried out
           array(
               'description'   => 'Луганськ',
               'description_ru' => 'Луганск'
           ),
           */
            array(
                'description'   => 'Вінниця',
                'description_ru' => 'Винница'
            ),
            /* Delivery is temporarily not carried out
           array(
               'description'   => 'Севастополь',
               'description_ru' => 'Севастополь'
           ),
           */
            /* Delivery is temporarily not carried out
          array(
              'description'   => 'Сімферополь',
              'description_ru' => 'Симферополь'
          ),
          */
            array(
                'description'   => 'Херсон',
                'description_ru' => 'Херсон'
            ),
            array(
                'description'   => 'Полтава',
                'description_ru' => 'Полтава'
            ),
            array(
                'description'   => 'Чернігів',
                'description_ru' => 'Чернигов'
            ),
            array(
                'description'   => 'Черкаси',
                'description_ru' => 'Черкассы'
            ),
            array(
                'description'   => 'Суми',
                'description_ru' => 'Сумы'
            ),
            array(
                'description'   => 'Хмельницький',
                'description_ru' => 'Хмельницкий'
            ),
            array(
                'description'   => 'Житомир',
                'description_ru' => 'Житомир'
            ),
            array(
                'description'   => 'Кропивницький',
                'description_ru' => 'Кропивницкий'
            ),
            array(
                'description'   => 'Рівне',
                'description_ru' => 'Ровно'
            ),
            array(
                'description'   => 'Чернівці',
                'description_ru' => 'Черновцы'
            ),
            array(
                'description'   => 'Тернопіль',
                'description_ru' => 'Тернополь'
            ),
            array(
                'description'   => 'Івано-Франківськ',
                'description_ru' => 'Ивано-Франковск'
            ),
            array(
                'description'   => 'Луцьк',
                'description_ru' => 'Луцк'
            ),
            array(
                'description'   => 'Ужгород',
                'description_ru' => 'Ужгород'
            )
        );

        foreach ($cities as $city) {
            $data[] = array(
                'description' => $city[$description]
            );
        }

        return $data;
    }

    public function getZone($zone_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE `zone_id` = '" . (int)$zone_id . "' AND `status` = '1'");

        return $query->row;
    }

    public function getShippingMethod() {
        if (!empty($this->request->post['shipping'])) {
            $shipping_method = strstr($this->request->post['shipping'], '.', true);
        } elseif (isset($this->session->data['shipping_method'], $this->session->data['shipping_method']['code'])) {
            $shipping_method = strstr($this->session->data['shipping_method']['code'], '.', true);
        } else {
            $shipping_method = '';
        }

        return $shipping_method;
    }
}

class ModelExtensionModuleShippingData extends ModelModuleShippingData {

}