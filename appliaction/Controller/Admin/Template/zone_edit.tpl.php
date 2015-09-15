<?php include $this->admin_tpl('header'); ?>
<div class="content">
<style type="text/css">
input.dialog{height:0; height:0; font-size:0; line-height:0; border:none}
.edit_areabox{display: block;}
</style>
	<div class="edit_areabox">
		<div class='edit_areabox_1'>
			<span class="edit_areabox_1_T">地区名称</span>
			<input type="text" name="name" value="<?php echo $info['name'] ?>" /> 请选择下列包含的区域
		</div>
		<!--选择省份-->
		<div class="edit_areabox_city">
		<?php foreach ($region_lists as $region): ?>
			<li <?php if (in_array($region['area_id'], $this->regionids)): ?>class="disabled"<?php endif; ?>>
				<label><input type="checkbox" name="provinces[]" id="area_id_<?php echo $region['area_id'];?>" value="<?php echo $region['area_id'] ?>"<?php if (in_array($region['area_id'], $provinces)): ?> checked<?php endif ?><?php if (in_array($region['area_id'], $this->regionids)): ?>disabled<?php endif; ?>/>
				<span><?php echo $region['area_name'] ?></span></label>
			</li>
		<?php endforeach ?>
		</div>
	</div>
<script type="text/javascript">
function dosubmit() {
	var provinces =[];
		$('input[name="provinces[]"]:checked').each(function(){
		provinces.push($(this).val());
	});
	$.post("<?php echo U('edit')?>", {id:'<?php echo $id; ?>', name:$("input[name='name']").val(), provinces:provinces}, function(result) {
		if (result.status == 1) {
			try{
				$('#area_box').window('close');
			}catch(e){
			}
			dom.datagrid("reload");
		} else {
		}
		return;
	}, 'JSON');
}
</script>
</body>
</html>