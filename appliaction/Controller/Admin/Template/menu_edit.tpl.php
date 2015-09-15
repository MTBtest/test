<?php include $this->admin_tpl("header"); ?>
<div class="content">
    <style>
        #Validform_msg{display: none}
    </style>
    <div class="site">
        Haidao Board <a href="#">站点管理</a> > 微信自定义菜单编辑
    </div>
    <span class="line_white"></span>
    <div class="install mt5">
        <dl>
            <dd>
                <div class="install">
                    <div class="install">
                        <dl>
                            <form action="<?php echo U('Menu/edit')?>" class="addform" method="post">
                                <dd>
                                    <ul class="web">
                                        <li style="padding: 0 0px 6px 5px;">
                                            <p>小提示：编辑完菜单后要点击生成菜单，并重新关注微信公众号，微信端才会显示，编辑自定义菜单前请确保微信配置正常</p>
                                        </li>
                                        <li>
                                            <strong>自定义菜单名称：</strong>
                                            <input type="text" class="text_input" name="name" placeholder='' datatype="*" value="<?php echo $data['name']?>"><span style="padding-left: 48px;" class="Validform_checktip" >填写自定义菜单名称，一级菜单最多4个汉字，二级菜单最多7个汉字</span>
                                        </li> 
                                        <li>
                                            <strong>上级菜单：</strong>
                                            <select name="parent_id" style="margin-right: 96px;">
                                                <option value="0">请选择</option>
                                                <?php foreach ($info as $key => $vo): ?>
                                                    <option value="<?php echo $vo['value'];?>"
                                                    <?php if($vo['value']==$data['parent_id']):?>
                                                        selected = "selected"
                                                    <?php endif?>>
                                                    <?php echo $vo['text'];?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <span style="margin-left:-51px;">
                                            	选择菜单所属哪个菜单下，微信自定义菜单仅支持一级菜单和二级菜单
                                            </span>
                                        </li>
                                        <li>
                                            <strong>菜单链接类型：</strong>
                                            <input type="radio" name="type" value="1" <?php echo ($data['type'] == 1) ? 'checked' : ''; ?>/> 内置链接 <input type="radio" name="type" value="2" <?php echo ($data['type'] == 2) ? 'checked' : ''; ?>/> 自定义菜单<span class="Validform_checktip" style="margin-left:161px;">选择菜单链接方式，系统内置了常用的内置链接，也可以自定义链接到指定地址    </span>
                                            <div id="link_type" style="margin-top:5px">
                                            <div style="display:none">
                                                <select name="url" style="margin-right: 96px;">
                                                        <option value="">请选择链接地址</option>
                                                <?php foreach ($built_info as $k => $v): ?>
                                                    <?php if($v['status']==1):?>
                                                        <option value="<?php echo $v['url'];?>"
                                                        <?php if($data['link']=='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$v['url'] || $data['link']=='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$v['url'].'&bind=account'):?>
                                                        selected='selected'
                                                        <?php endif?>
                                                        ><?php echo $v['name'];?></option>
                                                    <?php endif?>
                                                <?php endforeach ?>
                                                </select>
                                            </div>
                                            <div style="display:none">
                                                 <input type="text" class="text_input" name="customurl" value="<?php echo $data['link']?>">
                                            </div> 
                                            </div>
                                        </li>
                                        <li>
                                            <strong>菜单排序：</strong>
                                            <input type="text" class="text_input" name="sort" placeholder=''  datatype="n" value="<?php echo $data['sort']?>" /><span class="Validform_checktip" style="margin-left:-2px;">输入数字改变自定义菜单显示排序，数字越小越靠前</span>
                                        </li>
                                    </ul>
                                    <div class="submit">
                                        <input type="hidden" name="id" value="<?php echo $data['id'] ?>" />
                                        <input type="hidden" name="old_pid" value="<?php echo $data['parent_id'] ?>" />
                                        <input type="submit" class="button_search" value="提交"/>
										<a href="<?php echo U('lists')?>">返回</a>
                                    </div>
                                </dd>
                            </form>  
                        </dl>
                    </div>
            </dd>
        </dl>
    </div>
    <?php include $this->admin_tpl("copyright"); ?>
</div>
<script>
        $('#link_type>div:eq(' + ($('input:radio[name="type"]:checked').val()-1) + ')').show();
        $('input:radio[name="type"]').each(
            function(i){
                $(this).click(function(){
                $('#link_type>div').hide();
                   $('#link_type>div:eq(' + i + ')').show();
                });
           }
        );
        
        
        
</script>
</body>
</html>