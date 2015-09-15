<?php
/**
 *	  商品模块
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class IndexController extends HomeBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('Goods');
		$this->goods_category_db = model('Admin/Category');
		$this->goods_categorys = getcache('goods_category', 'goods');
	}

	/* 首页 */
	public function index() {
		$SEO = seo();
		include template('index');
	}
	
	/**
	 * 列表
	 */
	public function lists() {
		$catid = (int) $_GET['id'];
		// if($catid < 1) showmessage('参数错误');
		$category = $this->goods_categorys[$catid];
		if (!$category && !defined('IS_MOBILE')) showmessage('分类不存在');
		$top_parentid = $this->goods_category_db->getParent($catid);
		$category[$top_parentid] = $this->goods_categorys[$top_parentid];
		$grades = str2arr($category['grade']);
		/* 价格区间 */
		$category['grade'] = array();
		foreach ($grades as $grade) {
			if($grade!=''){
			$category['grade'][$grade] = str2arr($grade, '-');
		    }
		}
		/* 对应品牌 */
		$_dchildids = array($catid);
		$_childids = $this->goods_category_db->getChild($catid);
		if($_childids) {
			$_dchildids = array_merge($_childids, $_dchildids);
		}
		$sqlmap = $join = $brand_arr = array();
		foreach ($_dchildids as $_childid) {
			$join[] = "FIND_IN_SET({$_childid},cat_ids)";
		}
		$sqlmap['brand_id'] = array("GT", 0);
		$sqlmap['_string'] = JOIN(" OR ", $join);
		$brand_ids = $this->db->where($sqlmap)->getField('brand_id', TRUE);
		if($brand_ids) {
			$brand_arr = M('Brand')->where(array('id' => array("IN", $brand_ids)))->select();
		}
		extract($category);
		/* 获取顶级栏目 */
		$SEO = seo($catid,$name,$descript,$keywords);
		/* 多条件搜索 */
		$_order = 'sort ASC, id DESC';
		$_where = '';
		$brand_id = (int) $_GET['brand_id'];
		$price = $_GET['price'];
		/* fuck!!! */
		if($_GET['attr']) {
			$_attrs = $_GET['attr'];
			$_sqlmap = $_join = $_goods_ids = array();
			$_count = 0;
			foreach ($_attrs as $k => $attr) {
				list($_type, $_id) = explode("_", $k);
				$_id = (int) $_id;
				if($_id < 1 || empty($attr)) continue;
				$_count++;
				$type = ($_type == 'att') ? 1 : 2;
				$_join[] = "(`attribute_id` = '".$_id."' AND `attribute_value` = '".$attr."' AND `type` = '".$type."')";
			}
			if($join) $_sqlmap['_string'] = JOIN(" OR ", $_join);
			
			$_goods_ids = M('GoodsAttribute')->where($_sqlmap)->group('goods_id')->having('count(goods_id) >= '.$_count)->getField('goods_id', TRUE);
			$_goods_ids = ($_goods_ids) ? arr2str($_goods_ids) : -1;
		}
		if($_GET['sort']) {
			list($_sort, $_by) = str2arr($_GET['sort']);
			$_by = ($_by == 'ASC') ? 'DESC' : 'ASC';
			switch ($_sort) {
				case 'sale':
					$_order = "`sales_number` ".(($_by == 'ASC') ? 'DESC' : 'ASC');
					break;
				case 'hits':
					$_order = "`hits` ".(($_by == 'ASC') ? 'DESC' : 'ASC');
					break;
				case 'price':
					$_order = "`min_shop_price` ".(($_by == 'ASC') ? 'DESC' : 'ASC');
					break;
				default: 
					break;
			} 
		}
		$page = PAGE; 
		include template('goods_list');
	}

	/**
	 * 商品详细 
	 */
	public function detail() {
		$id = (int) $_GET['id'];
		if($id < 1) showmessage('参数错误');
		$rs = $this->db->detail($id);
		if (!$rs || $rs['status'] != 1) {
			showmessage('商品不存在或尚未上架');
		}
		/* 临时计算价格 */
		$this->db->where(array('id' => $rs['id']))->setField('min_shop_price', $rs['min_shop_price']);
		$rs['list_img'] = explode(',', $rs['list_img']);
		extract($rs);
        //品牌		
		$_sqlmap=array();
		$_sqlmap['id']=array('eq',$rs['brand_id']);
		$brand_name=model('brand')->where($_sqlmap)->getField('name');
		$cat_arr = str2arr($cat_ids);
		$cat_id = $cat_arr[0];
		$cat_topid = $this->goods_category_db->getParent($cat_id);
		$cat_parent = $this->goods_categorys[$cat_id];
		$cruuent_price = $shop_price;
		$cruuent_num = $goods_number;
		$cruuent_symbol = C('site_monetaryunit');
		/*规格逻辑*/
		$goods_id = $rs['id'];
		$products = $products_info;
		if($products){
			foreach ($products as $k => $v) {
				$spec_arr = $v['spec_array'];
				$spec_md5 = '';
				foreach ($spec_arr as $key => $value) {
					$spec_md5 .= $value['id'].':'.$value['value'].';';
				}

				$products[$k]['spec_array'] = $v['spec_array'];
				$products[$k]['spec_md5'] = $spec_md5;
				$products_arr[$spec_md5] = $products[$k];				
				//销售价和库存
				$products_price[] = $v['shop_price'];
				$products_num[] = $v['goods_number'];
				$market_price = $v['market_price'];
				
			}
			//销售价和库存
			if(min($products_price) != max($products_price)){
				$cruuent_price = min($products_price).' - '.max($products_price);
			}
			else{
				$cruuent_price = min($products_price);
			}
			$cruuent_num = array_sum($products_num);
		}
		$sqlmap=array();
		$sqlmap['id']=$cat_id;
		$grades=model('goods_category')->where($sqlmap)->getField('grade');
		$grade_arr=str2arr($grades,',');
		foreach ($grade_arr as $k => $v) {
			$grade_single_arr[]=str2arr($v,'-');
		}
		foreach ($grade_single_arr as $k => $v) {
			if(min($products_price)>$v[0] && min($products_price)<$v[1]){
				$price_grade_arr=$v;
			}
		}
		$price_grade=implode('-', $price_grade_arr);
		$goods_attrs=array();
        $goods_attrs = model('goods_attribute')->join('hd_attribute ON hd_goods_attribute.attribute_id=hd_attribute.id')->where('hd_goods_attribute.type=1 and goods_id='.$id)->field('attribute_id,attribute_value,name')->select();
		$attr=array();
		foreach ($goods_attrs as $key => $val) {
			$map=array();
			$map['id']=$val['attribute_id'];
			$attrs[]=model('attribute')->where($map)->field('id,name')->select();
		}
		$attr_name=array();
		foreach ($goods_attrs as $k => $v) {
			if($v['attribute_id']==$attrs[$k][0]['id']){
				$attr_name[$v['name']][]=$v['attribute_value'];
			}
		}
		foreach ($attr_name as $k => $v) {
			$v=implode('，', $v);
			$attr_name[$k]=$v;
		}
		/* 商品促销情况 */
		if($rs['prom_id'] > 0 && $rs['prom_type']) {
			$pro_map = array();
			$pro_map['id'] = $rs['prom_id'];
			$pro_map['status'] = 1;
			$pro_map['start_time'] = array("LT", NOW_TIME);
			$pro_map['end_time'] = array("GT", NOW_TIME);
			$promotion[$rs['prom_type']] = model($rs['prom_type'].'_promotion')->where($pro_map)->find();
		}
		/* 更新浏览次数 */
		$this->db->where(array('id' => $id))->setInc('hits');
		$page = PAGE;
		$SEO = seo($cat_id, $name, $brief, $keyword);
		$this->_history($id);
		include template('goods_detail');
	}

	/**
	 * 获取商品价格
	 * @param type $catId
	 * @param type $showPriceNum
	 * @return string
	 */
	public static function getGoodsPrice($catId, $showPriceNum = 5) {
		$data=model('GoodsCategory')->where(array('parent_id'=>$catId))->select();
		$tmps = array();
		foreach ($data as $key => $v) {
		   $tmps[] = $v['id'];

		}
		$ids = array();
		$ids['cat_ids'] = array('IN',$tmps);
		$goodsPrice =model('Goods')->field("min(shop_price) as min,MAX(shop_price) as max")->where($ids)->find();
		$brand = model('Goods')->where($ids)->select();
		$brand_id=array();
		foreach ($brand as $k => $v) {
		   $brand_id[] = $v['brand_id'];
		}
		$map=array();
		$map['id']=array('IN',$brand_id);
		$brand_name=model('Brand')->field('name,id')->where($map)->select();
	   
		if ($goodsPrice['min'] <= 0) {
			return array();
		}



		$minBit = strlen(intval($goodsPrice['min']));
	  
		if ($minBit <= 2) {
			$minPrice = 99;
		} else {
			$minPrice = substr(intval($goodsPrice['min']), 0, 1) . str_repeat('9', ($minBit - 1));
		}

		//商品价格计算
		$result=array();
		$result['price'] = array( '1-'.$minPrice);
		$result['brand'] = $brand_name;
		$perPrice = floor(($goodsPrice['max'] - $minPrice) / ($showPriceNum - 1));

		if ($perPrice > 0) {
			for ($addPrice = $minPrice + 1; $addPrice < $goodsPrice['max'];) {
				$stepPrice = $addPrice + $perPrice;
				$stepPrice = substr(intval($stepPrice), 0, 1) . str_repeat('9', (strlen(intval($stepPrice)) - 1));
				$result['price'][] = $addPrice . '-' . $stepPrice;
				$addPrice = $stepPrice + 1;
			}
		}

		return $result;
	}

	/**
	 * 商品历史浏览记录
	 * $data 商品记录信息
	 */
	private function _history($goods_id = 0) {
		$goods_id = (int) $goods_id;
		if($goods_id < 1) return FALSE;
		$_history = cookie('_history');
		$_history = str2arr($_history);
		if(empty($_history) || !in_array($goods_id, $_history)) {
			$_history[] = $goods_id;
		}
		$_history=array_reverse($_history);
			while (count($_history) > 20){
			array_pop($_history);
		}		
		cookie('_history', arr2str($_history));
		return TRUE;
	}

	/* 到货通知 */
	public function goods_message() {
		if (IS_POST) {
			extract($_GET);
			$goods_id = (int) $goods_id;
			$product_id = (int) $product_id;
			$num = (int) $num;
			if ($goods_id < 1) {
				showmessage('商品参数有误');
			}
			/* 判断通知方式 */
			if (empty($mobile) && empty($email)) {
				showmessage('手机号和邮箱地址至少填写一项');
			}
			if ($mobile && !is_mobile($mobile)) {
				showmessage('手机号格式不正确');
			}
			if ($email && !is_email($email)) {
				showmessage('电子邮箱格式不正确');
			}		
			/* 读取商品信息 */
			$goods_info = model('Goods')->detail($goods_id);
			if (!$goods_info || $goods_info['status'] != 1) {
				showmessage('该商品已被下架或删除');
			}
			if ($goods_info['products_info']) {
				if ($product_id < 1 || !isset($goods_info['products_info'][$product_id]) || empty($goods_info['products_info'][$product_id])) {
					showmessage('参数错误');
				}
				$products_info = $goods_info['products_info'];
				$product_info['spec_array'] = unserialize($product_info['spec_array']);
				if ($product_info['goods_number'] != 0) {
					showmessage('当前产品尚未售罄');
				}
			} else {
				$product_id = 0;
				if ($goods_info['goods_number'] != 0) {
					showmessage('当前产品尚未售罄');
				} 
			}



			$uid = is_login();
			$data = array(
				'user_id'	 => $uid,
				'goods_id'	=> $goods_id,
				'product_id'  => $product_id,
				'goods_price' => (int)$goods_info['shop_price'],
				'goods_name'  => $goods_info['name'],
				'mobile'	  => $mobile,
				'email'	   => $email,
				'num'		 => $num
			);
			/* 检测是否重复 */
			$sqlmap = array();
			$sqlmap['goods_id']   = $goods_id;
			$sqlmap['product_id'] = $product_id;
			$sqlmap['status']	 = 0;
			if ($uid) {
				$sqlmap['user_id'] = $uid;
			} else {
				$sqlmap['_string'] = "`mobile` = '{$mobile}' OR `email` = '{$email}'";
			}
			if (model('GoodsMessage')->where($sqlmap)->count()) {
				showmessage('请勿重复提交订阅');
			}
			$result = model('GoodsMessage')->update($data);
			if (!$result) {
				showmessage('到货通知订阅失败');
			} else {
				showmessage('到货通知订阅成功', '', 1);
			}
		} else {
			showmessage('请勿非法访问');
		}
	}

	public function comment(){
		include template('comment');
	}
	/** 
	*	商品分类 [wap] 
    * 	@param  none;
    * 	@author 老孔
    *	@date 2015-04-26
	*/
	public function category() {
		include template('category');
	}


}