<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content" >
<div class="site">
	Haidao Board <a href="#">站点设置</a> > 快递单打印模板管理
</div>
<span class="line_white"></span>
<div class="install mt10">
	<div class="login mt10" style="border: none;">
			<table id="adminuser" style="width:100%"></table>
			<div class="clear"></div>
		</div>
		<?php include $this->admin_tpl("copyright"); ?>
		<div class="clear"></div>
</div>
<script type="text/javascript">
	var dom = $('#adminuser');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('index')?>';
	var addurl = '<?php echo U('add')?>';
	var delurl = '<?php echo U('delete')?>';
	var repwdurl = '<?php echo U('reuserpwd')?>';
	var ststusurl = '<?php echo U('changeStatus')?>';
	$(function(){
		dom.datagrid({
			url:dataurl,
			striped:true,
			width:'100%',
			checkOnSelect:true,
			singleSelect:false,
			fitColumns:true,
            fixColumnSize:true,
			toolbar:[
				{
					id:'delrow',
					text:'删除',
					iconCls:'icon-del',
				},'-',
				{
					id:'addrow',
					text:'添加',
					iconCls:'icon-add',
				},
			],
			frozenColumns:[[
				{field:'delivery_id',checkbox:true}
			]],
			pagination:true,
			pageSize:pageSize,
			pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表
			columns:[[
				{field:'name',title:'模板名称',width:'20%',align:'center',resizable:false,editor: { type: 'text', options: { required: true}}},
				{field:'description',title:'模板描述',width:'45%',halign:'center',align:'left',resizable:false},
				{field:'status',title:'启用状态',width:'10%',halign:'center',align:'center',resizable:false,
					formatter:function(value, row, index) {
						if(row.status == 1) {
							return '启用';
						} else {
							return '禁用';
						}
					}
				},
				{field:'dateline',title:'更新时间',width:'15%',align:'center',sortable:true,resizable:false,
					formatter:function(value,row,index){
  						return $.fn.datebox.defaults.timeformat(value);
					}
				},
				{field:'none',title:'操作',width:'10%',halign:'center',align:'center',resizable:false,
					formatter:function(value,row,index){
                        return "<a href='javascript:void(0)' onclick=\"edit('"+ row.delivery_id +"');\">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:;' onclick=\"del('"+ row.delivery_id +"');\">删除</a>";
					}
				}
			]]
		});
		//添加
		$('#addrow').bind('click', function(){
			window.location.href=addurl;
		})
		//删除操作
		$('#delrow').bind('click', function(){
			var ids = [];
			var rows = dom.datagrid('getChecked');
			for(var i=0; i<rows.length; i++){
				ids.push(rows[i].delivery_id);
			}
			if (ids.length > 0){
				$.messager.confirm('确认','您确认想要删除记录吗？',function(r){
					if (r){
						$.post(delurl,{"delivery_id[]":ids}, function(data){
							if(1 == data.status){// 删除成功，则需要在树中删除节点
								// 检修任务grid 执行load
								location.reload();
							}else{
								$.messager.alert('警告',data.info);
							}
						}, 'JSON');
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
    function edit(id) {
        window.location.href = '?m=admin&c=print_delivery&a=edit&delivery_id=' + id;
    }
    function del(id) {
        window.location.href = '?m=admin&c=print_delivery&a=delete&delivery_id=' + id;
    }
</script>
