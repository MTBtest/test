<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
define('IN_PLUGIN', TRUE);
class PluginController extends AdminBaseController
{
	protected $error = '';

	public function _initialize() {
		parent::_initialize();
		$this->plugin_db = model('plugin');
		$this->pluginvar_db = model('pluginvar');
		$this->fieldtype = array(
			'text'      => '字串(text)',
			'number'    => '数字(number)',
			'textarea'  => '文本(textarea)',
			'radio'     => '单选(radio)',
			'checkbox'  => '复选(checkbox)',
			'select'    => '单项选择(select)',
			'selects'   => '多项选择(selects)',
			'datetime'  => '日期/时间(datetime)',
			'usergroup' => '用户等级(usergroup)',
			'usergroups'  => '用户等级(usergroups)',
		);
		libfile('Xml');
	}

	/* 主题列表 */
	public function manage() {
		$type = (int)$_GET['type'];
		$plugin_folder = dir(PLUGIN_PATH);
		$plugin_dir = str_replace(DOC_ROOT, '', PLUGIN_PATH);

		$addons = $this->plugin_db->select();
		foreach($addons as $plugin) {
			$plugin['modules'] = unserialize($plugin['modules']);
			$plugins[$plugin['identifier']] = $plugin;
		}

		while($entry = $plugin_folder->read()) {
			if(!in_array($entry, array('.', '..')) && is_dir(PLUGIN_PATH.$entry)) {
				$entrydir = PLUGIN_PATH.$entry;
				$importfile = $entrydir.'/'.$entry.'.xml';
				if(!file_exists($importfile)) continue;
				$importtxt = @implode('', file($importfile));
				$xmldata = xml2array($importtxt);
				if (!in_array($entry, array_keys($plugins))) {
					$plugin = array(
						'pluginid'    => 0,
						'available'   => 0,
						'adminid'     => $xmldata['Data']['plugin']['adminid'],
						'name'        => $xmldata['Data']['plugin']['name'],
						'identifier'  => $xmldata['Data']['plugin']['identifier'],
						'description' => $xmldata['Data']['plugin']['description'],
						'datatables'  => $xmldata['Data']['plugin']['datatables'],
						'directory'   => $xmldata['Data']['plugin']['directory'],
						'author'      => $xmldata['Data']['plugin']['author'],
						'copyright'   => $xmldata['Data']['plugin']['copyright'],
						'directory'   => $xmldata['Data']['plugin']['directory'],
						'modules'     => $xmldata['Data']['plugin']['modules'],
						'version' => $xmldata['Data']['plugin']['version'],
					);
				} else {
					$plugin = $plugins[$entry];
				}
				$plugin['new_ver'] = $xmldata['Data']['plugin']['version'];				
				$plugins[$entry] = $plugin;
			}
		}
		foreach ($plugins as $key => $value) {
			switch ($type) {
				case '1':
					if(!($value['pluginid'] > 0 && $value['available'] == 1)) unset($plugins[$key]);
					break;
				case '2':
					if(!($value['pluginid'] > 0 && $value['available'] == 0)) unset($plugins[$key]);
					break;
				case '3':
					if($value['pluginid'] > 0) unset($plugins[$key]);
					break;
				default:
					# code...
					break;
			}
		}
		include $this->admin_tpl('plugin_list');
	}

	/* 插件安装 */
	public function install($identifier = '') {
		$plugin_folder = PLUGIN_PATH.$identifier;
		$xmldata  = $this->getXmlConfig($identifier);
		if(!$xmldata) {
			showmessage($this->error);
		}
		$plugin_data = array(
			'available' => 0,
			'adminid' => $xmldata['Data']['plugin']['adminid'],
			'name' => $xmldata['Data']['plugin']['name'],
			'identifier' => $xmldata['Data']['plugin']['identifier'],
			'description' => $xmldata['Data']['plugin']['description'],
			'datatables' => $xmldata['Data']['plugin']['datatables'],
			'directory' => $xmldata['Data']['plugin']['directory'],
			'copyright' => $xmldata['Data']['plugin']['copyright'],
			'modules' => serialize($xmldata['Data']['plugin']['modules']),
			'version' => $xmldata['Data']['plugin']['version'],
			'author' => $xmldata['Data']['plugin']['author'],
		);
		
		/* 执行安装文件 */
		if($xmldata['Data']['installfile'] && file_exists($plugin_folder.'/'.$xmldata['Data']['installfile'])) {
			include $plugin_folder.'/'.$xmldata['Data']['installfile'];
		}
		$pluginid = $this->plugin_db->update($plugin_data);
		if (!$pluginid) {
			showmessage('插件安装失败');
		} else {			
			/* 创建插件字段 */
			$vars = array();
			foreach ($xmldata['Data']['var'] as $v) {
				$v['pluginid'] = $pluginid;
				$vars[] = $v;
			}
			$this->pluginvar_db->addAll($vars);
			/* 创建后台菜单 */
			$nodes = array();
			foreach($xmldata['Data']['plugin']['modules'] as $module) {
				if(is_numeric($module['type'])) {
					$nodes[] = array(
						'parentid' => $module['type'],
						'name' => $module['menu'],
						'sort' => $module['displayorder'],
						'url' => U('Admin/Plugin/module', array('pluginid' => $pluginid, 'mod' => $module['name'])),
						'pluginid' => $pluginid,
					);
				}
			}
			if($nodes) model('node')->addAll($nodes);
			/* 更新缓存 */
			$this->plugin_db->build_cache();
			showmessage('插件安装成功', U('manage', array('type' => 2)), 1);
		}
	}

