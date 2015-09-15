<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<?php echo jsfile('hddate');?>
<?php echo jsfile('hdvalid');?>
<script>
$(function() {
	var start = {
	    elem:'#start',
	    format:'YYYY-MM-DD',
	    //min:laydate.now(), //设定最小日期为当前日期
	    max:'2099-06-16 23:59:59', //最大日期
	    istime:false,
	    istoday:true,
	    choose:function(datas){
	         end.min = datas; //开始日选好后，重置结束日的最小日期
	         end.start = datas //将结束日的初始值设定为开始日
	    }
	};
	var end = {
	    elem:'#end',
	    format:'YYYY-MM-DD',
	    min:laydate.now(),
	    max:'2099-06-16 23:59:59',
	    istime:false,
	    istoday:true,
	    choose:function(datas){
	        start.max = datas; //结束日选好后，重置开始日的最大日期
	    }
	};
	laydate(start);
	laydate(end);
});
</script>
<div class="content">
	<div class="site">Haidao Board <a href="#">站点设置</a> > 管理团队日志</div>
	<span class="line_white"></span>
	<div class="install mt10">
		<div class="guanli">
				<span style="margin-right:10px;">按管理员查看</span>
					<select name='user_id' id='user_id' class="easyui-combobox" data-options="editable:false" style="height:26px;"></select>
				<span style="margin-right:10px;">按时间查看</span>
				  <span>按时间查看</span><div class="day fl" style="margin-top:2px;">
				  	<input type="text" id="start" class="time_input" style="height:24px;" name="stime" value="<?php echo $stime; ?>" /><em>~</em>
				  	<input type="text" id="end" style="height:24px;" class="time_input" name="etime" value="<?php echo $etime; ?>" /></div>
				<a id="search" href="#" class="easyui-linkbutton" style="height:26px;padding-right:10px;">查询</a>
		</div>
		<div class="login mt10" style="border:none;">
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
	var delurl = '<?php echo U('delete')?>';
	var clearurl = '<?php echo U('clear')?>';
	var user_arr = <?php echo json_encode($user_arr)?>;
	user_arr.unshift({"id":"0","name":"请选择"});
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
					id:'clearrows',
					text:'清空',
					iconCls:'icon-empty',
				},'-'
			],
			pagination:true,
			pageSize:pageSize,
			pageList:[pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表
			columns:[[
				{field:'id',checkbox:true},
				{field:'admin_name',title:'操作者',width:'10%',align:'center'},
				{field:'remark',title:'操作记录',width:'40%',halign:'center',align:'center'},
				{field:'dateline',title:'操作时间',width:'10%',align:'center',
					formatter:function(value,row,index){
  						return $.fn.datebox.defaults.timeformat(value);
					}
				},
				{field:'url',title:'操作地址',width:'30%',align:'center',halign:'center'},
				{field:'action_ip',title:'IP地址',width:'10%',halign:'center',align:'center'},
			]],
			onLoadSuccess:function(data){
				$('#user_id').combobox({
					data:user_arr,
					valueField:'id',
					textField:'name'
				});
				$('#user_id').combobox('setValue',data.search.s_user_id);
			}
		});
		//增加查询参数，重新加载表格
		$('#search').bind('click',function (){
			var queryParams = dom.datagrid('options').queryParams;
			//查询参数直接添加在queryParams中
			queryParams.user_id = $('#user_id').combobox('getValue');
			queryParams.stime = $('[name=stime]').val();
			queryParams.etime = $('[name=etime]').val();
			dom.datagrid('options').queryParams = queryParams;
			dom.datagrid('reload');
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
		//清空日志
		$('#clearrows').bind('click', function(){
			$.messager.confirm('确认','您确认想要清空记录吗？',function(r){
				if (r){
					$.messager.progress();
					$.getJSON(clearurl,
					function(data){
						$.messager.progress('close');
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
		});
	});
</script>