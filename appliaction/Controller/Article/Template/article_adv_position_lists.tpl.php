<?php include $this -> admin_tpl('header'); ?>
<div class="content">
	<div class="site">
	    Haidao Board <a href="#">内容管理</a> > 站点广告
	</div>
	<span class="line_white"></span>
    <p class="sitep">海盗云商内置了多种广告模板，以下广告位蓝色区域为广告位区域，同一个广告模板下如果有多个广告，会随机展示其中一个广告。</p>
   	<div class="install mt10">
    	<dl>
        	<dt><a href="javascript:void(0)" class="hover">广告位</a><a href="<?php echo U('Article_adv/lists') ?>">所有广告</a></dt>
            <dd>
                <div class="ad">
                	<ul class="clearfix">
                            <?php $i=1;foreach ($list as $vo):?>
                                <?php if($i <= 7) :?>
                                    <li>
                                        <a href="<?php echo U('Article_adv/lists?pos_id='.$vo['id'].'') ?>" ><img src="<?php echo IMG_PATH?>admin/ph_<?php echo $i ?>.png" width="160" height="160" alt="" /></a>
                                        <p><span>系统广告：<?php echo $vo['name'] ?></span>
                                            <a href="<?php echo U('Article_adv/update?opt=add&pos_id='.$vo['id'].'') ?>">添加</a>
                                            <a href="<?php echo U('Article_adv/lists?pos_id='.$vo['id'].'') ?>">列表</a>
                                        </p>
                                    </li>
                                <?php else:?>
                                    <li>
                                        <a href="javascript:void(changename(<?php echo $vo["id"] ?>))" title="点击这里可以编辑标题"><img src="<?php echo IMG_PATH?>admin/ph_custom.jpg" width="160" height="160" alt="" /></a>
                                        <p><span><input class="text_inputAd" name='name[<?php echo $vo["id"] ?>]' placeholder='广告位名称' datatype="*" style='width:160px;' value="<?php echo $vo['name'] ?>" type="text"/></span>
                                            <a href="<?php echo U('Article_adv/update?opt=add&pos_id='.$vo['id'].'') ?>">添加</a>
                                            <a href="<?php echo U('Article_adv/lists?pos_id='.$vo['id'].'') ?>">列表</a>
                                            <a url="<?php echo U('Article_adv_position/update?opt=del&id='.$vo['id']) ?>" class="ajax-get confirm">删除</a>
                                        </p>
                                    </li>
                                <?php endif?>
                            <?php $i++;endforeach;?>
                            <form action="<?php echo U('ArticleAdvPosition/update?opt=add') ?>" class="addform" name='addform' method="post">
                        <li>
                            <a href="javascript:void(demo.submitForm(false))"  title='点我可以添加广告位'><img src="<?php echo IMG_PATH?>admin/ph_8.png" width="160" height="160"  /></a>
                            <p><input class="text_inputAd" name='name' placeholder='广告位名称' datatype="*" style='width:160px;' type="text"/></p>
                        </li>
                        </form>
                    </ul>
                </div>
            </dd>
        </dl>
    </div>
    <?php include $this->admin_tpl('copyright'); ?>
</div>
<script>
    var demo;
    $(function(){
        //表单验证
        demo = $(".addform").Validform({
            btnSubmit: "#btn_sub",
            btnReset: ".btn_reset",
            tiptype:function(msg,o,cssctl){
                var e=o.obj.context.name;
                if (e.length>1 && o.type==3){
                    if (e=='content'){
                        alert(msg);
                    }
                }
            },
            showAllError: false
        });
    })
    function changename(id){
        var name=$("input[name='name["+id+"]']").val();
        if(!name){
            $("input[name='name["+id+"]']").focus();
            alert('名称不允许为空');
            return;
        }
        $.post("{:U('Article_adv_position/update?opt=edit')}",{"id":id,"name":name},function(data){
            if (data.status == 1){
                location.reload(true);
            }else{
                alert(data.info);
                return;
            }
        })
    }
</script>