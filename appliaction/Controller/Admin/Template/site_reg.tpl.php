<?php include $this->admin_tpl("header"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">站点设置</a> > 注册与登录设置
    </div>
    <span class="line_white"></span>
    <div class="install mt10 reg_login">
    <dl>
        <dt><a href="javascript:" class="hover">注册</a><a href="javascript:">第三方登录</a></dt>
        <!-- 注册 -->
        <dd>
            <form name="form" method="post" action="<?php echo U('Site/insert?ct=reg') ?>">
                <ul class="web p0">
                    <li>
                        <strong>允许新会员注册设置：</strong>
                        <b>
                            <label><input type="radio" name="reg_isreg" id="RadioGroup1_0" value="1"
                             <?php if (C('reg_isreg') == 1) { ?> checked    <?php } ?> /> 允许 </label>
                            <label><input type="radio" name="reg_isreg" id="RadioGroup1_1" value="0" <?php if (C('reg_isreg') == 0) { ?> checked    <?php } ?> /> 关闭 </label>
                        </b>
                        <span style="margin-left:-1px">设置是否允许新会员注册，默认为允许 </span>
                    </li>
                    <li>
                        <strong>商城注册文件名：</strong>
                        <input type="text" value="<?php echo C('reg_regfile') ?>" class="text_input" name="reg_regfile" /><span style="margin-left:2px">设置商城注册页的文件名，默认为“register.php”，如果您更改了此设置，需要您手动重命名文件名称</span>
                    </li>
                    <li>
                        <strong>注册链接文字：</strong>
                        <input type="text" value="<?php echo C('reg_regtext') ?>" class="text_input" name="reg_regtext" /><span style="margin-left:2px">您可以对商城注册页的链接文字进行重命名，如：成为会员，默认为注册，</span>
                    </li>
                    <!--<li>
                        <strong>新用户注册验证：</strong>
                        <select name="reg_regvalidation" style="margin-right: 50px;">
                            <option value="0" 
                             <?php if (C('reg_regvalidation') == 0) { ?> selected    <?php } ?>>无需验证</option>
                            <option value="1" 
                            < <?php if (C('reg_regvalidation') == 1) { ?> selected    <?php } ?>>手机验证</option>
                        </select><span>选择“无需验证”用户可直接注册成功；选择“Email 验证”将向用户填写的Email发送一封验证邮件以确认邮箱的有效性</span>
                    </li>-->
                    <li>
                        <strong>网站服务条款：</strong>
                        <textarea name="reg_terms"><?php echo C('reg_terms') ?></textarea>
                        <p>您可以在这里编辑网站服务条款的详细内容</p>
                    </li>
                </ul>
                <div class="input1">
                    <input type="submit" value="提交" class="button_search">
                </div>
                <input type="hidden" name="files" value="reg.php" />
            </form>
        </dd>

        <!-- 第三方登录 -->
        <dd class="login mt10" style="border: none;display:none;">
            <table style="width:100%" class="easyui-datagrid" data-options="striped:true">
                <thead>
                    <tr>
                        <th data-options="field:'pay_code',halign:'center',align:'center', width:'11%'">图标</th>
                        <th data-options="field:'pay_name',halign:'center',align:'center',width:'10%'">名称</th>
                        <th data-options="field:'enabled',halign:'center',align:'center',width:'9%'">状态</th>
                        <th data-options="field:'pay_desc',halign:'center',width:'50%'">描述</th>
                        <th data-options="field:'none',halign:'center',align:'center',width:'15%'">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logins as $code => $login): ?>
                    <tr>
                        <td><img src="<?php echo IMG_PATH.$login['code']; ?>_login.png" alt="" height="27"/></td>
                        <td><?php echo $login['name'] ?></td>
                        <td><?php echo ($login['enabled'] == 1) ? '开启' : '关闭'; ?></td>
                        <td style="text-align: left;text-indent: 50px;"><?php echo $login['description'] ?></td>
                        <td>
                            <?php if (empty($login['login_code'])): ?>
                                <a href="<?php echo U('Admin/Login/config', array('login_code' => $login['code'])) ?>"><font color="red">安装此接口</font></a>
                            <?php else: ?>
                                <a href="<?php echo U('Admin/Login/config', array('login_code' => $login['code'])) ?>">配置&nbsp;&nbsp;</a>
                                <a href="<?php echo U('Admin/Login/delete', array('login_code' => $login['login_code'])) ?>">卸载</a>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="clear"></div>
        </dd>
    </dl>
    <?php include $this->admin_tpl("copyright"); ?>
    <div class="clear"></div>
</div>

<script>  
    //切换
    $(window).load(function() {
        var tabTitle = ".reg_login dt a";
        var tabContent = ".reg_login dd";
        $(tabTitle).bind("click", function() {
            $(this).siblings("a").removeClass("hover").end().addClass("hover");
            var index = $(tabTitle).index($(this));
            $(tabContent).eq(index).show().siblings(tabContent).hide();
            $(window).resize();
        });
    });
</script>