	/* 插件更新 */
	public function upgrade($identifier = '') {
		$xmldata  = $this->getXmlConfig($identifier);
		if(!$xmldata) {
			showmessage($this->error);
		}
		$plugin_folder = PLUGIN_PATH.$identifier;
		/* 先卸载再安装 */
		$sqlmap = array();
		$sqlmap['identifier'] = $identifier;

		$version = $this->plugin_db->where($sqlmap)->getField('version');
		if($version >= $xmldata['Data']['plugin']['version']) {
			showmessage('该插件没有新版本，无需升级');
		}

		$this->pluginvar_db->where($sqlmap)->select();
		$this->plugin_db->where($sqlmap)->delete();

		$plugin_data = array(
			'available' => 0,
			'adminid' => $xmldata['Data']['plugin']['adminid'],
			'name' => $xmldata['Data']['plugin']['name'],
			'identifier' => $xmldata['Data']['plugin']['identifier'],
			'description' => $xmldata['Data']['plugin']['description'],
			'datatables' => $xmldata['Data']['plugin']['datatables'],
			'directory' => $xmldata['Data']['plugin']['directory'],
			'copyright' => $xmldata['Data']['plugin']['copyright'],
			'modules' => serialize($xmldata['Data']['plugin']['modules']),
			'version' => $xmldata['Data']['plugin']['version'],
			'author' => $xmldata['Data']['plugin']['author'],
		);
		/* 执行安装文件 */
		if($xmldata['Data']['upgradefile'] && file_exists($plugin_folder.'/'.$xmldata['Data']['upgradefile'])) {
			include $plugin_folder.'/'.$xmldata['Data']['upgradefile'];
		}
		$result = $this->plugin_db->update($plugin_data);
		if (!$result) {
			showmessage('插件升级失败');
		} else {
			/* 创建插件字段 */
			$vars = array();
			foreach ($xmldata['Data']['var'] as $v) {
				$v['pluginid'] = $result;
				$vars[] = $v;
			}
			$this->pluginvar_db->addAll($vars);
			$this->plugin_db->build_cache();
			showmessage('插件升级成功', U('manage', array('type' => 2)), 1);
		}		
	}

	public function uninstall($identifier = '') {
		$plugin_folder = PLUGIN_PATH.$identifier;
		$xmldata  = $this->getXmlConfig($identifier);
		if(!$xmldata) {
			showmessage($this->error);
		}
		$sqlmap = array();
		$sqlmap['identifier'] = $identifier;
		$pluginid = $this->plugin_db->where($sqlmap)->getField('pluginid');
		if(!$pluginid) {
			showmessage('插件不存在');
		}
		$this->pluginvar_db->where(array('pluginid' => $pluginid))->delete();
		$this->plugin_db->where($sqlmap)->delete();
		/* 执行安装文件 */
		if($xmldata['Data']['uninstallfile'] && file_exists($plugin_folder.'/'.$xmldata['Data']['uninstallfile'])) {
			include $plugin_folder.'/'.$xmldata['Data']['uninstallfile'];
		}
		/* 卸载菜单 */
		model('node')->where(array('pluginid' => $pluginid))->delete();
		$this->plugin_db->build_cache();
		showmessage('插件卸载成功', U('manage'), 1);	
	}

	public function available($identifier = '') {
		if(empty($identifier)) {
			showmessage('参数错误');
		}
		$sqlmap = array();
		$sqlmap['identifier'] = $identifier;
		$_available = $this->plugin_db->where($sqlmap)->getField('available');
		if($_available == 1) {
			$available = 0;
			$msg = '禁用';
		} else {
			$available = 1;
			$msg = '启用';			
		}
		$result = $this->plugin_db->where($sqlmap)->setField('available', $available);
		if(!$result) {
			showmessage('插件'.$msg.'失败');
		}
		$this->plugin_db->build_cache();
		showmessage('插件'.$msg.'成功', U('manage'), 1);
	}

