<?php
class ArticleHelpController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();	
		$this->db = model('help');
		libfile('form');
	}

 /**
 *	  文章分类列表
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
	public function lists(){
//		$validform = TRUE;
//		
//		$db->field('id,fpid as parent_id,title,message,identifier,keyword,status,sort');
//		$db->order('sort ASC,fpid ASC,id ASC');
//		$list=$db->select();
//		$list= list_to_tree($list, 'id', 'parent_id');
		include $this->admin_tpl('article_help_lists');
	}
	/**
	 * 列表
	 */
	public function help_child(){
		$sqlmap = array();
		$fpid = isset($_GET['id'])?$_GET['id']:0;
		$sqlmap['fpid'] = $fpid;
		$field = 'id,fpid,title,message,identifier,keyword,status,sort';
		$data = $this->db->field($field)->where($sqlmap)->order(array('sort'=>'ASC','id'=>'ASC'))->select();
		foreach ($data as $key => $value) {
			$data[$key]['state'] = $this->has_child($value['id']) ? 'closed' : 'open';
		}
		echo json_encode($data);
	}
	
	function has_child($id){
		$rows = $this->db->where(array('fpid'=>$id))->count();
		return $rows > 0 ? true : false;
	}
/**
 *	  添加修改页
 */
	public function update(){
		$validform = TRUE;
		$db=model('Help');
		$opt=I("opt");
		$id=I("id",0);
		if(IS_POST){
			self::save();
		}else{
			if(isset($opt) && $opt){
				//删除
				if($opt == 'del' && $id > 0){
					if(!$this->has_child($id)){
						$db->delete($id);
						showmessage('删除完成!',U('ArticleHelp/lists'),1);
					}else{
						showmessage('分类下有文章不允许删除!',U('ArticleHelp/lists'),0);
					}
				}
				if($opt == 'edit' && $id > 0){
					$info=$db->where('id='.$id)->find();
					$this->info=$info;
					include $this->admin_tpl('article_help_update');
				}
				//修改状态
				if($opt == 'ajax_status' && $id ){
					unset($where);
					$data['status']=array('exp',' 1-status ');
					$db->where('id='.$id)->save($data);
					showmessage('恭喜你，成功改变状态！',U('ArticleHelp/lists'),1); 
					exit(); 
				}
			}else{
				showmessage('参数错误,请联系管理员!',U('ArticleHelp/lists'),0);
			}
		}
		
	}
/**
 *	  处理数据
 */
	protected function save(){
		$data = json_decode(stripslashes($_GET['data']),true);
		if (isset($data)){
			foreach ($data as $key => $value) {
				$this->db->update($value);
			}
			showmessage('整理帮助主题成功',null,1);
		}else{
			$data['fpid'] = (int)$_GET['fpid'];
			$data['title'] = $_GET['title'];
			$data['sort'] = (int)$_GET['sort'];
			$data['status'] = (int)$_GET['status'];
			$this->db->update($data);
			$this->ajaxReturn(array('info'=>'添加主题成功','status'=>1));
		}
	}
/**
 * 保存详细
 */
 	public function edit(){
 		$_GET=htmlspecialchars_decode( dstripslashes($_GET));
 		$this->db->update($_GET);
		showmessage('编辑完成！',U('ArticleHelp/lists'),1); 
 	}
}
