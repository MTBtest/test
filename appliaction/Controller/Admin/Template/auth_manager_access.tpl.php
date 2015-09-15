<?php include $this->admin_tpl('header'); ?>
    <style>
        #Validform_msg{display: none}
    </style>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">站点设置</a> > 权限管理
    </div>
    <span class="line_white"></span>
    <div class="quan">
        <h3><span>当前编辑管理组： <?php echo $group_name; ?></span></h3>
        <!-- 访问授权 -->
        <form action="" enctype="application/x-www-form-urlencoded" method="POST" class="form-horizontal auth-form">
                <?php foreach ($node_list as $key => $node) {?>
                <dl class="check">
                    <dt><label><input type="checkbox" class="auth_rules rules_all" name="rules[]" value="<?php echo $node['id']; ?>" 
                              <?php if (in_array($node['id'], $rule)): ?>
                                    checked
                              <?php endif ?> /> <?php echo $node['name'] ?></label></dt>
                        <?php if ($node['_child']) {?>
                            <?php foreach ($node['_child'] as $key => $child) {?>
                            <dd>
                                <ul>
                                    <li>
                                        <strong><label>
                                            <input type="checkbox" class="auth_rules rules_row" name="rules[]"
                                            <?php if (in_array($child['id'], $rule)): ?>
                                                checked
                                            <?php endif ?>
                                            value="<?php echo $child['id'] ;?>"/><?php echo str_replace('&HR', '', $child['name']) ;?></label></strong>
                                        <?php if (!empty($child['_child'])){?>
                                        <p>
                                            <?php foreach ($child['_child'] as $key => $op) {?>
                                            <span><label>
                                                <input type="checkbox" class="auth_rules rule_check" name="rules[]" value="<?php echo $op['id'] ;?>"/><?php echo $op['name'] ;?></label>
                                                <img src="<?php echo IMG_PATH; ?>admin/ico_q.png" /></span>
                                        	<?php } ?>
                                        </p>
                                        <?php } ?>
                                        </li>
                                </ul>
                            </dd>
                         <?php } ?>
                   <?php } ?>
                </dl>
             <?php } ?>
            <!-- 访问授权 -->
            <div class="submit">
               <input type="submit" value="提交" class="button_search" id="btn_sub">
			   <a href="<?php echo U('index')?>">返回</a>
            </div>
        </form>
    </div>
</div>
    <script>
        +function($) {
            //全选节点
            $('.check dt input').on('change', function() {
                $(this).parents('dl').find('dd').find('input').prop('checked', this.checked);
            });
            $('.check li strong input').on('change', function() {
                $(this).parents('li').find('p').find('input').prop('checked', this.checked);
            });
            //选择框赋值
            var auth_groups = "{$auth_group}";
            auth_groups_arr = auth_groups.split(",");
            $('.auth_rules').each(function() {
                if ($.inArray(this.value, auth_groups_arr) > -1) {
                    $(this).prop('checked', true);
                }
            });
        }(jQuery);
    </script>
