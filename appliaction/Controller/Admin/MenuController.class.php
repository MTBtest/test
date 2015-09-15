<?php
/*自定义菜单*/
class MenuController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('menu');
		libfile('Wechat');
	}

	/**
	 * 列表管理
	 * @return mixed
	 */
	public function lists(){
		include $this->admin_tpl("menu_list");
	}

	public function cat_child(){
		$parent_id = isset($_GET['id'])?$_GET['id']:0;
		$data = $this->db->lists($parent_id);
		foreach ($data as $key => $value) {
			if($value['type']==1){
				 if($value['link']){
				 	if(!strpos($value['link'],'&bind')) {
				 		$url = substr($value['link'], strpos($value['link'], '?m'));
				 		$data[$key]['link']=model('default_menu')->where(array('url'=>$url))->getField('name');
				 	}else {
				 		$url = substr($value['link'], strpos($value['link'], '?m'),(strpos($value['link'], '&bind=account')-strpos($value['link'], '?m')));
				 		$data[$key]['link']=model('default_menu')->where(array('url'=>$url))->getField('name');
				 	}
				}
			}
			$data[$key]['state'] = $this->has_child($value['id']) ? 'closed' : 'open';
		}
		echo json_encode($data);
	}
	
	public function has_child($id){
		$rows = $this->db->where(array('parent_id'=>$id))->count();
		return $rows > 0 ? true : false;
	}

	/* 取所有父类ID*/
	public function findFather($pid){
		static $flist=array(); 
		$row = $this->db->where('id='.$pid)->find();
		if ((int)$row['parent_id'] != 0) {
			$classFID  = $row['parent_id'];
			$flist[] = $classFID;
			$this->findFather($classFID);
		}
		return $flist;	
	}

	public function ajax_del(){
		$id=intval($_GET['id']);
		if($id>0) {
			if(!$this->has_child($id)){
				$this->db->where('id='.$id)->delete();
				showmessage('恭喜你，删除菜单成功！',null,1); 
			} else {
				showmessage('请先删除子菜单！'); 
			}	
		} else {
			showmessage('非法操作，请联系管理员！'); 
		}
	}

	public function edit(){
		$validform = TRUE;
		if(IS_POST) {
			if($_GET['old_pid']==0 && $_GET['old_pid']!=$_GET['parent_id'])showmessage('不允许移动主菜单');
			$ptree=$this->findFather($_GET['parent_id']);
			if(in_array($_GET['old_pid'],$ptree)){
				showmessage("父菜单不能移动到子菜单",NULL,0);
			}
			$_GET['url']=htmlspecialchars_decode($_GET['url']);
			$url=$this->db->where(array('id'=>$_GET['id']))->getField('link');
			if($_GET['type']==1){
				if($url == $_GET['url']) {
					$_GET['link']=$url;
				} elseif ($_GET['url'] == '?m=user&c=public&a=login') {
					$_GET['link'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_GET['url'].'&bind=account';
				}else{
					$_GET['link']='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_GET['url'];
				}
			} elseif ($_GET['type']==2) {
			 	$_GET['link']=htmlspecialchars_decode($_GET['customurl']);
			}     
			$menu_num=$this->db->where(array('parent_id'=>array('EQ','0')))->count();
			$result=$this->db->update($_GET);
			if($result){
				showmessage('菜单修改成功！',U('lists'),1); 
			}else{
				showmessage('添加失败，请重试！',null,1); 
			}
		}else{
			$built_info=model('default_menu')->select();
			$data=$this->db->order('parent_id ASC,sort ASC,id ASC')->select();
			$info= getTree($data,0);
			$info = $this->db->formatCat($info);
			$data = $this->db->getById($_GET['id']);
			$data['parent_name']=$this->db->where(array('id'=>$data['parent_id']))->getField('name');
			$data['has_child']=$this->has_child($_GET['id']) ? 1 :0;
			include $this->admin_tpl("menu_edit");
		}
	}

	public function add(){
		$validform = TRUE;
		if(IS_POST) {
			if($_GET['type']==1){
				$_GET['url']=htmlspecialchars_decode($_GET['url']);
				if($_GET['url'] == '?m=user&c=public&a=login'){
					$_GET['link'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_GET['url'].'&bind=account';
					}else{
						$_GET['link']='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_GET['url'];
					}
				} elseif ($_GET['type']==2) {
					$_GET['link']=$_GET['customurl'];
				}
				if($_GET['parent_id']==0){
					$menu_num=$this->db->where(array('parent_id'=>array('EQ','0')))->count();
					if($menu_num < 3) {
						$result=$this->db->update($_GET);
						if($result){
							showmessage('恭喜你，添加菜单成功！',U('lists'),1); 
						}else{
							showmessage('添加失败，请重试！',null,1); 
						}
					}else{
						showmessage('一级菜单不能超过3个',NULL,0);
					}
				}else {
					$menu_num=$this->db->where(array('parent_id'=>$_GET['parent_id']))->count();
					if($menu_num < 5){
						$result=$this->db->update($_GET);
						if($result){
							showmessage('恭喜你，添加菜单成功！',U('lists'),1); 
						}else{
							showmessage('添加失败，请重试！',null,1); 
						}
					}else{
						showmessage('二级菜单不能超过5个',NULL,0);
				}
			} 
		} else {
			$built_info=model('default_menu')->select();
			$data=$this->db->order('parent_id ASC,sort ASC,id ASC')->select();
			$info= getTree($data,0);
			$info = $this->db->formatCat($info);
			include $this->admin_tpl("menu_add");
		}
	}


	public function create_menu(){
		$data=array();
		$topmenu=$this->db->where(array('parent_id'=>array('EQ','0')))->select();
		foreach ($topmenu as $k => $v) {
			$sqlmap=array();
		 	$sqlmap['parent_id']=$v['id'];
		 	if(($this->db->where($sqlmap)->count())>0){
		 		$data['button'][$k]=array('name'=>$v['name'],'sub_button'=>array());
		 		$secmenu=$this->db->where(array('parent_id'=>array('NEQ','0')))->select();
		 		foreach ($secmenu as $key => $value) {
		 			if($value['parent_id']==$v['id']){
		 				if($value['type'] < 3){
		 					$data['button'][$k]['sub_button'][]=array('type'=>'view','name'=>$value['name'],'url'=>$value['link']);
		 				}
		 			}
		 		}
		 	}else{
		 		if($v['type'] < 3){
		 			$data['button'][]=array('type'=>'view','name'=>$v['name'],'url'=>$v['link']);
		 		}
		 	}
		}

		$options=array();
		$options['token']=C('weixin_token');
		$options['encodingAesKey']=md5(C('weixin_token'));
		$options['appid']=C('appid');
		$options['appsecret']=C('appsecret');
		$weObj=new Wechat($options);
		$result = $weObj->createMenu($data);
		if($result){
			showmessage('生成菜单成功！',null,1); 
		}else{
			showmessage('生成菜单失败，请检查微信公众号配置是否正确',NULL,0);
		}
	}

	public function delete_menu(){
		$weObj=new Wechat(C('weixin_token'),md5(C('weixin_token')),C('appid'),C('appsecret'));
		$result = $weObj->deleteMenu();
	}
}