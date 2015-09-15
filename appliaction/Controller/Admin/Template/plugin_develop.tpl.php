<?php include $this->admin_tpl('header'); ?>
<link rel="stylesheet" href="<?php echo CSS_PATH; ?>admin/plugin.css">
<div class="content">
    <div class="site">
        Haidao Board <a href="javascript:void(0)">应用商店</a> &gt; 插件管理
    </div>
    <div class="line_white"></div>
    <p class="m10" style="color:#000000">本功能仅供插件开发者使用，如果您只是安装或使用插件，切勿修改本设置；警告：不正确的插件设计或安装可能危及整个站点正常使用。</p>
   	<form action="<?php echo U('develop') ?>" method="POST">
	    <input type="hidden" name="pluginid" value="<?php echo $rs['pluginid'] ?>">
	    <div class="install mt10">
	    	<dl>
	        	<dt>
	        		<a href="javascript:void(0)" data='setting' <?php if ($op == 'setting'): ?>class="hover"<?php endif ?>>插件设置</a>
	        		<a href="javascript:void(0)" data='module' <?php if ($op == 'module'): ?>class="hover"<?php endif ?>>模块设置</a>
	        		<a href="javascript:void(0)" data='var' <?php if ($op == 'var'): ?>class="hover"<?php endif ?>>变量设置</a>
	                <?php if ($pluginid > 0): ?>
	                <a href="<?php echo U('export', array('pluginid' => $pluginid)) ?>" class="more">导出插件>></a>
	                <?php endif ?>
	        		<a href="<?php echo U('manage') ?>" class="more">返回插件列表>></a>
	        	</dt>
	            <dd>
	            	<ul class="web" id="setting_show" <?php if ($op != 'setting'): ?>style="display:none;"<?php endif ?>>
	            		<li> <strong>插件名称(name):</strong>
	                        <input type="text" value="<?php echo $rs['name'] ?>" name="setting[name]" class="text_input">
	                        <span style="margin-left:-1px">此插件的名称，中英文均可，最多 40 个字节</span>
	            		</li>
	            		<li> <strong>插件版本号(version)：</strong>
	                        <input type="text" value="<?php echo $rs['version'] ?>" name="setting[version]" class="text_input">
	                        <span style="margin-left:-1px">此插件的版本，中英文均可，最多 20 个字节。版本号高于旧版本号时，安装给用户时将会提示更新</span>
	            		</li>
	            		<li> <strong>唯一标识符(identifier)：</strong>
	                        <input type="text" value="<?php echo $rs['identifier'] ?>" name="setting[identifier]" class="text_input">
	                        <span style="margin-left:-1px">插件的唯一英文标识，不能够与现有插件标识重复。可使用字母/数字/下划线命名，不能包含其他符号或特殊字符，最大 40 个字节</span>
	            		</li>
	            		<li> <strong>插件作者(author)：</strong>
	                        <input type="text" value="<?php echo $rs['author'] ?>" name="setting[author]" class="text_input">
	                        <span style="margin-left:-1px">插件的开发者名称，可选填</span>
	            		</li>
	            		<li> <strong>版权信息(copyright)：</strong>
	                        <input type="text" value="<?php echo $rs['copyright'] ?>" name="setting[copyright]" class="text_input">
	                        <span style="margin-left:-1px">插件的版权信息，可选填</span>
	            		</li>
	                    <li>
	                		<strong>插件描述(description)：</strong>
	                        <textarea name="setting[description]"><?php echo $rs['description'] ?></textarea>
	                        <p>插件的简单描述，最多 100 个字节，可选填</p>
	                    </li>
	                </ul>
	                <ul id="module_show" <?php if ($op != 'module'): ?>style="display:none;"<?php endif ?>>
	                    <dl class="blue_table mt10">
	                        <dt style="height:42px; background: none repeat scroll 0 0 #E8F5FC;">选中后提交将被删除选中项</dt>
	                    <div>
	                    <table class="border_table">
	                        <thead>
	                            <tr>
	                                <th width="50"><input type="checkbox" class="check-all"></th>
	                                <th>显示顺序</th>
	                                <th style="width:25%;">模块类型</th>
	                                <th>程序模块(必填)</th>
	                                <th>链接名称</th>
	                                <th>链接URL</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        <?php foreach ($rs['modules'] as $key => $module): ?>
	                            <tr>
	                                <td><input type="checkbox" class="ids" name="module[<?php echo $key ?>][del]" value="1"></td>
	                                <td><input type="text" value="<?php echo $module['displayorder'] ?>" name="module[<?php echo $key ?>][displayorder]" class="small"></td>
	                                <td>
	                                    <select name="module[<?php echo $key ?>][type]" onchange="setType(this)">
	                                        <optgroup label="后台菜单">
	                                        <?php foreach ($nodes as $node): ?>
	                                            <option value="<?php echo $node['id'] ?>"<?php if ($module['type'] == $node['id']): ?> selected<?php endif ?>><?php echo $node['name'] ?></option>
	                                        <?php endforeach ?>
	                                        </optgroup>
	                                        <optgroup label="程序脚本">
	                                            <option value="hook"<?php if ($module['type'] == 'hook'): ?> selected<?php endif ?>>嵌入脚本</option>
	                                            <option value="cache" <?php if ($module['type'] == 'cache'): ?> selected<?php endif ?>>缓存接口</option>
	                                            <option value="menu" <?php if ($module['type'] == 'menu'): ?> selected<?php endif ?>>插件菜单</option>
	                                        </optgroup>
	                                    </select>
	                                </td>
	                                <td><input type="text" value="<?php echo $module['name'] ?>" name="module[<?php echo $key ?>][name]" class="small"><span>.<?php if ($module['type'] == 'cache' || $module['type'] == 'hook'): ?>class.php<?php else: ?>inc.php&nbsp;&nbsp;&nbsp;<?php endif ?></span></td>
	                                <td><input type="text" value="<?php echo $module['menu'] ?>" name="module[<?php echo $key ?>][menu]" class="small" <?php if (!(is_numeric($module['type']) || $module['type'] == 'menu')): ?>style="display:none;"<?php endif ?>></td>
	                                <td><input type="text" value="<?php echo $module['url'] ?>" name="module[<?php echo $key ?>][url]" class="small" <?php if (!(is_numeric($module['type']) || $module['type'] == 'menu')): ?>style="display:none;"<?php endif ?>></td>
	                            </tr>
	                        <?php endforeach ?>
	                            <tr>
	                                <td>新增</td>
	                                <td><input type="text" name="module[new][displayorder]" class="small" value="200"></td>
	                                <td>
	                                    <select name="module[new][type]" onchange="setType(this)">
	                                        <optgroup label="后台菜单">
	                                        <?php foreach ($nodes as $node): ?>
	                                            <option value="<?php echo $node['id'] ?>"><?php echo $node['name'] ?></option>
	                                        <?php endforeach ?>
	                                        </optgroup>
	                                        <optgroup label="程序脚本">
	                                            <option value="hook">嵌入脚本</option>
	                                            <option value="cache">缓存接口</option>
	                                            <option value="menu">插件菜单</option>
	                                        </optgroup>
	                                    </select>
	                                </td>
	                                <td><input type="text" name="module[new][name]" class="small"><span>.class.php</span></td>
	                                <td><input type="text" name="module[new][menu]" id="modules_menu" class="small"></td>
	                                <td><input type="text" name="module[new][url]" id="modules_url" class="small"></td>
	                            </tr>
	                        </tbody>
	                    </table>
	                    </div>
	                    </dl>
	                    <script type="text/javascript">
	                    function setType(obj) {
	                        if($(obj).val() == 'cache' || $(obj).val() == 'hook') {
	                        	$(obj).parents('tr').find('td:eq(3)').find('span').html('.class.php');
	                        } else {
	                            $(obj).parents('tr').find('td:eq(3)').find('span').html('.inc.php&nbsp;&nbsp;&nbsp;');
	                        }
	                        if($.isNumeric($(obj).val()) == true || $(obj).val() == 'menu') {
	                            $(obj).parents('tr').find('td:gt(3)').find('input').show();
	                        } else {
	                            $(obj).parents('tr').find('td:gt(3)').find('input').hide();
	                        }
	                    }
	                    </script>
	                </ul>
	                <ul id="var_show" <?php if ($op != 'var'): ?>style="display:none;"<?php endif ?>>
	                    <dl class="blue_table mt10">
	                        <dt style="height:42px; background: none repeat scroll 0 0 #E8F5FC;">选中后提交将被删除选中项</dt>
	                        <div>
	                        <table class="border_table">
	                            <thead>
	                                <tr>
	                                    <th width="20px"><input type="checkbox" class="check-all"></th>
	                                    <th>显示顺序</th>
	                                    <th style="width:25%;">配置名称(必填)</th>
	                                    <th>配置变量名(必填)</th>
	                                    <th>配置类型</th>
	                                    <th>操作</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                                <?php foreach ($vars as $var): ?>
	                                <tr>
	                                    <td width="20"><input type="checkbox" name="vars[del_pluginvarid][]" class="ids" value="<?php echo $var['pluginvarid']?>"></td>
	                                    <td width="50"><input type="text" value="<?php echo $var['displayorder']?$var['displayorder']:200 ?>" name="vars[displayorders][<?php echo $var['pluginvarid'] ?>]" class="small"></td>
	                                    <td><span><?php echo $var['title'] ?></span></td>
	                                    <td><span><?php echo $var['variable'] ?></span></td>
	                                    <td><span><?php echo $this->fieldtype[$var['type']] ?></span></td>
	                                    <td><a href="<?php echo U('pluginvar', array('pluginvarid' => $var['pluginvarid'])) ?>">详情</a></td>
	                                </tr>
	                                <?php endforeach ?>
	                                <tr>
	                                    <td width="40px"><span>新增</span></td>
	                                    <td><input type="text" value="200" name="vars[new_pluginvar][displayorder]" class="small"></td>
	                                    <td><input type="text" name="vars[new_pluginvar][title]" class="small"></td>
	                                    <td><input type="text" name="vars[new_pluginvar][variable]" class="small"></td>
	                                    <td>
	                                        <select name="vars[new_pluginvar][type]">
	                                            <option value="text">字串(text)</option>
	                                            <option value="number">数字(number)</option>
	                                            <option value="textarea">文本(textarea)</option>
	                                            <option value="radio">单选(radio)</option>
	                                            <option value="checkbox">复选(checkbox)</option>
	                                            <option value="select">单项选择(select)</option>
	                                            <option value="selects">多项选择(selects)</option>
	                                            <option value="datetime">日期/时间(datetime)</option>
	                                            <option value="usergroup">用户等级(usergroup)</option>
	                                            <option value="usergroups">用户等级(usergroups)</option>
	                                        </select>
	                                    </td>
	                                    <td></td>
	                                </tr>
	                            </tbody>
	                        </table>
	                        </div>
	                    </dl>
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
			if($(this).hasClass('more') == false){
			    $(".install dt a").removeClass('hover');
			    $(this).addClass('hover');
			    $('.install dd ul').hide();
			    $('.install dd ul#' + $(this).attr('data') + '_show').show();
			}
		})
	</script>
<?php include $this->admin_tpl('copyright') ?>