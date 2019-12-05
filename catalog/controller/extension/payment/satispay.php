<?php
require_once(dirname(__FILE__).'/../../../../system/library/satispay/online-api-php-sdk/init.php');

class ControllerExtensionPaymentSatispay extends Controller {
	public function index() {
		$data['continue'] = $this->url->link('extension/payment/satispay/payment');
		return $this->load->view('extension/payment/satispay', $data);
	}
	
	public function payment() {
		\SatispayOnline\Api::setSecurityBearer($this->config->get('payment_satispay_security_bearer'));
    \SatispayOnline\Api::setStaging($this->config->get('payment_satispay_staging'));

    \SatispayOnline\Api::setPluginName('OpenCart');
		\SatispayOnline\Api::setType('ECOMMERCE-PLUGIN');

		$this->load->model('checkout/order');

		$order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		try {
			$checkout = \SatispayOnline\Checkout::create(array(
				'description' => '#'.$order['order_id'],
				'phone_number' => '',
				'redirect_url' => $this->url->link('extension/payment/satispay/redirect'),
				'callback_url' => htmlspecialchars_decode($this->url->link('extension/payment/satispay/callback', 'charge_id={uuid}')),
				'amount_unit' => round($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false) * 100),
				'currency' => $order['currency_code'],
				'metadata' => array(
					'order_id' => $order['order_id']
				)
			));

			$this->response->setOutput(json_encode(array(
				'redirect' => $checkout->checkout_url
			)));
		} catch (\Exception $ex) {
			echo 'Satispay Error '.$ex->getCode().': '.$ex->getMessage();
			exit;
		}
	}

	public function callback() {
		\SatispayOnline\Api::setSecurityBearer($this->config->get('payment_satispay_security_bearer'));
    \SatispayOnline\Api::setStaging($this->config->get('payment_satispay_staging'));

    \SatispayOnline\Api::setPluginName('OpenCart');
		\SatispayOnline\Api::setType('ECOMMERCE-PLUGIN');

		$charge = \SatispayOnline\Charge::get($this->request->get['charge_id']);
		if ($charge->status === 'SUCCESS') {
			$this->load->model('checkout/order');
			$this->model_checkout_order->addOrderHistory($charge->metadata->order_id, 2);
		}
	}

	public function redirect() {
		\SatispayOnline\Api::setSecurityBearer($this->config->get('payment_satispay_security_bearer'));
    \SatispayOnline\Api::setStaging($this->config->get('payment_satispay_staging'));

    \SatispayOnline\Api::setPluginName('OpenCart');
		\SatispayOnline\Api::setType('ECOMMERCE-PLUGIN');

		$charge = \SatispayOnline\Charge::get($this->request->get['charge_id']);
		if ($charge->status === 'SUCCESS') {
			$this->response->redirect($this->url->link('checkout/success'));
		} else {
			$this->response->redirect($this->url->link('checkout/checkout'));
		}
	}

	// public function payment() {
	// 	if ($this->session->data['payment_method']['code'] == 'satispay') {
	// 		$data['success_callback'] = $this->url->link('checkout/success');
	// 		$data['fail_callback'] = $this->url->link('checkout/checkout');

	// 		$this->load->model('checkout/order');
	// 		$this->load->model('extension/payment/satispay');
	// 		$this->load->language('extension/payment/satispay');

	// 		$this->document->setTitle($this->language->get('heading_title'));

	// 		$order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

	// 		$data['currency'] = $order['currency_code'];
	// 		$data['locale'] = $order['language_code'];
	// 		$data['amount'] = round($order['total'] * 100);
	// 		$data['phone'] = $order['telephone'];
	// 		$data['order_id'] = $order['order_id'];
	// 		$data['data_key'] = $this->config->get('satispay_data_key');
	// 		$data['webhook'] = $this->url->link('extension/payment/satispay/webhook', [
	// 			'order_id' => $order['order_id']
	// 		]);

	// 		$data['text_satispay'] = $this->language->get('text_satispay');

	// 		$staging = $this->config->get('satispay_staging');
	// 		$data['domain'] = 'https://online.satispay.com';
	// 		if ($staging)
	// 			$data['domain'] = 'http://staging.online.satispay.com';

	// 		$data['column_left'] = $this->load->controller('common/column_left');
	// 		$data['column_right'] = $this->load->controller('common/column_right');
	// 		$data['content_top'] = $this->load->controller('common/content_top');
	// 		$data['content_bottom'] = $this->load->controller('common/content_bottom');
	// 		$data['footer'] = $this->load->controller('common/footer');
	// 		$data['header'] = $this->load->controller('common/header');

	// 		$this->response->setOutput($this->load->view('extension/payment/satispay_payment', $data));
	// 	} else {
	// 		$this->response->redirect('checkout/checkout');
	// 	}
	// }

	// public function webhook() {
	// 	$this->load->model('checkout/order');
	// 	$this->load->model('extension/payment/satispay');
	// 	$this->load->library('satispay');

	// 	$order = $this->model_checkout_order->getOrder($this->request->get['order_id']);

	// 	if (!$order['order_status']) {
	// 		\Satispay\Satispay::setSecurityBearer($this->config->get('satispay_security_key'));
  //     \Satispay\Satispay::setStaging($this->config->get('satispay_staging'));

	// 		$data = json_decode(file_get_contents('php://input'), true);

	// 		$charge = \Satispay\Charge::create([
  //       'user_id' => $data['uuid'],
  //       'currency' => $order['currency_code'],
  //       'amount' => round($order['total'] * 100),
  //       'description' => '#'.$order['order_id'],
  //       'callback_url' => htmlspecialchars_decode(urldecode($this->url->link('extension/payment/satispay/callback', array(
	// 				'uuid' => '{uuid}'
	// 			)))),
  //       'required_success_email' => true,
  //       'metadata' => [
  //         'order_id' => $order['order_id']
  //       ]
  //     ]);
	// 	}
	// }

	// public function callback() {
	// 	$this->load->model('checkout/order');
	// 	$this->load->model('extension/payment/satispay');
	// 	$this->load->library('satispay');

	// 	\Satispay\Satispay::setSecurityBearer($this->config->get('satispay_security_key'));
	// 	\Satispay\Satispay::setStaging($this->config->get('satispay_staging'));

	// 	$charge = \Satispay\Charge::get($this->request->get['uuid']);
	// 	$order = $this->model_checkout_order->getOrder($charge->metadata->order_id);

	// 	if (!$order['order_status'] && $charge->status == 'SUCCESS') {
	// 		$this->model_checkout_order->addOrderHistory($charge->metadata->order_id, 2);
	// 	}
	// }
}
