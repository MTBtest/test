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
                            <form action="<?php echo U('edit') ?>" class="addform" method="post">
                                <input type="hidden" name="delivery_id" value="<?php echo $info['delivery_id']?>"/>
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>模板名称：</strong>
                                            <input type="text" class="text_input" name="name" placeholder='' datatype="*" value="<?php echo $info['name'] ?>" /><span class="Validform_checktip ">请填写标题，支持中文、英文和数字，4-10位
                                            </span>
                                        </li>
                                        <li>
                                            <strong>是否启用：</strong>
                                            <label><input type="radio" name="status" value="1" <?php echo ($info['status'] == 1) ? 'checked' : ''; ?>/> 启用</label>
                                            <label><input type="radio" name="status" value="0" <?php echo ($info['status'] == 0) ? 'checked' : '';?>/> 禁用</label>
                                        </li>
                                        <li>
                                            <strong>模板描述：</strong>
                                            <textarea name="description" cols="30" rows="10"><?php echo $info['description'] ?></textarea>
                                        </li>
                                        <li>
                                            <strong>请编辑快递单打印模板，注意选择好所属的快递公司。</strong>
                                            <div class="editor" id="content"></div>
                                            <img src='<?php echo IMG_PATH;?>delivery_express/<?php echo $deliverys[$info['delivery_id']]['enname'];?>.jpg' id="bgimg" style="display: none;">
                                            <input type="hidden" name="content" value="<?php echo $info['content'] ?>"/>
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
var deliverys = <?php echo json_encode($deliverys); ?>;
var contents = <?php echo json_encode($content);?>;
$(function(){
    $editor.editBox({
        id: '<?php echo random(6);?>',				//插件ID
        color:"red",				//控件颜色
        imgurl:"<?php echo JS_PATH;?>editbox/images/",	//图片路径
        height:<?php echo (int) $content['height'];?>,
        width:<?php echo (int) $content['width']?>,
        /* 快递公司列表 */
        delivery:'<?php echo $deliverys[$info['delivery_id']]['enname']?>',
        deliveryName: '<?php echo $deliverys[$info['delivery_id']]['name']?>',
        deliveryImage: '<?php echo IMG_PATH; ?>delivery_express/',
        deliveryOption:deliverys
    });
    $.each(contents.list, function(i, n){
        $editor.addBox({width:n.width,height:n.height,left:n.left,top:n.top,text:n.txt});
    })
})
$(":submit").click(function() {
    var content = $editor.content();
     /*var check_background = JSON.parse(content);*/
	var check_background = $('#panelTemplate span').text();
    if (check_background.background == 'undefined') {
        alert('请选择快递单模版');
        return false;
    }
    $("input[name=content]").attr("value", content);
});
</script>
