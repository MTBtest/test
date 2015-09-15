<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class MobileController extends AdminBaseController {
    public function _initialize() {
        parent::_initialize();
    }
    
    public function setting() {
        $pays = getcache('payment', 'pay');
        include $this->admin_tpl('mobile_setting');
    }
}
