<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">

<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>

<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content" >
<div class="site">
	Haidao Board <a href="#">站点设置</a> > 发货单模板编辑
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
				{field:'id',checkbox:true}
			]],
			pagination:true,
			pageSize:pageSize,
			pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表 
			columns:[[
				{field:'parcel_name',title:'模板名称',width:'40%',align:'center',resizable:false,editor: { type: 'text', options: { required: true}}},
				{field:'status',title:'模板状态',width:'30%',halign:'center',align:'center',resizable:false, 
					formatter:function(value, row, index) {
						if(row.status == 1) {
							return '启用';
						} else {
							return '禁用 ';
						}
					}
				},	
				{field:'none',title:'操作',width:'30%',halign:'center',align:'center',resizable:false,
					formatter:function(value,row,index){                        
                        return "<a href='javascript:void(0)' onclick=\"edit('"+ row.id +"');\">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:;' onclick=\"del('"+ row.id +"');\">删除</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:;' onclick=\"set('"+ row.id +"');\">启用</a>";						
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
				ids.push(rows[i].id);
			}
			if (ids.length > 0){
				$.messager.confirm('确认','您确认想要删除记录吗？',function(r){	
					if (r){
						$.post(delurl,{"id[]":ids}, function(data){
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
        window.location.href = '?m=admin&c=printParcel&a=edit&id=' + id;
    }
    
    function del(id) {
        window.location.href = '?m=admin&c=printParcel&a=delete&id=' + id;
    }
     function set(id) {
        window.location.href = '?m=admin&c=printParcel&a=set&id=' + id;
    }
</script>
