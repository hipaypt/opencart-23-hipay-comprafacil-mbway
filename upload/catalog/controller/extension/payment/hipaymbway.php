<?php

include __DIR__ . '/hipay/hipaypt-mbway-sdk-php/lib/HipayMbway/autoload.php';

use HipayMbway\MbwayClient;
use HipayMbway\MbwayRequestTransaction;
use HipayMbway\MbwayRequestTransactionResponse;
use HipayMbway\MbwayRequestDetails;
use HipayMbway\MbwayRequestResponse;
use HipayMbway\MbwayRequestDetailsResponse;
use HipayMbway\MbwayPaymentDetailsResult;
use HipayMbway\MbwayNotification;

class ControllerExtensionPaymentHipaymbway extends Controller {

    private $_reference = "";
    private $_entity = "";
    private $_value = "";
    private $_cferror = "";
    private $cf_username;
    private $cf_password;
    private $cf_mode;
    private $cf_entity;
    private $cf_category;
    private $key;
    private $order;

    public function index() {

        $data['text_loading'] = $this->language->get('text_loading');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['continue'] = $this->url->link('extension/payment/hipaymbway/redirect');
        return $this->load->view('extension/payment/hipaymbway', $data);
    }

    public function notification() {


        $entityBody = file_get_contents('php://input');
        $notification = new MbwayNotification($entityBody);
        if ($notification->get_isJson() === false) {
            die("Invalid notification received.");
        }

        $notification_cart_id = $notification->get_ClientExternalReference();
        $transactionId = $notification->get_OperationId();
        $transactionAmount = $notification->get_Amount();
        $transactionStatusCode = $notification->get_StatusCode();

        $this->load->model('checkout/order');
        $data = $this->model_checkout_order->getOrder($notification_cart_id);

        switch ($transactionStatusCode) {
            case "c1":
                if ($this->config->get('hipaymbway_pending_status') === $data["order_status_id"]) {
                    $check = $this->checkTransaction($transactionId);
                    if ($check !== false && $check['detailStatusCode'] == $transactionStatusCode && $transactionId == $check['detailOperationId']) {
                        $this->model_checkout_order->addOrderHistory($notification_cart_id, $this->config->get('hipaymbway_accepted_status'), '', true);
                        $this->db->query("UPDATE " . DB_PREFIX . "hipaymbway SET status = '" . $transactionStatusCode . "' WHERE  `reference` = '" . $transactionId . "' and `orderID` = '" . $notification_cart_id . "' LIMIT 1");
                        print "MB WAY payment confirmed for transaction $transactionId." . PHP_EOL;
                    }
                }
                break;
            case "c3":
            case "c6":
            case "vp1":
                //do nothing
                print "Waiting capture notification for transaction $transactionId." . PHP_EOL;
                break;
            case "ap1":
                //todo
                print "Refunded transaction $transactionId." . PHP_EOL;
                break;
            case "c2":
            case "c4":
            case "c5":
            case "c7":
            case "c8":
            case "c9":
            case "vp2":

                if ($this->config->get('hipaymbway_pending_status') === $data["order_status_id"]) {
                    $check = $this->checkTransaction($transactionId);
                    if ($check !== false && $check['detailStatusCode'] == $transactionStatusCode && $transactionId == $check['detailOperationId']) {
                        $this->model_checkout_order->addOrderHistory($notification_cart_id, $this->config->get('hipaymbway_denied_status'), '', true);
                        $this->db->query("UPDATE " . DB_PREFIX . "hipaymbway SET status = '" . $transactionStatusCode . "' WHERE  `reference` = '" . $transactionId . "' and `orderID` = '" . $notification_cart_id . "' LIMIT 1");
                        print "MB WAY payment cancelled transaction $transactionId." . PHP_EOL;
                    }
                }
                break;
        }

        return true;
    }

    public function redirect() {

        $this->load->model('checkout/order');
        $data = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        if ($this->config->get('hipaymbway_pending_status') === $data["order_status_id"])
            $this->response->redirect($this->url->link('checkout/success', '', true));
        else
            $this->response->redirect($this->url->link('checkout/failure', '', true));
    }