	/* 获取指定插件配置文件 */
	private function getXmlConfig($identifier = '') {
		if (empty($identifier)) {
			$this->error = '参数错误';
			return FALSE;
		}
		$plugin_folder = PLUGIN_PATH.$identifier;
		if(!is_dir($plugin_folder) || !file_exists($plugin_folder)) {
			$this->error = '插件目录不存在';
			return FALSE;
		}
		$plugin_xml = $plugin_folder.'/'.$identifier.'.xml';
		if(!file_exists($plugin_xml)) {
			$this->error = '插件配置文件丢失（'.$plugin_xml.'）';
			return FALSE;
		}
		/* 检测重复安装 */
		$importtxt = @implode('', file($plugin_xml));
		$xmldata = xml2array($importtxt);
		return $xmldata;
	}
	
	/* 插件设置 */
	public function setting($pluginid = '') {
		if(empty($pluginid)) {
			showmessage('参数错误');
		}
		$rs = $this->plugin_db->find($pluginid);
		if(!$rs) {
			showmessage('插件不存在');
		}
		$rs['modules'] = unserialize($rs['modules']);
		$submenu = array();
		if($rs['modules']) {
			foreach ($rs['modules'] as $key => $value) {
				if($value['type'] != 'menu') continue;
				$submenu[$key] = $value;
			}
		}

		$vars = $this->pluginvar_db->where(array('pluginid' => $rs['pluginid']))->order("displayorder ASC, pluginvarid ASC")->select();
		$plugin_folder = PLUGIN_PATH.$rs['identifier'];
		$plugin_dir = str_replace(DOC_ROOT, '', PLUGIN_PATH);
		define('PLUGIN_ID', $rs['identifier']);
		if(IS_POST) {
			$sqlmap = array();
			$sqlmap['pluginid'] = $pluginid;
			foreach ($_GET as $key => $value) {
				if(is_array($value)) $value = arr2str($value);
				$sqlmap['variable'] = $key;
				$this->pluginvar_db->where($sqlmap)->setField('value', $value);
			}
			$this->plugin_db->build_cache();
			showmessage('插件设置成功', U('setting', array('pluginid' => $pluginid)));
		} else {
			libfile('form');
			if(!empty($_GET['mod']) && !file_exists($plugin_folder.'/'.$_GET['mod'].'.inc.php')) {
				showmessage('模块文件不存在');
			}
			include $this->admin_tpl('plugin_setting');
		}
	}
	
	/* 插件设计 */
	public function develop($pluginid = 0) {
		$op = (isset($_GET['op'])) ? trim($_GET['op']) : 'setting';
		$rs = $vars = array();
		if($pluginid) {
			$sqlmap = array();
			$sqlmap['pluginid'] = $pluginid;
			$rs = $this->plugin_db->where($sqlmap)->find();
			if(!$rs) {
				showmessage('插件不存在');
			}
			$rs['modules'] = unserialize($rs['modules']);
			$vars = $this->pluginvar_db->where($sqlmap)->order('displayorder ASC,pluginvarid ASC')->select();	
		}
		$nodes = model('node')->where(array('parentid' => '0', 'id' => array("NEQ", 8)))->order("sort ASC, id ASC")->select();
		if (IS_POST) {
			/* 模板配置 */
			$message = '配置';
			$_GET['module'][] = $_GET['module']['new'];
			unset($_GET['module']['new']);
			$modules = array();
			foreach ($_GET['module'] as $key => $value) {
				$value['displayorder'] = (int) $value['displayorder'];
				if(!$value['name'] || $value['del'] == 1) continue; //删除没有名字的和选中项目的项2014-12-13 14:15:11
				$modules[] = $value;
			}
			$modules = multi_array_sort($modules, 'displayorder', SORT_ASC);
			$_GET['setting']['directory'] = $_GET['setting']['identifier'].'/';
			$_GET['setting']['modules'] = serialize($modules);
			$_GET['setting']['pluginid'] = $pluginid;
			/* 基本配置 */
			$result = $this->plugin_db->update($_GET['setting']);
			if(!$result) showmessage($this->plugin_db->getError());
			if($pluginid == 0) {
				$pluginid = $result;
				$message = '创建';
				/* 自动创建插件目录 */
				dir_create(PLUGIN_PATH.$_GET['setting']['identifier']);
			}
			$pluginid = ($pluginid > 0) ? $pluginid : $result;
			/* 变量配置 */
			if ($_GET['vars']) {
				/* 删除选中项2014-12-13 14:20:25 */
				$del_ids = $_GET['vars']['del_pluginvarid'];
				if($del_ids){
					$this->pluginvar_db->where(array('pluginvarid'=>array('IN',$del_ids)))->delete();
				}
				$new_pluginvar = $_GET['vars']['new_pluginvar'];
				if($new_pluginvar['title'] && $new_pluginvar['variable'] && isset($this->fieldtype[$new_pluginvar['type']])) {
					$new_pluginvar['displayorder'] = (int) $new_pluginvar['displayorder'];
					if($this->pluginvar_db->where(array('pluginid' => $pluginid, 'variable' => $new_pluginvar['variable']))->count() == 0) {
						$new_pluginvar['pluginid'] = $pluginid;
						$this->pluginvar_db->add($new_pluginvar);
					}
				}
				if($_GET['vars']['displayorders']) {
					$displayorders = $_GET['vars']['displayorders'];
					foreach ($displayorders as $pluginvarid => $displayorder) {
						$this->pluginvar_db->where(array('pluginvarid' => $pluginvarid))->setField('displayorder', (int) $displayorder);
					}
				}
			}
			$this->plugin_db->build_cache();
			showmessage('插件'.$message.'成功', U('develop', array('pluginid' => $pluginid)));
		} else {
			include $this->admin_tpl('plugin_develop');
		}
	}

