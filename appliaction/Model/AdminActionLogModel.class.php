<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class AdminActionLogModel extends SystemModel {
    protected $_auto = array (
        array('user_id', 'getAdminId', Model:: MODEL_BOTH, 'callback'),
        array('action_ip', 'get_client_ip', Model:: MODEL_BOTH, 'function'),
        array('dateline', NOW_TIME, Model:: MODEL_BOTH, 'string'),
        array('url', 'dreferer', Model:: MODEL_BOTH, 'function'),
    );

    public function write_log($msg) {
        $info = array();
        $info['module'] = strtolower(GROUP_NAME);
        $info['remark'] = $this->get_log_msg();
        if($info['remark']) parent::update($info);
        return TRUE;
    }

    public function getAdminId() {
        return session('ADMIN_ID');
    }

    private function get_log_msg() {
        $sqlmap = array();
        $sqlmap['g'] = GROUP_NAME;
        $sqlmap['m'] = MODULE_NAME;
        $sqlmap['a'] = ACTION_NAME;
        $msg = model('node')->where($sqlmap)->getField('name');
        return $msg ? $msg : '';
    }
}