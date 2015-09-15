<?php

class SiteDeliveryController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();
		$this->db = D('Delivery');
		$this->delivery_region_db = D('DeliveryRegion');
		
	}
	
	/**
	 * 配送方式列表
	 */
	public function lists(){
		if(IS_POST){
			$sqlmap = array();
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['sort'] = 'ASC';
				$order['id'] = 'DESC';
			}
			$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
			$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
			$data['total'] = $this->db->count();	//计算总数 
			$data['rows'] = $this->db->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			if (!$data['rows']) $data['rows']=array();
			for($i=0;$i<count($data['rows']);$i++){
              $data['rows'][$i][type] = unserialize($data['rows'][$i][type]);
            }
			echo json_encode($data);
		}else{
			include $this->admin_tpl('site_delivery_lists');
		}
	}
	/**
	 * 添加配送方式
	 */
	public function add(){
		$pays = getcache('payment', 'pay');
		if(IS_POST){
			$_GET['type'] = serialize($_GET['type']);
			$_GET['pays'] = serialize($_GET['pays']);
			$result = $this->db->update($_GET);
			if ($_GET['delivery_region']) {
				$this->public_update_delivery_region($_GET['delivery_region'], $result);
			}
			if(!$result) {
				showmessage('物流配送添加失败');
			} else {
				$this->db->build_cache();
				showmessage('物流配送设置成功', U('lists'), 1);
			}   
		}else{
			$dialog = TRUE;
			include $this->admin_tpl('site_delivery_add');
		}
	} 
	/**
	 * 编辑配送方式
	 */
	public function edit() {
		$pays = getcache('payment', 'pay');
		$id = (int) $_GET['id'];
		if ($id < 1) {
			showmessage('参数错误');
		}
		$info = $this->db->getById($id);
		if(!$info) {
			showmessage('您查看的记录不存在');
		}
		if(IS_POST) {
			if ($_GET['delivery_region']) {
				$this->public_update_delivery_region($_GET['delivery_region'], $id);
			}
			$_GET['type'] = serialize($_GET['type']);
			$_GET['pays'] = serialize($_GET['pays']);
			$this->db->update($_GET);
			$this->db->build_cache();
			showmessage('物流配送设置成功', U('lists'), 1);
		}else {
			$info['type'] = unserialize($info['type']);
			$info['pays'] = unserialize($info['pays']);
			$dialog = TRUE;
			/* 查询地区物流配置 */
			$delivery_region_list = $this->delivery_region_db->where(array('delivery_id' => $id))->select();
			$regions = model('region')->cache(TRUE)->getField('area_id, area_name', TRUE);
			include $this->admin_tpl('site_delivery_edit');
		}
	} 

	/* 更新配送地区 */
	private function public_update_delivery_region($delivery_region, $delivery_id) {
		/* 删除记录 */
		if (empty($delivery_region) || empty($delivery_id)) return FALSE;
		if ($delivery_region['delete']) {
			$del_ids = str2arr($delivery_region['delete']);
			$this->delivery_region_db->where(array('id' => array("IN", $del_ids)))->delete();
		}
		/* 编辑记录 */
		if ($delivery_region['edit']) {
			$edit_array = array();
			foreach ($delivery_region['edit'] as $k => $v) {
				$weight = sprintf("%.2f", $delivery_region['edit'][$k]['weight']);
				$price = sprintf("%.2f", $delivery_region['edit'][$k]['price']);
				$edit_array = array(
					'id' => $k,
					'delivery_id' => $delivery_id,
					'region_id'   => $v['area_id'],
					'weightprice' => $weight.','.$price,
					'status'	  => 1,
					'sort'		=> 100
				);
				$this->delivery_region_db->save($edit_array);
			}
		}
		/* 添加记录 */
		if ($delivery_region['add']) {
			$add_array = array();
			foreach ($delivery_region['add']['area_id'] as $k => $v) {
				$weight = sprintf("%.2f", $delivery_region['add']['weight'][$k]);
				$price = sprintf("%.2f", $delivery_region['add']['price'][$k]);
				$add_array[] = array(
					'delivery_id' => $delivery_id,
					'region_id'   => $v,
					'weightprice' => $weight.','.$price,
					'status'	  => 1,
					'sort'		=> 100
				);
			}
			if ($add_array) {
				$this->delivery_region_db->addAll($add_array);
			}
		}
		return TRUE;
	}
	public function ajax_sort(){
		$data['id'] = $_GET['id'];
		$data['sort'] = $_GET['val'];
		$r = $this->db->update($data);
		showmessage('改变排序完成');
	
	}

	/**
	 * 删除配送方式
	 */
	public function delete(){
		$id = (array)$_GET['id'];
		if(!empty($id)){
			$sqlmap = array();
			$sqlmap['id'] = array('IN',$id);
			$this->db->where($sqlmap)->delete();
			$this->delivery_region_db->where(array('delivery_id', array("IN", $id)))->delete();
			$this->db->build_cache();
			showmessage('删除配送方式成功！',null,1); 
		}else{
			showmessage('非法操作，请联系管理员！'); 
		}
	}
	
	/**
	 * 设置 地区
	 */
	public function region(){
		$db=D('Delivery_region');
		$id=I('id','','intval');
		$validform='';
		$dialog = '';
		$delivery=I('text');
		if(IS_POST){
			//组织数据
			foreach ($_POST['region_id'] as $k=>$v){
				$arr[]=array("delivery_id"=>$_POST['id']
						,"region_id"=>$v
						,"weightprice"=>$_POST['weightprice1'][$k].','.$_POST['weightprice2'][$k]
						,"sort"=>100
						,"status"=>1
						);
			}
			$db->where('delivery_id='.$_POST['id'].'')->delete();
			if($db->addAll($arr)){
				  showmessage('提交成功',U('Site_delivery/lists'));
			}else{
				showmessage($db->getError());
			}

		}else{
			$where['delivery_id']=$id;
			$_list=$db->where($where)->select();
			if($_list){
				foreach ($_list as $k=>$v){
					$_list[$k]['weightprice'] =  str2arr($v['weightprice']);
					$_list[$k]['delivery'] =  $delivery;
					$_regionArr = str2arr($v['region_id']); 
					//取出匹配地区
					unset($_regText);
					foreach($_regionArr as $rk=>$rv){
						foreach($this->region as $tk=>$tv){
							if($tv['area_id']==$rv){
								$_regText[]=$tv['area_name'];
							}
						}
					}
					$_list[$k]['delivery_text'] =  arr2str($_regText);
				}
			}
			$region=D('Region')->select();
			$submit_url = U('SiteDelivery/region');
			include $this->admin_tpl('site_delivery_region');
		}
	}
	//查询匹配快递
	public function find_list() {
		$region_ids =I("region_ids");
		$_list=D('Delivery')->query_delivery($region_ids);
	   echo json_encode($_list);
	}

	public function public_ajax_delivery($level = 0, $area_id = 0) {
		$sqlmap = array();
		if ($level > 0) {
			$sqlmap['parent_id'] = $area_id;
		} else {
			$provinces = M('Zone')->getFieldById($area_id, 'provinces');
			$provinces = explode(',', $provinces);
			$sqlmap['area_id'] = array("IN", $provinces);
		}
		$result = M('Region')->where($sqlmap)->order("`sort` ASC")->select();
		echo json_encode($result);
	}
}
