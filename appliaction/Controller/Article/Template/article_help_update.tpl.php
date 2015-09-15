<?php include $this->admin_tpl('header'); ?>
<div class="content">
	<div class="site">
		Haidao Board <a href="#">内容管理</a> > 编辑帮助
	</div>
	<span class="line_white"></span>
<div class="install mt10">
	<dl>
		<dd>
			<div class="install mt10">
				<div class="install mt10">
					<dl>
						<form action="<?php echo U('ArticleHelp/edit') ?>" class="addform" method="post" >
							<dd>
								<ul class="web">
									<li>
										<strong>分类主题：</strong>
										<input type="text" class="text_input" name="title" placeholder='' datatype="*" nullmsg='请填写分类名称' value="<?php echo $info['title'] ?>" /><span class="Validform_checktip ">请填写分类主题，支持中文、英文和数字，4-10位
										</span>
									</li>
									<li>
										<strong>状态：</strong>
										<?php $info['status'] = isset($info['status']) ? $info['status'] : 1; ?>
										<input type="radio" name="status" value="1" <?php if($info['status']==1) echo "checked=checked" ?> /> 显示 <input type="radio" name="status" value="0" <?php if($info['status']==0) echo "checked=checked" ?>  /> 禁用<span class="Validform_checktip" style="margin-left:222px;">请选择状态情况，默认为显示状态</span>
									</li>
									<li>
										<strong>排序：</strong>
										<input type="text" class="text_input" name="sort" placeholder=''  datatype="n"  nullmsg='请填写分类排序' value="<?php echo $info['sort'] ? $info['sort'] : 100 ?>" /><span class="Validform_checktip">请填写分类排序</span>
									</li>
								</ul>
									<?php if($info['fpid']!=0):?>
										<div class="edit_box">
										    <strong class="edit">您正在编辑当前商品内容，默认为所见即所得模式，您也可以点击HTML切换到代码模式进行编辑。</strong>
											<div class="editor edit">
												<?php
												echo form::editor('message', $info['message']);
												?>
											</div>
										</div>
									<?php endif;?>
								<div class="submit">
									<input name="id" type="hidden" value="<?php echo $id?>" />
									<input type="submit" class='button_search' value='提交'/>
									<a href="<?php echo U('lists')?>">返回</a>
								</div>
							</dd>
						</form>
					</dl>
				</div>
			</div>
		</dd>
	</dl>
</div>
<?php include $this->admin_tpl('copyright'); ?>
</div>
