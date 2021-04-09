<?php

class ModelExtensionPaymentHipaymbway extends Model {

    public function getMethod($address, $total) {
        $this->load->language('extension/payment/hipaymbway');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('hipaymbway_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        if ($query->num_rows) {
            $status = true;
        } elseif (!$this->config->get('hipaymbway_geo_zone_id')) {
            $status = true;
	} else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'hipaymbway',
                'title' => $this->language->get('text_title'),
                'terms' => $this->language->get('text_terms'),
                'sort_order' => $this->config->get('hipaymbway_sort_order')
            );
        }

        return $method_data;
    }

}

?>
