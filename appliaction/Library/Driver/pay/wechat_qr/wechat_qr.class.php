<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
libfile('pay_abstract');
class wechat_qr extends pay_abstract {
    public $parameters;
    public $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
	public function __construct($config = array()) {	
		if (!empty($config)) $this->set_config($config);
        $this->config['gateway_url'] =U('Goods/Order/detail');
        $this->config['gateway_method'] = 'POST';
        $this->config['notify_url'] = return_url('wechat_qr', 'notify');
		$this->config['return_url'] = return_url('wechat_qr', 'return');
	}
    public function _delivery() {
        return TRUE;
    }

    public function _return(){
        $data=xmlToArray($GLOBALS['HTTP_RAW_POST_DATA']);
        $tmpData=$data;
        unset($tmpData['sign']);
        $return=array();
        $sign = $this->getSign($tmpData,$this->config['key']);//本地签名
        if ($data['sign'] == $sign) {
            $return['return_code']='SUCCESS';
            $return['return_msg']='OK';
            postXmlCurl(arrayToXml($return),$this->url);
            $result=array();
            $result['result'] = 'success';
            $result['pay_code'] = 'wechat_qr';
            $result['out_trade_no'] = $data['out_trade_no'];
            return $result;
        }else{
            $return['return_code']='FAIL';
            $return['return_msg']='';
            postXmlCurl(arrayToXml($return),$this->url);
            return FALSE;
        }
    }
    public function _notify(){
                return $this->_return();
    }
    public function getpreparedata(){
        
    }
    
    public function response($result){
        if (FALSE == $result) echo 'fail';
        else echo 'success';

    }
    public function getCodeUrl() {
        $prepare_data['appid'] = $this->config['appid'];
        $prepare_data['body'] = $this->product_info['subject'];
        $prepare_data['mch_id'] = $this->config['mch_id'];
        $prepare_data['nonce_str'] =createNoncestr();
        $prepare_data['notify_url'] = $this->config['notify_url'];
        $prepare_data['out_trade_no'] = $this->product_info['trade_sn'];
        $prepare_data['product_id'] = $this->product_info['trade_sn'];
        // 商品信息
        $prepare_data['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'] ;
        $prepare_data['trade_type'] = 'NATIVE';
        $prepare_data['total_fee'] = (int)(100*$this->product_info['total_fee']);
        //订单信息
        $prepare_data['sign'] = $this->getSign($prepare_data,$this->config['key']);
        // 数字签名
        $prepare_xml=arrayToXml($prepare_data);
        $this->result = xmlToArray(postXmlCurl($prepare_xml,$this->url));
        return $this->result['code_url'];        
    }
    /**
     *  作用：格式化参数，签名过程需要使用
     */
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
               $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) 
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }
    
    /**
     *  作用：生成签名
     */
    public function getSign($Obj,$key)
    {
        foreach ($Obj as $k => $v)
        {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$key;
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }
}
    