<?php include $this->admin_tpl("header"); ?>
<div class="content">
	<div class="site">Haidao Board <a href="#">内容管理</a> > 编辑发货单打印模板</div>
	<span class="line_white"></span>
    <div class="install mt10">
        <dl>            
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="<?php echo U('edit') ?>" class="addform" method="post">
                                <input type="hidden" name="id" value="<?php echo $info['id']?>"/>
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>模发货单模板名称：</strong>
                                            <input type="text" class="text_input" name="parcel_name" placeholder='' datatype="*" value="<?php echo $info['parcel_name'] ?>" /><span class="Validform_checktip ">请填写标题，支持中文、英文和数字，4-10位
                                            </span>
                                        </li>
                                    </ul> 
                                    <div class="edit_box">  
                                        <strong class="edit">您正在编辑发货单模版，默认所见即所得模式，您也可以点击HTML源码切换到代码模式进行编辑。</strong> 
                                        <div class="editor edit"><?php echo form::editor('content', $info['content']);?></div>
                                    </div>
                                    <div class="submit">
									    <input type="submit" class='button_search' value='提交'/>
										<a href="<?php echo U('index')?>">返回</a>
									</div>
                                </dd>
                            </form>  
                        </dl>
                    </div>
            </dd>
        </dl>
    </div>
    <?php include $this->admin_tpl('copyright'); ?>
</div>
