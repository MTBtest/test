<?php
/**
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class ConsultController extends HomeBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('Consult');
	}

	/**
	 * 列表
	 */
	public function ajax_lists() {
		$goods_id = (int)$_GET['goods_id'];
		$sqlmap = array();
		$sqlmap['goods_id'] = $goods_id;
		$sqlmap['status']=1;

		libfile('Page');
		$count=model('Consult')->where($sqlmap)->count();
		$pagenum=getconfig('site_consultingnum');
	   
		$page = new Page($count, $pagenum);
		$page->setConfig('prev','');
		$page->setConfig('next','');
		$page->setConfig('theme','%upPage% %linkPage% %downPage%');
		
		$list=model('Consult')->where($sqlmap)->order('id DESC,sort ASC')->limit($page->firstRow . ',' . $page->listRows)->select();

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

	/* 咨询发表 */
	public function add(){
		if (IS_POST) {
			$_GET['user_id'] = is_login();
			$_GET['user_name'] = '游客';
			if ($_GET['user_id']) $_GET['user_name'] = cookie('_uname');
			// 过滤xss攻击;
			$_GET['question'] = remove_xss($_GET['question']);
			if (!$_GET['question']) showmessage('请输入您要咨询的问题');
			$result = $this->db->update($_GET);
			if (!$result) showmessage('商品咨询发表失败');
			showmessage('商品咨询完成，请等待管理员审核！', 1000,1);
		}elseif (defined('IS_MOBILE')) {	// [wap咨询模版]
			$goods = model('goods')->detail((int)$_GET['goods_id']);
			if (!$goods) showmessage('商品信息有误，请回到商品页面重新咨询',__APP__);
			include template('consult_add');		
		} else {
			showmessage('请勿非法提交');
		}	  
	}
}
