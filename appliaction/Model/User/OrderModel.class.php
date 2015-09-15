<?php
/**
 * 
 * 订单关联模型 相关数据处理
 * @author wj
 *
 */
class OrderModel extends RelationModel{
	
	protected $_link = array(
			//订单详情
            'OrderGoods'=>array(
            'mapping_type'   =>HAS_MANY,
            'class_name'    =>'Order_goods',
			'foreign_key' =>'order_id',
            'mapping_name' =>'orderInfo',  
             ),
             
            'comment'=>array(
            'mapping_type'   =>HAS_MANY,
            'class_name'    =>'Comment',
			'foreign_key' =>'order_id',
            'mapping_name' =>'comment',
            //'condition'=>'',  
             ),
             
            'payment'=>array(
             	'mapping_type'   =>BELONGS_TO,
	            'class_name'    =>'Payment',
				'foreign_key' =>'pay_code',
	            'mapping_name' =>'paymentInfo',  
             ),
             'coupons'=>array(
             	'mapping_type'   =>BELONGS_TO,
	            'class_name'    =>'coupons',
				'foreign_key' =>'coupons_id',
	            'mapping_name' =>'couponsInfo',  
             )
         );

	/**
	 * 
	 * 获取订单详情 通过用户id
	 * @param $uid 用户id
	 * @author wj
	 * @date 2014-10-14
	 */
	public function getOrderInfo($uid){
		$map['user_id'] = array('eq',$uid);
		$list = $this->relation(true)->where($map)->select();
		return $list;
	}
	
	/**
	 * 
	 * 获取订单信息 通过条件
	 * @param  $map 条件
	 * @author wj
	 * @date 2014-10-20
	 */
	public function getOrder($map,$limit=''){
		$list =  $this->relation(true)->where($map)->limit($limit)->order('create_time DESC')->select();
		unset($map);
		foreach ($list as $k=>$v){
			$status = $this->getOrderStatusByid($v['id'],$v['user_id']);//获取订单状态
			$list[$k]['in'] = $status;
			foreach($v['orderInfo'] as $k1=>$v1){
				$map['id'] = $v1['goods_id'];
				$gList = M('Goods')->field('thumb,list_img')->where($map)->find();//查找小图
				$list[$k]['orderInfo'][$k1]['img'] = $gList['thumb'];
				$list[$k]['orderInfo'][$k1]['list_img'] = $gList['list_img'];
			}
			
		}
		return $list;
	}
	
	/**
	 * 
	 * 通过订单id获取该订单对应的商品信息
	 * @param  $orderId 订单id
	 * @param  $uid 用户id
	 * @author wj
	 * @date 2014-10-16
	 */
	public function getOrderInfroByOid($orderId,$uid){
		$map['id'] = array('eq',$orderId);
		$map['user_id'] = array('eq',$uid);
		$list = $this->relation(true)->where($map)->find();
		unset($map);
		foreach ($list['orderInfo'] as $k=>$v){
			$map['id'] = $v['goods_id'];
			$gList = M('Goods')->field('thumb,list_img')->where($map)->find();//查找小图
			$list['orderInfo'][$k]['img'] = $gList['thumb'];
			$list['orderInfo'][$k]['list_img'] = $gList['list_img'];
		}
		return $list;
	}

	public function getOrderBysn($sn,$uid){
		$map['order_sn'] = array('eq',$sn);
		$map['user_id'] = array('eq',$uid);
		$list = $this->relation(true)->where($map)->find();
		unset($map);
		foreach ($list['orderInfo'] as $k=>$v){
			$map['id'] = $v['goods_id'];
			$gList = M('Goods')->field('thumb,list_img')->where($map)->find();//查找小图
			$list['orderInfo'][$k]['img'] = $gList['thumb'];
			$list['orderInfo'][$k]['list_img'] = $gList['list_img'];
		}
		return $list;
	}
	
