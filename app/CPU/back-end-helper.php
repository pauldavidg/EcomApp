<?php

namespace App\CPU;

use App\Model\BusinessSetting;
use App\Model\Currency;
use App\Model\Order;
use Illuminate\Support\Carbon;

class BackEndHelper
{
    public static function currency_to_usd($amount)
    {
        $data = BusinessSetting::where('type', 'system_default_currency')->first();
        $currency = Currency::where('id', $data->value)->first();
        $result = floatval($amount) / floatval($currency->exchange_rate);
        return $result;
    }

    public static function usd_to_currency($amount)
    {
        $data = BusinessSetting::where('type', 'system_default_currency')->first();
        $currency = Currency::where('id', $data->value)->first();
        $result = floatval($amount) * floatval($currency->exchange_rate);
        return $result;
    }

    public static function currency_symbol()
    {
        $data = BusinessSetting::where('type', 'system_default_currency')->first();
        $currency = Currency::where('id', $data->value)->first();
        return $currency->symbol;
    }

    public static function currency_code()
    {
        $data = BusinessSetting::where('type', 'system_default_currency')->first();
        $currency = Currency::where('id', $data->value)->first();
        return $currency->code;
    }
    public static function max_earning()
    {
        $data = Order::where(['order_status' => 'delivered'])->select('id', 'created_at', 'order_amount')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += $order['order_amount'];
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function max_orders()
    {
        $data = Order::select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }
}
