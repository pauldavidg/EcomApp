<?php

namespace App\Http\Controllers;

use App\CPU\CartManager;
use App\Model\Order;
use App\Model\Product;
use App\Model\ShippingMethod;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PaypalPaymentController extends Controller
{
    public function __construct()
    {
        $paypal_conf = Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function payWithpaypal(Request $request)
    {
        if (session()->has('mobile_app_payment_customer_id')) {

            $order = Order::with(['details'])->where(['id' => session('mobile_app_payment_order_id')])->first();
            $data = session('data');
            $tr_ref = Str::random(6) . '-' . rand(1, 1000);

            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $items_array = [];
            foreach ($order->details as $k => $c) {
                $price = ($c['price'] * $c['qty'])
                + ($c['tax'] * $c['qty'])
                + ShippingMethod::find($c['shipping_method_id'])->cost
                - $c['discount'] * $c['qty']
                - $k == 0 ? $order->discount_amount : 0;
                $item = new Item();
                $item->setName($c['name'])
                    ->setCurrency('USD')
                    ->setQuantity(1)
                    ->setPrice($price);
                array_push($items_array, $item);
            }
            $item_list = new ItemList();
            $item_list->setItems($items_array);

            $amount = new Amount();
            $amount->setCurrency('USD')
                ->setTotal($order['order_amount']);

            \session()->put('transaction_ref', $tr_ref);
            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription($tr_ref);

            $update_product = DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'transaction_ref' => $tr_ref,
                    'payment_method' => session('payment_method'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        } else {
            $customer_info = session('customer_info');
            $cart = session('cart');
            $coupon_discount = session()->has('coupon_discount') ? session('coupon_discount') : 0;

            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $items_array = [];
            foreach ($cart as $k => $c) {
                $price = ($c['price'] * $c['quantity'])
                + ($c['tax'] * $c['quantity'])
                + $c['shipping_cost']
                - ($c['discount'] * $c['quantity'])
                - $k == 0 ? $coupon_discount : 0;
                $item = new Item();
                $item->setName($c['name'])
                    ->setCurrency('USD')
                    ->setQuantity(1)
                    ->setPrice($price);
                array_push($items_array, $item);
            }

            $item_list = new ItemList();
            $item_list->setItems($items_array);

            $amount = new Amount();
            $amount->setCurrency('USD')
                ->setTotal(CartManager::cart_grand_total($cart) - $coupon_discount);

            $tran = Str::random(6) . '-' . rand(1, 1000);
            \session()->put('transaction_ref', $tran);
            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription($tran);

            $update_product = DB::table('orders')
                ->where('transaction_ref', $tran)
                ->insertGetId([
                    'id' => 100000 + Order::all()->count() + 1,
                    'customer_id' => auth('customer')->id(),
                    'customer_type' => 'customer',
                    'payment_status' => 'unpaid',
                    'order_amount' => CartManager::cart_grand_total($cart) - $coupon_discount,
                    'order_status' => 'pending',
                    'payment_method' => session('payment_method'),
                    'discount_amount' => session()->has('coupon_discount') ? session('coupon_discount') : 0,
                    'discount_type' => session()->has('coupon_discount') ? 'coupon_discount' : '',
                    'shipping_address' => $customer_info['address_id'],
                    'transaction_ref' => $tran,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            foreach ($cart as $c) {
                $product = Product::where('id', $c['id'])->first();
                $or_d = [
                    'order_id' => $update_product,
                    'product_id' => $c['id'],
                    'seller_id' => $product->added_by == 'seller' ? $product->user_id : '0',
                    'product_details' => $product,
                    'qty' => $c['quantity'],
                    'price' => $c['price'],
                    'tax' => $c['tax'] * $c['quantity'],
                    'discount' => $c['discount'] * $c['quantity'],
                    'discount_type' => 'discount_on_product',
                    'variant' => $c['variant'],
                    'variation' => json_encode($c['variations']),
                    'delivery_status' => 'pending',
                    'shipping_method_id' => $c['shipping_method_id'],
                    'payment_status' => 'unpaid',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                DB::table('order_details')->insert($or_d);
            }
        }


        session()->forget('cart');
        session()->forget('coupon_code');
        session()->forget('coupon_discount');
        session()->forget('payment_method');
        session()->forget('shipping_method_id');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('paypal-status'))/** Specify return URL **/
        ->setCancelUrl(URL::route('home'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (Config::get('app.debug')) {
                Session::put('error', 'Connection timeout');
                return Redirect::route('home');
            } else {
                Session::put('error', 'Some error occur, sorry for inconvenient');
                return Redirect::route('home');
            }
        }

        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        /** add payment ID to session **/
        Session::put('paypal_payment_id', $payment->getId());
        if (isset($redirect_url)) {
            return Redirect::away($redirect_url);
        }

        Session::put('error', 'Unknown error occurred');
        return Redirect::route('paywithpaypal');

    }

    public function getPaymentStatus(Request $request)
    {
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');
        /*Session::forget('paypal_payment_id');*/
        if (empty($request['PayerID']) || empty($request['token'])) {
            Session::put('error', 'Payment failed');
            return Redirect::route('home');
        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request['PayerID']);

        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);

        if ($result->getState() == 'approved') {
            DB::table('orders')
                ->where('transaction_ref', \session('transaction_ref'))
                ->update(['order_status' => 'processing', 'payment_status' => 'paid', 'transaction_ref' => $payment_id]);
            Session::put('success', 'Payment success');
            Toastr::success('payment complete successfully!');
            $order_id = Order::where('transaction_ref', $payment_id)->first()->id;

            if (session()->has('mobile_app_payment_customer_id')) {
                return \redirect()->route('paypal-success');
            } else {
                return view('web-views.checkout-complete', compact('order_id'));
            }
        }
        Session::put('error', 'Payment failed');
        if (session()->has('mobile_app_payment_customer_id')) {
            return \redirect()->route('paypal-fail');
        } else {
            return Redirect::route('/');
        }
    }

    public function success()
    {
        if (auth('customer')->check()) {
            Toastr::success('Payment success.');
            return redirect('/account-oder');
        }
        return response()->json(['message' => 'Payment succeeded'], 200);
    }

    public function fail()
    {
        if (auth('customer')->check()) {
            Toastr::error('Payment failed.');
            return redirect('/account-oder');
        }
        return response()->json(['message' => 'Payment failed'], 403);
    }
}
