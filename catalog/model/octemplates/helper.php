<?php
class ModelOctemplatesHelper extends Controller {

    public function getOctCartProducts() {
        $cart_query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
    
        if ($cart_query->num_rows > 0) {
            $product_ids = array_column($cart_query->rows, 'product_id');
            $unique_product_ids = array_unique($product_ids);
            return $unique_product_ids;
        } else {
            return [];
        }
    }

}