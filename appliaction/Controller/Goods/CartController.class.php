<?php
/**
 * 购物车
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042 
 */

/**
 * 游客时保存cookie，会员这保存在数据库
 */
class CartController extends HomeBaseController {
    public function _initialize() {
        parent::_initialize();
        $this->uid = (int) authcode(cookie('user_key'), 'DECODE', C('site_key'));
        $this->cart_db = model('Cart');
        $this->goods_db = model('Goods');
		libfile('Cart');
		$this->Cart = new Cart();
    }

    /* 购物车管理 */
    public function index() {
        $lists = $this->Cart->getAll();
		$count_price = $goods_num = 0;
		foreach($lists as $dateline => $item) {
			$count_price += $item['total_price'];
			$goods_num += $item['goods_num'];
		}
		$count_price = sprintf('%.2f', $count_price);
		$goods_num = (int) $goods_num;
		$SEO = seo(0,"购物车信息");
        include template('cart_list');
    }


    /* 添加商品 */
    public function add() {
        extract($_GET);
        $result = $this->Cart->add($goods_id, $product_id, $num, $this->uid);
        if(!$result) {
            showmessage($this->Cart->getError());
        } else {
            showmessage('商品已成功加入购物车', U('succeed'), 1);
        }
    }

    public function update() {
        extract($_GET);
        $result = $this->Cart->updateItem($timestamp, $num);
        if(!$result) {
            showmessage('购物车更新失败');
        } else {
            showmessage('购物车更新成功', U('index'), 1);
        }
    }
    public function delete() {
        $timestamp = $_GET['timestamp'];
        $result = $this->Cart->delItem($timestamp);
        if (!$result) {
            showmessage('删除失败，请重试', U('index'));
        } else {
            showmessage('购物车删除成功', U('index'), 1);
        }        
    }

    public function clear() {
        $this->Cart->clear();
        showmessage('购物车已清空', U('index'), 1);
    }
	
	/**
	 * 获取所有商品列表
	 */
	public function getCartList() {
		$result = array();
		$result['list'] = $this->Cart->getAll();
		$result['total_price'] = 0;
		$result['goods_num'] = 0;
		$result['goods_count'] = (!$result['list']) ? 0 : count($result['list']);
		foreach($result['list'] as $dateline => $item) {
			$result['goods_num'] += $item['goods_num'];
			$result['total_price'] += $item['total_price'];
		}
		echo json_encode($result);
	}
	
	/**
	 * 获取购物车总件数
	 */
	public function getCartCount() {
		$result = $this->Cart->getAll();
		echo (!$result) ? 0 : count($result);
	}

    public function succeed() {
    	$all_item = $this->Cart->getAll();
		$count_price = $goods_num = 0;
		foreach($all_item as $item) {
			$count_price += $item['total_price'];
			$count_price = sprintf("%.2f",$count_price);
			$goods_num += $item['goods_num'];
		}
//		p($all_item);
        include template('cart_succeed');
    }
}