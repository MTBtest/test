<?php include $this->admin_tpl('header'); ?>
<div class="content">
	<div class="site">Haidao Board <a href="#">会员管理</a> > 回复列表</div>
	<span class="line_white"></span>
	<div class="install mt10">
		<dl>
			<dd>
				<div class="install mt10">
					<form action="<?php echo U('reply') ?>" method="post">
					<input type="hidden" name="id" value="<?php echo $id ?>">
					<input type="hidden" name="goods_id" value="<?php echo $rs['goods_id'] ?>">
					<div class="install mt10">
						<dl>
							<dd>
								<ul class="web">
									<li>评论商品：<a href="<?php echo U('Goods/Index/detail', array('id' => $rs['goods_id'])) ?>" target="_blank" class="reply-pro"><?php echo $good_name; ?></a></li>
									<li>评论会员：<?php echo $user_name; ?><span>[ <?php echo mdate($rs['time']) ?> ]</span></li>
									<li>
										<div class="fl reply_T">评论内容：</div>
										<div class="fl reply-word"><?php echo $rs['content'] ?></div>
									</li>
									<li style="border-bottom: none;">
										<div class="fl reply_T" >回复内容：</div>
										<div class="fl reply-word" ><textarea name="reply" style="width: 1024px;"><?php echo $rs['reply'] ?></textarea></div>
									</li>
									<?php if ($rs['reply_time']): ?>
									<li>回复时间：<?php echo mdate($rs['reply_time']) ?></li>
									<?php endif ?>
									<li>审核状态：<label>
										<input type="radio" name="status" value="1" <?php echo ($rs['status'] == 1) ? 'checked' : '' ; ?>> 已审核</label>&nbsp;<label>
										<input type="radio" name="status" value="0" <?php echo ($rs['status'] == 0) ? 'checked' : '' ; ?>> 未审核</label></li>
								</ul>
								<div class="submit" style="margin-left: 78px;">
								 <input type="submit" class='button_search' value='提交'/>
								 <a href="<?php echo U('lists')?>">返回</a>
								</div>
							</dd>
						</dl>
					</div>
					</form>
				</div>
			</dd>
		</dl>
		<div class="clear"></div>
		<?php include $this->admin_tpl('copyright'); ?>
	</div>
</div>
</body>
</html>