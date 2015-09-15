<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<style type="text/css">
	.menu {background:#FFFFFF;}
</style>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
	<!-- 内容区 -->
	<div class="content">
		<div class="site">
			Haidao Board <a href="#">站点设置</a> > 通知模版设置
		</div>
		<span class="line_white"></span>
	<div class="goods mt10">	
	<dl class="mt10">
		<dd>
			<div class="login mt10" style="border: none;">
				<table id="notify_template" style="width:100%"></table>
			</div>
			<div class="clear"></div>
		</dd>
	</dl>
		 <?php include $this->admin_tpl('copyright') ?>
	</div>

<!--表格js-->
	<script type="text/javascript">
	var dom       = $('#notify_template');
	var dataurl   = '<?php echo U('template');?>';
	var statusurl = '<?php echo U('ajax_status');?>';
	$(function(){	
		dom.datagrid({   
			url:dataurl, 
			striped:true, //交替换行
			width:'100%',
			checkOnSelect:true,
			fitColumns:true, //真正的自动展开/收缩列的大小，以适应网格的宽度，防止水平滚动。
			columns:[<?php echo json_encode($cols); ?>],
		});
	})
	/* 开关按钮 */
	function ajax_driver(o){
		// 当前状态
		var status = ($(o).hasClass('ajax_off') == true) ? 1 : 0;
		$.get(statusurl,{
			notify_id : $(o).attr('notify_id'),
			notify_code : $(o).attr('notify_code'),
			status : status
		},function(ret){
			if (ret.status != 1) $.messager.alert('错误',ret.info);
			dom.datagrid("reload");  //重新加载
		},'json');
	}
</script>