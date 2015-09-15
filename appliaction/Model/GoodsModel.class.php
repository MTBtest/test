<?php
class GoodsModel extends SystemModel {
	protected $_auto = array();
	//自动验证
	protected $_validate = array(
		array('name','require','名称必须！'),
		array('cat_ids', 'require', '所属分类不能为空'),
	);
	
	public function detail($id, $product_id = 0, $field = TRUE, $extra = TRUE) {
		$id = (int) $id;
		if ($id < 1) {
			$this->error = '参数错误';
			return FALSE;
		}
		$goods_info = $this->field($field)->find($id);
		if (!$goods_info) {
			$this->error = '商品不存在';
			return FALSE;
		}
		if(!$goods_info['thumb']) {
			$list_img = str2arr($goods_info['list_img']);
			$goods_info['thumb'] = current($list_img);
		}else{
			$thumb = str2arr($goods_info['thumb']);
			$goods_info['thumb'] = current($thumb);
		}
		if(!$goods_info['small_pics']){
			$list_img = str2arr($goods_info['list_img']);
			$goods_info['small_pics'] = current($list_img);
		}else{
			$small_pics = str2arr($goods_info['small_pics']);
			$goods_info['small_pics'] = current($small_pics);
		}
		$goods_info['url'] = U('Goods/Index/detail', array('id' => $id));
		//取会员信息
		$user_group = model('user')->getFieldById(''.is_login().'','group_id');
		$user_discount = (int)model('user_group')->getFieldById($user_group,'discount');
		$user_discount = abs($user_discount);
		
		if ($extra === TRUE) {
			if($product_id > 0 || !$goods_info['spec_array']) {
				$sqlmap = array();
				if($product_id > 0) {
					$sqlmap['id'] = $product_id;
				} else {
					$sqlmap['goods_id'] = $id;
				}
				$products_info = M('GoodsProducts')->where($sqlmap)->find();
				if(!$products_info){
					$this->error = '产品不存在';
					return FALSE;
				}
				$products_info['product_id'] = $product_id;
				unset($products_info['id']);
				//计算会员折扣价
				if(is_login()> 0){
					if($user_discount >0 && $user_discount <= 100 ){
						$products_info['shop_price']= sprintf("%.2f",$products_info['shop_price']/100*$user_discount);
					}
				}
				$products_info['shop_price'] = $products_info['min_shop_price'] = $products_info['max_shop_price'] = sprintf("%.2f", $products_info['shop_price']);
				$goods_info = array_merge($goods_info, $products_info);
			} else {
				$products_list = M('GoodsProducts')->where(array('goods_id'=>$id))->select();
				if ($products_list) {
					$products_info = array();
					foreach ($products_list as $v) {
						$v['spec_array'] = unserialize($v['spec_array']);
						//计算会员折扣价
						if(is_login()> 0){
							if($user_discount >0 && $user_discount <= 100 ){
								$v['shop_price']= sprintf("%.2f",$v['shop_price']/100*$user_discount);
							}
						}
						$products_info[$v['id']] = $v;
						
					}
					$goods_info['products_info'] = $products_info;

					$_price = M('GoodsProducts')->field("
					min(shop_price) AS min_shop_price, 
					max(shop_price) AS max_shop_price,
					min(market_price) AS min_market_price, 
					max(market_price) AS max_market_price
					")->where(array('goods_id'=>$id))->find();
					$goods_info['min_shop_price'] = sprintf("%.2f", $_price['min_shop_price']);
					$goods_info['max_shop_price'] = sprintf("%.2f", $_price['max_shop_price']);
					$goods_info['min_market_price'] = sprintf("%.2f", $_price['min_market_price']);
					$goods_info['max_market_price'] = sprintf("%.2f", $_price['max_market_price']);
					//计算会员折扣价
					if(is_login()> 0){
						if($user_discount >0 && $user_discount <= 100 ){
							$_price['min_shop_price']= sprintf("%.2f",$_price['min_shop_price']/100*$user_discount);
						}
					}
					
					$goods_info['shop_price'] = sprintf("%.2f", $_price['min_shop_price']);
					
					//计算库存
					$goods_info['goods_number'] = M('GoodsProducts')->where(array('goods_id'=>$id))->sum('goods_number');
				}
			}
			/* 限时促销时，临时更改显示价格 */
			if ($goods_info['prom_type'] == 'timed' && $goods_info['prom_id'] > 0 ) {
				$pro_map = array();
				$pro_map['id'] = $goods_info['prom_id'];
				$pro_map['status'] = 1;
				$pro_map['start_time'] = array("LT", NOW_TIME);
				$pro_map['end_time'] = array("GT", NOW_TIME);
				$promotion = model('timed_promotion')->where($pro_map)->find();
				if ($promotion) {
					$goods_config = json_decode($promotion['goods_config'],TRUE);
					$goods_info['shop_price'] = sprintf("%.2f", $goods_config[$goods_info['id']]);
				}
			}
		}
		return $goods_info;
	}

	/**
	 * 指定商品/产品减少库存
	 * @param [int] $goods_id 商品ID
	 * @param [int] $product_id 产品ID
	 * @param [int] $goods_num变更数量
	 * @return bool
	 */
	public function setDecNumber($goods_id, $product_id, $goods_number) {
		$goods_id = (int) $goods_id;
		$product_id = (int) $product_id;
		$goods_number = (int) $goods_number;
		if(($goods_id < 1 && $product_id < 1) || $goods_number < 1) {
			$this->error = '参数错误';
			return FALSE;
		}
		$sqlmap = array();
		if ($product_id > 0) {
			$sqlmap['id'] = $product_id;
		} else {
			$sqlmap['goods_id'] = $goods_id;
		}
		M('GoodsProducts')->where($sqlmap)->setDec('goods_number', $goods_number);
		return TRUE;

	}
	
	/**
	 * 指定商品/产品增加库存
	 * @param [int] $goods_id 商品ID
	 * @param [int] $product_id 产品ID
	 * @param [int] $goods_num变更数量
	 * @return bool
	 */
	public function setIncNumber($goods_id, $product_id, $goods_number) {
		$goods_id = (int) $goods_id;
		$product_id = (int) $product_id;
		$goods_number = (int) $goods_number;
		if(($goods_id < 1 && $product_id < 1) || $goods_number < 1) {
			$this->error = '参数错误';
			return FALSE;
		}
		$sqlmap = array();
		if ($product_id > 0) {
			$sqlmap['id'] = $product_id;
			$this->_db = M('GoodsProducts');
		} else {
			$sqlmap['id'] = $goods_id;
			$this->_db = $this;
		}
		$this->_db->where($sqlmap)->setInc('goods_number', $goods_number);
		return TRUE;
	}	
	
	/* ==========后台========== */
	/**
	 * 获取产品列表
	 * @paramarray $map 查询参数
	 * @return array	产品列表
	 */
	public function getList($param){
		$where = array();
		//查询条件
		if(!empty($param['category_id'])){
			$cat_ids = D('Admin/Category')->getChild($param['category_id']);
			$_join = array("find_in_set('".$param['category_id']."',cat_ids)");
			foreach ($cat_ids as $key => $value) {
				$_join[] = "find_in_set('{$value}',cat_ids)";
			}
			$join_str = implode(' OR ', $_join);
			if(isset($where['_string'])){
				$where['_string'] .= " AND (".$join_str.")";
			}else{
				$where['_string'] = $join_str;
			}
		}
		if(!empty($param['brand_id'])){
			$where['brand_id'] = $param['brand_id'];
		}
		if(!empty($param['keyword'])){
			$gp_where['products_sn|products_barcode'] = array("LIKE", '%'.$param['keyword'].'%');
			$ids = M('GoodsProducts')->where($gp_where)->distinct(TRUE)->getField('goods_id',TRUE);
			$_where = array();
			if($ids) {
				$where['id'] = array('in',$ids);
			} else {
				$where['name']= array('like', '%'.$param['keyword'].'%');
			}
		}
		if(!empty($param['status_ext'])){
			if(isset($where['_string'])){
				$where['_string'] .= " AND find_in_set('".$param['status_ext']."',status_ext)";
			}else{
				$where['_string'] = "find_in_set('".$param['status_ext']."',status_ext)";
			}
		}
		switch($param['label']){
			case '1'://下架
				$where['status'] = 0;
				break;
			case '2'://缺货
				$_where = array();
				//$_where['g.goods_number'] = array('EQ','0');
				$_where['gp.goods_number'] = array('EQ','0');
				$_where['_logic'] = 'or';
				$join = "__GOODS_PRODUCTS__ gp on g.id=gp.goods_id";
				//$_where['goods_number'] = array('exp','<= warn_number');
				$where['_complex'] = $_where;
				$distinct = TRUE;
				break;
			case '3'://库存预警
				$_where = array();
				//$_where['g.goods_number'] = array('EXP','<= g.warn_number');
				$_where['g.warn_number'] = array('EXP','>=gp.goods_number');
				//$_where['_logic'] = 'or';
				$join = "__GOODS_PRODUCTS__ gp on g.id=gp.goods_id";
				$where['_complex'] = $_where;
				$distinct = TRUE;
				break;
			case '4'://删除
				$where['status'] = -1;
				break;
			default:
				$where['status'] = array('GT','-1');
				break;
		}
		//促销
		if(isset($param['prom_id'])){
			if(isset($where['_string'])){
				$where['_string'] .= " AND prom_id = '{$param['prom_id']}'";
			}else{
				$where['_string'] = "prom_id = '{$param['prom_id']}'";
			}
		}

		//分页
		//查询分页参数处理
		foreach ($param as $key => $value) {
			$pageMap .= "$key=" . urlencode ($value) . "&";
		}
		$count = $this->where($where)->count();
		libfile('Page');
		$pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');

		$page = new Page($count, $pagesize, $pageMap);
		// print_r($where);die;
		$goods = $this->distinct($distinct)->where($where)->alias('g')->field('g.*')->order('g.id desc')->join($join)->limit($page->firstRow . ',' . $page->listRows)->select();
		$cat_ids = array();
		$brand_ids = array();
		foreach ($goods as $key => $value) {
			$brand_ids[] = $value['brand_id'];
			if(!empty($value['cat_ids'])){
				$cat_ids = array_merge($cat_ids,explode(',', $value['cat_ids']));
			}
		}

		//给$goods数组添加brand_name和categoryext
		$brands = M('Brand')->where(array('id'=>array('in',$brand_ids)))->getField('id,name',TRUE);
		$category = M('GoodsCategory')->where(array('id'=>array('in',$cat_ids)))->getField('id,name',TRUE);
		foreach ($goods as $key => $value) {
			$goods[$key]['brand_name'] = $brands[$value['brand_id']];
			$cat_ids = explode(',', $value['cat_ids']);
			$cat_names = "";
			foreach ($cat_ids as $k => $v) {
				if(!empty($category[$v])){
					$cat_names .= $category[$v].',';
				}
			}
			$goods[$key]['goods_number'] = (int) D('GoodsProducts')->where(array('goods_id' => $value['id']))->sum('goods_number');
			$goods[$key]['categoryext'] = trim($cat_names,',');
			$_price =D('GoodsProducts')->field('min(shop_price) AS min_shop_price,max(shop_price) AS max_shop_price,min(market_price) AS min_market_price,max(market_price) AS max_market_price')->where(array('goods_id' => $value['id']))->find();
			if($_price['min_shop_price'] == $_price['max_shop_price']){
				$goods[$key]['shop_price'] = $_price['min_shop_price'];
			}else{
				$goods[$key]['shop_price'] = $_price['min_shop_price'].'-'.$_price['max_shop_price'];
			}
			if($_price['min_market_price'] == $_price['max_market_price']){
				$goods[$key]['market_price'] = $_price['min_market_price'];
			}else{
				$goods[$key]['market_price'] = $_price['min_market_price'].'-'.$_price['max_market_price'];
			}
		}
		$this->pages = $page->show();
		return $goods;
	}

	/**
	 * 获取单个商品信息
	 */
	public function getGoodById($id){
		$goods = $this->find($id);
		$cat_ids = array();
		$brand_id = $goods['brand_id'];
		if(!empty($goods['cat_ids'])){
			$cat_ids = explode(',', $goods['cat_ids']);
		}
		//给$goods数组添加brand_name和categoryext
		$brands = M('Brand')->where(array('id'=>$brand_id))->getField('id,name',TRUE);
		$category = M('GoodsCategory')->where(array('id'=>array('in',$cat_ids)))->getField('id,name',TRUE);
		$goods['pics']= array_filter(str2arr($goods['list_img']));
		$goods['thumb_pics'] = array_filter(str2arr($goods['thumb']));
		$goods['small_pics'] = array_filter(str2arr($goods['small_pics']));
		$goods['brand_name'] = $brands[$goods['brand_id']];
		$cat_ids = explode(',', $goods['cat_ids']);
		$cat_names = "";
		foreach ($cat_ids as $k => $v) {
			if(!empty($category[$v])){
				$cat_names .= $category[$v].',';
			}
		}
		$goods['category_names'] = trim($cat_names,',');
		$selectedItem = array();
		foreach (unserialize($goods['spec_array']) as $key => $value) {
		$spec_array = str2arr($value['value']);
		foreach ($spec_array as $k => $v) {
			$item = array();
			$item['id'] = $value['id'];
			$item['name'] = $value['name'];
			$item['type'] = $value['type'];
			$item['value'] = $v;
			$selectedItem[] = $item;
			}
		}
		$goods['status_ext'] = explode(',', $goods['status_ext']);
		//扩展状态
		$goods['is_sales'] = in_array('1', $goods['status_ext']);
		$goods['is_hot'] = in_array('2', $goods['status_ext']);
		$goods['is_new'] = in_array('3', $goods['status_ext']);
		$goods['selectedItem'] = json_encode($selectedItem);
		return $goods;
	}
	/**
	 * 分离添加单独处理 @lcl 2014-11-12 11:23:42
	 */
	public function update(){
		$PD = $_POST;
		//商品分类
		$_POST['cat_ids'] = '';
		if($PD['cat_ids']) {
			$_POST['cat_ids'] = arr2str($PD['cat_ids']);
		}
		//详细描述
		$_POST['descript'] = $PD['content']?$PD['content']:'';
		//图册列表
		$_POST['list_img'] = '';
		if($PD['goodsphoto']) {
			$_POST['list_img'] = arr2str($PD['goodsphoto']);
		}
		if($PD['thumb']){
			$_POST['thumb'] = arr2str($PD['thumb']);
		}
		if($PD['small_pics']){
			$_POST['small_pics'] = arr2str($PD['small_pics']);
		}
		//其它属性
		$_POST['status_ext'] = '';
		if(!empty($PD['is_sales'])){
			$_POST['status_ext'][] = 1;
		}
		if(!empty($PD['is_hot'])){
			$_POST['status_ext'][] = 2;
		}
		if(!empty($PD['is_new'])){
			$_POST['status_ext'][] = 3;
		}
		if($_POST['status_ext']) {
			$_POST['status_ext'] = arr2str($_POST['status_ext']);
		}
		$_POST['spec_array'] = '';
		if($_POST['_spec_array']){
			$_POST['spec_array'] = $this->create_goods_spec_array($_POST['_spec_array']);
			$_POST['spec_array'] = serialize($_POST['spec_array']);
		}
		if(!$this->create()){
			$this->error = $this->getError();
			return false;
		}	
		if(isset($_POST['id']) && $_POST['id'] > 0) {
			$this->save();
		} else {
			$_POST['id'] = $this->add();
		}
		$this->create_product_info();
		/* 属性数据组织 */
		if(isset($PD['_model']) || isset($PD['_spec_array'])){
			$this->carete_goods_attr_spec($id);
		}
		/* 更新最小价格和最大价格 */
		$_price = D('GoodsProducts')->field("min(shop_price) AS min_shop_price, max(shop_price) AS max_shop_price")->where(array("goods_id" => $_POST['id']))->find();
		$this->save(array('id' => $_POST['id'], 'min_shop_price' => $_price['min_shop_price'], 'max_shop_price' => $_price['max_shop_price']));
		return $_POST['id'];
	}

	/**
	*删除商品
	**/
	public function deleteGood($id){	
		if(!empty($id)){
			$this->where(array('id' => array("IN", $id)))->delete();
			D('GoodsProducts')->where(array('goods_id' => array('IN', $id)))->delete();
			return true;
		}else{
			return false;
		}
	}	
	/**
	 * 生成GOODS中的spec_array @lcl 2014-11-12 17:03:57
	 */
	private function create_goods_spec_array($data){
		$goodsUpdateData['spec_array'] = $data;
		//是否存在货品
		if(isset($goodsUpdateData['spec_array'])){
			//生成goods中的spec_array字段数据
			$goods_spec_array = array();
			foreach($goodsUpdateData['spec_array'] as $key => $val) {
				foreach($val as $v) {
					$tempSpec = json_decode($v,TRUE);
					if(!isset($goods_spec_array[$tempSpec['id']])) {
						$goods_spec_array[$tempSpec['id']] = array('id' => $tempSpec['id'],'name' => $tempSpec['name'],'value' => array());
					}
					$goods_spec_array[$tempSpec['id']]['value'][] = $tempSpec['value'];
				}
			}
			foreach($goods_spec_array as $key => $val) {
				$val['value'] = array_unique($val['value']);
				$goods_spec_array[$key]['value'] = join(',',$val['value']);
			}
		}
		return $goods_spec_array;
	}

	/**
	 * 处理货品
	 */
	private function create_product_info(){
		$PD = $_POST;
		$products_db = D('GoodsProducts');
		
		//执行删除
		if($_POST['del_products_all'] == 'yes'){
			$products_db->where('goods_id = '.$PD['id'])->delete();
		}
		$del_products_ids = $PD['del_products_ids'];
		if($del_products_ids){
			$map = array();
			$map['id'] = array("IN", $del_products_ids);
			$products_db->where($map)->delete();
		}
		$productIdArray = array();
		//创建货品信息
		foreach($PD['_goods_sn'] as $key => $rs){
			$info = array(
				'id' => $PD['_products_ids'][$key],
				'goods_id' => $PD['id'],
				'products_sn' => $PD['_goods_sn'][$key],
				'products_barcode' => $PD['_goods_barcode'][$key],
				'goods_number' => $PD['_goods_number'][$key],
				'shop_price' => $PD['_shop_price'][$key],
				'market_price' => $PD['_market_price'][$key],
				'cost_price' => $PD['_cost_price'][$key],
				'weight' => $PD['_weight'][$key],
			);
			//格式成数组
			$info['spec_array'] = '';
			if(isset($PD['_spec_array']) && !empty($PD['_spec_array'])){
				$info['spec_array'] = "[".join(',',$PD['_spec_array'][$key])."]";
				$info['spec_array'] = json_decode($info['spec_array'],TRUE);
				$info['spec_array'] = serialize($info['spec_array']);
			}							
			$product = $products_db->create($info);
			if(empty($product['id'])){
				unset($product['id']);
				$products_db->add($product);
			}else{
				$products_db->save($product);
			}
		}
	}
	
	/**
	 * 组织商品模型
	 */
	private function carete_goods_attr_spec(){
		$db = D('GoodsAttribute');
		$PDA = (array)$_POST['_model'];
		$PDS = $_POST['_spec_array'];
		$goods_id = $_POST['id'];		
		$db->where('goods_id='.$goods_id)->delete();
	
		//属性处理
		$adata = array();
		foreach ($PDA as $key => $value) {
		foreach ($value as $k => $v) {
			$item = array();
			$item['goods_id'] = $goods_id;
			$item['attribute_id'] = $key;
			$item['attribute_value'] = $v;
			$item['type'] = 1;
			$adata[] = $item;
			}
		}
		$db->addAll($adata);
			
		//规格处理
		$sdata = array();
		foreach ($PDS as $key => $value) {		
		foreach ($value as $k => $v) {
			$v = json_decode($v,TRUE);
			$item = array();
			$item['goods_id'] = $goods_id;
			$item['attribute_id'] = $v['id'];
			$item['attribute_value'] = $v['value'];
			$item['type'] = 2;
			$sdata[] = $item;
			}
		}
		$sdata=$this->array_unique_fb($sdata);
		$db->addAll($sdata);

		return TRUE;				
	}

	/**
	 * 规格二维数组去重
	 */
	private function array_unique_fb($array2D)	{
		foreach ($array2D as $k=>$v)		{
			$v = join(",",$v);//降维,也可以用implode,将一维数组转换为用逗号连接的字符串
			$temp[$k] = $v;
		}
		$temp = array_unique($temp);	//去掉重复的字符串,也就是重复的一维数组
		foreach ($temp as $k => $v)		{
			$array=explode(",",$v);		//再将拆开的数组重新组装
			$temp2[$k]["goods_id"] =$array[0]; 
			$temp2[$k]["attribute_id"] =$array[1];
			$temp2[$k]["attribute_value"] =$array[2];
			$temp2[$k]["type"] =$array[3];
		}
		return $temp2;
	}

	/**
    * 
    * 获取列表数据[wap]
    * @params  所传参数
    * @author 老孔
    * @date 2015-04-27
    */
	public function get_lists($params) {
		$DB = & $this;
		/* 筛选条件 */
		if (count($params['map']) > 0) {
			unset($params['map']['id']);
			unset($params['map']['page']);
			unset($params['map']['order']);
			unset($params['map']['sort']);
			if (is_array($params['map']['attr'])){
				foreach ($params['map']['attr'] as $k => $v) {
					if ($v == -1) {
						unset($params['map']['attr'][$k]);
					}
				}
			}
		}
		$sqlmap = $DB->buildMap($params);
        $result = $lists = array();
        // 分页大小
        if(isset($params['page'])) {
            $result['total'] = $DB->total($params);
            $DB->page($params['page']);
        }
        // 排序方式
        $order = $params['order'];        
		if ($order) {
			$sort = strtolower($params['sort']);
			if ($order == 'price') {
				$order = 'min_shop_price';
			}
			$sort = ($sort == 'desc' || $sort == 'asc') ? $sort : 'ASC';
			$_order = '`'.$order.'` '.$sort;
		}else{
			$_order = "`id` DESC";
		}
        $limit = (isset($params['limit'])) ? (int) $params['limit'] : 8;
        $items = $DB->where($sqlmap)->order($_order)->limit($limit)->select();
        if(!$items) return array('status' => 'error', 'info' => '没有符合条件的内容');        
        foreach ($items as $key => $item) {
        	$items[$key] = $this->detail($item['id']);
        	if ($items[$key]['list_img']) {
        		$items[$key]['list_img'] = str2arr($items[$key]['list_img']);
        	}
        }
        $result['lists'] = $items;
        return $result;
	}


	/**
     * 生成查询条件 [wap]
     * @param type $params
     * @author 老孔
     * @date 2015-04-27
     */
    private function buildMap($params = array()) {
        $sqlmap = array();
		$sqlmap['status'] = (isset($params['status'])) ? $params['status'] : 1;
        /* 全局搜索 */
    	if ($params['keyword'] != '') {
    		$keyword = $params['map']['keyword'];
    		$sqlmap['name'] = array('LIKE','%'.$keyword.'%');
    		$this->sqlmap = $sqlmap;
        	return $sqlmap;
    	}
        if((int)$params['id']) {
			$cat_ids = D('Admin/Category')->getChild((int)$params['id']);
			if (!$cat_ids) $sqlmap['_string']="find_in_set($params[id],cat_ids)";
			$_dchildids = array((int)$params['id']);
			$cat_ids = array_merge($cat_ids,$_dchildids);
			if (count($cat_ids) > 0) {
				foreach ($cat_ids as $_childid) {
					$join[] = "FIND_IN_SET({$_childid},cat_ids)";
				}
				$sqlmap['_string'] = JOIN(" OR ", $join);
			}
		}
		/* 筛选品牌 */
		if (isset($params['map']['brand_id']) && is_numeric($params['map']['brand_id']) && $params['map']['brand_id'] > 0) {
			$sqlmap['brand_id'] = $params['map']['brand_id'];
		}
		/* 筛选价格 */
		if ($params['map']['price']) {
			list($p_min, $p_max) = str2arr($params['map']['price'], ',');
			$_sqlmap = $_goods_ids = array();
			if($p_min > 0) $_sqlmap['shop_price'][] = array("EGT", $p_min);
			if($p_max > 0) $_sqlmap['shop_price'][] = array("ELT", $p_max);
			$goods_ids = M('GoodsProducts')->where($_sqlmap)->group('goods_id')->getField('goods_id', TRUE);
		}
		/* 自定义的商品类型 fuck!!! */
		if($params['map']['attr']) {
			$_attrs = $params['map']['attr'];
			$_join = array();
			$_count = 0;
			foreach ($_attrs as $k => $attr) {
				list($_type, $_id) = explode("_", $k);
				$_id = (int) $_id;
				if($_id < 1 || empty($attr) || $attr == -1) continue;
				$_count++;
				$type = ($_type == 'att') ? 1 : 2;
				$_join[] = "(`attribute_id` = '".$_id."' AND `attribute_value` = '".$attr."' AND `type` = '".$type."')";
			}
			if (count($_join) > 0) {
				$_sqlmap = array();
				$_sqlmap['_string'] = JOIN(" OR ", $_join);
				$_goods_ids = model('goods_attribute')->where($_sqlmap)->group('goods_id')->having('count(goods_id) >= '.$_count)->getField('goods_id', TRUE);
				if (!$_goods_ids) $_goods_ids = 'nohave';
			}
			if ($goods_ids) {
				if (count($_goods_ids) > 0) {
					$goods_ids = array_intersect($goods_ids, $_goods_ids);
					$goods_ids = ($goods_ids) ? $goods_ids : -1;					
				}
			}else{
				$goods_ids = $_goods_ids;
			}
		}
		if ($goods_ids) {
			$sqlmap['id'] = array("IN", $goods_ids);
		}
        $this->sqlmap = $sqlmap;
        return $sqlmap;
    }

    /**
     * 统计条数 [wap]
     * @param type $params
     * @author 老孔
     * @date 2015-04-27
     */
    public function total($params = array()) {
        if(!$this->sqlmap && count($params) > 0) $this->buildMap($params);
        return $this->where($this->sqlmap)->count();
    }
}