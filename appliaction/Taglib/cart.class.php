<?php 
/**
* 购物车
*/
class cart
{
	public function lists(){
		$carModel = M('car');
		if (!empty($data['limit'])) {
			$carModel->limit($data['limit']);
		}
		$ids = array();
		if($uid = is_login()){
			$car = $carModel->where(array('user_id'=>$uid))->select();
			$car = unserialize($car);
		}
		$cart = unserialize(cookie("cart"));
		$cart = array_merge($car,$cart);
		foreach ($cart as $key => $value) {
			$ids[] = $value['id'];//商品ID
			$nums[$id] = $value['num'];//购物车单个商品数量
		}
		$lists = M('Goods')->where(array('id'=>array('in',$ids)))->select();
		foreach ($lists as $key => $value) {
			$lists[$key]['num'] = $num[$value['id']];
		}
		return $lists;
	}
}