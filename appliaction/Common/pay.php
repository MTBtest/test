<?php 
/**
 *      生成流水号
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
function create_sn(){
    mt_srand((double )microtime() * 1000000 );
    return date("YmdHis" ).str_pad( mt_rand( 1, 99999 ), 5, "0", STR_PAD_LEFT );
}
/**
 * 返回响应地址
 */
function return_url($code, $method = 'notify') {
    return (is_ssl() ? 'https://':'http://').$_SERVER['HTTP_HOST'].ROOT_PATH.'api/trade/api_'.$method.'_'.$code.'.php';
}
    
function unserialize_config($cfg){
        if (is_string($cfg) ) {
            $arr = string2array($cfg);
        $config = array();
        foreach ($arr AS $key => $val) {
            $config[$key] = $val['value'];
        }
        return $config;
    } else {
        return false;
    }
}
/**
 * 返回订单状态
 */
function return_status($status) {
    $trade_status = array('0'=>'succ', '1'=>'failed', '2'=>'timeout', '3'=>'progress', '4'=>'unpay', '5'=>'cancel','6'=>'error');
    return $trade_status[$status];
}
/**
 * 返回订单手续费
 * @param  $amount 订单价格
 * @param  $fee 手续费比率
 * @param  $method 手续费方式
 */
function pay_fee($amount, $fee=0, $method=0) {
    $pay_fee = 0;
    if($method == 0) {
        $val = floatval($fee) / 100;
        $pay_fee = $val > 0 ? $amount * $val : 0;
    } elseif($method == 1) {
        $pay_fee = $fee;
    }
    return round($pay_fee, 2);
}

/**
 * 生成支付按钮
 * @param $data 按钮数据
 * @param $attr 按钮属性 如样式等
 * @param $ishow 是否显示描述
 */
function mk_pay_btn($data,$attr='class="payment-show"',$ishow='1') {
    $pay_type = '';
    if(is_array($data)){
        foreach ($data as $v) {
            $pay_type .= '<label '.$attr.'>';
            $pay_type .='<input name="pay_type" type="radio" value="'.$v['pay_id'].'"> <em>'.$v['name'].'</em>';
            $pay_type .=$ishow ? '<span class="payment-desc">'.$v['pay_desc'].'</span>' :'';
            $pay_type .= '</label>';
        }
    }
    return $pay_type;
}

/**
 *功能：支付宝接口公用函数
 *详细：该页面是请求、通知返回两个文件所调用的公用函数核心处理文件，不需要修改
 *版本：3.0
 *修改日期：2010-05-24
 '说明：
 '以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 '该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

*/
 
/**
 * 生成签名结果
 * @param $array要加密的数组
 * @param return 签名结果字符串
*/
function build_mysign($sort_array,$security_code,$sign_type = "MD5", $issort = TRUE) {
    if($issort == TRUE) {
	    $sort_array = arg_sort($sort_array);
	}
    $prestr = create_linkstring($sort_array);       //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
    $prestr = $prestr.$security_code;               //把拼接后的字符串再与安全校验码直接连接起来
	//echo $prestr;
    $mysgin = sign($prestr,$sign_type);             //把最终的字符串加密，获得签名结果
    return $mysgin;
}   


/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $array 需要拼接的数组
 * @param return 拼接完成以后的字符串
*/
function create_linkstring($array, $encode = FALSE) {
    $arg  = "";
    while (list ($key, $val) = each ($array)) {
        if($encode === TRUE) $val = urlencode($val);
        $arg.=$key."=".$val."&";
    }
    $arg = substr($arg,0,count($arg)-2);//去掉最后一个&字符
    // if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
    return $arg;
}

/********************************************************************************/

/**除去数组中的空值和签名参数
 * @param $parameter 加密参数组
 * @param return 去掉空值与签名参数后的新加密参数组
 */
