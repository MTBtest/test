<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">会员管理</a> > 会员列表 > 会员地址
	</div>
	<span class="line_white"></span>
	<div class="goods mt10">
		<div class="login mt10" style="border:none">
			<table id="member_address_list" style="width:100%"></table>
		 </div>
		<?php include $this->admin_tpl("copyright"); ?>
		<div class="clear"></div>
	</div>
</div>
<script type="text/javascript">
	var dom = $('#member_address_list');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('lists')?>';
	var listurl = '<?php echo U('member/lists')?>';
	var user_id = '<?php echo $user_id?>';
    $(function(){
		dom.datagrid({
			url:dataurl,
			striped:true,
			checkOnSelect:true,
			fitColumns:true,
			toolbar:[{
					id:'backlists',
					text:'返回',
					iconCls:'icon-undo',
				},'-'
			],
			frozenColumns:[[
			{field:'id',checkbox:true}
			]],
			pagination:true,
			pageSize:pageSize,
			pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表
			queryParams:{"user_id":user_id},
			columns:[[
				{field:'address_name',title:'收货人',width:'15%',align:'center',fixed:true},
				{field:'prov_name',title:'地址',width:'40%',align:'center',fixed:true,
					formatter:function(value,row,index){
  						return row.prov_name + row.city_name + row.dist_name + row.address;
					}
				},
				{field:'zipcode',title:'邮编',width:'15%',align:'center',fixed:true},
				{field:'tel',title:'电话',width:'15%',align:'center',fixed:true},
				{field:'mobile',title:'手机',width:'15%',align:'center',fixed:true},
			]]
		});
		//返回
		$('#backlists').bind('click',function(){
			window.location.href=listurl;
		})
	})
</script>