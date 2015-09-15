<?php
//动态引用JS
class jspack {
	private static $JSPackArr = array(
	//时间日期
	'hddate' => array('js' => 'hddate/hddate.js'),
	//表单验证
	'hdvalid' => array('js' => array('Validform_v5.3.2_min.js', 'Validform_Datatype.js')),
	//弹出层
	'hddalog' => array('js' => array('artDialog/artDialog.js?skin=default', 'artDialog/plugins/iframeTools.js')),
	//编辑器
	'hdedit' => array('js' => array('Editor/kindeditor-min.js', 'Editor/lang/zh_CN.js'), 'css' => 'Editor/themes/default/default.css'));
	public static function import($path, $name, $charset = 'UTF-8') {
		if (isset(self::$JSPackArr[$name]) && is_array(self::$JSPackArr[$name])) {
			$str = '';
			if (isset(self::$JSPackArr[$name]['css'])) {
				if (is_array(self::$JSPackArr[$name]['css'])) {
					foreach (self::$JSPackArr[$name]['css'] as $css) {
						$str .= '<link rel="stylesheet" type="text/css" href="' . $path . $css . '"/>';
					}
				} else {
					$str .= '<link rel="stylesheet" type="text/css" href="' . $path . self::$JSPackArr[$name]['css'] . '"/>';
				}
			}
			if (isset(self::$JSPackArr[$name]['js'])) {
				if (is_array(self::$JSPackArr[$name]['js'])) {
					foreach (self::$JSPackArr[$name]['js'] as $js) {
						$str .= '<script type="text/javascript" charset="' . $charset . '" src="' . $path . $js . '"></script>';
					}
				} else {
					$str .= '<script type="text/javascript" charset="' . $charset . '" src="' . $path . self::$JSPackArr[$name]['js'] . '"></script>';
				}
			}
			return $str;
		} else {
			return '';
		}
	}

}