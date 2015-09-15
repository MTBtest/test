<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">内容管理</a> > 分类列表
	</div>
	<span class="line_white"></span>
	<div class="login mt10" style="border: none;">
		<table id="article_categroy_lists" style="width:100%;height:auto"></table>
	</div>
	<div class="clear"></div>
	<?php include $this->admin_tpl('copyright') ?>
</div>
<script type="text/javascript">
	var dom = $('#article_categroy_lists');
	var dataurl = '<?php echo U('cat_child')?>';
	var addurl = '<?php echo U('update?opt=add')?>';
	var editurl = '<?php echo U('update?opt=edit')?>';
	var delurl = '<?php echo U('update?opt=del')?>';
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
				{field:'name',title:'分类名称',width:'70%',halign:'center',align:'left'},
				{field:'none',title:'操作',width:'31%',halign:'center',align:'center',
					formatter:function(value,row,index){
						spacetext = '&nbsp;&nbsp;&nbsp;&nbsp;';
						addsubhtml = '<a href="'+addurl+'&pid='+row.id+'" >添加子分类</a>';
						edithtml = '<a href="'+editurl+'&id='+row.id+'">编辑</a>';
						delhtml = '<a href="'+delurl+'&id='+row.id+'">删除</a>';
						return addsubhtml + spacetext + edithtml + spacetext + delhtml;
					}
				}
			]],
			onBeforeLoad: function(row,param){
				if (!row) { // load top level rows
					param.id = 0; // set id=0, indicate to load new page rows
				}
			},
			onLoadSuccess:function(data){
				dom.treegrid('expandAll');
			}
		});
	});
</script>