<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" href="<?php echo CSS_PATH; ?>admin/plugin.css">
<div class="content">
    <div class="site">
        Haidao Board <a href="javascript:void(0)">应用商店</a> &gt; 插件管理
    </div>
    <div class="line_white"></div>
   	<div class="install mt10">
    	<dl>
        	<dt>
                <a href="<?php echo U('manage') ?>"<?php if (!$type): ?> class="hover"<?php endif ?>>全部插件</a>
                <a href="<?php echo U('manage', array('type' => '1')) ?>"<?php if ($type == 1): ?> class="hover"<?php endif ?>>已启用</a>
                <a href="<?php echo U('manage', array('type' => '2')) ?>"<?php if ($type == 2): ?> class="hover"<?php endif ?>>未启用</a>
        		<a href="<?php echo U('manage', array('type' => '3')) ?>"<?php if ($type == 3): ?> class="hover"<?php endif ?>>未安装</a>
                <?php if (APP_DEBUG === TRUE): ?>
                <a href="<?php echo U('develop') ?>" class="more">设计新插件</a>
                <?php endif ?>
        		<a href="http://bbs.haidao.la/forum-44-1.html" target="_blank" class="more">获取更多插件>></a>
        	</dt>
            <dd>
            	<div class="plugbox">
            		<ul>
                    	<?php foreach ($plugins as $pluginid => $plugin): ?>
        				<li>
            				<div class="m10">
            					<span class="plugSoft fl">
            						<img src="<?php echo $plugin_dir.$pluginid.'/icon.png'; ?>" alt="<?php echo $plugin['name'] ?>" onerror="javascript:this.src='<?php echo IMG_PATH ?>plugin.png';"/>
            					</span>
            					<span class="soft fl">
            						<h1><?php echo $plugin['name'] ?> <?php echo $plugin['version'] ?></h1>
	            					<p>作者: <?php echo $plugin['author'] ?></p>
                                    <?php if ($plugin['new_ver'] > $plugin['version']): ?>
                                    <span class="newPlug">发现新版本：<?php echo $plugin['new_ver']; ?></span>
                                    <?php endif ?>
            					</span>
            				</div>
            				<div class="clear"></div>
            				<div class="plug_btn">
                                <?php if ($plugin['pluginid'] > 0): ?>
                                    <a class="delC" href="<?php echo U('uninstall', array('identifier' => $plugin['identifier'])) ?>">卸载</a>
                                    <?php if ($plugin['available'] == 1): ?>
                                        <a href="<?php echo U('available', array('identifier' => $plugin['identifier'])) ?>">禁用</a>
                                    <?php else: ?>
                                        <a href="<?php echo U('available', array('identifier' => $plugin['identifier'])) ?>">启用</a>
                                    <?php endif ?>
                                    <?php if ($plugin['new_ver'] > $plugin['version']): ?>
                                    <a href="<?php echo U('upgrade', array('identifier' => $plugin['identifier'])) ?>">更新</a>
                                    <?php endif ?>
                                    <a href="<?php echo U('setting', array('pluginid' => $plugin['pluginid'])) ?>">设置</a>
                                <?php else: ?>
                                    <a href="<?php echo U('install', array('identifier' => $plugin['identifier'])) ?>">安装</a>
                                <?php endif ?>
                                <?php if (APP_DEBUG === TRUE): ?>
                                    <a class="systemC" href="<?php echo U('develop', array('pluginid' => $plugin['pluginid'])) ?>">设计</a>
                                <?php endif ?>
            				</div>
            			</li>
                    	<?php endforeach ?>
                    </ul>
            	</div>
            	<div class="clear"></div>
            </dd>
        </dl>
    </div>
<?php include $this->admin_tpl('copyright') ?>
