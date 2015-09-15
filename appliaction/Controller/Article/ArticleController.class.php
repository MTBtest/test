<?php

class ArticleController extends AdminBaseController {

/**
 *	  自动执行
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function _initialize() {
		parent::_initialize();
		$this->db = model('Article');
		libfile('form');
	}

/**
 *	  文章列表
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
			$join = 'LEFT JOIN __ARTICLE_CATEGORY__ AS c ON a.category_id = c.id';
			$data['total'] = $this->db->count();	//计算总数 
			$data['rows']=$this->db->alias('a')->field('a.*,c.name as catename')->join($join)->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('article_list');
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
		$validform = TRUE;
		$opt = I("opt");
		$id = I("id", 0);
		if (IS_POST) {
			self::save();
		} else {
			if (isset($opt) && $opt) {
				//分类列表
				$cate = model('ArticleCategory');
				$data = $cate->order('parent_id ASC,sort ASC,id ASC')->select();

				$catInfo = getTree($data, 0);
				$catTree = $cate->formatCat($catInfo);

				//编辑
				if($opt=='edit' && $id>0){
					$info=$this->db->where('id='.$id)->find();
					$this->pid=$info['parent_id'];
					$this->info=$info;
				}
				//删除
				if($opt=='del' && $id>0){
					unset($where);
					$where['id'] = array('in', $id);
					$this->db->where($where)->delete();
					showmessage('恭喜你，删除成功！', U('ArticleAnnounce/lists'), 1);
					exit();
				}				
				//修改状态
				if($opt == 'ajax_status' && $id ){
					unset($where);
					$data['status']=array('exp',' 1-status ');;
					$this->db->where('id='.$id)->save($data);
					showmessage('恭喜你，成功改变状态！',U('lists'),1); 
					exit(); 
			}
				//是否置顶
				if($opt == 'ajax_top' && $id ){
					unset($where);
					$data['top']=array('exp',' 1-top ');;
					$this->db->where('id='.$id)->save($data);
					showmessage('恭喜你，成功改变状态！',U('lists'),1); 
					exit(); 
		}
				include $this->admin_tpl("article_update");
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
	protected function save(){
		$id = $_POST['id'];
		//处理
		if (isset($id) && $id) {
			if ($this->db->create()) {
				$this->db->save();
				$nid=$id;
				showmessage("修改文章成功", U('lists'),1);
			} else {
				$this->error($this->db->getError());
			}
		} else {
			if ($this->db->create()) {
				$nid = $this->db->add();
				showmessage("添加文章成功", U('lists'),1);
			} else {
				$this->error($this->db->getError());
			}
		}
	}
}
