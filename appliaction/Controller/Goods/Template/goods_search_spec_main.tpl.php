<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=7" />
        <title></title>
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>admin/style.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>font-awesome.css" />
        <script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-1.7.2.min.js"></script>
		<style> 
		 .content {
		  	background:#DDDDDD;
		  	padding: 0px;
		  	min-width: 0px;
		  	height: auto;
		  	margin: 0;
		  	border: 0;
		  	overflow: hidden;
		  }
		  .spec_nature.hover{cursor: default;}
		  .aui_main {
		  	text-align: left;
		  }
		</style>
		<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>admin/spec.css" />
</head>
<body>
		<!--规格选择框-->
		<div class="spec_box aui_main" id="testID2">
			<div class="spec_T2">
				<p class="fl">请选择规格(可多选)</p>
			</div>
			<div class="spec_body">
				<!--规格-->
				<div class="spec_body_L fl">
					<ul class="spec_names">
						<?php foreach ($list as $key => $value): ?>
							<label for="li_{$i}">
								<li>
									<span data-id="<?php echo $value['id'] ?>">
										<input type="checkbox" name="spec_ids[]" value="<?php echo $value['id'] ?>" data-name="<?php echo $value['name']; ?>" data-value="<?php echo $value['value'] ?>" data-type="<?php echo $value['type']; ?>" />
										<?php echo $value['name'] ?><span class="spec_cl"></span>
									</span>
								</li>
							</label>
						<?php endforeach ?>
					</ul>
				</div>
				<!--规格属性-->
				<div class="spec_body_R fl spec_values">
					<!--全選規格-->
					<div class="spec_body_R_title">
						<span style="margin-left: 15px;">规格值列表</span>
					</div>
					<p class="pation">请选择左边规格列表</p>
					<?php foreach ($list as $key => $data): ?>
						<ul>
							<?php foreach ($data['values'] as $k => $value): ?>
								<?php if ($data['type'] == '1'): ?>
									<li class="spec_nature hover" data-id="<?php echo $data['id']; ?>" data-value="<?php echo $value ?>" data-name="<?php echo $data['name']; ?>" data-type="<?php echo $data['type']; ?>"><p><?php echo $value; ?></p></li>
								<?php else: ?>
									<li class="spec_nature hover" data-id="<?php echo $data['id']; ?>" data-value="<?php echo $value ?>" data-name="<?php echo $data['name']; ?>" data-type="<?php echo $data['type']; ?>"><div class="spec_img"><img src="<?php echo $value; ?>"/></div></li>
								<?php endif ?>
							<?php endforeach ?>
						</ul>
					<?php endforeach ?>
				</div>
			</div>
		</div>
</body>
</html>
	<script type="text/javascript">
	//显示已选择的项目
		$(document).ready(function(){
			$.each(parent.$("input[name='spec_id[]']"), function() {    
		        $("input[name='spec_ids[]'][value='"+$(this).val()+"']").prop('checked',true);                                                 
			});
		});
//隐藏规格值
$(".spec_values >ul").hide();
//根据选择规格显示相应值容器
$(".spec_names li").each(function(i) {
	var num = i;
	$(this).on("click", function() {
		$(".pation").hide();
		$(".spec_names li").removeClass("hover");
		$(this).addClass("hover");
		$(".spec_values >ul").hide();
		$(".spec_values >ul:eq(" + num + ")").show();
	})
})
</script>