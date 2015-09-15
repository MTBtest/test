<?php include $this -> admin_tpl("header"); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">商品信息导入</a> 
    </div>
     <div class="line_white"></div>
  	<div class="install mt10">
    <form id="addform" action="<?php echo U('import?opt=import')?>" method="post" enctype="multipart/form-data"> 
            	<div class="sm" style='height:100px;line-height: 20px;'>
            		<strong>使用说明:</strong>
            		<li>1.请保留csv<font color="red">标题行</font>;</li>
            		<li>2.请控制csv文件大小在<font color="red">2m以下</font>,如超过建议分成多次导入;</li>
            		<li>3.<font color="red">商品分类、商品品牌，只能填写已经建好资料的ID，如不知道ID,请填写0;</font></li>
            		<li>4.如商品名称与已有商品名称<font color="red">重复则跳过</font>此条;</li>
            	</div>
            	<ul class="web">
                    <li>
                    	<div class="shuju">
                            <strong>第一步 下载数据模板(csv文件)：</strong>
                            <div style="margin-left: 40px;">
                            	<img  src="<?php echo IMG_PATH; ?>admin/ico_29.png" />
                            </div>
                            <a href='./goods_import.csv' style="display: inline-block;width: 438px;" ><img  src="<?php echo IMG_PATH; ?>admin/input_5.png" /></a><span>数据模板格式为csv文件，使用Excel表格打开并填写商品信息</span>
                    	</div>
                    </li>
                    <li>
                    	<div class="shuju">
                    		<strong></strong>
                    		<div style="margin-left: 40px;">
                            	<img  src="<?php echo IMG_PATH; ?>admin/ico_29.png" />
                            </div>
                            <strong style="margin-bottom: 5px;">第二步 选择文件(csv文件)：</strong>
                            <input type="file" name="file" style="margin-left: 70px;" /> <span style="padding-left:42px;">上传填写完毕的商品信息数据模板，完成数据批量导入</span>
                    	</div>
                    </li>
                    <li>
                    	<div class="shuju">
                    		<strong></strong>
                    		<div style="margin-left: 40px;margin-bottom: 10px;">
                            	<img  src="<?php echo IMG_PATH; ?>admin/ico_29.png" />
                            </div>
                            <strong></strong>
                            <input type="submit" class="btn button_search import_01" value="开始导入" /><span></span>
                    	</div>
                    </li>
                </ul>
	</form> 
    </div>
    <script type="text/javascript">
		$(function(){
			//默认高亮
			$(window.parent.document).find(".z_side").removeClass("hover");
			$(window.parent.document).find(".n17").addClass("hover");
		})
	</script>
<?php include $this -> admin_tpl("copyright"); ?>