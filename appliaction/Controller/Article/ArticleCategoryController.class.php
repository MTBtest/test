<?php
	/**
	 *	  文章分类列表
	 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
	 *	  This is NOT a freeware, use is subject to license terms
	 *
	 *	  http://www.haidao.la
	 *	  tel:400-600-2042
	 */
class ArticleCategoryController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();	
		$this->db = model('ArticleCategory');
	}

	public function lists(){
		include $this->admin_tpl('article_category_lists');
	}
	/**
	 * 列表
	 */
	public function cat_child(){
		$parent_id = isset($_GET['id'])?$_GET['id']:0;
		$data	= $this->db->lists($parent_id );
		foreach ($data as $key => $value) {
			$data[$key]['state'] = $this->has_child($value['id']) ? 'closed' : 'open';
		}
		echo json_encode($data);
	}
	
	function has_child($id){
		$rows = $this->db->where(array('parent_id'=>$id))->count();
		return $rows > 0 ? true : false;
	}
	/**
	 * 编辑
	 */
	public function update(){
		$validform = TRUE;
		$db=model('ArticleCategory');
		$opt=$_GET["opt"];
		$id=$_GET["id"];
		if(IS_POST){
			self::save();
		}else{
			if(isset($opt) && $opt){
				//分类列表
				$cate = model('ArticleCategory');
				$data = $cate->order('parent_id ASC,sort ASC,id ASC')->select();

				$catInfo = getTree($data, 0);
				$catTree = $cate->formatCat($catInfo);
				$pid = $_GET['pid'];
				//编辑
				if($opt=='edit' && $id>0){
					$info=$db->where('id='.$id)->find();
					$this->pid=$info['parent_id'];
					$this->info=$info;
				}
				//删除
				if($opt=='del' && $id>0){
					if($cate->where('parent_id='.$id)->find()){
						showmessage('删除失败，该类存在子类！',U('lists'),1); 
					}else{
						$db->where('id='.$id)->delete();
						showmessage('恭喜你，删除类别成功！',U('lists'),0); 
					}
				}
				
				include $this->admin_tpl('article_category_update');
			}else{
				showmessage('参数错误,请联系管理员!',U('lists'),0);
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
		$db = model('ArticleCategory');
		$id = $_POST['id'];
		
		//处理
		if (isset($id) && $id) {
			$ptree=$this->findFather($_POST['id']);
			if(in_array($_POST['parent_id'],$ptree)){
				showmessage("父分类不能移动到子分类",NULL,0);
			}
			if ($db->create()) {
				$db->save();
				$nid=$id;
				showmessage("修改类别成功", U('lists'),1);
			} else {
				$this->error($db->getError());
			}
		} else {
			if ($db->create()) {
				$nid = $db->add();
				showmessage("添加类别成功", U('lists'),1);
			} else {
				showmessage($db->getError(),U('lists'),0);
			}
		}
	}

/* 
 *	  取所有父类ID
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function findFather($pid){
		static $flist=array(); 
		$cate=model('Admin/Article_category');
		$row = $cate->where('id='.$pid)->find();
		if ((int)$row['parent_id'] != 0)
		 {
			$classFID  = $row['parent_id'];
			$flist[] = $classFID;
			$this->findFather($classFID);
		}
		return $flist;
		
	}
	
}
