<?php

namespace App\Http\Controllers;

use App\CPU\CartManager;
use App\Library\sslcommerz\SslCommerzNotification;
use App\Model\Order;
use App\Model\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SslCommerzPaymentController extends Controller
{

    public function index(Request $request)
    {
        if (session()->has('mobile_app_payment_customer_id')) {

            $order = Order::with(['details'])->where(['id' => session('mobile_app_payment_order_id')])->first();
            $data = session('data');
            $tr_ref = Str::random(6) . '-' . rand(1, 1000);

            $post_data = array();
            $post_data['total_amount'] = $order->order_amount;
            $post_data['currency'] = "USD";
            $post_data['tran_id'] = $tr_ref;

            # CUSTOMER INFORMATION
            $post_data['cus_name'] = $data['name'];
            $post_data['cus_email'] = $data['email'];
            $post_data['cus_add1'] = $order['shipping_address'];
            $post_data['cus_add2'] = "";
            $post_data['cus_city'] = "";
            $post_data['cus_state'] = "";
            $post_data['cus_postcode'] = "";
            $post_data['cus_country'] = "";
            $post_data['cus_phone'] = $data['phone'];
            $post_data['cus_fax'] = "";

            # SHIPMENT INFORMATION
            $post_data['ship_name'] = "Shipping";
            $post_data['ship_add1'] = "address 1";
            $post_data['ship_add2'] = "address 2";
            $post_data['ship_city'] = "City";
            $post_data['ship_state'] = "State";
            $post_data['ship_postcode'] = "ZIP";
            $post_data['ship_phone'] = "";
            $post_data['ship_country'] = "Country";

            $post_data['shipping_method'] = "NO";
            $post_data['product_name'] = "Computer";
            $post_data['product_category'] = "Goods";
            $post_data['product_profile'] = "physical-goods";

            # OPTIONAL PARAMETERS
            $post_data['value_a'] = "ref001";
            $post_data['value_b'] = "ref002";
            $post_data['value_c'] = "ref003";
            $post_data['value_d'] = "ref004";

            $update_product = DB::table('orders')
                ->where('id', $order['id'])
                ->update([
                    'transaction_ref' => $tr_ref,
                    'payment_method' => session('payment_method'),
                    'updated_at' => now(),
                ]);
        } else {
            $customer_info = session('customer_info');
            $cart = session('cart');
            $coupon_discount = session()->has('coupon_discount') ? session('coupon_discount') : 0;

            $post_data = array();
            $post_data['total_amount'] = CartManager::cart_grand_total($cart) - $coupon_discount;
            $post_data['currency'] = "USD";
            $post_data['tran_id'] = Str::random(6) . '-' . rand(1, 1000); // tran_id must be unique

            # CUSTOMER INFORMATION
            $post_data['cus_name'] = auth('customer')->user()->f_name . ' ' . auth('customer')->user()->l_name;
            $post_data['cus_email'] = auth('customer')->user()->email;
            $post_data['cus_add1'] = auth('customer')->user()->street_address == null ? 'address' : auth('customer')->user()->street_address;
            $post_data['cus_add2'] = "";
            $post_data['cus_city'] = "";
            $post_data['cus_state'] = "";
            $post_data['cus_postcode'] = "";
            $post_data['cus_country'] = "";
            $post_data['cus_phone'] = auth('customer')->user()->phone;
            $post_data['cus_fax'] = "";

            # SHIPMENT INFORMATION
            $post_data['ship_name'] = "Shipping";
            $post_data['ship_add1'] = "address 1";
            $post_data['ship_add2'] = "address 2";
            $post_data['ship_city'] = "City";
            $post_data['ship_state'] = "State";
            $post_data['ship_postcode'] = "ZIP";
            $post_data['ship_phone'] = "";
            $post_data['ship_country'] = "Country";

            $post_data['shipping_method'] = "NO";
            $post_data['product_name'] = "Computer";
            $post_data['product_category'] = "Goods";
            $post_data['product_profile'] = "physical-goods";

            # OPTIONAL PARAMETERS
            $post_data['value_a'] = "ref001";
            $post_data['value_b'] = "ref002";
            $post_data['value_c'] = "ref003";
            $post_data['value_d'] = "ref004";

            #Before  going to initiate the payment order status need to insert or update as Pending.
            $update_product = DB::table('orders')
                ->where('transaction_ref', $post_data['tran_id'])
                ->insertGetId([
                    'id' => 100000 + Order::all()->count() + 1,
                    'customer_id' => auth('customer')->id(),
                    'order_amount' => $post_data['total_amount'],
                    'customer_type' => 'customer',
                    'payment_status' => 'unpaid',
                    'order_status' => 'pending',
                    'payment_method' => session('payment_method'),
                    'discount_amount' => session()->has('coupon_discount') ? session('coupon_discount') : 0,
                    'discount_type' => session()->has('coupon_discount') ? 'coupon_discount' : '',
                    'shipping_address' => $customer_info['address_id'],
                    'transaction_ref' => $post_data['tran_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
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
                    'updated_at' => now(),
                ];
                DB::table('order_details')->insert($or_d);
            }

        }

        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($post_data, 'hosted');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }

    public function success(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        $sslc = new SslCommerzNotification();

        #Check order status in order tabel against the transaction id or order id.
        $order_detials = DB::table('orders')
            ->where('transaction_ref', $tran_id)
            ->select('id', 'transaction_ref', 'order_status', 'order_amount')->first();

        if ($order_detials->order_status == 'pending' || $order_detials->order_status == 'failed' || $order_detials->order_status == 'canceled') {
            $validation = $sslc->orderValidate($tran_id, $amount, $currency, $request->all());

            if ($validation == TRUE) {
                $update_product = DB::table('orders')
                    ->where('transaction_ref', $tran_id)
                    ->update(['order_status' => 'processing', 'payment_status' => 'paid']);

                session()->forget('cart');
                session()->forget('coupon_code');
                session()->forget('coupon_discount');
                session()->forget('payment_method');
                session()->forget('shipping_method_id');

                $order_id = $order_detials->id;
                return view('web-views.checkout-complete', compact('order_id'));
            } else {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel and Transation validation failed.
                Here you need to update order status as Failed in order table.
                */
                $update_product = DB::table('orders')
                    ->where('transaction_ref', $tran_id)
                    ->update(['order_status' => 'failed']);
                echo "validation Fail";
            }
        } else if ($order_detials->order_status == 'processing' || $order_detials->order_status == 'complete') {
            $order_id = $order_detials->id;
            return view('web-views.checkout-complete', compact('order_id'));
        } else {
            echo "Invalid Transaction";
        }
    }

    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $order_detials = DB::table('orders')
            ->where('transaction_ref', $tran_id)
            ->select('transaction_ref', 'order_status', 'order_amount')->first();

        session()->forget('cart');
        session()->forget('coupon_code');
        session()->forget('coupon_discount');
        session()->forget('payment_method');
        session()->forget('shipping_method_id');

        if ($order_detials->order_status == 'pending') {
            $update_product = DB::table('orders')
                ->where('transaction_ref', $tran_id)
                ->update(['order_status' => 'failed']);
            Toastr::warning('Transaction is Falied');
        } else if ($order_detials->order_status == 'processing' || $order_detials->order_status == 'complete') {
            Toastr::warning('Transaction is already Successful');
        } else {
            Toastr::warning('Transaction is Invalid');
        }

        return view('web-views.payment-failed');
    }

    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_detials = DB::table('orders')
            ->where('transaction_ref', $tran_id)
            ->select('transaction_ref', 'order_status', 'order_amount')->first();

        if ($order_detials->order_status == 'pending') {
            $update_product = DB::table('orders')
                ->where('transaction_ref', $tran_id)
                ->update(['order_status' => 'canceled']);
            Toastr::warning('Transaction is Cancel');
        } else if ($order_detials->order_status == 'processing' || $order_detials->order_status == 'complete') {
            Toastr::warning('Transaction is already Successful');
        } else {
            Toastr::warning('Transaction is Invalid');
        }

        session()->forget('cart');
        session()->forget('coupon_code');
        session()->forget('coupon_discount');
        session()->forget('payment_method');
        session()->forget('shipping_method_id');

        return view('web-views.payment-failed');
    }

    public function ipn(Request $request)
    {
        #Received all the payement information from the gateway
        if ($request->input('tran_id')) #Check transation id is posted or not.
        {
            $tran_id = $request->input('tran_id');
            #Check order status in order tabel against the transaction id or order id.
            $order_details = DB::table('orders')
                ->where('transaction_ref', $tran_id)
                ->select('transaction_ref', 'order_status', 'order_amount')->first();

            if ($order_details->order_status == 'pending') {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($tran_id, $order_details->order_amount, 'BDT', $request->all());
                if ($validation == TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successful transaction to customer
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_ref', $tran_id)
                        ->update(['order_status' => 'processing', 'payment_status' => 'paid']);

                    echo "Transaction is successfully completed";
                } else {
                    /*
                    That means IPN worked, but Transation validation failed.
                    Here you need to update order status as Failed in order table.
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_ref', $tran_id)
                        ->update(['order_status' => 'processing', 'payment_status' => 'unpaid']);

                    echo "validation Fail";
                }

            } else if ($order_details->order_status == 'processing' || $order_details->order_status == 'complete') {

                #That means Order status already updated. No need to udate database.

                echo "Transaction is already successfully completed";
            } else {
                #That means something wrong happened. You can redirect customer to your product page.

                echo "Invalid Transaction";
            }
        } else {
            echo "Invalid Data";
        }
    }

}
