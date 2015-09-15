<?php include $this->admin_tpl("header"); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">站点设置</a> > 通知系统设置
    </div>
    <span class="line_white"></span>
   	<div class="install tabs mt10">
        <?php if($xmldata['code']=='email'):?>
            <dt ><a href="javascript:void(0)" class="hover">邮件设置</a><a href="javascript:void(0)">测试发送</a></dt>
        <?php endif?>
        <dd>
        <div class="sm"><?php echo $xmldata['description']; ?></div>
        <form method="POST" action="<?php echo U('config'); ?>">
        <input type="hidden" name="code" value="<?php echo $xmldata['code'] ?>">
        <ul class="web">
            <?php if ($code == 'sms'): ?>
            <li>
                <strong>当前剩余短信条数：<b style="color:red;margin-right:0;text-align:center;width:60px;"><?php echo $cloud['sms']; ?></b>条<a href="http://www.haidao.la/index.php?m=Sms&c=Index&a=pay_sms" target="_blank" style="margin-left:180px;">去官网充值</a></strong>
            </li>
            <?php endif; ?>
            <li>
                <strong>是否开启：</strong>
                <label><input type="radio" name="info[enabled]" value="1" <?php echo ($notify['enabled'] == 1) ? 'checked' :''; ?>/> 开启</label>&nbsp;
                <label><input type="radio" name="info[enabled]" value="0" <?php echo ($notify['enabled'] == 0) ? 'checked' :''; ?>/> 关闭</label>
                <span style="margin-left: 218px;">设置是否开启该通知方式</span>
            </li>
            <?php foreach ($xmldata['config'] as $k => $v): ?>
                <?php
                    $_show = TRUE;
                    switch ($v['type']) {
                        case 'hidden':
                            $_show = FALSE;
                            $_form = '<input type="hidden" name="info[config]['.$k.'][value]" value="'.$v['value'].'">';
                            break;
                        default:
                            $_form = '<input type="text"  class="text_input" name="info[config]['.$k.'][value]" value="'.$notify['config'][$k].'" size="40">';
                            break;
                    }
                ?>
                <li <?php if (!$_show): ?>style="display:none;"<?php endif ?>><strong><?php echo $v['name']; ?>：</strong><b><?php echo $_form; ?></b><span style="margin-left:4px;"><?php echo $v['tips'] ?></span></li>
            <?php endforeach ?>
            <div class="submit"><input type="submit" class="button_search" name="dosubmit" value="提交"><a href="<?php echo U('setting') ?>">返回</a></div>
        </ul>
        </dd>
        </form>
        <?php if($xmldata['code']=='email'):?>
        <dd>
            <ul class="web">
                <form name="testmailform" action="<?php echo U('Admin/Site/testmail') ?>" method="post">
                <li>
                    <strong>测试发件人：</strong>
                    <input type="text" class="text_input" value="<?php $notifys=getcache('notify', 'notify');echo ($notifys['email']['config']['mail_formmail']) ?>" name="formuser" /><span>填写发件人邮箱,如使用QQ邮箱此项为邮箱地址不能更改</span>
                </li>
                <li>
                    <strong>测试邮件列表：</strong>
                    <textarea name="touser" style="margin-right: 104px;" placeholder="test@test.com"></textarea>
                    <p>如果要测试包含用户名的邮件地址，格式为 test1@test.com,test2@test.com 多个邮箱用半角符号分隔</p>
                </li>
                <input type="submit" class="button_search" value="测试发送" style="margin-left:5px;margin-top: 15px;"/>
                </form>
            </ul>
        </dd>
        <?php endif?>
    </div>
<script>
	$(function(){
		$(".odd li:odd").css("background","#fff");
		$(".even li:even").css("background","#fff");
	});
    $(document).ready(function () {
        $(function () {
            var tabTitle = ".tabs dt a";
            var tabContent = ".tabs dd";
            $(tabTitle + ":first").addClass("hover");
            $(tabContent).not(":first").hide();
            $(tabTitle).unbind("click").bind("click", function () {
                $(this).siblings("a").removeClass("hover").end().addClass("hover");
                var index = $(tabTitle).index($(this));
                $(tabContent).eq(index).siblings(tabContent).hide().end().fadeIn(0);
            });
        });
    });
</script>
    <?php include $this->admin_tpl("copyright"); ?>