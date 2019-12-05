<?php
class ModelExtensionPaymentSatispay extends Model {
  public function getMethod($address, $total) {
    $this->load->language('extension/payment/satispay');

		$status = $this->config->get('payment_satispay_status');
		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'satispay',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_satispay_sort_order')
      );
		}

		return $method_data;
  }
}
