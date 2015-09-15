<?php
class kuaidi
{
	protected $key = '6cb4071a68abf951';
	protected $url = 'http://www.kuaidi100.com/query?';
	protected $par = array();
	protected $error = '';
	
	public function __construct() {
		if(C('KUAIDI_KEY')) $this->key = C('KUAIDI_KEY');
		$this->par = array(
			'type' => '',
			'postid' => '',
			'id' => 1,
			'valicode' => '',
			'temp' => random(10),
		);
	}
	
	public function query($com, $nu, $show = '0', $muti = 1) {
		if(empty($com) || empty($nu)) return FALSE;
		$this->par['type'] = $com;
		$this->par['postid'] = $nu;
		$result =  _dfsockopen($this->url.http_build_query($this->par));
		if($result) {
			$result =  json_decode($result, TRUE);
			if($result['status'] == 0) {
				$this->error = '物流单暂无结果';
				return FALSE;
			} elseif($result['status'] == 2) {
				$this->error = '接口出现异常';
				return FALSE;
			} else {
				unset($result['status']);
				switch ($result['state']) {
					case '0':
						$result['message'] = '在途';
						break;
					case '1':
						$result['message'] = '揽件';
						break;
					case '2':
						$result['message'] = '疑难';
						break;
					case '3':
						$result['message'] = '签收';
						break;
					case '4':
						$result['message'] = '退签';
						break;
					case '5':
						$result['message'] = '派件';
						break;
					case '6':
						$result['message'] = '退回';
						break;
					default:
						$result['message'] = '其他';
						break;
				}
				return $result;
			}
		} else {
			$this->error = '查询失败，请稍候重试';
			return FALSE;
		}
	}
	
	public function getError() {
		return $this->error;
	}
}