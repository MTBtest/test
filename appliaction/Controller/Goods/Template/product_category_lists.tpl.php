<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">商品管理</a> > 分类列表
	</div>
	<span class="line_white"></span>
	<div class="login mt10" style="border: none;">
		<table id="product_categroy_lists" style="width:100%;height:auto"></table>
	</div>
	<div class="clear"></div>
	<?php include $this->admin_tpl('copyright') ?>
</div>
<script type="text/javascript">
	var dom = $('#product_categroy_lists');
	var dataurl = '<?php echo U('cat_child')?>';
	var addurl = '<?php echo U('add')?>';
	var editurl = '<?php echo U('edit')?>';
	var sorturl = '<?php echo U('ajax_sort')?>';
	var delurl = '<?php echo U('ajax_del')?>';
	var navurl = '<?php echo U('ajax_show_in_nav')?>';
	var statusurl = '<?php echo U('ajax_status')?>';
	$(function(){
		dom.treegrid({	
			url:dataurl,	
			idField:'id',	
			treeField:'name',
			striped:true,
			fitColumns:true,
			toolbar:[
				{
					id:'addareabtn',
					text:'添加',
					iconCls:'icon-add',
					handler:function(){
						window.location.href = addurl;
					}
				},'-'
			],
			columns:[[	
				{field:'name',title:'分类名称',width:'40%',halign:'center',align:'left'},
				{field:'grade',title:'价格分级',width:'20%',halign:'center',align:'left'},
				{field:'sort',title:'分类排序',width:'10%',halign:'center',align:'center',
					formatter:function(value,row,index){
						return '<input name="sort" class="easyui-numberspinner sort" style="width:80px;" required="required" data-options="min:0,editable:true" value="'+value+'" data-id="'+row.id+'">';
					}
				},
				{field:'show_in_nav',title:'是否导航',width:'10%',halign:'center',align:'center',
					formatter:function(value,row,index){
						if(value ==1){
							statustext = '<span url="'+navurl+'&id='+row.id+'" class="ajax-get ajax_on" ></span>'
						}else{
							statustext = '<span url="'+navurl+'&id='+row.id+'" class="ajax-get ajax_off" ></span>'
						}
						return statustext;
					}
				},
				{field:'status',title:'是否显示',width:'10%',halign:'center',align:'center',
					formatter:function(value,row,index){
						if(value ==1){
							statustext = '<span url="'+statusurl+'&id='+row.id+'" class="ajax-get ajax_on" ></span>'
						}else{
							statustext = '<span url="'+statusurl+'&id='+row.id+'" class="ajax-get ajax_off" ></span>'
						}
						return statustext;
					}
				},
				{field:'none',title:'操作',width:'11%',halign:'center',align:'center',
					formatter:function(value,row,index){
						spacetext = '&nbsp;&nbsp;&nbsp;&nbsp;';
						edithtml = '<a href="'+editurl+'&id='+row.id+'">编辑</a>';
						delhtml = '<a onclick=batch('+row.id+')>删除</a>';
						return edithtml + spacetext + delhtml;
					}
				}
			]],
			onBeforeLoad: function(row,param){
				if (!row) { // load top level rows
					param.id = 0; // set id=0, indicate to load new page rows
				}
			},
			onLoadSuccess:function(data){
				$('.sort').numberspinner({
					onChange:function(nvalue,ovalue){
						var id = $(this).attr('data-id');
						if($(this).val().length>0){
							ChangeSort(id,nvalue);
						}
						return;
					}
				});
				$('.datagrid-btable td[field="sort"]').each(function(){
					$(this).click(function(){
						return false;
					});
				})
				//dom.treegrid('expandAll');
			}
		});
		//修改排序
		function ChangeSort(id,val){
			$.messager.progress();
			$.getJSON(sorturl, {"id": id,"val": val}, function(data) {
				$.messager.progress('close');
			})
		};
	});
		function batch(row){
            $.messager.confirm('确认','您确认想要删除所选择分类吗？',function(r){
				if(r){
					window.location = delurl+'&id='+row;
				}
			})
		}
</script>