<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>
        @yield('title')
    </title>
    <!-- SEO Meta Tags-->
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- Viewport-->
    <meta name="_token" content="{{csrf_token()}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon and Touch Icons-->
    <link rel="apple-touch-icon" sizes="180x180" href="">
    <link rel="icon" type="image/png" sizes="32x32" href="">
    <link rel="icon" type="image/png" sizes="16x16" href="">

    <link rel="stylesheet" media="screen"
          href="{{asset('public/assets/front-end')}}/vendor/simplebar/dist/simplebar.min.css"/>
    <link rel="stylesheet" media="screen"
          href="{{asset('public/assets/front-end')}}/vendor/tiny-slider/dist/tiny-slider.css"/>
    <link rel="stylesheet" media="screen"
          href="{{asset('public/assets/front-end')}}/vendor/drift-zoom/dist/drift-basic.min.css"/>
    <link rel="stylesheet" media="screen"
          href="{{asset('public/assets/front-end')}}/vendor/lightgallery.js/dist/css/lightgallery.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/back-end')}}/css/toastr.css"/>
    <!-- Main Theme Styles + Bootstrap-->
    <link rel="stylesheet" media="screen" href="{{asset('public/assets/front-end')}}/css/theme.min.css">
    <link rel="stylesheet" media="screen" href="{{asset('public/assets/front-end')}}/css/slick.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('public/assets/back-end')}}/css/toastr.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/front-end')}}/css/master.css"/>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Titillium+Web:wght@400;600;700&display=swap"
        rel="stylesheet">

    @stack('css_or_js')

    <style>
        .dropdown-item:hover, .dropdown-item:focus {
            color: darkblue;
            text-decoration: none;
            background-color: rgba(0, 0, 0, 0)
        }

        .dropdown-item.active, .dropdown-item:active {
            color: darkblue;
            text-decoration: none;
            background-color: rgba(0, 0, 0, 0)
        }

        .topbar {
            background-color: #efefef;
        }

        .topbar a {
            color: black !important;
        }

        .navbar-light .navbar-tool-icon-box {
            color: {{$web_config['primary_color']}};
        }

        .search_button {
            background-color: {{$web_config['primary_color']}};
            border: none;
        }

        .search_form {
            border: 1px solid{{$web_config['primary_color']}};
            border-radius: 5px;
        }

        .nav-link {
            color: white !important;
        }

        .navbar-stuck-menu {
            background-color: {{$web_config['primary_color']}};
            min-height: 0;
            padding-top: 0;
            padding-bottom: 0;
        }

        .mega-nav {
            background: white;
            position: relative;
            margin-top: 6px;
            line-height: 17px;
            width: 251px;
            border-radius: 3px;
        }

        .mega-nav .nav-item .nav-link {
            padding-top: 11px !important;
            color: {{$web_config['primary_color']}}                  !important;
            font-size: 20px;
            font-weight: 600;
            padding-left: 20px !important;
        }

        .nav-item .dropdown-toggle::after {
            margin-left: 20px !important;
        }

        .navbar-tool-text {
            padding-left: 5px !important;
            font-size: 16px;
        }

        .navbar-tool-text > small {
            color: #4b566b !important;
        }

        .modal-header .nav-tabs .nav-item .nav-link {
            color: black !important;
            /*border: 1px solid #E2F0FF;*/
        }

        .checkbox-alphanumeric::after,
        .checkbox-alphanumeric::before {
            content: '';
            display: table;
        }

        .checkbox-alphanumeric::after {
            clear: both;
        }

        .checkbox-alphanumeric input {
            left: -9999px;
            position: absolute;
        }

        .checkbox-alphanumeric label {
            width: 2.25rem;
            height: 2.25rem;
            float: left;
            padding: 0.375rem 0;
            margin-right: 0.375rem;
            display: block;
            color: #818a91;
            font-size: 0.875rem;
            font-weight: 400;
            text-align: center;
            background: transparent;
            text-transform: uppercase;
            border: 1px solid #e6e6e6;
            border-radius: 2px;
            -webkit-transition: all 0.3s ease;
            -moz-transition: all 0.3s ease;
            -o-transition: all 0.3s ease;
            -ms-transition: all 0.3s ease;
            transition: all 0.3s ease;
            transform: scale(0.95);
        }

        .checkbox-alphanumeric-circle label {
            border-radius: 100%;
        }

        .checkbox-alphanumeric label > img {
            max-width: 100%;
        }

        .checkbox-alphanumeric label:hover {
            cursor: pointer;
            border-color: {{$web_config['primary_color']}};
        }

        .checkbox-alphanumeric input:checked ~ label {
            transform: scale(1.1);
            border-color: red !important;
        }

        .checkbox-alphanumeric--style-1 label {
            width: auto;
            padding-left: 1rem;
            padding-right: 1rem;
            border-radius: 2px;
        }

        .d-table.checkbox-alphanumeric--style-1 {
            width: 100%;
        }

        .d-table.checkbox-alphanumeric--style-1 label {
            width: 100%;
        }

        /* CUSTOM COLOR INPUT */
        .checkbox-color::after,
        .checkbox-color::before {
            content: '';
            display: table;
        }

        .checkbox-color::after {
            clear: both;
        }

        .checkbox-color input {
            left: -9999px;
            position: absolute;
        }

        .checkbox-color label {
            width: 2.25rem;
            height: 2.25rem;
            float: left;
            padding: 0.375rem;
            margin-right: 0.375rem;
            display: block;
            font-size: 0.875rem;
            text-align: center;
            opacity: 0.7;
            border: 2px solid #d3d3d3;
            border-radius: 50%;
            -webkit-transition: all 0.3s ease;
            -moz-transition: all 0.3s ease;
            -o-transition: all 0.3s ease;
            -ms-transition: all 0.3s ease;
            transition: all 0.3s ease;
            transform: scale(0.95);
        }

        .checkbox-color-circle label {
            border-radius: 100%;
        }

        .checkbox-color label:hover {
            cursor: pointer;
            opacity: 1;
        }

        .checkbox-color input:checked ~ label {
            transform: scale(1.1);
            opacity: 1;
            border-color: red !important;
        }

        .checkbox-color input:checked ~ label:after {
            content: "\f121";
            font-family: "Ionicons";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .card-img-top img, figure {
            max-width: 200px;
            max-height: 200px !important;
            vertical-align: middle;
        }

        .product-card {
            box-shadow: 1px 1px 6px #00000014;
            border-radius: 5px;
            height: 380px;
        }

        .product-card .card-header {
            /*background-color: #F9F9F9 ;*/
            height: 268px;
            text-align: center;
            background: #F9F9F9 0% 0% no-repeat padding-box;
            border-radius: 5px 5px 0px 0px;
        }

        .product-title1 {
            font-family: 'Roboto', sans-serif !important;
            font-weight: 400 !important;
            font-size: 22px !important;
            color: #000000 !important;
            position: relative;
            display: inline-block;
            word-wrap: break-word;
            overflow: hidden;
            max-height: 2.4em; /* (Number of lines you want visible) * (line-height) */
            line-height: 1.2em;
        }

        .product-title {
            font-family: 'Roboto', sans-serif !important;
            font-weight: 400 !important;
            font-size: 22px !important;
            color: #000000 !important;
        }

        .product-price .text-accent {
            font-family: 'Roboto', sans-serif;
            font-weight: 700;
            font-size: 17px;
            color: {{$web_config['primary_color']}};
        }

        .feature_header {
            display: flex;
            justify-content: center;

        }

        .feature_header span {
            padding-right: 15px;
            padding-left: 15px;
            font-weight: 700;
            font-size: 25px;
            background-color: #ffffff;
            text-transform: uppercase;
        }

        @media (max-width: 768px ) {
            .feature_header {
                margin-top: 0;
                display: flex;
                justify-content: flex-start !important;

            }

            .feature_header span {
                padding-right: 0;
                padding-left: 0;
                font-weight: 700;
                font-size: 25px;
                background-color: #ffffff;
                text-transform: uppercase;
            }

            .view_border {
                margin: 16px 0px;
                border-top: 2px solid #E2F0FF !important;
            }

        }

        .scroll-bar {
            max-height: calc(100vh - 100px);
            overflow-y: auto !important;
        }

        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey;
            border-radius: 5px;
        }

        ::-webkit-scrollbar {
            width: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: {{$web_config['primary_color']}};
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: {{$web_config['primary_color']}};
        }

        .mobileshow {
            display: none;
        }

        @media screen and (max-width: 500px) {
            .mobileshow {
                display: block;
            }
        }

        [type="radio"] {
            border: 0;
            clip: rect(0 0 0 0);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
        }

        [type="radio"] + span:after {
            content: '';
            display: inline-block;
            width: 1.1em;
            height: 1.1em;
            vertical-align: -0.10em;
            border-radius: 1em;
            border: 0.35em solid #fff;
            box-shadow: 0 0 0 0.10em #7d8a82;
            margin-left: 0.75em;
            transition: 0.5s ease all;
        }

        [type="radio"]:checked + span:after {
            background: #7d8a82;
            box-shadow: 0 0 0 0.10em#7d8a82;;
        }

        [type="radio"]:focus + span::before {
            font-size: 1.2em;
            line-height: 1;
            vertical-align: -0.125em;
        }


        .checkbox-color label {
            box-shadow: 0px 3px 6px #0000000D;
            border: none;
            border-radius: 3px !important;
            max-height: 35px;
        }

        .checkbox-color input:checked ~ label {
            transform: scale(1.1);
            opacity: 1;
            border: 1px solid #ffb943 !important;
        }

        .checkbox-color input:checked ~ label:after {
            font-family: "Ionicons", serif;
            position: absolute;
            content: "\2713" !important;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .navbar-tool .navbar-tool-label {
            position: absolute;
            top: -.3125rem;
            right: -.3125rem;
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 50%;
            background-color: {{$web_config['secondary_color']}}                 !important;
            color: #fff;
            font-size: .75rem;
            font-weight: 500;
            text-align: center;
            line-height: 1.25rem;
        }

        .btn-primary {
            color: #fff;
            background-color: {{$web_config['primary_color']}}                 !important;
            border-color: {{$web_config['primary_color']}}                 !important;
        }

        .btn-primary:hover {
            color: #fff;
            background-color: {{$web_config['primary_color']}}                 !important;
            border-color: {{$web_config['primary_color']}}                 !important;
        }

        .btn-secondary {
            color: #fff;
            background-color: {{$web_config['secondary_color']}}                 !important;
            border-color: {{$web_config['secondary_color']}}                 !important;
        }

        .btn-secondary:hover {
            color: #fff;
            background-color: {{$web_config['secondary_color']}}                 !important;
            border-color: {{$web_config['secondary_color']}}                 !important;
        }

        .btn-outline-accent:hover {
            color: #fff;
            background-color: {{$web_config['primary_color']}};
            border-color: {{$web_config['primary_color']}};
        }

        .btn-outline-accent {
            color: {{$web_config['primary_color']}};
            border-color: {{$web_config['primary_color']}};
        }

        .text-accent {
            font-family: 'Roboto', sans-serif;
            font-weight: 700;
            font-size: 18px;
            color: {{$web_config['primary_color']}};
        }

        a {
            color: {{$web_config['primary_color']}};
            text-decoration: none;
            background-color: transparent
        }

        a:hover {
            color: {{$web_config['secondary_color']}};
            text-decoration: none
        }

        .active-menu {
            color: #6cd26c !important;
        }

        .page-item.active > .page-link {
            box-shadow: 0 0.5rem 1.125rem -0.425rem{{$web_config['primary_color']}}








        }

        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: {{$web_config['primary_color']}};
            border-color: rgba(0, 0, 0, 0)
        }

        .btn-outline-accent:not(:disabled):not(.disabled):active, .btn-outline-accent:not(:disabled):not(.disabled).active, .show > .btn-outline-accent.dropdown-toggle {
            color: #fff;
            background-color: {{$web_config['secondary_color']}};
            border-color: {{$web_config['secondary_color']}};
        }

        .btn-outline-primary {
            color: {{$web_config['primary_color']}};
            border-color: {{$web_config['primary_color']}};
        }

        .btn-outline-primary:hover {
            color: #fff;
            background-color: {{$web_config['secondary_color']}};
            border-color: {{$web_config['secondary_color']}};
        }

        .btn-outline-primary:focus, .btn-outline-primary.focus {
            box-shadow: 0 0 0 0{{$web_config['secondary_color']}};
        }

        .btn-outline-primary.disabled, .btn-outline-primary:disabled {
            color: #6f6f6f;
            background-color: transparent
        }

        .btn-outline-primary:not(:disabled):not(.disabled):active, .btn-outline-primary:not(:disabled):not(.disabled).active, .show > .btn-outline-primary.dropdown-toggle {
            color: #fff;
            background-color: {{$web_config['primary_color']}};
            border-color: {{$web_config['primary_color']}};
        }

        .btn-outline-primary:not(:disabled):not(.disabled):active:focus, .btn-outline-primary:not(:disabled):not(.disabled).active:focus, .show > .btn-outline-primary.dropdown-toggle:focus {
            box-shadow: 0 0 0 0{{$web_config['primary_color']}};
        }

        .product-title > a {
            transition: color 0.25s ease-in-out;
            color: {{$web_config['primary_color']}};
            text-decoration: none !important
        }

        .product-title > a:hover {
            color: {{$web_config['secondary_color']}}
        }

        .stripe-button-el{
            display: none!important;
        }
        .razorpay-payment-button{
            display: none!important;
        }
    </style>
