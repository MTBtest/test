<?php
include $this -> admin_tpl('header');
?>
<div class="content">
<div class="site">
	Haidao Board
	<a href="#">
		内容管理
	</a> > 编辑帮助
</div>
 <span class="line_white"></span>
    <div class="install mt10">
		<form class="addform" name="addform" action="<?php echo U('GoodsCoupons/update')?>" method="post" autocomplete="off">
			<div class="list_order">
				<div class="vip_ss">
					<p>
						<strong style="color:#636363;">规则名称：</strong>
						<input type="text" name='name' value='<?php echo $info["name"]?>' placeholder="输入一个订单促销规则名称" datatype="*" class="input">
					</p>
					<div class="vip_ss_fa clearfix">
						<strong>规则描述：</strong>
						<textarea name="descript" ><?php echo $info['descript']?></textarea>						
						<font>规则描述信息将在购物车显示! 为空则显示规则名称</font>
					</div>
				</div>
				<dl class="gzzt clearfix mt10">
					<dt>
						规则
						<br />
						状态
					</dt>
					<dd>
						<div class="time fl">
							<strong>促销时间：</strong>
							<input type="text" id="start" name='start_time' value='<?php echo isset($info["start_time"])?date('Y-m-d H:i:s', $info["start_time"]):date('Y-m-d H:i:s',NOW_TIME)?>' datatype="*"  style='width:120px' />
							<!--<select><option>00</option></select><span>:</span><select><option>00</option></select>-->
							<span>
								~
							</span>
							<input type="text" id="end" name='end_time' value='<?php echo isset($info["end_time"])?date('Y-m-d H:i:s', $info["end_time"]):date('Y-m-d H:i:s', time()+30*86400)?>' datatype="*"  style='width:120px' />
							</select>
						</div>
						<!--                <div class="state fl">
						<strong>促销启用状态：</strong>
						<label><input type="radio" name="RadioGroup30" value="关闭" id="RadioGroup1_0"> 关闭 </label>
						<label><input type="radio" name="RadioGroup30" value="开启" id="RadioGroup1_1"> 开启 </label>
						</div>-->
					</dd>
				</dl>
				<div class="vip_ss mt10">
					<div class="vip_ss_fa clearfix">
						<strong>优惠规则：</strong>
						<ul>
							<li>
								<span>订单金额满</span>
								<input type="text" name='limit' value=' <?php echo $info["limit"]?$info["limit"]:100 ?>' datatype="num"  class="input">
								元可使用
							</li>
							<li>
								<span>个人积分满</span>
								<input type="text" name='integral' value=' <?php echo $info["integral"]?$info["limit"]:100 ?>' datatype="n" class="input">
								<?php echo C('site_integralname')?>可兑换
							</li>
						</ul>
						<font>请先选择优惠条件，再配置优惠规则</font>
					</div>
				</div>
				<div class="vip_ss mt10">
					<div class="vip_ss_fa clearfix">
						<strong>生成规则：</strong>
						<ul>
							<li>
								<span>单张金额</span>
								<input type="text" name="value" datatype="num" value=' <?php echo $info["value"]?$info["value"]:5?>' class="input">
								元
							</li>
						</ul>
						<font>请先选择优惠条件，再配置优惠规则</font>
					</div>
				</div>
				<div class="sub1 mt10">
					<notempty name="info">
						<input type="hidden" value="<?php echo $info['id']?>" name="id" />
						<input type="hidden" value="edit" name="opt" />
						<else />
						<input type="hidden" value="add" name="opt" />
					</notempty>
					<a href="javascript:void(demo.submitForm(false))">
						完 成
					</a>
					<a href="<?php echo U('GoodsCoupons/lists')?>">
						取 消
					</a>
				</div>
			</div>
		</form>
	</div>
	<?php include $this -> admin_tpl("copyright"); ?>
</div>
 <!--时间选择-->
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
        var demo;
        $(function() {
            $("#Validform_msg").hide();
            //表单验证
            demo = $(".addform").Validform({
                tiptype: function(msg, o, cssctl) {
                    var e = o.obj.context.name;
                    if (e.length > 1 && o.type == 3) {
                        if (e == 'message') {
                            alert(msg);
                        }
                    }
                },
                showAllError: false
            });
        });
    </script>