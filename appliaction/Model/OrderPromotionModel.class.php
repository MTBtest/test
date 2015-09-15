<?php
class OrderPromotionModel extends SystemModel {
	//自动完成
    protected $_auto = array(
       
    );
    //自动验证
    protected $_validate = array(
        array('name','require','名称不能为空！'), 
        array('start_time,end_time', 'checkDay', '结束日期不能大于开始日期', 1,'callback', 3),
    );
	//开始结束时间
	protected function checkDay($data){
	    if($data['start_time'] > $data['end_time'])
	        return false;
	    else
	        return true;
	}
}