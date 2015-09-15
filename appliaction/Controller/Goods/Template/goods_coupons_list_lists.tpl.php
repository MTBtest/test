<?php include $this -> admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">内容管理</a> > 优惠券查看
	</div>
	<div class="line_white"></div>
	<div class="goods mt10">
		<div class="login mt10" style="border: none;">
			<table id="goods_coupons_list_lists" style="width:100%;"></table> 
		</div>
		<div class="clear"></div>
	</div>
	<?php include $this->admin_tpl('copyright') ?>
</div>
<script type="text/javascript">
	var dom = $('#goods_coupons_list_lists');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('lists')?>';
	var delurl = '<?php echo U('delete')?>';
	var listurl = '<?php echo U('GoodsCoupons/lists')?>';
	var statusurl = '<?php echo U('update?opt=status');?>';
	var delurl = '<?php echo U('update?opt=del');?>';
	var excelurl = '<?php echo U('update?opt=excel')?>';
	var status_arr = <?php echo json_encode($this->status_text)?>;
	var cid = <?php echo $cid?>;
	var pagenum = <?php echo $pagenum?>;
	var rowsnum = <?php echo $rowsnum?>;
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
				id:'excel',
				text:'导出',
				iconCls:'icon-import',
			},'-',
			{
				id:'backlist',
				text:'返回',
				iconCls:'icon-undo',
			},'-'
		],
		frozenColumns:[[
			{field:'id',checkbox:true}
		]],
		queryParams:{
			id:cid
		},
		pagination:true,
		pageSize:pageSize,
		pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表 
		columns:[[
			{field:'sn',title:'优惠劵序列号',width:'37%',halign:'center',align:'center'},
			{field:'value',title:'优惠劵面值',width:'10%',halign:'center',align:'center',sortable:true},
			{field:'start_time',title:'优惠劵有效期',width:'15%',align:'center',fixed:true,
				formatter:function(value,row,index){
					return $.fn.datebox.defaults.timeformat(value)+'~'+$.fn.datebox.defaults.timeformat(row.end_time);
				}	
			},
			{field:'user_name',title:'所属会员',width:'10%',halign:'center',align:'center'},
			{field:'status',title:'状态',width:'10%',align:'center',fixed:true,sortable:true,
				 formatter:function(value,row,index){
				 	try{
				 		status_text = status_arr[value];
				 	}catch(e){
				 		status_text = '-';
				 	}
					return status_text;  
				}	
			},
			{field:'none',title:'操作',width:'15%',halign:'center',align:'center',
				formatter:function(value,row,index){
					var sendhtml = '<a href="'+statusurl+'&val=1&id='+row.id+'">发放</a>&nbsp;&nbsp;&nbsp;&nbsp;';
					var disablehtml = '<a href="'+statusurl+'&val=3&id='+row.id+'">禁用</a>&nbsp;&nbsp;&nbsp;&nbsp;';
					var usehtml = '<a href="'+statusurl+'&val=2&id='+row.id+'">使用</a>&nbsp;&nbsp;&nbsp;&nbsp;';
					var delhtml = '<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');$(\'#delrows\').trigger(\'click\')" href="#">删除</a>';
					var view = '';
					var value = row.status;
					if(value == 0){
						view = sendhtml+disablehtml;
					}
					if(value == 1){
						view = usehtml+disablehtml;
					}
					view += delhtml;
					return view
				}
			},
		]]	
	})
	//返回列表
	$('#backlist').bind('click', function(){
		window.location.href=listurl;
	})
	//生成excel
	$('#excel').bind('click', function(){
		window.location.href=excelurl+'&id='+cid+'&pagenum='+pagenum+'&rowsnum='+rowsnum;
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
</script>