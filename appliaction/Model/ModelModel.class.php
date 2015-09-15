<?php

class ModelModel extends RelationModel {

    protected $tableName = 'model'; 
    //自动完成
    protected $_auto = array(

    );
    //自动验证email, mobile_phone, user_name
    protected $_validate = array(
        array('name','require','标题必须！'),
       
    );
    protected $_link = array(
        //属性
        'attr' => array(
            'mapping_type' => HAS_MANY,
            'class_name' => 'attribute',
            'foreign_key' => 'model_id',
            'mapping_name' => 'attrinfo',
            'condition' => 'ISNULL(spec_ids)',
            'mapping_order'=>'sort asc',
            //'mapping_fields' => ''
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
	
	/**
     * 获取列表数据
     * @param type $map
     * @return type array
     */
    public function getList($map) {
        $count = $this->where($map)->count();
        libfile('Page');
        $pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
        $Page = new Page($count, $pagesize);
        $result = $this->relation(TRUE)->where($map)->order('sort ASC,id DESC')->limit($Page->firstRow, $Page->listRows)->select();
		//取分类信息
		foreach ($result as $k => $v) {
			$result[$k]['category'] = M('GoodsCategory')->where(array('id'=>array('in',$v['cat_ids'])))->getField('id,name',TRUE);
			$result[$k]['spec'] = M('spec')->where(array('id'=>array('in',$v['spec_ids'][0]['spec_ids'])))->select();
		}
		$category = M('GoodsCategory')->where(array('id'=>array('in',$cat_ids)))->getField('id,name',TRUE);
        $list['list'] = $result;
        $show = $Page->show();
        $list['page'] = $show;
        return $list;
    }
	
	/**
	 * 添加入库
	 */
	public function addinfo(){
			
		//获取新增模型数据
		$model_name = I('model_name');
		$category_ids = I('category_id');
		
		//获取新增规格数据
		$newAttrSpecId = I('spec_id');
		$newAttrSpecId = arr2str($newAttrSpecId);

		//获取新增属性数据
		$newAttrName = I('newAttrName');
		$newAttrValue = I('newAttrValue');
		$newAttrType = I('newAttrType');
		$newAttrSort = I('newAttrSort');
		$newAttrSearch = I('newAttrSearch');
		$newAttrSearch = str2arr($newAttrSearch);

		//新增入库
		if ($this -> check($model_name, 'require')) {
			//添加模型表
			$indata['name'] = $model_name;
			$indata['cat_ids'] = arr2str($category_ids);
			$model_id = $this -> add($indata);

			//添加属性表属性
			unset($indata);
			foreach ($newAttrName as $k => $v) {
				$indata[$k]['model_id'] = $model_id;
				$indata[$k]['name'] = $newAttrName[$k];
				$indata[$k]['value'] = $newAttrValue[$k];
				$indata[$k]['spec_ids'] = NULL;
				$indata[$k]['type'] = $newAttrType[$k];
				$indata[$k]['search'] = $newAttrSearch[$k];
				$indata[$k]['sort'] = $newAttrSort[$k];

			}
			$newAttrName = array_filter($newAttrName);
			//添加属性表规格
			if($newAttrSpecId){
				$indata[count($newAttrName)]['model_id'] = $model_id;
				$indata[count($newAttrName)]['name'] = NULL;
				$indata[count($newAttrName)]['value'] = NULL;
				$indata[count($newAttrName)]['spec_ids'] = $newAttrSpecId;
				$indata[count($newAttrName)]['type'] = 1;
				$indata[count($newAttrName)]['search'] = 1;
				$indata[count($newAttrName)]['sort'] = 100;
			}
			
			
			
			if (isset($indata) && $indata) {
				if (! M('attribute') -> addAll($indata)) {
					$err .= $this -> getError();
				}
			}
			return TURE;

		} else {
			return FALSE;
		}
	}

	/**
	 * 保存入库
	 */
	public function saveinfo(){
		$id = I('id');
		
		//获取类型编辑数据
		$model['name'] = I('model_name');
		
		$model['cat_ids'] = I('category_id');
		$model['cat_ids'] = arr2str(I('category_id'));
		
		//获取属性编辑数据
		$AttrId = I('AttrId');
		$AttrName = I('AttrName');
		$AttrValue = I('AttrValue');
		$AttrType = I('AttrType');
		$AttrSearch = I('AttrSearch');
		$AttrSearch = str2arr($AttrSearch);
		$AttrSort = I('AttrSort');
		
				
		//获取新增属性数据
		$newAttrName = I('newAttrName');
		$newAttrValue = I('newAttrValue');
		$newAttrType = I('newAttrType');
		$newAttrSort = I('newAttrSort');
		$newAttrSearch = I('newAttrSearch');
		$newAttrSearch = str2arr($newAttrSearch);
		
		//获取删除属性数据
		$delAttrId = I('AttrDelId');
		$delAttrId = arr2str($delAttrId);
		
		//获取规格数据
		$AttrSpecId = I('spec_id');
		$AttrSpecId = arr2str($AttrSpecId);
		
		//修改入库
		$this->where('id='.$id)->save($model);
		
		$attrdb = D('Attribute');
		foreach ($AttrId as $k => $v) {
			$uattr['id'] = $AttrId[$k];
			$uattr['model_id'] = $id;
			$uattr['name'] = $AttrName[$k];
			$uattr['value'] = $AttrValue[$k];
			$uattr['spec_ids'] = NULL;
			$uattr['type'] = $AttrType[$k];
			$uattr['search'] = $AttrSearch[$k];
			$uattr['sort'] = $AttrSort[$k];

			$attrdb->save($uattr);
		}
		
		//新增入库
		foreach ($newAttrName as $k => $v) {
			$iattr[$k]['model_id'] = $id;
			$iattr[$k]['name'] = $newAttrName[$k];
			$iattr[$k]['value'] = $newAttrValue[$k];
			$iattr[$k]['spec_ids'] = NULL;
			$iattr[$k]['type'] = $newAttrType[$k];
			$iattr[$k]['search'] = $newAttrSearch[$k];
			$iattr[$k]['sort'] = $newAttrSort[$k];

		}
		$attrdb -> addAll($iattr) ;
		
		//删除操作
		$where['id'] = array('in', $delAttrId);
		$attrdb -> where($where)->delete();
		
		//规格入库
		$data['spec_ids'] = $AttrSpecId;
		$map['id'] = $attrdb->where('!ISNULL(spec_ids) and model_id='.$id)->getField('id');
		if(($map['id'])){
			$attrdb->where($map)->save($data);
		}else{
			$data['model_id'] = $id;
			if($data['spec_ids']){
				$attrdb->add($data);
			}
		}
		
		return TRUE;
	}

	/**
	 * 根据多分类获取对应模型
	 */
	public function getModelInCat(){
		$cat_ids = $_GET['cat_ids'];
		$cat_ids_arr = str2arr($cat_ids);
		
		foreach ($cat_ids_arr as $k => $v) {
			$where['_string'] = "FIND_IN_SET('{$v}',cat_ids)";
			$_temp[] = $this->where($where)->getField('id,name,cat_ids',true);
		}
		$_temp = array_filter($_temp);
		//组织数据
		foreach ($_temp as $k => $v) {
			foreach($v as $k1 =>$v1){
				$info[$v1['id']] = $v1;
			}
		}
		return $info;
	}
	

	/**
	 * 根据模型ID获取对应属性及规格
	 */
	public function getAttrInModel(){
		$id = $_GET['id'];
		
		$map['id'] = $id;
		$info = $this->getList($map);
		$info = $info['list'][0];
		//组织属性及规格
		$r['name'] = $info['name'];
		$r['attrinfo'] = $info['attrinfo'];
		//$r['specinfo'] = $info['spec'];
		return $r;
	}
	
		
}
