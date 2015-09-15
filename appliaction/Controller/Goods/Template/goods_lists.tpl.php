<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<style type="text/css">
	.menu {background:#FFFFFF;}
	#batch .m-btn-small {margin-left: 0;}
</style>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
	<!-- 内容区 -->
	<div class="content">
		<div class="site">
			Haidao Board <a href="#">商品管理</a> > 商品列表
		</div>
		<span class="line_white"></span>
	<div class="goods mt10">
		<div class="guanli">
			<span style="margin-right: 10px;">按分类查看</span>
			<select name='category_id' id='category_id' class="easyui-combobox" data-options="editable:false" style="height: 26px;">
			</select>
			<span style="margin-right: 10px;margin-left: 5px;">按品牌查看</span>
			<select name='brand_id' id='brand_id' class="easyui-combobox" data-options="editable:false" style="height: 26px;">
			</select>
			<span style="margin-right: 10px;margin-left: 5px;">商品状态</span>
			<select name='status_ext' id='status_ext' class="easyui-combobox" data-options="editable:false,panelHeight:'auto'" style="height: 26px;">
			</select>
			<span style="margin-right: 10px;margin-left: 5px;">搜索</span>
			<input id="keyword" class="easyui-textbox" name="keyword" style="width:210px;height: 26px;" prompt="输入商品名称/货号/条码">
			<a id="search" href="#" class="easyui-linkbutton" style="height: 26px;padding-right: 10px;">查询</a>
		</div>
	<dl class="mt10">
		<dt><p>
			<a href="<?php echo U('lists');?>" <?php if($_GET['label'] == ''){?>class="hover"<?php } ?> >全部商品</a>
			<a href="<?php echo U('lists?label=1');?>" <?php if($_GET['label'] == '1'){?>class="hover"<?php } ?> >下架商品</a>
			<a href="<?php echo U('lists?label=2');?>" <?php if($_GET['label'] == '2'){?>class="hover"<?php } ?> >缺货商品</a>
			<a href="<?php echo U('lists?label=3');?>" <?php if($_GET['label'] == '3'){?>class="hover"<?php } ?> >库存警告</a>
			<a href="<?php echo U('lists?label=4');?>" <?php if($_GET['label'] == '4'){?>class="hover"<?php } ?> >回收站</a></p>
		</dt>
		<dd>
			<div class="login mt10" style="border: none;">
				<table id="order_list_grid" style="width:100%"></table> 
			</div>
			<div id="mm1" class="easyui-menu" style="width:90px!important;">
					<div>恢复商品</div>
					<div>销毁商品</div>
					<div>设为促销</div>
					<div>设为热卖</div>
					<div>设为新品</div>
					<div>取消促销</div>
					<div>取消热卖</div>
					<div>取消新品</div>
					<div>商品上架</div>
					<div>商品下架</div>
			 </div>
			<div class="clear"></div>
		</dd>
	</dl>
		 <?php include $this->admin_tpl('copyright') ?>
	</div>
<!-- /内容区 -->
	<script type="text/javascript">
		$(function(){
			//默认高亮
			$(window.parent.document).find(".z_side").removeClass("hover");
			$(window.parent.document).find(".n10").addClass("hover");
		})
	</script>
<!--表格js-->
	<script type="text/javascript">
	var excelurl= '<?php echo U('excel');?>';
	var dom = $('#order_list_grid');
	var label = '<?php echo $label?>';
	var cat_arr = <?php echo json_encode($this->treeMenu)?>;
	var brand_arr = <?php echo json_encode($brand_arr)?>;
	var status_ext_arr =[{"id":"0","name":"请选择"},
		{"id":"1","name":"促销"},
		{"id":"2","name":"热卖"},
		{"id":"3","name":"新品"}]
	var pageSize = <?php echo PAGE_SIZE?>;
	var dataurl = '<?php echo U('lists');?>';
	var addurl ='<?php echo U('add');?>';
	var importurl = '<?php echo U('import')?>';
	var viewurl = '<?php echo U('Goods/index/detail');?>';
	var delurl = '<?php echo U('ajax_del');?>';
	var editurl = '<?php echo U('edit');?>';
	var statusurl = '<?php echo U('ajax_status');?>';
	var status_exturl = '<?php echo U('ajax_status_ext');?>';
	var recoverurl = '<?php echo U('ajax_recover');?>';
	var deltrueurl = '<?php echo U('edit');?>';
	var keyword = '<?php echo $keyword?>';
	var brand_id = <?php echo $brand_id?>;	
	var sorturl = '<?php echo U('ajax_sort')?>';
	try{
		cat_arr.unshift({"value":"0","text":"请选择"});
		brand_arr.unshift({"id":"0","name":"请选择"});
	}catch(error){
		cat_arr=[{"value":"0","text":"请选择"}];
		brand_arr=[{"id":"0","name":"请选择"}];
	}
	$(function(){	
		dom.datagrid({   
			url:dataurl, 
			striped:true, //交替换行
			width:'100%',
			checkOnSelect:true,
			fitColumns:true, //真正的自动展开/收缩列的大小，以适应网格的宽度，防止水平滚动。
			toolbar:[{
					id:'delrows',
					text:'删除',
					iconCls:'icon-del',
				},'-',
				{
					id:'addrow',
					text:'添加',
					iconCls:'icon-add',
				},'-',
				{
					id:'import',
					text:'导入',
					iconCls:'icon-import',
				},'-',
				{
					id:'export',
					text:'导出',
					iconCls:'icon-export',
				},'-',
				{
					id:'batch',
					text:'批量操作',
					iconCls:'icon-alledit',
					width:'auto',
					style:'margin-left:-10px',
				},'-'
			],
			frozenColumns:[[
			  {field:'id',checkbox:true}
			]],
			queryParams:{
				label:label,
				brand_id:brand_id
			},
			pagination:true,
			pageSize:pageSize,
			pageList: [pageSize,pageSize*2,pageSize*4],//可以设置每页记录条数的列表 
			columns:[[
				{field:'name',title:'商品名称',width:'40%',halign:'center',align:'left',sortable:true,
					formatter:function(value,row,index){
						var status_ext_text = '';
						if(row.status_ext.indexOf('1') >= 0){
							status_ext_text +=' [促销] '
						}
						if(row.status_ext.indexOf('2') >= 0){
							status_ext_text +=' [热卖] '
						}
						if(row.status_ext.indexOf('3') >= 0){
							status_ext_text +=' [新品] '
						}
						return value+status_ext_text;
					}
				},
				{field:'cat_names',title:'商品分类',width:'10%',halign:'center',align:'center'},
				{field:'brand_id',title:'商品品牌',width:'12%',halign:'center',align:'center',
					formatter:function(value,row,index){ 
					var brand_name = '-';
						for(var o in brand_arr){
							if(brand_arr[o].id == value){
								brand_name = brand_arr[o].name
							}
						}
							return brand_name;
					}
				},
				{field:'shop_price',title:'销售价',width:'6%',halign:'center',align:'center'},
				{field:'market_price',title:'市场价',width:'6%',halign:'center',align:'center'},
				{field:'status',title:'上架',width:'5%',halign:'center',align:'center',
					formatter:function(value,row,index){
						if(value ==1){
							statustext = '<span url="'+statusurl+'&id='+row.id+'" class="ajax-get ajax_on" ></span>'
						}else{
							statustext = '<span url="'+statusurl+'&id='+row.id+'" class="ajax-get ajax_off" ></span>'							
						}
						return statustext;
					}
				},
				{field:'goods_number',title:'库存',width:'5%',halign:'center',align:'center'},
				{field:'sort',title:'排序',width:'6%',align:'center',sortable:true,
					formatter:function(value,row,index){
						return '<input name="sort" class="easyui-numberspinner sort" style="width:50px;" required="required" data-options="min:0,editable:true" value="'+value+'" data-id="'+row.id+'">';
					}
				},
				{field:'userID',title:'操作',width:'10%',halign:'center',align:'center',
					formatter:function(value,row,index){
						sptext = '&nbsp;&nbsp;&nbsp;&nbsp;';
						viewhtml = '<a href="'+viewurl+'&id='+row.id+'" target="_blank">查看</a>';
						edithtml = '<a href="'+editurl+'&id='+row.id+'">编辑</a>';
						delhtml = '<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');$(\'#delrows\').trigger(\'click\')" href="#">删除</a>';
						ritem = {"text":"恢复商品"};
						recoverhtml = '<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');batch(ritem)" href="#">恢复</a>';
						ditem = {"text":"销毁商品"};
						deltruehtml = '<a onclick="dom.datagrid(\'clearSelections\').datagrid(\'clearChecked\').datagrid(\'checkRow\','+index+');batch(ditem)" href="#">销毁</a>';
						if (row.status == -1){
							return viewhtml+sptext+recoverhtml+sptext+deltruehtml;
						}else{
							return viewhtml+sptext+edithtml+sptext+delhtml;
						}
					}
				},
			]],
			onLoadSuccess:function(data){
				$('#category_id').combobox({
					data:cat_arr,
					valueField:'value',
					textField:'text'
				});
				$('#brand_id').combobox({
					data:brand_arr,
					valueField:'id',
					textField:'name'
				});
				$('#status_ext').combobox({
					data:status_ext_arr,
					valueField:'id',
					textField:'name'
				});
				$('.sort').numberspinner({
					onChange:function(nvalue,ovalue){
						var id = $(this).attr('data-id');
						ChangeSort(id,nvalue);
					}
				});
				$('.datagrid-btable td[field="sort"]').each(function(){
					$(this).click(function(){
						return false;
					});
				})
				try{
					$('#status_ext').combobox('setValue',data.search.s_status_ext);
					$('#brand_id').combobox('setValue',data.search.s_barnd_id);
					$('#category_id').combobox('setValue',data.search.s_category_id);
				}catch(error){}
			}	
		});
        //修改排序
		function ChangeSort(id,val){
			$.messager.progress();
			$.getJSON(sorturl, {"id": id,"val": val}, function(data) {
				$.messager.progress('close');
			})
		}
		//添加
		$('#addrow').bind('click', function(){
			window.location.href=addurl;
		})	
		//导入
		$('#import').bind('click', function(){
			window.location.href=importurl;
		})
	   	//导出数据
	    $("#export").click(function(){
		var ids = [];
		var rows = dom.datagrid('getChecked');
		for(var i=0; i<rows.length; i++){
		  ids.push(rows[i].id);
		}
		if(ids == ''){
			alert('请选择你要导出的商品');
			return false;
		}
		window.location.href = excelurl +'&ids='+ids;
	    })
		//回车查询
		$('#keyword').textbox('textbox').bind('keydown',function (e) {
			if (e.keyCode == 13) {
				$('#search').trigger('click');
			}
		});
		//增加查询参数，重新加载表格  
		$('#search').bind('click',function (){	
			var queryParams = dom.datagrid('options').queryParams; 
			//查询参数直接添加在queryParams中	 
			queryParams.keyword = $("#keyword").val();
			queryParams.category_id = $('#category_id').combobox('getValue');
			queryParams.brand_id = $('#brand_id').combobox('getValue');
			queryParams.status_ext = $('#status_ext').combobox('getValue');
			dom.datagrid('options').queryParams = queryParams;  
			dom.datagrid('reload');  
		})
		//批量操作
		var Menu = $('#batch .l-btn-text').menubutton({ menu: '#mm1' });
		//menubutton 依赖于 menu、linkbutton 这两个插件，所以我们可以这样搞定她
		$(Menu.menubutton('options').menu).menu({
			onClick: function (item) {
				batch(item);
			}
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
						ajax_ids(ids,delurl);
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
	function batch(item){
			var ids = [];
			var rows = dom.datagrid('getChecked');
			for(var i=0; i<rows.length; i++){
				ids.push(rows[i].id);
			}
			if (ids.length > 0){
				$.messager.confirm('确认','您确认想要 '+item.text+' 所选择记录吗？',function(r){	
					if (r){
						if(item.text == '恢复商品'){
							ajax_ids(ids,recoverurl);
							return;
						}
						if(item.text == '销毁商品'){
							if(label != 4){
								$.messager.alert('错误','需要在回收站页才可以清空商品');
								return;
							}else{
								ajax_ids(ids,delurl+'&label=4');
							}
							return;
						}
						if(item.text == '设为促销'){
							ajax_ids(ids,status_exturl+'&val=1&status=1');
							return;
						}
						if(item.text == '设为热卖'){
							ajax_ids(ids,status_exturl+'&val=2&status=1');
							return;
						}
						if(item.text == '设为新品'){
							ajax_ids(ids,status_exturl+'&val=3&status=1');
							return;
						}
						if(item.text == '取消促销'){
							ajax_ids(ids,status_exturl+'&val=1&status=0');
							return;
						}
						if(item.text == '取消热卖'){
							ajax_ids(ids,status_exturl+'&val=2&status=0');
							return;
						}
						if(item.text == '取消新品'){
							ajax_ids(ids,status_exturl+'&val=3&status=0');
							return;
						}
						if(item.text == '商品上架'){
							ajax_ids(ids,statusurl+'&val=1');
							return;
						}
						if(item.text == '商品下架'){
							ajax_ids(ids,statusurl+'&val=0');
							return;
						}
					}else{
						dom.datagrid('clearSelections').datagrid('clearChecked');
					}
				});
			}else{
				$.messager.alert('警告','请选择要操作的记录'); 
				return false;
			}
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
		}
</script>