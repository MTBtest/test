<?php
/**
 * 广告列表
 *	[Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	This is NOT a freeware, use is subject to license terms
 *
 *	http://www.haidao.la
 *	tel:400-600-2042
 */
class ArticleAdvController extends AdminBaseController {

	public function _initialize() {
		parent::_initialize();
		$this->db = model('Adv');
		$this->type_text=array(1=>'图片',2=>'文字',3=>'代码');
	}

	public function lists(){

		$pos_id = $_GET['pos_id'];
		if(IS_POST){
			$sqlmap = array();
			
			if($pos_id){
				$sqlmap['position_id'] = $pos_id;
			}
			$_order=isset($_POST['order']) ? ($_POST['order']) : NULL;
			$_sort=isset($_POST['sort']) ? ($_POST['sort']) : NULL;
			if($_order && $_sort){
				$order[$_sort] = $_order;
			}else{
				$order['id'] = 'DESC';
			}

			$pagenum=isset($_POST['page']) ? intval($_POST['page']) : 1;
			$rowsnum=isset($_POST['rows']) && (int)($_POST['rows']) != 0 ? intval($_POST['rows']) : PAGE_SIZE;
			$data['total'] = $this->db->where($sqlmap)->count();	//计算总数 
			$data['rows']=$this->db
				->field(true)
				->where($sqlmap)
				->limit(($pagenum-1)*$rowsnum.','.$rowsnum)
				->order($order)
				->select();
			foreach ($data['rows'] as $key => $value) {
				$data['rows'][$key]['position_name'] = M('advPosition')->where(array('id'=>$value['position_id']))->getField('name');
			}
			if (!$data['rows']) $data['rows']=array();
			echo json_encode($data);
		}else{
			include $this->admin_tpl('article_adv_lists');
		}
	}
	/**
	 * 添加修改页
	 */
	public function update(){
		$validform = TRUE;
		$opt=I("opt");
		$id=I("id",0);
		if(IS_POST){
			self::save();
		}else{
			if(isset($opt) && $opt){
				//添加
				if($opt=='add') {
					$position_id = I('pos_id');
					$adv_position=M('adv_position')->where('id='.$position_id)->Find();
					include $this->admin_tpl('article_adv_update');
				}
				//编辑
				if($opt=='edit' && $id>0){
					$map['id'] = $id;
					$r = $this->db->getList($map);
					$info = $r['list'][0];
					$info['content'] = dstripslashes($info['content']);
					include $this->admin_tpl('article_adv_update');
				}
				//删除
				if($opt == 'del' && $id ){
					unset($where);
					$where['id']=array('in',$id);
					$this->db->where($where)->delete();
					showmessage('恭喜你，删除成功！',U('Article_adv/lists'),1); 
					exit(); 
				}
			}else{
				showmessage('参数错误,请联系管理员!',U('ArticleAdv/lists'),0);
			}
		}
		
	}
	/**
	 * 处理数据
	 */
	protected function save(){
		$opt=I('opt');
		$type=I('type',1,'intval');
		
		if($type=='1') {
			$content=I('img');
			$_POST['link']=$_POST['ilink'];
		}
		if($type=='2'){
			$content=I('text');
			$_POST['link']=$_POST['tlink'];
		} 
		if($type=='3'){
			$content=I('code');
			} 
		
		
		
		$_POST['content']=htmlspecialchars_decode($content);
		$_POST['starttime']=strtotime(I('starttime'));
		$_POST['endtime']= strtotime(I('endtime'));
 		$_POST = daddslashes($_POST);
		
		//添加
		if(isset($opt) && $opt=='add'){
			if($this->db->create()){
				$this->db->add();
				showmessage('添加广告成功',U('ArticleAdv/lists'),1);
			}else {
				showmessage($this->db->getError(),U('ArticleAdv/lists'),0);
			}
		}
		//编辑
		if(isset($opt) && $opt=='edit'){
			if($this->db->create()){
				$this->db->save();
				showmessage('编辑广告成功',U('ArticleAdv/lists'),1);
			}else {
				showmessage($this->db->getError(),U('ArticleAdv/lists'),0);
			}
		}
		
	}
}
