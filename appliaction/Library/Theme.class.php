<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class Theme  {
	public function __construct() {
		$this->db = D('Payment');
	}
		
	/**
	 * 获取支付类型列表
	 */
	public function get_list() {
		return $this->get_theme();
	 }
	 		
	/**
	 * 获取插件目录信息
	 * @param unknown_type $code
	 */
	public function get_theme( $code = '') {
		$theme_info = array();
		$modules = $this->read_theme(TMPL_PATH);
		foreach($modules as $directory => $theme) {
			$theme['isdefault'] = ($directory == C('TMPL_THEME')) ? 1 : 0 ;
			$theme_info[$directory] = $theme;
		}
		if (empty($code)) {
			return $theme_info;
		} else {
			return $theme_info[$code];
		}		
	}
	
	/**
	 * 读取插件目录中插件列表
	 * @param unknown_type $directory
	 */
	public function read_theme($directory = ".") {
		$dir = @opendir($directory);
		$set_modules = true;
		$modules = array();
		libfile('Xml');
		while (($file = @readdir($dir)) !== false) {
			if($file == '.' || $file == '..' || !is_dir($directory.$file)) continue;
			if (file_exists($directory.$file.DIRECTORY_SEPARATOR.'config.xml')) {
				$config = @file_get_contents($directory.$file.DIRECTORY_SEPARATOR.'config.xml');
				$xml = xml2array($config);

				if (file_exists($directory.$file.DIRECTORY_SEPARATOR.'thumb.png')) {
					$thumb_dir = $directory.$file.'/';
				} else {
					$thumb_dir = $directory.'/';
				}
				$xml['thumb'] = str_replace(DOC_ROOT, ROOT_PATH, $thumb_dir.'thumb.png');
				if($file == 'wap') continue;
				$modules[$file] = $xml;
			}
		}
		@closedir($dir);
		foreach ($modules as $key => $value ) {
			asort($modules[$key] );
		}
		asort( $modules );		
		return $modules;
	}		
}
?>