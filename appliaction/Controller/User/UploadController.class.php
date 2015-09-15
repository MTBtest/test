<?php

class UploadController extends UserBaseController {

	//编辑器文件上传
	public function upload_user_avatar() {
		//图片上传路径
		$savelPath .= "./uploadfile/avatar/";
		if (!is_dir($savelPath)) dir_create($savelPath, 0777, true);
		$array = array("200", "80", "50");
		$i = 0;
		$user_id = is_login();
		if ($user_id > 0 ){
			while (list($key, $val) = each($_FILES)) {
				if ($key == '__source') {
					$initParams = $_POST["__initParams"];
					$virtualPath = "$savelPath$user_id" . ".jpg";
					$result['sourceUrl'] = '/' . $virtualPath . $initParams;
					move_uploaded_file($_FILES[$key]["tmp_name"], $virtualPath);
					$successNum++;
				} else if (strpos($key, '__avatar') === 0) {
					$virtualPath = "$savelPath" . $user_id . "_" . $array[$i] . ".jpg";
					$result['avatarUrls'][$i] = '/' . $virtualPath;
					move_uploaded_file($_FILES[$key]["tmp_name"], $virtualPath);
					$successNum++;
					$i++;
				}
			}
			$result['msg'] = $msg;
			if ($successNum > 0) {
				$result['success'] = true;
				$map['id'] = $user_id;
		        $data['ico'] = __ROOT__.'/uploadfile/avatar/'.$user_id.'_200.jpg';
		        $res = model('User')->where($map)->save($data);
			}
		}else{
			$result['msg'] = '请登录后上传头像';
			$result['success'] = false;
		}
		
		//返回图片的保存结果（返回内容为json字符串）
		print json_encode($result);
	}

	/**
    * 
    * 头像上传[wap]
    * @author 老孔
    * @date 2015-04-29
    *
    */
	public function upload_user_avatar_wap() {
		$data = array();		
		$image_path = DOC_ROOT.ltrim(str_replace(__ROOT__, '', $_POST['img_url']), '/');
		$user_id = is_login();
		if ($user_id < 1) {
			unlink($image_path);
			$result['status'] = 0;
			$result['info'] = '请登录后上传头像';
			$result['url'] = U('User/Public/login');
			exit(json_encode($result));
		}
		libfile('Image');
		$image = new Image();
		$image_info = $image::getImageInfo($image_path);//获取图片信息
		$nums = array(0 =>200, 1 => 80 , 2 => 50);
		foreach ($nums as $k => $n) {
			$thumbname = DOC_ROOT.'/uploadfile/avatar/'.$user_id.'_'.$n.'.jpg';
			$result[$k] = $image::thumb2($image_path,$thumbname,'jpg',$n,$n);
		}
		if (!array_filter($result)) {
			$data['status'] = 0;
			$data['info'] = '上传失败，请稍后再试';
		}else{
			$avatar_path = __ROOT__.'/uploadfile/avatar/'.$user_id.'_200.jpg';
			model('user')->where(array('id' => $user_id))->setField('ico',$avatar_path);
			$data['status'] = 1;
			$data['info'] = '上传成功';
		}
		unlink($image_path);
		exit(json_encode($data));
	}

}
