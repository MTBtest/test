<?php include $this->admin_tpl("header"); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">站点设置</a> > 第三方登录设置
    </div>
    <span class="line_white"></span>
   	<div class="install mt10">
        <div class="sm"><?php echo $xmldata['description']; ?></div>
        <form method="POST" action="<?php echo U('Admin/Login/config'); ?>">
            <input type="hidden" name="login_code" value="<?php echo $xmldata['code'] ?>" />
        <ul class="web">
            <li>
                <strong>是否开启：</strong>
                <label><input type="radio" name="info[enabled]" value="1" <?php echo ($this->logins[$login_code]['enabled'] == 1) ? 'checked' :''; ?>/> 开启</label>&nbsp;
                <label><input type="radio" name="info[enabled]" value="0" <?php echo ($this->logins[$login_code]['enabled'] == 0) ? 'checked' :''; ?>/> 关闭</label>
                <span style="margin-left: 218px;">设置是否开启该登录方式</span>
            </li>
            <li>
                <strong>登录名称：</strong>
                <label><input type="text"  class="text_input" name="info[login_name]" value="<?php echo ($this->logins[$login_code]['login_name']) ? $this->logins[$login_code]['login_name'] : $xmldata['name']?>" size="40"></label>
                <span style="margin-left:1px;">设置登录名称供前台显示</span>
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
                            $_form = '<input type="text"  class="text_input" name="info[config]['.$k.'][value]" value="'.$this->logins[$login_code]['config'][$k].'" size="40">';
                            break;
                    }
                    ?>
                <li <?php if (!$_show): ?>style="display:none;"<?php endif ?>><strong><?php echo $v['name']; ?>：</strong><b><?php echo $_form; ?></b><span style="margin-left:4px;"><?php echo $v['tips'] ?></span></li>
            <?php endforeach ?>
            <li>
                <strong>登录方式描述：</strong>
				<textarea style="margin-right: 108px;" name="info[login_desc]"><?php echo $this->logins[$login_code]['login_desc'] ?></textarea>
                <p>您可以在这里编辑登录方式的详细描述，可在用户登录时显示</p>
            </li>
        </ul>
        <div class="submit"><input type="submit" class="button_search" name="dosubmit" value="提交"><a href="javascript:history.go(-1);">返回</a></div>
    </div>
<script>
	$(function(){
		$(".odd li:odd").css("background","#fff");
		$(".even li:even").css("background","#fff");
	});
</script>
    <?php include $this->admin_tpl("copyright"); ?>