</head>
<!-- Body-->
<body class="toolbar-enabled">
<!-- Page Content-->
<div class="container pb-5 mb-2 mb-md-4">
    <div class="row">
        <div class="col-md-12 mb-5 pt-5">
            <center class="feature_header text-center" style="background: #dcdcdc;line-height: 1px">
                <span>{{ trans('messages.payment_method')}}</span>
            </center>
        </div>
        <section class="col-lg-12">
            <div class="checkout_details mt-3">
                <h2 class="h6 pb-3 mb-2 mt-5">{{trans('messages.choose_payment')}}</h2>
                <div class="row">
                    <div class="col-md-6 mb-4" style="cursor: pointer">
                        @php($config=\App\CPU\Helpers::get_business_settings('ssl_commerz_payment'))
                        @if($config['status'])
                            <div class="card" onclick="setPaymentMethod('ssl_commerz_payment')">
                                <div class="card-body">
                                    <input type="radio" name="payment_gateway"
                                           id="ssl_commerz_payment" {{session('payment_method')=='ssl_commerz_payment'?'checked':''}}>
                                    <span class="checkmark" style="margin-right: 10px"></span>
                                    <span>{{trans('messages.ssl_commerz_payment')}}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-4" style="cursor: pointer">
                        @php($config=\App\CPU\Helpers::get_business_settings('paypal'))
                        @if($config['status'])
                            <div class="card" onclick="setPaymentMethod('paypal')">
                                <div class="card-body">
                                    <input type="radio" name="payment_gateway"
                                           id="paypal" {{session('payment_method')=='paypal'?'checked':''}}>
                                    <span class="checkmark" style="margin-right: 10px"></span>
                                    <span>{{trans('messages.paypal_online_payent')}}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-4" style="cursor: pointer">
                        @php($config=\App\CPU\Helpers::get_business_settings('stripe'))
                        @if($config['status'])
                            <div class="card" onclick="setPaymentMethod('stripe')">
                                <div class="card-body">
                                    <input type="radio" name="payment_gateway"
                                           id="stripe" {{session('payment_method')=='stripe'?'checked':''}}>
                                    <span class="checkmark" style="margin-right: 10px"></span>
                                    <span>{{trans('messages.stripe_online_payment')}}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-4" style="cursor: pointer">
                        @php($config=\App\CPU\Helpers::get_business_settings('razor_pay'))
                        @if($config['status'])
                            <div class="card" onclick="setPaymentMethod('razor_pay')">
                                <div class="card-body">
                                    <input type="radio" name="payment_gateway"
                                           id="razor_pay" {{session('payment_method')=='razor_pay'?'checked':''}}>
                                    <span class="checkmark" style="margin-right: 10px"></span>
                                    <span>Razor Pay</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-4" style="cursor: pointer">
                        @php($config=\App\CPU\Helpers::get_business_settings('paytm'))
                        @if($config['status'])
                            <div class="card" onclick="setPaymentMethod('paytm')">
                                <div class="card-body">
                                    <input type="radio" name="payment_gateway"
                                           id="paytm" {{session('paytm')=='ssl_commerz_payment'?'checked':''}}>
                                    <span class="checkmark" style="margin-right: 10px"></span>
                                    <span>{{trans('messages.paytm_online_payment')}}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="col-md-12 mb-5 pt-5">
            @if(session('payment_method')=='ssl_commerz_payment')
                <form action="{{ url('/pay-ssl') }}" method="POST" class="needs-validation">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token"/>
                    <button class="btn btn-primary btn-block" type="submit">
                        <i class="czi-card"></i> Pay Now
                    </button>
                </form>
            @elseif(session('payment_method')=='paypal')
                <form class="needs-validation" method="POST" id="payment-form"
                      action="{{route('pay-paypal')}}">
                    {{ csrf_field() }}
                    <button class="btn btn-primary btn-block" type="submit">
                        <i class="czi-card"></i> Pay Now
                    </button>
                </form>
            @elseif(session('payment_method')=='stripe')
                @php($config=\App\CPU\Helpers::get_business_settings('stripe'))
                <form class="needs-validation" method="POST" id="payment-form"
                      action="{{route('pay-stripe')}}">
                    {{ csrf_field() }}
                    <button class="btn btn-primary btn-block" type="button"
                            onclick="$('.stripe-button-el').click()">
                        <i class="czi-card"></i> Pay Now
                    </button>
                    @php($order=\App\Model\Order::find(session('mobile_app_payment_order_id')))
                    <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="{{$config['published_key']}}"
                            data-amount="{{$order->order_amount*100}}"
                            data-name="{{auth('customer')->check()?auth('customer')->user()->f_name:''}}"
                            data-description=""
                            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                            data-locale="auto"
                            data-currency="usd">
                    </script>
                </form>
            @elseif(session('payment_method')=='razor_pay')
                @php($config=\App\CPU\Helpers::get_business_settings('razor_pay'))
                @php($order=\App\Model\Order::find(session('mobile_app_payment_order_id')))
                <form action="{!!route('payment-razor.payment2')!!}" method="POST">
                @csrf
                <!-- Note that the amount is in paise = 50 INR -->
                    <!--amount need to be in paisa-->
                    <script src="https://checkout.razorpay.com/v1/checkout.js"
                            data-key="{{ Config::get('razor.razor_key') }}"
                            data-amount="{{$order->order_amount*100}}"
                            data-buttontext="Pay {{$order->order_amount}} INR"
                            data-name="{{\App\Model\BusinessSetting::where(['type'=>'company_name'])->first()->value}}"
                            data-description="{{$order['id']}}"
                            data-image="{{asset('storage/app/public/restaurant/'.\App\Model\BusinessSetting::where(['type'=>'company_web_logo'])->first()->value)}}"
                            data-prefill.name="{{$order->customer->f_name}}"
                            data-prefill.email="{{$order->customer->email}}"
                            data-theme.color="#ff7529">
                    </script>
                </form>

                <button class="btn btn-primary btn-block" type="button"
                        onclick="$('.razorpay-payment-button').click()">
                    <i class="czi-card"></i> Pay Now
                </button>
            @endif
        </div>

    </div>
