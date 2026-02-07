<?php

namespace App\Services;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Helper;
class PaymentService
{
    protected $merchantAuthentication;

    public function __construct()
    {
        $this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $this->merchantAuthentication->setName(env('AUTHORIZE_NET_API_LOGIN_ID'));
        $this->merchantAuthentication->setTransactionKey(env('AUTHORIZE_NET_TRANSACTION_KEY'));
    }

    public function processPayment($cardNumber, $expiryDate, $cardCode, $amount, $userDetails, $orderDetails)
    {
        // 1. Set up the credit card details
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expiryDate);
        $creditCard->setCardCode($cardCode);

        $paymentType = new AnetAPI\PaymentType();
        $paymentType->setCreditCard($creditCard);

        // 2. Set up billing address
        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($userDetails['first_name']);
        $billTo->setLastName($userDetails['last_name']);
        $billTo->setAddress($userDetails['address']);
        $billTo->setCity($userDetails['city']);
        $billTo->setState($userDetails['state']);
        $billTo->setZip($userDetails['zip']);
        $billTo->setCountry($userDetails['country']);
        $billTo->setEmail($userDetails['email']);

        // 3. Add order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($orderDetails['invoice_number']);
        $order->setDescription($orderDetails['description']);

        // 4. Create the transaction request
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setPayment($paymentType);
        $transactionRequestType->setBillTo($billTo);
        $transactionRequestType->setOrder($order);

        // 5. Assemble and execute the request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setTransactionRequest($transactionRequestType);

        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(env('AUTHORIZE_NET_ENV') === 'live' 
            ? \net\authorize\api\constants\ANetEnvironment::PRODUCTION 
            : \net\authorize\api\constants\ANetEnvironment::SANDBOX);

        // 6. Handle the response
        if ($response != null && $response->getMessages()->getResultCode() == "Ok") {
            // Helper::pr($response);
            return [
                'success' => true,
                'transaction_id' => $response->getTransactionResponse()->getTransId(),
                'response_code' => $response->getTransactionResponse()->getResponseCode()
            ];
        } else {
            $errorMessages = $response->getMessages()->getMessage();
            return [
                'success' => false,
                'error_code' => $errorMessages[0]->getCode(),
                'error_message' => $errorMessages[0]->getText()
            ];
        }
    }
}
