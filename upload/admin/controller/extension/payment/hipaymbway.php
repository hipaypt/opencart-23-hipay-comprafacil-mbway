<?php

class ControllerExtensionPaymentHipaymbway extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/payment/hipaymbway');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('hipaymbway', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
        }


        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['entry_cf_username'] = $this->language->get('entry_cf_username');
        $data['entry_cf_password'] = $this->language->get('entry_cf_password');
        $data['entry_cf_mode'] = $this->language->get('entry_cf_mode');
        $data['entry_cf_entity'] = $this->language->get('entry_cf_entity');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');


        $data['entry_pending_status'] = $this->language->get('entry_pending_status');
        $data['entry_accepted_status'] = $this->language->get('entry_accepted_status');
        $data['entry_denied_status'] = $this->language->get('entry_denied_status');

        $data['entry_cf_category'] = $this->language->get('entry_cf_category');

        $data['entry_cf_no'] = $this->language->get('entry_cf_no');
        $data['entry_cf_yes'] = $this->language->get('entry_cf_yes');
        $data['entry_cf_active'] = $this->language->get('entry_cf_active');
        $data['entry_cf_disabled'] = $this->language->get('entry_cf_disabled');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');


        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/hipaymbway', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action'] = $this->url->link('extension/payment/hipaymbway', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['hipaymbway_username'])) {
            $data['hipaymbway_username'] = $this->request->post['hipaymbway_username'];
        } else {
            $data['hipaymbway_username'] = $this->config->get('hipaymbway_username');
        }

        if (isset($this->request->post['hipaymbway_password'])) {
            $data['hipaymbway_password'] = $this->request->post['hipaymbway_password'];
        } else {
            $data['hipaymbway_password'] = $this->config->get('hipaymbway_password');
        }

        if (isset($this->request->post['hipaymbway_mode'])) {
            $data['hipaymbway_mode'] = $this->request->post['hipaymbway_mode'];
        } else {
            $data['hipaymbway_mode'] = $this->config->get('hipaymbway_mode');
        }


        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


        if (isset($this->request->post['hipaymbway_geo_zone_id'])) {
            $data['hipaymbway_geo_zone_id'] = $this->request->post['hipaymbway_geo_zone_id'];
        } else {
            $data['hipaymbway_geo_zone_id'] = $this->config->get('hipaymbway_geo_zone_id');
        }


        if (isset($this->request->post['hipaymbway_pending_status'])) {
            $data['hipaymbway_pending_status'] = $this->request->post['hipaymbway_pending_status'];
        } else {
            $data['hipaymbway_pending_status'] = $this->config->get('hipaymbway_pending_status');
        }

        if (isset($this->request->post['hipaymbway_accepted_status'])) {
            $data['hipaymbway_accepted_status'] = $this->request->post['hipaymbway_accepted_status'];
        } else {
            $data['hipaymbway_accepted_status'] = $this->config->get('hipaymbway_accepted_status');
        }

        if (isset($this->request->post['hipaymbway_denied_status'])) {
            $data['hipaymbway_denied_status'] = $this->request->post['hipaymbway_denied_status'];
        } else {
            $data['hipaymbway_denied_status'] = $this->config->get('hipaymbway_denied_status');
        }



        if (isset($this->request->post['hipaymbway_entity'])) {
            $data['hipaymbway_entity'] = $this->request->post['hipaymbway_entity'];
        } else {
            $data['hipaymbway_entity'] = $this->config->get('hipaymbway_entity');
        }


        if (isset($this->request->post['hipaymbway_category'])) {
            $data['hipaymbway_category'] = $this->request->post['hipaymbway_category'];
        } else {
            $data['hipaymbway_category'] = $this->config->get('hipaymbway_category');
        }



        if (isset($this->request->post['hipaymbway_status'])) {
            $data['hipaymbway_status'] = $this->request->post['hipaymbway_status'];
        } else {
            $data['hipaymbway_status'] = $this->config->get('hipaymbway_status');
        }

        if (isset($this->request->post['hipaymbway_sort_order'])) {
            $data['hipaymbway_sort_order'] = $this->request->post['hipaymbway_sort_order'];
        } else {
            $data['hipaymbway_sort_order'] = $this->config->get('hipaymbway_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/hipaymbway.tpl', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/hipaymbway')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}

?>