    public function confirm() {

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hipaymbway` (`id` int(11) NOT NULL AUTO_INCREMENT,`reference` text NOT NULL,`entity` text NOT NULL,`value` float(10,2) NOT NULL,`status` varchar(5) DEFAULT '0',`sandbox` tinyint(1) DEFAULT '1',`key` text,`orderID` int(11) DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;");
        $this->order = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int) $this->session->data['order_id'] . "'");

        $this->load->model('checkout/order');
        $data = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->order->row['total'] = $data['total'];

        $this->cf_username = $this->config->get('hipaymbway_username');
        $this->cf_password = $this->config->get('hipaymbway_password');
        $this->cf_mode = $this->config->get('hipaymbway_mode');
        $this->cf_entity = $this->config->get('hipaymbway_entity');
        $this->cf_category = $this->config->get('hipaymbway_category');
        $this->key = md5(rand(0, 10000) . time());

        $notificationUrl = $this->url->link('extension/payment/hipaymbway/notification');

        $customerPhone = $this->order->row['telephone'];
        $customerEmail = $this->order->row['email'];
        $merchantId = $this->session->data['order_id'];
        $orderDescription = $this->session->data['order_id'];
        $customerVATNumber = "";
        $customerName = "";

        $order_total = $this->order->row['total'];
        $order_total = number_format($this->order->row['total'], 2, ".", "");

        $mbway = new MbwayClient($this->cf_mode);
        $mbwayRequestTransaction = new MbwayRequestTransaction($this->cf_username, $this->cf_password, $order_total, $customerPhone, $customerEmail, $merchantId, $category, $notificationUrl, $this->cf_entity);
        $mbwayRequestTransaction->set_description($orderDescription);
        $mbwayRequestTransaction->set_clientVATNumber($customerVATNumber);
        $mbwayRequestTransaction->set_clientName($customerName);
        $mbwayRequestTransactionResult = new MbwayRequestTransactionResponse($mbway->createPayment($mbwayRequestTransaction)->CreatePaymentResult);

        if ($mbwayRequestTransactionResult->get_Success() && $mbwayRequestTransactionResult->get_ErrorCode() == "0") {

            switch ($mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_StatusCode()) {
                case "vp1":
                    $transactionId = $mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_OperationId();

                    $this->db->query("INSERT INTO " . DB_PREFIX . "hipaymbway (`reference`, `entity`, `value`, `status`, `key`, `orderID`,`sandbox`) VALUES ('" . $transactionId . "', '" . $this->cf_entity . "'," . $order_total . ", 'vp1', '" . $this->key . "', " . $this->session->data["order_id"] . "," . $this->cf_mode . ")");
                    $this->load->model('checkout/order');

                    if ($this->session->data['payment_method']['code'] == 'hipaymbway') {
                        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('hipaymbway_pending_status'), 'MB WAY: ' . $transactionId, true);
                    }


                    break;
                default:
                    $this->load->model('checkout/order');
                    $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('hipaymbway_denied_status'));
            }
        } else {
            $this->load->model('checkout/order');
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('hipaymbway_denied_status'), $mbwayRequestTransactionResult->get_ErrorDescription());
        }
    }

    private function checkTransaction($transactionId) {

        $this->cf_username = $this->config->get('hipaymbway_username');
        $this->cf_password = $this->config->get('hipaymbway_password');
        $this->cf_mode = $this->config->get('hipaymbway_mode');
        $this->cf_entity = $this->config->get('hipaymbway_entity');
        $this->cf_category = $this->config->get('hipaymbway_category');

        $mbway = new MbwayClient($this->cf_mode);
        $mbwayRequestDetails = new MbwayRequestDetails($this->cf_username, $this->cf_password, $transactionId, $this->cf_entity);
        $mbwayRequestDetailsResult = new MbwayRequestDetailsResponse($mbway->getPaymentDetails($mbwayRequestDetails)->GetPaymentDetailsResult);

        if ($mbwayRequestDetailsResult->get_ErrorCode() <> 0 || !$mbwayRequestDetailsResult->get_Success()) {
            return false;
        } else {
            $result == array();
            $result['detailStatusCode'] = $mbwayRequestDetailsResult->get_MBWayPaymentDetails()->get_StatusCode();
            $result['detailOperationId'] = $mbwayRequestDetailsResult->get_MBWayPaymentDetails()->get_OperationId();
            return $result;
        }
    }

}

?>
