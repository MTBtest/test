<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">站点设置</a> > 微信自定义菜单列表
	</div>
	<span class="line_white"></span>
	 <li style="margin-top: 6px; padding-bottom: 6px; border-bottom: 1px dotted #D2E2ED; list-style: none;">
         <p>小提示：编辑完菜单后要点击生成菜单，并重新关注微信公众号，微信端才会显示，编辑自定义菜单前请确保微信配置正常</p>
      </li>
	<div class="login mt10" style="border: none;">
		<table id="menu_lists" style="width:100%;height:auto"></table>
	</div>
	<div class="clear"></div>
	<?php include $this->admin_tpl('copyright') ?>
</div>
<script type="text/javascript">
	var dom = $('#menu_lists');
	var dataurl = '<?php echo U('cat_child')?>';
	var addurl = '<?php echo U('add')?>';
	var editurl = '<?php echo U('edit')?>';
	var delurl = '<?php echo U('ajax_del')?>';
	var createurl ='<?php echo U('create_menu')?>'
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
				},'-',
				{
					id:'creatmenu',
					text:'生成菜单',
					iconCls:'icon-backup',
					handler:function(){
						window.location.href = createurl;
					}
				},'-'
			],
			columns:[[	
				{field:'name',title:'菜单名称',width:'40%',halign:'center',align:'left',expandAll:true},
				{field:'type',title:'菜单类型',width:'15%',halign:'center',align:'center',
                   formatter:function(value,row,index){
						try{
						  if(value == 1){
								type_text = '内置链接';
							 }else if(value == 2){
								type_text = '自定义链接';
							}else if(value == 3){
								type_text = '';
							}
						}catch(e){
							type_text = '-'
						}
						return type_text;
					}
 				},
				{field:'link',title:'地址链接',width:'30%',halign:'center',align:'center',},
			    {field:'none',title:'操作',width:'15%',halign:'center',align:'center',
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
				$('.datagrid-btable td[field="sort"]').each(function(){
					$(this).click(function(){
						return false;
					});
				})
				dom.treegrid('expandAll');
			}
		});
		//修改排序
		
	});
		function batch(row){
            $.messager.confirm('确认','您确认想要删除所选择分类吗？',function(r){
				if(r){
					window.location = delurl+'&id='+row;
				}
			})
		}
</script>