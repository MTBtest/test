<?php

class GoodsMessageController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();
		$this->db = model('GoodsMessage');
	}
	
	public function lists(){
		$status = $_GET['status']?$_GET['status']:0;
		if(IS_POST){
			$sqlmap = array();
			if(is_numeric($status)){
				$sqlmap['status'] = $status;
			}
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'DESC';
			}
			$group = ('goods_id,product_id');
			$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
			$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
			$data['total'] = $this->db->where($sqlmap)->group($group)->getField('id', true);	//计算总数 
			$data['total'] = count($data['total']);
			$data['rows']=$this->db
				->field('id,goods_id,product_id, count(id) AS count')
				->where($sqlmap)
				->group($group)
				->limit(($pagenum-1)*$rowsnum.','.$rowsnum)
				->order($order)
				->select();
			foreach($data['rows'] as $key=>$value){
				$data['rows'][$key]['goods_name'] = getGoodsNumber($value['goods_id'],$value['product_id'],'name');
				$data['rows'][$key]['goods_num'] = getGoodsNumber($value['goods_id'],$value['product_id'],'goods_number');
			}
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('goods_message_lists');
		}
	}
	/**
	 * 到货通知详细列表
	 */
	public function detail(){

		$status = $_GET['status'];
		$goods_id = $_GET['goods_id'];
		$product_id = $_GET['product_id'];
		
		$status = $status ? $status : 0;
		$sqlmap = array();
		//筛选
		if($status==0 && is_numeric($status)){
			$hover[0]='hover';
			$hover[1]='';
			$sqlmap['status'] = 0;
		}
		if($status==1 && is_numeric($status)){
			$hover[0]='';
			$hover[1]='hover';
			$sqlmap['status'] = 1;
		}
        $sqlmap['goods_id'] = $goods_id;
        $sqlmap['product_id'] = $product_id;
		if(IS_POST){

			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'DESC';
			}

			$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
			$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
			$data['total'] = $this->db->where($sqlmap)->count();	//计算总数 
			$data['rows']=$this->db
				->field(true)
				->where($sqlmap)

				->limit(($pagenum-1)*$rowsnum.','.$rowsnum)
				->order($order)
				->select();
			foreach ($data['rows'] as $key => $value) {
				$user_name = getMemberfield($value['user_id'],'username');
				$data['rows'][$key]['user_name'] = isset($user_name)?$user_name:'游客';
			}
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('goods_message_detail');
		}

	}
	
	/**
	 *数据修改 
	 */
	public function update(){
		$db = model('GoodsMessage');
		$opt = I('opt');
		$id = I('id');
		//删除
		if($opt=='del' && $id){
			 unset($where);
			$where['id'] = array('in', $id);
			$db->where($where)->delete();
			showmessage('恭喜你，删除成功！', U('lists'), 1);
			exit();
		}				
	}
	
	public function send_mail(){
		$db = model('GoodsMessage');
		$id =  $_GET['id'];

		$map['goods_id'] = $_GET['goods_id'];
		$map['product_id']= $_GET['product_id'];
		$map['status'] = 0;
		$map['_string'] ='!ISNULL(email)';
		
		if($id){
			$map['id'] = $id;
		}
		$_list = $db->where($map)->select();
		$send_num = 0;
		$err_num = 0;
		$msg_tpl = model('notify_template')->where(array('id'=>'n_goods_arrival'))->Find();
		$msg_content=json_decode($msg_tpl['template'],TRUE);
		foreach($_list as $k=>$v){
			if($v['email']){
				$user_name = getMemberfield($v['user_id'],'username');
				$user_name = $user_name ? $user_name : '游客' ;
				$subject = str_replace(array('{user}','{goods_name}'), array($user_name,$v['goods_name']), $msg_tpl['name']);
				$body = str_replace(array('{user}','{goods_name}'), array($user_name,$v['goods_name']), $msg_content['email']);
				
				$r = SendMail($v['email'],$subject,$body);				
				if($r){
					$data['status']=1;
					$db->where('id='.$v['id'])->save($data);
					$send_num++;
				}else{
					$err_num++;
				}
			}
		}
		showmessage('邮件发送成功 '.$send_num.' 邮件发送失败 '.$err_num,U('lists'),1);
	}
	public function send_letter(){
		$db = model('GoodsMessage');
		$id =  $_GET['id'];
		$map['goods_id'] = $_GET['goods_id'];
		$map['product_id']= $_GET['product_id'];
		$map['status'] = 0;
		if($id){
			$map['id'] = $id;
		}
		$_list = $db->where($map)->select();
		$send_num = 0;
		$err_num = 0;
		$letter_tpl = json_decode(model('notify_template')->where(array('id'=>'n_goods_arrival'))->getField('template'),TRUE);
		foreach($_list as $k=>$v){
			if($v['user_id']!=0){
				$user_name = getMemberfield($v['user_id'],'username');
				$user_name = $user_name ? $user_name : '游客' ; 
				$data['title'] = str_replace(array('{$user}','{$goods_name}'), array($user_name,$v['goods_name']), $letter_tpl['letter'][0]);
				$data['content'] = str_replace(array('{$user}','{$goods_name}'), array($user_name,$v['goods_name']), $letter_tpl['letter'][1]);
				$data['user_id'] = $v['user_id'];
				$data['stype'] = 2;
				$result = model('Sms')->update($data);
				if($result){
					$info['status']=1;
					$db->where('id='.$v['id'])->save($info);
					$send_num++;
				}else{
					$err_num++;
				}
            }
		}
		showmessage('站内信发送成功 '.$send_num.' 站内信发送失败 '.$err_num,U('lists'),1);
	}
}
