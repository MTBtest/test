<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 * 
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class UserMoneylogModel extends SystemModel {

    public function lists() {
    	$sqlmap = array();
    	if ($_GET['keyword']) {
    		$map = array();
    		$map['username'] = array('LIKE',"%".trim($_GET['keyword'])."%");
    		$ids = model('user')->where($map)->getField('id',TRUE);
    		$sqlmap['user_id'] = array('IN',$ids);
    	}
    	$infos = $this->where($sqlmap)->order('id DESC')->limit(($_GET['pagenum']-1)*$_GET['rowsnum'].','.$_GET['rowsnum'])->select();
    	foreach ($infos as $k => $info) {
    		$info['username'] = get_nickname($info['user_id']);
    		$infos[$k] = $info;
    	}
    	return $infos;
    }

}