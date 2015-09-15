<?php  include $this->admin_tpl('header'); ?>
<style>
#Validform_msg{display: none}
.web li span{padding-left: 0px;}
.blue_table table tr{line-height: 15px;}
.add_area{max-height: 347px;min-height: 240px;padding-bottom: 30px;}
div.tagsinput{height: 30px;overflow-y: auto;}
.fendd div a{background: url(images/ico_0.png) no-repeat 9px center;}
.fendd div a input{margin-right: 5px;}
em {font-style: normal;}
</style>
<link rel="stylesheet" href="<?php echo CSS_PATH ?>admin/common.css">
<div class="content">
    <div class="site">Haidao Board <a href="#">站点设置</a> > 物流配送增加</div>
    <span class="line_white"></span>
    <div class="install mt10">
        <form action="<?php echo U('add');?>" class="addform" method="post">
            <dl>
                <dd>
                    <div class="sm">设置配送方式请根据实际配送方式进行配置，Haidao Board系统本身不提供物流公司设置</div>
                    <ul class="web">
						<li>
							<strong>是否开启当前配送方式：</strong>
							<b style="margin-right: 44px;">
								<label><input type="radio" name="status" value="1" 
									<?php if ($info['status'] == 1) {  ?>  checked  <?php } ?> /> 开启 </label>
								<label><input type="radio" name="status" value="0" 
								<?php if ($info['status'] == 0) {  ?>  checked  <?php } ?> /> 关闭 </label>
							</b>
							<span style="margin-left:3px";>设置是否开启当前配送方式，开启即可使用</span>
						</li>
						<li>
							<strong>配送方式名称：</strong>
							<input type="text" value="<?php echo $info['name']; ?>" name='name' class="text_input" datatype="*" /><span>设置配送方式名称，此处设置将在会员结算时显示，请根据实际情况填写，如宅急送</span>
						</li>
						<li>
							<strong>配送方式英文名称：</strong>
							<input type="text" value="<?php echo $info['enname']; ?>" name='enname' class="text_input"  /><span>请按照《API URL 所支持的快递公司及参数说明》设置</span>
						</li>
						<li id="li_type">
							<strong>设置配送方式类型：</strong>
							<b style="margin-right: 44px;">
								<label><input type="checkbox" name="type[]" value="0" 
									  <?php if (in_array('0', $info['type'])) {  ?>  checked  <?php } ?> data-type="pays"/> 先支付后发货 </label>
								<label><input type="checkbox" name="type[]" value="1" 
									<?php if (in_array('1', $info['type'])) {  ?>  checked  <?php } ?>  /> 先发货后付款 </label>
							</b>
							<span style="margin-left:3px";>先支付后发货类型需要用户先通过支付平台付款才能发货，先发货后付款类型即为货到付款模式</span>
						</li>
						<li <?php if (!in_array('0', $info['type'])): ?>style="display:none;"<?php endif ?> id="li_pays">
							<strong>请选择支持的支付方式</strong>
                            <?php if (!$pays): ?>
                               <label><a href="<?php echo U('Pay/Pay/manage') ?>">暂未开启任何支付方式，点击这里去配置.</a></label>
                            <?php else: ?>
                                <?php foreach ($pays as $code => $pay): ?>
                                    <label><input type="checkbox" name="pays[]" value="<?php echo $code ?>" <?php if (in_array($code, $info['pays'])): ?>checked<?php endif ?>/> <?php echo $pay['pay_name'] ?></label><br/>
                                <?php endforeach ?>
                            <?php endif ?>
						</li>
						<!--<li>
							<strong>配送方式重量设置：</strong>
							首重:<select id="select" name="weight1" style="margin-right: 24px;">
								<option value='0.5'  <?php if ($info['weight'][0] == 0.5) {  ?>  selected  <?php } ?> >0.5公斤</option>
								<option value='1'
								  <?php if ($info['weight'][0] == 1) {  ?>  selected  <?php } ?> >1公斤</option>
								<option value='1.5' 
								 <?php if ($info['weight'][0] == 1.5) {  ?>  selected  <?php } ?> >1.5公斤</option>
								<option value='2' 
								 <?php if ($info['weight'][0] == 2) {  ?>  selected  <?php } ?> >2公斤</option>
								<option value='2.5' 
								 <?php if ($info['weight'][0] == 2.5) {  ?>  selected  <?php } ?> >2.5公斤</option>
								<option value='3' 
								<?php if ($info['weight'][0] == 3) {  ?>  selected  <?php } ?> >3公斤</option>
							</select>
							续重:<select id="select" name="weight2" style="margin-right: 50px;">
								<option value='0.5'  <?php if ($info['weight'][1] == 0.5) {  ?>  selected  <?php } ?> >0.5公斤</option>
								<option value='1' 
								 <?php if ($info['weight'][1] == 1) {  ?>  selected  <?php } ?> >1公斤</option>
								<option value='1.5' 
								 <?php if ($info['weight'][1] == 1.5) {  ?>  selected  <?php } ?> >1.5公斤</option>
								<option value='2' 
								 <?php if ($info['weight'][1] == 2) {  ?>  selected  <?php } ?>>2公斤</option>
								<option value='2.5' 
								 <?php if ($info['weight'][1] == 2.5) {  ?>  selected  <?php } ?> >2.5公斤</option>
								<option value='3' 
								 <?php if ($info['weight'][1] == 3) {  ?>  selected  <?php } ?> >3公斤</option>
							</select><span style="margin-left:9px";>设置配送方式首重和续重的计重单位重量，默认首重和续重均为一公斤</span>
						</li>-->
						<li>
							<strong ><input  type="checkbox" onclick="$('#protectBox').toggle();" value="1" name="is_save_price" 
								<?php if($info) {?>  checked <?php } ?>
								>&nbsp;<span style="margin-top: 2px;padding-left: 0px;">是否开启物流保价：</span></strong>
							<div id='protectBox' 
							<?php if (empty($info)) {   ?>  style='display:none' <?php } ?>  >
								<input type="text" value="<?php echo intval($info['insure']) ?>" name="insure" class="text_input" style="margin-right: 43px;px;"/>
								<span>您可以当前配送方式设置物流保价费率，单位为%，默认为0</span></div>
						</li>
						<dl class="blue_table mt10">
							<dt style="height:42px; background: none repeat scroll 0 0 #E8F5FC;">
								<img src="<?php echo IMG_PATH;?>admin/area_add.png" style="padding:10px;float:left;cursor: pointer; " onclick="addrow(this, 0, 0)" />
								<span class="add" style="float:left;line-height: 44px;">点击添加区域模板，为每个区域设置对应的配送费用</span>
							</dt>
							<div>
								<table class="border_table">
									<thead>
										<!--商品标题模板-->
										<tr>
											<th style="width:25%;">配送区域</th>
											<th>配送费用</th>
											<!--<th>续重费用</th>-->
											<th>操作</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</dl>
						<li>
							<strong>配送方式排序：</strong>
							<input type="text" value="<?php if ($info['sortsort']): ?><?php echo $info['sortsort'] ?><?php else: ?>100<?php endif ?>" datatype="*,n" name='sort' class="text_input" /><span>设置配送方式排序，此处设置将会在会员选择配送方式时排序</span>
						</li>
						<li>
							<strong>支付方式描述：</strong>
							<textarea name='descript' style="margin-right: 50px;"><?php echo $info['descript'] ;?></textarea>
							<span style="margin-left:4px";>您可以在这里编辑支付方式的详细描述，将在用户结算时显示</span>
						</li>
					</ul>
				</dd>
			</dl>
			<div class="submit">
					<?php if(!empty($info)){ ?>
					 <input type="hidden" value="<?php echo $info['id']; ?>" name="id">
					<?php } ?>
				<input type="submit" value="提交" class="button_search" id="btn_sub">
				<a href="<?php echo U('lists')?>">返回</a>
			</div>
		</form>
	</div>
	<?php include $this->admin_tpl('copyright') ?>
