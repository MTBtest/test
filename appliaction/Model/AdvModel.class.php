<?php

class AdvModel extends RelationModel {

    protected $tableName = 'adv'; 
    //自动完成
    protected $_auto = array(
        
    );
    //自动验证email, mobile_phone, user_name
    protected $_validate = array(
        array('title','require','标题必须！'),
        array('sort','number','排序必须是数字！'),
        array('starttime,endtime', 'checkDay', '结束日期不能大于开始日期', 1,'callback', 3),
        array('content','require','内容不能为空！'),
       
    );
    protected $_link = array(
        //用户分类名称
        'userGroup' => array(
            'mapping_type' => BELONGS_TO,
            'class_name' => 'advPosition',
            'foreign_key' => 'position_id',
            'mapping_name' => 'positioninfo',
            'as_fields' => 'name:position_name',
        ),
    );
	
	/**
     * 获取列表数据
     * @param type $map
     * @return type array
     */
    public function getList($map) {
        $count = $this->where($map)->count();
        libfile('Page');
        $pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
        $Page = new Page($count, $pagesize);
        $Page->listRows = $pagesize;
        $result = $this->relation(true)->where($map)->order('sort ASC,id ASC')->limit($Page->firstRow, $Page->listRows)->select();
        $list['list'] = $result;
        $show = $Page->show();
        $list['page'] = $show;
        return $list;
    }
	//开始结束时间
	protected function checkDay($data){
	    if($data['starttime'] > $data['endtime'])
	        return false;
	    else
	        return true;
	}
}
