<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>仟姿贷</title>
    <script src="{{asset('js/jquery-3.1.0.min.js')}}" type="text/javascript" charset="utf-8"></script>
    <style>
        .basic-grey {
            margin-left:auto;
            margin-right:auto;
            max-width: 520px;
            background: #F7F7F7;
            padding: 25px 15px 25px 10px;
            font: 12px Georgia, "Times New Roman", Times, serif;
            color: #888;
            text-shadow: 1px 1px 1px #FFF;
            border:1px solid #E4E4E4;
        }
        .basic-grey h1 {
            font-size: 25px;
            padding: 0px 0px 10px 40px;
            display: block;
            border-bottom:1px solid #E4E4E4;
            margin: -10px -15px 30px -10px;;
            color: #888;
        }
        .basic-grey h1>span{
            display: block;
            font-size: 11px;
            color: #f4645f;
            margin-top: 10px;
        }
        .basic-grey label {
            display: block;
            margin: 0px;
        }
        .basic-grey label>span {
            float: left;
            width: 20%;
            text-align: right;
            padding-right: 10px;
            margin-top: 10px;
            color: #888;
        }
        .basic-grey input[type="text"], .basic-grey input[type="email"], .basic-grey textarea, .basic-grey select {
            border: 1px solid #DADADA;
            color: #888;
            height: 30px;
            margin-bottom: 16px;
            margin-right: 6px;
            margin-top: 2px;
            outline: 0 none;
            padding: 3px 3px 3px 5px;
            width: 70%;
            font-size: 12px;
            line-height:15px;
            box-shadow: inset 0px 1px 4px #ECECEC;
            -moz-box-shadow: inset 0px 1px 4px #ECECEC;
            -webkit-box-shadow: inset 0px 1px 4px #ECECEC;
        }

        .basic-grey .btn {
            background: #E27575;
            border: none;
            padding: 10px 25px 10px 25px;
            color: #FFF;
            box-shadow: 1px 1px 5px #B6B6B6;
            border-radius: 3px;
            text-shadow: 1px 1px 1px #9E3F3F;
            cursor: pointer;
        }
        .basic-grey .btn:hover {
            background: #CF7A7A
        }


    </style>

</head>
<body>
    <form action="re-ca-tpl" method="post" class="basic-grey">
        <h1>重新推送ca签署模板消息<span>多个合同请用英文',' 隔开(只会对状态为审核通过(080)的重新推送签署模板)</span></h1>
        <label>
            <span>订单号 :</span>
            <input id="constract_no" type="text" name="constract_no" placeholder="请输入订单号" />
        </label>

        <label>
            <span>&nbsp;</span>
            <input type="button" class="btn" value="确定" />
        </label>
    </form>
</body>
</html>

<script>

    $('.btn').click(function(){

        var constract_no = $('#constract_no').val();

        if(checkNumber(constract_no)){
            $.ajax({
                type: "POST",
                url: "/wx/conlist/re-ca-tpl",
                data: "'_token':'{{csrf_token()}}'&constract_no="+constract_no,
                dataType:"json",
                success: function(data){
                    //alert(data.length);
                    for(var i=0;i<data.length;i++){
                        alert(data[i].msg);
                    }
                }
            });

        }else{
            alert('请输入正确的合同号！');
        }
    })

    function checkNumber(theObj) {
        var reg = /^[0-9]+(,[0-9]+)*$/;
        if (reg.test(theObj)) {
            return true;
        }
        return false;
    }

</script>
