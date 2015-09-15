<?php

class GoodsCouponsController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();
		$this->db = model('Coupons');
	}
	/**
	 * 优惠券列表
	 */
	public function lists() {
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
			//统计数量
			foreach ($data['rows'] as $key => $value) {
				$data['rows'][$key]['nums'] = getCouponsCount($value['id']);
			}
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('goods_coupons_lists');
		}
	}
	/**
	 * 添加修改页
	 */
	public function update() {
		$validform = TRUE;
		$opt = I("opt");
		$id = I("id", 0);
		if (IS_POST) {
			self::save();
		} else {
			if (isset($opt) && $opt) {
				//删除
				if ($opt == 'del' && $id) {
					unset($where);
					$where['id'] = array('in', $id);
					$this->db->where($where)->delete();
					showmessage('恭喜你，删除成功！', U('GoodsCoupons/lists'),1);
					exit();
				}
				//添加
				if ($opt == 'add')
					include $this->admin_tpl('goods_coupons_update');
				//编辑
				if ($opt == 'edit' && $id > 0) {
					$info = $this->db->where('id=' . $id)->find();
					$this->info = $info;
					include $this->admin_tpl('goods_coupons_update');
				}
				
			} else {
				showmessage('参数错误,请联系管理员!');
			}
		}
	}

	/**
	 * 处理数据
	 */
	public function save() {
		$id = I('id');
		$_POST['start_time'] = strtotime(I('start_time'));
		$_POST['end_time'] = strtotime(I('end_time'));
		if ($_POST['start_time'] > $_POST['end_time'])showmessage("开始日期不能大于结束日期");
		//处理
		if (isset($id) && $id) {
			if ($this->db->create()) {
				$this->db->save();
				$nid = $id;
				showmessage("处理优惠券成功", U('GoodsCoupons/lists'));
			} else {
				$this->error($db->getError());
			}
		} else {
			if ($this->db->create()) {
				$nid = $this->db->add();
				showmessage("添加优惠券成功", U('GoodsCoupons/lists'));
			} else {
				$this->error($db->getError());
			}
		}
	}

}
