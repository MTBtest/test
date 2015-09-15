<?php include $this->admin_tpl('header'); ?>
<div class="content">
	<div class="site">
	    Haidao Board <a href="#">内容管理</a> >
	     <?php if(!empty($info['id'])):?>
		    	编辑文章
		    <?php else:?>
		    	添加文章
	    	<?php endif;?>
	</div>
<span class="line_white"></span>
<div class="install mt10">
    <dl>
        <dd>
            <div class="install mt10">
                <div class="install mt10">
                    <dl>
                        <form action="<?php echo U('Article_category/update') ?>" class="addform" method="post">
                            <dd>
                                <ul class="web">
                                    <li>
                                        <strong>分类名称：</strong>
                                        <input type="text" class="text_input" name="name" placeholder='' datatype="*" nullmsg='请填写分类名称' value="<?php echo $info['name'] ?>" /><span class="Validform_checktip" style="margin-left:-2px;">填写文章分类名称</span>
                                    </li>
                                    <li>
                                        <strong>顶级分类：</strong>
                                        <select name="parent_id" style="margin-right: 95px;">
                                            <option value="0">顶级分类</option>
                                            <?php $pid = isset($pid) ? $pid : $info['parent_id'] ?>
                                            <?php foreach ($catTree as $v): ?>
                                                <option value="<?php echo $v['value'] ?>"  <?php if ($pid == $v['value']): ?>selected<?php endif; ?>><?php echo $v['text'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span style="margin-left:-50px;">
                                        	选择分类隶属哪个文章分类下
                                        </span>
                                    </li>
                                    <li>
                                        <strong>状态：</strong>
                                        <?php $info['status'] = isset($info['status']) ? $info['status'] : 1; ?>
                                        <input type="radio" name="status" value="1" <?php if (($info['status'] ? $info['status'] : 1) == 1) echo "checked" ?> /> 显示 <input type="radio" name="status" value="0" <?php if ($info['status'] == 0) echo "checked" ?>  />禁用
                                        <span class="Validform_checktip" style="margin-left: 216px;">禁用文章分类，用户在前台将看不到此条分类</span>
                                    </li>
                                    <li>
                                        <strong>排序：</strong>
                                        <input type="text" class="text_input" name="sort" placeholder=''  datatype="n"  nullmsg='请填写分类排序' value="<?php echo $info['sort'] ? $info['sort'] : 100 ?>" /><span class="Validform_checktip" style="margin-left:-2px;">输入数字改变文章分类显示排序，数字越小越靠前</span>
                                    </li>
                                </ul>
                                <div class="submit">
                                    <?php if(!empty($info['id'])){ ?>
                                        <input type="hidden" value="<?php echo $info['id'] ?>" name="id" />
                                        <input type="hidden" value="edit" name="opt" />
                                    <?php }else{ ?>
                                        <input type="hidden" value="add" name="opt" />
                                    <?php }; ?>
                                    <input type="submit" class="button_search" value="提交"/>
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
<script>
    $(function () {
        var demo = $(".addform").Validform({
            btnSubmit: "#btn_sub",
            btnReset: ".btn_reset",
            tiptype: 3,
            label: ".label",
            showAllError: false,
            tiptype:function (msg, o, cssctl) {
                var e = o.obj.context.name;
                if (e.length > 1 && o.type == 3) {
                    if (e == 'content') {
                        alert(msg);
                    }
                }
            }
        });
    });
</script>