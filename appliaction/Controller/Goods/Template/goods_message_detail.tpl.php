<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">商品管理</a> > 商品到货通知 > 发送详情
	</div>
		<div class="goods mt10">
			<dl class="mt10">
				<dt>
					<p>
						<a href="<?php echo U('Goods/GoodsMessage/detail?goods_id='.$goods_id.'&product_id='.$product_id.'&status=0')?>" class="<?php echo $hover[0]?>">尚未发送</a>
						<a href="<?php echo U('Goods/GoodsMessage/detail?goods_id='.$goods_id.'&product_id='.$product_id.'&status=1')?>" class="<?php echo $hover[1]?>">已经发送</a>
					</p>
				</dt>
				<p style="margin-top: 12px;">商品:<?php echo getGoodsNumber($goods_id,$product_id,'name')?> 现有库存:<?php echo getGoodsNumber($goods_id,$product_id,'goods_number')?></p>
			</dl>
			<div class="login mt10" style="border: none;">
				<table id="product_band_lists" style="width:100%"></table> 
			</div>
			<div class="clear"></div>
			<?php include $this->admin_tpl('copyright') ?>
	</div>
</div>
<script>
    //发送邮件
	function send_mail(goods_id,product_id,id) {
		$.messager.confirm("发送到货邮件通知", '发送邮件可能比较费时,如果邮件较多,请使用专业软件发送!', function (r) {
			if (r) {
				var url = "<?php echo U('Goods/GoodsMessage/send_mail') ?>";
				$.messager.progress();
				$.getJSON(url, {"goods_id": goods_id,"product_id": product_id,"id":id}, function(data) {
					$.messager.progress('close');
					$.messager.alert('提示',data.info);
				})
				return true;
			}
    	});
    	return false;
	}
	//发送站内信
	function send_letter(goods_id,product_id,id) {
			$.messager.confirm("发送到货站内信通知", '确认发送站内信通知？', function (r) {
			if (r) {
				var url = "<?php echo U('Goods/GoodsMessage/send_letter') ?>";
				$.messager.progress();
				$.getJSON(url, {"goods_id": goods_id,"product_id": product_id,'id':id}, function(data) {
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
	var dom = $('#product_band_lists');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('detail',array('goods_id'=>$_GET['goods_id'],'product_id'=>$_GET['product_id'],'status'=>$_GET['status']))?>';
	var delurl = '<?php echo U('update?opt=del')?>';
	var listsurl = '<?php echo U('lists')?>';
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
				},'-',
				{
					id:'backlists',
					text:'返回',
					iconCls:'icon-undo',
				},'-'
			],
			frozenColumns:[[
				{field:'id',checkbox:true}
			]],
			queryParams:{
				status:status
			},
			pagination:true,
			pageSize:pageSize,
			pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表 
			columns:[[
				{field:'user_name',title:'用户名',width:'12%',halign:'center',align:'center'},
				{field:'email',title:'用户Email',width:'18%',align:'center',sortable:true},
				{field:'mobile',title:'手机号',width:'12%',align:'center',sortable:true},
				{field:'num',title:'订购数量',width:'10%',halign:'center',align:'center',sortable:true},
				{field:'status',title:'通知状态',width:'10%',halign:'center',align:'center',sortable:true,
					formatter:function(value,row,index){
						if(value == 0){
							status_text = '未通知';
						}else{
							status_text = '已通知';
						}
						return status_text;
					}
				},
				{field:'dateline',title:'用户登记时间',width:'13%',halign:'center',align:'center',sortable:true,
					formatter:function(value,row,index){  
  						return $.fn.datebox.defaults.timeformat(value);
					}
				},
				{field:'none',title:'操作',width:'25%',halign:'center',align:'center',
					formatter:function(value,row,index){
						var sendemailthtml = '<a href="javascript:" onclick="send_mail('+row.goods_id+','+row.product_id+','+row.id+');">发送到货邮件</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var sendmsgthtml = '<a href="javascript:">发送到货短信</a>&nbsp;&nbsp;&nbsp;&nbsp;';
						var sendletterthtml = '<a href="javascript:" onclick="send_letter('+row.goods_id+','+row.product_id+','+row.id+');">发送到货站内信</a>';
						if(row.user_id != 0){
							if(!row.email){
								return sendmsgthtml+sendletterthtml;
							}else if(!row.mobile){
								return sendemailthtml+sendletterthtml;
							}else{
								return sendemailthtml+sendmsgthtml+sendletterthtml;
							}
						}else{
							if(!row.email){
								return sendmsgthtml;
							}else if(!row.mobile){
								return sendemailthtml;
							}else{
								return sendemailthtml+sendmsgthtml;
							}
						}
					}
				},
			]]	
		});
		//返加
		$('#backlists').bind('click',function(){
			window.location.href=listsurl;		
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
								$.messager.alert('警告',data.info);
							}	
						})	
					} 
				});  
			}else{
				$.messager.alert('警告','请选择要删除的记录'); 
				return false;
			}
		});
		
	})
</script>