<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">站点设置</a> &gt; 数据库恢复
	</div>
	<div class="line_white"></div>
	<div class="goods mt10">
		<div class="login mt10" style="border: none;">
			<table id="database_import" style="width:100%" class="easyui-datagrid" data-options="toolbar:toolbar,checkOnSelect:true">
				<thead>
					<tr>
						<th data-options="field:'time',halign:'center',align:'center', width:'25%'">备份名称</th>
						<th data-options="field:'part',halign:'center',align:'center', width:'15%'">卷数</th>
						<th data-options="field:'compress',halign:'center',align:'center', width:'10%'">压缩</th>
						<th data-options="field:'size',halign:'center',align:'center', width:'10%'">数据大小</th>
						<th data-options="field:'key',halign:'center',align:'center', width:'15%'">备份时间</th>
						<th data-options="field:'status',halign:'center',align:'center', width:'10%'">状态</th>
						<th data-options="field:'none',halign:'center',align:'center', width:'15%'">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($list as $key => $data): ?>
					<tr>
						<td><?php echo date('Ymd-His',$data['time']); ?></td>
						<td><?php echo $data['part'] ?></td>
						<td><?php echo $data['compress'] ?></td>
						<td><?php echo format_bytes($data['size']); ?></td>
						<td><?php echo $key;?></td>
						<td>-</td>
						<td class="action">
							<a class="db-import" onclick="db_import('<?php echo $data['time']?>',null,null)">还原</a>&nbsp;
							<a class="ajax-get confirm" href="<?php echo U('del?time='.$data['time']); ?>">删除</a>
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
<!--表格js-->
<script type="text/javascript">
	var dom = $('#database_import');
	var importurl = '<?php echo U('import')?>';
	var	toolbar = [{
					id:'delrows',
					text:'删除',
					iconCls:'icon-del',
				}];
	function db_import(time,part,start){
		var rowIndex = dom.datagrid('getRowIndex',dom.datagrid('getSelected'));
		$.getJSON(importurl,{"time":time,"part":part,"start":start},function(data){
			if (data.status) {
				if (data.gz) {
					data.info += status;
					if (status.length === 5) {
						status = ".";
					} else {
						status += ".";
					}
				}
				if (data.status.part) {
					db_import(time,data.status.part,data.status.start);
				} else {
					window.onbeforeunload = function() {
						return null;
					}
				}
			} else {
				//updateAlert(data.info,'alert-error');
				art.dialog({width: 320, time: 5, title: '温馨提示', content: data.info, ok: true});
			}
		})
	}
</script>
