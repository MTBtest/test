<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="x-ua-compatible" content="ie=7" />
<title><?php echo C('site_name').' '.C('version')?> 系统升级</title>
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>admin/style.css" />
<!--[if IE 6]>
<script type="text/javascript" src="<?php echo JS_PATH; ?>DD_belatedPNG.js" ></script>
<script type="text/javascript">
DD_belatedPNG.fix('*');
</script>
<![endif]-->
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>common.js"></script>
</head>
<body>
<div class="content">
	<div class="" style="height: 20px;"></div>
	<dl class="tishi">
		<dt><em><img src="<?php echo IMG_PATH; ?>admin/ico_i.png" /></em><span>系统升级(此面可能会很耗时,升级中请勿关闭浏览器)</span></dt>
		<dd>
			<?php
			if (!function_exists('curl_init')){
				$ver_text = '你的服务器不支持CRUL请联系空间提供商解决此问题';
			}else{
				$r = version_compare($lastver, $ver);
				if($r == 1){
					$ver_text = '最新版本 '.$lastver.' 请升级!';
				}elseif($r == 0){
					$ver_text = '当前版本 '.$ver.' 已是最新!';
				}elseif($r == -1){
					$ver_text = '非法版本!请重新下载安装!';
				}
			}
			?>
			<h3 class="trip" style='min-height:100px' id="progress"><?php echo $ver_text?></h3>
			<?php if($r == 1):?>
			<p><a href="<?php echo U('uppack')?>" >如已确认备份文件，点击开始升级!</a></p>
			<?php endif;?>
		</dd>
	</dl>
	<?php include $this->admin_tpl('copyright') ?>
</div>
</body>
</html>
