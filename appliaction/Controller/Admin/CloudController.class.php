<?php
set_time_limit(0);
ini_set("memory_limit", "2000M");

class CloudController extends AdminBaseController {
    public $totalScore = 100;

	public function _initialize() {
		parent::_initialize();
		libfile('cloud');

        $this -> remote_url = 'http://cloud.haidao.la';
        $this -> update_path = RUNTIME_PATH . 'uppack/';

    }

	/* 云平台首页 */
	public function index() {
        // 实时更新站点用户信息(真实姓名、金币、短信)
        libfile('cloud');
        $_cloud = new cloud();
        $_cloud->update_site_userinfo();
		$cloud = C('__CLOUD__');
		include $this->admin_tpl('cloud_index');
	}

    /**
     * 登陆远程账号
     */
	public function login() {
        if(IS_POST) {
            if(empty($_GET['account'])) {
                showmessage('用户名不能为空');
            }
            if(empty($_GET['password'])) {
                showmessage('密码不能为空');
            }
            $_cloud = new cloud();
            $_result = $_cloud->getMemberLogin($_GET['account'], $_GET['password']);
            if($_result['code'] != 200) {
                showmessage($_result['msg']);
            } else {
                $_config = array(
                    '__CLOUD__' => array(
                        'username'   => $_result['result']['username'],
                        'realname'   => $_result['result']['realname'],
                        'sms'        => $_result['result']['sms'],
                        'coin'       => $_result['result']['coin'],
                        'token'      => $_result['result']['token'],
                        'identifier' => $_result['result']['site']['identifier'],
                        'authorize'  => (int) $_result['result']['site']['authorize_status'],
                    )
                );
                $config_file = CONF_PATH.'cloud.php';
                if($fp = @fopen($config_file, 'wb')) {
                    fwrite($fp, "<?php \nreturn " . stripslashes(var_export($_config, true)) . ";");
                    fclose($fp);
                    showmessage('绑定成功', U('index'), 1);
                } else {
                    showmessage('配置文件不可写');
                }
            }
        } else {
            showmessage('请勿非法请求');
        }
	}



    public function uppack(){
        include $this -> admin_tpl('cloud_uppack');
    }

    public function uppack_trip(){
        $cloud = C('__CLOUD__');
        if(empty($cloud['token'])) {
            showmessage('请先绑定云平台账号', U('index'));
        }
        $lastver = file_get_contents(($this -> remote_url . '/index.php?a=check&v=') . getconfig('version'));
        $ver = getconfig('version');
        include $this -> admin_tpl('cloud_uppack_trip');
    }

    /**
     *      检测版本更新
     *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
     *      This is NOT a freeware, use is subject to license terms
     *
     *      http://www.haidao.la
     *      tel:400-600-2042
     */
    public function checkup() {
        ob_flush();
        flush();
        //$this -> showProgress('setup_2', 'test-normal-btn', '正在检测');
        $lastver = file_get_contents(($this -> remote_url . '/index.php?a=check&v=') . getconfig('version'));
        $ver = getconfig('version');
        $setup = $_GET['setup'] ? $_GET['setup'] : 0;
        $info = array();
        $info['obj'] = 'setup_2';
        $r = version_compare($lastver, $ver);
        if($r == 1){
            $info['status'] = 1;
            $info['version'] = $lastver;
            $info['score'] = 40;

            $info['css']='test-error-btn';
            $info['text']='<a  href=' . U('Admin/Cloud/uppack') . '>升级' . $lastver . '</a>';
        }elseif($r == 0){
            $info['status'] = 0;
            $info['version'] = getconfig('version');
            $info['css']='test-success-btn';
            $info['text']='已是最新版';
            $info['score'] = 0;
        }elseif($r == -1){
            $info['status'] = 0;
            $info['version'] = getconfig('version');
            $info['css']='test-error-btn';
            $info['text']='<a href="http://www.haidao.la" target="_blank">非法版本!请重新下载安装!</a>';
            $info['score'] = 0;
        }
        echo json_encode($info);
    }

