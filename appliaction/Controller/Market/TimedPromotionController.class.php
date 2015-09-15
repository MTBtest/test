<?php
class TimedPromotionController extends AdminBaseController{
	public function _initialize() {
		parent::_initialize();
		$this->db = model('TimedPromotion');
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
			include $this->admin_tpl('timed_promotion_lists');
		}
	}
	
	/*
	 * 添加/编辑
	 */
	public function edit(){
		$validform = TRUE;
		$dialog = TRUE;
		$id = $_GET['id'];
		$map = array();
		if(IS_POST){
			$data = $_GET;
			unset($data['timed_prices']);
			unset($data['goods_id']);
			$data['start_time'] = strtotime($_POST['start_time']);
			$data['end_time'] = strtotime($_POST['end_time']);
			// 对商品价格设置并存为json
			$ids = $_POST['goods_id'];
			$prices = $_POST['timed_prices'];
			if (is_array($ids)) {
				foreach ($ids as $k => $v) {
					if ($prices[$k] == 0) {
						showmessage('商品价格必须填写且大于0！');
					}
					$goods_config[$v] = sprintf('%.2f',$prices[$k]);
				}
			}
			$data['goods_config'] = json_encode($goods_config);
			$rs = $this->db->update($data);
			if (!$rs && !$id) {
				showmessage('添加促销失败，请重试！');
			}
			$info = array();
			$info['prom_id'] = $id ? $id : $rs ;
			$info['prom_type'] = 'timed';
			model('goods')->where(array('prom_id'=>array('EQ',$info['prom_id'])))->save(array('prom_id'=>0));
			model('goods')->where(array('id'=>array('IN',$ids)))->save($info);
			if($id){
				showmessage('编辑完成',U('lists'));
			}else{
				showmessage('添加完成',U('lists'));
			}
		}else{
			if(isset($id)){
				$map['id'] = $id;
				$info = $this->db->where($map)->find();
				$goods_config = json_decode($info['goods_config'],TRUE);
				$goods_list = model('goods')->where(array('prom_id'=>array('EQ',$info['id'])))->order('id DESC')->select();
			}
			include $this->admin_tpl('timed_promotion_edit');
		}
	}

	/*删除*/
	public function ajax_del(){
		$id = (array) $_GET['id'];
		if(empty($id)) showmessage('参数错误');
		$_goods_ids = array();
		$_goods_ids =$this->db->where(array('id'=>array('IN',$id)))->field('goods_config')->select();
		
		$goods_ids = array();
		foreach ($_goods_ids as $v) {
			$goods_ids = array_merge($goods_ids,array_keys(json_decode($v['goods_config'],TRUE)));
		}
		if($goods_ids){
			model('goods')->where(array('id'=>array('IN',$goods_ids)))->save(array('prom_id'=>0,'prom_type'=>''));
		}
		$this->db->where(array('id'=>array('IN',$id)))->delete();
		showmessage('数据删除成功', U('lists'), 1);
	}
}