@extends('_layouts.default1')
@section('title', '签署协议')
@section('content')
<div style="clear: both; height: 10px;"></div>
    @include('mobile.document.beauty_application')

    <label class="p-cont-padding" for="agreement_checkbox" >
        <input type="checkbox" id="agreement_checkbox" checked>已阅读，并同意以上条款
    </label>
    <div class="p-input-box p-margin-bottom-10" style="margin-top: 45px;">
        <a  onclick="comfirmloan()" href="javascript:void(0)" class="sumbit-a p-bg-color-primary p-height-40">确认申请借款</a>
    </div>

    <script type="text/javascript">
        function comfirmloan(){
            check_flag = $("#agreement_checkbox").is(":checked");
            if(check_flag == false){
                alert('请勾选已阅读条款');
            }else{
                location.href = '/document/ca-auth/';
            }
        }
    </script>
@endsection