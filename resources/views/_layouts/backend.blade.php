<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta content="telephone=no" name="format-detection">
        <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" name="viewport">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <title>仟姿贷后台管理系统</title>
        <link href='/backend/css/bootstrap/bootstrap.css' media='all' rel='stylesheet' type='text/css' />
        <link href='/backend/css/light-theme.css' id='color-settings-body-color' media='all' rel='stylesheet' type='text/css' />
        <link href='/backend/css/page.css' id='color-settings-body-color' media='all' rel='stylesheet' type='text/css' />
        <style>
            body{font-size:14px;}
            .logo_btn{margin-left: 50%;border:1px solid black;text-align: center;height:30px;line-height:30px;background-color:#969696;color: #fff;cursor: pointer;border-radius: 3px;width:80px;}
            .logo_btn a{text-decoration:none;color:black}
            .content{margin:1% auto;width: 98%;background-color: #fff;border-radius: 5px;}
            .content>a{color: #1d79c7;padding: 0 20px;}
            .header-title{padding: 15px;border-bottom:1px solid #eaeaea;font-size: 18px;}
            .tile-template{padding:20px;}
            .tile-template .table{margin-top: 0;}
            .tile-template .table th {padding: 4px 0px;text-align: center;line-height: 20px; font-size: 14px}
            .tile-template .table td {padding: 8px;line-height: 20px;text-align: center;font-size: 14px}
            .tile-template .table .txt_left{text-align: left;}
            
            .s_form>table{width:100%;}
            .s_form>table td{white-space: nowrap;}
            .s_form>table td.col-title{width:70px;text-align: right;}
            .s_form>table td>span,.s_form>table td>input,.s_form>table td>select{margin: 0;}
            .s_form>table td>span{}
            .s_form>table td>select{box-sizing: border-box;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;-ms-box-sizing: border-box;height: 24px;line-height: 24px;padding: 0 2px;min-width: 124px;max-width: 140px;}
            .s_form>table td>input{height: 24px;box-sizing: border-box;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;-ms-box-sizing: border-box;width: 80%;}
            .s_form>table td>input.Wdate{width:100%;height: 24px;}
            .s_form>table td>input.s_btn{background-color: #1d8bd8;width: 60px;height: 25px;font-size: 14px;color: #FFFFFF;border: none;border-radius: 3px;line-height: 25px;margin: 0 0 0 20px;}
            .s_btn{background-color: #1d8bd8;width: 60px;height: 25px;font-size: 14px;color: #FFFFFF;border: none;border-radius: 3px;line-height: 25px;}
            .s_expose{margin-top: 30px;text-align: left;margin-bottom: 10px}
            .s_expose>a{background-color: #1d8bd8;font-size: 14px;text-align: center;color: #FFFFFF;border: none;border-radius: 3px;padding: 5px 10px;margin:0 14px 0 0;text-decoration: none;}
            .s_expose>a:hover{background-color: #0aaefd;}
            .s_expose>a:active{background-color: #0aaefd;}
            .logo_btn{margin-left: 50%;border:1px solid #9A9A9A;text-align: center;height:30px;line-height:30px;background-color:#E7E7E7;color: #fff;cursor: pointer;border-radius: 3px;width:80px;}
            .logo_btn a{text-decoration:none;color:black}
            .page-header .pull-left{margin-left:20px;}
        </style>
        @section('style')
        @show
    </head>
    <body>
        <div class="content">
            @yield('content')
        </div>
        <script type="text/javascript" src="/backend/js/jquery.min.js"></script>
        <script type="text/javascript" src="/plugin/layer_pc/layer.js"></script>
        <script type="text/javascript" src="/plugin/My97DatePicker/WdatePicker.js"></script>
        <script type="text/javascript" src="/backend/js/jquery.cookie.js"></script>
        <script type="text/javascript" src="/backend/js/public.js"></script>
        <script src="{{url('js/jquery-1.9.1.min.js')}}"></script>
        <script>
            $.ajaxSetup({
                         headers: {
                             'X-XSRF-TOKEN': $.cookie('XSRF-TOKEN')
                        }
            });
        </script>
        @section('script')
        @show
    </body>
</html>
