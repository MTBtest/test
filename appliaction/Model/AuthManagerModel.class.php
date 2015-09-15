<?php
class AuthManagerModel extends Model {
    protected $tableName = 'admin_auth_group'; 

    protected $_validate = array(
        array('title','require', '必须设置用户组标题', Model::MUST_VALIDATE ,'regex',Model::MODEL_INSERT),
        array('description','0,80', '描述最多80字符', Model::VALUE_VALIDATE , 'length'  ,Model::MODEL_BOTH ),
    );

    public function lists($status = 1, $order = 'id DESC', $field = true){

        //$map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

     public function get_byId($group_id){
	 	if ($group_id) {

	 		  $auth_group = $this->where( array('status'=>array('egt','0'),'module'=>'admin','id'=>$group_id) ) ->getfield('id,id,title,rules');

	 		  return $auth_group[$group_id];

	 	}
      
	}


	public function getList(){
		 $model=M('node');
		 $map         = array('module'=>'admin','status'=>1);
		 $list = $model->where($map)->order('id ASC')->Field(true)->select();
	   

         $map         = array('module'=>'admin','type'=>1,'status'=>1);
         $child_rules = $model->where($map)->order('sort ASC')->getField('name,id');
         $result = array(
         	'list' =>$list , 
         	'child_rules'=>$child_rules,

         	);

         return $result;
	}

}

