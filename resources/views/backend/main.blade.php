<!DOCTYPE html>
<html>
<head>
<title>仟姿贷后台管理系统</title>
<meta
	content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'
	name='viewport' />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href='/backend/css/bootstrap/bootstrap.css' media='all'
	rel='stylesheet' type='text/css' />
<link href='/backend/css/bootstrap/bootstrap-responsive.css' media='all'
	rel='stylesheet' type='text/css' />
<link href='/backend/css/light-theme.css' id='color-settings-body-color'
	media='all' rel='stylesheet' type='text/css' />
<style>
	#change_pwd_form >table td {
		padding: 8px 0;
		vertical-align: middle;
		text-align: right;
		width: 138px;
		padding-right: 6px;
	}
	#change_pwd_form >table td>input[type="password"] {
		width: 280px;
		height: 30px;
		border-radius: 2px;
		border: 1px solid #b9dcee;
		margin: 0;
	}
	#chErrorMessages {
		height: 20px;
		text-align: center;
		color: #ff3c00;
	}
	#change_pwd_form >table td>input[type="button"]:first-child {
		background-color: #4db9ff;
		color: #fff;
	}
	#change_pwd_form >table td>input[type="button"] {
		width: 80px;
		height: 40px;
		border-radius: 2px !important;
		margin-right: 24px;
		border: none;
	}
.navbar {
	background-color: #f7f7f7;
}

.navbar .brand {
	margin-left: 0;
	padding: 15px 35px;
}

.navbar .brand>img {
	width: 150px;
	height: 50px;
}

.navbar .container-fluid {
	padding-left: 0;
}

.navbar .nav.pull-right {
	margin: 20px 0;
}

.navbar .nav>li>a {
	color: #808080;
}
/*.navbar .nav.pull-right a>span{color: #808080;}
            .navbar .nav.pull-right a:hover>span.u-ope{color: #808080;}
            .navbar .nav.pull-right a>span.u-ope{color: #202020;}*/
#main-nav .navigation>.nav>li  a.active {
	color: #1d8bd8;
}

#main-nav .navigation>.nav>li  a.active>span, #main-nav .navigation>.nav>li  a.active>i
	{
	color: #1d8bd8;
}

#wrapper {
	bottom: 0;
	left: 0;
	overflow: hidden;
	position: absolute;
	top: 81px;
	width: 100%;
	min-height: auto;
}

#content {
	bottom: 0;
	left: 181px;
	position: absolute;
	right: 0;
	top: 0;
	margin: 0;
	padding: 0;
	background-color: #eaeaea;
}

#main-nav-bg {
	z-index: 0;
}

#wrapper .container-fluid {
	position: absolute;
	bottom: 0;
	left: 0;
	width: 100%;
	top: 0;
	padding: 0;
	overflow: auto;
}

#wrapper .container-fluid iframe {
	border: medium none;
	width: 100%;
	min-height: 100%;
}

.footer {
	background-color: #f9f9f9;
	width: 100%;
	color: #808080;
}

.footer>div {
	width: 968px;
	margin: 0 auto;
	padding: 15px 0;
}

.footer>div>div {
	display: inline-block;
	vertical-align: middle;
}

.footer>div>div:first-child {
	text-align: center;
}

.footer>div>div:last-child {
	padding: 6px 10px 0;
}

.footer>div>div:last-child img {
	margin: 0 10px;
}

.footer>div>div:last-child img, .footer>div>div:last-child span {
	display: inline-block;
	vertical-align: middle;
}

