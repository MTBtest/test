<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="renderer" content="webkit">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理中心</title>
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>admin/style.css" />
<!--[if IE 6]>
<script type="text/javascript" src="<?php echo JS_PATH; ?>DD_belatedPNG.js" ></script>
<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-1.7.2.min.js"></script>
</head>
<?php //print_r($this->topMenu) ?>
<body>
<div class="header">
    <div class="logo fl"><img src="<?php echo IMG_PATH; ?>admin/logo.png" width="160" height="21" alt="" /></div>
    <div class="menu-box">
        <div class="menu-left-bg"></div>
        <div class="menu fl">
            <?php
            foreach ($this->topMenu['topmenu'] as $key => $tmo) {
                echo "<a href='{$tmo['url']}' class='{$tmo['css']}' data='{$tmo['url']}'>{$tmo['name']}</a>";
            }
            ?>
        </div>
        <div class="menu-right-bg"></div>
    </div>
    <div class="help">
        <a href="<?php echo U('Cache/clear'); ?>"><img src="<?php echo IMG_PATH; ?>admin/ico_1.png" alt="" />更新缓存</a>
        <a href="http://wiki.haidao.la/"><img src="<?php echo IMG_PATH; ?>admin/ico_2.png" alt="" />帮助</a>
    </div>
    <div class="clear"></div>
    <div class="welcome">
        <a href="javascript:void(0)">欢迎您 <?php echo session('ADMIN_UNAME'); ?></a>|
        <a href="<?php echo U('Admin/index/changepwd'); ?>" target="mainFrame">更改密码</a>|
        <a href="<?php echo __APP__ ?>" target="_blank">网站前台</a>|
        <a href="<?php echo U('Admin/Public/logout'); ?>">退出系统</a>|
    </div>
</div>
<div class="side">
    <div class="head">
        <img src="<?php echo IMG_PATH; ?>admin/head.jpg" width="43" height="43" alt="" />
    </div>
    <h3><img src="<?php echo IMG_PATH; ?>admin/ico_6.png" />管理员</h3>
    <ul>
        <?php
        $i = 0;
        $notshow = array(13,30,31,34,49,50,51,52,53,54);
        foreach ($this->topMenu['submenu'] as $key => $mo) {
            if(in_array($mo['id'], $notshow)){
                echo "<li><a href='javascript:;' name='disabled' class='z_side disabled'>".str_replace('&HR', '', $mo['name'])."</a></li>";
            }else{
                echo "<li><a href='#' name='' class='n1{$i} z_side {$mo['css']}' data='{$mo['url']}'>".str_replace('&HR', '', $mo['name'])."</a></li>";
            }
            //添加分隔线
            if(strstr($mo['name'],'&HR')){
                echo"</ul><ul>";
            }
            $i++;
	}
        ?>
    </ul>
</div>
<div id="Container" style="min-width: 1000px;">
    <div class="ico_left"><img src="<?php echo IMG_PATH; ?>admin/ico_8.png" /></div>
    <iframe id="mainFrame" style="min-width: 1000px;" name="mainFrame" frameborder="0" src="" width="100%" height="100%" >
    </iframe>
</div>
<script>
$(".z_side").click(function() {
    $("iframe").attr("src", $(this).attr("data"));
});
if (top.location !== self.location) {
    top.location = self.location;
}
$(".side a[name!='disabled']").eq(0).addClass('hover').click();
//左侧side中的hover 效果
$(function(){
	$(".side li a").click(function(){
		if($(this).hasClass('disabled')) return false;
		$(".side li a").removeClass("hover");
		$(this).addClass("hover");
	});
});
/**
 * 显示和收起后台导航
 */
$(".ico_left").toggle(function(){
			$(".side").animate({left:"-200px"});
			$("#Container").animate({left:"0"});
			$(".welcome").animate({paddingLeft:"10px"});
			$(this).children().attr('src','<?php echo IMG_PATH; ?>admin/ico_8a.png');
		},
		function(){
			$(".side").animate({left:"0px"});
			$("#Container").animate({left:"200px"});
			$(".welcome").animate({paddingLeft:"65px"});
			$(this).children().attr('src','<?php echo IMG_PATH; ?>admin/ico_8.png');
		}
	  );
</script>
</body>
</html>