<?php include $this->admin_tpl("header"); ?>
<div class="content">
	<div class="site">Haidao Board <a href="#">内容管理</a> > 添加快递单打印模板</div>
	<span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="<?php echo U('add') ?>" class="addform" method="post">
                                <input type="hidden" name="delivery_id" value="0"/>
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>模板名称：</strong>
                                            <input type="text" class="text_input" name="name" placeholder='' datatype="*" value="<?php echo $info['title'] ?>" /><span class="Validform_checktip ">请填写标题，支持中文、英文和数字，4-10位
                                            </span>
                                        </li>
                                        <li>
                                            <strong>是否启用：</strong>
                                            <label><input type="radio" name="status" value="1" checked/> 启用</label>
                                            <label><input type="radio" name="status" value="0" /> 禁用</label>
                                        </li>
                                        <li>
                                            <strong>模板描述：</strong>
                                            <textarea name="description" cols="30" rows="10"></textarea>
                                        </li>
                                        <li>
                                            <strong>请编辑快递单打印模板，注意选择好所属的快递公司。</strong>
                                            <div class="editor" id="content"></div>
                                            <img src='' id="bgimg" style="display: none;">
                                            <input type="hidden" name="content" value=""/>
                                        </li>
                                    </ul>
                                    <div class="submit">
									 <input type="submit" class='button_search' value='提交'/>
									 <a href="<?php echo U('index')?>">返回</a>
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
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>editbox/editbox.css" />
<script type="text/javascript" src="<?php echo JS_PATH;?>editbox/jquery.editbox.debug.js"></script>
<script type="text/javascript">
var $editor = $("#content");
$(function(){
    $editor.editBox({
        id:"test12345",				//插件ID
        color:"red",				//控件颜色
        imgurl:"<?php echo JS_PATH;?>editbox/images/",	//图片路径
        height:600,
        width:960,
        /* 快递公司列表 */
        deliveryImage: "<?php echo IMG_PATH; ?>delivery_express/",
        deliveryOption:<?php echo json_encode($deliverys); ?>
    });
})
$(":submit").click(function() {
    var content = $editor.content();
    if ($('input[name=delivery_id]').val() == 0) {
        alert('请选择快递单模版');
        return false;
    }
    $("input[name=content]").attr("value", content);
});
</script>
