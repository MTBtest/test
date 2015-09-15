<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH?>admin/jquery.tagsinput.css" />
<style type="text/css">
		.web li span{padding-left: 0px;}
		.blue_table table tr{line-height: 15px;}
		.aui_title {background: #7ca4c2;height: 40px;line-height: 30px;}
		.add_area{max-height: 347px;min-height: 240px;padding-bottom: 30px;}
		.aui_state_focus .aui_title {color: #fff;}
		.aui_state_focus .aui_outer{border:1px solid #afaeae;}
		div.tagsinput{height: 30px;overflow-y: auto;}
		.fendd div a{background: url(<?php echo IMG_PATH?>admin/ico_0.png) no-repeat 9px center;}
		.fendd div a input{margin-right: 5px;}
	</style>
	<div class="content">
		<div class="site">
			Haidao Board <a href="#">商品管理</a> > 商品属性
		</div>
		<span class="line_white"></span>
		<div class="install tabs mt10">
			<form class="addform" action="<?php echo U('Goods/ProductModel/update') ?>" method="Post" onsubmit="return checkform()">
			<dl>
				<dt><a href="javascript:" class="hover">商品属性</a><a href="javascript:">商品规格</a></dt>
				<dd>
					<ul class="web">
						<li>
							<strong>类型名称：</strong>
							<input class="text_input" type="text" name="model_name" datatype="*"  value="<?php echo $info['name']?>">
							<span>填写商品类型名称，如：手机</span>
						</li>
						<li>
						<strong>允许筛选分类：</strong>
						<div class="fenlei">
							<div class="fentt clearfix">
								<h3 class="fl">所选<br />分类</h3>
								<div class="sl fl">
									<?php foreach ($info['category'] as $key => $value): ?>
										<div><?php echo $value; ?><em><img src="<?php echo IMG_PATH;?>admin/ico_close1.png"></em><input type="hidden" value="<?php echo $key; ?>" name="category_id[]"></div>
									<?php endforeach ?>
								</div>
								<div class="flts fr">
										选择商品所属分类，一个商品可选择多个分类
								</div>
							</div>
							<div class="fendd clearfix">
								<div class="root">
								</div>
								<div class="bgef">
								</div>
								<div class="bgef">
								</div>
								<div class="bgef">
								</div>
							</div>
						</div>
						<div class=" xtishi">
							<div class="fl">为商品类型指定哪些分类下可以使用此商品类型进行筛选，用于产品列表页产品查找，可指定任何一级分类，点击右侧[选择当前分类]或者双击即可选中分类</div>
							<div class="submit fr">
								<a href="javascript:" id="jiafen">选择当前分类</a>
							</div>
						</div>
					</li>
						<li>
							<strong>基本数据：</strong>
							<dl class="blue_table mt10">
								<dt style="height:42px; background: none repeat scroll 0 0 #E8F5FC;">
								<img src="<?php echo IMG_PATH?>admin/add_type_btn_03.png"  style="padding:10px;float:left;cursor: pointer;" onclick="addrow(this, 0, 0)"/>
								<span class="add" style="float:left;line-height: 44px;">点击添加属性规格筛选项，可用于前台产品商品列表页按照商品规格筛选</span>
							</dt>
								<div>
									<table class="border_table">
										<thead>
											<tr>
												<th>商品属性</th>
												<th>显示方式</th>
												<th>类型标签(每个类型标签用键盘回车分隔)</th>
												<th>是否参与筛选</th>
												<th>排序</th>
												<th>操作</th>
											</tr>
										</thead>
										<tbody class="attrow" id="attrBaseBody">
											<script id="attrRowTemplate" type="text/html">
												<%for(var i in templateData){%>
												<%item = templateData[i]%>
												<tr class='td_c1' style="height:40px;ling -moz-animation-duration: 40px;">
													<td><input class="pro_nature_input1" datatype="*" name="<%if(item['opt'] == 'add'){%>new<%}%>AttrName[]" nullmsg="" type="text" value="<%=item['name']%>" /></td>
													<td>
														<select name="<%if(item['opt'] == 'add'){%>new<%}%>AttrType[]" class="pro_nature_input2">
															<option value="1" <%if(item['type']==1){%>selected<%}%>>单选框</option>
															<option value="2" <%if(item['type']==2){%>selected<%}%>>复选框</option>
															<option value="3" <%if(item['type']==3){%>selected<%}%>>输入框</option>
														</select>
													</td>
													<td><input  class="pro_nature_input3 tags" datatype="" name="<%if(item['opt'] == 'add'){%>new<%}%>AttrValue[]" id="tags<%=item['temp_id']%>" value="<%=item['value']%>" /></td>
													<td><input type="checkbox" name="Search[]"  value="1" <%if(item['search']==1){%>checked<%}%>/></td>
													<td><input class="pro_nature_input1" datatype="n" type="input" name="<%if(item['opt'] == 'add'){%>new<%}%>AttrSort[]"  value="<%=item['sort']%>" /></td>
													<td><a href="javascript:" onclick="delattr(this,<%=item['id']%>)"> 删除</a></td>
												</tr>
												
												<%if(item['opt'] != 'add'){%>
													<input type="hidden" name="AttrId[]" value="<%=item['id']%>">
												<%}%>
												
												<%}%>
											</script>
										</tbody>
									</table>
								</div>
							</dl>
						</li>
					</ul>
				</dd>
				<!--商品规格-->
				<dd>
						<ul class="web">
							<li>
								<strong>基本数据：</strong>
								<dl class="blue_table mt10">
									<dt style="height:42px; background: none repeat scroll 0 0 #E8F5FC;">
										<img src="<?php echo IMG_PATH?>admin/add_type_btn_03.png" class="btn" onclick="selSpec()" style="padding:10px;float:left;cursor: pointer;">
								<span class="add" style="float:left;line-height: 44px;">点击添加属性规格筛选项，可用于前台产品商品列表页按照商品规格筛选</span>
								</dt>
									<div>
										<table class="border_table">
											<thead id="goodsBaseHead"></thead>
											<tr>
												<th width="250px">规格名称</th>
												<th>规格属性</th>
												<th style="text-align: right;padding-right: 40px;">操作</th>
											</tr>
											<!--商品内容模板-->
											<tbody id="specBaseBody">
												<script id="specRowTemplate" type="text/html">
													<%for(var i in templateData){%>
													<%item = templateData[i]%>
													<tr class='td_c1' style="height:40px;ling -moz-animation-duration: 40px;">
														<td>
															<%=item['name']%>
														</td>
														<td style="text-align: left;text-indent: 100px;">
															<%var specArrayList = item['value'].split(',');%>
															<%for(var result in specArrayList){%>
																[<%=specArrayList[result]%>]
															<%}%>
														</td>
														<td style="text-align: right;padding-right: 40px;"><a href="javascript:" onclick="$(this).parent().parent('tr').remove();"> 删除</a>
														</td>
														<input type="hidden" name="spec_id[]" value="<%=item['id']%>" />
													</tr>
													<%}%>
												</script>
											</tbody>
										</table>
									</div>
								</dl>
							</li>
						</ul>
					</dd>
				<div class="input1">
					<?php if(!empty($info)): ?>
						<input type="hidden" value="<?php echo $info['id'] ?>" name="id" />
						<input type="hidden" value="edit" name="opt" />
					<?php else: ?>
						<input type="hidden" value="add" name="opt" />
					<?php endif; ?>
					<input type="hidden" name="newAttrSearch" />
					<input type="hidden" name="AttrSearch" />
					<input class="button_search" type="submit" onclick="checkform()" value="提交">
					<input class="button_search" type="button" value="返回">
				</div>
				</dt>
			</dl>
			</form>
		</div>
		<?php include $this->admin_tpl("copyright"); ?>
	</div>
	<script src="<?php echo JS_PATH?>jquery.tagsinput.js" type="text/javascript" charset="utf-8"></script>
	<!--模板-->
	<script type="text/javascript" src="<?php echo JS_PATH?>/artTemplate/artTemplate.js"></script>
	<script type="text/javascript">
	<!--返回上一页-->
	$(".button_search[type=button]").bind('click',function() { 
	     history.back(-1);
	})
		init_template();
		//初始化
		function init_template() {
			//初始化属性
			var attrValueData = <?php echo json_encode($info['attrinfo'])?>;
			var attrRowHtml = template('attrRowTemplate',{'templateData':attrValueData});
		   	$('#attrBaseBody').html(attrRowHtml);
			$('.tags').tagsInput();	
			//初始化规格
			specValueData=<?php echo json_encode($info['spec'])?>;
			//specValueData = {};
			var specRowHtml = template('specRowTemplate',{'templateData':specValueData});
			$('#specBaseBody').html(specRowHtml);
		}
		//添加属性
		function addrow(){
			temp_id = $('#attrBaseBody>tr').length+1;
			var attrRowHtml = template('attrRowTemplate',{'templateData':[{'id':'0','opt':'add','search':'1','sort':'100','temp_id':temp_id}]});
		   	$('#attrBaseBody').append(attrRowHtml);
			$('#tags'+temp_id).tagsInput();	
		}
		//删除属性
		function delattr(self,id){
			if(id > 0){
				$(".addform").append("<input name='AttrDelId[]' value='"+id+"' type='hidden'>");
				$("input[name='AttrId[]'][value='"+id+"']").remove();   
			}
			$(self).parent().parent('tr').remove();
		}
	</script>
	<script type="text/javascript">		
		//表单 验证
		function checkform(){
			var arr = [];
				$("input[name='Search[]']").each(function() {
					if($(this).attr('checked') == 'checked'){
						arr.push("1");
					}else{
						arr.push("0");
					}
				})			  
			$("input[name='newAttrSearch'],input[name='AttrSearch']").val(arr.toString());
			console.debug(arr);
			return true;
		}
	</script>
	<script type="text/javascript">
	//分类选择
	$(function(){
		JsonCategory=<?php echo json_encode($this->tree) ?>;
		//初始化分类
		nb_category(0,'.root');
	})
	//显示分类
	function nb_category(pid,e){
		$(e).parent().nextAll('div').empty();
		$(e).parent().nextAll('div').css('background','#EFF7FE');
		$(e).parent("div").find("a").removeClass("hover");
		$(e).addClass("hover");
		var strHTML = "";
		$.each(JsonCategory,function(InfoIndex,Info){
			if(pid==Info.parent_id)
			strHTML+="<a href='javascript:void(0)' onclick='nb_category("+Info.id+",this)' id="+Info.id+">"+Info.name+"</a>";		   
		});
		if (pid==0){
			$(".root").html(strHTML);
		}else{
			$(e).parent().next('div').css('background','#FFF');
			$(e).parent().next('div').html(strHTML);
		}
	}
	//判断是否重复选择
	function nb_checkjiafen(val){
	var arr = [];
		$('input[name="category_id[]"]').each(function(){   
				arr.push($(this).val());		
			});  
			var t1=arr.sort().toString();
			var t2=$.unique( arr ).sort().toString();
			if( t1==t2 ){
			return false;
		 }else{
			return true;
		 }
	}
	$(function(){
		$("#jiafen").live("click",function(){
			var id=$('.fendd').find('.hover:last').attr("id");
			if($('.fendd .hover').length>1){
				$(".sl").append("<div>"+ $('.fendd').find('.hover:last').html() +"<em><img src='<?php echo IMG_PATH;?>admin/ico_close1.png' /></em><input name='category_id[]' value='"+$('.fendd').find('.hover:last').attr("id")+"' type='hidden'></div>");
			}   
			if(nb_checkjiafen(id)){
				$(".sl div:last").remove();
				alert('已选择过此类别!');
				return;
			}
		});
	$('.fendd a').live("dblclick",function(){
		//if($(this).parent().nextAll('div').text() == ""){
			$("#jiafen").click();
		//}else{
		//	return;
		//}
	});
	$(".sl div em").live("click",function(){
		$(this).parent("div").remove();
	});
	});
	//切换
	$(function() {
		var tabTitle = ".tabs dt a";
		var tabContent = ".tabs dd";
		$(tabTitle + ":first").addClass("hover");
		$(tabContent).not(":first").hide();
		$(tabTitle).unbind("click").bind("click", function() {
			$(this).siblings("a").removeClass("hover").end().addClass("hover");
			var index = $(tabTitle).index($(this));
			$(tabContent).eq(index).siblings(tabContent).hide().end().fadeIn(0);
		});
		//默认选中
		$(".tabs dt a").eq("{$showpage}").siblings("a").removeClass("hover").end().addClass("hover");
		$(".tabs dd").eq("{$showpage}").siblings(tabContent).hide().end().fadeIn(0);
	});
	//添加规格
	function selSpec(){
		var tempUrl = '<?php echo U('Goods/Goods/search_spec_main');?>';
		var model_id = $('[name="model_id"]').val();
		var goods_id = $('[name="id"]').val();
		tempUrl = tempUrl.replace('@model_id@',model_id);
		tempUrl = tempUrl.replace('@goods_id@',goods_id);
		art.dialog.open(tempUrl,{
			title:'设置商品的规格',
			okVal:'确认选择',
			width:620,
			background: '#ddd',
			opacity:0.3,
			ok:function(iframeWin, topWin)
			{
				var addSpecObject = $(iframeWin.document).find("[name='spec_ids[]']:checkbox:checked");
				var specValueData = {} ;
				addSpecObject.each(function() {
					var data_id = parseInt($(this).val());
					var data_name = $(this).attr('data-name');
					var data_value = $(this).attr('data-value');
					var data_type = $(this).attr('data-type');
					if(typeof(specValueData[data_id]) == 'undefined'){
						specValueData[data_id] = [];
					}
					specValueData[data_id]={'id':data_id,'name':data_name,'value':data_value,'type':data_type};
				})  
				//创建选择规格表格
				var specRowHtml = template('specRowTemplate',{'templateData':specValueData});
				$('#specBaseBody').html(specRowHtml);
			}
	});
}
$(function(){
	var input_length = $(".pro_nature_input2").length;
	for(var i=0;i<input_length;i++){
		if($(".pro_nature_input2").eq(i).val() == 3){
			$(".pro_nature_input2").eq(i).parent().next().find('input').attr('readonly','readonly');
			$(".pro_nature_input2").eq(i).parent().next().next().find('input').attr('disabled','disabled').removeAttr('checked');
		}
	}
	$(".pro_nature_input2").change(function(){
	   var option_val = $(this).val();
       if(option_val == '3'){
		   $(this).parent().next().find('input').attr('readonly','readonly');
		   $(this).parent().next().next().find('input').attr('disabled','disabled').removeAttr('checked');
	   }else{
		   $(this).parent().next().find('input').removeAttr('readonly');
		   $(this).parent().next().next().find('input').removeAttr('disabled' );
	   }
     })	
})	
</script>