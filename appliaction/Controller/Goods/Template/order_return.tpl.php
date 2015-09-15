<?php  include  $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
	Haidao Board <a href="#">订单管理</a> > 申请退货列表
	</div>
	<span class="line_white"></span>
	<div class="goods mt10">
		<div class="search_ind">
			<input type="hidden" name="m" value="<?php echo GROUP_NAME ?>">
			<input type="hidden" name="c" value="<?php echo MODULE_NAME ?>">
			<input type="hidden" name="a" value="<?php echo ACTION_NAME ?>">
			<span style="margin-right: 10px;">订单搜索 </span>
			<input id="keyword" class="easyui-textbox" name="keyword" style="width:210px;height: 26px;" prompt="输入订单号/收货人姓名/手机均可查询">
			<a id="search" href="#" class="easyui-linkbutton" style="height: 26px;padding-right: 10px;">查询</a>
		</div>
		<dl class="mt10">
			<dt><p>
				<a href="<?php echo  U('AdminOrder/order_return')?>" <?php if ($type == -2) { echo "class='hover'"; }; ?> > 全部</a>
				<a href="<?php echo  U('AdminOrder/order_return?type=0')?>"  <?php if ($type == 0) { echo "class='hover'"; }; ?>>申请中</a>
				<a href="<?php echo  U('AdminOrder/order_return?type=1')?>" <?php if ($type == 1) { echo "class='hover'"; }; ?>
					>已通过</a>
				<a href="<?php echo  U('AdminOrder/order_return?type=2')?>" <?php if ($type == 2) { echo "class='hover'"; }; ?>
					>未通过</a>
				<a href="<?php echo  U('AdminOrder/order_return?type=-1')?>" <?php if ($type == -1) { echo "class='hover'"; }; ?>
					>已取消</a>
			</p>
			</dt>
		</dl>
		<div class="login mt10" style="border: none;border-right: 1px solid #e6e6e6;">
			<table id="order_list_grid" style="width:100%;"></table>
		</div>
		<div class="clear"></div>
		<?php include $this->admin_tpl('copyright') ?>
	</div>
</div>
	<script type="text/javascript">
		$(function() {
		//批量操作
			$(".batch").hover(function() {
				$(this).find("p").show();
				}, function() {
				$(this).find("p").hide();
			});
		})
		//改变状态架
		function corderstatus(ftype,val) {
			var arr = [];
			$("input[name='selid[]']:checked").each(function() {
				arr.push($(this).val());
			});
			if (arr.length == 0) {
				alert('请选择操作项目');
				return false;
			}
			$.post("<?php echo U('AdminOrder/ajax_status');?>", {"ftype":ftype ,"id": arr.toString(), "val": val}, function(data) {
				if (data.status == 1) {
				location.reload();
				}
			})
		}
		//切换
		$(function() {
			var tabTitle = ".mt10 dt p a";
			$(tabTitle).click(function(){
				$(this).siblings("a").removeClass("hover").end().addClass("hover");
			});
		});
	</script>
	<!--表格js-->
	<script type="text/javascript">
	var dom = $('#order_list_grid');
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('order_return')?>';
	var delurl = '<?php echo U('delete')?>';
	var editurl = '<?php echo U('edit')?>';
	var imgpach = '<?php echo IMG_PATH;?>';
	var region_arr	= <?php echo json_encode($region_arr)?>;
	var delivery_arr = <?php echo json_encode($this->deliverys)?>;
	var order_status = <?php echo json_encode($this->_config['c_order_status'])?>;
	var pay_status = <?php echo json_encode($this->_config['c_pay_status'])?>;
	var delivery_status = <?php echo json_encode($this->_config['c_delivery_status'])?>;
	var type = <?php echo $type?>;
	var keyword = '<?php echo $keyword?>';
	var user_id = <?php echo $user_id?>;
	$(function(){
		dom.datagrid({
			url:dataurl,
			striped:true,
			width:'100%',
			checkOnSelect:false,
			fitColumns:true,
			toolbar:[
				// {
				// 	id:'exportrows',
				// 	text:'导出',
				// 	iconCls:'icon-export',
				// },'-'
			],
			frozenColumns:[[
				{field:'id',checkbox:true}
			]],
			queryParams:{
				type:type,
				keyword:keyword,
				user_id:user_id
			},
			scrollbarSize:0,
			pagination:true,
			pageSize:pageSize,
			pageList: [pageSize,pageSize*4,pageSize*8,pageSize*16,pageSize*32,pageSize*64],
			//可以设置每页记录条数的列表
			columns:[[
			{field:'order_sn',title:'订单号',width:'13%',align:'center',sortable:true},
			{field:'return_type',title:'类型',width:'3%',align:'center',sortable:true,
				formatter:function(value,row,index){
					try{
						if (value == 1) {
							return_type = '退货';
						} else if(value == 2) {
							return_type = '换货';
						}
					}catch(e) {
						return_type = '-';
					}
					return return_type;
				}
			},
			{field:'user_name',title:'用户名',width:'5%',align:'center',sortable:true},
			{field:'real_amount',title:'订单金额',width:'5%',halign:'center',align:'center',sortable:true},
			{field:'return_status',title:'申请状态',width:'5%',halign:'center',align:'center',
				formatter:function(value,row,index){
					switch (value) {
						case '-1' :
							status_text = '已取消';
							break;
						case '1' :
							status_text = '已通过';
							break;
						case '2' :
							status_text = '未通过';
							break;
						default :
							status_text = '<span style="color:red;">申请中</span>';
							break;
					}
					return status_text;
				}
			},
			{field:'return_date',title:'申请时间',width:'10%',halign:'center',align:'center',sortable:true,
				formatter:function(value,row,index){
					return $.fn.datebox.defaults.timeformat(value);
				}
			},
			{field:'return_delivery_name',title:'退货快递',width:'9%',halign:'center',align:'center',sortable:true},
			{field:'return_delivery_sn',title:'快递单号',width:'18%',halign:'center',align:'center',sortable:true},
			{field:'return_imgs',title:'上传截图',width:'24%',halign:'center',align:'center',sortable:true,
				formatter:function(value,row,index){
					var _str = '';
					$.each(value, function(i,val){
						_str += '<a href="'+ val +'" title="点击查看原图" target="_blank"><img src="'+ val +'" width="45px" height="45px" style="margin-left:5px;padding:2px;"/></a>';
					});
					return (_str.length > 1) ? _str : '-';
				}
			},
			{field:'none',title:'操作',width:'6%',halign:'center',align:'center',
				formatter:function(value,row,index){
					var viewthtml = '<a href="'+editurl+'&order_sn='+row.order_sn+'">查看</a>';
					return viewthtml ;
				}
			}
			]]
		});
		//回车查询
		$('#keyword').textbox('textbox').bind('keydown',function (e) {
			if (e.keyCode == 13) {
				$('#search').trigger('click');
			}
		});
	});
	//增加查询参数，重新加载表格
	$('#search').bind('click',function (){
		var queryParams = dom.datagrid('options').queryParams;
		//查询参数直接添加在queryParams中
		queryParams.keyword = $("#keyword").val();
		dom.datagrid('options').queryParams = queryParams;
		dom.datagrid('reload');
	})
</script>