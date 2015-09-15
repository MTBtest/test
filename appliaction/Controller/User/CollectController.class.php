<?php
/**
 *      会员中心 - 我的收藏夹
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class CollectController extends UserBaseController
{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('user_collect');
    }
    
    /* 收藏管理 */
    public function lists() {
        $type = I('type','all');
        if ($type==''){
            $type='all';
        }
        $_GET['user_id'] = $this->userid;
        $sqlmap = array();
        $sqlmap['user_id'] = $this->userid;
        $lists = $this->db->lists($_GET);

        if($lists !== FALSE) {
            foreach($lists['lists'] as $key => $value) {
               $lists['lists'][$key]['shop_price'] = M('GoodsProducts')->field("min(shop_price) AS min_shop_price, max(shop_price) AS max_shop_price")->where(array('goods_id'=>$value['goods_id']))->find();
            }
        }
        // print_r($lists);
        $SEO=seo(0,"我的收藏");
        include template('collect_list');
    }

    /* 添加收藏 */
    public function add($goods_id = 0) {
        $goods_id = (int) $goods_id;
        if(!M('Goods')->getById($goods_id))
            showmessage('商品不存在，加入失败');
        $sqlmap             = array();
        $sqlmap['goods_id'] = $goods_id;
        $sqlmap['user_id']  = $this->userid;
        if ($this->db->where($sqlmap)->count()) {
            showmessage('您已收藏过本商品！');
        }
        $sqlmap['add_time'] = NOW_TIME;
        $this->db->add($sqlmap);
        showmessage('商品收藏成功', '', 1);
    }
    
    /**
     * 删除会员的指定商品收藏记录
     * @param type $goods_id
     * @return mixed
     */
    public function delone($goods_id = '') {
        $goods_id = (int) $goods_id;
        if($goods_id < 1) showmessage ('参数有误');
        $sqlmap = array();
        $sqlmap['goods_id'] = $goods_id;
        $sqlmap['user_id'] = $this->userid;
        $this->db->where($sqlmap)->delete();
        showmessage('收藏记录删除成功', '', 1);
    }
    
    /**
     * 获取收藏状态
     * @param int $goods_id 商品ID
     * @return bool
     */
    public function getCollectStatus($goods_id = 0) {
        $goods_id = (int) $goods_id;
        if($goods_id < 1) showmessage ('参数有误');
        $sqlmap = array();
        $sqlmap['goods_id'] = $goods_id;
        $sqlmap['user_id'] = $this->userid;
        if($this->db->where($sqlmap)->count()) {
            showmessage('已收藏', '', 1);
        } else {
            showmessage('未收藏');
        }
    }
    
    /* 收藏删除 */
    public function delete() {
        $idarr = explode(",", $_GET['idarr']);
        if (empty($idarr)) {
            showmessage('参数错误');
        }
        $sqlmap = array();
        $sqlmap['id'] = array("IN", $idarr);
        $sqlmap['user_id'] = $this->userid;
        $rs = $this->db->where($sqlmap)->delete();
        if (!$rs) {
            showmessage('收藏记录删除失败');
        } else {
            $info=array();
            $info['status'] = 1;
            $info['info'] = '指定收藏删除成功';
            $this->ajaxReturn($info);
            // showmessage('指定收藏删除成功', U('index'), 1);
        }
    }
}