function para_filter($parameter) {
    $para = array();
    while (list ($key, $val) = each ($parameter)) {
        if($key == "sign" || $key == "sign_type" || $val == "")continue;
        else    $para[$key] = $parameter[$key];
    }
    return $para;
}

/********************************************************************************/

/**对数组排序
 * @param $array 排序前的数组
 * @param return 排序后的数组
 */
function arg_sort($array) {
    $array = para_filter($array);
    ksort($array);
    reset($array);
    return $array;
}

/********************************************************************************/

/**加密字符串
 * @param $prestr 需要加密的字符串
 * @param return 加密结果
 */
function sign($prestr,$sign_type) {
    return md5($prestr);
}

// 日志消息,把支付宝返回的参数记录下来
function log_result($word) {
    $fp = fopen("log.txt","a");
    flock($fp, LOCK_EX) ;
    fwrite($fp, L('execute_date', 'pay')."：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}   


/**实现多种字符编码方式
 * @param $input 需要编码的字符串
 * @param $_output_charset 输出的编码格式
 * @param $_input_charset 输入的编码格式
 * @param return 编码后的字符串
 */
function charset_encode($input,$_output_charset ,$_input_charset) {
    $output = "";
    if(!isset($_output_charset) )$_output_charset  = $_input_charset;
    if($_input_charset == $_output_charset || $input ==null ) {
        $output = $input;
    } elseif (function_exists("mb_convert_encoding")) {
        $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
    } elseif(function_exists("iconv")) {
        $output = iconv($_input_charset,$_output_charset,$input);
    } else die("sorry, you have no libs support for charset change.");
    return $output;
}

/********************************************************************************/

/**实现多种字符解码方式
 * @param $input 需要解码的字符串
 * @param $_output_charset 输出的解码格式
 * @param $_input_charset 输入的解码格式
 * @param return 解码后的字符串
 */
function charset_decode($input,$_input_charset ,$_output_charset) {
    $output = "";
    if(!isset($_input_charset) )$_input_charset  = $_input_charset ;
    if($_input_charset == $_output_charset || $input ==null ) {
        $output = $input;
    } elseif (function_exists("mb_convert_encoding")) {
        $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
    } elseif(function_exists("iconv")) {
        $output = iconv($_input_charset,$_output_charset,$input);
    } else die("sorry, you have no libs support for charset changes.");
    return $output;
}

/*********************************************************************************/

/**用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
注意：由于低版本的PHP配置环境不支持远程XML解析，因此必须服务器、本地电脑中装有高版本的PHP配置环境。建议本地调试时使用PHP开发软件
 * @param $partner 合作身份者ID
 * @param return 时间戳字符串
*/
function query_timestamp($partner) {
    $URL = "https://mapi.alipay.com/gateway.do?service=query_timestamp&partner=".$partner;
    $encrypt_key = "";
    return $encrypt_key;
}

function getHttpResponsePOST($url, $para, $input_charset = 'utf-8') {
    if (trim($input_charset) != '') {
        $url = $url."_input_charset=".$input_charset;
    }
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//严格认证
    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    curl_setopt($curl,CURLOPT_POST,true); // post传输数据
    curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
    $responseText = curl_exec($curl);
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    return $responseText;
}
    /*
    *xml to array
    */
    function xmlToArray($xml)
    {       
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);      
        return $array_data;
    }
    /*
    *array to xml
    */
    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
             $xml.="<".$key.">".$val."</".$key.">"; 
        }
        $xml.="</xml>";
        return $xml; 
    }
    /*
    *生成32位随机字符串
    */
    function createNoncestr( $length = 32 ) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {  
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        }  
        return $str;
    }
    /*
    *与微信通讯获得二维码地址信息，必须以xml格式
    */
    function postXmlCurl($xml,$url,$second=30)
    {       
        //初始化curl        
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        curl_close($ch);
        //返回结果
        if($data)
        {
            curl_close($ch);
            return $data;
        }
        else 
        { 
           return false;
        }
    }