<?php
/**
 *      会员收货地址模型
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class UserAddressModel extends SystemModel{
    protected $_validate = array(
       array('user_id','require','数据异常！'),
       array('address_name','require','请填写收件人姓名！'),
       array('zipcode','require','请填写邮编！'),
       array('address','require','请填写收货人详细地址！'),
       array('mobile','require','请填写电话！'),
    );
	
  	protected $_auto = array(
  		array('best_time', NOW_TIME, Model:: MODEL_BOTH, 'string'),
  	);
	
	
  	public function detail($id, $field = TRUE) {
  		$row = parent::detail($id);
  		if(!$row) {
  			$this->error = parent::getError();
  		}
  		$row['province_name'] = model('region')->where(array('area_id' => $row['province']))->getField('area_name');
  		$row['city_name'] = model('region')->where(array('area_id' => $row['city']))->getField('area_name');
  		$row['district_name'] = model('region')->where(array('area_id' => $row['district']))->getField('area_name');
  		return $row;
  		
  	}

    /**
     * 
     * 通过地址id获取该条记录
     * @param  $map 条件
     * @author wj
     * @date 2014-10-15
     */     
    public function getInfo($map){
    		return $this->where($map)->select();
    }
   
    /**
    * 
    * 获取当前用户的默认地址
    * @param $address_id  用户地址
    * @author wj
    * @date 2014-10-15
    */
    public function DefautAddress($address_id){
    	$map['id'] = array('eq',$uid);
    	return 	D('User')->where($map)->find();
    }

}
?>