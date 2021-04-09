# HiPay MB WAY SDK PHP

### Create a transaction

    use HipayMbway\MbwayClient;
    use HipayMbway\MbwayRequestTransaction;
    use HipayMbway\MbwayRequestTransactionResponse;
    
    /*
     * Account config
     * $sandbox false if account is a production account
     * $entity, $username, $password and $category are provided by customer management 
     */
    
    $sandbox = true;
    $entity = 11249;
    $username = "hpXXXXXX";
    $password = "xxxxxxxx";
    $category = 3;
    
    /*
     * Transaction parameters
     */

    $notificationUrl = 'http://mynotificationurl.com';
    $amount = 12;
    $customerPhone = "9xxxxxxxx";
    $customerEmail = "xxxxxxx@xxxxx.com";
    $merchantId = "xxxxxx";
    $orderDescription = "order description";
    $customerVATNumber = "000000000";
    $customerName = "John Doe";


    /*
     * Create a Transaction
     */
    
    $mbway = new MbwayClient($sandbox);
    $mbwayRequestTransaction = new MbwayRequestTransaction($username, $password, $amount, $customerPhone, $customerEmail, $merchantId, $category, $notificationUrl, $entity);
    $mbwayRequestTransaction->set_description($orderDescription);
    $mbwayRequestTransaction->set_clientVATNumber($customerVATNumber);
    $mbwayRequestTransaction->set_clientName($customerName);
    $mbwayRequestTransactionResult = new MbwayRequestTransactionResponse($mbway->createPayment($mbwayRequestTransaction)->CreatePaymentResult);

    /*
     * Check Transaction creation result
     */

    if ($mbwayRequestTransactionResult->get_Success() && $mbwayRequestTransactionResult->get_ErrorCode() == "0") {
        switch ($mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_StatusCode()) {
            case "vp1":
                $transactionId = $mbwayRequestTransactionResult->get_MBWayPaymentOperationResult()->get_OperationId();
                print "SUCCESS - TRANSACTION CREATED WITH ID: $transactionId" . PHP_EOL;
                break;
            case "vp2":
                print "Operation refused. Please try again or choose another payment method." . PHP_EOL;
                break;
            case "vp3":
                print "Operation refused. Limit exceeded. Please try again or choose another payment method." . PHP_EOL;
                break;
            case "er1":
                print "Operation refused. Invalid phone number. Please try again with another phone number or choose another payment method." . PHP_EOL;
                break;
            case "er2":
                print "Operation refused. Unassigned phone number. Please try again with another phone number or choose another payment method." . PHP_EOL;
                break;
            default:
                print "Operation refused. Please try again or choose another payment method." . PHP_EOL;
        }
    } else {
        print $mbwayRequestTransactionResult->get_ErrorDescription() . PHP_EOL;
    }  
        


### Check transaction status

    use HipayMbway\MbwayClient;
    use HipayMbway\MbwayRequestDetails;
    use HipayMbway\MbwayRequestResponse;
    use HipayMbway\MbwayRequestDetailsResponse;
    use HipayMbway\MbwayRequestTransactionResponse;
    use HipayMbway\MbwayPaymentDetailsResult;
       
    /*
     * Account config
     * $sandbox false if account is a production account
     * $entity, $username, $password and $category are provided by customer management 
     */
    
    $sandbox = true;
    $entity = 11249;
    $username = "hpXXXXXX";
    $password = "xxxxxxxx";
    
    /*
     * Operation parameter
     * $transactionId is the id returned on the transaction creation 
     */
    
    $transactionId = 'XXXXXXXXXXXXXXXXX';
    
    /*
     * Operation  
     */
    
    $mbway = new MbwayClient($sandbox);
    $mbwayRequestDetails = new MbwayRequestDetails($username, $password, $transactionId, $entity);
    $mbwayRequestDetailsResult = new MbwayRequestDetailsResponse($mbway->getPaymentDetails($mbwayRequestDetails)->GetPaymentDetailsResult);
    
    /*
     * Check Operation result  
     */
    
    if ($mbwayRequestDetailsResult->get_ErrorCode() <> 0 || !$mbwayRequestDetailsResult->get_Success()) {
        print "Notification: Unable to confirm payment status. Operation returned: " . $mbwayRequestDetailsResult->get_ErrorDescription() . PHP_EOL;
    } else {
    
        $detailStatusCode = $mbwayRequestDetailsResult->get_MBWayPaymentDetails()->get_StatusCode();
        $detailAmount = $mbwayRequestDetailsResult->get_MBWayPaymentDetails()->get_Amount();
        $detailOperationId = $mbwayRequestDetailsResult->get_MBWayPaymentDetails()->get_OperationId();
    
        switch ($detailStatusCode) {
            case "c1":
                print "MB WAY payment confirmed for transaction $transactionId." . PHP_EOL;
                break;
            case "c3":
            case "c6":
            case "vp1":
                print "Waiting capture notification for transaction $transactionId." . PHP_EOL;
                break;
            case "ap1":
                print "Refunded transaction $transactionId." . PHP_EOL;
                break;
            case "c2":
            case "c4":
            case "c5":
            case "c7":
            case "c8":
            case "c9":
            case "vp2":
                print "MB WAY payment cancelled transaction $transactionId." . PHP_EOL;
                break;
        }
    }



### Process notification

    use HipayMbway\MbwayNotification;
    
    $entityBody = file_get_contents('php://input');
    $notification = new MbwayNotification($entityBody);
    if ($notification->get_isJson() === false) {
        die("Invalid notification received.");
    }
    
    $notification_cart_id = $notification->get_ClientExternalReference();
    $transactionId = $notification->get_OperationId();
    $transactionAmount = $notification->get_Amount();
    $transactionStatusCode = $notification->get_StatusCode();
    
    switch ($transactionStatusCode) {
        case "c1":
            print "MB WAY payment confirmed for transaction $transactionId." . PHP_EOL;
            break;
        case "c3":
        case "c6":
        case "vp1":
            print "Waiting capture notification for transaction $transactionId." . PHP_EOL;
            break;
        case "ap1":
            print "Refunded transaction $transactionId." . PHP_EOL;
            break;
        case "c2":
        case "c4":
        case "c5":
        case "c7":
        case "c8":
        case "c9":
        case "vp2":
            print "MB WAY payment cancelled transaction $transactionId." . PHP_EOL;
            break;
    }
