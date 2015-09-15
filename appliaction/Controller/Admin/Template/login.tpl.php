<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="x-ua-compatible" content="ie=7" />
<title>后台登录</title>
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>admin/login.css" />
<!--[if IE 6]>
<script type="text/javascript" src="<?php echo JS_PATH; ?>DD_belatedPNG.js" ></script>
<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH ?>jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH ?>Validform_v5.3.2_min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH ?>artDialog/artDialog.js?skin=default"></script>
</head>
<style type="text/css">
	#Validform_msg{display: none;}
	.Validform_error {
		background-color: #fffbea;
		border: 1px solid #e9bb89;
	}
</style>
<body>
	<div class="main">
		<div class="login">
			<div class="left">
				<p>
					<a href="" target="_blank">海盗云商系统</a> 是 <a href="" target="_blank">迪米盒子</a>公司旗下以电子商务为基础的专业网店建站系统，专注电子商务运营服务。
				</p>
			</div>
			<div class="right">
                    <form action="" method="" name="" id="" class="loginform">
				<p>
					<label>用户名：</label><input type='text' name="username" class="Inpt" placeholder="请输入用户名" datatype="*4-10|/^[\u4E00-\u9FA5\uf900-\ufa2d]{2,4}$/"/>
				</p>
				<p>
					<label>密&nbsp;&nbsp;&nbsp;码：</label><input type='password' name="userpass"  class="Inpt" placeholder="请输入密码" datatype="*"/>
				</p>
				<p>
					<label>验证码：</label><input type='text' name="userverify" class="captcha" placeholder="验证码" datatype="/\w{4}/i" errormsg="请输入4位验证码！" /><img src="<?php echo U('verify') ?>" id="verify" alt="看不清楚，换一张" style="cursor: pointer; vertical-align:middle; margin-top:-4px!important;maargin-top:-0px" onclick="this.src='<?php echo U('verify') ?>&_t=' + Math.round(new Date().getTime()/1000)" />
				</p>
				<p>
					<label></label><input type="submit" name="" value="" class="Btn" />
				</p>
				</form>
			</div>
		</div>
	</div>
<script type="text/javascript">
var demo=$(".loginform").Validform({
        tiptype: function(){},
        label:".label",
        showAllError:true,
        ajaxPost:true,
        callback:function(ret){
        	if(ret.status != 1) {
        		$('#verify').trigger('click');
        		art.dialog({width: 320,time: 5,title:'温馨提示(5秒后关闭)',content: ret.info,ok:true});
        	} else {
          		window.location.href=ret.url;
        		return;
        	}
        }
});
if (top.location !== self.location) {
    top.location=self.location;
}
</script>
</body>
</html>