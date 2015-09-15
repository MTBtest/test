<?php
/**
 * 帮助中心
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class IndexController extends HomeBaseController{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('Article');
        $this->category_db = model('ArticleCategory');
    }

    /* 首页 */
    public function index(){
        include template('index');
    }

    /* 列表页 */
    public function lists($id = 0) {
        $id = I('id');
        if ($id < 1)
            showmessage('参数错误');
        $category = $this->category_db->getById($id);
        if (!$category) {
            showmessage('您查看的分类不存在');
        }
        $top_category = $category;
        $top_catid = $this->category_db->getParent($id);
        if ($top_catid != $id) {
            $top_category = $this->category_db->getById($id);
        }
        extract($category);
        $page=PAGE;
        $SEO=seo(0,"文章信息");
        include template('list'); 
    }

    /* 详情页 */
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
        $SEO=seo(0,"文章详情信息");
        include template('detail');
    }

 /* 公告详情页 */
	public function announce($id = '') {
		$db = model('Announcement');
		$id = (int) $id;
		$rs = $db->getById($id);
		if($id < 1 || !$rs)
			showmessage('参数错误');
		extract($rs);
		/* 获取上/下一篇 */
		$sqlmap = array();
		$sqlmap = array('status' => 1);
		$sqlmap['id'] = array('LT', $id);
		$prenext['pre'] = $db->where($sqlmap)->order("`id` DESC")->find();
		$sqlmap['id'] = array('GT', $id);
		$prenext['next'] = $db->where($sqlmap)->order("`id` ASC")->find();
		$SEO=seo(0,"文章详情信息");
		include template('announce');
	}	    
}