.footer .phone {
	color: #525252;
	font-size: 24px;
}
</style>
</head>
<body class='contrast-red '>
	<div class='navbar'>
		<div class='container-fluid'>
			<a class='brand' href='/backend/main/index'> <img
				src="/backend/images/logo.png" alt="">
			</a>
			<ul class='nav pull-right'>
				<li class=''><a href='javascript:;'> <span class="">欢迎您！@if(isset(json_decode(session('back_user'))->username))
								{{json_decode(session('back_user'))->username}}
								@else
								{{json_decode(session('back_user'))->USERNAME}}
							@endif
								</span>
				</a></li>
				<li class=''><a href='#'> <span class="u-ope" id="change_password">修改密码</span>
				</a></li>
				<li class='dark'><a href='/backend/login/logout'> <i
						class='icon-signout'></i><span class="u-ope">安全退出</span>
				</a></li>
			</ul>
		</div>
	</div>
	<div id='wrapper'>
		<div id='main-nav-bg'>
			<nav class='' id='main-nav'>
				<div class='navigation'>
					<div class='search'>
						<form accept-charset="UTF-8" action="search_results.html"
							method="get" />
						<div style="margin: 0; padding: 0; display: inline">
							<input name="utf8" type="hidden" value="&#x2713;" />
						</div>
						<div class='search-wrapper'>
							<input autocomplete="off" class="search-query" id="q" name="q"
								placeholder="Search..." type="text" value="" />
							<button class="btn btn-link icon-search" name="button"
								type="submit"></button>
						</div>
						</form>
					</div>
					{{--<ul class='nav nav-stacked'>
						<!--后台导航-->
						<li class=''><a class='dropdown-collapse in' href='#'> <i
										class='icon-list-alt'></i> <span>订单管理</span> <i
										class='icon-angle-down angle-down'></i>
							</a>
							<ul class='nav nav-stacked in'>
								<li class=''><a href='#' class="link_a active"
												data-src="/backend/order/submit-index"> <i class='icon-caret-right'></i>
										<span>已提交订单</span>
									</a></li>
								<li class=''>
									<a href='#' class="link_a" data-src="/backend/order/not-submit-index"> <i class='icon-caret-right'></i>
										<span>未提交订单</span>
									</a>
								</li>
								<li class=''><a href='#' class="link_a"
												data-src="/backend/order/order-export-index"> <i class='icon-caret-right'></i>
										<span>数据导出</span>
									</a></li>
							</ul></li>
						<li class=''><a class='dropdown-collapse' href='#'> <i
										class='icon-edit'></i> <span>查询管理</span> <i
										class='icon-angle-down angle-down'></i>
							</a>
							<ul class='nav nav-stacked'>
								<li class=''><a href='#' class="link_a"
												data-src="/backend/inquiry-management/merchant"> <i class='icon-caret-right'></i>
										<span>商户查询</span>
									</a></li>
							</ul></li>
						<li class=''><a class='dropdown-collapse' href='#'> <i
										class='icon-align-left'></i> <span>数据统计</span> <i
										class='icon-angle-down angle-down'></i>
							</a>
							<ul class='nav nav-stacked'>
								<li class=''><a href='#' class="link_a"
												data-src="/backend/statistics/market-performance"> <i class='icon-caret-right'></i>
										<span>销售业绩</span>
									</a></li>
								<li class=''><a href='#' class="link_a"
												data-src="/backend/statistics/risk-control"> <i class='icon-caret-right'></i>
										<span>风控数据</span>
									</a></li>
							</ul></li>
						<li class=''><a class='dropdown-collapse' href='#'> <i
										class='icon-gift'></i> <span>后台管理</span> <i
										class='icon-angle-down angle-down'></i>
							</a>
							<ul class='nav nav-stacked'>
								<li class=''>
									<a href='#' class="link_a" data-src="{{url('user')}}">
										<i class='icon-caret-right'></i>
										<span>添加管理员</span>
									</a>
								</li>
								<li class=''>
									<a href='#' class="link_a" data-src="{{url('user/list')}}">
										<i class='icon-caret-right'></i>
										<span>查看管理员</span>
									</a>
								</li>
								<li class=''>
									<a href='#' class="link_a" data-src="{{url('role')}}">
										<i class='icon-caret-right'></i>
										<span>添加角色</span>
									</a>
								</li>
								<li class=''>
									<a href='#' class="link_a" data-src="{{url('role/list')}}">
										<i class='icon-caret-right'></i>
										<span>角色列表</span>
									</a>
								</li>
								<li class=''>
									<a href='#' class="link_a" data-src="{{url('privilege')}}">
										<i class='icon-caret-right'></i>
										<span>添加权限</span>
									</a>
								</li>
								<li class=''>
									<a href='#' class="link_a" data-src="{{url('log')}}">
										<i class='icon-caret-right'></i>
										<span>操作日志</span>
									</a>
								</li>
								<li class=''>
									<a href='#' class="link_a" data-src="{{url('test')}}">
										<i class='icon-caret-right'></i>
										<span>登录接口test</span>
									</a>
								</li>
							</ul>
						</li>

					</ul>--}}
					{{--以下代码动态显示导航栏，莫删除--}}
					<ul class='nav nav-stacked'>
						<!--后台导航-->
						@foreach($data as $v)
						<li class=''><a class='dropdown-collapse' href='#'> <i
										class='icon-gift'></i> <span>{{$v->name}}</span> <i
										class='icon-angle-down angle-down'></i>
							</a>
							@foreach($v->son as $value)
							<ul class='nav nav-stacked'>
								<li class=''>
									<a href='#' class="link_a" data-src="{{url("$value->base_uri")}}">
										<i class='icon-caret-right'></i>
										<span>{{$value->name}}</span>
									</a>
								</li>
							</ul>
								@endforeach
						</li>
							@endforeach
					</ul>
					{{--以上动态生成左侧导航栏--}}

				</div>
			</nav>
		</div>
            <div id='content'>
                <div class='container-fluid'>
                    <!--div id="refresh" style="position: absolute;right: 20px;top: 30px;"><a href="javascript:void(0);"><i class="btn btn-success icon-refresh" title="刷新"></i></a></div-->
                    <iframe src="/backend/order/submit-index"  id="page-cont" scrolling="auto"></iframe>
                    <!--div class="footer">
                        <div>
                            <div>
                                <img src="/backend/images/qcode.png" alt="">
                                <p>扫一扫，关注我们</p>
                            </div>
                            <div>
                                <p><img src="/backend/images/phone.png" alt=""><span class="phone">400-161-1188</span></p>
                                <p><img src="/backend/images/position.png" alt=""><span>深圳市福田彩田路7018号新浩e都9楼</span></p>
                                <p><img src="/backend/images/mail.png" alt=""><span>service@ydl001.cn</span></p>
                                <p><span>Copyright©2016 www.ydl001.cn. All Rights Reserved. 深圳市员动力科技有限公司 粤ICP备16048328号</span></p>
                            </div>
                        </div>
                    </div-->
				</div>
		</div>

		<div id="change_pwd_box" style="display:none;">
			<form id="change_pwd_form">
				<div id="chErrorMessages"><p></p></div>
				<table>
					<tr>
						<td>旧密码<span class="star"></span>：</td>
						<td>
							<input id="old_password" value="" type="password" />
							<span id="oldError" class="formErrors"></span>
						</td>
					</tr>
					<tr>
						<td>新密码<span class="star"></span>：</td>
						<td>
							<input id="new_password" value="" type="password" />
							<span id="newError" class="formErrors"></span>
						</td>
					</tr>
					<tr>
						<td>确认密码<span class="star"></span>：</td>
						<td>
							<input id="confirm_password" value="" type="password" />
							<span id="confirmError" class="formErrors"></span>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input style="margin-left: 50px;float: left;" type="button" id="change_pwd_save" value="确定" />
							<input style="float: left;" type="button" id="change_pwd_cancel"value="取消" />
						</td>
					</tr>
					{!! csrf_field() !!}
				</table>
			</form>
		</div>
	</div>
	<script src='/backend/js/jquery/jquery.min.js' type='text/javascript'></script>
	<script src='/backend/js/bootstrap/bootstrap.min.js'
		type='text/javascript'></script>
	<script src='/backend/js/nav.js' type='text/javascript'></script>
	<script type="text/javascript" src="/plugin/layer_pc/layer.js"></script>
	<script>
            $('.link_a').on('click', function () {
                $('#page-cont').attr("src", $(this).attr("data-src"));
                $('.link_a').removeClass('active');
                $(this).addClass('active');
            });
            $('.navigation>.nav>li>a').on('click',function(){
                var $siblings = $(this).parent('li').siblings();
                $siblings.children('a').removeClass('in');
                $siblings.children('ul').removeClass('in').css('display','none');
            });
            //ifream 高度适应
            function setIframeHeight(iframe) {
                if (iframe) {
                    var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
                    if (iframeWin.document.body) {
                        iframe.height = iframeWin.document.documentElement.scrollHeight || iframeWin.document.body.scrollHeight;
                    }
                }
            };
            window.onload = function () {
                setIframeHeight(document.getElementById('page-cont'));
            };
            $("#refresh").click(function(){
                document.getElementById('page-cont').contentWindow.location.reload(true);
            });


			/**
			 * 单击修改密码弹出
			 */
			var changePwdLayer;
			$('#change_password').click(function() {
				$("#change_pwd_form input[type='password']").val('');
				$('#chErrorMessages>p').text('').hide();
				changePwdLayer = layer.open({
					type: 1,
					title:  ['修改密码', 'font-size:16px;color:#617293;'],
					closeBtn: 1,
					offset: '20%',
					fix: true,
					area: ['560px', '340px'],
					shadeClose: true,
					skin: '',
					content: $("#change_pwd_box")
				});
			});

			/**
			 * 修改密码
			 */
			$('#change_pwd_save').click(function() {
				if (!$("#new_password").val() && !$("#old_password").val() && !$("#confirm_password").val()) {
					$('#chErrorMessages>p').text('请填入相应信息').fadeIn();
				} else {
					$.ajax({
						type: "post",
						url: "/backend/main/change-password",
						data: {
							newP: $("#new_password").val(),
							oldP: $("#old_password").val(),
							conP: $("#confirm_password").val(),
							_token: $('input[name="_token"]').val()
						},
						dataType: "json",
						async: false,
						success: function (response) {
							if (response.state == 'success') {
								layer.alert(response.msg);
								layer.close(changePwdLayer);
							} else {
								$('#chErrorMessages').text(response.msg).fadeIn();
							}
						}
					});
				}
			});
			$('#change_pwd_cancel').click(function () {
				layer.close(changePwdLayer);
			});


        </script>
</body>
</html>
