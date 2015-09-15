<?php
class CountController extends AdminBaseController
{
	public function _initialize() {
		parent::_initialize();
		$this->order_db = model('order');
		$this->user_db = model('user');
	}
	
	/* 销售分析 */
	public function sell() {
		$map_today = array();
		$map=array();
		/* 今日销售情况 */
		$map_today['order_status'] = array("LT", 3);
		$map_today['_string'] = "DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
		$sell_today = $this->order_db->field("sum(real_amount) AS real_amount, COUNT(id) AS total_order")->where($map_today)->find();
		$sell_today['real_amount'] = sprintf('%.2f', $sell_today['real_amount']);
		$sell_today['per_cust_transaction'] = sprintf('%.2f', $sell_today['real_amount'] / $sell_today['total_order']);	
		$map['_string']="DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
		$map['order_status']=array("eq",4);
		$sell_today_cancel=$this->order_db->field("COUNT(id) AS cancel_num")->where($map)->find();
		/* 今日当前时间销售额 */
		$map_today['create_time'] = array("ELT", NOW_TIME);
		$amount_today = $this->order_db->where($map_today)->sum('real_amount');
		/* 昨日销售情况 */
		$map_today['create_time'] = array("ELT", NOW_TIME - 86400);
		$map_today['_string'] = "DATEDIFF(NOW() , FROM_UNIXTIME(`create_time`)) = 1";
		$amount_yesterday = $this->order_db->where($map_today)->sum('real_amount');
		/* 涨/跌幅公司：(今日销售额 - 昨日销售额) / 昨日销售额 * 100% */
		$sell_gain = sprintf('%.2f', (($amount_today - $amount_yesterday) / $amount_yesterday * 100));
		/* 时间周期销售概况 */
		$days = (isset($_GET['days'])) ? (int) $_GET['days'] : 7;
		$sqlmap = array();
		$sqlmap['order_status'] = array("LT", 3);		
		switch ($days) {
			case '7':
				$sqlmap['create_time'] = array("GT", NOW_TIME - 86400 * 7);
				$_start_time = mdate(NOW_TIME - 86400 * 6, 'Y-m-d');
				$_end_time = mdate(NOW_TIME, 'Y-m-d');
				break;
			case '30':
				$sqlmap['create_time'] = array("GT", NOW_TIME - 86400 * 30);
				$_start_time = mdate(NOW_TIME - 86400 * 29, 'Y-m-d');
				$_end_time = mdate(NOW_TIME, 'Y-m-d');
				break;
			default:
				$create_time = array();
				$start_time = strtotime($_GET['start_time'].' 00:00:00');
				$end_time = strtotime($_GET['end_time'].' 23:59:59');
				$create_time[] = array("GT", $start_time);
				$create_time[] = array("LT", $end_time);
				$create_time[] = 'AND';
				$sqlmap['create_time'] = $create_time;
				$days = ceil(($end_time - $start_time) / 86400);
				$_start_time = mdate($start_time, 'Y-m-d');
				$_end_time = mdate($end_time, 'Y-m-d');
				break;
		}
		$time_sell = $this->order_db->field("sum(real_amount) AS real_amount, COUNT(id) AS total_order")->where($sqlmap)->find();
		//时间段类注册用户
		$map_user = array();
		$map_user['reg_time'] = $sqlmap['create_time'];
		$user_ids = model('user')->where($map_user)->getField('id', TRUE);
		
		$user_order_num = 0;
		if($user_ids) {
			$sqlmap['user_id'] = array("IN", $user_ids);
			$user_order_num = $this->order_db->where($sqlmap)->count();
		}
		$time_sell['user_cust_transaction'] = sprintf('%.2f', ($user_order_num / $time_sell['total_order']) * 100);
		
		$sell_list = $series = $series_real_amount = $series_order_effect = array();
		for ($i=0; $i < $days; $i++) { 
			$_time = NOW_TIME - 86400 * $i;
			$sell_list[mdate($_time, 'Y-m-d')] = $this->public_daysell(mdate($_time, 'Ymd'));
			$series_real_amount[] = intval($sell_list[mdate($_time, 'Y-m-d')]['real_amount']);
			$series_order_effect[] = intval($sell_list[mdate($_time, 'Y-m-d')]['order_effect']);
		}
		$series[] = array('name' => '销售额', 'data' => array_reverse($series_real_amount), 'tooltip' => array('valueSuffix' => ' 元'));
		$series[] = array('name' => '订单数', 'data' => array_reverse($series_order_effect), 'tooltip' => array('valueSuffix' => ' 笔'));
		$sell_list = array_reverse($sell_list, TRUE);
		$sell_days = array_keys($sell_list);
		/* 引入后台模板 */
		include $this->admin_tpl('sell');
	}

