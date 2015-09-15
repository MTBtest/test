<?php
/**
 *      帮助中心
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class HelpController extends HomeBaseController{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('help');
    }

    /* 帮助中心首页 */
    public function index(){
        include template('help_index');
    }

    /* 列表 */
    public function detail($id = '') {
        $id = (int) $id;
        $rs = $this->db->getById($id);
        if($id < 1 || !$rs)
            showmessage('参数错误');
        extract($rs);
        $SEO=seo(0,$title);
        include template('help_detail');
    }    
}