<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<style type="text/css">
	input{margin-top: 0px;}
	.text_input{margin-right: 0px;}
	.tree-title{height: 26px;}
	.table a{padding: 0px;}
</style>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">内容管理</a> > 站点帮助
	</div>
	<span class="line_white"></span>
	<div class="login mt10" style="border: none;">
		<table id="article_help_lists" style="width:100%;height:auto"></table>
	</div>
	<div class="clear"></div>
	<?php include $this->admin_tpl('copyright') ?>
</div>
<!--弹窗-->
<div id="add_help_list" class="easyui-window"  style="width:400px;" closed="true" modal="true" minimizable="false" maximizable="false" collapsible="false" data-options="title:'添加主题'">
		<form id="updateForm" name="updateForm" class="setpoinosform" method="post" >
		<div class="login " style="border: none;">
			<!--<h3>设置积分</h3>-->
			<div class="table">
				<table>
				<tr style="border-top: none;height: 40px !important;">
					<td align="right">帮助主题：</td>
					<td align="left"><input type="text" name="title" placeholder="输入主题"  style="margin:0;" class="input_ss easyui-validatebox" data-options="required:true"  ></td>
				</tr>
				<tr style="border-top: none;">
					<td align="right">排序：</td>
					<td align="left">
						<input name="sort" class="easyui-numberspinner sort" style="width:100px;" required="required" ata-options="min:0,editable:true" value="100">
					</td>
				</tr>
				<tr style="border-bottom:1px solid #CCCCCC;border-top: none;">
					<td align="right">是否显示：</td>
					<td align="left"><input type="checkbox" name="status" value="1" checked="" /></td>
				</tr>
				</table>
			</div>
		</div>
		<div data-options="region:'south',border:false" style="text-align:right;padding:5px 20px;">
			<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="save()" style="padding: 0 10px;">确定</a>
			<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="$('#add_help_list').window('close');" style="padding: 0 10px;">取消</a>
		</div>
			<input type="hidden" name="fpid" value="" />
		</form>
	</div>
<script type="text/javascript">
	var dom = $('#article_help_lists');
	var dataurl = '<?php echo U('help_child')?>';
	var editurl = '<?php echo U('update?opt=edit')?>';
	var saveurl = '<?php echo U('update')?>';
	var delurl = '<?php echo U('update?opt=del')?>';
	var statusurl = '<?php echo U('update',array('opt'=>'ajax_status'))?>';
	var editRow = undefined;
	$(function(){
		dom.treegrid({
			url:dataurl,
			idField:'id',
			treeField:'title',
			striped:true,
			fitColumns:true,
			toolbar:[
				{
					id:'addrows',
					text:'添加',
					iconCls:'icon-add',
					handler:function(){	
						add_help_list(0);
					}
				},'-',
				{
					id:'saverows',
					text:'保存',
					iconCls:'icon-backup',
					handler:function(){
						flag = dom.treegrid('validateRow', editRow);
						if(flag){
							dom.treegrid('endEdit', editRow);
							rows = dom.treegrid('getChanges');
							if (rows.length > 0){
								var rowstr = JSON.stringify(rows);
								$.post(saveurl, {data:rowstr}, function (data){
									//dom.treegrid("reload");	//重新加载
								});
							}
						}
					}
				},'-'
			],
			columns:[[
				{field:'title',title:'帮助主题',width:'40%',halign:'center',align:'left',editor:{type:'validatebox', options:{required:true}}},
				{field:'sort',title:'排序',width:'20%',halign:'center',align:'center',editor:{type:'numberspinner', options:{required:true}}},
				{field:'status',title:'是否显示',width:'20%',halign:'center',align:'center',editor:{type:'checkbox',options:{on:'1',off:'0'}},
					formatter:function(value,row,index){
						if(value ==1){
							statustext = '<span url="'+statusurl+'&id='+row.id+'" class="ajax-get ajax_on" ></span>'
						}else{
							statustext = '<span url="'+statusurl+'&id='+row.id+'" class="ajax-get ajax_off" ></span>'
						}
						return statustext;
					}
				},
				{field:'none',title:'操作',width:'21%',halign:'center',align:'center',
					formatter:function(value,row,index){
						var level = dom.treegrid('getLevel',row.fpid);
						spacetext = '&nbsp;&nbsp;&nbsp;&nbsp;';
						addhtml = '<a href="javascript:void (0)" onclick="add_help_list('+row.id+')">添加</a>';
						edithtml = '<a href="javascript:void(0)" onclick="dom.treegrid(\'select\', '+row.id+');editrow('+row.id+')">编辑</a>';
						delhtml = '<a href="javascript:void (0)" onclick="dom.treegrid(\'select\', '+row.id+');delrows('+row.id+')">删除</a>';
						detailhtml = '<a href="'+editurl+'&id='+row.id+'" >详细</a>'
						if(level == 0){
							return addhtml + spacetext + detailhtml + spacetext + edithtml + spacetext + delhtml ;
						}
						else{
							return  detailhtml + spacetext + edithtml + spacetext + delhtml ;
						}
					}
				}
			]],
			onLoadSuccess:function(data){
				dom.treegrid('expandAll');
			},
			onAfterEdit: function (rowIndex, rowData, changes) {
				editRow = undefined;
			}
		});
	});
	//编辑
	function editrow(index){
		if (editRow != undefined){
			dom.treegrid('endEdit', editRow);
		}
		var row = dom.treegrid('getSelected');
		editRow = row.id;
		dom.treegrid('beginEdit', editRow);
	}
	//删除
	function delrows(index){
		var row = dom.treegrid('getSelected');
		$.messager.confirm('确认','您确认想要删除记录吗？',function(r){
			if (r){
				$.getJSON(delurl,
				{"id":index},
				function(data){
					if(1 == data.status){// 删除成功，则需要在树中删除节点
						try{
							dom.treegrid('remove', row.id).treegrid('reload', row.fpid);
						}catch(err){};
					}else{
						$.messager.alert('警告',data.info);
					}
				})
			}else{
				dom.treegrid('clearSelections').treegrid('clearChecked');
			}
		});
	}
	//添加主题弹窗
	function add_help_list(fpid){
		$('#updateForm').form('reset');
		$('#add_help_list').window('open');
		$('input[name="fpid"]').val(fpid);
		$(".window-mask").css({ height: $(document).height()}).click(function(){
			$('#add_help_list').window('close');
		});
		$('#add_help_list').window('center');
	}
	function save(){
		var fpid = $('input[name="fpid"]').val();
		$.messager.progress();
		$('#updateForm').form('submit', {
			url:saveurl,
			onSubmit: function(){
				var isValid = $(this).form('validate');
				if (!isValid){
					$.messager.progress('close');	// 如果表单是无效的则隐藏进度条
				}
				return isValid;
			},
			success:function(data){
				$('#add_help_list').window('close');
				try{
					//添加一条空的再刷新 如果是根的时候不添加空的无法刷新节点
					dom.treegrid('append',{parent: fpid,data: []})
					dom.treegrid('reload', fpid);
				}catch(err){
					dom.treegrid('reload');
				}
				$.messager.progress('close');
			}
		});
	}
</script>