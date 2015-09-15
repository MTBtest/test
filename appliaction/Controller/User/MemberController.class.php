<?php
/**
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class MemberController extends AdminBaseController {
	/**
	 * 自动执行
	 */
	public function _initialize() {
		parent::_initialize(); 
		$this->db = model('user');
	}
	/**
	 * 会员列表
	 */

   
	public function lists(){
		if(IS_POST){
			$sqlmap = array();
			$field = 'id,username,group_id,mobile_phone,email,pay_points,user_money,exp,reg_time';
			//排序
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'DESC';
			}
			//筛选
			if(isset($_GET['group_id']) && $_GET['group_id']){//分类
				$sqlmap['group_id']= $_GET['group_id'];
			}
			if(isset($_GET['keyword']) && $_GET['keyword']){//分类
				$keyword = $_GET['keyword'];
				$sqlmap['username|email|mobile_phone'] = array("LIKE", "%".$keyword."%");
			}
			//分页
			$pagenum=isset($_GET['page']) ? intval($_GET['page']) : 1;
			$rowsnum=isset($_GET['rows']) && (int)($_GET['rows']) != 0 ? intval($_GET['rows']) : PAGE_SIZE;
			//计算总数 
			$data['total'] = $this->db->where($sqlmap)->count();	
			$data['group'] = M('UserGroup')->field('id,name')->order('sort ASC, id ASC')->select();
			$data['rows']=$this->db->field($field)->where($sqlmap)->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			if (!$data['group']) $data['group']=array();
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('member_list');
		}
	}
	
	/**
	 * 更新会员
	 */
	public function update(){
		$id=$_POST['id'];
		$dialog = TRUE;
		$_POST['birthday']=  strtotime($_POST['birthday']);

		if($id){
			if($this->db->create()){
				$this->db->save();
			}else{
				showmessage($this->db->getError());
			}  
			 
		}else{
			 $_POST['reg_time']=  time();
		  
			if($this->db->create()){
				$id=$this->db->add();
			}else{
			   showmessage($this->db->getError());
			} 
		}
		if($id){
			showmessage('提交成功',U('Member/lists'), 1);
		}else{
			showmessage('提交成功',U('Member/add'), 1); 
		}
	}
	/**
	 * 添加会员
	 */
	public function add(){
		$validform = TRUE;
		$dialog = TRUE;
		if(IS_POST){
			$_POST['valid'] = random(10);
			$_POST['password'] = md5($_POST['valid'].'123456');
			self::update();
		}else{
			$group = model('UserGroup')->where(array('status'=> 1))->order('sort ASC')->select();
			include $this->admin_tpl('member_add');
		}
	} 
	/**
	 * 编辑会员
	 */
	public function edit(){
		$validform = TRUE;
		$dialog = TRUE;
		$id = I('id', 0, 'intval');
		if(IS_POST){
			self::update();
		}else{
			$validform = '';
			$dialog = '';
			$info = $this->db->getById($id);
			$group = model('UserGroup')->where(array('status'=> 1))->order('sort ASC')->select();
			include $this->admin_tpl('member_add');
		}
	} 
	/**
	 * 删除会员
	 */
	public function delete(){
		$id = (array) $_GET['id'];
		if(empty($id)) showmessage('参数错误');
		$sqlmap = $oauth_map = array();
		$sqlmap['id'] = $oauth_map['user_id'] = array("IN", $id);
		$this->db->where($sqlmap)->delete();
		model('user_oauth')->where($oauth_map)->delete();
		showmessage('数据删除成功', U('lists'), 1);
	}
	/**
	 * 重设密码
	 */
	public function repassword(){
		$id=$_GET['id'];
		if(!empty($id)){
			unset($where);
			$where['id']=$id;
			$data['valid'] = random(10);
			$data['password'] = md5($data['valid'].'123456');
			$this->db->where($where)->save($data);
			//邮件通知
			$info=$this->db->where($where)->find();
			$mail=$info['email'];
			if($mail){
				//读取模板内容
				$msg_info = M('MsgTemplate')->find(3);
				$title	= $msg_info['title'];
				$content  = $msg_info['content'];				
				//替换模板内容
				$subject = str_replace(array('{$user_name}','{$password}','{$site_name}'),array($info['username'],'123456',C('site_name')),$content);
				$body	= str_replace(array('{$user_name}','{$password}','{$site_name}'),array($info['username'],'123456',C('site_name')),$content);
				SendMail($mail,$subject,$body);
			}
			showmessage('会员密码已设为123456', U('Member/lists'), 1); 
		}else{
			showmessage('非法操作，请联系管理员！'); 
		}
	}

	/**
	 * 余额变动
	 */
	public function setmoney(){
		if(IS_POST){
			$user_money = (float)trim($_GET['user_money']);
			if($_GET['at']=='setDec') $user_money = -$user_money;
			$msg = trim($_GET['msg']);
			$user_id = intval($_GET['user_id']);
			if (!$user_money) showmessage('余额必须是不等于零的数字！');
			if (empty($msg)) showmessage('原因必须填写！');
			$data['user_id']=$user_id;
			$data['money']=$user_money;
			$data['msg']='后台余额变更;原因:'.$msg;
			$data['dateline']=NOW_TIME;
			//增加记录
			$ret = model('user_moneylog')->add($data);
			if (!$ret) showmessage('余额变更失败！');
			//改变帐户余额
			$reuslt = $this->db->where(array('id' => $user_id))->setInc('user_money',$user_money);
			if (!$reuslt) {
				model('user_moneylog')->delete($ret);
				showmessage('余额变更失败！');
			}
			/* 通知推送 */
			runhook('n_money_change',array('user_id' => $user_id,'log_id' => $ret));
			showmessage('余额变更成功', U('Member/lists'), 1);
		}else{
			showmessage('非法操作，请联系管理员！');
		}
	}

	/**
	 * 积分变动
	 */
	public function setpoints(){
		if($_POST){
			$pay_points=I('exp',0,'intval');
			if($_POST['at']=='setDec') $pay_points=-$pay_points;
			$descript=$_POST['descript'];
			$user_id=intval($_POST['user_id']);
			
			if ((int)$_POST['exp']==0) showmessage('积分必须是整数！');
			if (empty($descript)) showmessage('原因必须填写！');  
			$data['user_id']=$user_id;
			$data['descript']=$descript;
			$data['pay_points']=$pay_points;
			$data['time']=TIME();
			//增加记录
			$points=model('User_pointslog');
			$points->add($data);
			//改变帐户积分
			unset ($where);
			unset ($data);
			$where['id']=$user_id;
			$data['pay_points']=array('exp','pay_points + '.$pay_points);
			$this->db->where($where)->save($data);
			showmessage('积分变动成功', U('Member/lists'), 1);	 
		}else{
			showmessage('非法操作，请联系管理员！'); 
		}
	}   
	
	/**
	 * 经验变动
	 */
	public function setexp(){
		if($_POST){
			$expval=I('exp',0,'intval');
			$at = $_GET['at'];
			$user_id = $_GET['user_id'];
			if ($expval==0) showmessage('经验必须是整数！');
			if ($at == 'setDec') $expval = 0-$expval;
			action_exp($user_id,$expval);
			showmessage('经验变动成功', U('Member/lists'), 1);	 
		}else{
			showmessage('非法操作，请联系管理员！'); 
		}
	} 
		
}

