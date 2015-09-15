<?php 
class PushMailController extends AdminBaseController{
		public function _initialize() {
        parent::_initialize();
		//分类列表
        $cate = model('Category');
        $data = $cate->where('status = 1')->order('parent_id ASC,sort ASC,id ASC')->select();
        $this->tree = $info = getTree($data,0);

        $this->treeMenu = $cate->formatCat($info);
        //品牌列表
        $brandModel = model('Brand');
        $this->brand = $brandModel->where('status = 1')->order('sort ASC')->select();
    }

    /**
     * 列表处理
     */
    public function lists() {
		$validform='';
		$dialog = '';
		if (IS_POST) {
            $user_ids = $_POST['member_id'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            if (isset($user_ids) && isset($title) && isset($content)) {
                self::push_mail($user_ids, $title, $content);
            } else {
               showmessage('参数错误，请联系管理员');
            }
		} else { 
            //会员列表
            $user_group = M('UserGroup')->select();

			include $this->admin_tpl('push_mail');
		}
    }
	
	/**
	 * 按条件搜索会员
	 */
	public function search_user(){
		$opt = $_GET['opt'];
		$group_id =$_GET['group_id'];
        if (IS_GET && $opt) {
            if (isset($opt) && $opt == 'search') {
                $userDB=model('User');
                $info=$userDB->push_userids($_GET);
                	if($info){               
	                    if (($info['id']) ) {
	                        $_info = $info;
	                    }else{
	                    	$_info['ids'] = arr2str($info);
	                    	$_info['count'] = count($info);
	                    }
                        
                        $this->ajaxReturn($_info); 
                    }else{
                         $_info=array('status'=>0);
                         $this->ajaxReturn($_info); 
                    }                    
                }
        } 
	}

    //发送
    private function push_mail($user_ids, $title, $content) {

        $user_list = model('User')->where("id in ($user_ids) AND !ISNULL(email)")->getField('id,username,email',TRUE);
		$data['title'] = $title;
		$data['content'] = $content;
		
		if(!$user_list) showmessage('没有找到会员邮件地址!');
		$send_num = 0;
		$err_num = 0;
		
        foreach ($user_list as $k => $v) {

			$user_name = $v['username'];
			
			$subject = str_replace(array('{$user_name}'), array($user_name), $title);
			$body = str_replace(array('{$user_name}'), array($user_name), $content);
			
			$r = SendMail($v['email'],$subject,$body);
			
			if($r){
				$send_num++;
			}else{
				$err_num++;
			}
		}

        showmessage('邮件发送成功 '.$send_num.' 邮件发送失败 '.$err_num,U('PushMail/lists'),1);
    }
}