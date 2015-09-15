<?php include $this->admin_tpl("header"); ?>
<style>#Validform_msg{display: none} </style>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">会员管理</a> > 添加会员等级
    </div>
    <span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="<?php echo U(ACTION_NAME) ?>" class="addform" method="post">
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>会员等级名称：</strong>
                                            <input type="text" class="text_input" name="name" placeholder='' datatype="*" value="<?php echo $info['name']; ?>" /><span class="Validform_checktip ">设置会员等级名称
                                            </span>
                                        </li>
                                        <li>
                                            <strong>最低经验值：</strong>
                                            <input type="text" class="text_input" name="min_points" placeholder='' datatype="n"  value="<?php echo $info['min_points']; ?>"  /><span class="Validform_checktip">设置会员等级所需要的最低经验值下限</span>
                                        </li>
                                        <li>
                                            <strong>最高经验值：</strong>
                                            <input type="text" class="text_input" name="max_points" placeholder='' datatype="n"  value="<?php echo $info['max_points'] ?>" /><span class="Validform_checktip">设置会员等级所需要的最高经验值上限</span>
                                        </li>
                                        <li>
                                            <strong>会员折扣：</strong>
                                            <input type="text" class="text_input" name="discount" placeholder=''  datatype="n1-2|/^100$/" value="<?php echo empty($info['discount']) ? 100 : $info['discount']; ?>" /><span class="Validform_checktip">折扣率单位为%，如输入90，表示该会员等级的用户可以以商品原价的90%购买</span>
                                        </li>
                                        <?php if (!empty($info)): ?>
                                            <input type="hidden" value="<?php echo $info['id']; ?>" name="id">
                                        <?php endif ?>
                                    </ul>
                                    <div class="submit">
                                        <input type="image" src="<?php echo IMG_PATH; ?>admin/input_1.png" />
										<a href="<?php echo U('lists')?>">返回</a>
                                    </div>
                                </dd>
                            </form>
                        </dl>
                    </div>
            </dd>
        </dl>
    </div>
</div>
<script type="text/javascript">
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
<?php include $this->admin_tpl("copyright"); ?>