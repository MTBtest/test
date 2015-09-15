<?php 
class HelpModel extends SystemModel
{
	protected $_validate  = array(
		array('title', 'require', '主题不能为空', self::EXISTS_VALIDATE, '', self:: MODEL_BOTH),
	);
}