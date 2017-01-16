<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>产品介绍</title>
    <link rel="stylesheet" type="text/css" href="{{asset('css/csspc/comomHead&Footer.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/csspc/proIntro.css')}}"/>
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
<div id="banner"></div>
<!--商品简介-->
<div id="proIntro">
    <div class="proIntro">
        <h3>产品简介</h3>
        <ul>
            <li class="list1">
                <img src="{{asset('img/pcImg/proIntro1.png')}}"/>
                <p>申请便捷,审核</p>
                <p>快捷的消费分期服务</p>
            </li>
            <li class="list2">
                <img src="{{asset('img/pcImg/proIntro2.png')}}"/>
                <p>客户自主选择首付比例、</p>
                <p>还款期数，还款灵活多变。</p>
            </li>
            <li class="list3">
                <img src="{{asset('img/pcImg/proIntro3.png')}}"/>
                <p>无需商品抵押担保，</p>
                <p>无需用卡作为证明材料。</p>
            </li>
            <li class="list4">
                <img src="{{asset('img/pcImg/proIntro4.png')}}" class="lastImg"/>
                <p class="lastP">只需简单资料，申请分期</p>
                <p>付款后按时还款，培养良好信用</p>
                <p>记录。覆盖多数消费记录。</p>
            </li>
        </ul>
    </div>
</div>
<!--服务流程-->
<div id="service">
    <div class="service">
        <h3>服务流程</h3>
        <ul>
            <li class="">
                <img src="{{asset('img/pcImg/service1.png')}}"/>
                <p>1.扫描商家二维码</p>
            </li>
            <li class="arrow">
                <img src="{{asset('img/pcImg/arrow.png')}}"/>
            </li>
            <li>
                <img src="{{asset('img/pcImg/service2.png')}}"/>
                <p>2.填写简单申请资料</p>
            </li>
            <li class="arrow">
                <img src="{{asset('img/pcImg/arrow.png')}}"/>
            </li>
            <li>
                <img src="{{asset('img/pcImg/service3.png')}}"/>
                <p>3.审核通过进行医美服务</p>
            </li>
        </ul>
    </div>
</div>
<!--办理条件-->
<div id="manage">
    <div class="manage">
        <h3>办理条件</h3>
        <div class="manage-left manage-condition">
            <div class="left apply">
                <img src="{{asset('img/pcImg/manage1.png')}}" alt="" />
                <p>申请资格</p>
            </div>
            <ul>
                <li class="frist">
                    <img src="{{asset('img/pcImg/manage12.png')}}"/>
                    <p>中国公民</p>
                </li>
                <li>
                    <img src="{{asset('img/pcImg/manage13.png')}}"/>
                    <p>18-55周岁</p>
                </li>
            </ul>
        </div>
        <div class="manage-right manage-condition">
            <div class="left file">
                <img src="{{asset('img/pcImg/manage2.png')}}" alt="" />
                <p>所需文件</p>
            </div>
            <ul>
                <li class="frist">
                    <img src="{{asset('img/pcImg/manage21.png')}}"/>
                    <p>二代身份证</p>
                </li>
                <li>
                    <img src="{{asset('img/pcImg/manage22.png')}}"/>
                    <p>简单身份信息</p>
                </li>
            </ul>
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
</body>
</html>
