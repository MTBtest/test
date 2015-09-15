<?php
class ConsultingModel extends RelationModel{
	protected $_link = array(
			//订单详情
            'OrderGoods'=>array(
            'mapping_type'   =>BELONGS_TO,
            'class_name'    =>'goods',
			'foreign_key' =>'goods_id',
            'mapping_name' =>'goodsInfo',  
			'mapping_fields'=>'name,thumb',
             ),
             
            'User'=>array(
            'mapping_type'   =>BELONGS_TO,
            'class_name'    =>'User',
			'foreign_key' =>'user_id',
            'mapping_name' =>'userinfo',
            'mapping_fields'=>'user_name',
             ),
         );
	public function getList($map){
		$count = $this->where($map)->count();
        import('ORG.Util.Page');
        $Page = new Page($count,5);
        $Page->setConfig('prev','');
        $Page->setConfig('next','');
        $Page->setConfig('theme','%upPage% %linkPage% %downPage%');
        $Page->listRows = 5;
        $show = $Page->show();
		$list['list'] = $this->relation(true)->where($map)->order('sort')->limit($Page->firstRow, $Page->listRows)->select();
		$list['page'] = $show;
		return $list;
	}
}
?>