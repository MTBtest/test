<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" href="<?php echo CSS_PATH; ?>admin/plugin.css">
<div class="content">
    <div class="site">
        Haidao Board <a href="javascript:void(0)">应用商店</a> &gt; 插件管理
    </div>
    <div class="line_white"></div>
    <p class="m10" style="color:#000000">本功能仅供插件开发者使用，如果您只是安装或使用插件，切勿修改本设置；警告：不正确的插件设计或安装可能危及整个站点正常使用。</p>
   	<form action="<?php echo U('pluginvar') ?>" method="POST">
    <input type="hidden" name="pluginvarid" value="<?php echo $rs['pluginvarid'] ?>">
    <div class="install mt10">
    	<dl>
        	<dt>
        		<a href="<?php echo U('develop', array('pluginid' => $rs['pluginid'], 'op' => 'setting')); ?>" data='setting'>插件设置</a>
        		<a href="<?php echo U('develop', array('pluginid' => $rs['pluginid'], 'op' => 'module')); ?>" data='module'>模块设置</a>
        		<a href="<?php echo U('develop', array('pluginid' => $rs['pluginid'], 'op' => 'var')); ?>" data='var' class="hover">变量设置</a>
        		<a href="javascript:void(0)" class="more">返回插件列表>></a>
        	</dt>
            <dd>
            	<ul class="web" id="setting_show">
            		<li> <strong>配置名称：</strong>
                        <input type="text" value="<?php echo $rs['title'] ?>" name="pluginvar[title]" class="text_input">
                        <span>中英文均可，用于显示在插件配置的菜单中，最多 100 个字节。</span>
            		</li>
            		<li> <strong>配置说明：</strong>
                        <textarea name="pluginvar[description]"><?php echo $rs['description'] ?></textarea>
                        <span>描述此项配置的用途和取值范围，详细的描述有利于插件使用者了解这个设置的作用，最多 255 个字节。此处和配置名称类似，也支持语言定义</span>
            		</li>
            		<li><strong>配置类型：</strong>
                        <select name="pluginvar[type]">
                            <?php foreach ($this->fieldtype as $type => $value): ?>
                            <option value="<?php echo $type ?>"<?php if ($type == $rs['type']): ?> selected<?php endif ?>><?php echo $value ?></option>
                            <?php endforeach ?>
                        </select>
                        <span>设置此配置的数据类型，用于程序中检查和过滤相应配置值</span>
            		</li>
            		<li> <strong>配置变量名：</strong>
                        <input type="text" value="<?php echo $rs['variable'] ?>" name="pluginvar[variable]" class="text_input">
                        <span>设置配置项目的变量名，用于插件程序中调用，可包含英文、数字和下划线，在同一个插件中需要保持变量名的唯一性，最多 40 个字节</span>
            		</li>
            		<li> <strong>扩充设置：</strong>
                        <textarea name="pluginvar[extra]"><?php echo $rs['extra'] ?></textarea>
                        <span>只在配置类型为“选择(select)”时有效，用于设定选项值。等号前面为选项索引(建议用数字)，后面为内容，例如: <br/>1 = 光电鼠标<br/>2 = 机械鼠标<br/>3 = 没有鼠标</span>
            		</li>
                </ul>
                <div class="input1">
					<input class="button_search" type="submit" value="提交">
				</div>
            </dd>
        </dl>
    </div>
    </form>
<script type="text/javascript">
	$(".install dt a").click(function() {
	    $(".install dt a").removeClass('hover');
	    $(this).addClass('hover');
	    $('.install dd ul').hide();
	    $('.install dd ul#' + $(this).attr('data') + '_show').show();
	})
</script>
    <?php include $this->admin_tpl('copyright') ?>