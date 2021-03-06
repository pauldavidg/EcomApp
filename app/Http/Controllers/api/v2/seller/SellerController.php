<?php

namespace App\Http\Controllers\api\v2\seller;

use App\CPU\BackEndHelper;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use App\Model\SellerWallet;
use App\Model\Shop;
use App\Model\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class SellerController extends Controller
{
    public function shop_info(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => 'Your existing session token does not authorize you any more'
            ], 401);
        }

        return response()->json(Shop::where(['seller_id' => $seller['id']])->first(), 200);
    }

    public function seller_info(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => 'Your existing session token does not authorize you any more'
            ], 401);
        }

        return response()->json(Seller::with(['wallet'])->where(['id' => $seller['id']])->first(), 200);
    }

    public function shop_info_update(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => 'Your existing session token does not authorize you any more'
            ], 401);
        }

        $old_image = Shop::where(['seller_id' => $seller['id']])->first()->image;
        $image = $request->file('image');
        if ($image != null) {
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('shop')) {
                Storage::disk('public')->makeDirectory('shop');
            }

            if (Storage::disk('public')->exists('shop/' . $old_image)) {
                Storage::disk('public')->delete('shop/' . $old_image);
            }

            $note_img = Image::make($image)->stream();
            Storage::disk('public')->put('shop/' . $imageName, $note_img);
        } else {
            $imageName = $old_image;
        }

        Shop::where(['seller_id' => $seller['id']])->update([
            'name' => $request['name'],
            'address' => $request['address'],
            'contact' => $request['contact'],
            'image' => $imageName,
            'updated_at' => now()
        ]);

        return response()->json('Shop info updated successfully!', 200);
    }

    public function seller_info_update(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);

        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => 'Your existing session token does not authorize you any more'
            ], 401);
        }

        $old_image = Seller::where(['id' => $seller['id']])->first()->image;
        $image = $request->file('image');
        if ($image != null) {
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('seller')) {
                Storage::disk('public')->makeDirectory('seller');
            }

            if (Storage::disk('public')->exists('seller/' . $old_image)) {
                Storage::disk('public')->delete('seller/' . $old_image);
            }

            $note_img = Image::make($image)->stream();
            Storage::disk('public')->put('seller/' . $imageName, $note_img);
        } else {
            $imageName = $old_image;
        }

        Seller::where(['id' => $seller['id']])->update([
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'bank_name' => $request['bank_name'],
            'branch' => $request['branch'],
            'account_no' => $request['account_no'],
            'holder_name' => $request['holder_name'],
            'password' => $request['password'] != null ? bcrypt($request['password']) : Seller::where(['id' => $seller['id']])->first()->password,
            'image' => $imageName,
            'updated_at' => now()
        ]);

        if ($request['password'] != null) {
            Seller::where(['id' => $seller['id']])->update([
                'auth_token' => Str::random('50')
            ]);
        }

        return response()->json('Info updated successfully!', 200);
    }

    public function withdraw_request(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => 'Your existing session token does not authorize you any more'
            ], 401);
        }
        $withdraw = SellerWallet::where('seller_id', $seller['id'])->first();
        if ($withdraw->balance >= BackEndHelper::currency_to_usd($request['amount']) && $request['amount'] > 1) {
            $data = [
                'seller_id' => $seller['id'],
                'amount' => BackEndHelper::currency_to_usd($request['amount']),
                'transaction_note' => null,
                'approved' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
            DB::table('withdraw_requests')->insert($data);
            SellerWallet::where('seller_id', $seller['id'])->decrement('balance', BackEndHelper::currency_to_usd($request['amount']));
            return response()->json('Withdraw request sent successfully!', 200);
        }
        return response()->json('Invalid withdraw request', 400);
    }

    public function close_withdraw_request(Request $request)
    {
        $data = Helpers::get_seller_by_token($request);
        if ($data['success'] == 1) {
            $seller = $data['data'];
        } else {
            return response()->json([
                'auth-001' => 'Your existing session token does not authorize you any more'
            ], 401);
        }

        $withdraw_request = WithdrawRequest::find($request['id']);
        if (isset($withdraw_request) && $withdraw_request->approved == 0) {
            SellerWallet::where('seller_id', $seller['id'])->increment('balance', BackEndHelper::currency_to_usd($withdraw_request['amount']));
            $withdraw_request->delete();
            return response()->json('Withdraw request has been closed successfully!', 200);
        }
        return response()->json('Withdraw request is invalid', 400);
    }
}
