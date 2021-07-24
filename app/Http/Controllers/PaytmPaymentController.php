<?php

namespace App\Http\Controllers;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use Illuminate\Http\Request;

class PaytmPaymentController extends Controller
{
    /**
     * Redirect the user to the Payment Gateway.
     *
     * @return Response
     */
    public function order()
    {
        $payment = PaytmWallet::with('receive');
        $payment->prepare([
            'order' => '1092',
            'user' => 3,
            'mobile_number' => '01759412381',
            'email' => 'showrov2185@gmail.com',
            'amount' => 100,
            'callback_url' => 'http://localhost/ecommerce-sixvaly/payment/status'
        ]);
        return $payment->receive();
    }

    /**
     * Obtain the payment information.
     *
     * @return Object
     */
    public function paymentCallback()
    {
        $transaction = PaytmWallet::with('receive');

        $response = $transaction->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm

        if($transaction->isSuccessful()){
            return 'ok';
            //Transaction Successful
        }else if($transaction->isFailed()){
            return 'failed';
            //Transaction Failed
        }else if($transaction->isOpen()){
            return 'isopen';
            //Transaction Open/Processing
        }
        $transaction->getResponseMessage(); //Get Response Message If Available
        //get important parameters via public methods
        $transaction->getOrderId(); // Get order id
        $transaction->getTransactionId(); // Get transaction id
    }

    public function statusCheck(){
        $status = PaytmWallet::with('status');
        $status->prepare(['order' => '1092']);
        $status->check();

        $response = $status->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=txn-status-api-description

        if($status->isSuccessful()){
            //Transaction Successful
        }else if($status->isFailed()){
            //Transaction Failed
        }else if($status->isOpen()){
            //Transaction Open/Processing
        }
        $status->getResponseMessage(); //Get Response Message If Available
        //get important parameters via public methods
        $status->getOrderId(); // Get order id
        $status->getTransactionId(); // Get transaction id
    }
}

