<?php
class ProductModelController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('Model');
		//分类列表
		$cate = model('Category');
		$data = $cate -> where('status = 1') -> order('parent_id ASC,sort ASC,id ASC') -> select();
		$this -> tree = $info = getTree($data, 0);
	}

	/**
	 * 模型列表
	 */
	public function lists() {
//		$db = model('Model');
//		$list = $db -> getList();
//		//p($list);
//		include $this -> admin_tpl("product_model_lists");
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
			$data['rows']=$this->db->order($order)->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->select();
			//分类
			foreach ($data['rows']as $k => $v) {
				$spec_ids = M('attribute')->where(array("model_id"=>$v['id'],"_string"=>'!ISNULL(spec_ids)'))->getField('spec_ids');
				$data['rows'][$k]['category'] = M('GoodsCategory')->where(array('id'=>array('in',$v['cat_ids'])))->getField('id,name',TRUE);
				$data['rows'][$k]['spec'] = M('spec')->where(array('id'=>array('in',$spec_ids)))->getField('id,name',TRUE);
				$data['rows'][$k]['attrinfo'] = M('attribute')->where(array("model_id"=>$v['id'],"_string"=>'ISNULL(spec_ids)'))->getField('id,name,value,type',TRUE);
			}
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('product_model_lists');
		}

	}

	/**
	 * 列新模型
	 */
	public function update() {
		$dialog = TRUE;
		$validform = TRUE;
		$opt = I("opt");
		$id = I("id");
		$db = model('Model');
		if (IS_POST) {
			self::save();
		} else {
			//添加
			if ($opt == 'add')
				include $this -> admin_tpl('product_model_update');
			//编辑
			if ($opt == 'edit' && $id > 0) {
				$map['id'] = $id;
				$list = $db -> getList($map);
				$info = $list['list'][0];

				include $this -> admin_tpl('product_model_update');
			}
			//删除
			if ($opt == 'del' && $id) {
				unset($where);
				$where['id'] = array('in', $id);
				$db -> where($where) -> delete();
				unset($where);
				$where['model_id'] = array('in', $id);
				M('attribute') -> where($where) -> delete();
				showmessage('恭喜你，删除成功！', U('lists'), 1);
				exit();
			}
		}
	}

	/**
	 * 保存数据
	 */
	protected function save() {
		$db = model('Model');
		$id = I('id');
		//处理
		if (isset($id) && $id) {
			if ($db -> saveinfo()) {
				showmessage("修改商品类型成功", U('lists'));
			} else {
				$this -> error($db -> getError());
			}
		} else {
			if ($db -> addinfo()) {
				showmessage("添加商品类型成功", U('lists'));
			} else {
				showmessage($db -> getError());
			}
		}
	}
	
	/**
	 * 获取模型
	 */
	public function ajax_get_model(){
		$db = model('Model');
		$info = $db -> getList();
		echo json_encode($info);
	}
	
	/**
	 * 获取模型属性
	 */
	public function ajax_get_model_info(){
		$db = model('Model');
		$info = $db -> getAttrInModel();
		$goods_id = $_GET['goods_id'];
		$selmode = model('GoodsAttribute')->where('goods_id = '.$goods_id.' AND type=1')->select();
		foreach ($info['attrinfo'] as $k => $v) {
			foreach ($selmode as $kk => $vv) {
				if($vv['attribute_id'] == $v['id']){
					$info['attrinfo'][$k]['selvalue'][] = $vv['attribute_value'];
				}
			}
		}
		echo json_encode($info);
	}
}
