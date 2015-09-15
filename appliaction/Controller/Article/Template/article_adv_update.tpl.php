<?php include $this -> admin_tpl('header'); ?>
<div class="content">
	 <div class="site">
        Haidao Board <a href="#">内容管理</a> > 添加广告
    </div>
    <span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dt><a href="javascript:void(0)" class="hover">添加广告</a><a href="<?php echo U('Article_adv/lists') ?>">所有广告</a></dt>
            <form class="addform" name="addform" action="<?php echo U('Article_adv/update') ?>" method="post" autocomplete="off">
                <dd>
                    <ul class="webad">
                        <li>
                            <strong>广告标题：</strong>
                            <input type="text" name='title' datatype="*" class="text_inputAd" value='<?php echo $info["title"] ?>' style="margin-right: 50px; color:#3f3f3f"><span>广告标题：为设置的广告制定一个标题，广告标题只为识别辨认不同广告条目之用，并不在广告中显示</span>
                        </li>
                        <li>
                            <strong>广告起始时间：</strong>
                            <input type="text" value="<?php echo isset($info["starttime"])?date('Y-m-d H:i:s', $info["starttime"]):date('Y-m-d H:i:s',NOW_TIME)?>" name="starttime" class="time_input" id="start" style="width:225px;margin-right: 50px;">
                            <span>设置广告起始生效的时间，格式 yyyy-mm-dd，留空为不限制起始时间</span>
                        </li>
                        <li>
                            <strong>广告结束时间：</strong>
                            <input type="text" value="<?php echo isset($info["endtime"])?date('Y-m-d H:i:s', $info["endtime"]):date('Y-m-d H:i:s', time()+30*86400)?>" name="endtime" class="time_input" id="end" style="width:225px;margin-right: 50px;">
                            <span>设置广告广告结束的时间，格式 yyyy-mm-dd，留空为不限制结束时间</span>
                        </li>
                        <li>
                            <strong>所属广告位：</strong>
                            <b style="margin-right: 52px;"><?php echo $adv_position['name']?$adv_position['name']:$info['position_name'] ?></b>
                            <span>设置本广告投放的页面，全局是指全站投放，部分广告位不能设置部分投放范围</span>
                        </li>
                        <li>
                            <strong>展现方式：</strong>
                            <b style="margin-right:52px;">
                                <label><input type="radio" name="type" value="1" <?php if($info['type'] == 1):?> checked <?php endif?>> 图片 </label>
                                <label><input type="radio" name="type" value="2" <?php if($info['type'] == 2):?> checked <?php endif?>> 文字 </label>
                                <label><input type="radio" name="type" value="3" <?php if($info['type'] == 3):?> checked <?php endif?>> 代码 </label>
                            </b>
                            <span>请选择所需的广告展现方式</span>
                        </li>
						<li>
                                <div id='ad_box'>
                                <div style='display:<?php if($info['type'] == 1):?>block<?php else:?>none<?php endif?>'>
                                <strong>图片地址（必填）：</strong>
                                    <input type="text" name="img" class="text_inputAd"   style="margin-right: -25px; color:#3f3f3f" value="<?php if($info["type"]==1) echo $info['content'] ?> " /><span class="upimg" style="cursor:pointer" >选择</span><?php if($info['content']){?><span><img height="30" src="<?php echo $info['content']?>"></span><?php }else{?><span>请输入图片广告的图片调用地址</span><?php }?>
                                </div>
                                <div style='display:<?php if($info['type'] == 2):?>block<?php else:?>none<?php endif?>'>
                                <strong>文字内容（必填）：</strong>
                                    <input type="text" name="text"  class="text_inputAd" style="margin-right: 50px; color:#3f3f3f" value="<?php if($info["type"]==2) echo $info['content'] ?>" /><span>请输入文字广告的内容</span>
                                </div>
                                <div style='display:<?php if($info['type'] == 3):?>block<?php else:?>none<?php endif?>'>
                                <strong>代码内容（必填）：</strong>
                                    <textarea class='textarea' style="margin-right: 50px; color:#3f3f3f" name='code'><?php if($info["type"]==3) echo $info['content'] ?></textarea><span>请输入代码内容</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div id='ad_box2'>
                                <div style="display:<?php if($info['type'] == 1):?>block<?php else:?>none<?php endif?>" >
                                         <strong>图片链接（必填）：</strong>
                                         <input type="text" name="ilink" style="margin-right: 45px; color:#3f3f3f" class="text_inputAd" value="<?php if($info["type"]==1) echo $info['link'] ?>" /> <span>请输入图片广告指向的url链接地址</span>
                                </div>
                                <div style="display:<?php if($info['type'] == 2):?>block<?php else:?>none<?php endif?>" >
                                         <strong>文字链接（必填）：</strong>
                                         <input type="text" name="tlink" style="margin-right: 45px; color:#3f3f3f" class="text_inputAd" value="<?php if($info["type"]==2) echo $info['link'] ?>" /> <span>请输入文字广告指向的url链接地址</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="submit">
                         <?php if(!empty($info)){ ?>
                            <input type="hidden" value="<?php echo $info['id'] ?>" name="id" />
                            <input type="hidden" value="edit" name="opt" />
                    	<?php }else{ ?>
                            <input type="hidden" value="add" name="opt" />
                            <input type="hidden" name="position_id" value="<?php echo $adv_position['id'] ?>">
                        <?php }; ?>
                            <input type="submit" class="button_search" value="提交"/>
							<a href="<?php echo U('lists')?>">返回</a>
                    </div>
                </dd>
            </form>
        </dl>
    </div>
    <?php include $this->admin_tpl('copyright'); ?>
