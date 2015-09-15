<?php
/**
 * 
 * 地区模型
 * @author wj
 * @date 2014-10-14
 *
 */
class RegionModel extends RelationModel{
	protected $_link = array(
			
            'Region'=>array(
            'mapping_type'   =>HAS_MANY,
            'class_name'    =>'Region',
			'parent_key' =>'parent_id',
            'mapping_name' =>'son',  
             ),
         );

	/**
	 * 
	 * 获取一级地域
	 * @author wj
 	 * @date 2014-10-14
	 */
	public function getAllP(){
		 $list = $this->where(array('parent_id'=>1))->select();
		 return $list;
	}
	
}
?>