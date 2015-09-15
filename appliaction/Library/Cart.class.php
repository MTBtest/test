<?php
class Cart {
    protected $error = '';
    public function __construct() {
		$this->uid = (int) authcode(cookie('user_key'), 'DECODE', C('site_key'));
        $this->cart_db = model('cart');
        $this->goods_db = model('goods');
    }
    
    public function getError() {
        return $this->error;
    }

    /* 添加商品 */
    public function add($goods_id, $product_id = 0, $num = 1, $uid = 0) {
        $max_num = $uid ? 0 : 20;
        $goods_id = (int) $goods_id;
        $product_id = (int) $product_id;
        $num = max(1, (int) $num);
        $goods_info = $this->goods_db->detail($goods_id);
        if (!$goods_info) {
            $this->error = $this->goods_db->getError();
            return FALSE;
        }
        if ($goods_info['status'] != 1) {
            $this->error = '本商品尚未上架';
            return FALSE;
        }
        
        $key = md5($goods_id.'_'.$product_id.'_'.$uid);
           
        if ($goods_info['products_info']) {
            if ($product_id < 1 || !isset($goods_info['products_info'][$product_id]) || empty($goods_info['products_info'][$product_id])) {
                $this->error = '请选择你要购买的商品规格';
                return FALSE;
            }
            $products_info = $goods_info['products_info'];
        } else {
            $product_id = 0;
        }
        $data                   = $sqlmap = array();
        $sqlmap['goods_id']     = $data['goods_id']   = $goods_id;
        $sqlmap['products_id']  = $data['products_id'] = $product_id;
        $data['num']            = $num;
        $data['key']            = $key;
        if ($uid) {
            $data['user_id'] = $sqlmap['user_id'] = $this->uid;
            $item = $this->cart_db->where($sqlmap)->find();
            if ($item) {
                $this->cart_db->where($sqlmap)->setInc('num', $num);
            } else {
               $this->cart_db->update($data);
            }
        } else {
            $all_item = json_decode(cookie('Cart'), TRUE);
            $merge = 0;
            if ($all_item) {
                foreach ($all_item as $k => $item) {
                    $item = explode(',', $item);
                    if ($goods_id == $item[0] && $product_id == $item[1]) {
                        $k = $key;
                        $item[2] = $item[2] + $num;
                        $merge = 1;
                        unset($data);
                    }
                    $cookie_item[$k] = arr2str($item);
                }
            }
            ksort($cookie_item);
            if (!$all_item || $merge != 1) {
                if(count($all_item) == $max_num) {
                    $this->error = '您的购物车容量已满';
                    return FALSE;
                }                
                $cookie_item[$key] = arr2str(array_values($data));
            }
        }
        cookie('cart_num', count($cookie_item));
        cookie('Cart', json_encode($cookie_item));
        return TRUE;
    }    
    
	
	public function getAll($keys = '') {
        $keys = (!empty($keys)) ? authcode($keys, 'DECODE') : '';
        if ($this->uid) {
            $sqlmap = array();
            $sqlmap['user_id'] = $this->uid;
            $all_item = $this->cart_db->where($sqlmap)->getField('key,goods_id,products_id,num');
			foreach($all_item as $dateline => $item) {
				unset($item['key']);
				$all_item[$dateline] = arr2str($item);
			}
        } else {
            $all_item = json_decode(cookie('Cart'), TRUE);
        }        

		if(!$all_item) {
			cookie('cart_num', 0);
			return FALSE;
		}
        $lists = array();
        $goods_num = '';

        $md5_arr = array();
        if($keys) $md5_arr = explode(',', $keys);
        foreach ($all_item as $key => $value) {
            if(count($md5_arr) > 0 && !in_array($key, $md5_arr)) continue;
            list($goods_id, $product_id, $num) = str2arr($value);
            $item = $this->goods_db->detail($goods_id, $product_id);
            if (!$item || $item['status'] != 1 || !$item['goods_number']) {
                $this->delItem($key);
                continue;
            }
            $item['spec_array'] = unserialize($item['spec_array']);
            $item['list_img'] = $item['thumb'] ? str2arr($item['thumb']) : str2arr($item['list_img']);
            $item['goods_img'] = $item['list_img'][0];           
            $item['spec_text'] = '';
			$item['goods_num'] = ($item['goods_number'] < $num) ? $item['goods_number'] : $num;
            foreach ($item['spec_array'] as $spec) {
                $item['spec_text'] .= $spec['name'].':'.$spec['value'].';';
            }
			$item['shop_price'] = sprintf("%.2f", $item['shop_price']);
			$item['goods_num'] = (int) $item['goods_num'];
            $item['total_price'] = sprintf("%.2f", ($item['shop_price'] * $item['goods_num']));
			unset($item['descript']);
            $lists[$key] = $item;
        }
		foreach($lists as $dateline => $item) {
			if($item['prom_id'] > 0 && $item['prom_type']) {
				$pro_map = array();
				$pro_map['status'] = 1;
				$pro_map['start_time'] = array("ELT", NOW_TIME);
				$pro_map['end_time'] = array("EGT", NOW_TIME);
                $pro_map['id'] = $item['prom_id'];
                $promotion_goods = model($item['prom_type'].'_promotion')->where($pro_map)->find();
				if($promotion_goods && $item['prom_type'] != 'order') {
                    if ($item['prom_type'] == 'timed') {    // 限时促销
                        $goods_config        = json_decode($promotion_goods['goods_config'],TRUE);
                        $item['shop_price']  = sprintf('%.2f',$goods_config[$item['id']]);
                        $item['total_price'] = sprintf('%.2f', $item['shop_price'] * $item['goods_num']);
                    } else {
                        if ($promotion_goods['money'] <= $item['total_price']) {
                            switch ($promotion_goods['award_type']) {
                                /* 直接打折 */
                                case '1':
                                    $item['total_price'] = sprintf('%.2f', $item['total_price'] * $promotion_goods['award_value'] / 100);
                                    break;
                                /* 满额减价 */
                                case '2':
                                    $item['total_price'] = $item['total_price'] - $promotion_goods['award_value'];
                                    break;
                                /* 满送优惠券 */
                                default:
                                    break;
                            }
                        }
                    }
                    $item['promotion_name'] = $promotion_goods['name'];
                    // 是否还在活动中
                    if ($promotion_goods['status'] == 1 && $promotion_goods['start_time'] <= NOW_TIME && $promotion_goods['end_time'] >= NOW_TIME) {
                            $item['is_promotion'] = TRUE;
                    } else {
                        $item['is_promotion'] = FALSE;
                    }
				}				
			}
			$count_price += $item['total_price'];
			$goods_num += $item['goods_num'];
			$lists[$dateline] = $item;
		}
        cookie('cart_num', $goods_num);
        return $lists;
	}

