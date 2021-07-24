<?php

namespace App\Http\Controllers\Seller;

use App\CPU\BackEndHelper;
use App\Http\Controllers\Controller;
use App\Model\SellerWallet;
use App\Model\WithdrawRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    public function w_request(Request $request)
    {
        $w = SellerWallet::where('seller_id', auth()->guard('seller')->id())->first();
        if ($w->balance >= BackEndHelper::currency_to_usd($request['amount']) && $request['amount'] > 1) {
            $data = [
                'seller_id' => auth()->guard('seller')->user()->id,
                'amount' => BackEndHelper::currency_to_usd($request['amount']),
                'transaction_note' => null,
                'approved' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
            DB::table('withdraw_requests')->insert($data);
            SellerWallet::where('seller_id', auth()->guard('seller')->user()->id)->decrement('balance', BackEndHelper::currency_to_usd($request['amount']));
            Toastr::success('Withdraw request has been sent.');
            return redirect()->back();
        }

        Toastr::error('invalid request.!');
        return redirect()->back();
    }

    public function close_request($id)
    {
        $wr = WithdrawRequest::find($id);
        if ($wr->approved == 0) {
            SellerWallet::where('seller_id', auth()->guard('seller')->user()->id)->increment('balance', BackEndHelper::currency_to_usd($wr['amount']));
        }
        $wr->delete();
        Toastr::success('request closed!');
        return back();
    }
}
