<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates 
 * and open the template in the editor.
 */
class UploadController extends BaseController
{
    public function _initialize() {
        parent::_initialize();
    }
    
    /**
     * 附件上传
     */
    public function upload() {
    }
    
    public function swfupload() {
        libfile('upload');
        // (可自定义)文件夹名
        $file_name = $_GET['file_name'] ? $_GET['file_name'] : 'image';
        $config = array("pathFormat" => 'uploadfile/'.$file_name.'/{yyyy}{mm}{dd}/{time}{rand:6}',"maxSize" => 2048000,"allowFiles" => array(".png", ".jpg", ".jpeg", ".gif", ".bmp"));
        $fieldName = 'upfile'; 
        $up = new upload($fieldName, $config, $base64);
        $this->ajaxReturn($up->getFileInfo());
    }

    public function create_thumb(){
        $image_path = DOC_ROOT.ltrim(str_replace(__ROOT__, '', $_POST['img_url']), '/');
        libfile('Image');
        $info = Image::getImageInfo($image_path);
        $image_type = $info['type'];
        $data = array();
        $nums = array(0 => explode(',', getconfig('site_thumbnaillist')),1 => explode(',', getconfig('site_thumbnailProduct')));
        foreach ($nums as $k => $v) {
            $_thumb_path = 'uploadfile/image/'.date('Ymd',time()).'/'.time().rand(100000,999999);
            $_thumb_suffix = '_'.$k.'.'.$image_type;
            $thumb_name = $_thumb_path.$_thumb_suffix;
            $result[$k] = Image::thumb2($image_path,DOC_ROOT.$thumb_name,$image_type,$v[0],$v[1]);
            if($k == 0){
                $data['thumb'] = __ROOT__.'/'.$_thumb_path.'_0.'.$image_type;
            }else{
                 $data['small_pics'] = __ROOT__.'/'.$_thumb_path.'_1.'.$image_type;
            }
        }
       if(array_filter($result)){
            $data['status'] = 1;
            $data['info'] = '缩略图生成成功';
        }else{
            $data['status'] = 0;
            $data['info'] = '缩略图生成失败，请检查图片格式或环境配置是否正确'; 
        }
        exit(json_encode($data));
    }
        
    public function ueditor() {
        $base64 = "upload";
        libfile('upload');
        $ueditor_path = DOC_ROOT.  str_replace(__ROOT__, '', JS_PATH) . 'ueditor/';
        $config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents( $ueditor_path . "config.json")), TRUE);
        if($_GET['action'] == 'config') {
            $this->ajaxReturn($config);
        } elseif($_GET['action'] == 'catchimage') {
            $fileConfig = array(
                "pathFormat" => $config['catcherPathFormat'],
                "maxSize" => $config['catcherMaxSize'],
                "allowFiles" => $config['catcherAllowFiles'],
                "oriName" => "remote.png"
            );
            $fieldName = $config['catcherFieldName'];
            $lists = array();
            foreach ($_GET[$fieldName] as $imgUrl) {
                $item = new upload($imgUrl, $fileConfig, "remote");
                $info = $item->getFileInfo();
                array_push($lists, array(
                    "state" => $info["state"],
                    "url" => $info["url"],
                    "source" => $imgUrl
                ));
            }
            $result = array();
            $result['list'] = $lists;
            $result['state'] = (count($lists)) ? 'SUCCESS' : 'ERROR';
            $this->ajaxReturn($result);
        } else { 
            switch ($_GET['action']) {
                /* 上传图片 */
                case 'uploadimage':
                    $fileConfig = array(
                        "pathFormat" => $config['imagePathFormat'],
                        "maxSize" => $config['imageMaxSize'],
                        "allowFiles" => $config['imageAllowFiles']
                    );
                    $fieldName = $config['imageFieldName'];
                    break;
                /* 上传涂鸦 */
                case 'uploadscrawl':
                    $fileConfig = array(
                        "pathFormat" => $config['scrawlPathFormat'],
                        "maxSize" => $config['scrawlMaxSize'],
                        "allowFiles" => $config['scrawlAllowFiles'],
                        "oriName" => "scrawl.png"
                    );
                    $fieldName = $config['scrawlFieldName'];
                    $base64 = "base64";
                    break;
                /* 上传视频 */
                case 'uploadvideo':
                    $fileConfig = array(
                        "pathFormat" => $config['videoPathFormat'],
                        "maxSize" => $config['videoMaxSize'],
                        "allowFiles" => $config['videoAllowFiles']
                    );
                    $fieldName = $config['videoFieldName'];
                    break;
                case 'listimage':
                case 'listfile':
                    if($_GET['action'] == 'listfile') {
                        $allowFiles = $config['fileManagerAllowFiles'];
                        $listSize = $config['fileManagerListSize'];
                        $path = $config['fileManagerListPath'];
                    } else {
                        $allowFiles = $config['imageManagerAllowFiles'];
                        $listSize = $config['imageManagerListSize'];
                        $path = $config['imageManagerListPath'];
                    }
                    $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
                    $size = isset($_GET['size']) ? $_GET['size'] : $listSize;
                    $start = isset($_GET['start']) ? $_GET['start'] : 0;
                    $end = $start + $size;
                    
                    $path = DOC_ROOT.(substr($path, 0, 1) == "/" ? "":"/") . $path;
                    $files = $this->getfiles($path, $allowFiles);
                    if (!count($files)) {
                        return json_encode(array(
                            "state" => "no match file",
                            "list" => array(),
                            "start" => $start,
                            "total" => count($files)
                        ));
                    }
                    $len = count($files);
                    for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
                        $list[] = $files[$i];
                    }
                    $result = array(
                        "state" => "SUCCESS",
                        "list" => $list,
                        "start" => $start,
                        "total" => count($files)
                    );
                    $this->ajaxReturn($result);
                    break;
                default:
                    $fileConfig = array(
                        "pathFormat" => $config['filePathFormat'],
                        "maxSize" => $config['fileMaxSize'],
                        "allowFiles" => $config['fileAllowFiles']
                    );
                    $fieldName = $config['fileFieldName'];
                    break;
            }
            $up = new upload($fieldName, $fileConfig, $base64);
            $this->ajaxReturn($up->getFileInfo());
        }
    }

    private function getfiles($path, $allowFiles, &$files = array())
        {
            if (!is_dir($path)) return null;
            if(substr($path, strlen($path) - 1) != '/') $path .= '/';
            $handle = opendir($path);
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    $path2 = $path . $file;
                    if (is_dir($path2)) {
                        $this->getfiles($path2, $allowFiles, $files);
                    } else {
                        if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                            $files[] = array(
                                'url'=> str_replace(DOC_ROOT, __ROOT__, $path2),
                                'mtime'=> filemtime($path2)
                            );
                        }
                    }
                }
            }
            return $files;
        }
}