</div>
<?php 
$zone_lists = M('Zone')->order("`id` ASC")->select();
?>
<div id="testID" style="display: none;">
	<div class="fendd clearfix" style="margin-right: 10px;">
		<div class="bgef hover" data-level="0">
		<?php foreach ($zone_lists as $k => $v): ?>
			<li class="li_1" data-areaid="<?php echo $v['id'] ?>" onclick="getShowBox(this)"><span id="<?php echo $v['id'] ?>"><?php echo $v['name'] ?></span></li>
		<?php endforeach ?>
		</div>
		<!--二级菜单出现添加hover样式-->
		<div class="bgef hover" data-level="1"></div>
		<div class="bgef hover" data-level='2'></div>
		<div class="bgef hover" data-level='3'></div>
	</div>
</div>
<script text="text/javascript">
$("#li_type input[data-type=pays]").click(function() {
	if($(this).attr('checked')) {
		$("#li_pays").show();
	} else {
		$("#li_pays").hide();
	}
});
var ids = Array(), vals = Array();
var ischked = false;
var item_id = 0;
//添加帮助
function addrow(obj, type, pid) {
	var tr_id = parseInt(Math.random() * 1000000);
	var temp = "<tr class='area_item' id='"+ tr_id +"'>"
			+"<td><span style=\"float: left; color: #0F0F0F;text-indent: 30px;\" class=\"area_val\">请选择配送地区</span></td>"
			+"<td><span class=\"btnArea\" style=\"color:#2d689f;margin-left: -50px;cursor:pointer\" onclick=\"_dialog(this)\">编辑区域</span><input class=\"small\" name=\"delivery_region[add][weight][]\" type=\"text\" value=\"\" /><input type=\"hidden\" name=\"delivery_region[add][area_id][]\"></td>"
			//+"<td><input class=\"small\" name=\"delivery_region[add][price][]\" type=\"text\" datatype=\"num\" value=\"\" /></td>"
			+"<td><a href=\"javascript:void(0)\" onclick=\"removeRow(this)\">删除</a></td>"
			+"</tr>";
	$(obj).parent().next().children().find('tbody').append(temp);
}
function removeRow (obj) {
	$(obj).parents('tr.area_item').remove();
}
//编辑区域
function _dialog(o) {
	item_id = $(o).parents('tr').attr('id');
	init();
	art.dialog({
		padding: '5px ',
		id: 'testID',
		background: '#ccc',
		opacity: 0.35,
		title: '选择地区',
		fixed:true,
		lock:true,
		content:document.getElementById('testID'),
		ok: function () {
			return _insert(o);
		},
		cancel: true
	});
}
function init() {
	for (var i = 0; i < 4; i++) {
		$("div.bgef[data-level="+ (i + 1) +"] ul").hide();//隐藏所有下级
	}
}
/* 插入数据 */
function _insert(o) {
	var in_area_id = Array();
	var in_area_val = Array();
	$('.fendd input[item_id='+item_id+'][ischk=true]').each(function(i){
		in_area_id.push($(this).attr('value'));
		in_area_val.push($(this).parents('li').find('span').text());
	});
	in_area_id = in_area_id.toString();
	in_area_val = in_area_val.toString();
	if (in_area_id.length < 1) {
		alert('请选择配送地区');
		return false;
	}
	$(o).parents('td').find("input[type=hidden]").attr('value', in_area_id);
	$(o).parents('tr').find("span.area_val").html(in_area_val);
	return true;
}
/* 获取区块内容 */
function getShowBox(obj) {
	var level = parseInt($(obj).parents('.bgef').attr('data-level')),//当前层级
		area_id = $(obj).attr('data-areaid');
	var _this = "div.bgef[data-level="+ (level + 1) +"]";
	$(obj).parents('.bgef').find('li').removeClass('hover');
	$(obj).addClass('hover');
	if(level > 2) {
		return false;
	}
	ischked = false;
	return getarealist(level, area_id);
}
/* 获取地区列表 */
function getarealist(level, area_id) {
	if (level == 3) return false;
	var _this = "div.bgef[data-level="+ (level + 1) +"]";
	var _this_input = "div.bgef[data-level="+ level +"] li[data-areaid="+ area_id +"] input";
	for (var i = level; i < 4; i++) {
		$("div.bgef[data-level="+ (i + 1) +"] ul").hide();//隐藏所有下级
	}
	last_id = $(_this_input).val();
	var chked = (ischked == true || $(_this_input).attr('checked')) ? ' checked' : '';
	var dised = ($(_this_input).attr('checked') && $(_this_input).attr['item_id'] != item_id && $(_this_input).attr['ischk'] == true) ? ' disabled="disabled"' : '';
	if($(_this + ' ul#area_'+area_id).length > 0) {
		$(_this + ' ul#area_'+area_id + ' input').each(function(i) {
			if ($(_this_input).attr('checked')) {
				$(this).attr('item_id', $(_this_input).attr('item_id'));
			} else {
				//$(this).attr('item_id', 0);
			}
			if ($(this).attr('checked') && parseInt($(this).attr('item_id')) > 0 && $(this).attr('item_id') != item_id) {
				$(this).attr("disabled","disabled");
			}
		});
		$(_this + ' ul#area_'+area_id).show();
		return false;
	}
	var _html = '';
	$.ajax({
		type: "post",
		async: false,
		url: "<?php echo U('public_ajax_delivery') ?>",
		data: {level:level, area_id:area_id},
		dataType: "json",
		success: function (ret) {
			var item =  (chked) ? $(_this_input).attr('item_id') : 0;
			_html += '<ul id="area_'+ area_id +'" data-parentid="'+area_id+'">';
			$.each(ret, function(i, n) {
				var dised = '';
				if (level > 0 && parseInt($(_this_input).attr['item_id']) > 0 && $(_this_input).attr['item_id'] != item_id || (item > 0 && item_id != item)) {
					dised = ' disabled="disabled"';
				}
				_html += '<li class="li_1" data-areaid="'+ n.area_id +'" onclick="getShowBox(this)"><input type="checkbox" name="area_id[]" value="'+ n.area_id +'" item_id="'+ item +'" onclick="setChecked(this)" '+ chked + dised +'><span id="' + n.area_id + '">'+ n.area_name +'</span></li>';
			});
			_html += '</ul>';
			$(_this).append(_html);
		},
		error: function (err) {
			alert(err);
		}
	});
}
/* 勾选窗体并赋值 */
function setChecked(obj) {
	var level = parseInt($(obj).parents('.bgef').attr('data-level'));
	var area_id = $(obj).attr('value');
	ischked = ($(obj).attr('checked')) ? true : false;
	getarealist(level, area_id);
	for (var i = level; i < 4; i++) {
		$("div.bgef[data-level="+ (i + 1) +"] ul").hide();//隐藏所有下级
	}
	childCk(level, area_id, $(obj).attr('checked'));
	parentCk(level, area_id, $(obj).attr('checked'));
}
/* 循环上一级 */
function parentCk(level, area_id, t) {
	if (level < 2) return false;
	l = level - 1;
	/* 当前表单 */
	var _this_chk = "div.bgef[data-level="+ level +"] li[data-areaid="+area_id+"] input";
	 /* 获取上级ID */
	var parent_id = $(_this_chk).parents('ul').attr('data-parentid');
	/* 所有同级 */
	var _this_input = "div.bgef[data-level="+ level +"] ul[data-parentid="+parent_id+"] input";
	/* 所属上级 */
	var _this_parent = "div.bgef[data-level="+ l +"] li[data-areaid="+ parent_id +"] input";
	if (t == 'checked') {
		$(_this_chk).attr('item_id', item_id);
	} else {
		$(_this_chk).attr('item_id', 0);
	}
	if ($(_this_input).length == $(_this_input + ':checked').length) {
		$(_this_parent).prop('checked', true);
		if ($(_this_input).length == $(_this_input + '[item_id='+ item_id +']:checked').length) {
			$(_this_parent).attr('ischk', true);
			$(_this_input).attr('ischk', false);
		}
	} else {
		$.each($(_this_input + '[item_id='+ item_id +']'), function(i, n) {
			if ($(this).attr('checked') == 'checked') {
				$(this).attr('ischk', true);
			} else {
				$(this).attr('ischk', false);
			}
		});	
		$(_this_parent).prop('checked', false);
		$(_this_parent).attr('ischk', false); 
	}
	parentCk(l, parent_id, t);
}
/* 循环下级 */
function childCk(level, area_id, t) {
	if (level == 3) return false;
	var l = level + 1;
	/* 当前表单 */
	var _this_chk = "div.bgef[data-level="+ level +"] li[data-areaid="+area_id+"] input";
	 /* 获取上级ID */
	var parent_id = $(_this_chk).parents('ul').attr('data-parentid');
	/* 所有同级 */
	var _this_input = "div.bgef[data-level="+ level +"] ul[data-parentid="+parent_id+"] input";
	/* 所有下级 */
	var _this_child = "div.bgef[data-level="+ l +"] ul[data-parentid="+area_id+"] input";
	if (t == 'checked') {
		$(_this_child+'[item_id=0]').attr('item_id', item_id);
		$(_this_child+'[item_id='+ item_id +']').prop('checked', true);
		if ($(_this_child+'[item_id='+ item_id +']:checked').length == $(_this_child).length) {
			$(_this_child+'[item_id='+ item_id +']:checked').attr('ischk', false);
			$(_this_chk).attr('item_id', item_id);
		} else {
			$(_this_child+'[item_id='+ item_id +']:checked').attr('ischk', true);
			$(_this_chk).attr('item_id', 0);
		}
		$(_this_chk).prop('checked', true);
	} else {
		$(_this_child+'[item_id='+ item_id +']').prop('checked', false);
		$(_this_child+'[item_id='+ item_id +']').attr('item_id', 0);
		$(_this_chk).attr('item_id', 0);
		$(_this_chk).prop('checked', false);
		$(_this_chk).attr('ischk', false);
	}
	$.each($(_this_input + '[item_id='+ item_id +']'), function(i, n){
		if ($(this).attr('checked') == 'checked') {
			$(_this_chk).attr('ischk', true);
		} else {
			$(_this_chk).attr('ischk', false);
		}
	});
}
</script>
