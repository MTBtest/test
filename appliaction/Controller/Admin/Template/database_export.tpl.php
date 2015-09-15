<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">站点设置</a> > 数据库管理
	</div>
	<span class="line_white"></span>
	<!-- 应用列表 -->
	<div class="goods mt10">
		<div class="login mt10" style="border: none;">
			<table id="database_export" style="width:100%" class="easyui-datagrid" data-options="toolbar:toolbar,checkOnSelect:true,striped:true">
				<thead>
					<tr>
						<th data-options="field:'tablename',checkbox:true"></th>
						<th data-options="field:'name',halign:'center',align:'center', width:'30%'">表名</th>
						<th data-options="field:'rows',halign:'center',align:'center',width:'10%'">数据量</th>
						<th data-options="field:'length',halign:'center',align:'center',width:'10%'">数据大小</th>
						<th data-options="field:'create_time',halign:'center',align:'center',width:'30%'">创建时间</th>
						<th data-options="field:'none',halign:'center',align:'center',width:'20%'">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($list as $key => $table): ?>
					<tr>
						<td align="left"><?php echo $table['name']; ?></td>
						<td align="left"><?php echo $table['name']; ?></td>
						<td><?php echo $table['rows']; ?></td>
						<td> <?php echo format_bytes($table['data_length']); ?></td>
						<td><?php echo $table['create_time']; ?></td>
						<td class="action">
							<a class="ajax-get no-refresh" href="<?php echo U('optimize?tables='.$table['name']); ?>">优化表</a>&nbsp;
							<a class="ajax-get no-refresh" href="<?php echo U('repair?tables='.$table['name']); ?>">修复表</a>
						</td>
					</tr>
				  <?php endforeach ?>
				</tbody>
			</table>
		</div>
		<div class="clear"></div>
		<?php include $this->admin_tpl('copyright') ?>
	</div>
</div>
<script type="text/javascript">
	var dom = $('#database_export');
	var optimizeurl = '<?php echo U('optimize'); ?>'
	var repairurl = '<?php echo U('repair'); ?>'
	var backurl = '<?php echo U('export'); ?>'
	var toolbar = [
			{
				id:'backup',
				text:'备份',
				iconCls:'icon-backup',
				handler:function(){
					tables = gettables();
					if (tables.length > 0){
						post_ids(tables,backurl);
					}else{
						$.messager.alert('警告','请选择要操作的数据表');
						return false;
					}
				}
			},'-',
			{
				id:'optimize',
				text:'优化',
				iconCls:'icon-optimize',
				handler:function(){
					tables = gettables();
					if (tables.length > 0){
						ajax_ids(tables,optimizeurl);
					}else{
						$.messager.alert('警告','请选择要操作的数据表'); 
						return false;
					}
				}
			},'-',
			{
				id:'restore',
				text:'修复',
				iconCls:'icon-restore',
				handler:function(){
					tables = gettables();
					if (tables.length > 0){
						ajax_ids(tables,repairurl);
					}else{
						$.messager.alert('警告','请选择要操作的数据表'); 
						return false;
					}
				}
			},'-'
		];
	function gettables(){
		var tables = [];
		var rows = dom.datagrid('getChecked');
		for(var i=0; i<rows.length; i++){
			tables.push(rows[i].tablename);
		}
		return tables
	}
	function ajax_ids(ids,url){
		$.getJSON(url,
		{"id[]":ids},
		function(data){
			if(1 == data.status){// 删除成功，则需要在树中删除节点
				// 检修任务grid 执行load
				dom.datagrid("reload");  //重新加载
			}else{
				$.messager.alert('错误',data.info);
			}
		})
		dom.datagrid('clearSelections').datagrid('clearChecked');
	}
	function post_ids(ids,url){
		$.post(url,
		{"tables[]":ids},
		function(data){
			if(1 == data.status){// 删除成功，则需要在树中删除节点
				tables = data.tables;
				//$export.html(data.info + "开始备份，请不要关闭本页面！");
				backup(data.tab);
			}else{
				$.messager.alert('错误',data.info);
//					dom.datagrid('getRows')[0].info = '正常'; //第0行 列state的 设置字符串'正常'
//					dom.datagrid('updateRow',{index:0});//更新改行的数据
			}
		},"JSON")
		dom.datagrid('clearSelections').datagrid('clearChecked');
	}
	function backup(tab, status) {
		$.get(backurl, tab, function(data) {
			if (data.status) {
				//showmsg(tab.id, data.info);
				$.messager.progress({
					title: '备分数据库,请不要关闭窗口',
					msg: '请不要关闭窗口...',
					interval: 0  // disable auto update progress value
				});
				var bar = $.messager.progress('bar');
				if (!$.isPlainObject(data.tab)) {
					$.messager.progress('close');
					$.messager.alert('提示',data.info);
					//$export.parent().children().removeClass("disabled");
					//$export.html("备份完成，点击重新备份");
					window.onbeforeunload = function() {
						return null;
					}
					return;
				}else{
					bar.prev().text('正在处理 '+data.tab.table+' ...');
					bar.progressbar('setValue', data.rate);
				}
				backup(data.tab, tab.id != data.tab.id);
			} else {
				$.messager.progress('close');
				$.messager.alert('提示',data.info);
				//showmsg(tab.id, '备份完成！');
				//art.dialog({width: 320, time: 5, title: '温馨提示', content: data.info, ok: true});
				//$export.parent().children().removeClass("disabled");
				//$export.html("立即备份");
			}
		}, "json");
	}
	</script>