@extends('_layouts.default_wx')

@section('content')
<div>
    <div id="photoInfro" class="content">
        <div class="titleCom installment">
            <p>
                <span class="logoPic iconPic"></span>
                <span class="title">影像资料</span>
            </p>
        </div>
        <form method="POST" action="/wx/loan/do-filepic" id="form_warp">
            <input name="OrderId" type="hidden" value=""/>
            <div class="photos">
                <ul>
                    <li>
                        <dl>
                            <dt id="up_pic0">
                                @if(!empty($pic->cert_face_pic) && (strpos($pic->cert_face_pic,'wechat')>0))
                                    <img src="{{\App\Util\FileReader::read_storage_image_resize_file($pic->cert_face_pic)}}"/>
                                @else
                                    <img src="{{asset('img/wxSecond/filepic.png')}}"/>
                                @endif

                                @if(!empty($pic->cert_face_pic) && (strpos($pic->cert_face_pic,'wechat')>0))
                                    <input name="cert_face_pic" type="hidden" value="{{$pic->cert_face_pic}}"/>
                                @else
                                    <input name="cert_face_pic" type="hidden" value=""/>
                                @endif

                            </dt>
                            <dd><i class="star">*</i>身份证正面</dd>
                        </dl>
                    </li>
                    <li>
                        <dl>
                            <dt id="up_pic1">
                                @if(!empty($pic->cert_opposite_pic) && (strpos($pic->cert_opposite_pic,'wechat')>0))
                                    <img src="{{\App\Util\FileReader::read_storage_image_resize_file($pic->cert_opposite_pic)}}"/>
                                @else
                                    <img src="{{asset('img/wxSecond/filepic.png')}}"/>
                                @endif

                                @if(!empty($pic->cert_opposite_pic) && (strpos($pic->cert_opposite_pic,'wechat')>0))
                                    <input name="cert_opposite_pic" type="hidden" value="{{$pic->cert_opposite_pic}}"/>
                                @else
                                    <input name="cert_opposite_pic" type="hidden" value=""/>
                                @endif
                            </dt>
                            <dd><i class="star">*</i>身份证反面</dd>
                        </dl>
                    </li>
                    <li>
                        <dl>
                            <dt id="up_pic2">
                                @if(!empty($pic->cert_hand_pic) && (strpos($pic->cert_hand_pic,'wechat')>0))
                                    <img src="{{\App\Util\FileReader::read_storage_image_resize_file($pic->cert_hand_pic)}}"/>
                                @else
                                    <img src="{{asset('img/wxSecond/filepic.png')}}"/>
                                @endif

                                @if(!empty($pic->cert_hand_pic) && (strpos($pic->cert_hand_pic,'wechat')>0))
                                    <input name="cert_hand_pic" type="hidden" value="{{$pic->cert_hand_pic}}"/>
                                @else
                                    <input name="cert_hand_pic" type="hidden" value=""/>
                                @endif
                            </dt>
                            <dd><i class="star">*</i>手持身份证</dd>
                        </dl>
                    </li>
                    <li>
                        <dl>
                            <dt id="up_pic3">
                                @if(!empty($pic->credit_auth_pic) && (strpos($pic->credit_auth_pic,'wechat')>0))
                                    <img src="{{\App\Util\FileReader::read_storage_image_resize_file($pic->credit_auth_pic)}}"/>
                                @else
                                    <img src="{{asset('img/wxSecond/filepic.png')}}"/>
                                @endif

                                @if(!empty($pic->credit_auth_pic) && (strpos($pic->credit_auth_pic,'wechat')>0))
                                    <input name="credit_auth_pic" type="hidden" value="{{$pic->credit_auth_pic}}"/>
                                @else
                                    <input name="credit_auth_pic" type="hidden" value=""/>
                                @endif
                            </dt>
                            <dd><i class="star">*</i>征信授权书</dd>
                        </dl>
                    </li>
                    <li>
                        <dl>
                            <dt id="up_pic4">
                                @if(!empty($pic->work_pic))
                                    <img src="{{\App\Util\FileReader::read_storage_image_resize_file($pic->work_pic)}}"/>
                                @else
                                    <img src="{{asset('img/wxSecond/filepic.png')}}"/>
                                @endif

                                @if(!empty($pic->work_pic))
                                    <input name="work_pic" type="hidden" value="{{$pic->work_pic}}"/>
                                @else
                                    <input name="work_pic" type="hidden" value=""/>
                                @endif
                            </dt>
                            <dd>名片/工牌</dd>
                        </dl>
                    </li>
                </ul>

            </div>
            <input type="button" id="next_pic" value="下一步" class="nextBtn filepic_btn" style="background: rgb(235,33,107)"/>
        </form>
        <div class="alterApplySuccess">
            <div class="applySuccess"></div>
        </div>
    </div>

    <script src="{{asset('js/jweixin-1.0.0.js')}}"></script>
    <script>
        wx.config({
            debug: false,
            appId: '{{{$signPackage["appId"]}}}',
            timestamp: '{{{$signPackage["timestamp"]}}}',
            nonceStr: '{{{$signPackage["nonceStr"]}}}',
            signature: '{{{$signPackage["signature"]}}}',
            jsApiList: [
                'checkJsApi',
                'chooseImage',
                'previewImage',
                'uploadImage',
                'getLocation',
                'getNetworkType']
        });
        wx.error(function(res){
            layer.open({
                skin:"oAlterWindow",
                title:'小提示',
                offset:['120px',''],
                shade: [0.8, '#000'],
                shadeClose: true,
                content:"<p class='tips_1'></p><p class='dia_span tips_2'>咦，微信掉链子了 O(∩_∩)O~~</p>",
                closeBtn:0,
                btn:['知道了','']
            })
            window.location.href="/wx/loan/file-pic?wxerror="+res.errMsg;
        });
        var images = {
            localId: [],
            serverId: []
        };

        $(function (){

            $('#up_pic0').click(function (e){
                e.stopPropagation();
                up_img('#up_pic0');
            })


            $('#up_pic1').click(function (e){
                e.stopPropagation();
                up_img('#up_pic1');
            })


            $('#up_pic2').click(function (e){
                e.stopPropagation();
                up_img('#up_pic2');
            })

            $('#up_pic3').click(function (e){
                e.stopPropagation();
                up_img('#up_pic3');
            })

            $('#up_pic4').click(function (e){
                e.stopPropagation();
                up_work_img('#up_pic4');
            })

        })

        function up_img(e){
            var target = $(e);
            wx.chooseImage({
                sizeType: ['compressed'],
                // 指定来源是拍照
                //sourceType: ['camera'],
                success: function (res) {
                    if(res.localIds.length > 1){
                        layer.open({
                            skin:"oAlterWindow",
                            title:'小提示',
                            offset:['120px',''],
                            shade: [0.8, '#000'],
                            shadeClose: true,
                            content:"<p class='tips_1'></p><p class='dia_span tips_2'>上传失败，每次请选择1张上传</p>",
                            closeBtn:0,
                            btn:['知道了','']
                        })
                    }else{
                        images.localId = res.localIds;
                        wx.uploadImage({
                            localId: images.localId[0],
                            success: function(res){
                                layer.open({
                                    skin:"loadWindow",
                                    title:0,
                                    shade: [0.8, '#000'],
                                    shadeClose: true,
                                    offset:['235px',''],
                                    content:"<p class='tips_1'><img src='{{asset('img/wx/loading.png')}}'/></p><p class='dia_span tips_2'>加载中...</p>",
                                    closeBtn:0,
                                    btn:0,
                                    time:1000
                                })
                                target.find("input").eq(0).val(Date.parse(new Date())/1000);
                                $.post('/wx/wechat/downwxpic',{'media_id':res.serverId},function(data){
                                    if(data.status){
                                        target.find("input").eq(0).val(data.path);
                                        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                                        target.find('img').attr('src',images.localId[0]);
                                    }else{
                                        layer.open({
                                            skin:"loadWindow",
                                            title:0,
                                            shade: [0.8, '#000'],
                                            shadeClose: true,
                                            offset:['235px',''],
                                            content:"<p class='tips_1'></p><p class='dia_span tips_2'></p>",
                                            closeBtn:0,
                                            btn:0,
                                            time:1000
                                        })
                                        $(".tips_2").html(data.msg);
                                    }
                                },'json');
                            }
                        });
                    }
                }
            });
        }

        function up_work_img(e){
            var target = $(e);
            wx.chooseImage({
                sizeType: ['compressed'],
                success: function (res) {
                    if(res.localIds.length > 1){
                        layer.open({
                            skin:"oAlterWindow",
                            title:'小提示',
                            offset:['120px',''],
                            shade: [0.8, '#000'],
                            shadeClose: true,
                            content:"<p class='tips_1'></p><p class='dia_span tips_2'>上传失败，每次请选择1张上传</p>",
                            closeBtn:0,
                            btn:['知道了','']
                        })
                    }else{
                        images.localId = res.localIds;
                        wx.uploadImage({
                            localId: images.localId[0],
                            success: function(res){
                                layer.open({
                                    skin:"loadWindow",
                                    title:0,
                                    shade: [0.8, '#000'],
                                    shadeClose: true,
                                    offset:['235px',''],
                                    content:"<p class='tips_1'><img src='{{asset('img/wx/loading.png')}}'/></p><p class='dia_span tips_2'>加载中...</p>",
                                    closeBtn:0,
                                    btn:0,
                                    time:1000
                                })
                                target.find("input").eq(0).val(Date.parse(new Date())/1000);
                                $.post('/wx/wechat/downwxpic',{'media_id':res.serverId},function(data){
                                    if(data.status){
                                        target.find("input").eq(0).val(data.path);
                                        //console.log(data.path);
                                        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                                        target.find('img').attr('src',images.localId[0]);
                                    }else{
                                        layer.open({
                                            skin:"loadWindow",
                                            title:0,
                                            shade: [0.8, '#000'],
                                            shadeClose: true,
                                            offset:['235px',''],
                                            content:"<p class='tips_1'></p><p class='dia_span tips_2'></p>",
                                            closeBtn:0,
                                            btn:0,
                                            time:1000
                                        })
                                        $(".tips_2").html(data.msg);
                                    }
                                },'json');
                            }
                        });
                    }
                }
            });
        }

        $("#next_pic").click(function() {
            var cert_face_pic = $("input[name=cert_face_pic]").val();
            var cert_opposite_pic = $("input[name=cert_opposite_pic]").val();
            var cert_hand_pic = $("input[name=cert_hand_pic]").val();
            var credit_auth_pic = $("input[name=credit_auth_pic]").val();

            if (!cert_face_pic || !cert_opposite_pic || !cert_hand_pic || !credit_auth_pic ) {
                layer.open({
                    skin:"oAlterWindow",
                    title:'小提示',
                    offset:['120px',''],
                    shade: [0.8, '#000'],
                    shadeClose: true,
                    content:"<p class='tips_1'></p><p class='dia_span tips_2'>请上传完图片再提交</p>",
                    closeBtn:0,
                    btn:['知道了','']
                })
                return false;
            }
            var that = $(this);
            that.attr('disabled','disabled');
            that.css("background","#ccc");
            layer.open({
                skin:"loadWindow",
                title:0,
                shade: [0.8, '#000'],
                shadeClose: true,
                offset:['235px',''],
                content:"<p class='tips_1'><img src='{{asset('img/wx/loading.png')}}'/></p><p class='dia_span tips_2'>正在提交...</p>",
                closeBtn:0,
                btn:0,
                time:1500
            })

            $.post($("#form_warp").attr('action'),$("#form_warp").serialize(),function(data){
                //console.log(data)
                if(data.code == '10000'){
                    window.location.href='/wx/loan/phone-pwd';
                }else{
                    layer.open({
                        skin:"oAlterWindow",
                        title:'小提示',
                        offset:['120px',''],
                        shade: [0.8, '#000'],
                        shadeClose: true,
                        content:"<p class='tips_1'></p><p class='dia_span tips_2'></p>",
                        closeBtn:0,
                        btn:['知道了','']
                    })
                    $(".tips_2").html(data.msg);
                    that.removeAttr('disabled');
                    that.css("background","rgb(235,33,107)");
                }
            },'json');
            return false;

        })

        $(function(){
            pushHistory();
            window.addEventListener("popstate", function(e) {
                window.location.href = '/wx/loan/family';
            }, false);
            function pushHistory() {
                var state = {title: "title",  url: "#" };
                window.history.pushState(state, "title", "#");
            }
        })
    </script>
</div>
@endsection

