<?php 
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
define('IN_PLUGIN', TRUE);
class PluginController extends BaseController {
	public function _initialize() {
		parent::_initialize();
		$id = $_GET['id'];
		list($id, $module) = explode(':', $id);
		if (empty($id)) showmessage('参数错误');
		$plugins = getcache('plugins', 'commons');
		$pluginvars = getcache('pluginvars', 'commons');
		$plugin = $plugins[$id];
		$pluginvar = $pluginvars[$id];
		if(!in_array($id, array_keys($plugins))) {
			showmessage('插件不存在或未开启');
		}		
		$module = !$module ? $id : $module;
		define('PLUGIN_ID', $id);
		define('PLUGIN_MODULE', $module);
		$libfile = PLUGIN_PATH.$id.DIRECTORY_SEPARATOR.$module.'.inc.php';
		if (!file_exists($libfile)) die('访问模块不存在');
		include $libfile;
	}

	/* 强制退出 */
	public function _empty() {
		return FALSE;
	}
}