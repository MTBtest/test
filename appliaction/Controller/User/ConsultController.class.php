<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class ConsultController extends UserBaseController
{

    public function _initialize() {
        parent::_initialize();
        $this->db = model('Consult');
    }

    /* 管理列表 */
    public function manage() {
        extract($_GET);
        $isreply = (isset($isreply) && is_numeric($isreply)) ? $isreply : -1;
        $sqlmap = array();
        $sqlmap['user_id'] = $this->userid;
        $meta_title = '';
        switch ($isreply) {
            case '0':
                $sqlmap['reply_time'] = 0;
                $meta_title = '- 未回复咨询';
                break;
            case '1':
                $sqlmap['reply_time'] = array("GT", 0);
                $meta_title = '- 已回复咨询';
                break;
            default:
                break;
        }
        $count = $this->db->where($sqlmap)->count();
        $lists = $this->db->where($sqlmap)->page(PAGE, 10)->select();
        $pages = pages($count, 10);

        if($lists) {
            foreach ($lists as $k => $v) {
                $v['_goods_info'] = model('Goods')->detail($v['goods_id'], $v['product_id']);
                $lists[$k] = $v;
            }
        }
        $SEO = seo(0, '咨询回复'.$meta_title);
        include template('consult_list');
    }
}