    /**
     *      下载更新文件
     *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
     *      This is NOT a freeware, use is subject to license terms
     *
     *      http://www.haidao.la
     *      tel:400-600-2042
     */
    public function downfile() {
        set_time_limit(0);

        if (!is_dir($this -> update_path)) dir_create($this -> update_path, 0777, true);

        $_version = getconfig('version');
        $lastver = file_get_contents(($this -> remote_url . '/index.php?a=check&v=') . $_version);
        $filename = random(8) . ".zip";
        $newfname = $this -> update_path . $filename;

        if ($lastver == getconfig('version')) {
            $this -> showProgress('您已是最新版本' . $lastver . '',1);
            exit();
        }

        //需要下载的远程文件url
        $url = $this -> remote_url . '/index.php?a=uppack&v='.$lastver;

        $ch = curl_init();
        //Initialize a cURL session.
        $fp = fopen($newfname, 'w');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 64000);
        /*$a_opt = array(CURLOPT_URL => $url, CURLOPT_HEADER => 0, //不包含头信息到输出文件
        CURLOPT_RETURNTRANSFER => 1, //当CURLOPT_RETURNTRANSFER设置为false时，方法curl_exec的调用在成功时会直接输出结果并返回bool（true），而不是以字符串形式返回取得的结果数据.
        CURLOPT_NOPROGRESS => 0, //true用来禁用curl transfers的进度条显示，注意：php默认设置该选项为true，此处我们需要false
        CURLOPT_PROGRESSFUNCTION => 'progress', //用来显示进度的回调函数
        CURLOPT_FILE => $fp, //目标文件保存路径
        CURLOPT_FOLLOWLOCATION => TRUE, CURLOPT_BUFFERSIZE => 64000, );
        curl_setopt_array($ch, $a_opt);*/
        $str = curl_exec($ch);

        if (curl_errno($ch)){
            die(curl_error($ch));
        }else{
            curl_close($ch);
        }
        $this -> expfile($filename, $lastver);
    }

    /**
     *      执行升级
     *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
     *      This is NOT a freeware, use is subject to license terms
     *
     *      http://www.haidao.la
     *      tel:400-600-2042
     */
    public function expfile($file, $version) {
        $this -> showProgress('正在解压文件',0);
        libfile('PclZip');
        $updatezip = $this -> update_path . $file;
        $archive = new PclZip($updatezip);

        if ($archive -> extract(PCLZIP_OPT_PATH, './', PCLZIP_OPT_REPLACE_NEWER) == 0) {
            $info['status'] = "0";
            $info['info'] = "文件不存在.升级失败";
        } else {

            $sqlfile = $this -> DOC_ROOT . 'update.sql';
            $sql = file_get_contents($sqlfile);
            if ($sql) {
                $sql = str_replace("hd_", getconfig('DB_PREFIX'), $sql);
                $Model = new Model();
                error_reporting(0);
                foreach (split(";[\r\n]+", $sql) as $v) {
                    @mysql_query($v);
                }
            }
            @unlink($sqlfile);
            //删除文件
            $updatefile = $this -> DOC_ROOT . 'update.php';
            if (file_exists($updatefile)){
                require($updatefile);
            }
            @unlink($updatefile);

            libfile("Dir");
            if(file_exists($this -> update_path)){
                Dir::del($this -> update_path);
            }
            $info['status'] = "1";
            $info['info'] = $version."升级完成!";
            $this -> update_config(array('VERSION' => '' . $version . '', 'BUILD' => '' . date('Y-m-d H:i:s',time()) . ''), CONF_PATH . 'version.php');
        }
        $this -> showProgress($info['info'],0);
        $this -> clcache($version);
    }

    /**
     *      清进缓存
     *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
     *      This is NOT a freeware, use is subject to license terms
     *
     *      http://www.haidao.la
     *      tel:400-600-2042
     */
    public function clcache($version) {
        $this -> showProgress('正在件清理缓存文件',0);
        clearCache();
        $this -> showProgress($version.'升级完成',0);
        sleep(1);
        $this -> redirect(U('Admin/Cloud/uppack'));
    }

    /**
     *      更新配置文件
     *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
     *      This is NOT a freeware, use is subject to license terms
     *
     *      http://www.haidao.la
     *      tel:400-600-2042
     */
    private function update_config($config, $config_file = '') {
        !is_file($config_file) && $config_file = CONF_PATH . 'web.php';
        if (is_writable($config_file)) {
            file_put_contents($config_file, "<?php \nreturn " . stripslashes(var_export($config, true)) . ";", LOCK_EX);
            @unlink(RUNTIME_FILE);
            return true;
        } else {
            return false;
        }
    }


    /**
     * 返回进度
     */
    function showProgress($text, $status = 0) {
        echo '<script type="text/javascript">showInfo(\'' . $text . '\',\'' . $status . '\');</script>';
        ob_flush();
        flush();

    }






}


function progress($dltotal, $dlnow, $ultotal, $ulnow) {
    $now = date('Y-m-d H:i:s');
    //当前时间
    //刚开始下载或上传时，$dltotal和$ultotal为0，此处避免除0错误
    if (empty($dltotal)) {
        $percent = "0";
    } else {
        $percent = $dlnow / $dltotal;
    }
    //echo "$dltotal, $dlnow, $percent, $now , $ultotal, $ulnow"."<br />";
    echo "<script>updateProgress('$percent','$dlnow','$dltotal')</script>";
    //$this->showProgress(Math.round(percentage * 100) + '% (' + dltotal + '/' + dlnow + ')');
    ob_flush();
    flush();
    return (0);
}
