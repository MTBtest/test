<?php

class PushCouponsController extends AdminBaseController {

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
     * 优惠券处理
     */
    public function lists() {
		$validform='';
		$dialog = '';
		if (IS_POST) {
            $member_id = $_POST['member_id'];
            $push_num = $_POST['push_num'];
            $coupons_id = $_POST['coupons_id'];

            if (isset($member_id) && isset($push_num) && isset($coupons_id)) {
                self::push_coupons($member_id, $push_num, $coupons_id);
            } else {
               showmessage('参数错误，请联系管理员');
            }
		} else {
             //优惠券列表
            $field = "id,name";
            $coupons = model('Coupons')->field($field)->order('id DESC')->select();
            foreach ($coupons as $k => $v) {
                $coupons[$k]['num'] = getCouponsCount($v['id'], 0);
            }
            $coupons_list = $coupons;

            //会员列表
            $user_group = M('UserGroup')->select();

			include $this->admin_tpl('push_coupons');
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

    //发送优惠券
    private function push_coupons($member_id, $push_num, $coupons_id) {
        $member_id_arr = str2arr($member_id);
        //数量判断
        $push_num_count = count($member_id_arr) * $push_num;
		$map['cid'] = $coupons_id;
		$map['status'] = 0;
        $coupons_list = model('CouponsList')->where($map)->getField('id',true);
		$coupons_count = count($coupons_list);
        if ($coupons_count < $push_num) {
            showmessage("发放失败，优惠券数量不够发放！");
            exit();
        }
		$coupons_key = 0;

        foreach ($member_id_arr as $k => $v) {
            $user_id = $v;
            $user_name = getMemberfield($v['user_id'],'username');
		
            //发送代金券
            for ($i=0; $i < $push_num ; $i++) { 
            	$where['id'] = current($coupons_list);
	            $where['status'] = 0;
	            $where['cid'] = $coupons_id;
	            $data['user_id'] = $user_id;
	            $data['user_name'] = $user_name;
	            $data['to_time'] = time();
	            $data['status'] = 1;
	            model('CouponsList')->where($where)->save($data);
				unset($coupons_list[key($coupons_list)]);
				$coupons_key++;
            }
			runhook('push_coupons_success');
        }
        showmessage("发放优惠券成功！");
    }

}
