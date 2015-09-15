<?php
class notice{
	public function lists($data){
		$announcModel = M("Announcement");
		$nowtime = time();
		$map = array(
			'status' => 1,
			//'starttime' => array('ELT',$nowtime),
			//'endtime' => array('EGT',$nowtime),
			);
		$order = "`sort` desc,`id` desc";
		if(isset($data['order'])){
			$order = $data['order'];
		}
		$limit = $data['limit'] ? $data['limit'] : 3;
		return $announcModel->where($map)->order($order)->limit($limit)->select();
	}
}