	/* 商品数据分析 */
	public function goods() {
		$days = (isset($_GET['days'])) ? (int) $_GET['days'] : 1;
		$sqlmap = array();
		$sqlmap['order_status'] = array("LT", 3);
		switch ($days) {
			/* 今天 */
			case '1':
				$sqlmap['_string'] = "DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
				break;
			/* 最近7天 */
			case '7':
				$sqlmap['create_time'] = array("GT", NOW_TIME - 96400 * 7);
				break;
			/* 最近30天 */
			case '30':
				$sqlmap['create_time'] = array("GT", NOW_TIME - 96400 * 30);
				break;
			/* 三个月 */
			case '90':
				$sqlmap['create_time'] = array("GT", NOW_TIME - 96400 * 90);
				break;
			/* 一年 */
			case '365':
				$sqlmap['create_time'] = array("GT", NOW_TIME - 96400 * 365);
				break;
			/* 自定义时间段 */
			case '-2':
				$start_time = strtotime($_GET['start_time'].' 00:00:00');
				$end_time = strtotime($_GET['end_time'].' 23:59:59');
				$create_time = array();
				$create_time[] = array("GT", $start_time);
				$create_time[] = array("LT", $end_time);
				$create_time[] = 'AND';
				$sqlmap['create_time'] = $create_time;
				break;
			/* 昨天 */
			default:
				$create_time = NOW_TIME - 86400;
				$sqlmap['_string'] = "TO_DAYS(NOW()) - TO_DAYS(FROM_UNIXTIME(`create_time`)) = 1";
				break;
		}
		$order_ids = $this->order_db->where($sqlmap)->getField('id', TRUE);
		$lists = array();
		$count_price = 0;
		if($order_ids) {
			$sqlmap = array();
			$sqlmap['order_id'] = array("IN", $order_ids);
			$count = model('order_goods')->where($sqlmap)->group('goods_id')->count();
			$lists = model('order_goods')->where($sqlmap)->page(PAGE, 15)->order("dateline DESC")->field("*, sum(shop_number) AS total_shop_number")->group('goods_id')->select();			
			foreach ($lists as $key => $value) {				
				$lists[$key]['total_shop_price'] = sprintf('%.2f', $value['shop_price'] * $value['total_shop_number']);
				$lists[$key]['hits'] = model('goods')->getFieldById($value['goods_id'], 'hits');
				$count_price += $lists[$key]['total_shop_price'];
			}
			$pages = pages($goods_count, 15);
		}
		include $this->admin_tpl('goods');
	}

	/* 会员数据分析 */
	public function user() {
		// ----------- 会员统计 -----------
		/* 会员总数 */
		$user_count['total'] = (int) $this->user_db->count();
		/* 今日新增 */
		$map_today = array();
		$map_today['_string'] = $map_today['_string'] = "DATE_FORMAT(FROM_UNIXTIME(reg_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
		$user_count['today'] = (int) $this->user_db->where($map_today)->count();
		/* 本周新增 */
		$map_week = array();
		$map_week['_string'] = "YEARWEEK(FROM_UNIXTIME(reg_time)) = YEARWEEK(NOW())";
		$user_count['week'] = (int) $this->user_db->where($map_week)->count();
		/* 本月新增 */
		$map_month = array();
		$map_month['_string'] = "DATE_FORMAT(FROM_UNIXTIME(reg_time),'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')";
		$user_count['month'] = (int) $this->user_db->where($map_month)->count();
		/* 新增涨幅 */
		$map_today['reg_time'] = array("ELT", NOW_TIME);
		$today_num = (int) $this->user_db->where($map_today)->count();
		
		$map_today['reg_time'] = array("ELT", NOW_TIME - 86400);
		$map_today['_string'] = "DATEDIFF(NOW() , FROM_UNIXTIME(`reg_time`)) = 1";
		$yesterday_num = (int) $this->user_db->where($map_today)->count();
		$user_gain = sprintf('%.2f', ($today_num - $yesterday_num) / $yesterday_num * 100);
		// ----------- 新增会员概况 -----------
		$days = (isset($_GET['days'])) ? (int) $_GET['days'] : 7;
		$sqlmap = array();
		switch ($days) {
			case '7':
				$sqlmap['reg_time'] = array("GT", NOW_TIME - 86400 * 7);
				$start_time = NOW_TIME - 86400 * 6;
				$end_time = NOW_TIME;
				break;
			case '30':
				$sqlmap['reg_time'] = array("GT", NOW_TIME - 86400 * 30);
				$start_time = NOW_TIME - 86400 * 29;
				$end_time = NOW_TIME;
				break;
			default:
				$create_time = array();				
				$start_time = strtotime($_GET['start_time'].' 00:00:00');
				$end_time = strtotime($_GET['end_time'].' 23:59:59');
				$create_time[] = array("GT", $start_time);
				$create_time[] = array("LT", $end_time);
				$create_time[] = 'AND';
				$sqlmap['reg_time'] = $create_time;
				$days = ceil(($end_time - $start_time) / 86400);
				break;
		}
		$days_user_count = (int) $this->user_db->where($sqlmap)->count();
		for ($i=0; $i < $days; $i++) { 
			$_time = $end_time - 86400 * $i;
			$_day = mdate($_time, 'Y-m-d');
			$sqlmap['_string'] = "DATE_FORMAT(FROM_UNIXTIME(reg_time),'%Y-%m-%d') = '".$_day."'";
			$user_list[$_day] = (int) $this->user_db->where($sqlmap)->count();
		}
		$user_list = array_reverse($user_list, TRUE);
		$sell_days = array_keys($user_list);
		$series[] = array('name' => '新注册', 'data' => array_values($user_list), 'tooltip' => array('valueSuffix' => ' 位'));
		include $this->admin_tpl('user');
	}

