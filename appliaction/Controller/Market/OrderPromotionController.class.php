<?php
class OrderPromotionController extends AdminBaseController{
	public function _initialize() {
		parent::_initialize();
		$this->db = model('order_promotion');
		$this->parse_type = array(
			'1' =>'满额打折',
			'2' =>'满额优惠金额',
			'3' =>'满额送倍数积分',
			'4' =>'满额送优惠券',
			'5' =>'满额免运费'
		);
	}
	/*
	 * 列表
	 */
	public function lists(){
		if(IS_POST){
			$sqlmap = array();
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'DESC';
			}
			$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
			$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
			$data['total'] = $this->db->count();	//计算总数 
			$data['rows']=$this->db->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('order_promotion_lists');
		}
	}
	
	/*
	 * 添加
	 */
	public function edit(){
		$validform = TRUE;
		$dialog = TRUE;
		$id = $_GET['id'];
		$map = array();
		$parse_type = $this->parse_type;
		//优惠券列表
		$field = "id,name";
		$coupons = model('coupons')->field($field)->order('id DESC')->select();
		foreach ($coupons as $k => $v) {
			$coupons[$k]['num'] = getCouponsCount($v['id'], 0);
		}
		$coupons_list = $coupons;
		if(IS_POST){
			$_POST['start_time'] = strtotime($_POST['start_time']);
			$_POST['end_time'] = strtotime($_POST['end_time']);
			$_POST['goods_id'] = (string)arr2str(array_unique($_POST['goods_id']));
			$r = $this->db->create();
			if($r == FALSE){
				showmessage($this->db->getError());
			}else{
				$rs = $this->db->update();
				$data = array();
				$data['prom_id'] = $id ? $id : $rs ;
				$data['prom_type'] = 'order';
				model('goods')->where(array('prom_id'=>$data['prom_id']))->save(array('prom_id'=>0));
				model('goods')->where(array('id'=>array('IN',$_POST['goods_id'])))->save($data);
				if($id){
					showmessage('修改完成',U('lists'));
				}else{
					showmessage('添加完成',U('lists'));
				}
				
			}
			
		}else{
			if(isset($id)){
				$map['id'] = $id;
				$info = $this->db->where($map)->find();
				$goods_list = model('Goods')->where(array('id'=>array('IN',$info['goods_id'])))->order('FIELD(id, '.$info['goods_id'].')')->select();
			}
			include $this->admin_tpl('order_promotion_edit');
		}
	}
	/*
	 * 删除
	 * */
	public function ajax_del(){
		$id = (array) $_GET['id'];
		$sqlmap = array();
		if(empty($id)) showmessage('参数错误');
		$sqlmap = array();
		$sqlmap['id'] = array("IN", $id);
		$this->db->where($sqlmap)->delete();
		showmessage('数据删除成功', U('lists'), 1);
	}
}
