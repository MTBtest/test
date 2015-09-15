<?php include $this->admin_tpl('header'); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">站点设置</a> > 后台管理团队
    </div>
    <span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dt><a href="<?php echo U('AdminUser/index') ?>">团队列表</a><a href="javascript:void(0)" class="hover">添加团队</a></dt>
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="<?php echo U('AdminUser/add') ?>" class="addform" method="post">
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>用户名：</strong>
                                            <input type="text" class="text_input" name="name" placeholder='' datatype="*4-10|/^[\u4E00-\u9FA5\uf900-\ufa2d]{2,4}$/"/><span class="Validform_checktip ">请填写管理员用户名，支持中文、英文和数字，4-10位
                                            </span>
                                        </li>
                                        <li>
                                            <strong>用户密码：</strong>
                                            <input type="text" class="text_input" name="password" placeholder=''  datatype="*4-10"/><span class="Validform_checktip">请填写管理员后台登录密码，请包涵英文和数字，4-10位</span>
                                        </li>
                                        <li>
                                            <strong>确认密码：</strong>
                                            <input type="text" class="text_input" name="password2" placeholder='' datatype="*" recheck="password"/><span class="Validform_checktip">核对密码，确保两次密码输入正确</span>
                                        </li>
                                        <li>
                                            <strong>邮箱：</strong>
                                            <input type="text" class="text_input" name="email" placeholder=''  datatype="e" ignore="ignore" /><span class="Validform_checktip">填写管理员邮箱，格式为abc@abc.com</span>
                                        </li>
                                    </ul>
                                    <div class="input1">
                                         <input type="button" class="button_search "  id="btn_sub" value="提交" />
                                    </div>
                                </dd>
                            </form>
                        </dl>
                    </div>
            </dd>
        </dl>
    </div>
    <script>
        $(function() {
            var demo = $(".addform").Validform({
                btnSubmit: "#btn_sub",
                btnReset: ".btn_reset",
                tiptype: function(){},
                label: ".label",
                showAllError: false,
                ajaxPost: true,
                callback: function(data) {
                    $("#Validform_msg").hide();
                    if (data.status == 0) {
                        art.dialog({width: 320, time: 5, title: '温馨提示(5秒后关闭)', content: data.info, ok: true});
                    }
                    if (data.status == 1) {
                        window.location.href = data.url;
                    }
                }
            });
        });
    </script>
  <?php include $this->admin_tpl('copyright') ?>