<?php

namespace App\Http\Controllers;

use App\Model\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class UpdateController extends Controller
{
    public function update_software_index(){
        return view('update.update-software');
    }

    public function update_software(){
        Artisan::call('migrate', ['--force' => true]);
        $previousRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvier, $previousRouteServiceProvier);
        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        if (BusinessSetting::where(['key' => 'fcm_topic'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'fcm_topic',
                'value' => ''
            ]);
        }
        if (BusinessSetting::where(['key' => 'fcm_project_id'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'fcm_project_id',
                'value' => ''
            ]);
        }
        if (BusinessSetting::where(['key' => 'push_notification_key'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'push_notification_key',
                'value' => ''
            ]);
        }
        if (BusinessSetting::where(['key' => 'order_pending_message'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'order_pending_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => ''
                ])
            ]);
        }
        if (BusinessSetting::where(['key' => 'order_confirmation_msg'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'order_confirmation_msg',
                'value' => json_encode([
                    'status' => 0,
                    'message' => ''
                ])
            ]);
        }
        if (BusinessSetting::where(['key' => 'order_processing_message'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'order_processing_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => ''
                ])
            ]);
        }
        if (BusinessSetting::where(['key' => 'out_for_delivery_message'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'out_for_delivery_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => ''
                ])
            ]);
        }
        if (BusinessSetting::where(['key' => 'order_delivered_message'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'order_delivered_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => ''
                ])
            ]);
        }
        if (BusinessSetting::where(['key' => 'delivery_boy_assign_message'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'delivery_boy_assign_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => ''
                ])
            ]);
        }
        if (BusinessSetting::where(['key' => 'delivery_boy_start_message'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'delivery_boy_start_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => ''
                ])
            ]);
        }
        if (BusinessSetting::where(['key' => 'delivery_boy_delivered_message'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'delivery_boy_delivered_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => ''
                ])
            ]);
        }
        if (BusinessSetting::where(['key' => 'terms_and_conditions'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'terms_and_conditions',
                'value' => ''
            ]);
        }
        if (BusinessSetting::where(['key' => 'razor_pay'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'razor_pay',
                'value' => '{"status":"1","razor_key":"","razor_secret":""}'
            ]);
        }
        if (BusinessSetting::where(['key' => 'minimum_order_value'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'minimum_order_value'], [
                'value' => 1
            ]);
        }
        if (BusinessSetting::where(['key' => 'about_us'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'about_us'], [
                'value' => ''
            ]);
        }
        if (BusinessSetting::where(['key' => 'privacy_policy'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'privacy_policy'], [
                'value' => ''
            ]);
        }
        if (BusinessSetting::where(['key' => 'terms_and_conditions'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'terms_and_conditions'], [
                'value' => ''
            ]);
        }

        return redirect('/admin/auth/login');
    }
}
