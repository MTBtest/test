<?php
class Auth_managerModel extends Model {
    protected $tableName = 'admin_auth_group'; 

    protected $_validate = array(
        array('title','require', '必须设置用户组标题', Model::MUST_VALIDATE ,'regex',Model::MODEL_INSERT),
        array('description','0,80', '描述最多80字符', Model::VALUE_VALIDATE , 'length'  ,Model::MODEL_BOTH ),
    );

    public function lists($status = 1, $order = 'id DESC', $field = true){
        //$map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

}

