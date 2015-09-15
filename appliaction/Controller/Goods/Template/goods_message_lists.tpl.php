<?php  include  $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">商品管理</a> > 商品到货通知
	</div>
	<span class="line_white"></span>
		<div class="goods mt10">
			<dl class="mt10">
				<dt>
					<p>
						<a href="<?php echo U('lists?status=0')?>" <?php if ($status == 0) { echo "class='hover'"; }; ?>">尚未处理</a>
						<a href="<?php echo U('lists?status=1')?>" <?php if ($status == 1) { echo "class='hover'"; }; ?>">已经处理</a>
					</p>
				</dt>
				<dd>
					<div class="login mt10" style="border: none;">
						<table id="goods_message_list" style="width:100%;"></table> 
					</div>
					<div class="clear"></div>
					<div class="submit fl"></div>
				</dd>
			</dl>
		<?php include $this->admin_tpl('copyright') ?>
	</div>
</div>
<script>
	//批量发送邮件
	function send_mail(goods_id,product_id) {
		$.messager.confirm("发送到货邮件通知", '发送邮件给所有登记该商品并填写邮箱的用户。如果邮件较多，请使用专业软件发送!', function (r) {
			if (r) {
				var url = "<?php echo U('Goods/GoodsMessage/send_mail') ?>";
				$.messager.progress();
				$.getJSON(url, {"goods_id": goods_id,"product_id": product_id}, function(data) {
					$.messager.progress('close');
					$.messager.alert('提示',data.info);
				})
				return true;
			}
    	});
    	return false;
	}
	//批量发送站内信
	function send_letter(goods_id,product_id) {
			$.messager.confirm("发送到货站内信通知", '发送站内信给所有登记该商品的会员，确认发送站内信通知？', function (r) {
			if (r) {
				var url = "<?php echo U('Goods/GoodsMessage/send_letter') ?>";
				$.messager.progress();
				$.getJSON(url, {"goods_id": goods_id,"product_id": product_id}, function(data) {
					$.messager.progress('close');
					$.messager.alert('提示',data.info);
				})
				return true;
			}
    	});
    	return false;
	}
</script>
<!--表格js-->
	<script type="text/javascript">
	var dom = $('#goods_message_list');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('lists')?>';
	var detailurl = '<?php echo U('detail')?>';
	var delurl = '<?php echo U('update?opt=del')?>';
	var status = <?php echo $status?>;
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
				},'-'
			],
			frozenColumns:[[
				{field:'id',checkbox:true}
			]],
			queryParams:{
				status:status
			},
			scrollbarSize:0,
			pagination:true,
			pageSize:pageSize,
			pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表 
			columns:[[
				{field:'goods_name',title:'缺货商品名称',width:'47%',halign:'center',align:'left'},
				{field:'count',title:'订单数量',width:'10%',halign:'center',align:'center'},
				{field:'goods_num',title:'库存数量',width:'10%',halign:'center',align:'center'},
				{field:'none',title:'操作',width:'30%',halign:'center',align:'center',
					formatter:function(value,row,index){
						var viewthtml = '<a href="'+detailurl+'&goods_id='+row.goods_id+'&product_id='+row.product_id+'&status='+status+'">详细</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var sendemailthtml = '<a href="javascript:" onclick="send_mail('+row.goods_id+','+row.product_id+');">发送到货邮件</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var sendletterthtml = '<a href="javascript:" onclick="send_letter('+row.goods_id+','+row.product_id+');">发送到货站内信</a>';
						var sendmsgthtml = '<a href="javascript:">发送到货短信</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						return viewthtml+sendemailthtml+sendmsgthtml+sendletterthtml;
					}
				},
			]]	
		});
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
	});
</script>