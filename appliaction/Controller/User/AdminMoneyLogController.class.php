<?php
/**
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class AdminMoneyLogController extends AdminBaseController {
	/**
	 * 自动执行
	 */
	public function _initialize() {
		parent::_initialize(); 
		$this->db = model('user_moneylog');
	}

	/**
	 * 会员财务管理
	 */   
	public function index() {
		if (IS_POST) {
			//分页
			$_GET['pagenum'] = isset($_GET['page']) ? intval($_GET['page']) : 1;
			$_GET['rowsnum'] = isset($_GET['rows']) && (int)($_GET['rows']) != 0 ? intval($_GET['rows']) : PAGE_SIZE;
			$result = $this->db->lists($_GET);
			if (!$result) $result=array();
			$data = array();
			$data['total'] = $this->db->count();	//计算总数  
			$data['rows'] = $result;
			echo json_encode($data);
		}else {
			$pays = getcache('payment', 'pay');
			if ($pays['ws_wap']['pay_name']) $pays['ws_wap']['pay_name'] = $pays['ws_wap']['pay_name'].' (不支持)';
			if ($pays['alipay_escow']['pay_name']) $pays['alipay_escow']['pay_name'] = $pays['alipay_escow']['pay_name'].' (不支持)';
			include $this->admin_tpl('admin_money_log');
		}
	}

	/* 财务设置 */
	public function admin_money_log_set() {
		$file = $_POST['files'];
		if(IS_POST) {
			unset($_POST['files']);
			//生成密钥
			$site_key =  C('site_key') ;
			$site_key = isset($site_key) ? $site_key : generate_password();
			$_POST['site_key'] = $site_key;
            if (file_exists(CONF_PATH . $file)){
				if ($this->update_config(daddslashes($_POST), CONF_PATH . $file)) {
					showmessage('操作成功', '', -1);
				} else {
					showmessage('操作失败');
				}	
			}else{
				$conf_file=file_put_contents(CONF_PATH . $file, '<?php return array(); ?>');
				if ($this->update_config(daddslashes($_POST), CONF_PATH . $file)) {
					showmessage('操作成功', '', -1);
				} else {
					showmessage('操作失败');
				}	
			}		
		} else {
			showmessage('请勿非法访问');
		}
	}

	/* 保存配置文件 */
	private function update_config($config, $config_file = '') {
        if(empty($config_file)) return false;
        if($fp = @fopen($config_file, 'wb')) {
            fwrite($fp, "<?php \nreturn " . stripslashes(var_export($config, true)) . ";");
            fclose($fp);
            return true;
        } else {
            return false;
        }
    }

}