<?php
header('Content-Type: text/html; charset=UTF-8');
class GoodsController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize(); 
		//分类列表
		$cate = model('Category');
		$data = $cate->where('status = 1')->order('parent_id ASC,sort ASC,id ASC')->select();
		$this->tree = $info = getTree($data,0);
		$this->treeMenu = $cate->formatCat($info);
		//品牌列表
		$this->brand = model('Brand')->where('status = 1')->order('sort ASC')->select();
		$this->db = model('Goods');
		$this->model = model('Model')->order('sort ASC,id DESC')->select();
		libfile('form');
	}
     /**
	 * 商品列表
	 */
	public function lists(){
		$category_arr = array();
		$label = $_GET['label'];
		$brand_id = is_string($_GET['brand_id'])?$_GET['brand_id']:'0';
		$status_ext = is_string($_GET['status_ext'])?$_GET['status_ext']:'0';
		$category_id = is_string($_GET['category_id'])?$_GET['category_id']:'0';
		$keyword = $_GET['keyword']?$_GET['keyword']:'';
		$region_arr = (array)getcache('region','region');
		$brand_arr = (array)M('Brand')->field('id,name')->order('sort ASC, id ASC')->select();
		$category_arr = (array)M('GoodsCategory')->getField('id,name',TRUE);
		if(IS_POST){
			$sqlmap = array();
			$goods_ids = array();
			$field = "id,name,cat_ids,brand_id,status,status_ext,sort";
			$goods_ids = $this->db->getField('id',true);
			//排序
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['sort'] = 'ASC';
			}
			$order['id'] = 'DESC';
			//筛选
			if (isset($keyword) && $keyword) {
				//名称ID
				$_nameids = (array)$this->db->where(array('name'=>array('LIKE','%'.$keyword.'%')))->getField('id',TRUE);
				//货号 规格ID 
				$gp_where['products_sn|products_barcode'] = array("LIKE", '%'.$keyword.'%');
				$_goodsids = (array)M('GoodsProducts')->where($gp_where)->distinct(TRUE)->getField('goods_id',TRUE);
				//取结果
				$result_ids = array_unique(array_merge($_nameids,$_goodsids));
				$sqlmap = (array('id'=>array('IN',$result_ids)));
				
			}
			if(isset($category_id) && $category_id){
				$cat_ids = (array)D('Category')->getChild($category_id);
				$_join = array("find_in_set('{$category_id}',cat_ids)");
				foreach ($cat_ids as $key => $value) {
					$_join[] = "find_in_set('{$value}',cat_ids)";
				}
				$join_str = implode(' OR ', $_join);
				if(isset($where['_string'])){
					$where['_string'] .= " AND (".$join_str.")";
				}else{
					$where['_string'] = $join_str;
				}
				$_goods_ids = (array)$this->db->where($where)->getField('id',true);
				$result_ids = array_intersect($goods_ids,$_goods_ids);
				$sqlmap = (array('id'=>array('IN',$result_ids)));
			}
			if (isset($brand_id) && $brand_id) {
				$sqlmap['brand_id'] = $brand_id ;
			}
			if (isset($status_ext) && $status_ext) {
				$sqlmap['_string'] = "find_in_set('{$status_ext}',status_ext)";
			}

			switch($label){
				case '1'://下架
					$sqlmap['status'] = 0;
					break;
				case '2'://缺货
					$products_ids = (array)M('goods_products')
					->distinct('goods_id')
					->where(array('goods_number'=>array('EQ','0')))
					->getField('goods_id',true);
					$result_ids = array_intersect($goods_ids,$products_ids);
					$sqlmap = (array('id'=>array('IN',$result_ids)));
					break;
				case '3'://库存预警
					$products_ids = (array)M('goods_products')
					->distinct('goods_id')
					->join('__GOODS__ g on g.id = goods_id')
					->where(array('goods_number'=>array('EXP','<=g.warn_number')))
					->getField('goods_id',true);
					$result_ids = array_intersect($goods_ids,$products_ids);
					$sqlmap = (array('id'=>array('IN',$result_ids)));
					break;
				case '4'://删除
					$sqlmap['status'] = -1;
					break;
				default:
					$sqlmap['status'] = array('NEQ','-1');
					break;
			}
			
			//分页
			$pagenum=isset($_GET['page']) ? intval($_GET['page']) : 1;
			$rowsnum=isset($_GET['rows']) && (int)($_GET['rows']) != 0 ? intval($_GET['rows']) : PAGE_SIZE;
			//计算总数 
			$data['total'] = $this->db->where($sqlmap)->count();	
			$data['rows']=$this->db
			->field($field)
			->where($sqlmap)
			->limit(($pagenum-1)*$rowsnum.','.$rowsnum)
			->order($order)
			->select();
			foreach ($data['rows'] as $key => $value) {
				$goods_info = $this->db->detail($value['id']);
				//库存
				$data['rows'][$key]['goods_number'] = $goods_info['goods_number'];
				//销售价格
				$data['rows'][$key]['shop_price'] = 
					$goods_info['min_shop_price'] == $goods_info['max_shop_price']?
					$goods_info['min_shop_price']:
					$goods_info['min_shop_price'].'~'.$goods_info['max_shop_price'];
				//市场价格
				$data['rows'][$key]['market_price'] = 
					$goods_info['min_market_price'] == $goods_info['max_market_price']?
					$goods_info['min_market_price']:
					$goods_info['min_market_price'].'~'.$goods_info['max_market_price'];
				//分类
				$cat_ids = explode(',', $value['cat_ids']);
				$cat_names = "";
				foreach ($cat_ids as $k => $v) {
					if(!empty($category_arr[$v])){
						$cat_names .= $category_arr[$v].',';
					}
				}
				$data['rows'][$key]['cat_names'] = $cat_names;
			}
			$data['search']['s_barnd_id'] = $brand_id;
			$data['search']['s_status_ext'] = $status_ext;
			$data['search']['s_category_id'] = $category_id;
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('goods_lists');
		}
	}
	 /**
	 * 上架下架
	 */
	 public function ajax_status(){
		$id=intval($_GET['id']);
		if($id>0){
			$data['status']=array('exp',' 1-status ');;
			$this->db->where('id='.$id)->save($data);
			$this->success('恭喜你，成功改变状态！'); 
		}else{
		   $this->error('非法操作，请联系管理员！'); 
		}
	}

	/**
	 * 设置商品扩展状态
	 */
	public function ajax_status_ext(){
		$id = (array)$_GET['id'];
		$val = isset($_GET['val'])?$_GET['val']:0;
		$status = isset($_GET['status'])?$_GET['status']:0;
		$goods = $this->db->where(array('id'=>array('IN',$id)))->getField('id,status_ext');
		foreach ($goods as $key => $value) {
			$status_ext_arr = array_filter(str2arr($value));
			if($status == 1){
				$status_ext_arr[] = $val;
				$status_ext_arr = array_unique($status_ext_arr);
			}else{
				unset($status_ext_arr[array_search($val,$status_ext_arr)]);//按元素值返回键名。去除后保持索引
			}
			$this->db->where(array('id'=>$key))->save(array('status_ext'=>arr2str($status_ext_arr)));		
		}
		showmessage('恭喜你，成功改变状态！',null,1);
	}

	 /**
	 * 改变排序
	 */
	public function ajax_sort(){
		$id=intval($_GET['id']);
		$val=  intval($_GET['val']);
		if($id>0){
			$data['sort']=$val;
			$this->db->where('id='.$id)->save($data);
			$this->success('恭喜你，成功改变排序！'); 
		}else{
			$this->error('非法操作，请联系管理员！'); 
		}
	}
	 /**
	 * 删除商品
	 * 需要同步删除规格及分类数据 
	 */
	 public function ajax_del(){
		$id = (array)$_GET['id'];
		$label = $_GET['label'];
		if($id){
			if($label == 4){
				$this->db->deleteGood($id);
				showmessage('恭喜你，删除商品成功！', '', 1);
			}else{
				$data['id'] = array('IN',$id);
				$data['status'] = '-1';
				$this->db->save($data);
				showmessage('恭喜你，回收商品成功！', '', 1);
			}
			
		}else{
			showmessage('非法操作，请联系管理员！'); 
		}
	}

	/**
	 * 恢复商品
	 */
	public function ajax_recover(){
		$id = $_GET['id'];
		if($id){
			$data['id'] = array('in',$id);
			$data['status'] = '1';
			$this->db->save($data);
			showmessage('恭喜你，恢复商品成功！', '', 1); 
		}else{
			showmessage('非法操作，请联系管理员！'); 
		}
	}

	/**
	 * 添加商品
	 */
	public function add(){
		if(IS_POST){
			$id = $this->db->update();
			if($id){
				showmessage('提交成功',U('lists'),1);
			}else{
				showmessage($this->db->getError(),U('add')); 
			}
		}else{
			$validform = TRUE;
			$dialog = TRUE;
			$posturl = U('add');
			include $this->admin_tpl("goods_add");
		}
	}

	/**
	 * 编辑商品
	 */
	public function edit(){
		if(IS_POST){
			$result = $this->db->update();			
			if($result){
				showmessage('提交成功',U('lists'),1);
			}else{
				showmessage('商品信息编辑失败',U('add'),1); 
			}
		}else{
			$id = $_GET['id'];
			$_list = $this->db->getGoodById($id);
			$product = M('GoodsProducts')->where('goods_id = '.$_list['id'])->select();
			$category = model('Category')->getInfoByIds($_list['cat_ids']);
			$validform = TRUE;
			$dialog = TRUE;
			$posturl = U('edit');
			//反序列化字段中的数组
			foreach ($product as $k => $v) {
				$product[$k]['spec_array'] = unserialize($v['spec_array']);
			}
			include $this->admin_tpl("goods_add");
		}
	} 
	/**
	 * 查找主规格2014-11-09 10:38:33
	 * 用于商品模型
	 */
	public function search_spec_main(){
		$map = array();
		$list = M('Spec')->where($map)->select();
		foreach ($list as $key => $value) {
			$list[$key]['values'] = str2arr($value['value']);
		}
		include $this->admin_tpl('goods_search_spec_main');
	}
	/**
	 * 查找规格
	 */
	public function search_spec(){
		$map = array();
		$list = M('Spec')->where($map)->order('id ASC')->select();
		foreach ($list as $key => $value) {
			if (empty($value['value'])) continue;
			$list[$key]['values'] = str2arr($value['value']);
		}
		include $this->admin_tpl('goods_search_spec');
	}
	/**
	 * 选择规格
	 */
	public function select_spec(){
		$items=M('spec')->where('status=1')->select();
		$this->assign('items',$items);
		$this->display();
	}
	public function search_goods(){
		$param = I('param.');
		$param['prom_type'] = array('EQ','NULL');
		$list = $this->db->getList($param);
		$pages = $this->db->pages;
		include $this->admin_tpl('goods_search');
	}
	/**
	 * 导入商品
	 */
	public function import(){
		libfile('Csv');
		$csv = new csv();
		if(IS_POST){
			$action = $_GET['opt']; 
	
			if ($action == 'import') { //导入CSV 
			   	//导入处理 
			   	$filename = $_FILES['file']['tmp_name']; 
				if (empty ($filename)) { 
					showmessage('请选择要导入的CSV文件！') ; 
					exit; 
				} 
				$handle = fopen($filename, 'r'); 
				$result = $csv->input_csv($handle); //解析csv 
				$len_result = count($result); 
				if($len_result<=1){ 
					showmessage('没有任何数据！'); 
					exit; 
				} 
				$success_num = 0;
				$err_num = 0;
				for ($i = 1; $i < $len_result; $i++) { //循环获取各字段值 
					unset($_POST);
					$_POST['name'] = iconv('gb2312', 'utf-8', $result[$i][0]);
					$_POST['cat_ids'] = array($result[$i][1]);
					$_POST['brand_id'] = $result[$i][2];
					$_POST['_shop_price'] = array($result[$i][3]);
					$_POST['_market_price'] = array($result[$i][4]);
					$_POST['_cost_price'] = array($result[$i][5]);
					$_POST['_goods_number'] = array($result[$i][6]);
					$_POST['thumb'] = $result[$i][7];
					$_POST['keyword'] =	iconv('gb2312', 'utf-8', $result[$i][8]);
					$_POST['brief'] = iconv('gb2312', 'utf-8', $result[$i][9]);
					$_POST['content'] = iconv('gb2312', 'utf-8', $result[$i][10]);
					$_POST['_goods_barcode'] = array('');
					$_POST['_goods_sn'] = array('');
					$_POST['del_products_all'] = 'no';
					$_POST['give_integral'] = '-1';
					$_POST['model'] = '0';
					$_POST['status'] = '1';
					$_POST['warn_number'] =	'2';
					//入库
					
					if($this->db->where(array('name'=>array('EQ',iconv('gb2312', 'utf-8', $result[$i][0]))))->count() == 0){
						$id = $this->db->update();
						$success_num++;
					}else{
						$err_num++;
					}
					
				} 
					showmessage('共有数据'.($len_result-1).' 导入成功 '.$success_num.' 重复 '.$err_num); 

			} elseif ($action=='export') { //导出CSV 
			   	//导出处理 
			} 
		}else{
			include $this->admin_tpl('goods_import');
		}
		
	}

	/**
	* 导出商品
	*/
	public function excel(){
		$category_arr = array();
		$category_arr = (array)M('GoodsCategory')->getField('id,name',TRUE);
		$brand = (array)M('Brand')->getField('id,name',TRUE);
			//导出商品数据
			$field = "id,name,cat_ids,brand_id";
			//计算总数 
			$data['rows']=$this->db->field($field)->where(array('id' => array("IN", $_GET['ids'])))->select();
			foreach ($data['rows'] as $key => $value) {
				$goods_info = $this->db->detail($value['id']);
				//库存
				$data['rows'][$key]['goods_number'] = $goods_info['goods_number'];
				//销售价格
				$data['rows'][$key]['shop_price'] = 
					$goods_info['min_shop_price'] == $goods_info['max_shop_price']?
					$goods_info['min_shop_price']:
					$goods_info['min_shop_price'].'~'.$goods_info['max_shop_price'];
				//市场价格
				$data['rows'][$key]['market_price'] = 
					$goods_info['min_market_price'] == $goods_info['max_market_price']?
					$goods_info['min_market_price']:
					$goods_info['min_market_price'].'~'.$goods_info['max_market_price'];
				//分类
				$cat_ids = explode(',', $value['cat_ids']);
				$cat_names = "";
				
				foreach ($cat_ids as $k => $v) {
					if(!empty($category_arr[$v])){
						$cat_names .= $category_arr[$v].',';
					}
				}
				$data['rows'][$key]['cat_names'] = $cat_names;
				//品牌
				$data['rows'][$key]['brand_id'] = $brand[$value['brand_id']];
			}
			$header = array(
			  'id' =>'商品id',
			  'name'=>'商品名称',
			  'cat_ids'=>'商品分类id',
			  'brand_id'=>'商品品牌',
			  'goods_number'=>'库存',
			  'market_price'=>'市场价',
			  'shop_price'=>'销售价',
			  'cat_names'=>'商品分类'
			);
          $this->DownloadCSV($data['rows'],$header);			
	}
	/*
	* 通用方法
	* 导出大数据为CSV 
	* 参数依次传入 查询对象,CSV文件列头(键是数据表中的列名,值是csv的列名),文件名.对数据二次处理的函数;
	*/
	private function DownloadCSV($selectObject,$head){
		$fileName=time();
		set_time_limit(0);
		//下载头.
		header ('Content-Type: application/vnd.ms-excel;charset=gbk');
		header ('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
		header ('Cache-Control: max-age=0');
		//输出流;
		$file = 'php://output';
		$fp = fopen ( $file, 'a' );
		function changCode( $changArr ) {
			// 破Excel2003中文只支持GBK编码;
			foreach ( $changArr as $k => $v ) {
				$changArr [$k] = iconv ( 'utf-8', 'gbk', $v );
			}
			//返回一个 索引数组;
			return array_values( $changArr );
		};
		//写入头部;
		fputcsv ( $fp, changCode( $head ) );
		//写入数据;
		foreach($selectObject as $key => $value){
			fputcsv ( $fp, changCode($value) );	
			flush();
		}
		exit();
	}

	/* 添加或编辑商品时：新增规格 */
	public function goods_add_spec() {
		if (empty($_POST['name'])) $this->error('请填写要添加的规格名称');
		$result = model('Spec')->add(array('name'=>$_POST['name']));
		if (!$result) $this->error('新规格添加失败');
		$this->success('添加成功');
	}

	/* 添加或编辑商品时：新增属性 */
	public function goods_add_value() {
		$data       = array();
		$data['id'] = (int)$_POST['spec_id'];
		$new_value  = str_replace('，',',',trim($_POST['new_value']));
		if (empty($new_value)) $this->error('请填写要添加的属性值');
		if ($data['id'] < 1) $this->error('该规格不存在或规格ID有误');
		$old_info = model('Spec')->find($data['id']);
		if (empty($old_info['value'])) {
			$data['value'] = $new_value;
		} else {
			$data['value'] = $old_info['value'].','.$new_value;
		}
		$result = model('Spec')->save($data);
		if (!$result) $this->error('新增属性失败');
		$this->success('新增属性成功');
	}
}