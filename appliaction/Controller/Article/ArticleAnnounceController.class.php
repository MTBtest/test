<?php

class ArticleAnnounceController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();
		$this->db = model('Announcement');
		libfile('form');
	}

 /**
 *	  公告分类列表
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function lists() {
		if(IS_POST){
			$sqlmap = array();
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'DESC';
			}
			$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
			$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
			$data['total'] = $this->db->count();	//计算总数 
			$data['rows']=$this->db->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('article_announce_lists');
		}
	}

/**
 *	  添加修改页
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function update() {
		$opt = I("opt");
		$id = I("id", 0);
		$validform = TRUE;
		$dialog = TRUE;
		if (IS_POST) {
			self::save();
		} else {
			if (isset($opt) && $opt) {
				//删除
				if ($opt == 'del' && $id) {
					unset($where);
					$where['id'] = array('in', $id);
					$this->db->where($where)->delete();
					showmessage('恭喜你，删除成功！', U('ArticleAnnounce/lists'), 1);
					exit();
				}
				//添加
				if ($opt == 'add')
					include $this->admin_tpl('article_announce_update');
				//编辑
				if ($opt == 'edit' && $id > 0) {
					$info = $this->db->where('id=' . $id)->find();
					$this->info = $info;
					include $this->admin_tpl('article_announce_update');
				}
				//修改状态
				if ($opt == 'ajax_status' && $id) {
					unset($where);
					$data['status'] = array('exp', ' 1-status ');
					$this->db->where('id=' . $id)->save($data);
					showmessage('恭喜你，成功改变状态！', U('ArticleAnnounce/lists'), 1);
					exit();
				}
			} else {
				showmessage('参数错误,请联系管理员!');
			}
		}
	}

/**
 *	  处理数据
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	protected function save() {
		$id = I('id');

		//处理
		if (isset($id) && $id) {
			if ($this->db->create()) {
				$this->db->save();
				$nid = $id;
				showmessage("修改公告成功", U('ArticleAnnounce/lists'));
			} else {
				$this->error($this->db->getError());
			}
		} else {
			if ($this->db->create()) {
				$nid = $this->db->add();
				showmessage("添加公告成功", U('ArticleAnnounce/lists'));
			} else {
				showmessage($this->db->getError());
			}
		}
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
