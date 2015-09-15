<?php include $this -> admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
	<div class="content">
		<div class="site">
			Haidao Board <a href="#">运营推广</a> > 优惠券管理
		</div>
		<div class="line_white"></div>
		<div class="goods mt10">
			<div class="login mt10" style="border: none;">
				<table id="goods_coupons_list" style="width:100%"></table> 
				<div class="clear"></div>
			</div>
			<?php include $this->admin_tpl("copyright"); ?>
			<div class="clear"></div>
		</div>
	</div>
	<!--弹窗-->
	<div id="createpanel" class="easyui-window"  style="width:320px;" closed="true" modal="true" minimizable="false" maximizable="false" collapsible="false" data-options="title:'生成优惠劵'">
		<form id="SetPoinosForm" class="setpoinosform" method="post" action="<?php echo U('GoodsCouponsList/update?opt=add'); ?>">
		<div class="login " style="border: none;">
			<div class="table">
				<table > 
				<tr style="border-top: none;background: #FFFFFF;">
					<td>请输入生成优惠券数量(1-5000)：</td>
				</tr>
				<tr style="border-bottom:1px solid #CCCCCC;border-top: none;">
					<td align="center" colspan="2">
						<input type="text" name="num" id="num" value=""  style="margin:0;" class="input_ss easyui-numberbox" min="1" max="5000" data-options="required:true,validType:'minLength[5]'">
						<input type="hidden" name="coupons_id" id="coupons_id" value="" />
					</td>
				</tr>
				</table>
			</div>
		</div>
		<div data-options="region:'south',border:false" style="text-align:right;padding:5px 20px;">
			<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="$('#SetPoinosForm').submit()" style="padding: 0 10px;">确定</a>
			<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="$('#createpanel').window('close');" style="padding: 0 10px;">取消</a>
		</div>
		</form>
	</div>
<!--表格js-->
<script type="text/javascript">
	var dom = $('#goods_coupons_list');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('lists')?>';
	var addurl = '<?php echo U('update',array('opt'=>'add'))?>';
	var editurl = '<?php echo U('update',array('opt'=>'edit'))?>';
	var delurl = '<?php echo U('update',array('opt'=>'del'))?>';
	var listurl = '<?php echo U('GoodsCouponsList/lists')?>';
	var createurl = '<?php echo U('edit')?>';
	var CouponsCount = 0;
	$(function(){
		dom.datagrid({
			url:dataurl,
			striped:true,
			checkOnSelect:true,
			fitColumns:true,
			toolbar:[
						{
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
				{field:'name',title:'促销名称',width:'10%',align:'center',sortable:true},	
				{field:'value',title:'金额',width:'10%',align:'center',sortable:true},	
				{field:'nums',title:'数量',width:'5%',align:'center',sortable:true},
				{field:'integral',title:'兑换积分',width:'5%',align:'center',sortable:true},
				{field:'limit',title:'使用规则',width:'10%',align:'center',sortable:true},
				{field:'start_time',title:'使用时间',width:'30%',align:'center',
					formatter:function(value,row,index){
  						return $.fn.datebox.defaults.timeformat(value)+'~'+$.fn.datebox.defaults.timeformat(row.end_time);
					}	
				},
				{field:'descript',title:'促销说明',width:'10%',align:'center',sortable:true},
				{field:'none',title:'操作',width:'20%',align:'center',
					formatter:function(value,row,index){
						sptext = '&nbsp;&nbsp;&nbsp;&nbsp;';
						createhtml = '<a href="javascript:void(0)" onclick="cteatepanel('+row.id+','+index+')">生成</a>';
						listhtml = '<a href="'+listurl+'&id='+row.id+'">详细</a>';
						edithtml = '<a href="'+editurl+'&id='+row.id+'">编辑</a>';
						delhtml = '<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');$(\'#delrows\').trigger(\'click\')" href="#">删除</a>';
						return createhtml+sptext+listhtml+sptext+edithtml+sptext+delhtml;
						}
				},
			]]	
		});
		//添加
		$('#addrow').bind('click', function(){
			window.location.href=addurl;
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
	})
	//积分弹窗
	function cteatepanel(value,index){
		$('input[name="coupons_id"]').val(value);
		$('#createpanel').window('open');
	}
</script>