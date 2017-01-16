<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>合作加盟</title>
    <link rel="stylesheet" type="text/css" href="{{asset('css/csspc/comomHead&Footer.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/csspc/alliance.css')}}"/>

    <script src="{{asset('js/jquery-3.1.0.min.js')}}" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<!--头部-->
<div id="header">
    <div class="header">
        <img src="{{asset('img/pcImg/logo.png')}}"/>
        <ul>
            <li><a href="{{url('/')}}"><span>首页</span></a></li>
            <li><a href="{{url('pc/pro')}}"><span>产品介绍</span></a></li>
            <li><a href="{{url('pc/alliance')}}"><span>合作加盟</span></a></li>
            <li><a href="{{url('pc/company')}}" class="lastA"><span>公司介绍</span></a></li>
        </ul>
    </div>
</div>
<!--广告牌-->
<div id="banner">
    <!--<img src="img/qzdhzjmBj.png"/>-->
    <div class="banner">
        <div class="loginPage">
            <div class="loginTitle">
                <h5>开放申请</h5>
                <div class="delbtn">
                    &otimes;
                </div>
            </div>
            <div class="comName column">
                <span>公司名称</span>
                <input type="text" name="comName" id="comName" placeholder="请输入公司名称" />
            </div>
            <div class="cityName column">
                <span>公司城市</span>
                <input type="text" name="cityName" id="cityName" placeholder="请输入公司所在城市" />
            </div>
            <div class="linkMen column">
                <span>联系人</span>
                <input type="text" name="linkMen" id="linkMen" placeholder="请输入联系人姓名" />
            </div>
            <div class="linkPhone column">
                <span>联系电话</span>
                <input type="text" name="linkPhone" id="linkPhone" placeholder="请输入联系人电话" />
            </div>
            <div class="linkEmail column">
                <span>邮箱</span>
                <input type="text" name="linkEmail" id="linkEmail" placeholder="请输入邮箱" />
            </div>
            <div class="loginBtn">
                提交
            </div>
        </div>
    </div>

</div>
<!--链接-->
<div id="link">
    <div class="link">
        <div class="contact">
            <div class="contactUs">
                <p>联系我们：400-998-7101 <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>9:00-22:00（节假日休息）</p>
            </div>
            <div class="txt1">
                <p>邮箱:opsaf.cs@bqjr.cn</p>
            </div>
            <div class="txt2">
                <p>黔ICP备15016177号-1</p>
            </div>
        </div>
        <div class="txt3">
            <p class="fristP">扫描关注:仟姿贷用户版</p>
            <p>商户版，获取更多服务、行业资讯</p>
        </div>
        <div class="code1">
            <img src="{{asset('img/pcImg/code1.jpg')}}" alt="" />
        </div>
        <div class="code2">
            <img src="{{asset('img/pcImg/code2.jpg')}}" alt="" />
        </div>
    </div>
</div>
<script type="text/javascript">
    $(".delbtn").click(function(){
        $(".loginPage").hide();
    })
</script>
</body>
</html>
