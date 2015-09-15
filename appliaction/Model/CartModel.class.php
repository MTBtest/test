<?php
/**
 *      咨询模型
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class CartModel extends SystemModel {
    protected $_validate = array(
    );

    protected $_auto = array(
        array('dateline', NOW_TIME , Model:: MODEL_INSERT, 'string'), 
        array('clientip','get_client_ip', Model:: MODEL_INSERT, 'function'),
    );
}