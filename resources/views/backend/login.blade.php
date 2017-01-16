<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="renderer" content="webkit|ie-comp|ie-stand">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <title>后台登录 - 仟姿贷</title>
        <style>
            *{margin: 0;padding: 0;}
            body{color:#333; background-color:#1d8bd8; font-family:'microsoft yahei','Arial', 'Helvetica', 'sans-serif';}
            .article{width: 100%;height: 100%;position: absolute;left: 0;top: 0;background:url(/backend//images/bg.png) no-repeat center center;}
            .article .footer{position: absolute;bottom:0;left: 0;width:100%;height:60px;text-align:center;background-color: #1d8bd8;color: #fff;font-size: 12px;}
            .article .footer p:first-child{margin: 12px auto 8px;}

            .article .form{position:absolute;width: 450px;height:360px;top: 50%;left: 50%;margin: -230px 0 0 -225px;}
            .article .form .f_header{font-size: 36px;color:#e3f2ff;}
            .article .form .f_header>img{display: inline-block;vertical-align: middle;}
            .article .form .f_header>span{display: inline-block;vertical-align: middle;}
            .article .form .f_cont{background-color:#e8f4ff;border-radius: 3px;border: 1px solid #fff;border-top-width: 2px;padding: 34px 0 42px 0;margin: 28px 0 0;box-shadow: 0 0 5px #888;-webkit-box-shadow: 0 0 5px #888;-moz-box-shadow: 0 0 5px #888;-ms-box-shadow: 0 0 5px #888;-o-box-shadow: 0 0 5px #888;}
            .article .form .f_cont>div{margin: 0 auto;}
            .article .form .f_cont .f_col{border: 1px solid #77aadd;border-radius: 3px;padding:10px;width: 290px;margin:0 auto 18px;background-color: #fff;}
            .article .form .f_cont .f_col>input,.article .form .f_cont .f_col>img{display: inline-block;vertical-align: middle;}
            .article .form .f_cont .f_col>input{border: none;height: 24px;padding: 0 0 0 8px;color: #999899;margin: 0 0 0 4px;width: 240px;}
            .article .form .f_cont .f_pw>img{margin: 0 3px;}
            .article .form .f_cont .tips{width: 310px;margin: 10px auto;font-size: 14px;color: #ff6600;}
            .article .form .f_cont .tips_show{display: block;}
            .article .form .f_cont .tips>img,.article .form .f_cont .tips>span{display: inline-block;vertical-align: middle;margin: 0 6px 0 0;}
            .article .form .f_cont .logo_btn{text-align: center;height: 45px;line-height: 45px;background-color:#18a4f5;color: #fff;cursor: pointer;border-radius: 3px;width: 310px;}
            .article .form .verifyimg_wrap{border: 1px solid #77aadd;border-radius: 3px;padding: 0 0 0 10px;width: 300px;margin:0 auto 18px;background-color: #fff;    overflow: auto;}
            .article .form .verifyimg_wrap>input{width: 140px;transition: all 0.3s ease 0s;border: none;height: 24px;padding:0 0 0 8px;color: #999899;margin: 11px 0 0 4px;display: inline-block;vertical-align: middle;}
            .article .form .verifyimg_wrap>img.verifyimg{border-left: 1px solid #ddd;float: right;height: 45px;margin-left: 10px;width: 100px;}
            .article .form .verifyimg_wrap .validate_icon{width: 23px;height: 23px;display: inline-block;vertical-align: middle;margin: 8px 0 0 2px;}
        </style>
    </head>
    <body>
	<div class="article">
        <div class="form">
            <form class="form form-horizontal" action="" method="post">
                <div class="f_header"><img src="/backend//images/logo_1.png" alt="" ><span>仟姿贷后台管理系统</span></div>
                <div class="f_cont">
                    <div class="f_col">
                        <img src="/backend//images/user.png" alt="">
                        <input type="text" placeholder="账户名" id="username" name="username">
                    </div>
                    <div class="f_col f_pw">
                        <img src="/backend//images/pw.png" alt="">
                        <input id="password" name="password" type="password" placeholder="密码" >
                    </div>
                    <div class="verifyimg_wrap">
                        <img class="validate_icon" src="/backend//images/validate.png" alt="">
                        <input class="input-text size-L" name="code" type="text"  placeholder="验证码"  >
                        <label class="form-label col-3"><i class="Hui-iconfont"></i></label>
                        <img class="verifyimg reloadverify" alt="点击切换" src="/backend/login/verify?rand={{rand(1000,100000)}}" style="cursor:pointer;">
                    </div>
                    <div class="row tips">
                        <!--<img src="/backend//images/warning.png" alt="">--><span>{{ Session::get('msg') }}</span>
                    </div>
                    <div class="logo_btn" style="margin-top:15px;">
                        <input name="" type="submit" value=" 登录 " style="width:100%;height:100%;font-size:20px;color:white;border-radius:10px;background:#18A4F5;border:1px solid #18A4F5;">
                    </div>
                </div>
                {!! csrf_field() !!}
            </form>
        </div>
        <div class="footer">
            <p>Copyright©{{date("Y")}} www.qianzidai.cn. All Rights Reserved. </p>
            <p>深圳市佰仟金融服务有限公司</p>
        </div>
    </div>
	<script type="text/javascript" src="/js/jquery-3.0.0.js"></script>
        <script type="text/javascript" src="/js/jquery.min.js"></script> 
        <script>
        $(function () {
            $(".reloadverify,.reloadverify_a").click(function () {
                var verifyimg = $(".verifyimg").attr("src");
                if (verifyimg.indexOf('?') > 0) {
                    $(".verifyimg").attr("src", verifyimg + '&random=' + Math.random());
                } else {
                    $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/, '') + '?' + Math.random());
                }
            });
        });
        </script>
    </body>
</html>
