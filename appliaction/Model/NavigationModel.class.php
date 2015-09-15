<?php 
class NavigationModel extends SystemModel
{
	protected $_validate  = array(
		array('name', 'require', '区域名称不能为空', self::EXISTS_VALIDATE, '', self:: MODEL_BOTH),
	);
}