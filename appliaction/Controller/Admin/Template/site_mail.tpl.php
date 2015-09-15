<?php include $this->admin_tpl('header'); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">站点设置</a> > 邮件服务器设置
    </div>
    <span class="line_white"></span>
    <div class="install tabs mt10">
        <dl>
            <dt ><a href="javascript:void(0)" class="hover">邮件设置</a><a href="javascript:void(0)">测试发送</a></dt>
            <dd>
                <form name="form" method="post" action="<?php echo U('Site/insert?ct=mail') ?>">
                    <ul class="web">
                        <li>
                            <strong>邮件发送方式：</strong>
                            <b style="width:318px;">
                                <label><input type="radio" name="mail_sendmail" class="mailtype" value="0"   <?php if (C('mail_sendmail') == 0) { ?> checked  <?php } ?> /> 通过PHP函数的sendmail发送（推荐此方式） </label><br />
                                <label><input type="radio" name="mail_sendmail" class="mailtype" value="1"   <?php if (C('mail_sendmail') == 1) { ?> checked  <?php } ?> /> 通过SOCKET连接SMTP服务器发送（支持ESMTP验证） </label>
                            </b>
                        </li>
                        <li class="smtp">
                            <strong>SMTP 服务器：</strong>
                            <input type="text" value="<?php echo C('mail_smtpserver') ?>" class="text_input" name="mail_smtpserver" /><span>设置SMTP服务器地址</span>
                        </li>
                        <li class="smtp">
                            <strong>SMTP 端口：</strong>
                            <input type="text" value="<?php echo C('mail_smtpport') ?>" class="text_input" name="mail_smtpport" /><span>设置 SMTP 服务器的端口，默认为 25</span>
                        </li>
                        <li class="smtp">
                            <strong>SMTP 是否要求身份验证：</strong>
                            <b style="width:308px;">
                                <label><input type="radio" name="mail_smtplogin" value="1" 
                                              <?php if (C('mail_smtplogin') == 1) { ?> checked  <?php } ?> /> 是 </label>
                                <label><input type="radio" name="mail_smtplogin" value="0" 
                                              <?php if (C('mail_smtplogin') == 0) { ?> checked  <?php } ?> /> 否 </label>
                            </b>
                        </li>
                        <li class="smtp">
                            <strong>发件人邮件地址：</strong>
                            <input type="text" value="<?php echo C('mail_formmail') ?>" class="text_input" name="mail_formmail" /><span>如果需要验证，必须为本服务器的邮件地址，格式为:user@domain.com</span>
                        </li>
                        <li class="smtp">
                            <strong>SMTP 身份验证用户名：</strong>
                            <input type="text" value="<?php echo C('mail_mailuser') ?>" class="text_input" name="mail_mailuser" /><span>填写SMTP身份验证用户名</span>
                        </li>
                        <li class="smtp">
                            <strong>SMTP 身份验证密码：</strong>
                            <input type="text" value="<?php echo C('mail_mailpass') ?>" class="text_input" name="mail_mailpass" /><span>填写SMTP身份验证密码</span>
                        </li>
                        <li>
                            <strong>邮件头的分隔符：</strong>
                            <b style="width:308px;margin-right: -8px;">
                                <label><input type="radio" name="mail_mailseparator" value="1" 
                                              <?php if (C('mail_mailseparator') == 1) { ?> checked  <?php } ?>  />  使用 CRLF 作为分隔符(通常为 Windows 主机) </label><br />
                                <label><input type="radio" name="mail_mailseparator" value="0" 
                                              <?php if (C('mail_mailseparator') == 0) { ?> checked  <?php } ?> />  使用 LF 作为分隔符(通常为 Unix/Linux 主机) </label>
                            </b>
                            <span style="margin-left:3px">请根据您邮件服务器的设置调整此参数</span>
                        </li>
                        <li>
                            <strong>收件人地址中包含用户名：</strong>
                            <b style="width:308px;margin-right: -8px;">
                                <label><input type="radio" name="mail_mailcontainsuser" value="1" 
                                              <?php if (C('mail_mailcontainsuser') == 1) { ?> checked  <?php } ?> />  是 </label>
                                <label><input type="radio" name="mail_mailcontainsuser" value="0" 
                                              <?php if (C('mail_mailcontainsuser') == 0) { ?> checked  <?php } ?> />  否 </label>
                            </b>
                            <span style="margin-left:3px">选择是将在将默认屏蔽邮件发送过程中的错误提示，默认为是</span>
                        </li>
                        <li>
                            <strong>屏蔽邮件发送中的全部错误提示：</strong>
                            <b style="width:308px;margin-right: -8px;">
                                <label><input type="radio" name="mail_mailcontainserro" value="1"
                                              <?php if (C('mail_mailcontainserro') == 1) { ?> checked  <?php } ?> />  是 </label>
                                <label><input type="radio" name="mail_mailcontainserro" value="0" 
                                              <?php if (C('mail_mailcontainserro') == 0) { ?> checked  <?php } ?>  />  否 </label>
                            </b>
                            <span style="margin-left:3px">选择是将在将默认屏蔽邮件发送过程中的错误提示，默认为是</span>
                        </li>
                        <div class="input1">
                            <input type="submit" class="button_search" value="提交"/>
                            <input type="hidden" name="files" value="mail.php" />
                        </div>
                </form>
                </ul>
            </dd><dd>
                <ul class="web">
                    <form name="testmailform" action="<?php echo U('Site/testmail') ?>" method="post">
                        <li>
                            <strong>测试发件人：</strong>
                            <input type="text" class="text_input" value="<?php echo C('mail_formmail') ?>" name="formuser" /><span>填写发件人邮箱,如使用QQ邮箱此项为邮箱地址不能更改</span>
                        </li>
                        <li>
                            <strong>测试邮件列表：</strong>
                            <textarea name="touser" style="margin-right: 104px;" placeholder="test@test.com"></textarea>
                            <p>如果要测试包含用户名的邮件地址，格式为 test1@test.com,test2@test.com 多个邮箱用半角符号分隔</p>
                        </li>
                        <input type="submit" class="button_search" value="测试发送" style="margin-left:5px;margin-top: 15px;"/>
                    </form>
                </ul>
            </dd>
        </dl>
<script>
    $(function () {
        $(".odd li:odd").css("background", "#fff");
        $(".even li:even").css("background", "#fff");
        $("#RadioGroup1_0").click(function () {
            (".smtp").hide();
        })
    });
    $(document).ready(function () {
        var mail_sendmail = "<?php echo C('mail_sendmail'); ?>";
        if (mail_sendmail == 1) {
            $(".smtp").hide();
        }
        else {
            $(".smtp").show();
        }
        $(".mailtype").change(
                function () {
                    var $selectedvalue = $("input[name='mail_sendmail']:checked").val();
                    if ($selectedvalue == 1) {
                        $(".smtp").hide();
                    }
                    else {
                        $(".smtp").show();
                    }
                });
        //切换
        $(function () {
            var tabTitle = ".tabs dt a";
            var tabContent = ".tabs dd";
            $(tabTitle + ":first").addClass("hover");
            $(tabContent).not(":first").hide();
            $(tabTitle).unbind("click").bind("click", function () {
                $(this).siblings("a").removeClass("hover").end().addClass("hover");
                var index = $(tabTitle).index($(this));
                $(tabContent).eq(index).siblings(tabContent).hide().end().fadeIn(0);
            });
        });
    });
</script>
<?php include $this->admin_tpl('copyright') ?>