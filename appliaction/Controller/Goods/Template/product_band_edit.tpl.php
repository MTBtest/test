<?php include $this->admin_tpl("header"); ?>
    <!-- 内容区 -->
    <div class="content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div class="site">
            Haidao Board <a href="#">商品管理</a> > 品牌列表 > 编辑品牌
        </div>
        <span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dd>
                <div class="install mt10">
                    <div class="install mt10">
                        <dl>
                            <form action="<?php echo U('ProductBand/edit'); ?>" class="addform" method="post">
                                <dd>
                                    <ul class="web">
                                        <li>
                                            <strong>名称：</strong>
                                            <input type="text" class="text_input" name="name" placeholder='' datatype="*" value="<?php echo $info['name'];?>" /><span class="Validform_checktip ">设置商品品牌名称</span>
                                        </li>
                                        <li>
                                            <strong>网址：</strong>
                                            <input type="text" class="text_input" name="url" placeholder='' datatype="url" ignore="ignore" value="<?php echo $info['url'];?>" /><span class="Validform_checktip">设置品牌链接，请填写http://</span>
                                        </li>
                                        <li style="line-height:24px;">
				                            <strong>LOGO：</strong>
				                                                
				                            <b style="width:256px;">
				                                <input value="<?php echo $info['logo']?>" type="hidden" name="logo">
				                                <img class="upimg" src="<?php echo IMG_PATH; ?>admin/input_9.png">
				                                <span style="padding-left: 0px;">
				                                	<?php	if (!empty($info['logo'])): ?>
														<img src="<?php echo $info['logo']?>" height=43>
													<?php endif ?>
				                                </span>
				                            </b><span>品牌LOGO</span>
				                        </li>
                                        <li>
                                            <strong>推荐：</strong>
                                            <input type="radio" name="isrecommend" value="1" <?php if ($info['isrecommend'] == 1): ?>checked="checked"<?php endif ?>/>是 <input type="radio" name="isrecommend" value="0" <?php if ($info['isrecommend'] == 0): ?>checked="checked"<?php endif ?>/>否<span class="Validform_checktip"  style="margin-left:253px;">是否在前台推荐该品牌</span>
                                        </li>
                                        <li>
                                            <strong>状态：</strong>
                                            <input type="radio" name="status" value="1" <?php if ($info['status'] == 1): ?> checked="checked"<?php endif ?> />显示 
                                            <input type="radio" name="status" value="0" <?php if ($info['status'] == 0): ?> checked="checked"<?php endif ?> />禁用
                                            <span class="Validform_checktip"  style="margin-left:225px;">设置品牌是否显示，默认为显示</span>
                                        </li>
                                        <li>
                                            <strong>排序：</strong>
                                            <input type="text" class="text_input" name="sort" placeholder=''  datatype="n" value="<?php echo $info['sort'];?>"/><span class="Validform_checktip" >输入数字显示排序，数字越小越靠前数字越大越靠后</span>
                                        </li>
                                        <li>
                                            <strong>描述：</strong>
                                            <textarea name="descript" rows="4" cols="20" style="margin-right: 50px;"><?php echo $info['descript'];?></textarea>
                                            <span class="Validform_checktip" style="margin-left:4px;">请填写品牌描述，品牌描述可显示在前台品牌专区页面</span>
                                        </li>
                                    </ul>
                                    <div class="submit">
                                        <input type="hidden" name="id" value="<?php echo $info['id'] ?>" />
                                         <input type="submit" class='button_search' value='提交'/>
										 <a href="<?php echo U('lists')?>">返回</a>
                                    </div>
                                </dd>
                            </form>  
                        </dl>
                    </div>
            </dd>
        </dl>
    </div>
</div>
<!--编辑器开始-->
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>Editor/themes/default/default.css">
<script type="text/javascript" src="<?php echo JS_PATH; ?>Editor/kindeditor-min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>Editor/lang/zh_CN.js"></script>
<script>
    $(function() {
		//编辑器载入
		KindEditor.ready(function(K) {
			//K.create('#content');
			editor = K.editor({
                uploadJson : '<?php echo U("Admin/Editor/upload?SSID=",'','');?>'+'<?php echo C("SSID");?>',
            	fileManagerJson : '<?php echo U("Admin/Editor/file_manage?parentdir=brand/",'','');?>',
                extraFileUploadParams: {
                     PHPSESSID : '<?php echo session_id() ?>',
                     parentdir : 'brand/'
                },
                allowFileManager: true
            });
			//给按钮添加click事件
			$('.upimg').live('click', function() {
				var self = $(this);
				editor.loadPlugin('image', function() {
					//图片弹窗的基本参数配置
					editor.plugin.imageDialog({
						imageUrl: self.prev('input').val(), //如果图片路径框内有内容直接赋值于弹出框的图片地址文本框
						//点击弹窗内”确定“按钮所执行的逻辑
						clickFn: function(url, title, width, height, border, align) {
							self.prev("input").val(url);
							self.next("span").html("<img src=" + url + " height=43>");
							editor.hideDialog(); //隐藏弹窗
						}
					});
				});
			});
		});
		//表单验证
        var demo = $(".addform").Validform({
            btnSubmit: "#btn_sub",
            btnReset: ".btn_reset",
            tiptype: 3,
            label: ".label",
            showAllError: false,
            ajaxPost: true,
            callback: function(data) {
                $("#Validform_msg").hide();
                if (data.status == "0") {
                    art.dialog({width: 320, time: 5, title: '温馨提示(5秒后关闭)', content: data.info, ok: true});
                }
                if (data.status == "1") {
                    window.location.href = '<?php echo U('lists') ?>';
                }
            },
            tiptype:function(msg,o,cssctl){
            },
        });
    });
</script>
</body>
</html>