	/**
	 * 
	 * 判断订单的状态
	 * @param  $orderId 订单id
	 * @param  $uid 用户id
	 * @author wj
	 * @date 2014-10-17
	 */
	public function getOrderStatusByid($orderId,$uid){
		$map['id'] = array('eq',$orderId);
		$map['user_id'] = array('eq',$uid);
		$list = $this->where($map)->find();
		if ($list['pay_status']==0 && $list['order_status'] ==0 && $list['delivery_status'] ==0){
			$status['id'] = '0';//订单未付款
			$status['text'] = '待付款';
			return $status;
			
		}
		if ($list['pay_status']==2){
			$status['id'] = '5';//
			$status['text'] = '已关闭';
			return $status;
			
		}

		if ($list['order_status'] == 0 && $list['pay_status'] == 1 && $list['delivery_status'] ==0){
			$status['id'] = '1';//订单未确定
			$status['text'] = '未确认';
			return $status;
			
		}elseif($list['order_status'] == 4){
			$status['id'] = '6';//订单已取消
			$status['text'] = '已取消';
			return $status;
		}elseif ($list['order_status'] == 2 && $list['pay_status'] == 1 && $list['delivery_status'] ==1){
			$status['id'] = '4';//订单已完成
			$status['text'] = '已完成';
			return $status;
			
		}elseif ($list['order_status'] == 1  && $list['pay_status'] == 1){
			if ($list['delivery_status']==0){
				$status['id'] = '2';//订单未发货
				$status['text'] = '未发货';
				return $status;
				
			}
			if ($list['delivery_status']==1){
				$status['id'] = '3';//订单已发货
				$status['text'] = '已发货';
				return $status;
				
			}
		}
	}

	/**
	 * 
	 * 获取商品评论信息
	 * @param  $uid 用户id
	 * @param  $type 1 未评论 2 已评论
	 * @author wj
	 * @date 2014-10-14
	 */
	public function getComment($uid,$type=0){
		$map = "o.user_id = '" . $uid . "'";
        if ($type == 1) {
            $map .= " and og.goods_id not in (select goods_id from cz_comment where user_id = '" . $uid . "' )";
        } else if ($type == 2) {
            $map .= " and og.goods_id in (select goods_id from cz_comment where user_id = '" . $uid . "' )";
        }
        $Model = new Model();
        $count = $this->getCommentNum($uid,$type);
        libfile('Page');
        $Page = new Page($count,5);
        $Page->setConfig('theme', '%upPage% %linkPage%  %downPage%');
   		$Page->setConfig('prev', "<span class='page_prev'></span>");
        $Page->setConfig('next', "<span class='page_next'></span>");
        $Page->listRows = 5;
        $show = $Page->show();
        $list = $Model->query("select og.name,og.order_id,og.goods_id,og.shop_price,og.thumb,o.create_time from cz_order o,cz_order_goods og where o.id = og.order_id and " . $map . " and o.order_status=2 limit " . $Page->firstRow . "," . $Page->listRows);
      
		unset($map);
        foreach ($list as $key => $t) {
            $lists = $Model->query("select c.id,c.contents from cz_comment c where c.goods_id = " . $t["goods_id"] . " and c.user_id = '" . $uid . "'");
			$map['id'] = $t['goods_id'];
			$gList = M('Goods')->field('thumb,list_img')->where($map)->find();//查找小图
			$list[$key]['img'] = $gList['thumb'];
			$list[$key]['list_img'] = $gList['list_img'];
            if ($lists[0]['contents']) {
                $list[$key]["ctitle"] = "2";
                $list[$key]["contents"] = $lists[0]['contents'];
            } else {
                $list[$key]["ctitle"] = "1";
                $list[$key]["contents"] = "";
            }
        }
        $list1['list'] = $list;
        $list1['page'] = $show;
        return $list1;
	}
	/**
	 * 
	 * 获取商品评论信息条数
	 * @param  $uid 用户id
	 * @param  $type 1 未评论 2 已评论
	 * @author wj
	 * @date 2014-10-14
	 */
	public function getCommentNum($uid, $type=0){
		$map = "o.user_id = '" . $uid . "'";
        $Model = new Model();
        if ($type == 1) {
            $sql = "select count(*) as count from cz_order o,cz_order_goods og where o.id = og.order_id and " . $map . " and og.goods_id not in (select goods_id from cz_comment where user_id = '" . $uid . "' ) and o.order_status= 2";
        } else if ($type == 2) {
            $sql = "select count(*) as count from cz_order o,cz_order_goods og where o.id = og.order_id and " . $map . " and og.goods_id in (select goods_id from cz_comment where user_id = '" . $uid . "' ) and o.order_status= 1";
        } else {
            $sql = "select count(*) as count from cz_order o,cz_order_goods og where o.id = og.order_id and " . $map . " and o.order_status= 2";
        }
        $list = $Model->query($sql);
        $count = $list[0]['count'];
        return $count;
	}
	
