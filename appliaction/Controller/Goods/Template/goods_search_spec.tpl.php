<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="x-ua-compatible" content="ie=7" />
<title></title>
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>admin/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>font-awesome.css" />
<!--[if IE 6]>
<script type="text/javascript" src="{:THEME_PATH}js/DD_belatedPNG.js" ></script>
<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>Validform_v5.3.2_min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>Validform_Datatype.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>artDialog/artDialog.js?skin=default"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>artDialog/plugins/iframeTools.js" ></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>common.js"></script>
<style>  
.content {
    background: none repeat scroll 0 0 #fff;
    padding: 0px;
    min-width: 0px;
    height: auto;
    margin: 0;
    border: 0;
    overflow: hidden;
}
.aui_main{
	text-align: left;
}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>admin/spec.css" />
<script src="<?php echo JS_PATH; ?>jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
<block name="body">
		<!--规格选择框-->
		<div class="spec_box aui_main" id="testID2">
			<div class="spec_T2">
				<p class="fl">请选择规格(可多选)</p>
				<p class="fl">请勾选规格属性(可多选)</p>
			</div>
			<div class="spec_body">
				<!--规格-->
				<div class="spec_body_L fl">
					<ul class="spec_names">
						<?php foreach ($list as $key => $value): ?>
							<label>
								<li>
									<span data-id="<?php echo $value['id'] ?>">
										<?php echo $value['name'] ?><span class="spec_cl">[0]</span>
									</span>
								</li>
							</label>
						<?php endforeach ?>
						<label>
							<li class="newgg">
								<span>添加新规格</span>
							</li>
						</label>
					</ul>
				</div>
				<!--规格属性-->
				<div class="spec_body_R fl spec_values">
					<!--全選規格-->
					<div class="spec_body_R_title">
						<div class="spec_dafult">
							<label>
								<input type="checkbox" id="check_all" autocomplete="off" />
								<span>全选</span>
							</label>
						</div>
						<!-- 添加新属性 -->
						<div class="spec_add_input">
							<input class="input" type="text" name="new_value" placeholder='新属性名称(多个用" , "分割)' />
							<input class="btn" type="button" id="sub_value" value="确定" />
						</div>
						<div class="spec_add_info">
							<span>请填写您要添加的新规格名称</span>
						</div>
					</div>
					<p class="pation">请选择左边规格列表</p>
					<?php foreach ($list as $key => $data): ?>
						<ul class="spec_defalut_ul">
							<?php foreach ($data['values'] as $k => $value): ?>
								<li class="spec_nature" data-id="<?php echo $data['id']; ?>" data-value="<?php echo $value ?>" data-name="<?php echo $data['name']; ?>" data-type="<?php echo $data['type']; ?>"><p><?php echo $value; ?></p></li>
							<?php endforeach ?>
							<li class="addsx" data-id="<?php echo $data['id']; ?>"><p>添加新属性</p></li>
						</ul>
					<?php endforeach ?>
					<!-- 新规格 -->
					<ul style="display: none;">
						<li class="spec_add_gg">								
							<input class="input" type="text" name="new_spec" placeholder="请输入新规格名称" />
							<input class="btn" type="button" id="sub_spec" value="确定" />
						</li>
					</ul>
				</div>
			</div>
		</div>
</block>

<script type="text/javascript">
	//显示已选择的项目
	$(document).ready(function(){
		$.each(parent.selectedItem,function(key,val){
			$("li[data-id='"+val.id+"'][data-value='"+val.value+"']").addClass('hover');
			var num = parseInt($("span[data-id='"+val.id+"'] .spec_cl").text().replace('[','').replace(']',''));
			$("span[data-id='"+val.id+"'] .spec_cl").text('['+(++num)+']');
		});
	});
	//隐藏规格值
	$(".spec_values >ul").css({display:'none'});
	//根据选择规格显示相应值容器
	$(".spec_names li").each(function(i) {
		var num = i;
		$(this).on("click", function() {
			$(".pation").hide();
			$("#check_all").attr("checked", false);
			$(".spec_names li").removeClass("hover");
			$(this).addClass("hover");
			$(".spec_values >ul").css({
				display: 'none'
			});
			$(".spec_values >ul:eq(" + num + ")").css({
				display: ''
			});
			if($(".spec_names li").index(this)==$(".spec_names li").length-1){
				$(".spec_dafult").hide();
				$(".spec_add_input").hide();
				$(".spec_add_info").show();
			}else{
				$(".spec_dafult").show();
				$(".spec_add_input").hide();
				$(".spec_add_info").hide();
			}
			spec_select();
		})
	})
	/**
	 *选择规格值处理
	 */
	function spec_select() {
		var current_spec = $(".spec_values ul:visible");
		var num = 0;
		$(".spec_nature",current_spec).each(function(){
			if($(this).hasClass("hover")){
				num++;
			}
		});
		$(".spec_names .spec_cl").eq(current_spec.index()-2).text(num<0?"[0]":"["+num+"]");
		if($(".spec_nature",current_spec).length==num && num>0){
			$("#check_all").attr("checked",true);
		}else{
			$("#check_all").attr("checked",false);
		}
	}
	//全选
	$("#check_all").on("click",function(){
		if($(this).is(":checked")){
			$(".spec_values ul:visible").find("li").addClass('hover');
		}else{
			$(".spec_values ul:visible").find("li").removeClass('hover');
		}
		spec_select();
	});
	
	//点击规格值
	$(".spec_nature").click(function(){
		if($(this).hasClass("hover")){
			$(this).removeClass('hover');
		}else{
			$(this).addClass('hover');
		}
		spec_select();
	})
	//添加新属性
	$(".addsx").click(function(){
		if($(this).hasClass("hovered")){
			$(this).removeClass('hovered');
			$(".spec_dafult").show();
			$(".spec_add_input").hide();
			$(".spec_add_info").hide();
			$(this).find('p').html('添加新属性');
		}else{
			$(this).addClass('hovered');
			$(".spec_dafult").hide();
			$(".spec_add_input").show();
			$(".spec_add_info").hide();
			$(".spec_add_input").find('input:first').attr('data-id',$(this).attr('data-id'));
			$(this).find('p').html('取消添加');
		}		
	});
	// 提交添加新规格
	$('#sub_spec').bind('click',function() {
		var new_spec = $.trim($('input[name="new_spec"]').val());
		if (new_spec.length < 1) {
			alert('请填写新的规格名称');
			return false;
		}
		$.post('<?php echo U("Goods/Goods/goods_add_spec") ?>',{
			name : new_spec
		},function(ret){
			if (ret.status == 1) {
				alert('新规格添加成功');
				location.reload();
			} else {
				alert(ret.info);
				return false;
			}
		},'json');

	});
	// 提交添加新属性
	$('#sub_value').bind('click',function() {
		var new_value = $.trim($('input[name="new_value"]').val());
		var spec_id = $('input[name="new_value"]').attr('data-id');
		if (new_value.length < 1) {
			alert('请填写新的属性值，多个用" , "分开');
			return false;
		}
		if (spec_id < 1) {
			alert('该规格不存在或规格ID有误');
			return false;
		}
		$.post('<?php echo U("Goods/Goods/goods_add_value") ?>',{
			spec_id : spec_id,
			new_value : new_value
		},function(ret){
			if (ret.status == 1) {	
				alert(ret.info);
				location.reload();				
			} else {
				alert(ret.info);
				return false;
			}
		},'json');
	});
</script>