<?php

class GoodsCouponsListController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();
		$this->db = $this->db=model('CouponsList');
		$this->status_text =  array( "0" => "未发放", "1" => "已发放", "2" => "已使用", "3" => "禁用");
	}

	/**
	 * 分类列表
	 */
	public function lists(){
		$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
		$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
		$cid=$_GET['id'];
		if(IS_POST){
			$sqlmap = array();
			$sqlmap['cid'] = $cid;
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'DESC';
			}
			$data['total'] = $this->db->where($sqlmap)->count();	//计算总数 
			$data['rows']=$this->db->where($sqlmap)->limit(($pagenum-1)*$rowsnum.','.$rowsnum)->order($order)->select();
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('goods_coupons_list_lists');
		}
	}
	/**
	 * 添加修改页
	 */
	public function update(){
		$opt = $_GET["opt"];
		$coupons_id = $_GET["coupons_id"];
		$num = $_GET["num"];
		$val = $_GET["val"];
		$id = $_GET["id"];
		$pagenum = $_GET['pagenum'];
		$rowsnum = $_GET['rowsnum'];
		if(IS_POST || IS_GET){
			if(isset($opt) && $opt){
				//添加
				if($opt=='add' && $coupons_id>0 && $num>0) {
					//取项目父信息
					$coupons_item=M('coupons')->where('id='.$coupons_id)->Find();
					if(null===$coupons_item) {
						showmessage("项目已经删除!");exit();
					}
					self::create_card($coupons_item,$num);
					$this->redirect(U('GoodsCoupons_list/lists?id='.$coupons_id.''));
				}
				
				//发放
				if($opt=='status' && $id>0){
					$obj='coupons_list';
					$field='status';
					$list=$id;
					$val=$val;
					if(checkstatus($obj,$field,$list,$val)){
						showmessage('修改状态完成');
					}
				}
				
				//删除
				if($opt == 'del' && $id ){
					unset($where);
					$where['id']=array('in',$id);
					$this->db->where($where)->delete();
					showmessage('恭喜你，删除成功！',NULL,1); 
					exit(); 
				}
				//下载
				if($opt == 'excel' && $id ){
					$where['cid']=$id;
					$list = $this->db->field(true)->where($where)->limit($pagenum,$rowsnum);
					$headerArr = array('name'=>'名称',
						'sn'=>'卡号',
						'password'=>'密码',
						'value'=>'面值',
						'start_time'=>'开始时间',
						'end_time'=>'结束时间',
						'user_name'=>'所属会员',
						'use_order'=>'使用订单号',
						't_status'=>'状态'
						);
					};
					$this->DownloadCSV( $list, $headerArr);
			}else{
				showmessage('参数错误,请联系管理员!');
			}
		}
		
	}

	/*
	 * 生成卡号
	 */
	public function create_card($base,$num){
		$db_pre = C('DB_PREFIX');
		$sql="SHOW TABLE STATUS WHERE Name='{$db_pre}coupons_list'";
		$r=  $this->db->query($sql);
		$max_id= $r[0]['Auto_increment'];
		$max_id= $max_id? $max_id:0;
		
		libfile('Code');
		$code = new Code(); 
		//基本字串 用户ID+当前时间戳的大写MD5值
		$base_text= strtoupper(md5(ADMIN_ID.strtotime(now)));
		$num = $num>5000 ? 5000 : $num;
		for ($index = 0; $index < $num; $index++){
			$card_pre = substr($base_text,0,3);//基本字串前三位
			$card_no = $code->encodeID((int)$max_id+$index,5); //根据数据生成5位不重复号码最大36^5 = 60466176
			$card_vc = strtoupper(substr(md5($card_pre.$card_no),0,2)); //校验码 基本字串+卡号MD5的后两位
			$card_sn = $card_pre.$card_no.$card_vc;
			
			$data[$index]['cid'] = $base['id'];
			$data[$index]['sn'] = $card_sn;
			$data[$index]['password'] = generate_password(6);
			$data[$index]['name'] = $base['name'];
			$data[$index]['value'] = $base['value'];
			$data[$index]['start_time'] = $base['start_time'];
			$data[$index]['end_time'] = $base['end_time'];
			$data[$index]['status'] = 0;
		}
		$this->db->addAll($data);

	}
	/*
	* 通用方法
	* 导出大数据为CSV 
	* 参数依次传入 查询对象,CSV文件列头(键是数据表中的列名,值是csv的列名),文件名.对数据二次处理的函数;
	*/
	private function DownloadCSV($selectObject,$head){
		$fileName=time();
		if ( !is_object( $selectObject ) || !is_array( $head ) ) {
			exit('参数错误!');
		}
		set_time_limit(0);
		//下载头.
		header ('Content-Type: application/vnd.ms-excel;charset=gbk');
		header ('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
		header ('Cache-Control: max-age=0');
		//输出流;
		$file = 'php://output';
		$fp = fopen ( $file, 'a' );

		function changCode( $changArr ) {
			// 破Excel2003中文只支持GBK编码;
			foreach ( $changArr as $k => $v ) {
				$changArr [$k] = iconv ( 'utf-8', 'gbk', $v );
			}
			//返回一个 索引数组;
			return array_values( $changArr );
		};
		//写入头部;
		fputcsv ( $fp, changCode( $head ) );

		//写入数据;
		$pageSize = 100;//每次查询一百条;
		$page  = 1;//起始页码;
		$list = array();

		//查库;
		$cloneObj = clone $selectObject;//因为thinkphp内部执行完select方法后会清空对象属性,所以clone;
		$t_status=array( "0" => "未发放", "1" => "已发放", "2" => "已使用", "3" => "禁用");
		while ( $list = $cloneObj ->limit( $pageSize*( ($page++)-1 ), $pageSize )->select()  ) {
			$cloneObj = clone $selectObject;
			//对查询结果二次处理
			foreach ( $list as $key => $value ) {
				$value['start_time']= date('Y-m-d H:i',$value['start_time']);
				$value['end_time']= date('Y-m-d H:i',$value['end_time']); 
				$value['t_status']= $t_status[$value['status']];
				$value = array_intersect_key( $value, $head );//返回需要写入CSV的数据;
				$value = array_merge( $head, $value );//利用此函数返回需要的顺序;
				$value = changCode( $value );
				fputcsv ( $fp, $value );//写入数据;
				flush();
			}
			ob_flush();
		}
		exit();
	}
}