	/* 插件模块 */
	public function module($pluginid = 0, $mod = '') {
		$pluginid = (int) $pluginid;
		$rs = $this->plugin_db->find($pluginid);
		$rs['modules'] = unserialize($rs['modules']);
		$nav_title = '插件配置';
		foreach ($rs['modules'] as $v) {
			if(is_numeric($v['type']) && $v['name'] == $mod) {
				$nav_title = $v['menu'];
				break;
			}
		}
		if(!$rs) showmessage('插件不存在');
		if(!$mod) showmessage('参数错误');
		$plugin_folder = PLUGIN_PATH.$rs['identifier'];
		define('PLUGIN_ID', $rs['identifier']);
		$modfile = $plugin_folder.'/'.$mod.'.inc.php';
		if(!file_exists($modfile)) {
			showmessage('模块文件不存在');
		}
		include $this->admin_tpl('plugin_module');
	}

	/* 字段编辑 */
	public function pluginvar($pluginvarid = 0) {
		$rs = $this->pluginvar_db->find($pluginvarid);
		if(IS_POST) {
			$_GET['pluginvar']['pluginvarid'] = $pluginvarid;
			$result = $this->pluginvar_db->update($_GET['pluginvar']);
			if(!$result) {
				showmessage('变量配置失败');
			} else {
				$this->plugin_db->build_cache();
				showmessage('变量配置成功', U('develop', array('pluginid' => $rs['pluginid'], 'op' => 'var')));
			}
		} else {
			include $this->admin_tpl('plugin_pluginvar');
		}
	}
	
	/* 插件导出 */
	public function export($pluginid = 0) {
		$pluginid = (int) $pluginid;
		$rs = $this->plugin_db->find($pluginid);
		if(!$rs) showmessage('插件不存在');
		$rs['modules'] = unserialize($rs['modules']);
		$plugin_array = array(
			'Title' => C('site_name'),
			'Version' => C('VERSION'),
			'Time' => mdate(NOW_TIME),
			'From' => C('site_name').'('.C('site_companyurl').')',
			'Data' => array(
				'plugin' => $rs,
			),
		);
		$plugin_folder = PLUGIN_PATH.$rs['identifier'];
		$vars = $this->pluginvar_db->where(array('pluginid' => $pluginid))->order("displayorder ASC, pluginvarid ASC")->select();
		if($vars) {
			$plugin_array['Data']['var'] = $vars;
		}		
		$incfiles = array('install', 'uninstall', 'upgrade', 'check', 'enable', 'disable');
		foreach ($incfiles as $file) {
			if(file_exists($plugin_folder.'/'.$file.'.php')) {
				$plugin_array['Data'][$file.'file'] = $file.'.php';
			}
		}	
		unset($plugin_array['Data']['plugin']['dateline'], $plugin_array['Data']['plugin']['pluginid']);
		$plugin_export = array2xml($plugin_array, 1);
		ob_end_clean();
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-Encoding: none');
		header('Content-Length: '.strlen($plugin_export));
		header('Content-Disposition: attachment; filename='.$rs['identifier'].'.xml');
		header('Content-Type: text/xml');
		echo $plugin_export;
	}

	/* 重新生成插件缓存 */
	private function build_cache() {
		return TRUE;
	}
}