	/**
	 * 
	 * 获取未支付订单的条数
	 * @param unknown_type $uid
	 */
	public function getNOpayNum($uid){
		$map['user_id'] = array('EQ',$uid);
		$map['pay_status'] = array('EQ',0);
		$map['order_status'] = array('NEQ',4);
		return $this->getCount($map);
	}

/**
	 * 
	 * 获取代发货订单的条数
	 * @param unknown_type $uid
	 */
	public function getShop($uid){
		$map['user_id'] = array('eq',$uid);
		$map['order_status'] = array('eq',1);
		$map['delivery_status'] = array('eq',0);
		return $this->getCount($map);
	}

	
/**
	 * 
	 * 获取未确认收货订单的条数
	 * @param unknown_type $uid
	 */
	public function getNOgoodsNum($uid){
		$map['user_id'] = array('eq',$uid);
	   	$map['order_status'] = array('eq',1);
	   	$map['pay_status'] = array('eq',1);
	   	$map['delivery_status'] = array('eq',1);
		return $this->getCount($map);
	}
	/**
	 * 
	 * 获取待返货订单的条数
	 * @param unknown_type $uid
	 */
	public function getDeliveryNum($uid){
		$map['user_id'] = array('eq',$uid);
	   	$map['order_status'] = array('eq',1);
	   	$map['pay_status'] = array('eq',1);
	   	$map['delivery_status'] = array('eq',0);
		return $this->getCount($map);
	}
	
	/**
	 * 
	 * 获取订单列表
	 * @param  $map  条件
	 */
	public function getOrderList($map){
		$count = $this->getCount($map);
        libfile('Page');
        $Page = new Page($count,5);
        $Page->setConfig('prev','');
        $Page->setConfig('next','');
        $Page->setConfig('theme','%upPage% %linkPage% %downPage%');
        $Page->listRows = 5;
        $show = $Page->show();
		$list['list'] = D('Order')->relation(true)->where($map)->order('sort,create_time DESC')->limit($Page->firstRow, $Page->listRows)->select();

		unset($map);
		foreach ($list['list'] as $k=>$v){
			$status = $this->getOrderStatusByid($v['id'],$v['user_id']);//获取订单状态
			$list['list'][$k]['in'] = $status;
			foreach($v['orderInfo'] as $k1=>$v1){
				$map['id'] = $v1['goods_id'];
				$gList = M('Goods')->field('thumb,list_img')->where($map)->find();//查找小图

				
				$list['list'][$k]['orderInfo'][$k1]['img'] = $gList['thumb'];
				$list['list'][$k]['orderInfo'][$k1]['list_img']=$gList['list_img'];
				
			}
		}

		$list['page'] = $show;
		return $list;
	}
	
	
	
	/**
	 * 
	 * 获取条数
	 * @param  $map 条件
	 * @author wj
 	 * @date 2014-10-16
	 */
	public function getCount($map){
		return $this->where($map)->count();
	}
	
	/**
	 * 
	 * 获取条数
	 * @param  $map 条件
	 * @param  $type 时间范围判断
	 * @author wj
 	 * @date 2014-10-16
	 */
	public function getOrderStatus($type,$map){
		if ($type == 1) {
            $time = time() - 86400 * 7;
            $map['create_time'] = array('GT',$time);
        } else if ($type == 2) {
            $time = time() - 86400 * 30;
            $map['create_time'] = array('GT',$time);
        } else if ($type == 3) {        
            $time = time() - 86400 * 90;
            $map['create_time'] = array('GT',$time);
        } else if ($type == 4) {         
            $time = time() - 86400 * 180;
            $map['create_time'] = array('GT',$time);
        } else if ($type == 5) {          
            $time = time() - 86400 * 365;
            $map['create_time'] = array('GT',$time);
        }
        return $this->getOrderList($map);
	}
}

?>