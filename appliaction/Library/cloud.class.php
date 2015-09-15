<?php
class cloud
{
    protected $_api = 'http://www.haidao.la/api.php?';
    protected $_params = array();
    protected $error = '';
    
    public function __construct() {
        $this->_params['format'] = 'json';
        $this->_params['charset'] = CHARSET;
        $this->_params['timestamp'] = NOW_TIME;
        $this->_params['site'] = $this->_getApiSite();
    }

    /**
     * 获取用户信息
     * @return boolean
     */
    public function getAccountInfo() {
        $_account = getcache('account', 'cloud');
        if($_account) {
            return authcode($_account, 'DECODE');
        }
        return FALSE;
    }
    
    /**
     * 登录远程用户
     * @param type $account
     * @param type $password
     */
    public function getMemberLogin($account, $password) {
        $this->_params['method'] = 'api.member.login';
        $this->_params['account'] = $account;
        $this->_params['password'] = $password;
        $this->_params['sign'] = $this->create_sign();
        $result = $this->getHttpResponse($this->_api, $this->_params);
        return $this->_response($result);
    }

    /**
     * 获取短信模版
     * @param type $tpl_type    [模版标识]
     * @param type $token       [站点token]
     * @param type $identifier  [站点标识]
     */
    public function getSmsTpl($tpl_type, $token , $identifier) {
        $this->_params['method']     = 'api.sms.template';  // 提交地址
        $this->_params['tpl_type']   = $tpl_type;
        $this->_params['token']      = $token;
        $this->_params['identifier'] = $identifier;
        $this->_params['sign'] = $this->create_sign();
        $result = $this->getHttpResponse($this->_api, $this->_params);
        return $this->_response($result);
    }

    /**
     * 发送短信
     * @param type $tpl_id      [模版ID]
     * @param type $mobile      [手机号码]
     * @param type $sms_sign    [短信签名]
     * @param type $tpl_vars    [模版变量(array)]
     * @param type $token       [站点token]
     * @param type $identifier  [站点标识]
     */
    public function send_sms($params) {
        $this->_params['method']     = 'api.sms.send';  // 提交地址
        $this->_params['tpl_id']  = $params['tpl_id'];
        $this->_params['mobile']  = $params['mobile'];
        $this->_params['sms_sign'] = $params['sms_sign'];
        $this->_params['tpl_vars'] = base64_encode(json_encode($params['tpl_vars']));
        $this->_params['token']      = $params['cloud']['token'];
        $this->_params['identifier'] = $params['cloud']['identifier'];
        $this->_params['sign'] = $this->create_sign();
        $result = $this->getHttpResponse($this->_api, $this->_params);
        return $this->_response($result);
    }

    /**
     * 实时更新站点用户信息
     * @param type $username    [平台用户名]
     * @param type $token       [站点token]
     * @param type $identifier  [站点标识]
     */
    public function update_site_userinfo($params) {
        $this->_params['method']     = 'api.member.info';  // 提交地址
        $this->_params['username']   = C('__CLOUD__.username');
        $this->_params['token']      = C('__CLOUD__.token');
        $this->_params['identifier'] = C('__CLOUD__.identifier');
        $this->_params['sign']       = $this->create_sign();
        $result = $this->getHttpResponse($this->_api, $this->_params);
        $_result = $this->_response($result);
        if($_result['code'] == 200 && !empty($_result['result'])) {
            $_config = array(
                '__CLOUD__' => array(
                    'username'   => C('__CLOUD__.username'),
                    'realname'   => $_result['result']['realname'],
                    'sms'        => $_result['result']['sms'],
                    'coin'       => $_result['result']['coin'],
                    'token'      => C('__CLOUD__.token'),
                    'identifier' => C('__CLOUD__.identifier'),
                    'authorize'  => $_result['result']['authorize_status'],
                )
            );
            $config_file = CONF_PATH.'cloud.php';
            if($fp = @fopen($config_file, 'wb')) {
                fwrite($fp, "<?php \nreturn " . stripslashes(var_export($_config, true)) . ";");
                fclose($fp);
            }
        }
    }
    
    /**
     * 组装数据返回格式
     * @param type $result
     * @return type
     */
    private function _response($result) {
        if(!$result) {
            return array('code' => -10000, 'msg' => '接口网络异常，请稍后。');
        } else {
            return json_decode($result, TRUE);
        }
    }
    
    /**
     * 获取接口返回值
     * @param type $url
     * @param type $params
     * @return type
     */
    private function getHttpResponse($url, $params) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//严格认证
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl,CURLOPT_POST, FALSE); // post传输数据
        curl_setopt($curl,CURLOPT_POSTFIELDS,$params);// post传输数据
        $responseText = curl_exec($curl);
        return $responseText;
    }
    
    /**
     * 创建接口签名
     * @return type
     */
    private function create_sign() {
        $array = $this->_params;
        ksort($array,SORT_STRING);
        $arg  = "";
        while (list ($key, $val) = each ($array)) {
            $arg.=$key."=".$val."&";
        }
        $arg = substr($arg,0,count($arg)-2);
        return strtolower(md5($arg.$array['timestamp']));        
    }
    
    private function _getApiSite() {
        $_site = array();
        $_site['site_name'] = C('site_name');
        $_site['site_url'] = $_SERVER['HTTP_HOST'];
        $_site['install_ip'] = get_client_ip();
        $_site['last_var'] = C('VERSION');
        return base64_encode(serialize($_site));
    }
    
}
/* end of file cloud.class.php */
/* location: ./appliaction./Library/cloud.class.php */