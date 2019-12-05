<?php
class ControllerExtensionPaymentSatispay extends Controller {
  public function index() {
		$this->load->language('extension/payment/satispay');

		$this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_satispay', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

    $data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/satispay', 'user_token=' . $this->session->data['user_token'], true)
		);

    $data['heading_title'] = $this->language->get('heading_title');

    $data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

    $data['entry_sort_order'] = $this->language->get('entry_sort_order');
    $data['entry_status'] = $this->language->get('entry_status');
    $data['entry_security_bearer'] = $this->language->get('entry_security_bearer');
    $data['entry_staging'] = $this->language->get('entry_staging');

    // $data['text_edit'] = $this->language->get('text_edit');
    // $data['text_yes'] = $this->language->get('text_yes');
    // $data['text_no'] = $this->language->get('text_no');
    // $data['text_enabled'] = $this->language->get('text_enabled');
    // $data['text_disabled'] = $this->language->get('text_disabled');

    $data['action'] = $this->url->link('extension/payment/satispay', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

    $data['payment_satispay_staging'] = $this->config->get('payment_satispay_staging');
    if (isset($this->request->post['payment_satispay_staging'])) {
			$data['payment_satispay_staging'] = $this->request->post['payment_satispay_staging'];
		}

    $data['payment_satispay_security_bearer'] = $this->config->get('payment_satispay_security_bearer');
    if (isset($this->request->post['payment_satispay_security_bearer'])) {
			$data['payment_satispay_security_bearer'] = $this->request->post['payment_satispay_security_bearer'];
		}

    $data['payment_satispay_status'] = $this->config->get('payment_satispay_status');
    if (isset($this->request->post['payment_satispay_status'])) {
			$data['payment_satispay_status'] = $this->request->post['payment_satispay_status'];
		}

    $data['payment_satispay_sort_order'] = $this->config->get('payment_satispay_sort_order');
    if (isset($this->request->post['payment_satispay_sort_order'])) {
			$data['payment_satispay_sort_order'] = $this->request->post['payment_satispay_sort_order'];
		}

    $data['user_token'] = $this->session->data['user_token'];

    $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('extension/payment/satispay', $data));
  }

  protected function validate() {
    if (!$this->user->hasPermission('modify', 'extension/payment/satispay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
  }
}
