<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class AdminBaseController extends BaseController {
    public function _initialize() {
        define('IN_ADMIN', TRUE);
		
		C('ADMIN_LIST',array('1')); //定义超级管理员的ID；
		C('PAGE_NUM',15); //后台每页默认显示数量；
		C('PAGE_CONFIG',array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 每页<input onkeypress="changePageSize(event,this)" class="page_view_num" name="page_view_num" value=%listRows%>%frist% %upPage% %linkPage% %downPage% %end%')); 
		
		$page_size = $_GET['pagesize'] ? $_GET['pagesize'] : C('PAGE_NUM');
		define('PAGE_SIZE',$page_size); //每页显示数量

        parent::_initialize();
        $this->admin_id = (int) session('ADMIN_ID');
        if ($this->admin_id < 1) {
            showmessage('请登录后操作', U('Admin/Public/login'));
        }
        define('ADMIN_ID', $this->admin_id);            
        libfile('Auth');
        $auth = new Auth();
        //菜单
        $pid=$_GET['pid'] ? $_GET['pid'] : 1 ;
        $this->topMenu = $topMenu = $auth->getTopMenu($pid);
        //检查权限
        if(!$auth->authCheck()){
            showmessage('你没有操作权限');
        }
        /* 记录日志 */
        if(IS_POST) {
            model('admin_action_log')->write_log();
        }
    }
}