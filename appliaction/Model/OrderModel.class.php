<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class OrderModel extends SystemModel {
    protected $sqlmap = array();
    protected $deliverys = array();
    protected $stepconfigs = array();
    protected $_validate = array(
    );

    protected $_auto = array(
        array('create_time', NOW_TIME , Model:: MODEL_INSERT, 'string'), 
    );
    
    public function _initialize() {
        parent::_initialize();
        $this->deliverys = getcache('deliverys', 'site');
		$this->stepconfigs = array(
			array('待付款', '待确认', '待发货', '待收货', '已完成'),
			array('待确认', '待发货', '待收货', '已完成'),
		);
    }
	
    /**
     * 获取订单列表
     * @param  array $param 查询参数
     */
    public function lists($param){
    	$db_pre = C('DB_PREFIX');
        $table = "{$db_pre}order AS a";
        $field = "a.*, 
            e.name AS delivery_name,
            f.pay_name AS payment_name";
        $join = "
            LEFT JOIN `{$db_pre}delivery` AS e ON a.delivery_id = e.id
            LEFT JOIN `{$db_pre}payment` AS f ON a.pay_code = f.pay_code";

        $order = "a.sort ASC,a.id Desc";
        //条件筛选
        $keyword = $param['keyword'];
        $type = I('type', -1);
		switch ($type) {
			/* 未处理 */
			case '6':
				$where['a.order_status'] = 0;
				break;
			/* 已发货 */
			case '5':
				$where['a.delivery_status'] = 1;
				break;
			/* 代发货 */
			case '4':
				$where['a.delivery_status'] = 0;
				break;
			/* 已作废 */
			case '3':
				$where['a.order_status'] = 3;
				break;
			case '1':
				$where['a.order_status'] = 2;
				break;
			default:
				
				break;
		} 
        if (isset($param['user_id']) && $param['user_id'] > 0) {
        	$where['a.user_id'] = $param['user_id'];
        }
        if (isset($keyword) && $keyword) {
            $where['_string'] = "a.order_sn LIKE '%{$keyword}%' OR a.accept_name LIKE '%{$keyword}%' OR a.mobile LIKE '%{$keyword}%' ";
            
        }
        //分页
        $count = $this->table($table)->join($join)->field($field)->where($where)->count();

        libfile('Page');
		$pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
        $page = new Page($count, $pagesize);

        $list = $this->table($table)->join($join)->field($field)->where($where)->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
        $result=array(
        	'list'=>$list,
        	'page'=>$page->show(),
            'type'=>$type,
        );
    	return $result;
    }
	
	public function detail($order_sn = '', $field = TRUE, $extra = TRUE) {
		if(empty($order_sn)) {
			$this->error  = '订单号不能为空';
			return false;
		}
		$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
		$rs = $this->field($field)->where($sqlmap)->find();
		if(!$rs) {
			$this->error = '订单号不存在';
			return false;
		}
		if($extra === TRUE) {
			
		}
		return $rs;
	}
    
    /**
     * 查询订单列表
     * @param type $params
     * @data 2015-4-21 14:42
     */
    public function getlists($params) {
        $result = $lists = array();
        $DB = & $this;
        $this->build_sql($params);
        $limit = (isset($params['limit']) && is_numeric($params['limit'])) ? (int) $params['limit'] : 5;   
        if(isset($params['page']) && is_numeric($params['page'])) {
            $result['count'] = $DB->where($this->sqlmap)->count();
            $DB->page($params['page']);
        }        
        $items = $DB->where($this->sqlmap)->limit($limit)->order("`create_time` DESC")->select();
        if(!$items) return array('status' => 'error', 'info' => '没有符合条件的内容');
        $deliverys = getcache('deliverys', 'site');
        foreach($items as $item) {
            $item['_delivery'] = $deliverys[$item['delivery_id']];
            $item['_goods'] = getGoodsInfoByOrderId($item['id']);
            $curr_step = $this->getCurrStatus($item['pay_type'], $item['order_status'], $item['pay_status'], $item['delivery_status']);
            $item['_currstep'] = array('step_id' => $curr_step, 'step_txt' => $this->stepconfigs[$item['pay_type']][$curr_step]);
            $lists[] = $item;
        }
        $result['lists'] = $lists;
        return $result;
    }
    
    /**
     * 生成查询条件
     * @param type $options
     * @data 2015-4-21 14:42
     */
    private function build_sql($options = array()) {
        $sqlmap = array();
        /* 屏蔽已作废的订单 */
        $sqlmap['order_status'] = array("NOT IN", '3');
        /* 是否读取用户数据 */
        if(isset($options['user_id']) && is_numeric($options['user_id'])) {
            if($options['user_id'] != is_login()) {
                return FALSE;
            }
            $sqlmap['user_id'] = $options['user_id'];
        }
        /* 根据类型读取列表 */
		switch ($options['type']) {
			/* 待付款 */
			case 'pay':
				$sqlmap['pay_type'] = 0;
				$sqlmap['pay_status'] = 0;
                $sqlmap['order_status'] = array("NOT IN", '4');
				break;
			/* 待发货：已确定 ** 未发货 */
			case 'delivery':
				$sqlmap['order_status'] = 1;
				$sqlmap['delivery_status'] = 0;
				break;
			/* 待确认收货：先支付后发货 && 已确认 && 已发货 */	
			case 'finish':
				$sqlmap['pay_type'] = 0;
				$sqlmap['order_status'] = 1;
				$sqlmap['delivery_status'] = 1;
				break;
            case 'comment':
                $sqlmap['order_status'] = 2;
                $sqlmap['is_comment'] = 0;
                break;
			default:				
				break;
		}
        $this->sqlmap = $sqlmap;
        return $sqlmap;
    }
    
    /**
     * 获取当前订单状态
     * @param int $pay_type         购物方式
     * @param int $order_status     订单状态
     * @param int $pay_status       支付状态
     * @param int $delivery_status  发货状态
     */
    public function getCurrStatus($pay_type, $order_status, $pay_status, $delivery_status) {
        $pay_type = (int) $pay_type;
		/* 检测订单状态 */		
		$step = 0;
		/* 先支付后发货 */
		if($pay_type == 0) {
			if($order_status == 2) {
				// 已完成
				$step = 4;
			} elseif($order_status == 1 && $delivery_status == 1) {
				// 已发货
				$step = 3;
			} elseif($order_status == 1 && $delivery_status == 0) {
				// 待发货
				$step = 2;
			} elseif($order_status == 0 && $pay_status == 1) {
				// 待确认
				$step = 1;
			}
		} else{
			if($order_status == 2) {
				// 已完成
				$step = 3;
			} elseif($order_status == 1 && $delivery_status == 1) {
				// 已发货
				$step = 2;
			} elseif($order_status == 1) {
				// 待发货
				$step = 1;
			}
		}
        return $step;
    }
}