<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class CommentController extends AdminBaseController {
    public function _initialize() {
        parent::_initialize();
        $this->db = model('Comment');  		
    }

    public function lists() {
    	libfile('Page');
		$pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
    	$sqlmap = array();
		$count = $this->db->where($sqlmap)->count();
		$pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
        $page = new Page($count, $pagesize);
        $lists = $this->db->where($sqlmap)->limit($page->firstRow . ',' . $page->listRows)->select();
        $page = $page->show();
    	include $this->admin_tpl('comment_list');
    }

    /* 评论回复 */
    public function reply() {
    	$id = (int) $_GET['id'];
    	$rs = $this->db->getById($id);
    	if (!$rs) showmessage('数据不存在');
    	$good_name = M('Goods')->getFieldById($rs['goods_id'], 'name');
    	if ($rs['user_id'] > 0) {
    		$user_name = M('User')->getFieldById($rs['user_id'], 'user_name');
    		$user_name = ($user_name) ? $user_name : '用户不存在' ;
    	} else {
    		$user_name = '游客';
    	}
    	if (IS_POST) {
    		$_GET['reply_time'] = NOW_TIME;
    		$result = $this->db->save($_GET);
    		if (!$result) {
    			showmessage($this->db->getError());
    		}
    		showmessage('评论回复成功', U('Admin/Comment/lists'), 1);
    	} else {
    		include $this->admin_tpl('comment_reply');
    	}
    }

    /* 评论删除 */
    public function delete() {
    	$id = (array) $_GET['id'];
    	if(empty($id)) showmessage('参数错误');
    	$sqlmap = array();
    	$sqlmap['id'] = array("IN", $id);
    	$this->db->where($sqlmap)->delete();
    	showmessage('评论数据删除成功', U('Admin/Comment/lists'), 1);
    }
}