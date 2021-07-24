<?php

namespace App\Http\Controllers;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Model\Order;
use App\Model\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\Charge;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    public function __construct()
    {

    }

    public function paymentProcess()
    {
        if (session()->has('mobile_app_payment_customer_id')) {

            $order = Order::with(['details'])->where(['id' => session('mobile_app_payment_order_id')])->first();
            $data = session('data');
            $tr_ref = Str::random(6) . '-' . rand(1, 1000);

            DB::table('orders')
                ->where('id', $order['id'])
                ->update([
                    'payment_method' => session('payment_method'),
                    'transaction_ref' => $tr_ref,
                    'updated_at' => now(),
                ]);

            $config = Helpers::get_business_settings('stripe');
            Stripe::setApiKey($config['api_key']);
            $token = $_POST['stripeToken'];
            $payment = Charge::create([
                'amount' => $order->order_amount * 100,
                'currency' => 'usd',
                'description' => $tr_ref,
                'source' => $token
            ]);

            if ($payment->status == 'succeeded') {
                DB::table('orders')
                    ->where('transaction_ref', $tr_ref)
                    ->update(['order_status' => 'processing', 'payment_status' => 'paid']);

                return redirect()->route('pay-stripe.success');
            } else {
                return redirect()->route('pay-stripe.fail');
            }

        } else {

            if (session()->has('cart') == false) {
                Toastr::warning('no items in your cart.');
                return redirect()->route('home');
            }

            $customer_info = session('customer_info');
            $cart = session('cart');
            $coupon_discount = session()->has('coupon_discount') ? session('coupon_discount') : 0;

            $tran = Str::random(6) . '-' . rand(1, 1000);
            \session()->put('transaction_ref', $tran);
            $order = DB::table('orders')
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
                    'transaction_ref' => $tran,
                    'shipping_address' => $customer_info['address_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            foreach ($cart as $c) {
                $product = Product::where('id', $c['id'])->first();
                $or_d = [
                    'order_id' => $order,
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

            $config = Helpers::get_business_settings('stripe');
            Stripe::setApiKey($config['api_key']);
            $token = $_POST['stripeToken'];
            $payment = Charge::create([
                'amount' => (Order::find($order)->order_amount) * 100,
                'currency' => 'usd',
                'description' => $tran,
                'source' => $token
            ]);

            if ($payment->status == 'succeeded') {
                DB::table('orders')
                    ->where('transaction_ref', $tran)
                    ->update(['order_status' => 'processing', 'payment_status' => 'paid']);
            }
        }

        session()->forget('cart');
        session()->forget('coupon_code');
        session()->forget('coupon_discount');
        session()->forget('payment_method');
        session()->forget('shipping_method_id');

        $order_id = $order;

        return view('web-views.checkout-complete', compact('order_id'));
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
