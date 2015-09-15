<?php
class AnnouncementModel extends Model{
	
	/**
	 * 
	 * 获取公告列表
	 * @param  $map 条件
	 * @author wj
	 * 
	 */
	public function getList($map){
		$count = $this->where($map)->count();
        libfile('Page');
        $Page = new Page($count,5);
        $Page->setConfig('prev','');
        $Page->setConfig('next','');
        $Page->setConfig('theme','%upPage% %linkPage% %downPage%');
        $Page->listRows = 5;
        $list1 = $this->where($map)->order('sort')->limit($Page->firstRow, $Page->listRows)->select();
        $list['list'] = $list1;
        $show = $Page->show();
        $list['page'] = $show;
        return $list;
	}
}
?>