@extends('_layouts.default_wx')
@section('content')
    <div id="listBox" class="content">

        @if(count($orderList)<1)
            <style>
                body {
                    background: #fff !important;
                }
            </style>
            <div class="emptyBox">
                <img src="{{asset('img/wxkhd/listBox.png')}}"/>

                <p>您当前没有申请订单！</p>

                <div class="applyNow nextBtn">
                    <a href="{{url('wx/loan/mcode')}}">马上申请</a>
                </div>
            </div>
            <style>
                body {
                    background: #fff;
                }
            </style>
        @else
            <ul class="box">
                @foreach($orderList as $order)
                <li class="list">
                    <a href="{{url('wx/order/detail').'?order_id='.$order->oid}}">
                    <div class="type">
                        <div class="typeName">{{$order->service_type}}</div>
                        <div class="audit status1">{{\Illuminate\Support\Arr::get($statusList,$order->status,'未知状态')}}</div>
                    </div>
                    <div class="infro">
                        <div class="left">
                            贷款金额：&nbsp<span>&yen;<i class="loanAmount"> {{$order->loan_money}}</i></span>
                        </div>
                        <div class="right">
                            月供：&nbsp<span>&yen;<i class="monthMoney">{{$order->monthly_repay_money}}</i>*<i class="times">{{$order->periods}}期</i></span>
                        </div>
                    </div>
                    </a>
                </li>
                @endforeach
            </ul>
        @endif
    </div>
    <script src="{{asset("js/jweixin-1.0.0.js")}}"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('css/wx/qzdClient.css')}}"/>

    <script type="text/javascript">
        $(function(){
            var preUrl = document.referrer;

            //如果上级来源是电商页面，则不让返回电商页面
            if(preUrl.indexOf('wx/loan/ecommerce') != -1){
                pushHistory();
                window.addEventListener("popstate", function(e) {
                    window.location.href = '/wx/loan/mcode';
                }, false);
                function pushHistory() {
                    var state = {title: "title",  url: "#" };
                    window.history.pushState(state, "title", "#");
                }
            }
        })
    </script>
@endsection