<?php 

class GoodsCategoryModel extends RelationModel
{
	protected $_validate  = array();
	protected $_auto = array();

	protected $_link = array(
        //属性
        'attr' => array(
            'mapping_type' => HAS_MANY,
            'class_name' => 'attribute',
            'foreign_key' => 'model_id',
            'mapping_name' => 'attrinfo',
            'condition' => 'ISNULL(spec_ids)',
        ),
        //规格
        'spec' => array(
            'mapping_type' => HAS_MANY,
            'class_name' => 'attribute',
            'foreign_key' => 'model_id',
            'mapping_name' => 'spec_ids',
            'condition' => '!ISNULL(spec_ids)',
            'mapping_fields' => 'spec_ids',
            'as_fields' => 'spec_ids:spec_ids',
        ),
    );
	public function getInfo($map){
		$info=$this->where($map)->find();
		$mdb = D('Admin/Model');
		$where['_string'] = "FIND_IN_SET('{$info['id']}',cat_ids)";
		$modelIds = $mdb->where($where)->getField('id',TRUE);
		//取模型
		foreach ($modelIds as $k=>$v){
			$tw['id'] = $v;
			$_temp[$k] = $mdb->getList($tw);
			$_temp[$k] = current($_temp[$k]['list']);
		}
		$info = $_temp ;
		return $info;
	}
}