</div>
    <!--编辑器开始-->
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>Editor/themes/default/default.css">
<script type="text/javascript" src="<?php echo JS_PATH; ?>Editor/kindeditor-min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>Editor/lang/zh_CN.js"></script>
    <!--日期控件-->
	<?php echo jsfile('hddate');?>
	<?php echo jsfile('hdvalid');?>
	<script>
	$(function() {
		var start = {
		    elem: '#start',
		    format: 'YYYY-MM-DD hh:mm:ss',
		    //min: laydate.now(), //设定最小日期为当前日期
		    max: '2099-06-16 23:59:59', //最大日期
		    istime: true,
		    istoday: true,
		    choose: function(datas){
		         end.min = datas; //开始日选好后，重置结束日的最小日期
		         end.start = datas //将结束日的初始值设定为开始日
		    }
		};
		var end = {
		    elem: '#end',
		    format: 'YYYY-MM-DD hh:mm:ss',
		    min: laydate.now(),
		    max: '2099-06-16 23:59:59',
		    istime: true,
		    istoday: true,
		    choose: function(datas){
		        start.max = datas; //结束日选好后，重置开始日的最大日期
		    }
		};
		laydate(start);
		laydate(end);
	});
	</script>
    <script>
        //单选按钮点击绑定
        $('input:radio[name="type"]').each(
            function(i){
                $(this).click(function(){
                $('#ad_box>div').hide();
                   $('#ad_box>div:eq(' + i + ')').show();
                });
           }
        );
        $('input:radio[name="type"]').each(
            function(i){
                $(this).click(function(){
                $('#ad_box2>div').hide();
                   $('#ad_box2>div:eq(' + i + ')').show();
                });
           }
        );
        $(function() {
        $("#Validform_msg").hide();
            //编辑器载入
        KindEditor.ready(function(K){
            //K.create('#content');
            editor = K.editor({
            uploadJson : '<?php echo U("Admin/Editor/upload?SSID=",'','');?>'+'<?php echo C("SSID");?>',
                fileManagerJson : '<?php echo U("Admin/Editor/file_manage?parentdir=article/",'','');?>',
                extraFileUploadParams: {
                PHPSESSID : '<?php echo session_id() ?>',
                    parentdir : 'adv/'
                },
                allowFileManager: true
            });
            //给按钮添加click事件
            $('.upimg').live('click', function() {
                var self = $(this);
                editor.loadPlugin('image', function() {
                //图片弹窗的基本参数配置
                    editor.plugin.imageDialog({
                    imageUrl : self.prev("input").val(), //如果图片路径框内有内容直接赋值于弹出框的图片地址文本框
                        //点击弹窗内”确定“按钮所执行的逻辑
                        clickFn : function(url, title, width, height, border, align) {
                        self.prev("input").val(url);
                            self.next("span").html("<img src=" + url + " height=30>");
                            editor.hideDialog(); //隐藏弹窗
                        }
                    });
                });
            });
            //给按钮添加click事件
            $('.upfla').live('click', function() {
                var self = $(this);
                //图片弹窗的基本参数配置
                editor.loadPlugin('insertflash', function() {
                    editor.plugin.fileDialog({
                        fileUrl : K('#url').val(),
                        viewType : 'LIST',
                        dirName : 'flash',
                        clickFn : function(url, title) {
                                self.prev("input").val(url);
                                editor.hideDialog();
                        }
                    });
                });
            });
        });
        //表单验证
        var demo = $(".addform").Validform({
        btnSubmit: "#btn_sub",
            btnReset: ".btn_reset",
            tiptype:function(msg, o, cssctl){
                var e = o.obj.context.name;
                if (e.length > 1 && o.type == 3){
                    if (e == 'content'){
                        alert(msg);
                    }
                }
            },
            showAllError: false
        });
    });
    </script>