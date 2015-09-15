<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class ApiBaseController extends BaseController {
    public function _initialize() {
        parent::_initialize();
    }
    
    /**
     * 默认定位
     */
    public function _empty() {
        $table = parse_name(ACTION_NAME);
        $model = model($table);
        $method = (empty($_GET['method'])) ? 'lists' : $_GET['method'];
        $result = $model->$method($_GET);
        if(IS_AJAX) {
            $this->ajaxReturn($result);
        } else{
            echo $result;
        }
    }
}
