<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">

<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery.print.js"></script>
 <style type="text/css">
    .button_search{
        background-image: -webkit-linear-gradient(top,#ffffff,#e6e6e6);
        background-image: -moz-linear-gradient(top,#ffffff,#e6e6e6);
        background-image: -o-linear-gradient(top,#ffffff,#e6e6e6);
        border: 1px solid #c7c7c7;
        cursor: pointer;
        width: 80px;
        height: 26px;
        line-height: 24px;
        margin-top: 20px;
        margin-bottom: 20px;
    }
</style>

<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content" >
<div class="site">
	Haidao Board <a href="#">站点设置</a> > 发货单打印
</div>
<div id="tool" style="text-align:center;">
    <input type='button' class='button_search' onclick="printStart();" value='开始打印' /> &nbsp;&nbsp;
    <input type='button' class='button_search' onclick='window.history.go(-1);' value='返回上一级' />
</div>
<div id="content">
<?php echo $content;?>
</div>
<?php include $this->admin_tpl('copyright'); ?>
</div>

<script type="text/javascript">
function printStart() {
    $("#content").jqprint();
}
</script>

