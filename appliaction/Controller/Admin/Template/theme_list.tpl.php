<?php include $this->admin_tpl("header"); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">站点设置</a> > 主题设置
    </div>
    <span class="line_white"></span>
   	<div class="install mt10">
    	<dl>
            <dd>
            	<ul class="moban">
                    <?php foreach ($lists as $k => $v): ?>
                	<li>
                    	<div class="is_used<?php echo ($v['isdefault'] == 1) ? ' hover' : '';?>" >
                    		<img src="<?php echo $v['thumb'] ?>" alt="<?php echo $v['name'] ?>" />
                    	</div>
                        <div>
                        <p>
                            模板名称：<?php echo $v['name'] ?></span><br />
                            开发人员：<span style="color:#005ABD;"><?php echo $v['author'] ?></span> <br />
                            适用版本：<span style="color:#005ABD;">Haidao Board <?php echo $v['version'] ?></span><br />
                            更新时间：<span style="color:#005ABD;">2014-11-14</span><br />
                        </p>
                        <a href="<?php if ($v['isdefault'] == 1): ?>javascript:;" class="disabled<?php else: ?><?php echo U('setdefault', array('theme' => $k)) ?><?php endif ?>">设为默认</a>
                        </div>
                    </li>
                    <?php endforeach ?>
                </ul>
            </dd>
        </dl>
    </div>
    <div class="clear"></div>
<script>
$(function(){
		$(".odd li:odd").css("background","#fff");
		$(".even li:even").css("background","#fff");
	});
</script>
<?php include $this->admin_tpl("copyright"); ?>
