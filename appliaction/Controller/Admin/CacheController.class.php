<?php 
/**
 * @version        $Id$
 * @author         master@xuewl.com
 * @copyright      Copyright (c) 2007 - 2014, Chongqing xuewl Information Technology Co., Ltd.
 * @link           http://www.xuewl.com
**/
class CacheController extends AdminBaseController
{
	public function _initialize() {
		parent::_initialize();
		libfile('syscache');
	}

	// 清理缓存成功
	public function clear($trip = 1) {
		$syscache = new syscache();
		$mods = array(
			array('name' => '商品分类', 	'method' => 'goods_category'),
			array('name' => '支付方式', 	'method' => 'site_payment'),
			array('name' => '配送方式', 	'method' => 'site_delivery'),
			array('name' => '模板标签', 	'method' => 'cache_template'),
			array('name' => '数据库字段', 	'method' => 'cache_field'),
			array('name' => '系统垃圾', 	'method' => 'cache_tmp'),
			array('name' => '错误日志', 	'method' => 'cache_log'),
			array('name' => '地区信息', 	'method' => 'site_region'),
		);
		foreach ($mods as $k => $mod) {
			if(method_exists($syscache, $mod['method'])) {
				$syscache->$mod['method']();
			}
		}
		if($trip == 1){
			showmessage('缓存更新成功');
		}
	}
	//检查缓存
	public function check_cache(){
		$cachedirs[] = 'caches_goods';
		$cachedirs[] = 'caches_pay';
		$cachedirs[] = 'caches_region';
		$cachedirs[] = 'caches_site';
		foreach ($cachedirs as $key => $value) {
			if(!is_dir(DATA_PATH.$value)){
				$this->clear(0);
				return true;
			}
		}
	}
}