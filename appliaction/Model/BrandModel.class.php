<?php
class BrandModel extends SystemModel {
    //自动完成
    protected $_auto = array(
       
    );
    //自动验证
    protected $_validate = array(
        array('name','require','名称必须！'),
        //array('descript','require','描述必须！'),
        array('url','url','url格式不对！',2),
        array('sort','number','排序必须为数字！'),
    );

   public function getList($map){
   		$count = $this->where($map)->count();
        libfile('Page');
		$pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
        $page = new Page($count, $pagesize);
        $field=true;
        $join = "";
        $order="sort ASC,id DESC";

        $list['list']=$this->join($join)->field($field)->where($map)->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();

        $list['page'] = $page->show();
		
		return $list;
   }

    

}
