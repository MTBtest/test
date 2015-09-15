<?php include $this -> admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
	Haidao Board <a href="#">运营推广</a> > 商品促销
	</div>
	<span class="line_white"></span>
	<div class="goods mt10">
		<div class="login mt10" style="border: none;">
			<table id="goods_promotion_list" style="width:100%"></table>
			<div class="clear"></div>
		</div>
		<?php include $this->admin_tpl("copyright"); ?>
		<div class="clear"></div>
	</div>
</div>
	<div class="clear"></div>
<script type="text/javascript">
	var dom = $('#goods_promotion_list');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('lists')?>';
	var addurl = '<?php echo U('edit')?>';
	var editurl = '<?php echo U('edit')?>';
	var delurl = '<?php echo U('ajax_del')?>';
	var parse_type = <?php echo json_encode($this->parse_type)?>;
	var type_text = '-';
	var status_text = '-';
	$(function(){
		dom.datagrid({
			url:dataurl,
			striped:true,
			checkOnSelect:true,
			fitColumns:true,
			toolbar:[
						{
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
				{field:'name',title:'商品名称',width:'15%',align:'center',fixed:true,sortable:true},
				{field:'award_type',title:'促销类型',width:'15%',align:'center',fixed:true,sortable:true,formatter:function(value,row,index){
						return parse_type[value];
					}
				},
				{field:'status',title:'开启状态',width:'10%',align:'center',fixed:true,sortable:true,
					 formatter:function(value,row,index){
						if(value == 1){
							status_text = '<font style="color:#009900">开启</font>';
						}else{
							status_text = '<font style="color:#ff0000">关闭</font>';
						}
						return status_text;
					}
				},
				{field:'start_time',title:'活动时间',width:'30%',align:'center',fixed:true,
					formatter:function(value,row,index){
  						  						return $.fn.datebox.defaults.timeformat(value)+'~'+$.fn.datebox.defaults.timeformat(row.end_time);
					}
				},
				{field:'description',title:'促销说明',width:'15%',align:'center',fixed:true,sortable:true},
				{field:'none',title:'操作',width:'15%',align:'center',
					formatter:function(value,row,index){
						var edithtml = '<a href="'+editurl+'&id='+row.id+'">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var delhtml = '<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');$(\'#delrows\').trigger(\'click\')" href="#">删除</a>';
						return edithtml + delhtml;
					}
				},
			]]
		});
		//添加
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