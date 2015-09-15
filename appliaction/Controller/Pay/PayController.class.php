<?php 
class PayController extends AdminBaseController
{
    protected $entrydir = '';
    public function _initialize() {
		parent::_initialize();
		$this->db = model('payment');
        libfile('Xml');
        $this->entrydir = EXTEND_PATH.'Driver/pay/';
	}

	/* 支付方式模块 */
	public function manage() {
        $payment = getcache('payment', 'pay');
        $pays = array();
        $folders = glob($this->entrydir.'*');
        foreach ($folders as $key => $folder) {
            $file = $folder. DIRECTORY_SEPARATOR .'config.xml';
            if(file_exists($file)) {
                $importtxt = @implode('', file($file));
                $xmldata = xml2array($importtxt);
                $pays[$xmldata['code']] = $xmldata;
            }
        }
        $pays = $this->array_sort($pays,'sort');
        $pays = array_merge_multi($pays, $payment);
		include $this->admin_tpl('pay_manage');
	}
    /* 对多位数组进行键值排序 */
    function array_sort($arr,$keys,$type='asc'){
        $keysvalue = $new_array = array();
        foreach ($arr as $k=>$v){
            $keysvalue[$k] = $v[$keys];
        }
        if($type == 'asc'){
            asort($keysvalue);
        }else{
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k=>$v){
            $new_array[$k] = $arr[$k];
        }
        return $new_array; 
    }

	/* 配置支付接口 */
	public function config($pay_code = '') {
        $importfile = $this->entrydir . $pay_code . '/config.xml';
        if(!file_exists($importfile)) {
            showmessage ('支付方式配置文件丢失');
        }
        $importtxt = @implode('', file($importfile));
        $xmldata = xml2array($importtxt);
		if (IS_POST) {
            $infoarr = array_merge_multi($xmldata, $_GET['info']);
            $xmldata = array2xml($infoarr);       
            $config = array();
            foreach($infoarr['config'] as $key => $value) {
                $config[$key] = $value['value'];
            }
            $data = array(
                'pay_code'  => $infoarr['code'],                
                'pay_name'  => $_GET['info']['pay_name'],
                'pay_desc'  => $infoarr['pay_desc'],
                'enabled'   => $infoarr['enabled'],
                'config'    => serialize($config),
                'isonline'  => $infoarr['isonline'],
                'pay_desc'  => $infoarr['pay_desc'],
                'applies'  => $infoarr['applies'],
                'sort'    => $infoarr['sort']
            );
            $result = $this->db->update($data);
            $this->db->build_cache();
            showmessage('支付方式配置成功', U('manage'), 1);
		} else {
            $pays = getcache('payment', 'pay');
            $pay = $pays[$pay_code];
			include $this->admin_tpl('pay_config');
		}
	}

	/* 卸载接口 */
	public function delete($pay_code = '') {
		$result = $this->db->delete($pay_code);
		if (!$result) {
			showmessage('支付接口卸载失败');
		} else {
			$this->db->build_cache();
			showmessage('支付接口卸载成功', U('manage'), 1);
		}
	}    
}