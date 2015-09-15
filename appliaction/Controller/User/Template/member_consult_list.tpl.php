<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">会员管理</a> > 咨询列表
	</div>
	<span class="line_white"></span>
	<div class="goods mt10">
		<table id="consultgrid" style="width:100%"></table>
		<div class="clear"></div>
		<?php include $this->admin_tpl('copyright'); ?>
	</div>
</div>
<script type="text/javascript">
	var dom = $('#consultgrid');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('lists')?>';
	var delurl = '<?php echo U('delete')?>';
	var repurl = '<?php echo U('reply')?>';
	$(function(){
		dom.datagrid({
			url:dataurl,
			striped:true,
			fitColumns:true,
			checkOnSelect:true,
			toolbar:[{
					id:'delrows',
					text:'删除',
					iconCls:'icon-del',
				},'-'
			],
			frozenColumns:[[
				{field:'id',checkbox:true}
			]],
			pagination:true,
			pageSize:pageSize,
			pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表
			columns:[[
			{field:'status',title:'状态',fixed:true,sortable:true,align:'center',width:'10%',
						formatter:function(value,row,index){
						var status_text = '';
						if(value == 0){
							status_text = '<font style="color:red">未审核</font>';
						}else{
							status_text = '<font style="color:#009900">已审核</font>';
						}
						return status_text;
					}
				},
				{field:'question',title:'咨询内容',halign:'center',width:'50%'},
				{field:'time',title:'咨询时间',fixed:true,width:'15%',align:'center',
					formatter:function(value,row,index){
  						return $.fn.datebox.defaults.timeformat(value);
					}
				},
				{field:'reply_time',title:'回复时间',align:'center',width:'15%',align:'center',
					 formatter:function(value,row,index){
					 	 var reply_text = '';
							if(value == 0){
							y_text = '未回复';
							}else{
								reply_text = $.fn.datebox.defaults.timeformat(value);
							}
							return reply_text;
					}
				},
				{field:'userID',title:'操作',width:'10%',align:'center',
					formatter: function(value,row,index){
					return '<a href='+repurl+'&id='+row.id+'>回复</a>&nbsp;&nbsp;<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');$(\'#delrows\').trigger(\'click\')" href="#">删除</a>';
					}
				},
			]]
		});
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
								dom.datagrid("reload");  //重新加载
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
</body>
</html>