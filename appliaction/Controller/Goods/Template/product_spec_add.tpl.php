<?php include $this->admin_tpl("header"); ?>
<style>
#Validform_msg{display: none}
.operator{cursor: pointer;padding:10px;}
.spec_input{margin:10px;}
img{cursor: pointer}
</style>
<div class="content">
    <div id="top-alert" class="fixed alert alert-error" style="display: none;">
        <button class="close fixed" style="margin-top: 4px;">&times;</button>
        <div class="alert-content">这是内容</div>
    </div>
    <div class="site">
        Haidao Board <a href="#">商品管理</a> > 规格列表 > 添加规格
    </div>
    <span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="<?php echo U('add')?>" class="addform" method="post">
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>规格名称：</strong>
                                            <input type="text" class="text_input" name="name" placeholder='' datatype="s"/><span class="Validform_checktip " style="padding-left: 43px;">设置商品规格名称　如：颜色
                                            </span>
                                        </li>
                                        <li>
                                            <strong>状态：</strong>
                                            <input type="radio" name="status" value="1" checked="checked" /> 显示 <input type="radio" name="status" value="0"  /> 禁用<span class="Validform_checktip" style="margin-left:214px;">设置商品规格是否显示，默认为显示</span>
                                        </li>
                                    </ul>
                                    <dl class="blue_table mt10">
                                        <dt style="height:42px;"><img src="<?php echo IMG_PATH;?>admin/spec_add.png" id="specAddButton" style="padding:10px;float:left"><span class="add" style="float:left;line-height: 44px;">点击按钮添加商品规格，多个规格可使用规格右侧“上移”“下移”调整规格顺序。</span></dt>
                                        <dd>
                                                <table>
                                                <tbody id='spec_box'></tbody></table>
                                        </dd>
                                    </dl>
                                    <div class="submit">
                                        <input type="submit" class='button_search' value='提交'/>
										<a href="<?php echo U('lists')?>">返回</a>
                                    </div>
                                </dd>
                            </form>  
                        </dl>
                    </div>
            </dd>
        </dl>
        <?php include $this->admin_tpl('copyright'); ?>
    </div>
</div>
</body>
    <!--编辑器开始-->
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>Editor/themes/default/default.css">
    <script type="text/javascript" src="<?php echo JS_PATH; ?>Editor/kindeditor-min.js"></script>
    <script type="text/javascript" src="<?php echo JS_PATH; ?>Editor/lang/zh_CN.js"></script>
    <script>
    KindEditor.ready(function(K) {
        var editor = K.editor({
            uploadJson : '<?php echo U("Admin/Editor/upload?SSID=",'','')?>'+'<?php echo C("SSID")?>',
            fileManagerJson : '<?php echo U("Admin/Editor/file_manage?parentdir=spec/",'','')?>',
            extraFileUploadParams: {
                 PHPSESSID : '<?php echo session_id() ?>',
                 parentdir : 'spec/'
            },
            allowFileManager : true //允许图片管理 开启后再挑选图片的时候可以直接从图片空间内挑选
        });
        //给按钮添加click事件
        $('.upimg').live('click', function() {
            var self=$(this);
            editor.loadPlugin('image', function() {
                //图片弹窗的基本参数配置
                editor.plugin.imageDialog({
                    imageUrl : self.prev("input").val(), //如果图片路径框内有内容直接赋值于弹出框的图片地址文本框
                    //点击弹窗内”确定“按钮所执行的逻辑
                    clickFn : function(url, title, width, height, border, align) {
                        self.prev("input").val(url);
                        self.after("<img src="+url+" height=43>");
                        editor.hideDialog(); //隐藏弹窗
                    }
                });
	   });
        });
    });
    </script>
    <!--编辑器结束-->
<script>
//添加规格按钮(点击绑定)
$('#specAddButton').click(function(){
	var specSize = $('#spec_box tr').size();
	var specRow = getTr(specSize);
	$('#spec_box').append(specRow);
	initButton(specSize);
})
//根据显示类型返回格式
function getTr(indexValue) {
	//规格图片格式
	var specInputHtml = getValHtml();
	//数据
	var specRow = '<tr class="td_c"><td  width="350" align="left" height="35"> '+specInputHtml+'</td>'
	+'<td align="right"><span  class="operator">向上</span> '
	+'<span  class="operator">向下</span> '
	+'<span  class="operator">删除</span> '
	+'</td></tr>';
	return specRow;
}
//规格值html
function getValHtml(dataValue) {
	if(dataValue == undefined)
	dataValue = '';
	return '<input class="text_input spec_input" type="text" name="value[]" value="'+(dataValue ? dataValue :"")+'" datatype="s" alt="填写规格值" />';
}
        //按钮(点击绑定)
function initButton(indexValue) {
	//上传图片按钮
	$('#spec_box tr:eq('+indexValue+') td img').click(function(){
	});
	//功能操作按钮
	$('#spec_box tr:eq('+indexValue+') .operator').each(function(i){
		switch(i){
		//向上排序
			case 0:
				$(this).click(function(){
					var insertIndex = $(this).parent().parent().prev().index();
					if(insertIndex >= 0){
						$('#spec_box tr:eq('+insertIndex+')').before($(this).parent().parent());
					}
				})
				break;
		//向下排序
		case 1:
			$(this).click(function(){
				var insertIndex = $(this).parent().parent().next().index();
				$('#spec_box tr:eq('+insertIndex+')').after($(this).parent().parent());
			})
			break;
		// 删除排序
		case 2:
			$(this).click(function() {
				var obj = $(this);
				art.dialog({width: 320,id:'spec_del',title:'温馨提示',content: '确定要删除么？',ok:function(){obj.parent().parent().remove()},cancel:true});
			})
			break;
		}
	})
}
$(function() {
    var demo = $(".addform").Validform({
        btnSubmit: "#btn_sub",
        btnReset: ".btn_reset",
        tiptype: function(msg,o,cssctl){},
        label: ".label",
        showAllError: false,
        ajaxPost: true,
        callback: function(data) {
            $("#Validform_msg").hide();
            if (data.status == "0") {
                art.dialog({width: 320, time: 5, title: '温馨提示(5秒后关闭)', content: data.info, ok: true});
            } else {
                //art.dialog.confirm(data.info, function () {
                    window.location.href = data.url;
                //});     
            }
        }
    });
});
</script>
</html>