    /* 清空所有购物车 */
    public function clear() {
        if ($this->uid) {
            $sqlmap = array();
            $sqlmap['user_id'] = $this->uid;
            $this->cart_db->where($sqlmap)->delete();
			$this->getAll();
        } else {
            cookie('cart_num', 0);
            cookie('Cart', NULL);
        }
    }

    /* 更新单个购物车 */
    public function updateItem($timestamp, $num) {
    	if(empty($timestamp)) return FALSE;
    	$num = $num;
    	if ($this->uid) {
            $sqlmap = array();
            $sqlmap['user_id'] = $this->uid;
            $sqlmap['key'] = $timestamp;
            if($num > 0) {
            	$this->cart_db->where($sqlmap)->setField('num', $num);
            } else {
            	$this->cart_db->where($sqlmap)->delete();
            }
    	} else {
    		$all_item = json_decode(cookie('Cart'), TRUE);
    		if (!$all_item || !$all_item[$timestamp]) return FALSE;
            if($num > 0) {
            	list($goods_id, $product_id, $number) = str2arr($all_item[$timestamp]);
            	$all_item[$timestamp] = $goods_id.','.$product_id.','.$num;
            	cookie('Cart', json_encode($all_item));
            } else {
            	unset($all_item[$timestamp]);
            }    		
    	}
        $this->getAll();
    	return TRUE;
    }

    public function delItem($timestamp = '') {
        if(empty($timestamp)) return FALSE;    
        if ($this->uid) {
            $sqlmap = array();
            $sqlmap['user_id'] = $this->uid;
            $sqlmap['key'] = $timestamp;
            $this->cart_db->where($sqlmap)->delete();
        } else {
        	$all_item = json_decode(cookie('Cart'), TRUE);
        	if (!$all_item) return FALSE;
            unset($all_item[$timestamp]);
            cookie('Cart', json_encode($all_item));
        }
        $this->getAll();
        return TRUE;
    }
}