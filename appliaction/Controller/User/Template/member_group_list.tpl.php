<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">会员管理</a> > 会员等级
	</div>
	<span class="line_white"></span>
	<div class="login mt10" style="border: none;">
		<table id="group_list" style="width:100%"></table>
		<div class="clear"></div>
	</div>
	<?php include $this->admin_tpl("copyright"); ?>
	<div class="clear"></div>
</div>
<script type="text/javascript">
	var dom = $('#group_list');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('lists')?>';
	var addurl = '<?php echo U('add')?>';
	var delurl = '<?php echo U('ajax_del')?>';
	var editurl = '<?php echo U('edit')?>';
	$(function(){
		dom.datagrid({
			url:dataurl,
			striped:true,
			width:'100%',
			fitColumns:true,
			checkOnSelect:true,
			toolbar:[{
					id:'delrows',
					text:'删除',
					iconCls:'icon-del',
				},'-',
				{
					id:'addrow',
					text:'添加',
					iconCls:'icon-add',
				},'-'
				],
				frozenColumns:[[
					{field:'id',checkbox:true}
				]],
				pagination:true,
				pageSize:pageSize,
				pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表
				columns:[[
				{field:'name',title:'等级名称',halign:'center',align:'center',width:'20%',sortable:true},
				{field:'min_points',title:'最小经验',width:'20%',align:'center',sortable:true},
				{field:'max_points',title:'最大经验',width:'20%',align:'center',sortable:true},
				{field:'discount',title:'折扣率',width:'20%',align:'center',sortable:true},
				{field:'none',title:'操作',width:'20%',align:'center',halign:'center',
					formatter:function(value,row,index){
							return '<a href="'+editurl+'&id='+row.id+'">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');$(\'#delrows\').trigger(\'click\')" href="#">删除</a>';
						}
				},
			]],
			onLoadSuccess:function(data){}
		});
		//添加会员
		$('#addrow').bind('click', function(){
			window.location.href=addurl;
		})
		//删除操作
		$('#delrows').bind('click', function(){
			var ids = [];
			var rows = dom.datagrid('getChecked');
			for(var i=0; i<rows.length; i++){
				ids.push(rows[i].id);
			}
			if (ids.length > 0){
				$.messager.confirm('确认','您确认想要删除记录吗？',function(r){
					if (r){
						$.getJSON(delurl,
						{"id[]":ids},
						function(data){
							if(1 == data.status){// 删除成功，则需要在树中删除节点
								// 检修任务grid 执行load
								dom.datagrid("reload");//重新加载
							}else{
								$.messager.alert('警告',data.info);
							}
						})
					}else{
						dom.datagrid('clearSelections').datagrid('clearChecked');
					}
				});
			}else{
				$.messager.alert('警告','请选择要删除的记录');
				return false;
			}
		});
	})
</script>