<?php 
/**
 *      文章分类模型
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class ArticleCategoryModel extends Model
{
	 //自动验证
    protected $_validate = array(
        array('name', 'require', '名称必须！'),
        array('parent_id', 'require', '主分类必须！', 1, '', 3),
        array('sort', 'number', '排序值必是数字！', 1, '', 3), // 在新增的时候验证name字段是否唯一    
    );
	protected $_auto = array();

	public function update($data, $iscreate = TRUE) {		
		if ($iscreate == TRUE) $data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (isset($data['id']) && is_numeric($data['id'])) {
			$result = $this->save($data);
			if (!$result) {
				$this->error = '更新数据失败';
				return false;
			}
		} else {
			$result = $this->add($data);
			if ($result === false) {
				$this->error = '添加数据失败';
				return false;
			}
		}
		return $result;
	}

    /* 根据分类ID获取上级ID */
    public function getParent($cid = '') {
        $return = array();
        $category = $this->getbyId($cid);
        $result = $category['id'];
        if ($category['parent_id']) {
            $result = $this->getParent($category['parent_id']);
        }
        return $result;
    }


    public function getById($id){
    	return $this->where(array('id'=>$id))->find();
    }	
	
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
     *  根据ID获取父分类信息
     */
    public function findFather($pid) {
        static $flist = array();
        $row = $this->where('id=' . $pid)->find();
        if ((int) $row['parent_id'] != 0) {
            $classFID = $row['parent_id'];
            $flist[] = $classFID;
            $this->findFather($classFID);
        }
        return $flist;
    }

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array          分类树
     */
    public function getTree($id = 0, $field = true) {
        die("abc");
        /* 获取当前分类信息 */
        if ($id) {
            $info = $this->info($id);
            $id = $info['id'];
        }

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
	
}