<?php
class ControllerModuleShippingData extends Controller {
	public function getShippingData() {
		$json = array();

        if (version_compare(VERSION, '2.3', '>=')) {
            $this->load->model('extension/module/shippingdata');

            $model_name = 'model_extension_module_shippingdata';
        } else {
            $this->load->model('module/shippingdata');

            $model_name = 'model_module_shippingdata';
        }

		$shipping = $this->$model_name->getShippingMethod();
		
		if (isset($this->request->post['action'])) {
			$action = $this->request->post['action'];
		} else {
			$action = '';
		}
		
		if (isset($this->request->post['filter'])) {
			$filter = trim($this->request->post['filter']);
		} else {
			$filter = '';
		}
		
		if (isset($this->request->post['search'])) {
			$search = trim($this->request->post['search']);
		} else {
			$search = '';
		}

		if ($shipping == 'novaposhta') {
            require_once(DIR_SYSTEM . 'helper/novaposhta.php');

            $novaposhta = new NovaPoshta($this->registry);

            if ($action == 'getCities') {
                if ($filter) {
                    $zone_info = $this->$model_name->getZone($filter);

                    if ($zone_info) {
                        $filter = $novaposhta->getAreaRef($zone_info['name']);
                    }
                }

                $json = $this->$model_name->getNovaPoshtaCities($filter, $search);
            } elseif ($action == 'getDepartments') {
                $json = $this->$model_name->getNovaPoshtaDepartments($filter, $search);
            } elseif ($action == 'getPoshtomats') {
                $json = $this->$model_name->getNovaPoshtaPoshtomats($filter, $search);
            }
        } elseif ($shipping == 'justin') {
            require_once(DIR_SYSTEM . 'helper/justin.php');

            $justin = new Justin($this->registry);

            if ($action == 'getCities') {
                if ($filter) {
                    $zone_info = $this->$model_name->getZone($filter);

                    if ($zone_info) {
                        $filter = $justin->getRegionUUID($zone_info['name']);
                    }
                }

                $json = $this->$model_name->getJustinCities($filter, $search);
            } elseif ($action == 'getDepartments') {
                $json = $this->$model_name->getJustinDepartments($filter, $search);
            }
        }
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

class ControllerExtensionModuleShippingData extends ControllerModuleShippingData {

}