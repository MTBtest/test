<?php

class CategoryModel extends Model {

    protected $tableName = 'goods_category';
    //自动验证
    protected $_validate = array(
        array('name', 'require', '名称必须！'),
        array('parent_id', 'require', '主分类必须！', 1, '', 3),
        array('sort', 'number', '排序值必是数字！', 1, '', 3), // 在新增的时候验证name字段是否唯一    
    );

    /**
     * 获取指定分类2014-10-28 16:17:50
     * @param type $parent_id 父类ID 
     * @param type $order
     * @param type $field
     * @return type
     */
    public function lists($parent_id = 0, $order = 'sort ASC', $field = true) {
        $map = array('parent_id' => $parent_id);
        return $this->field($field)->where($map)->order($order)->select();
    }

    /**
     *  根据ID获取分类信息
     */
    public function getInfoByIds($ids){
        if(!is_array($ids)){
            $ids = explode(',', $ids);
        }
        return $this->where(array('id'=>array('in',$ids)))->select();
    }

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array          分类树
     */
    public function getTree($id = 0, $field = true) {
        /* 获取所有分类 */
        $map = array('status' => array('GT', -1));
        $list = $this->field($field)->where($map)->order('sort ASC,id ASC')->select();
        $list = list_to_tree($list, $pk = 'id', $pid = 'parent_id', $child = '_', $root = $id);

        /* 获取返回数据 */
        if (isset($info)) { //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }

        return $info;
    }

    /**
     * 更新单字段 用于AJAX
     * @param type $val
     * @param type $key
     * @return boolean
     */
    public function updateKey($map, $data) {
        if ($this->where($map)->save($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除数据
     * @param type $key 主建
     * @param type $val 可传多ID用,分隔
     * @return boolean
     */
    public function delKey($key, $val) {
        $map[$key] = array('exp', 'in(' . $val . ')');
        if ($this->where($map)->delete()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 格式化分类到选择框2014-10-28 18:22:01
     * @param type $data
     */
    public function formatCat($data) {
        foreach ($data as $key => $value) {

            $level = $value['level'];
            $tnum1 = $data[$key + 1]['level'];
            $_t = "├";

            if ($tnum1 == 0 || $level != $tnum1 && $level != 0) {
                $_t = "└";
            }

            unset($_n);
            for ($i = 0; $i < $level; $i++) {
                $_n.="│";
            }

            $result[$key]['text'] = $_n . $_t . $value['name'];
            $result[$key]['value'] = $value['id'];
            
        }
        return $result;
    }

    /**
     * 根据父分类获取所有子分类
     */
    public function getChild($cid){
        $return = array();
        $ids = M('GoodsCategory')->where(array('parent_id'=>$cid))->getField('id',TRUE);
        $return = $ids;
        if(is_array($ids)){
            foreach ($ids as $key => $value) {
                $child = $this->getChild($value);
                if(!empty($child)){
                    $return = array_merge($return,$child);
                }
            }
        }
        return $return;
    }

    /* 根据分类ID获取顶级ID */
    public function getParent($cid = '', $istop = 1) {
        if ($istop ==  1) {
            $result = 0;
            $category = $this->getbyId($cid);
            $result = $category['id'];
            if ($category['parent_id']) {
                $result = $this->getParent($category['parent_id'], $istop);
            }
        } else {
            $result = array();
            $category = $this->getbyId($cid);
            if ($category['parent_id'] ) {
                $result[] = $category['parent_id'];
                if ($category['parent_id'] != $cid) {
                    $parent_id = $this->getParent($category['parent_id'], $istop);
                    if(!empty($parent_id)){
                        $result = array_merge($result,$parent_id);
                    }
                }
               
            }
        }
        return $result;
    }

    /* 生成缓存 */
    public function build_cache() {
        $result = $this->getField(implode(',', array_keys($this->fields['_type'])), TRUE);
        setcache('goods_category', $result, 'goods');
        return;
    }
}