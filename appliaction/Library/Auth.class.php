<?php
class Auth {
    /**
     * 获取当前用户分组2014-10-30 10:57:29
     */
    public function getGroupID() {
        $r = D("AdminUser")->where('id='.ADMIN_ID)->getField('group_id');
        return $r;
    }

    public function getAuthList() {
        //获取会员分组
        $groupId = $this->getGroupId();
        //获取会员相应权限
        $rulesIds = D("AdminAuthGroup")->where('id=' . $groupId)->getField('rules');
		$sqlmap = array();
		//超级管理员
		if (in_array(session('ADMIN_ID'), C('ADMIN_LIST')) || in_array($rule, C('NOT_AUTH_ACTION'))) {
	        $sqlmap = '1=1';
	    }else{
	    	$sqlmap = array('id' => array('in', $rulesIds));
	    }
        $m = D("Node")->where($sqlmap)->order('sort ASC,id Asc')->select();
        return $m;
    }

    /**
     * 获取顶部菜单 2014-10-30 13:36:31
     * @return type
     */
    public function getTopMenu($pid = 0) {
        $m = $this->getAuthList();
        $pid = $pid ? $pid : 1;
        foreach ($m as $k => $v) {
        	            
            //组合主菜单
            if ($v['parentid'] == 0) {
                $r['topmenu'][$k] = $v;
                $r['topmenu'][$k]['url'] = U("Admin/Index/index?pid={$v['id']}");
                //处理当前选中样式
                if ($pid == $v['id'])
                    $r['topmenu'][$k]['css'] = "hover";
            }
            //组合子菜单
            if ($v['parentid'] == $pid && $v['status'] == 1) {
                $r['submenu'][$k] = $v;
                $r['submenu'][$k]['url'] = (!empty($v['pluginid']) && $v['url']) ? $v['url'] : U("{$v['g']}/{$v['m']}/{$v['a']}?{$v['data']}");
                //处理当前选中样式
                if (MODULE_NAME == $v['m'] && ACTION_NAME == $v['a'])
                    $r['submenu'][$k]['css'] = "hover";
            }
        }
        return $r;
        
    }

    /**
     * 检查权限2014-10-30 15:59:02
     * @return type
     */
    public function authCheck() {
        $map['g'] = GROUP_NAME;
        $map['m'] = MODULE_NAME;
        $map['a'] = ACTION_NAME;
		//超级管理员
		if (in_array(session('ADMIN_ID'), getconfig('ADMIN_LIST')) || in_array($rule, getconfig('NOT_AUTH_ACTION'))) {
	        return true;    //如果是，则直接返回真值，不需要进行权限验证
	    } 
        //获取会员分组
        $groupId = $this->getGroupId();
        //获取会员相应权限
        $rulesIds = D("AdminAuthGroup")->where('id=' . $groupId)->getField('rules');
        //获取当前操作规则id
        $selRulesId = D("Node")->where($map)->getField('id');

        $rulesIdsArr = str2arr($rulesIds);
        $r = !$selRulesId || in_array($selRulesId, $rulesIdsArr);
        return $r;
    }

}
