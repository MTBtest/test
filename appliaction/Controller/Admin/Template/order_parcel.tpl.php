<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">

<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>

<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>

<div class="content" >
<div class="site">
	Haidao Board <a href="#">站点设置</a> > 发货单列表
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
   <div id="changestatus" class="easyui-window"  style="width:320px;" closed="true" modal="true" minimizable="false" maximizable="false" collapsible="false" data-options="title:'更改配送状态'">
		<form id="SetStatusForm" class="setmoneyform" method="post" action="<?php echo U('updstatus'); ?>">
		<div class="login " style="border: none;">
			<div class="table">
				<table>
				<tr style="border-top: none;">
					<td>配送状态</td>
					<td align="left" width="33%"><input type="radio" value="0" name="status" checked>待配送<input type="radio" value="1" name="status" >配送中 <input type="radio" value="2" name="status">配送完成</td>
					</tr>
				<tr style="border-bottom:1px solid #CCCCCC;border-top: none;">
					<td>变更日志</td>
					<td align="left" colspan="2"><input type="text" value="" style="margin:0;" class="input_ss" name="msg" ></td>
				</tr>
				</table>
			</div>
		</div>
		<div data-options="region:'south',border:false" style="text-align:right;padding:5px 20px;">
			<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="$('#SetStatusForm').submit()" style="padding: 0 10px;">确定</a>
			<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="$('#changestatus').window('close');" style="padding: 0 10px;">取消</a>
		</div>
			<input type="hidden" name="id" value="" />
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
			singleSelect:false,
			fitColumns:true,
            fixColumnSize:true,
			pagination:true,
			pageSize:pageSize,
			pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表 
			columns:[[
				{field:'accept_name',title:'收货人姓名',width:'15%',align:'center',resizable:false,editor: { type: 'text', options: { required: true}}},
				{field:'order_sn',title:'订单号',width:'20%',halign:'center',align:'center',resizable:false},	
				{field:'address',title:'联系地址',width:'30%',halign:'center',align:'center',resizable:false,},	
				{field:'total_number',title:'购买数量',width:'10%',halign:'center',align:'center',resizable:false,},	
				{field:'status',title:'配送状态',width:'10%',halign:'center',align:'center',resizable:false,
					formatter:function(value,row,index){                        
                        switch(row.status) {
							case '0':return "待配送";	
							case '1':return "配送中";	
							case '2':return "配送完成";	
						}			
					}
				},
				{field:'none',title:'操作',width:'15%',halign:'center',align:'center',resizable:false,
					formatter:function(value,row,index){
					    var sp_txt="&nbsp;&nbsp;&nbsp;&nbsp;";                        
                        return '<a  href="javascript:void (0)" onclick="prints('+row.id+')">打印</a>'+sp_txt+
							'<a  href="javascript:void (0)" onclick="changeStatus('+row.id+')">更改状态</a>'+sp_txt+
							'<a  href="javascript:void (0)" onclick="see(\''+(row.order_sn)+'\')">查看日志</a>';
					}
				}
			]]
		});
		//添加
		
		//删除操作
		
	});
    
    function prints(id) {
        window.location.href = '?m=admin&c=parcel&a=prints&id=' + id;
    }
    function changeStatus(value,index){
    	  $('input[name="id"]').val(value);
		$('#changestatus').window('open');
       }
		
       function see(order_sn){
       	$.getJSON('?m=admin&c=parcel&a=view_log', {
				order_sn:order_sn,
				}, function(ret) {
				if(ret.status == 1) {
					var _html = '<table>'
					$.each(ret.data, function(i, n) {
						_html += '<tr class="order-win">';
						_html += '<td class="order-win1">'+ n.user_name +'</td>';
						_html += '<td>'+ n.action +'</td>';
						_html += '<td>'+ n.msg +'</td>';
						_html += '<td>'+ n.clientip +'</td>';
						_html += '<td>'+ n.dateline +'</td>';
						_html += '</tr>'
					});
					_html += '</table>';
					art.dialog({
						id:'view_log',
						title:'配送操作日志',
						fixed:true,
						lock:true,
						content:_html,
						ok:true
					});
				} else {
					alert(ret.info);
					return false;
				}
			})
		}
    
</script>
