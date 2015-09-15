<?php include $this->admin_tpl('header'); ?>
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
        Haidao Board <a href="#">内容管理</a> > 文章列表 > 文章添加
    </div>
	<span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="<?php echo U('Article/update') ?>" class="addform" method="post">
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>标题：</strong>
                                            <input type="text" class="text_input" name="title" placeholder='' datatype="*" value="<?php echo $info['title'] ?>" /><span class="Validform_checktip ">填写商城公告标题</span>
                                        </li>
                                        <li>
                                            <strong>顶级分类：</strong>
                                            <select name="category_id" style="margin-right: 95px;">
                                                <?php foreach ($catTree as $v):?>
                                                <option value="<?php echo $v['value']?>"  <?php if($info['category_id']==$v['value']):?>selected<?php endif;?>><?php echo $v['text']?></option>
                                                <?php endforeach;?>
                                            </select>
                                            <span style="margin-left:-48px;">
                                            	选择文章所属分类
                                            </span>
                                        </li>
                                        <li>
                                            <strong>关键词：</strong>
                                            <input type="text" class="text_input" name="keywords" placeholder=''  datatype="*" ignore="ignore" value="<?php echo $info['keywords'] ?>" /><span class="Validform_checktip">关键词出现在页面头部的Keywords标签中，用于记录本文章的关键字，多个关键字请用分隔符分隔</span>
                                        </li>
                                        <li>
                                            <strong>摘要：</strong>
                                            <input type="text" class="text_input" name="description" placeholder='' datatype="*" ignore="ignore" value="<?php echo $info['description'] ?>" /><span class="Validform_checktip">摘要是文章的内容概括，建议不超过80个字</span>
                                        </li>
                                        <li>
                                            <strong>是否置顶：</strong>
                                            <?php $info['top'] = isset($info['top']) ? $info['top'] : 1; ?>
                                            <input type="radio" name="top" value="1" <?php if($info['top']==1) echo "checked"; ?> /> 是 <input type="radio" name="top" value="0" <?php if($info['top'] ==0) echo "checked" ?>  /> 否<span class="Validform_checktip" style="margin-left: 246px;">如开启置顶，本文章将置于所有文章顶部</span>
                                        </li>
                                        <li>
                                            <strong>是否显示：</strong>
                                            <?php $info['status'] = isset($info['status']) ? $info['status'] : 1; ?>
                                            <input type="radio" name="status" value="1" <?php if($info['status']==1) echo "checked" ?> /> 显示 <input type="radio" name="status" value="0" <?php if($info['status'] ==0) echo "checked" ?>  /> 禁用<span class="Validform_checktip" style="margin-left: 221px;">默认显示</span>
                                        </li>
                                        <li>
                                            <strong>排序：</strong>
                                            <input type="text" class="text_input" name="sort" placeholder=''  datatype="n" value="<?php echo $info['sort'] ? $info['sort'] : 100 ?>"/><span class="Validform_checktip">输入数字显示排序，数字越小越靠前数字越小越靠前</span>
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