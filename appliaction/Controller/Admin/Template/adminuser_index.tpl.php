<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content" >
<div class="site">
	Haidao Board <a href="#">站点设置</a> > 后台管理团队
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
<!--设置权限组弹窗-->
<div id="gropulist" class="easyui-dialog" style="display: block;width:400px;" modal="true" minimizable="false" maximizable="false" collapsible="false" data-options="title:'设置权限组',
	closed:true,
	buttons:[{
		text:'保存',
			handler:function(){
				$('#SetForm').submit();
			}
		},{
			text:'关闭',
			handler:function(){
				try{
					$('#gropulist').window('close');
				}catch(e){
				}
			}
		}]">
	<form class="setform" id="SetForm" method="post" action="<?php echo U('setgroup'); ?>">
		<div class="login " style="border: none;">
			<div class="table">
				<table>
					<tr style="border-top:none;">
						<th>选择</th>
						<th>用户组</th>
						<th>描述</th>
					</tr>
					<?php if($group_list):?>
					<?php foreach ($group_list as $key => $vo) { ?>
						<tr>
							<td><input type="radio" class="auth_rules" id="ids[]" name="ids[]" value="<?php echo $vo['id'] ?>"/></td>
							<td><?php echo $vo['title']; ?></td>
							<td><?php echo $vo['description']; ?></td>
						</tr>
					<?php } ?>
					<?php else:?>
						<tr>
							<td colspan='3'>请先设置权限组别</td>
						</tr>
					<?php endif;?>
				</table>
			</div>
		</div>
		<input type="hidden" name="user_id" />
	</form>
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
			singleSelect:true,
			fitColumns:true,
			toolbar:[
				{
					id:'addrow',
					text:'添加',
					iconCls:'icon-add',
				},'-'
			],
			pagination:true,
			pageSize:pageSize,
			pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表
			columns:[[
				{field:'name',title:'用户名',width:'20%',align:'center'},
				{field:'group_id',title:'所属分组',width:'20%',halign:'center',align:'center',sortable:true,
					formatter:function(value,row,index){
						var group_text = '';
						if(row.id == 1){
							group_text = '超级管理员';
						}else{
							if (value == 0) {
								group_text = '请设置管理权限';
							}else{
								group_text = row.group_name;
							}
						}
  						return group_text;
					}
				},
				{field:'last_login',title:'最后登录时间',width:'30%',align:'center',
					formatter:function(value,row,index){
  						return $.fn.datebox.defaults.timeformat(value);
					}
				},
				{field:'login_num',title:'共计登录次数',width:'10%',align:'center',halign:'center'},
				{field:'none',title:'操作',width:'21%',halign:'center',align:'center',
					formatter:function(value,row,index){
						var resettml = '<a href="'+repwdurl+'&id='+row.id+'">重置密码</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var setgrouphtml = '<a href="javascript:void(0)" onclick="set_group('+row.id+','+row.group_id+')">设置权限组</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						if(row.status == 1){
							var banhtml = '<a href="javascript:void(0)" onclick="chang_status(\'forbidGroup\','+row.id+')">禁用</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						}else{
							var banhtml = '<a href="javascript:void(0)" onclick="chang_status(\'resumeGroup\','+row.id+')">启用</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						}
						var delhtml = '<a href="javascript:void(0)" onclick="chang_status(\'deletegroup\','+row.id+')">删除</a>';
						if (row.id > 1){
							return resettml + setgrouphtml + banhtml + delhtml;
						}else{
							return '管理员不允许操作';
						}
					}
				},
			]],
		});
		//添加
		$('#addrow').bind('click', function(){
			window.location.href=addurl;
		})
	});
	//弹窗
	function set_group(value,group_id){
		$("input[name='ids[]'][value=" + group_id + "]").attr("checked", true);
		$('input[name="user_id"]').val(value);
		$('#gropulist').window('open');
	}
	//状态
	function chang_status(method,id){
		$.messager.confirm('确认','您确认想要执行操作吗？',function(r){
			if (r){
				$.getJSON(ststusurl,
				{"method":method,"id":id},
				function(data){
					if(1 == data.status){
						dom.datagrid("reload");  //重新加载
					}else{
						$.messager.alert('提示',data.info);
					}
				})
			}
		});
	}
</script>
