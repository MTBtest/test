<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<style type="text/css">
	input{margin-top: 0px;}
	.text_input{margin-right: 0px;}
</style>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">内容管理</a> &gt; 编辑导航
	</div>
	<div class="line_white"></div>
	<div class="goods mt10">
		<div class="login mt10" style="border: none;">
			<table id="article_nav_lists" style="width:100%"></table>
		</div>
		<div class="clear"></div>
		<?php include $this->admin_tpl('copyright') ?>
	</div>
</div>
<!--表格js-->
	<script type="text/javascript">
	var dom = $('#article_nav_lists');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('manage')?>';
	var saveurl = '<?php echo U('update')?>';
	var delurl = '<?php echo U('update',array('opt'=>'del'))?>';
	var statusurl = '<?php echo U('ajax_status')?>';
	var editRow = undefined;
	$(function(){
		dom.datagrid({
			url:dataurl,
			striped:true,
			width:'100%',
			checkOnSelect:true,
			fitColumns:true,
			toolbar:[
				{
					id:'delrows',
					text:'删除',
					iconCls:'icon-del',
				},'-',
				{
					id:'addnavrows',
					text:'添加',
					iconCls:'icon-add',
//					 //添加导航
					 handler:function(){
						row = {name: '',url: 'http://',title: '',sort:100,enable:1}
						if (editRow != undefined) dom.datagrid('endEdit', editRow);
						flag = dom.datagrid('validateRow', editRow);
						if (flag){
							dom.datagrid('appendRow',row);
							index = dom.datagrid('getRows').length-1;
							dom.datagrid('beginEdit', index);
							editRow = index;
						}
					}
				},'-',
				{
					id:'saverows',
					text:'保存',
					iconCls:'icon-backup',
					handler:function(){
						flag = dom.datagrid('validateRow', editRow);
						if(flag){
							dom.datagrid('endEdit', editRow);
							rows = dom.datagrid('getChanges');
							if (rows.length > 0){
								var rowstr = JSON.stringify(rows);
								$.post(saveurl, {data:rowstr}, function (data){
									dom.datagrid("reload");	//重新加载
								});
							}
						}
					}
				},'-'
			],
			frozenColumns:[[
				{field:'id',checkbox:true}
			]],
			columns:[[
				{field:'name',title:'导航标题',width:'25%',halign:'center',align:'center',editor:{type:'validatebox', options:{required:true}}},
				{field:'url',title:'链接地址',width:'25%',halign:'center',align:'center',editor:{type:'validatebox', options:{required:true}}},
				{field:'title',title:'链接说明',width:'20%',halign:'center',align:'center',editor:{type:'text'}},
				{field:'sort',title:'链接排序',width:'15%',halign:'center',align:'center',editor:{type:'numberspinner', options:{required:true}}},
				{field:'enable',title:'是否显示',width:'10%',halign:'center',align:'center',editor:{type:'checkbox',options:{on:'1',off:'0'}},
					formatter:function(value,row,index){
						if(value ==1){
							statustext = '<span url="'+statusurl+'&id='+row.id+'" class="ajax-get ajax_on" onclick="dom.datagrid(\'reload\')"></span>'
						}else{
							statustext = '<span url="'+statusurl+'&id='+row.id+'" class="ajax-get ajax_off" onclick="dom.datagrid(\'reload\')"></span>'
						}
						return statustext;
					}
				},
				{field:'none',title:'操作',width:'5%',halign:'center',align:'center',
					formatter:function(value,row,index){
						var spacehtml = '&nbsp;&nbsp;&nbsp;&nbsp;';
						var edithtml = '<a href="javascript:void(0)" onclick="dom.datagrid(\'clearSelections\');editrow('+index+')">编辑</a>'
						var delhtml = '<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');$(\'#delrows\').trigger(\'click\')" href="#">删除</a>';
						return edithtml+spacehtml+delhtml;
					}
				},
			]],
			onLoadSuccess:function(data){
				$('.sort').numberspinner({
					onChange:function(nvalue,ovalue){
						var id = $(this).attr('data-id');
						ChangeSort(id,nvalue);
					}
				});
				$('.datagrid-btable td[field="sort"]').each(function(){
					$(this).click(function(){
						return false;
					});
				})
			},
			onAfterEdit: function (rowIndex, rowData, changes) {
				editRow = undefined;
			},
//			onClickRow:function(rowIndex,rowData){
//				if (editRow != undefined) dom.datagrid('endEdit', editRow);
//				dom.datagrid('selectRow', rowIndex).datagrid('beginEdit', rowIndex);
//				editRow = rowIndex;
//			}
		});
		//编辑行
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
								dom.datagrid("reload");	//重新加载
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
	function editrow(index){
		if (editRow != undefined) dom.datagrid('endEdit', editRow);
			dom.datagrid('beginEdit', index);
			editRow = index;
	}
</script>