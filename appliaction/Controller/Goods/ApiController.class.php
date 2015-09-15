<?php
/**
 *      会员中心 - 我的收藏夹
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class ApiController extends ApiBaseController {

    public function _initialize() {
        parent::_initialize();
    }

    /**
    * 
    * 获取商品列表[wap]
    * @param  
    * @author 老孔
    * @date 2015-04-27
    */
    public function goods_list() {
		$result = model('goods')->get_lists($_GET);
		exit(json_encode($result));
    }

}