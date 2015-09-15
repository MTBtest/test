<?php
class hook {
	protected $system_hook = array();
	protected $plugin_hook = array();
	
	public function __construct() {
		load('@.hook');
		libfile('plugin');
		if(C('SYSTEM_HOOK_LIST')) {
			$this->system_hook = str2arr(C('SYSTEM_HOOK_LIST'));
			$this->plugin_hook = getcache('hooks', 'commons');
		}
	}
	
	public function run($hookid, &$param, $type = 'string') {
		$hookfiles = $this->system_hook;
		if($this->plugin_hook) $hookfiles = array_merge($this->system_hook, $this->plugin_hook);
		$result = ($type == 'string') ? '' : array();
		foreach($hookfiles as $name) {
			if(in_array($name, $this->system_hook)){
				$hookfile = APP_PATH.'Hook'.DIRECTORY_SEPARATOR.$name.'.class.php';
				$classname = $name;
				$hookname = '';
			} else {
				$hookfile = PLUGIN_PATH.$name.'.class.php';				
				$classarr = explode("/", $name);
				$hookname = current($classarr);
				$classname = 'plugin_'.current($classarr);
			}
			require_cache($hookfile);
			if(!class_exists($classname)) continue;
			$class = new $classname($hookname);
			if(method_exists($class, $hookid)) {
				if($type == 'string') {
					$result .= $class->$hookid($param);
				} else {
					$result[] = $class->$hookid($param);
				}				
			}
		}
		return $result;		
	}
}