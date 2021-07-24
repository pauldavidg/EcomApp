<?php

namespace App\Http\Controllers\Seller\Auth;

use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use App\Model\Shop;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function create()
    {
        return view('seller-views.auth.register');
    }

    public function store(Request $request)
    {

        try {
            $this->validate($request, [
                'email' => 'required|unique:sellers',
            ]);
        } catch (Exception $e) {

        }

        $exception = DB::transaction(function ($r) use ($request) {

            $seller = new Seller();
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name;
            $seller->phone = $request->phone;
            $seller->email = $request->email;
            $seller->image = json_encode(ImageManager::upload('seller/', 'png', 'image_modal'));
            $seller->password = bcrypt($request->password);
            $seller->status = "pending";
            $seller->save();

            $shop = new Shop();
            $shop->seller_id = $seller->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->shop_address;
            $shop->contact = $request->phone;
            $shop->image = json_encode(ImageManager::upload('seller/logo/', 'png', 'logo_modal'));
            $shop->save();

        });

        if (is_null($exception)) {
            Toastr::success('Shop apply successfully!');
        } else {
            Toastr::error('Something went wrong!');
        }

        return redirect()->route('seller.auth.login');

    }

}
