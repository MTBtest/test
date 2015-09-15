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
            <h3 class="trip" id="progress">连接服务器!</h3>
        <p>
        </dd>
    </dl>
    <?php include $this->admin_tpl('copyright') ?>
</div>
<script style="text/javascript">
	showInfo('正在连接服务器...',0);
	function showInfo(_text,_status){
		$('.trip').html(_text);
	}
</script>
<script type="text/javascript">
function updateProgress(percentage, dlnow, dltotal) {
	var progressElement = document.getElementById('progress')
//	progressElement.style.width = Math.round(percentage * 200) + "px";
	progressElement.innerHTML ='正在下载'+ Math.round(percentage * 100) + '% (' + dltotal + '/' + dlnow + ')';
}
</script>
<script type="text/javascript">
		var _maq = _maq || [];
		_maq.push(['_setAccount', '<?php echo getconfig('site_name')?>']);
		_maq.push(['_setAction', 'uppack']);
		_maq.push(['_setVersion', '<?php echo getconfig('version')?>']);
		_maq.push(['_setEmail', '<?php echo session('ADMIN_EMAIL'); ?>']);
		(function() {
	    var ma = document.createElement('script'); ma.type = 'text/javascript'; ma.async = true;
	    ma.src = ('https:' == document.location.protocol ? 'https://cloud.haidao.la' : 'http://cloud.haidao.la') + '/ma.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ma, s);
	})();
	</script>
<?php
	$this->downfile();
?>
</body>
</html>
