<?php
/**
 * 
 * user表模型
 * @author wj
 * @date 2014-10-14
 *
 */
class UserGroupModel extends SystemModel
{
	 //自动完成
    protected $_auto = array(
    );
    //自动验证
    protected $_validate = array(
        array('name', 'require', '名称必须！'),
        array('min_points', 'number', '最小经验必须是数字！'),
        array('max_points', 'number', '最大经验必须是数字！'),
        array('discount', '/^\d{1,3}$/', '折扣为1-100的整数', 1),
    );
    
 	

}
?>