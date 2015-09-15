<?php include $this->admin_tpl("header"); ?>
    <style>
        #Validform_msg{display: none}
        /*编辑器冲突*/
         .editor .ke-toolbar span,.editor .ke-statusbar span{
            padding:0px;
        }
        .editor .ke-toolbar .ke-outline {
            border: 1px solid #F0F0EE;
            cursor: pointer;
            display: block;
            float: left;
            font-size: 0;
            line-height: 0;
            margin: 1px;
            overflow: hidden;
            padding: 1px 2px;
        }
    </style>
<div class="content">
	<div class="site">
	    Haidao Board <a href="#">内容管理</a> >
	    <?php if($_GET['opt'] == 'add'):?>
	    	添加处理
	    <?php else:?>
	    	编辑公告
    	<?php endif;?>
	</div>
	<span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="<?php echo U('ArticleAnnounce/update') ?>" class="addform" method="post">
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>标题：</strong>
                                            <input type="text" class="text_input" name="title" placeholder='' datatype="*" value="<?php echo $info['title'] ?>" /><span class="Validform_checktip ">请填写标题，支持中文、英文和数字，4-10位
                                            </span>
                                        </li>
                                        <li>
                                            <strong>是否显示：</strong>
                                            <input type="radio" name="status" value="1" 
                                            <?php 
                                            if (($info['status'] ? $info['status'] : 1) == 1){
                                            	echo 'checked="checked"';
                                            }
                                            ?>
                                             /> 显示
                                            <input type="radio" name="status" value="0" 
                                            <?php 
                                            if ($info['status'] == 0){
                                            	echo 'checked="checked"';
                                            }
                                            ?>
                                             /> 禁用<span class="Validform_checktip"></span>
                                        </li>
                                        <li>
                                            <strong>排序：</strong>
                                            <input type="text" class="text_input" name="sort" placeholder=''  datatype="n" value="<?php echo $info['sort'] ? $info['sort'] : 100 ?>"/><span class="Validform_checktip">数字越小越靠前数字越大越靠后</span>
                                        </li>
                                    </ul>
                                     <div class="edit_box">
                                        <strong class="edit">您正在编辑当前商品详细信息，默认所见即所得模式，您也可以点击HTML源码切换到代码模式进行编辑。</strong>
                                        <div class="editor edit">
                                            <?php echo form::editor('content',$info["content"]);?>
                                        </div>
                                    </div>
                                    <div class="submit">
                                        <?php if(!empty($info)){ ?>
                                            <input type="hidden" value="<?php echo $info['id'] ?>" name="id" />
                                            <input type="hidden" value="edit" name="opt" />
                                        <?php }else{ ?>
                                            <input type="hidden" value="add" name="opt" />
                                        <?php }; ?>
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
    <!--编辑器开始-->