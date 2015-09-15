<?php
class DeliveryModel extends SystemModel{
    //自动完成
    protected $_auto = array(
       
    );
    //自动验证
    protected $_validate = array(
        array('name','require','名称必须！'),
    );
	
	public function build_cache() {
		$sqlmap = array();
		$sqlmap['status'] = 1;
		$rows = $this->where($sqlmap)->select();
		$result = array();
		if($rows) {
			foreach($rows as $row) {
				$result[$row['id']] = $row;
			}
		}
		return setcache('deliverys', $result, 'site');
	}

    public function getList(){
        $count = $this->where($where)->count();
        libfile('Page');
		$pagesize = $_GET['pagesize'];
		$pagesize = $pagesize ? $pagesize : getconfig('page_num');
        $page = new Page($count, $pagesize);

        $order="sort ASC,id Desc";

        $_list=$this->table($table)->join($join)->field($field)->where($where)->order($order)->limit($page->firstRow . ',' . $page->listRows)->select();
        $page=$page->show();
        $result = array(
        	'_list' =>$_list ,
        	'page'=>$page,

        	);
        return $result;

    }


    //查询匹配快递
    public function query_delivery($region_ids){
         $regionArr =  explode(",",$region_ids);
        $db_pre = C('DB_PREFIX');
        $Model = M();
        $list = array();
        $region_id = end($regionArr);
        
        $Model->field("a.*, b.weightprice as weightprice1");
        $Model->table("cz_delivery a,cz_delivery_region b");
        $Model->where("
            a.`status`> -1
            AND
                b.delivery_id = a.id
            AND
                find_in_set(\"{$region_id}\",b.region_id)"
        );

        $_list=$Model->select();

        foreach ($_list as $key => $value) {
            unset($_tempArr,$_tempArr1,$_tempArr2);
            $_tempArr1 = explode(',',$value['weightprice']);
            $_tempArr2 = explode(',',$value['weightprice1']);
            $_tempArr = array_sum($_tempArr2)!= 0 ? $_tempArr2 : $_tempArr1 ;
            

            $_list[$key]['wp1'] = $_tempArr[0] ;
            $_list[$key]['wp2'] = $_tempArr[1] ;
            
        }
        $_list = is_array($_list) ? $_list : array() ;
        return $_list;
    }
}
