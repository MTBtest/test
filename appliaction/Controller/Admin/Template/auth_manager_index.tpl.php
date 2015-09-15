<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">站点设置</a> > 权限管理
	</div>
	<span class="line_white"></span>
	<div class="install mt10">
		<div class="login mt10" style="border: none;">
			<table id="auth_manage" style="width:100%"></table>
			<div class="clear"></div>
		</div>
		<?php include $this->admin_tpl("copyright"); ?>
		<div class="clear"></div>
	</div>
<script type="text/javascript">
	var dom = $('#auth_manage');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('index')?>';
	var addurl = '<?php echo U('creategropup')?>';
	var delurl = '<?php echo U('delete')?>';
	var accessurl = '<?php echo U('access')?>';
	var ststusurl = '<?php echo U('changeStatus')?>';
	$(function(){
		dom.datagrid({
			url:dataurl,
			striped:true,
			width:'100%',
			checkOnSelect:true,
			singleSelect:true,
			fitColumns:true,
			pagination:true,
			toolbar:[
				{
					id:'addrow',
					text:'添加',
					iconCls:'icon-add',
				},'-'
			],
			pageSize:pageSize,
			pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表
			columns:[[
				{field:'title',title:'用户组',width:'26%',align:'center'},
				{field:'description',title:'描述',width:'50%',halign:'center',align:'center',sortable:true},
				{field:'none',title:'操作',width:'25%',halign:'center',align:'center',
					formatter:function(value,row,index){
						var authhtml = '<a href="'+accessurl+'&group_id='+row.id+'&title='+row.title+'" >访问授权</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var banhtml = '<a href="javascript:void(0)" >禁止</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var edithtml = '<a href="'+addurl+'&id='+row.id+'" >编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var delhtml = '<a href="javascript:void(0)" onclick="chang_status(\'deletegroup\','+row.id+')">删除</a>';
						return authhtml  + edithtml + delhtml;
					}
				},
			]],
		});
		//添加
		$('#addrow').bind('click', function(){
			window.location.href=addurl;
		})
	});
	//状态
	function chang_status(method,id){
		$.messager.confirm('确认','您确认想要执行操作吗？',function(r){
			if (r){
				$.getJSON(ststusurl,
				{"method":method,"id":id},
				function(data){
					if(1 == data.status){
						dom.datagrid("reload");  //重新加载
					}else{
						$.messager.alert('提示',data.info);
					}
				})
			}
		});
	}
</script>