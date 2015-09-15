<?php

class CommentController extends HomeBaseController {

	/**
	 * 自动执行
	 */
	public function _initialize() {
		parent::_initialize();
	}

	/**
	 * 列表
	 */
	public function ajax_lists() {
		$goods_id=(int)$_GET['goods_id'];
		$sqlmap = array();
		$sqlmap['goods_id']=$goods_id;
		$sqlmap['status']=1;
		
		libfile('Page');
		$count=model('Comment')->where($sqlmap)->count();
		$pagenum=getconfig('site_commentsnum');
		$page = new Page($count, $pagenum);
		$page->setConfig('prev','');
		$page->setConfig('next','');
		$page->setConfig('theme','%upPage% %linkPage% %downPage%');
		
		$list=model('Comment')->where($sqlmap)->limit($page->firstRow . ',' . $page->listRows)->select();

		$uid =array();
		foreach ($list as $k =>$v) {
		$uid[] = $v['user_id'];
		$userid = $uid;
		$map = array();
		$map['id'] = array('IN',$userid);
		$u_list = model('User')->where($map)->select();
	   
	  
			foreach ($u_list as $key => $s) {
				if ($v['user_id'] == $s['id']) {
					$list[$k]['ico'] = $s['ico'];
				}
				
			}

		}
	   
		$data['list']=$list;
		
		$data['page']=$page->show();
		
		$data['total']=$count;
		
		
		$this->ajaxReturn($data);
		
		
	}
	
		
}
