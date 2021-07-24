<?php

namespace App\Http\Controllers;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Model\Order;
use App\Model\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Redirect;
use Session;

class RazorPayController extends Controller
{
    public function payWithRazorpay()
    {
        return view('razor-pay');
    }

    public function payment(Request $request)
    {
        if (session()->has('cart') == false) {
            Toastr::warning('no items in your cart.');
            return redirect()->route('home');
        }

        $customer_info = session('customer_info');
        $cart = session('cart');
        $coupon_discount = session()->has('coupon_discount') ? session('coupon_discount') : 0;

        $tran = Str::random(6) . '-' . rand(1, 1000);
        session()->put('transaction_ref', $tran);
        $order_id = DB::table('orders')
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
                'order_id' => $order_id,
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

        try {
            //Input items of form
            $input = $request->all();
            //get API Configuration
            $api = new Api(config('razor.razor_key'), config('razor.razor_secret'));
            //Fetch payment information by razorpay_payment_id
            $payment = $api->payment->fetch($input['razorpay_payment_id']);

            if (count($input) && !empty($input['razorpay_payment_id'])) {

                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                $order = Order::where(['id' => $order_id])->first();
                DB::table('orders')
                    ->where('id', $order['id'])
                    ->update([
                        'payment_method' => 'razor_pay',
                        'transaction_ref' => $input['razorpay_payment_id'],
                        'order_status' => 'confirmed',
                        'payment_status' => 'paid',
                        'updated_at' => now(),
                    ]);

                $fcm_token = $order->customer->cm_firebase_token;
                $value = Helpers::order_status_update_message('confirmed');
                if ($value) {
                    $data = [
                        'title' => 'Order',
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            }

            session()->forget('cart');
            session()->forget('coupon_code');
            session()->forget('coupon_discount');
            session()->forget('payment_method');
            session()->forget('shipping_method_id');
        }catch (\Exception $exception){

        }

        return view('web-views.checkout-complete', compact('order_id'));
    }

    public function payment_mobile(Request $request){

        //Input items of form
        $input = $request->all();
        //get API Configuration
        $api = new Api(config('razor.razor_key'), config('razor.razor_secret'));
        //Fetch payment information by razorpay_payment_id
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                $order = Order::where(['id' => $response->description])->first();
                $tr_ref = $input['razorpay_payment_id'];
                DB::table('orders')
                    ->where('id', $order['id'])
                    ->update([
                        'payment_method' => 'razor_pay',
                        'transaction_ref' => $tr_ref,
                        'order_status' => 'confirmed',
                        'payment_status' => 'paid',
                        'updated_at' => now(),
                    ]);
            } catch (\Exception $e) {
                /*return $e->getMessage();*/
                return redirect()->route('payment-razor.fail');
            }
        }

        return redirect()->route('payment-razor.success');
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