</div>

{{--loader--}}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" style="display: none;">
                <div style="position: fixed;z-index: 9999; left: 40%;top: 37% ;width: 100%">
                    <img width="200" src="{{asset('public/assets/front-end/img/loader.gif')}}">
                </div>
            </div>
        </div>
    </div>
</div>
{{--loader--}}

<script src="{{asset('public/assets/front-end')}}/vendor/jquery/dist/jquery-2.2.4.min.js"></script>
<script src="{{asset('public/assets/front-end')}}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script
    src="{{asset('public/assets/front-end')}}/vendor/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script src="{{asset('public/assets/front-end')}}/vendor/simplebar/dist/simplebar.min.js"></script>
<script src="{{asset('public/assets/front-end')}}/vendor/tiny-slider/dist/min/tiny-slider.js"></script>
<script src="{{asset('public/assets/front-end')}}/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

<script src="{{asset('public/assets/front-end')}}/vendor/drift-zoom/dist/Drift.min.js"></script>
<script src="{{asset('public/assets/front-end')}}/vendor/lightgallery.js/dist/js/lightgallery.min.js"></script>
<script src="{{asset('public/assets/front-end')}}/vendor/lg-video.js/dist/lg-video.min.js"></script>
{{--Toastr--}}
<script src={{asset("public/assets/back-end/js/toastr.js")}}></script>
<!-- Main theme script-->
<script src="{{asset('public/assets/front-end')}}/js/theme.min.js"></script>
<script src="{{asset('public/assets/front-end')}}/js/slick.min.js"></script>

<script src="{{asset('public/assets/front-end')}}/js/sweet_alert.js"></script>

<script>
    function setPaymentMethod(name) {
        $.get({
            url: '{{ url('/') }}/customer/set-payment-method/' + name,
            success: function () {
                $('#' + name).prop('checked', true);
                toastr.success(name.replace(/_/g, " ") + ' has been selected successfully');
                location.reload();
            }
        });
    }

    setInterval(function () {
        $('.stripe-button-el').hide()
    }, 10)

    setTimeout(function () {
        $('.stripe-button-el').hide();
        $('.razorpay-payment-button').hide();
    }, 10)

</script>

</body>
</html>
