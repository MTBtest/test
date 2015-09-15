<?php 
class FocusModel extends SystemModel
{
	protected $_validate  = array(
		array('pic', 'require', '图片不能为空', self::EXISTS_VALIDATE, '', self:: MODEL_BOTH),
	);
}