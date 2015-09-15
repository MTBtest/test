<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="x-ua-compatible" content="ie=7" />
<title>提示信息</title>
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
    	<dt><em><img src="<?php echo IMG_PATH; ?>admin/ico_i.png" /></em><span>温馨提示</span></dt>
        <dd>
            <h3><?php echo($msg); ?></h3>
        <p>
        <?php if ($url_forward=='goback' || $url_forward==''): ?>
        	<?php $url_forward = 'javascript:history.back();'; ?>
        	<a href="javascript:history.back();" >如果您的浏览器没有自动跳转，请点击这里</a>
        <?php elseif ($url_forward=="close"): ?>
        	<?php $url_forward = 'javascript:window.close()'; ?>
        	<a href="javascript:;" onclick="window.close();">如果您的浏览器没有自动跳转，请点击这里</a>
        <?php else: ?>
        	<a href="<?php echo strip_tags($url_forward) ?>">如果您的浏览器没有自动跳转，请点击这里</a>
        <?php endif ?>
        </p>
        </dd>
    </dl>
<script language="javascript">
setTimeout("redirect('<?php echo strip_tags($url_forward) ?>');",<?php echo $ms ?>);
</script>
    <?php if($returnjs) { ?> <script style="text/javascript"><?php echo $returnjs;?></script><?php } ?>
    <?php if ($dialog): ?>
    <script style="text/javascript">window.top.location.reload();window.top.art.dialog({id:'<?php echo $dialog; ?>'}).close();</script>
    <?php endif ?>
    <div class="copy">
        Powered by Haidao v1.0 版权所有 © 2013-2015 迪米盒子科技有限公司，并保留所有权利。
    </div>
</div>
<script style="text/javascript">
    function close_dialog() {
        window.top.right.location.reload();
        window.top.art.dialog({id:"<?php echo $dialog?>"}).close();
    }
</script>
</body>
</html>
