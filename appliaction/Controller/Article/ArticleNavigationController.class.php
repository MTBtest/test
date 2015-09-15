<?php
class ArticleNavigationController extends AdminBaseController
{
	public function _initialize() {
		parent::_initialize();
		$this->db = model('Navigation');
	}
	
	public function manage() {
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
			include $this->admin_tpl('article_navigation_list');
		}
	}

	public function ajax_del(){
		$id = $_GET['id'];
		if(!empty($id)){
			unset($where);
			$where['id']=array('in',$id);
			$this->db->where($where)->delete();
			$data=array();
			$data['status'] = 1;
			$this->ajaxReturn($data);
		}else{
			$data=array();
			$data['info'] = '非法操作，请联系管理员！！';
			$this->ajaxReturn($data);
		}
	}

	public  function ajax_status(){

		$status = $_GET['status'];
		$id = $_GET['id'];
		$result = $this->db->where(array('id'=>$id))->save(array('enable'=>array('exp','1-enable')));
		if ($result) {
			showmessage('恭喜你，成功改变状态!',U('article_navigation/manage'),1);
		}else{
			showmessage('修改失败');
		}
	}


	 /**
	 * 添加修改页
	 */
	public function update(){
		$db=model('Navigation');
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
					showmessage('恭喜你，删除成功！',U('article_navigation/manage'),1); 

					exit(); 
				}

			}else{
				showmessage('参数错误,请联系管理员!',NULL,0);
			}
		}
		
	}
	/**
	 * 处理数据
	 */
	protected function save(){
		$data = json_decode(stripslashes(htmlspecialchars_decode($_GET['data'])),true);
		foreach ($data as $key => $value) {
			$this->db->update($value);
		}
		showmessage('更新导航成功',null,1);
	}
	
}