	/* 会员销售排行 */
	public function public_userlist() {
		$days = (isset($_GET['days'])) ? (int) $_GET['days'] : 7;
		switch ($days) {
			case '7':
				$sqlmap['u.reg_time'] = array("GT", NOW_TIME - 86400 * 7);
				$start_time = NOW_TIME - 86400 * 6;
				$end_time = NOW_TIME;
				break;
			case '30':
				$sqlmap['u.reg_time'] = array("GT", NOW_TIME - 86400 * 30);
				$start_time = NOW_TIME - 86400 * 29;
				$end_time = NOW_TIME;
				break;
			default:
				$create_time = array();				
				$start_time = strtotime($_GET['start_time'].' 00:00:00');
				$end_time = strtotime($_GET['end_time'].' 23:59:59');
				$create_time[] = array("GT", $start_time);
				$create_time[] = array("LT", $end_time);
				$create_time[] = 'AND';
				$sqlmap['u.reg_time'] = $create_time;
				$days = ceil(($end_time - $start_time) / 86400);
				break;
		}
		$sqlmap['o.order_status'] =  array("LT", 3);		
		$_lists = model('order')->alias('o')->join("__USER__ AS u ON o.user_id = u.id")->where($sqlmap)->field('u.username, o.user_id, SUM(o.real_amount) AS order_price, COUNT(o.id) AS order_total')->group('o.user_id')->order("order_price DESC")->select();
		$lists = array();
		if($_lists) {
			$map_canpel = array();
			$map_canpel['order_status'] = array("GT", 2);
			$rank = (PAGE - 1) * PAGE_SIZE + 1;
			$_s = (PAGE - 1) * PAGE_SIZE;
			$_e = (PAGE - 1) * PAGE_SIZE + PAGE_SIZE;
			foreach ($_lists as $k => $v) {
				if($k >= $_s && $k < $_e) {
					$map_canpel['user_id'] = $v['user_id'];
					$v['order_canpel'] = $this->order_db->where($map_canpel)->count();
					$v['rank'] = $rank;
					$lists[$k] = $v;
					$rank++;					
				}
			}
		}
		$count = count($_lists);
		$pages = pages($count, PAGE_SIZE);
		echo json_encode(array('count' => $count, 'lists' => $lists, 'pages' => $pages));
	}

	public function public_dayselllist() {
		$days = (isset($_GET['days'])) ? (int) $_GET['days'] : 7;
		$sqlmap = array();
		$sqlmap['order_status'] = array("LT", 3);		
		switch ($days) {
			case '7':
				$sqlmap['create_time'] = array("GT", NOW_TIME - 86400 * 7);
				break;
			case '30':
				$sqlmap['create_time'] = array("GT", NOW_TIME - 86400 * 30);
				break;
			default:
				$create_time = array();
				$start_time = strtotime($_GET['start_time'].' 00:00:00');
				$end_time = strtotime($_GET['end_time'].' 23:59:59');
				$create_time[] = array("GT", $start_time);
				$create_time[] = array("LT", $end_time);
				$create_time[] = 'AND';
				$sqlmap['create_time'] = $create_time;
				$days = ceil(($end_time - $start_time) / 86400);
				break;
		}
		$si = (PAGE - 1) * PAGE_SIZE;
		$ei = $si + PAGE_SIZE;
		if($ei > $days) $ei = $days;

		for ($i=$si; $i < $ei; $i++) { 
			$_time = NOW_TIME - 86400 * $i;		
			$sell_list[mdate($_time, 'Y-m-d')] = $this->public_daysell(mdate($_time, 'Ymd'));
			$series_real_amount[] = intval($sell_list[mdate($_time, 'Y-m-d')]['real_amount']);
			$series_order_effect[] = intval($sell_list[mdate($_time, 'Y-m-d')]['order_effect']);
		}
		$pages = pages($days, PAGE_SIZE);
		echo json_encode(array('lists' => $sell_list, 'pages' => $pages));			
	}
	
	/* 获取指定日期的销售情况（$time 格式为：20141231） */
	private function public_daysell($time) {
		$sqlmap = array();
		$sqlmap['order_status'] = array("LT", 3);
		$sqlmap['_string'] = "DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y%m%d') = '".$time."'";
		$sell_day = $this->order_db->field("sum(real_amount) AS real_amount, COUNT(id) AS order_effect")->where($sqlmap)->find();
		$sell_day['real_amount'] = sprintf('%.2f', $sell_day['real_amount']);
		unset($sqlmap['order_status']);
		$sell_day['order_total'] = $this->order_db->where($sqlmap)->count();
		$sell_day['user_cust_transaction'] = sprintf('%.2f', ($sell_day['real_amount'] / $sell_day['order_effect']));
		return $sell_day;
	}
}