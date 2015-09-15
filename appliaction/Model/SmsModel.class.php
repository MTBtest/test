<?php
/**
 * 
 * 站内信
 * @author wj
 * @date 2014-10-16
 *
 */
class SmsModel extends  Model{
	protected $_validate = array(
        
    );
    protected $_auto = array(
        array('time', NOW_TIME, Model:: MODEL_BOTH, 'string'),
    );
    
    public function update($data) {
        $data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
        $sqlmap = array();
        $sqlmap['id'] = $data['id'];
        if($this->where($sqlmap)->count()) {
            $result = $this->save($data);
        } else {
            $result = $this->add($data);
        }
        if(!$result) {
            $this->error('数据更新失败');
            return FALSE;
        }
        return TRUE;
    }
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
		$list['list'] = $this->where($map)->order('id desc')->limit($Page->firstRow, $Page->listRows)->select();
		
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