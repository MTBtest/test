<?php include $this->admin_tpl("header"); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">站点设置</a> > 主题设置
    </div>
    <span class="line_white"></span>
   	<div class="install mt10">
    	<dl>
        	<dt><a href="home1.6.1.html">模板管理</a><a href="home1.6.2.html" class="hover">模板维护</a></dt>
            <dd>
				<div class="weihu">
                	<span>您正在修改主题模板，如要修改请将要修改的模板文件做好备份以后再修改。</span>
                    <h3><?php echo $info['name'] ?>模板</h3>
                    <ul>
                        <?php foreach ($info['template'] as $key => $value): ?>
                    	<li>
                            <strong><?php echo $key ?></strong>
                            <?php if (is_array($value)): ?>
                            <ul>
                                <?php foreach ($value as $k => $v): ?>
                                <li><?php echo $k ?>(<?php echo $v ?>) <a href="home1.6.3.html">查看</a></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </dd>
        </dl>
    </div>
<script>
	$(function(){
			$(".odd li:odd").css("background","#fff");
			$(".even li:even").css("background","#fff");
		});
	</script>
<?php include $this->admin_tpl("copyright"); ?>