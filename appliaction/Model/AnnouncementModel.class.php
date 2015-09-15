<?php

class AnnouncementModel extends Model {

    protected $tableName = 'announcement';
    //自动完成
    protected $_auto = array(
        array('create_time', 'time', 3, 'function'), //新增数据是插入注册时间
    );
    //自动验证email, mobile_phone, user_name
    protected $_validate = array(
        array('title', 'require', '标题必须！'),
        array('sort', 'number', '排序必须是数字！'),
        array('content', 'require', '内容不能为空！'),
    );

    /**
     * 获取列表数据2014-10-31 11:03:02
     * @param type $map
     * @return type array
     */
    public function getList($map) {
        $count = $this->where($map)->count();
        libfile('Page');
        $Page = new Page($count, PAGE_SIZE);
        $result = $this->where($map)->order('sort ASC,id ASC')->page(PAGE, PAGE_SIZE)->select();
        $list['list'] = $result;
        $list['page'] = $Page->show();
        return $list;
    }

}
