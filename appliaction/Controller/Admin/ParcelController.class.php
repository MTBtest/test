<?php
/**
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */ 
class ParcelController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('order_parcel');
		$this->log_db = model('order_parcel_log');
		$this->_config = $this->load_config('status');
	}
    public function index(){
        if(IS_POST){
	            $result = $this->db->lists($_GET);
				$this->ajaxReturn($result);
		    }else{
		    	$dialog = TRUE;
		        $validform = TRUE;
		    	include $this->admin_tpl('order_parcel'); 
		    }
		}
	public function prints(){
        $map=array();
		$map['status']=array('eq',1);
		$sqlmap=array();
		$sqlmap['id']=$_GET['id'];
		$info=$this->db->where($sqlmap)->find();
		$content = model('print_tpl_parcel')->where($map)->getfield('content');
        $goodslist=json_decode(stripslashes($info['goods_list']),true);
        $tbcontent1=substr($content, strpos($content,'<tr id="goodslist">'));
		$tbcontent2=substr($tbcontent1,strpos($tbcontent1,'<tr id="goodslist">'),strpos($tbcontent1, '<tr>'));
		$tbcontent3=$tbcontent2;
		$total_price=array();
        foreach ($goodslist as $k => $v) {
			$str =str_replace('{goods_name}',$v["goods_name"], $tbcontent3);
			$str =str_replace('{shop_price}',$v["shop_price"], $str);
			$str =str_replace('{number}',$v["shop_number"], $str);
			$str =str_replace('{products_sn}',$v["products_sn"], $str);
	        $str =str_replace('{sort_id}', $k+1, $str);
	        $str=str_replace('{total_goods_price}',$v["shop_price"]*$v["shop_number"], $str);
            $total_price[]=$v["shop_price"]*$v["shop_number"];
	        $goodslist[$k] = $str;
		}
			$content=str_replace('{total_num}',$info['total_number'],$content);
			$content=str_replace('{total_price}',array_sum($total_price),$content);
			$goodslist=implode('', $goodslist);
			$content=str_replace($tbcontent2, $goodslist, $content);
		$regions=array();
		$regions['province']=model('region')->where('area_id='.$info['province'])->field('area_name')->find();
		$regions['city']=model('region')->where('area_id='.$info['city'])->field('area_name')->find();
		$regions['area']=model('region')->where('area_id='.$info['area'])->field('area_name')->find();
            $content = str_replace('{order_sn}',$info['order_sn'],$content);
			$content = str_replace('{accept_name}',$info['accept_name'],$content);
			$content = str_replace('{address}',$info['address'],$content);
			$content = str_replace('{province}',$regions['province']['area_name'],$content);
			$content = str_replace('{city}',$regions['city']['area_name'],$content);
			$content = str_replace('{area}',$regions['area']['area_name'],$content);
			$content = str_replace('{mobile}',$info['mobile'],$content);
			$content = str_replace('{delivery_txt}',$info['delivery_txt'],$content);
            $content=str_replace('{print_time}', date('Y-m-d H:i:s'), $content);
            $content=str_replace('{total_price}', $info['real_amount'], $content);
            $content=str_replace('{img}',IMG_PATH,$content);
		include $this->admin_tpl('order_parcel_detail');
	}	
	public function updstatus(){
		extract($_GET);
		$result=$this->setstatus();
		$sqlmap=array();
		$sqlmap['id']=array('EQ',$id);
		$order_sn=$this->db->where($sqlmap)->getfield('order_sn');
		$action=$this->_config['log_parcel_status'][$status];
		if(!$result) {
			showmessage('操作失败,请勿重复更改');
		} else {
			$this->write_log($order_sn, $action, $msg);
			showmessage('操作成功', '', 1);
		}
	}
    private function setstatus(){
    	extract($_GET);
        $data=array();
        $data['id']=$id;
        $data['status']=$status;
        $result=$this->db->save($data);
        return (!$result) ? $this->db->getError() : TRUE;
    }
   private function write_log($order_sn, $action, $msg) {
		$log = array(
			'order_sn' => $order_sn,
			'action' => $action,
			'msg' => $msg,
		);
		$this->log_db->update($log);
	}
    public function view_log(){
    	libfile('Dir');
		extract($_GET);
		if(empty($order_sn)) showmessage('订单号参数错误');	
		$sqlmap = array();
		$sqlmap['order_sn'] = $order_sn;
		$result = $this->log_db->where($sqlmap)->order("id DESC")->select();
		if(!$result) {
			showmessage('暂无任何订单操作日志信息');
		} else {
			foreach ($result as $key => $value) {
				$value['dateline'] = mdate($value['dateline'], 'Y/m/d H:i');
				$result[$key] = $value;
			}
			$return['data'] = $result;
			showmessage('订单操作日志查阅成功', '', 1, 1 ,1 ,1, $return);
		}
	}
}
	
	

 