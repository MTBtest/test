<?php
/**
 * 
 * user表模型
 * @author wj
 * @date 2014-10-14
 *
 */
class UserModel extends SystemModel
{
	 //自动完成
    protected $_auto = array(
        array('reg_time', 'time', 1, 'function'), //新增数据是插入注册时间
        //array('password', 'md5', 1, 'function'), //插入时加密密码
    );
    //自动验证
    protected $_validate = array(
       
        array('username', 'require', '用户名必须！'),
        array('password', 'require', '密码必须！'),
        array('userpassword2', 'require', '确认密码必须填写！'),
        array('userpassword2','password','确认密码不正确',0,'confirm'),
        array('mobile_phone', 'require', '手机必须填写！'),
        array('mobile_phone','/^(1[0-9][0-9])\d{8}$/','手机号格式错误！','0','regex',1),
        array('mobile_phone', '', '手机已经存在！', 2, 'unique', 3),
        array('email', 'email', '邮箱格式错误！'),
        array('username', '', '用户名已经存在！', 0, 'unique', 1),
        array('email', '', '邮箱已经存在！', 2, 'unique', 3),
        
    );
    
 	/**
     * ajax验证字段
     */
    function checkKey($val, $key) {
        $map[$key] = array(array('eq', $val));
        $res = $this->where($map)->count();
        if ($res >0) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * 
     * 获取用户详细信息
     * @param  $uid 用户id
     * @author wj
     * @date 2014-10-14
     */
    public function getUserInfo($uid){
    	$map['id'] = array('eq',$uid);
    	$list = $this->relation(true)->where($map)->find();
    	$list['in']['c'] = D('Order')->getCommentNum($uid,1);//未评论数
    	$list['in']['notPay'] = D('Order')->getNOpayNum($uid);//未支付条数
    	$list['in']['notGoods'] = D('Order')->getNOgoodsNum($uid);//未收货条数getDeliveryNum
    	$list['in']['delivery'] = D('Order')->getDeliveryNum($uid);//未收货条数getDeliveryNum
    	return $list;
    }
    /**
     * 
     * 通过地址编号获取城市名称
     * @param  $id 地址编号
     * @author wj
     * @date 2014-10-14
     */
    public function getCityByid($id){
    	$list =  model('region')->field('area_name')->where(array('area_id'=>$id))->find();
    	return $list['area_name'];
    }
    
    /**
     * 
     * 检查密码是否争取
     * @param $password 密码
     * @param $uid 用户id
     * @author wj
     * @date 2014-10-20
     */
    public function chekPassword($uid,$password){
    	$map['id'] = array('eq',$uid);
		$valid = $this->getFieldById($uid,'valid');
    	$map['password'] = array('eq',md5($valid.$password));
    	$r = $this->where($map)->find();
    	if (empty($r)){
    		return false;
    	}else{
    		return true;
    	}
    }
    
    /**
     * 
     * 检查当前用户登录记录的login_cookie和当前浏览器的cookie是否一致 - 来判断该账号在其他地方是否登录 
     * @param $uid 用户id
     * @author wj
     * @date 2014-10-21 
     */
    public function checkSession($uid){
    	$map['id'] = array('eq',$uid);
    	$r = $this->field('last_session')->where($map)->find();    
    	if (session_id() == $r['last_session'] || session_id()==''){
    		return true;
    	}else{
    		return false;
    	}
    }


    /*
     * 判断名称是否唯一
     */
    function unique_name($user_name, $user_id = 0) {
        if($user_id < 1 || empty($user_name)) return FALSE;
        $sqlmap = array();
        $sqlmap['id'] = array("NEQ", $user_id);
        $sqlmap['username'] = $user_name;
        if(!$this->where($sqlmap)->count()) {
            return TRUE;
        }
        return FALSE;
    }
    
    /* ========== 后台 ==========*/
    public function getList($map) {
		$count = $this -> where($map) -> count();
		libfile('Page');
		$pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
		$page = new Page($count, $pagesize);
		$list['list'] = $this -> where($map) -> limit($page -> firstRow . ',' . $page -> listRows) -> select();
		$list['page'] = $page -> show();
		return $list;
	}

	public function push_userids($param) {
		$keyword = $param['keyword'];
		$group_id = $param['group_id'];
		$ordermap['dateline'] = $param['order_time'];
		$ordermap['cat_ids'] = $param['cat_ids'];
		$ordermap['brand_id'] = $param['brand_id'];
		foreach($ordermap as $k=>$v){
			if ($v==0)unset($ordermap[$k]);
		}

		if(!empty($ordermap)){
			$ordermap['_string'] = "1 = 1 ";
		}
		
		//分类
		if($ordermap['cat_ids']){
			//子级分类
			$cat_ids = D('Admin/Category')->getChild($ordermap['cat_ids']);
			$_join = array("find_in_set('".$ordermap['cat_ids']."',cat_ids)");
			foreach ($cat_ids as $key => $value) {
				$_join[] = "find_in_set('{$value}',cat_ids)";
			}
			$join_str = implode(' OR ', $_join);
			if(isset($ordermap['_string'])){
				$ordermap['_string'] .= " AND (".$join_str.")";
			}else{
				$ordermap['_string'] = $join_str;
			}
			unset ($ordermap['cat_ids']);
		}
		//购买时间
		if($ordermap['dateline']){
			$ordermap['_string'] .= " AND DATE_SUB(CURDATE(), INTERVAL {$ordermap['dateline']} DAY) <= date(FROM_UNIXTIME(dateline)) ";
			unset ($ordermap['dateline']);
		}

		//搜索会员
		if (isset($keyword) && $keyword) {
			$field = "id,username,email,mobile_phone";
			$where['status'] = array('gt', -1);
			$where['_string'] = "username = '{$keyword}' OR email = '{$keyword}' OR mobile_phone = '{$keyword}' ";
			$info = $this -> field($field) -> where($where) -> find();
		}
		//会员等级

		if (isset($group_id)) {
			unset($where);
			if ($group_id) {
				$where['group_id'] = $group_id;
			}
			$info = $this -> where($where) -> order("id ASC") -> getField("id", true);
		}
		if(!empty($ordermap)){
			$user_idArr = D('Admin/OrderGoods')->where($ordermap)->getField("user_id",true);
			$user_idArr = array_unique($user_idArr);	
			$info = array_intersect($info, $user_idArr);	//取交集
		}
		
		return $info;
	}

}
?>