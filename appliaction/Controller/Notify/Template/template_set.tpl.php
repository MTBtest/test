<?php  include $this->admin_tpl('header'); ?>
<div class="content">
	<style>
		#Validform_msg{display: none}
	</style>
	<div class="site">
		Haidao Board <a href="#">通知模版设置</a> > 模版设置 > <?php echo $result['name']; ?>设置
	</div>
	<span class="line_white"></span>
	<div class="install tabs mt10">
		<dl>
			<dt>
				<?php foreach ($notifys as $k => $notify) { ?>
					<a href="javascript:"><?php echo $notify['name'] ?></a>
				<?php } ?>
			</dt>
			<form name="form" method="post" action="<?php echo U(ACTION_NAME) ?>">
				<input type="hidden" name="notify_id" value="<?php echo $id; ?>">
				<?php foreach ($notifys as $k => $notify) { ?>
				<dd>
					<?php  include $this->admin_tpl('template_'.$notify['code']); ?>
				</dd>
				<?php } ?>				
				<div class="submit">
					<input type="submit" value="提交" class="button_search">
					<a href="<?php echo U('template') ?>">返回</a>
				</div>
			</form>
		</dl>

	<script>  
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
	</script>
<?php include $this->admin_tpl('copyright') ?>