<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<style type="text/css">
	#area_box .content{min-width: 570px;padding: 0px;}
</style>
<div class="content">
	<div class="site">Haidao Board <a href="#">站点设置</a> > 区域划分</div>
	<div class="goods mt10">
		<div class="login mt10" style="border: none;">
			<table id="zone_manage" style="width:100%"></table>
			<div class="clear"></div>
		</div>
		<?php include $this->admin_tpl("copyright"); ?>
		<div class="clear"></div>
	</div>
<!--弹出框-->
	<div id="area_box" class="easyui-dialog edit_areabox"  closed="false" modal="true" minimizable="false" maximizable="false" collapsible="false" data-options="title:'地区管理',
	closed:true,
	buttons:[{
		text:'保存',
			handler:function(){
				dosubmit();
			}
		},{
			text:'关闭',
			handler:function(){
				try{
					$('#area_box').window('close');
				}catch(e){
				}
			}
		}]">
	</div>
</div>
<script type="text/javascript">
	var dom = $('#zone_manage');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('manage')?>';
	var addurl = '<?php echo U('add')?>';
	var delurl = '<?php echo U('delete')?>';
	var editurl = '<?php echo U('edit')?>';
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
				{field:'name',title:'地区名称',width:'25%',align:'center'},
				{field:'region',title:'包含地区',width:'50%',align:'left',halign:'center'},
				{field:'none',title:'操作',width:'25%',halign:'center',align:'center',
					formatter:function(value,row,index){
						var edithtml = '<a href="javascript:void(0)" onclick="area_btn('+row.id+','+index+',\''+editurl+'\')" >编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var delhtml = '<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');$(\'#delrows\').trigger(\'click\')" href="javascript:void(0)">删除</a>';
						return edithtml + delhtml;
					}
				},
			]]
		});
		//添加
		$('#addrow').bind('click', function(){
			area_btn(0,0,addurl);
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
	});
	//编辑配送地区弹窗
	function area_btn(value,index,_url){
		dom.datagrid('clearSelections').datagrid('clearChecked');
		$('#area_box').window('open').window('refresh', _url+'&id='+value);
	}
</script>