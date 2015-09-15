<?php include $this->admin_tpl('header'); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">站点设置</a> > 权限管理
    </div>
    <span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dt><a href="<?php echo U('AuthManager/index'); ?>">用户组列表</a><a href="javascript:void(0)" class="hover">
                <?php echo $meta_title; ?></a></dt>
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="<?php echo U('AuthManager/creategropup'); ?>" class="addform" method="post">
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>用户组：</strong>
                                            <input type="text" class="text_input" name="title" placeholder='' datatype="*" value="<?php echo $info['title']; ?>" /><span class="Validform_checktip ">请填写用户组名
                                            </span>
                                        </li>
                                        <li>
                                            <strong>描述：</strong>
                                            <textarea name="description" rows="4" cols="20" placeholder=''  style="margin-right: 50px;"><?php echo $info['description']; ?></textarea>
                                            <span class="Validform_checktip" style="margin-left:4px";>请填写用户组描述</span>
                                        </li>
                                    </ul>
                                    <div class="submit">
                                          <input type="button" class="button_search "  id="btn_sub" value="提交" />
										  <a href="<?php echo U('index')?>">返回</a>
                                    </div>
                                </dd>
                                <input name="id" type='hidden' value='<?php echo $info["id"]; ?>'>
                            </form>
                        </dl>
                    </div>
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
                    if (data.status == "n") {
                        art.dialog({width: 320, time: 5, title: '温馨提示(5秒后关闭)', content: data.info, ok: true});
                    }
                    if (data.status == "y") {
                        window.location.href = data.url;
                    }
                }
            });
        });
    </script>
	<?php include $this->admin_tpl('copyright'); ?>