<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>快递单打印</title>
    <script type="text/javascript" src="<?php echo JS_PATH; ?>jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="<?php echo JS_PATH; ?>jquery.print.js"></script>
    <style type="text/css">
    .button_search{
        background-image: -webkit-linear-gradient(top,#ffffff,#e6e6e6);
        background-image: -moz-linear-gradient(top,#ffffff,#e6e6e6);
        background-image: -o-linear-gradient(top,#ffffff,#e6e6e6);
        border: 1px solid #c7c7c7;
        cursor: pointer;
        width: 80px;
        height: 26px;
        line-height: 24px;
        margin-top: 20px;
        margin-bottom: 20px;
    }
</style>
</head>
<body>
<div id="tool" style="text-align:center;">
    <input type='button' class='button_search' onclick="printStart();" value='开始打印' /> &nbsp;&nbsp;
    <input type='button' class='button_search' onclick='window.history.go(-1);' value='返回上一级' />
</div>
<div id="content" style="position: relative;background:url(<?php echo IMG_PATH;?>delivery_express/<?php echo $content['background'];?>.jpg);width:<?php echo $content['width']?>px; height:<?php echo $content['height']; ?>px;">
    <?php foreach ($content['list'] as $key => $val): ?>
        <div style="position: absolute;width:<?php echo $val['width'] ?>; height:<?php echo $val['height']; ?>px; left:<?php echo $val['left'] ?>px; top: <?php echo $val['top'] ?>px;"><?php echo $val['txt'] ?></div>
    <?php endforeach ?>
</div>
<script type="text/javascript">
function printStart() {
    $("#content").jqprint();
}
</script>
</body>
</html>