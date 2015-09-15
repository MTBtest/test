<?php
class plugin_regexp extends plugin{
		
	/**
	 * 注册成功
	 */
	public function user_public_reg_success () {
		$db = model('reg_promotion');
		$rs = $db->find();
		$uid = is_login();
		$current_time = NOW_TIME;
		if(isset($rs) && isset($uid)){
			extract($rs);
			if(($current_time >= $start_time) && ($current_time<=$end_time) && ($status == 1)){
				//积分
				if($pay_points > 0){
					action_point($uid, $pay_points, $this->pluginvar['log_text']?$this->pluginvar['log_text']:'新注册奖励积分'); 
				}
				//优惠券
				if($coupons_id > 0){
					action_coupons($uid, $coupons_id);
				}
			}
		}
	}
	
}