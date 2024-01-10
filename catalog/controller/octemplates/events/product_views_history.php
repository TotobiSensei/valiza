<?php
class ControllerOCTemplatesEventsProductViewsHistory extends Controller {

    public function index(&$route, &$data) {
        $product_id = (int)$this->request->get['product_id'] ?? null;
        $module_id = $this->getModuleIdByCode('oct_product_views');
        $module_info = $this->model_setting_module->getModule($module_id);

        if ($product_id && $module_id && isset($module_info['status']) && $module_info['status'] == 'on') {
            $product_ids = $this->getProductViews(['oct_product_views', 'viewed']);
            $this->addProductIdToViewed($product_ids, $product_id);
        }
    }
    
    private function getModuleIdByCode($code) {
        $escaped_code = $this->db->escape($code);
        $query = $this->db->query("SELECT module_id FROM `" . DB_PREFIX . "module` WHERE `code` = '$escaped_code'");
        return $query->num_rows ? $query->row['module_id'] : null;
    }
    
    private function getProductViews($keys) {
        $product_ids = [];
    
        foreach ($keys as $key) {
            if (isset($this->request->cookie[$key])) {
                $product_ids = array_merge($product_ids, explode(',', $this->request->cookie[$key]));
            } elseif (isset($this->session->data[$key])) {
                $product_ids = array_merge($product_ids, $this->session->data[$key]);
            }
        }
    
        return $product_ids;
    }
    
    private function addProductIdToViewed(&$product_ids, $product_id) {
        // Remove the product ID if it already exists in the array
        if (($key = array_search((int)$product_id, $product_ids)) !== false) {
            unset($product_ids[$key]);
        }
    
        // Add the product ID to the start of the array
        array_unshift($product_ids, (int)$product_id);
        $pr_ids = array_slice($product_ids, 0, 20);
    
        setcookie('oct_product_views', implode(',', $pr_ids), time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
    }
}