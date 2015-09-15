<?php
// 通知模块
class NotifyController extends AdminBaseController {
    protected $entrydir = '';
    public function _initialize() {
		parent::_initialize();
        $this->db = model('notify');
		$this->tmp = model('notify_template');
        libfile('Xml');
        $this->entrydir = EXTEND_PATH.'Driver/notify/';
        libfile("form");
        libfile('cloud');
	}


    /* 支付方式模块 */
    public function setting() {
        $notify = getcache('notify', 'notify');
        $notifys = array();
        $folders = glob($this->entrydir.'*');
        foreach ($folders as $key => $folder) {
            $file = $folder. DIRECTORY_SEPARATOR .'config.xml';
            if(file_exists($file)) {
                $importtxt = @implode('', file($file));
                $xmldata = xml2array($importtxt);
                $notifys[$xmldata['code']] = $xmldata;
            }
        }
        $notifys = $this->array_sort($notifys,'sort');
        $notifys = array_merge_multi($notifys, $notify);

        // 实时更新站点用户信息(真实姓名、金币、短信)
        libfile('cloud');
        $_cloud = new cloud();
        $_cloud->update_site_userinfo();
        
        include $this->admin_tpl('notify_setting');
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

	/* 配置通知接口 */
	public function config($code = '') {
        $importfile = $this->entrydir . $code . '/config.xml';
        if(!file_exists($importfile)) showmessage ('支付方式配置文件丢失');
        if ($code == 'sms') {
            $cloud = C('__CLOUD__');
            if(empty($cloud['token'])) {
                showmessage('请先绑定云平台账号', U('Admin/Cloud/index'));
            }
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
                'code' => $infoarr['code'],                
                'enabled'     => $infoarr['enabled'],
                'config'      => json_encode($config),
            );
            $result = $this->db->update($data);
            $this->db->build_cache();
            if (!$result) showmessage('通知方式配置失败');
            showmessage('通知方式配置成功', U('setting'), 1);
		} else {
            $notifys = getcache('notify', 'notify');
            $notify = $notifys[$code];
			include $this->admin_tpl('notify_config');
		}
	}

	/* 卸载通知接口 */
	public function delete($code = '') {
		$result = $this->db->delete($code);
		if (!$result) showmessage('通知接口卸载失败');
		$this->db->build_cache();
		showmessage('通知接口卸载成功', U('setting'), 1);
	}

    /* 通知模版设置 */
    public function template() {
        $notifys = $this->get_notify();        
        $notifys = $this->array_sort($notifys,'sort');
        $data = array();
        if(IS_POST){
            // 获取通知模版黑名单
            foreach ($notifys as $k => $v) {
                if ($v['blacklist']) {
                    $blacklist[$k] = explode('|', $v['blacklist']);
                }
            }
            /* 获取通知模版设置数据 */
            $settings = $this->tmp->select();
            foreach ($settings as $k => $setting) {
                $setting['driver'] = json_decode($setting['driver'],TRUE);
                $item['hookname'] = $setting['name'];
                foreach ($notifys as $k => $v) {
                    if (in_array($setting['id'], $blacklist[$v['code']])) {
                        if (is_array($blacklist[$v['code']])) {
                            $item[$v['code']] = '不支持';
                        }
                    } else {       
                        if ($v['code'] == 'alipay') {
                            $item[$v['code']] = '敬请期待...';
                        } else {
                            if ($setting['driver'][$v['code']] == 1) {
                                $item[$v['code']] = '<a href="javascript:;" notify_id='.$setting['id'].' notify_code='.$v['code'].' onclick="ajax_driver(this);" class="ajax_get ajax_on"></a>';
                            } else {
                                $item[$v['code']] = '<a href="javascript:;" notify_id='.$setting['id'].' notify_code='.$v['code'].' onclick="ajax_driver(this);" class="ajax_get ajax_off"></a>';
                            }
                        }
                        
                    }                                    
                }
                $item['option'] = '<a href="'.U('template_set').'&id='.$setting['id'].'">模版设置</a>';
                $cols[] = $item;
            }
            echo json_encode($cols);
        }else{
            $colnum = count($notifys);
            $cols = array(
                array(
                    'field' => 'hookname',
                    'title' => '通知类型',
                    'width' => '10%',
                    'halign' => 'center',
                    'align' => 'center',
                    'sortable' => FALSE,
                )
            );
            foreach ($notifys as $k => $v) {
                $cols[] = array(
                    'field' => $v['code'],
                    'title' => $v['name'],
                    'width' => ceil(80 / $colnum).'%',
                    'halign' => 'center',
                    'align' => 'center',
                    'sortable' => FALSE,                    
                );
            }
            $cols[] = array(
                'field' => 'option',
                'title' => '操作',
                'width' => '10%',
                'halign' => 'center',
                'align' => 'center',
                'sortable' => FALSE,                    
            );
            include $this->admin_tpl('notify_template');
        }
    }

    /* 通知模版设置->点击开启或关闭 */
    public function ajax_status() {
        $notifys = $this->get_notify();
        $driver = $this->tmp->where(array('id'=>$_GET['notify_id']))->getField('driver');
        if (!$driver) showmessage('您的操作有误');
        $driver = json_decode($driver,TRUE);
        $data = array();
        $data['id'] = $_GET['notify_id'];
        $driver[$_GET['notify_code']] = $_GET['status'];
        $data['driver'] = json_encode($driver);
        $result = $this->tmp->save($data);
        if (!$result) showmessage('更改失败');
        showmessage('更改成功','',1);
    }

    /* 获取所有的通知接口 */
    public function get_notify() {
        $folders = glob($this->entrydir.'*');
        foreach ($folders as $key => $folder) {
            $file = $folder. DIRECTORY_SEPARATOR .'config.xml';
            if(file_exists($file)) {
                $importtxt = @implode('', file($file));
                $xmldata = xml2array($importtxt);
                $notifys[$xmldata['code']] = $xmldata;
            }
        }
        return $notifys;
    }
        
    /* 模版选择 */
    public function template_set() {
        $id = $_GET['id'];
        $result = $this->tmp->find($id);
        if (!$result) showmessage('您的操作有误');
        $notifys = $this->get_notify();
        $notifys = $this->array_sort($notifys,'sort');
        // 过滤黑名单模版
        foreach ($notifys as $k => $v) {
            if ($v['blacklist']) {
                $blacklist = explode('|', $v['blacklist']);
                if (in_array($id, $blacklist)) unset($notifys[$k]);
            }
        }
        $template = json_decode($result['template'],TRUE);
        if (IS_POST) {
            $data = array();
            $data['id'] = $_GET['notify_id'];
            foreach ($notifys as $k => $notify) {
                if($k=='email'){
                    $infos[$k] = htmlspecialchars_decode($_GET[$notify['code']]);
                }else{
                    $infos[$k] = $_GET[$notify['code']];
                }
            }
            $data['template'] = json_encode($infos);
            $result = $this->tmp->save($data);
            if (!$result) showmessage('模版设置失败！');
            showmessage('模版设置成功',U('template'),1);
        } else {
            // 获取官方短信模版
            $cloud = C('__CLOUD__');    // 获取站点绑定信息
            $_cloud = new cloud();
            $_result = $_cloud->getSmsTpl($id , $cloud['token'] , $cloud['identifier']);
            include $this->admin_tpl('template_set'); 
        }        
    }

}