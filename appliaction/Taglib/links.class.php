<?php
class links{
	public function lists($data){
		$linksModel = M('FriendLink');
		$map = array(
			'status' => 1,
			);
		$order = "`sort` desc,`id` desc";
		if(isset($data['order'])){
			$order = $data['order'];
		}
		if (!empty($data['limit'])) {
			$linksModel->limit($data['limit']);
		}
		return $linksModel->where($map)->order($order)->select();
	}
}