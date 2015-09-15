<?php
/**
 *	  文章分类列表
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class ArticleLinkController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();
		$this->db = model('Friend_link');
	}

	public function lists(){
		if(IS_POST){
			$sqlmap = array();
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['sort'] = 'ASC';
				$order['id'] = 'ASC';
			}
			$data['rows']=$this->db->order($order)->select();
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('article_link_lists');
		}
	}
/**
 *	  添加修改页
 */
	public function update(){
		$db=model('Friend_link');
		$opt=I("opt");
		$id=I("id",0);
		if(IS_POST){
			self::save();
		}else{
			if(isset($opt) && $opt){
				//删除
				if($opt == 'del' && $id ){
					unset($where);
					$where['id']=array('in',$id);
					$db->where($where)->delete();
					showmessage('恭喜你，删除成功！',U('Article_link/lists'),1); 

					exit(); 
				}

			}else{
				showmessage('参数错误,请联系管理员!',NULL,0);
			}
		}
		
	}
/**
 *	  处理数据
 */
	protected function save(){
		$data = json_decode(stripslashes(htmlspecialchars_decode($_GET['data'])),true);
		foreach ($data as $key => $value) {
			$this->db->update($value);
		}
		showmessage('整理友情链接成功',null,1);
		
	}

	
}
