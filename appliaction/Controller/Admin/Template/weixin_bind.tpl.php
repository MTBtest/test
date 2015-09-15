<?php include $this->admin_tpl("header"); ?>
<div class="content">
    <style>
        #Validform_msg{display: none}
    </style>
    <div class="site">
        Haidao Board <a href="#">站点管理</a> > 微信公众号配置
    </div>
    <span class="line_white"></span>
    <div class="install mt5">
        <dl>
            <dd>
                <div class="install">
                    <div class="install">
                        <dl>
                            <form name="form" method="post" action="<?php echo U('Site/insert?ct=weixin&showpage=0') ?>">
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>公众号名称：</strong>
                                            <input type="text" class="text_input" name="name" placeholder='' datatype="*" value="<?php echo C('name')?>"><span style="padding-left: 48px;" class="Validform_checktip" >请填写要绑定的公众号名称</span>
                                        </li> 
                                        <li>
                                            <strong>微信号：</strong>
                                            <input type="text" class="text_input" name="weixin_account" placeholder='' datatype="*" value="<?php echo C('weixin_account')?>"><span style="padding-left: 48px;" class="Validform_checktip" >请填写公众号对应的微信号，在公众号设置中可以看到</span>
                                        </li>
                                        <li>
                                            <strong>公众号类型：</strong>
                                            <input type="radio" name="type" value="1" <?php echo (C('type') == 1) ? 'checked' : ''; ?>/> 服务号 <input type="radio" name="type" value="2" <?php echo (C('type')== 2) ? 'checked' : ''; ?>/> 订阅号<span class="Validform_checktip" style="margin-left:195px;">请选择公众号类型，推荐使用服务号    </span>
                                        </li>
                                        <li>
                                            <strong>原始ID：</strong>
                                            <input type="text" class="text_input" name="weixin_id" placeholder=''  datatype="n" value="<?php echo C('weixin_id')?>" /><span class="Validform_checktip" style="margin-left:-2px;">请填写公众号对应的原始ID，在公众号设置中可看到</span>
                                        </li>
                                        <li>
                                            <strong>AppID（应用ID）：</strong>
                                            <input type="text" class="text_input" name="appid" placeholder=''  datatype="n" value="<?php echo C('appid')?>" /><span class="Validform_checktip" style="margin-left:-2px;">请填写公众号对应的AppID，在公众号开发者中心可看到</span>
                                        </li>
                                        <li>
                                            <strong>AppSecret（应用密钥）：</strong>
                                            <input type="text" class="text_input" name="appsecret" placeholder=''  datatype="n" value="<?php echo C('appsecret')?>" /><span class="Validform_checktip" style="margin-left:-2px;">请填写公众号对应的AppSecret，在公众号开发者中心可看到</span>
                                        </li>
                                        <li>
                                            <strong>是否启用微信公众号：</strong>
                                        <input type="radio" name="weixin_status" value="1" <?php echo (C('weixin_status') == 1) ? 'checked' : ''; ?>/> 启用 <input type="radio" name="weixin_status" value="0" <?php echo (C('weixin_status') == 0) ? 'checked' : ''; ?>/> 关闭<span class="Validform_checktip" style="margin-left:220px;">如关闭微信公众号设置，自定义菜单将无法使用，此功能不影响微信支付    </span>
                                    </ul>
                                    <div class="submit">
                                        <input type="hidden" name="files" value="weixin.php" />
                                        <input type="hidden" name="url" value="" />
                                        <input type="hidden" name="weixin_token" value="" />
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
</body>
</html>