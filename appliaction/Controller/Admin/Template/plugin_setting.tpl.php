<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" href="<?php echo CSS_PATH; ?>admin/plugin.css">
<div class="content">
    <div class="site">
        Haidao Board <a href="javascript:void(0)">应用商店</a> &gt; 插件管理
        <a href="<?php echo U('manage') ?>" class="return_plug fr" style="color: #2d689f;">返回插件列表</a>
    </div>
    <div class="line_white"></div>
    <div class="Plug_head">
    	<div class="m10">
			<div class="plugSoft fl"><img src="<?php echo $plugin_dir.$rs['identifier'].'/icon.png' ?>" onerror="javascript:this.src='<?php echo IMG_PATH ?>plugin.png';"/></div>
			<div class="fl">
				<div class="soft_title">
					<h1 class="fl"><?php echo $rs['name'];?> <?php echo $rs['version'] ?></h1>
                    <?php if ($xml['Data']['plugin']['version'] > $rs['version']): ?>
                    <span class="newPlug1 fl m40">发现新版本：<?php echo $xml['Data']['plugin']['version']; ?></span>
                    <?php endif ?>
				</div>
				<p><?php echo $rs['description'] ?></p>
			</div>
		</div>
		<div class="clear"></div>
    </div>
   	<div class="install mt10">
    	<dl>
        	<dt>
        		<a href="<?php echo U('setting', array('pluginid' => $pluginid)) ?>" <?php if (empty($_GET['mod'])): ?>class="hover"<?php endif ?>>设置</a>
                <?php foreach ($submenu as $menu): ?>
                <a href="<?php echo U('setting', array('pluginid' => $pluginid, 'mod' => $menu['name'])) ?>" <?php if ($menu['name'] == $_GET['mod']): ?>class="hover"<?php endif ?>><?php echo $menu['menu'] ?></a>
                <?php endforeach ?>
        	</dt>
            <dd>
            <?php if (empty($_GET['mod'])): ?>
                <form action="<?php echo U('setting') ?>" method="POST">
                <input type="hidden" name="pluginid" value="<?php echo $rs['pluginid'] ?>">
            	<ul class="web">
                <?php foreach ($vars as $var): ?>
                    <li>
                        <strong><?php echo $var['title'] ?>：</strong>
                        <?php
                        $array = array();
                        if(in_array($var['type'], array('radio', 'checkbox', 'select', 'selects'))) {
                            $extra = explode("\r\n", $var['extra']);
                            foreach ($extra as $key => $value) {
                                list($k, $v) = explode("=", $value);
                                $array[$k] = $v;
                            }
                        }
                        switch ($var['type']) {
                            case 'radio':
                                $input = form::radio($var['variable'], $var['value'], $array);
                                break;
                            case 'checkbox':
                                $input = form::checkbox($var['variable'], $var['value'], $array);
                                break;
                            case 'textarea':
                                $input = form::textarea($var['variable'], $var['value'], $var['extra']);
                                break;
                            case 'select':
                                $input = form::select($var['variable'], $var['value'], $array);
                                break;
                            case 'selects':
                                $input = form::selects($var['variable'], $var['value'], $array);
                                break;
                            case 'datetime':
                                $input = form::datetime($var['variable'], $var['value']);
                                break;
                            case 'usergroup':
                                $usergroups = model('user_group')->getField('id,name', TRUE);
                                $input = form::select($var['variable'], $var['value'],$usergroups);
                                break;
                            case 'usergroups':
                                $usergroups = model('user_group')->getField('id,name', TRUE);
                                $input = form::selects($var['variable'], $var['value'],$usergroups);
                                break;
                            default:
                                $input = form::text($var['variable'], $var['value']);
                                break;
                        }
                        echo $input;
                        ?>
                        <span><?php echo $var['description'] ?></span>
                    </li>
                <?php endforeach ?>
                </ul>
                <div class="input1">
					<input class="button_search" type="submit" value="提交">
				</div>
            </form>
            <?php else: ?>
            <?php echo include $plugin_folder.'/'.$_GET['mod'].'.inc.php'; ?>
            <?php endif ?>
            </dd>
        </dl>
    </div>
    <?php include $this->admin_tpl('copyright') ?>
