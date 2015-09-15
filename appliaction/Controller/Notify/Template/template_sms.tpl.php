<ul class="web p3">
<?php if (empty($cloud['token'])): ?>	
	<li><strong>请先绑定云平台账号，<a href="<?php echo U('Admin/Cloud/index') ?>">去绑定</a></strong></li>
<?php elseif(is_string($cloud['token'])): ?>
	<?php if (is_array($_result['result'])): ?>
		<?php foreach ($_result['result'] as $k => $temp) : ?>		
			<li class="message-list">
	        	<label>
	        		<input type="radio" name="<?php echo $notify['code'] ?>" value="<?php echo $temp['id'] ?>" <?php if($template[$notify['code']] == $temp['id']){echo "checked";} ?>/><?php echo $temp['title'];?>
	        	</label>
	        	<p class="tips"><?php echo $temp['content'];?></p>
	       	</li>
		<?php endforeach; ?>
	<?php else: ?>
		<li><strong>暂无可用模版！</strong></li>
	<?php endif; ?>
<?php endif; ?>
</ul>
<style>
ul.web{
	padding-left: 0;
}
li.message-list{
	position: relative;
	padding: 20px 0;
}
.message-list label{
	position: absolute;
	left: 15px;
	top: 20px;
	width: 160px;
	color: #2d689f;
	cursor: pointer;
}
.message-list p{
	float: left;
	width: 100%;
	padding-left: 160px; 
	margin-left: 20px;
	box-sizing: border-box;
}
</style>