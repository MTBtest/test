<?php 
class FriendLinkModel extends SystemModel
{
	protected $_validate  = array(
		array('name', 'require', '链接名称不能为空', self::EXISTS_VALIDATE, '', self:: MODEL_BOTH),
	);
}