<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<style type="text/css">
	span.textbox.numberbox{padding-left: 0px;}
	.text_input{margin-right: 0px;}
</style>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">站点设置</a> > 地区管理
	</div>
	<span class="line_white"></span>
	<div class="login mt10" style="border: none;">
		<table id="region" style="width:100%;height:auto"></table>
	</div>
		<?php include $this->admin_tpl("copyright"); ?>
</div>
<!--弹窗编辑-->
	<div id="edit_area_box" class="easyui-window"  style="width:500px;" closed="true" modal="true" minimizable="false" maximizable="false" collapsible="false" data-options="title:'编辑地区'">
		<form id="editform" name="editform" method="post" action="<?php echo U('ajax_update'); ?>" autocomplete="off" onsubmit="return $(this).form('validate');">
			<div >
				<dl>
					<dd>
						<ul class="web">
							<li>
								<strong  >地区名称设置：</strong>
								<input type="text" name="area_name" class="easyui-validatebox text_input"  data-options="required:true,enableValidation:'text'" />
								<span>设置当前地区名称</span>
							</li>
						</ul>
					</dd>
				</dl>
			</div>
			<div data-options="region:'south',border:false" style="text-align:right;padding:5px 20px;">
				<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="$('#editform').submit()" style="padding: 0 10px;">确定</a>
				<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="$('#edit_area_box').window('close');" style="padding: 0 10px;">取消</a>
			</div>
			<input type="hidden" name="area_id" value="" />
			<input type="hidden" name="sort" value="" />
		</form>
	</div>
<!--弹窗添加-->
	<div id='add_area_box' class="easyui-window editdandp"  style="width:500px;" closed="true" modal="true" minimizable="false" maximizable="false" collapsible="false" data-options="title:'新增省份'">
		<form id="addform" name="addform" class="priceform" method="post" action="<?php echo U('ajax_update'); ?>" onsubmit="return $(this).form('validate');">
			<ul style="padding-top: 30px;margin-left: 100px;">
				<li style="margin: auto;">
					<strong>新增省份</strong>
					<input type="text" name="area_name" value="" class="easyui-validatebox text_input" data-options="required:true,enableValidation:'text'" />
				</li>
			</ul>
			<div data-options="region:'south',border:false" style="text-align:right;padding:5px 20px;">
				<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="$('#addform').submit()" style="padding: 0 10px;">确定</a>
				<a class="easyui-linkbutton"  href="javascript:void(0)" onclick="$('#add_area_box').window('close');" style="padding: 0 10px;">取消</a>
			</div>
			<input type="hidden" name="parent_id" value="" />
		</form>
	</div>
<script type="text/javascript">
	var dom = $('#region');
	var dataurl = '<?php echo U('area_child')?>';
	var sorturl = '<?php echo U('ajax_sort')?>';
	var editurl = '<?php echo U('ajax_update')?>';
	var delurl = '<?php echo U('ajax_del')?>';
	$(function(){
		dom.treegrid({
			url:dataurl,
			idField:'area_id',
			treeField:'area_name',
			striped:true,
			fitColumns:true,
			toolbar:[
				{
					id:'addareabtn',
					text:'添加',
					iconCls:'icon-add',
				},'-'
			],
			columns:[[
				{title:'地区名称',field:'area_name',width:'61%',halign:'center',align:'left'},
				{title:'地区排序',field:'sort',width:'10%',halign:'center',align:'center',
					formatter:function(value,row,index){
						return '<input name="sort" class="easyui-numberspinner sort" style="width:80px;" required="required" data-options="min:0,editable:true" value="'+value+'" data-id="'+row.area_id+'">';
					}
				},
				{title:'操作',field:'none',width:'30%',halign:'center',align:'center',
					formatter:function(value,row,index){
						spacetext = '&nbsp;&nbsp;&nbsp;&nbsp;';
						addsubhtml = '<a href="javascript:void (0)" onclick="add_listsbtn('+row.area_id+')">添加子地区</a>';
						edithtml = '<a href="javascript:void (0)" onclick="edit_areabtn('+row.area_id+',\''+row.area_name+'\','+row.sort+')">编辑</a>';
						delhtml = '<a href="javascript:void (0)" onclick="del('+row.area_id+')">删除</a>';
						return addsubhtml + spacetext + edithtml + spacetext + delhtml;
					}
				}
			]],
			onBeforeLoad: function(row,param){
				if (!row) { // load top level rows
					param.id = 1; // set id=0, indicate to load new page rows
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
			}
		});
		//修改排序
		function ChangeSort(id,val){
			$.messager.progress();
			$.getJSON(sorturl, {"id": id,"val": val}, function(data) {
				$.messager.progress('close');
			})
		};
		//添加地区弹窗
		$("#addareabtn").click(function(){
			$('#add_area_box').window('open');
			$(".window-mask").css({ height: $(document).height()}).click(function(){
				$('#add_area_box').window('close');
			});
			$('#edit_area_box').window('center');
		})
	});
		//添加子地区弹窗
		function add_listsbtn(area_id){
			$('#addform').form('clear');
			$('#addform [name="parent_id"]').val(area_id);
			$('#add_area_box').window('open');
			$(".window-mask").css({ height: $(document).height()}).click(function(){
				$('#add_area_box').window('close');
			});
			$('#edit_area_box').window('center');
		}
		//编辑地区弹窗
		function edit_areabtn(area_id,area_name,sort){
			$('#editform').form('clear');
			$('#editform [name="area_name"]').val(area_name);
			$('#editform [name="sort"]').val(sort);
			$('#editform [name="area_id"]').val(area_id);
			$('#edit_area_box').window('open');
			$(".window-mask").css({ height: $(document).height()}).click(function(){
				$('#edit_area_box').window('close');
			});
			$('#edit_area_box').window('center');
		}
		//删除
		function del(id){
			$.messager.confirm('确认','您确认想要删除记录吗？',function(r){
				if (r){
					$.getJSON(delurl,
					{"id":id},
					function(data){
						if(1 == data.status){// 删除成功，则需要在树中删除节点
							// 检修任务grid 执行load
							dom.treegrid("reload");  //重新加载
						}else{
							$.messager.alert('警告',data.info);
						}
					})
				}else{
					dom.treegrid('clearSelections').treegrid('clearChecked');
				}
			});
		}
</script>