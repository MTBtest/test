<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class PluginModel extends SystemModel
{
	protected $_validate  = array(
		array('name', 'require', '插件名称不能为空', Model::EXISTS_VALIDATE, 'regex', Model:: MODEL_BOTH),
		array('version', 'require', '插件版本号不能为空', Model::EXISTS_VALIDATE, 'regex', Model:: MODEL_BOTH),
		array('identifier', 'require', '插件标识不能为空', Model::EXISTS_VALIDATE, 'regex', Model:: MODEL_BOTH),
		array('identifier', '', '插件标识已存在', Model::EXISTS_VALIDATE, 'unique', Model:: MODEL_BOTH),
	);
	protected $_auto = array (
		array('dateline', NOW_TIME, Model:: MODEL_BOTH, 'string'),
	);

	/* 生成缓存 */
	public function build_cache() {
		$sqlmap = array();
		$sqlmap['available'] = 1;
		$tmp = $this->where($sqlmap)->select();
		if($tmp) {
			$hooks = array();
			foreach ($tmp as $t) {
				$t['modules'] = unserialize($t['modules']);
				foreach($t['modules'] as $mod) {
					if($mod['type'] == 'hook') $hooks[] = $t['identifier'].'/'.$mod['name'];
				}
				$plugins[$t['identifier']] = $t;
				$pluginvars[$t['identifier']] = $this->getPluginVar($t['pluginid']);
			}
		}
		setcache('hooks', $hooks, 'commons');
		setcache('plugins', $plugins, 'commons');
		setcache('pluginvars', $pluginvars, 'commons');
	}

	/* 获取指定插件的设置 */
	private function getPluginVar($pluginid) {
		$sqlmap = array();
		$sqlmap['pluginid'] = $pluginid;
		return model('pluginvar')->where($sqlmap)->getField('variable, value', TRUE);
	}
}