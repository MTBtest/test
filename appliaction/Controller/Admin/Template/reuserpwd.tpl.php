<?php include $this->admin_tpl("header"); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="javascript:void(0)">系统设置</a> &gt; 重设密码
    </div>
    <div class="line_white"></div>
    <div>
    	<div class="mt10">
				<p style="color:#000000">本功能仅对系统超级管理员进行账号管理，您必须填写原密码才能修改下面的资料信息。</p>
		</div>
		<div class="clear"></div>
    </div>
   	<div class=" mt10">
   		<form class="changepwdform" action="<?php echo U('reuserpwd')?>" method="post">
    	<dl>
            <dd>
            	<ul class="web">
            		<li> <strong>修改用户：</strong>
                        <input type="text" value="<?php echo current($user)?>" class="text_input" name="user" readonly >
                        <span class="Validform_checktip">要修改的管理员用户名</span>
            		</li>
            		<li> <strong>管理员密码：</strong>
                        <input type="password" value="" class="text_input" datatype="*6-15" errormsg="密码范围在6~15位之间！" name="adminpassword" nullmsg='请填写原密码'>
                        <span class="Validform_checktip">请输入管理员原密码作为验证信息</span>
            		</li>
            		<li> <strong>新密码：</strong>
                        <input type="password" value="" class="text_input" datatype="*6-15" errormsg="密码范围在6~15位之间！" name="password" nullmsg='请填写新密码' >
                        <span>请输入您要修改的管理员新密码，如不修改可不填</span>
            		</li>
            		<li> <strong>确认新密码：</strong>
                        <input type="password" value="" class="text_input" datatype="*6-15" errormsg="您两次输入的账号密码不一致！" recheck="password" name="npassword" nullmsg='请确认新密码' >
                        <span>请再次输入您要修改的管理员新密码，如不修改可不填</span>
            		</li>
                </ul>
                <div class="input1">
                	<input type="hidden" name="id" id="id" value="<?php echo key($user)?>" />
					<input class="button_search" type="submit" value="提交">
				</div>
            </dd>
        </dl>
        </form>
    </div>
<script type="text/javascript">
$(function(){
	$(".changepwdform").Validform({
		ajaxPost : true,
		tiptype:function(msg,o,cssctl){
			if(o.type == 3){
				//alert(msg);
			}
		},
		callback:function(data){
			alert(data.info);
			if(data.status == 1){
				window.location.href=data.url;
			}
		}
	});
});
</script>
<?php include $this->admin_tpl("copyright"); ?>
