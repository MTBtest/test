<?php include $this->admin_tpl("header"); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">会员管理</a> > 添加会员
    </div>
    <span class="line_white"></span>
    <div class="sm">小提示：所添加的会员默认密码为<strong><font color='red'>123456</font></strong></div>
    <div class="install mt10">
        <dl>
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="<?php echo U(ACTION_NAME) ?>" class="addform" method="post" onsubmit="return false">
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>会员用户名：</strong>
                                            <?php if (empty($info)): ?>
                                                <input type="text" class="text_input" name="username" placeholder='' datatype="*3-15" value="<?php echo $info['username']; ?>" /><span class="Validform_checktip ">设置会员用户名称，3-15个字符，英文，数字或中文皆可</span>
                                            <?php else: ?>
                                                <dd style="float:left" class="text_input"><?php echo $info['username']; ?></dd><span class="Validform_checktip " style="float:left">不可编辑</span>
                                            <?php endif ?>
                                        </li>
                                        <li>
                                            <strong>手机号：</strong>
                                            <input type="text" class="text_input" name="mobile_phone" placeholder='' datatype="m" ignore="ignore"  value="<?php echo $info['mobile_phone']; ?>"  /><span class="Validform_checktip">设置会员的手机号码</span>
                                        </li>
                                       <!--  <li>
                                            <strong>会员等级：</strong>
                                            <select name="group_id" style="margin-right: 44px;">
                                                <?php foreach ($group as $r): ?>
                                                <?php if (!$info): ?>
                                                <option value="0">请选择</option>
                                                <?php endif ?>
                                                <option value="<?php echo $r['id']; ?>"
                                                    <?php if ($r['id'] == $info['group_id']) { ?>selected <?php }?>><?php echo $r['name']; ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <span class="Validform_checktip">设置会员所属等级</span>
                                        </li>  -->
                                        <li>
                                            <strong>会员性别：</strong>
                                             <b style="width:250px;">
                                            	<label><input type="radio" name="sex" placeholder=''  value='1' 
                                            		<?php echo ($info['sex'] == 1) ? 'checked' : ''; ?>> 男</label>
                                            	<label><input type="radio" name="sex" placeholder=''  value=0 <?php echo ($info['sex'] == 0) ? 'checked' : ''; ?>> 女</label>
                                            </b>
                                            <span class="Validform_checktip" style="margin-left:3px;">设置会员性别</span>
                                        </li>
                                        <li>
                                            <strong>会员生日：</strong>
                                            <div class="day fl"><input type="text" name="birthday" id="birthday" class="time_input" datetype="date" style="width:225px;margin-right:50px;" /></div>
                                            <span class="Validform_checktip">设置会员生日</span>
                                        </li>
                                        <li>
                                            <strong>会员邮箱：</strong>
                                            <input type="text" class="text_input" name="email" placeholder='' datatype="e" ignore="ignore" value="<?php echo $info['email']; ?>" /><span class="Validform_checktip">设置会员电子邮箱，格式为:user@domain.com</span>
                                        </li>
                                        <li>
                                            <strong>会员QQ：</strong>
                                            <input type="text" class="text_input" name="qq" placeholder='' datatype="n5-15" ignore="ignore" value="<?php echo $info['qq'] ?>" /><span class="Validform_checktip">设置会员QQ号码</span>
                                        </li>
                                        <?php if (!empty($info)): ?>
                                            <input type="hidden" value="<?php echo $info['id']; ?>" name="id">
                                        <?php endif ?>
                                    </ul>
                                    <div class="submit">
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
    <?php include $this->admin_tpl("copyright"); ?>
</div>
<!--时间选择-->
<?php echo jsfile('hddate');?>
<script>
	$(function() {
		laydate({
		    elem: '#birthday',
		    format: 'YYYY-MM-DD'
		});
	});
	</script>
<script type="text/javascript">
    //提交表单
    $(function() {
    	//默认高亮
		$(window.parent.document).find(".z_side").removeClass("hover");
		$(window.parent.document).find(".n12").addClass("hover");
        var demo = $(".addform").Validform({
            btnSubmit: "#btn_sub",
            btnReset: ".btn_reset",
            tiptype: function(){},
            label: ".label",
            showAllError: false,
            ajaxPost: true,
            callback: function(data) {
                $("#Validform_msg").hide();
                if (data.status == "0") {
                    art.dialog({width: 320, time: 5, title: '温馨提示(5秒后关闭)', content: data.info, ok: true});
                }
                if (data.status == "1") {
                    window.location.href = data.url;
                }
            }
        });
    });
</script>