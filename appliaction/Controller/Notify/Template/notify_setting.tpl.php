<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">站点设置</a> > 通知系统设置
	</div>
	<span class="line_white"></span>
	<div class="install mt10">
		<div class="login mt10" style="border: none;">
			<table id="pay_manage" style="width:100%" class="easyui-datagrid" data-options="striped:true">
				<thead>
					<tr>
						<th data-options="field:'pay_code',halign:'center',align:'center', width:'11%'">图标</th>
						<th data-options="field:'pay_name',halign:'center',align:'center',width:'10%'">名称</th>
						<th data-options="field:'enabled',halign:'center',align:'center',width:'10%'">状态</th>
						<th data-options="field:'pay_desc',halign:'center',width:'50%'">描述</th>
						<th data-options="field:'none',halign:'center',align:'center',width:'20%'">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($notifys as $code => $notify): ?>
					<tr>
						<td><img src="<?php echo IMG_PATH.'notify_'.$notify['code']; ?>.png" title="<?php echo $notify['name'] ?>" height="27"/></td>
						<td><?php echo $notify['name'] ?></td>
						<td><?php echo ($notify['enabled'] == 1) ? '开启' : '关闭'; ?></td>
						<td style="text-align: left;text-indent: 50px;"><?php echo $notify['description'] ?></td>
						<td>
							<?php if ($notify['file_exists'] == 1) { ?>
								<font color="red">即将推出，敬请期待...</font>
							<?php }else if (empty($notify['dateline'])) { ?>
								<a href="<?php echo U('config', array('code' => $notify['code'])) ?>"><font color="red">安装此接口</font></a>
							<?php }else{ ?>
								<a href="<?php echo U('config', array('code' => $notify['code'])) ?>">配置&nbsp;&nbsp;</a>
								<a href="<?php echo U('delete', array('code' => $notify['code'])) ?>">卸载</a>
							<?php } ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div class="clear"></div>
		</div>
		<?php include $this->admin_tpl("copyright"); ?>
		<div class="clear"></div>
	</div>