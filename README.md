# HiPay MBWAY extension for Opencart 2.3

## API credentials

HiPay Comprafacil API production or sandbox account credentials:
   - username
   - password
   - entity
   - category

   
## Installation
Copy the upload folder contents to your Opencart upload content. This copy will not replace any file.

Go to Extensions »» Payments and install HiPay MB WAY. Edit the extension and proceed to the configuration unit. 
 
 
## Configuration

  - Username, Password, Category id and Entity: credentials for MB WAY API 
  - Test Mode: enable or disable sandbox/test account
  - Status: Enable or disable the extension
  - Pending, Accepted and Denied Status: order status id for each scenario 
  - Geo Zone: zones for which the payment method will be available during the checkout
  - Sort order: payment method checkout order

  
## Show MB WAY reference on success page
Edit file ***catalog/controller/checkout/success.php*** and find 

    $this->cart->clear();

After that line add

    $data['hipaymbway'] = $this->db->query("SELECT reference, value FROM ".DB_PREFIX."hipaymbway WHERE orderID = ".$this->session->data['order_id']);

Then find

    $data['continue'] = $this->url->link('common/home');

and before that line add

	if (isset($data['hipaymbway']->row["reference"] )) {
		$this->load->language('extension/payment/hipaymbway');
       	$mb_table_ref = "<table border='1' cellpadding='5' cellspacing='5' style='border:1px solid #ccc;'><tr><td align='center' valign='middle'><img src='".$this->config->get('config_url')."/image/catalog/mbway.png' border=0></td><td valign='top' align='left' style='padding:12px;'><p>" . $this->language->get('hipaymbway_desc') . "</p>".$this->language->get('hipaymbway_reference').":<br><b>".$data['hipaymbway']->row["reference"]."</b><br>".$this->language->get('hipaymbway_amount').":<br><b>".number_format($data['hipaymbway']->row["value"],2)." Euros</b></td></tr></table>"; 
   		$data['text_message'] .= $mb_table_ref;
   	}	

   	

## Requirements
  - SOAP extension

Version 1.0.0.0
