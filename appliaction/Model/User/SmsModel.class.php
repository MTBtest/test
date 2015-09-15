<?php
/**
 * 
 * 站内信
 * @author wj
 * @date 2014-10-16
 *
 */
class SmsModel extends  Model{
	
	/**
	 * 
	 * 获取对应数据
	 * @param  $map 条件
	 * @param  $pageStart 分页开始
	 * @param  $pageLimit 每页显示条数
	 * @author wj
 	* @date 2014-10-16
	 */
	public function getList($map){
		
   		$count = $this->getCount($map);
        libfile('Page');
        $Page = new Page($count);
        $Page->setConfig('prev','');
        $Page->setConfig('next','');
        $Page->setConfig('theme','%upPage% %linkPage% %downPage%');
        $Page->listRows = 5;
        $show = $Page->show();
		$list['list'] = $this->where($map)->order('id DESC')->limit($Page->firstRow, $Page->listRows)->select();
		
		$list['page'] = $show;
		return $list;
	}
	
	/**
	 * 
	 * 获取条数
	 * @param  $map 条件
	 * @author wj
 	 * @date 2014-10-16
	 */
	public function getCount($map){
		return $this->where($map)->count();
	}
	
	public function read($map){
		$data['status'] = 1;
		return $this->where($map)->save($data);
	}
}
?>