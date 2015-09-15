<?php

class ArticleAnnouncesController extends HomeBaseController {

	public function _initialize() {
		parent::_initialize();
		$this->db = model('Announcement');
		libfile('form');
	}

/**
 *	  获取详情页内容
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */   

    public function detail($id = '') {
        $id = (int) $id;
        $rs = $this->db->getById($id);
        if($id < 1 || !$rs)
            showmessage('参数错误');
        extract($rs);
        /* 获取上/下一篇 */
        $sqlmap = array();
        $sqlmap = array('status' => 1);
        $sqlmap['id'] = array('LT', $id);
        $prenext['pre'] = $this->db->where($sqlmap)->order("`id` DESC")->find();
        $sqlmap['id'] = array('GT', $id);
        $prenext['next'] = $this->db->where($sqlmap)->order("`id` ASC")->find();
        $SEO=seo(0,"商城公告信息");
        include template('announce');
    }
}
