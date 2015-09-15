<?php include $this->admin_tpl("header"); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">云平台</a> &gt; 平台首页
    </div>
    <div class="line_white"></div>
    <?php if (!$cloud): ?>
    <div class="tisi gray7d mt5" tips-id="01"><em class="improt">温馨提示：</em>您还未绑定您的海盗云平台，请先绑定账号后再继续操作！</div>
    <?php endif ?>
    <dl class="gzzt clearfix mt10">
        <dt>平台<br>状态</dt>
        <dd>
            <div class="boxl gray7d fl">
                <p style="width: 460px;"><?php if (isset($cloud['username'])): ?><strong><?php echo $cloud['username'] ?></strong> 您好，欢迎登陆. <?php else: ?>我还没有海盗云平台账号&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.haidao.la/index.php?m=Member&c=Public&a=register" target="_blank" style="color: #2d689f;">免费注册</a><?php endif ?></p>
            </div>
            <div class="boxr gray7d fl">
                <p style="width: 240px; padding: 0 18px;"><em style="color: #2d689f;font-style:normal;">短信余额：</em><?php if (isset($cloud['sms'])): ?><?php echo $cloud['sms'] ?><?php else: ?>绑定后云平台查看<?php endif ?></p>
            </div>
            <div class="boxr gray7d fl">
                <p style="padding: 0 18px;"><i class="sq-info <?php if ($cloud['authorize'] == 1): ?>sq-ok<?php endif ?>"></i><?php if (isset($cloud['authorize'])): ?><?php if ($cloud['authorize'] == 1): ?>已授权 <?php else: ?>未授权<?php endif ?><?php else: ?>绑定后云平台查看<?php endif ?></p><!--加sq-ok为橙色图标-->
            </div>
        </dd>
    </dl>
    <div class="terrace mt10">
        <?php if (!$cloud): ?>
        <div class="terrace-login">
            <p class="title">输入海盗云商官方账号完成绑定</p>
            <div class="list">
                <p><b>登录名：</b></p>
                <input class="input" type="text" name="account" value="" placeholder="手机/昵称" />
            </div>
            <div class="list">
                <p><b>密码：</b><a href="http://www.haidao.la/index.php?m=Member&c=Public&a=repwd" target="_blank">忘记密码？</a></p>
                <input class="input" type="password" name="password" value="" placeholder="登录密码" />
            </div>
            <div class="list">
                <input class="btn" type="button" id="cloud_login" value="绑定" />
            </div>
        </div>
        <?php endif ?>
        <div class="terrace-box fl">
            <p class="terrace-intro-txt">
                <em>海盗云商云平台</em><br/>
                海盗云商云平台是海盗最新推出的云服务平台，绑定账号后您所管理的站点将获得以下功能
            </p>
            <div class="terrace-content">
                <dl class="fl">
                    <dt><img src="<?php echo IMG_PATH; ?>admin/terrace_ico_1.png" /></dt>
                    <dd>
                        <p class="terrace-intro-txt"><em>短信服务</em></p>
                        <p>您所绑定的站点将可以使用<br/>您海盗云平台的短信条数发<br/>送短信</p>
                    </dd>
                </dl>
                <dl class="fl">
                    <dt><img src="<?php echo IMG_PATH; ?>admin/terrace_ico_2.png" /></dt>
                    <dd>
                        <p class="terrace-intro-txt"><em>微信通知</em>（即将推出）</p>
                        <p>您所绑定的站点将无法访问<br/>时通过云平台绑定的微信账<br/>号收到通知</p>
                    </dd>
                </dl>
                <dl class="fl">
                    <dt><img src="<?php echo IMG_PATH; ?>admin/terrace_ico_3.png" /></dt>
                    <dd>
                        <p class="terrace-intro-txt"><em>应用安装</em>（即将推出）</p>
                        <p>您所绑定的站点将可以安装<br/>使用海盗云平台的任意模板<br/>及插件</p>
                    </dd>
                </dl>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
<script type="text/javascript">
$(function() {
    $("#cloud_login").click(function() {
        var $_this = $(this);
        if($_this.hasClass('disabled')) return false;
        var account = $("input[name=account]"), password = $("input[name=password]");
        if(account.val().length < 1) {
            account.focus();
            return;
        }
        if(password.val().length < 1) {
            password.focus();
            return;
        }
        $_this.attr('value', '绑定中').addClass('disabled')
        $.post('<?php echo U("login");?>', {account:account.val(), password:password.val()}, function(data, textStatus, xhr) {
            $_this.attr('value', '绑定').removeClass('disabled')
            alert(data.info);
            if(data.status == 1) {
                setTimeout(function() {
                    redirect(data.url);
                }, 1000);
            }
            return;
        }, 'JSON');
    });
})
</script>
<?php include $this->admin_tpl("copyright"); ?>