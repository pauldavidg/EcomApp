@extends('layouts.front-end.app')

@section('title','Payment Method Choose')

@push('css_or_js')

@endpush

@section('content')
    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4">
        <div class="row">
            <div class="col-md-12 mb-5 pt-5">
                <div class="feature_header" style="background: #dcdcdc;line-height: 1px">
                    <span>{{ trans('messages.payment_method')}}</span>
                </div>
            </div>
            <section class="col-lg-8">
                <hr>
                <div class="checkout_details mt-3">
                @include('web-views.partials._checkout-steps',['step'=>3])
                <!-- Payment methods accordion-->
                    <h2 class="h6 pb-3 mb-2 mt-5">{{trans('messages.choose_payment')}}</h2>

                    <div class="row">
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            @php($config=\App\CPU\Helpers::get_business_settings('cash_on_delivery'))
                            @if($config['status'])
                                <div class="card" onclick="setPaymentMethod('cash_on_delivery')">
                                    <div class="card-body">
                                        <input type="radio" name="payment_gateway" id="cash_on_delivery" checked>
                                        <span class="checkmark" style="margin-right: 10px"></span>
                                        <span>{{trans('messages.cash_on_delivery')}}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            @php($config=\App\CPU\Helpers::get_business_settings('ssl_commerz_payment'))
                            @if($config['status'])
                                <div class="card" onclick="setPaymentMethod('ssl_commerz_payment')">
                                    <div class="card-body">
                                        <input type="radio" name="payment_gateway" id="ssl_commerz_payment" {{session('payment_method')=='ssl_commerz_payment'?'checked':''}}>
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
                                        <input type="radio" name="payment_gateway" id="paypal" {{session('payment_method')=='paypal'?'checked':''}}>
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
                                        <input type="radio" name="payment_gateway" id="stripe" {{session('payment_method')=='stripe'?'checked':''}}>
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
                                        <input type="radio" name="payment_gateway" id="razor_pay" {{session('payment_method')=='razor_pay'?'checked':''}}>
                                        <span class="checkmark" style="margin-right: 10px"></span>
                                        <span>{{trans('messages.razor_pay')}}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            @php($config=\App\CPU\Helpers::get_business_settings('paytm'))
                            @if($config['status'])
                                <div class="card" onclick="setPaymentMethod('paytm')">
                                    <div class="card-body">
                                        <input type="radio" name="payment_gateway" id="paytm" {{session('paytm')=='ssl_commerz_payment'?'checked':''}}>
                                        <span class="checkmark" style="margin-right: 10px"></span>
                                        <span>{{trans('messages.paytm_online_payment')}}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Navigation (desktop)-->
                    <div class="row">
                        <div class="col-6">
                            <a class="btn btn-secondary btn-block" href="{{route('checkout-shipping')}}">
                                <i class="czi-arrow-left mt-sm-0 mr-1"></i>
                                <span class="d-none d-sm-inline">Back to Shipping</span>
                                <span class="d-inline d-sm-none">Back</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a class="btn btn-primary btn-block"
                               href="{{route('checkout-review')}}">
                                <span class="d-none d-sm-inline">Review your order</span>
                                <span class="d-inline d-sm-none">Review order</span>
                                <i class="czi-arrow-right mt-sm-0 ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Sidebar-->
            @include('web-views.partials._order-summary')
        </div>
    </div>
@endsection

@push('script')
    <script>
        function setPaymentMethod(name) {
            $.get({
                url: '{{ url('/') }}/customer/set-payment-method/' + name,
                success: function () {
                    $('#' + name).prop('checked', true);
                    toastr.success(name.replace(/_/g, " ") + ' has been selected successfully');
                }
            });
        }
    </script>
@endpush
