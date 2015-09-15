<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<style type="text/css">
	.textbox .textbox-text{text-align: center;}
</style>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">站点设置</a> > 物流配送设置
	</div>
	<span class="line_white"></span>
	<div class="install mt10">
		<div class="login mt10" style="border: none;">
			<table id="pay_manage" style="width:100%"></table>
			<div class="clear"></div>
		</div>
		<?php include $this->admin_tpl("copyright"); ?>
		<div class="clear"></div>
	</div>
<script type="text/javascript">
	var dom = $('#pay_manage');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('lists')?>';
	var addurl = '<?php echo U('add')?>';
	var editurl = '<?php echo U('edit')?>';
	var delurl = '<?php echo U('delete')?>';
	var sorturl = '<?php echo U('ajax_sort')?>';
	$(function(){
		dom.datagrid({
			url:dataurl,
			striped:true,
			width:'100%',
			checkOnSelect:true,
			fitColumns:true,
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
				{field:'name',title:'配送方式',width:'15%',align:'center',sortable:true,},
				{field:'status',title:'是否启用',width:'15%',align:'center',sortable:true,
					formatter:function(value,row,index){
						try{
							if(value == 1){
								status_text = '开启';
							}else{
								status_text = '关闭';
							}
						}catch(e){
							status_text = '-'
						}
						return status_text;
					}
				},
				{field:'type',title:'货到付款',width:'20%',align:'center',sortable:true,
					formatter:function(value,row,index){
						try{
						   /*if(value == 1){
								type_text = '先发货后付款,支持';
							}else{
								type_text = '先支付后发货';
							}*/
							if($.inArray('1',value) == -1){
								type_text = '不支持';
							 }else if($.inArray('1',value) == 0){
								type_text = '支持';
							}else if($.inArray('1',value) == 1){
								type_text = '支持';
							}
						}catch(e){
							type_text = '-'
						}
						return type_text;
					}
				},
				{field:'insure',title:'物流保价(%)',width:'15%',align:'center',sortable:true,
					formatter:function(value,row,index){
						try{
							if(value == 1){
								insure_text = value+'%';
							}else{
								insure_text = '不支持';
							}
						}catch(e){
							insure_text = '-'
						}
						return insure_text;
					}
				},
				{field:'sort',title:'品牌排序',width:'15%',align:'center',sortable:true,
					formatter:function(value,row,index){
						return '<input name="sort" class="easyui-numberspinner sort" style="width:80px;" required="required" data-options="min:0,editable:true" value="'+value+'" data-id="'+row.id+'">';
					}
				},
				{field:'none',title:'操作',width:'20%',align:'center',
					formatter:function(value,row,index){
						var edithtml = '<a href="'+editurl+'&id='+row.id+'">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var delhtml = '<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');$(\'#delrows\').trigger(\'click\')" href="javascript:void(0)">删除</a>';
						return edithtml + delhtml;
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
			}
		});
		//修改排序
		function ChangeSort(id,val){
			$.messager.progress();
			$.getJSON(sorturl, {"id": id,"val": val}, function(data) {
				$.messager.progress('close');
			})
		}
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
								$.messager.alert('提示',data.info);
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
	});
</script>