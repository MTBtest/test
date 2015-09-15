<?php
class IndexController extends AdminBaseController {
   public function _initialize() {
        parent::_initialize();    
    }
    public function index(){
    	include $this->admin_tpl('index');
            
    }

    public function home() {
    	/* 今日统计概况 */		
    	$today_map = array();
		$today_map['order_status'] = array("LT", 3);
		$today_map['_string'] = "DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
    	$today_count = model('order')->cache(TRUE, 3600)->field("sum(real_amount) AS real_amount, COUNT(id) AS total_order")->where($map_today)->find();
		$today_count['real_amount'] = sprintf('%.2f', $today_count['real_amount']);
		$today_count['per_cust_transaction'] = sprintf('%.2f', $today_count['real_amount'] / $today_count['total_order']);
		
		/* 新增会员 */
		$user_map = array();
		$user_map['_string'] = "DATE_FORMAT(FROM_UNIXTIME(reg_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
		$today_count['user_reg_num'] = (int) model('user')->cache(TRUE, 3600)->where($user_map)->count();
		/* 评论统计 */
		$comm_map = array();
		$comm_map['_string'] = "DATE_FORMAT(FROM_UNIXTIME(time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
		$today_count['user_comm_num'] = (int) model('comment')->cache(TRUE, 3600)->where($comm_map)->count();
		
		/* 订单处理情况 */
		$order_count = $order_map = array();
		$order_map['order_status'] = 0;
		$order_count['confirm'] = model('order')->where($order_map)->count();
		
		$order_map['order_status'] = 2;
		$order_count['finish'] = model('order')->where($order_map)->count();		
		$order_map = array();
		$order_map['delivery_status'] = 0;
		$order_count['delivery'] = model('order')->where($order_map)->count();
		// 商品咨询
		$order_count['consult_total'] = model('consult')->count();
		$order_count['consult_reply'] = model('consult')->where(array('reply_time' => 0))->count();
		
		/* 商品信息统计 */
		// 商家商品总数
		$goods_count['sell_num'] = model('goods')->cache(TRUE, 3600)->where(array('status' => 1))->count();
		// 缺货登记
		$goods_count['goods_message'] = model('goods_message')->cache(TRUE, 3600)->count();
		// 库存警告
		$goods_map = array();
		$goods_map['_string'] = "g.warn_number >= p.goods_number";
		$goods_count['warn_number'] = count(model('goods')->alias('g')->join("__GOODS_PRODUCTS__ AS p ON g.id = p.goods_id")->where($goods_map)->cache(TRUE, 3600)->group('g.id')->field("COUNT(distinct('g.id'))")->select());		
		// 今日发放优惠券
		$coupons_map = array();
		$coupons_map['_string'] = "DATE_FORMAT(FROM_UNIXTIME(to_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
		$goods_count['coupons_to'] = model('coupons_list')->cache(TRUE, 3600)->where($coupons_map)->count();
		$coupons_map['_string'] = "DATE_FORMAT(FROM_UNIXTIME(use_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
		$goods_count['coupons_use'] = model('coupons_list')->cache(TRUE, 3600)->where($coupons_map)->count();
    	/* 系统信息 */
    	$sys_info = get_sysinfo();
    	include $this->admin_tpl('home');
    }

	/**
     * 修改密码
     */
    public function changepwd() {
    	$validform = TRUE;
    	$db = model('AdminUser');
		$r = $db->where('id = ' . ADMIN_ID)->find();
		extract($r);
		if (IS_POST){
			if(md5($_POST['opassword'].$_POST['valid']) != $password ){
				showmessage('原密码不对');exit();
			}
			if(!empty($_POST['password']) && !empty($_POST['npassword']) && $_POST['password'] != $_POST['npassword']){
				showmessage('两次新密码不一致');exit();
			}
			if($db->where('email = \'' . $_POST['email'] . '\' AND id !='. ADMIN_ID)->count() > 0){
				showmessage('邮箱已存在');exit();
			}			
			if(!empty($_POST['password']) && !empty($_POST['npassword'])){
				$data['valid'] = random(10);
				$data['password'] = md5($_POST['npassword'].$data['valid']);
			}
			if($email != $_POST['email']){
				$data['email'] = $_POST['email'];
			}
			if($data){
				$db->where('id = ' . ADMIN_ID)->save($data);
				showmessage('更新信息成功!',U('changepwd'),1);
			}else{
				showmessage('本次无更新信息!',U('changepwd'),1);
			}
			
		}else{
        	include $this->admin_tpl('admin_changepwd');
		}
    }
}