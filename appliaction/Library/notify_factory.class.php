<?php
/**
 *      通知模块调用工厂
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class notify_factory {
	public function __construct($param = array() , $adapter_config = array()) {
		if (!is_string($param['id'])  || empty($param)) return false;
		load('@.notify');
		$this->set_adapter($param,$adapter_config);
	}
	/**
	 * 构造适配器
	 * @param  $param 订单号 && 通知code
	 * @param  $adapter_config 通知模块配置
	 */
	public function set_adapter($param , $adapter_config) {
		if (empty($adapter_config)) {
			$drivers = json_decode(model('notify_template')->getFieldById($param['id'],'driver'),TRUE);
			$notifys = getcache('notify', 'notify');
			foreach ($drivers as $k => $driver) {
				if ($notifys[$k]['enabled']==1 && $notifys[$k]['config'] && $driver ==1) {
					$adapter_config[$k] = $notifys[$k]['config'];
				}
			}
		}
		if (!empty($adapter_config)) {
			foreach ($adapter_config as $k => $config) {
				$class_file = EXTEND_PATH.'Driver'.DIRECTORY_SEPARATOR.'notify'.DIRECTORY_SEPARATOR.$k.DIRECTORY_SEPARATOR.$k.'.class.php';
				if (file_exists($class_file)) {
					require_cache($class_file);
					$data = array();
					$data['config'] = $config;
					$data['param'] = $param;
					$this->adapter_instance[$k] = new $k($data);
				}
			}
		}
		return $this->adapter_instance;
	}
	
	// public function __call($method_name, $method_args) {
	// 	if (method_exists($this, $method_name))
	// 		return call_user_func_array(array(& $this, $method_name), $method_args);
	// 	elseif (
	// 		!empty($this->adapter_instance)
	// 		&& ($this->adapter_instance instanceof pay_abstract)
	// 		&& method_exists($this->adapter_instance, $method_name)
	// 	) 
	// 	return call_user_func_array(array(& $this->adapter_instance, $method_name), $method_args);
	// }	
}
?>