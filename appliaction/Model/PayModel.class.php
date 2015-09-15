<?php
/**
 *      咨询模型
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class PayModel extends Model {
    protected $_validate = array(
        array('trade_sn','require','订单号不能为空', Model::EXISTS_VALIDATE, 'regex', Model:: MODEL_INSERT), 
        array('goods_id','require','咨询商品不能为空！', Model::EXISTS_VALIDATE, 'regex', Model:: MODEL_BOTH), 
    );

    protected $_auto = array(
        array('status', '0', 'string', Model:: MODEL_INSERT), 
        array('dateline', NOW_TIME, 'string', Model:: MODEL_INSERT),
    );


    public function update($data, $iscreate = TRUE) {       
        if ($iscreate == TRUE) $data = $this->create($data);
        if (empty($data)) {
            $this->error = $this->getError();
            return false;
        }
        if (isset($data['id']) && is_numeric($data['id'])) {
            $result = $this->save($data);
            if (!$result) {
                $this->error = '更新数据失败';
                return false;
            }
        } else {
            $result = $this->add($data);
            if ($result === false) {
                $this->error = '添加数据失败';
                return false;
            }
        }
        return $result;
    }
}