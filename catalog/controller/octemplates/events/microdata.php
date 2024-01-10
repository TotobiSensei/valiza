<?php
class ControllerOCTemplatesEventsMicrodata extends Controller {

    public function index(&$route, &$data) {
        $controllers = [
            'information/information',
            'octemplates/blog/oct_blogarticle',
            'octemplates/blog/oct_blogcategory',
            'octemplates/module/oct_sreview_reviews',
            'product/category',
            'product/manufacturer_list',
            'product/manufacturer_info',
            'product/product',
            'product/special'
        ];
    
        if (in_array($route, $controllers) || isset($data['breadcrumbs'])) {
            $oct_showcase_data = $this->config->get('theme_oct_showcase_data');
            $data['oct_showcase_data']['micro'] = $oct_showcase_data['micro'] ?? null;
        
            if ($route == 'product/product') {
                $product_id = (int)$data['product_id'];
                $data['oct_micro_heading_title'] = htmlspecialchars($data['heading_title']);
                $data['oct_product_categories'] = $this->getProductCategoriesName($product_id);
            
                // Get the currency
                $oct_currency = $data['oct_price_currency'] = $this->session->data['currency'];
                $currency_info = $this->model_localisation_currency->getCurrencyByCode($oct_currency);
                $decimal_place = $currency_info ? $currency_info['decimal_place'] : 0;
            
                $data['oct_special_microdata'] = false;
                $data['oct_price_microdata'] = false;
            
                $oct_price = $this->getProductPriceSpecial($product_id);
            
                // Get the product price or special
                if (!empty($data['special'])) {
                    $data['oct_special_microdata'] = $this->getCurrencyValue($oct_price['special'], $oct_currency, $decimal_place);
                } else {
                    $data['oct_price_microdata'] = $this->getCurrencyValue($oct_price['price'], $oct_currency, $decimal_place);
                }
            
                $data['oct_description_microdata'] = $this->sanitizeDescription($data['description']);
            
                $data['oct_reviews_all'] = $this->getReviewsByProductId($product_id);
            }
        }
    }
    
    private function getProductCategoriesName($product_id) {
        $oct_product_categories = $this->model_catalog_product->getCategories($product_id);
        $oct_cat_info = array_map(function ($product_category) {
            return $this->model_catalog_category->getCategory($product_category['category_id']);
        }, $oct_product_categories);
    
        return implode(', ', array_column($oct_cat_info, 'name'));
    }
    
    private function getCurrencyValue($price, $currency, $decimal_place) {
        return (float)rtrim(preg_replace('/[^.\d]/', '', $this->currency->format($price, $currency, '', $decimal_place)));
    }
    
    private function sanitizeDescription($description) {
        return htmlspecialchars(strip_tags(str_replace("\r", "", str_replace("\n", "", str_replace("\\", "/", str_replace("\"", "", html_entity_decode($description, ENT_QUOTES, 'UTF-8')))))));
    }
    
    private function getReviewsByProductId($product_id) {
        $oct_reviews_all = $this->model_catalog_review->getReviewsByProductId($product_id);
    
        return array_map(function ($result) {
            return [
                'author'     => htmlspecialchars($result['author']),
                'text'       => $this->sanitizeDescription($result['text']),
                'rating'     => (int)$result['rating'],
                'date_added' => date($this->language->get('Y-m-d'), strtotime($result['date_added']))
            ];
        }, $oct_reviews_all);
    }
    
    private function getProductPriceSpecial($product_id) {
        $product_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
    
        if ($product_query->num_rows) {
            $price = $product_query->row['price'];
            $special_query = $this->db->query("SELECT price AS special FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
            
            $special = null;
            if ($special_query->num_rows) {
                $special = $special_query->row['special'];
            }
    
            return array(
                'price' => $price,
                'special' => $special
            );
        } else {
            return false;
        }
    }
}