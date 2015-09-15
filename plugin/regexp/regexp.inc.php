<?php
defined('IN_ADMIN') or die('Access Denied');
$plugins = getcache('plugins', 'commons');
$avaliable = $plugins[$_GET['mod']]['available'];
if ($avaliable == 0){
	showmessage('请先启用插件再进行操作',U('Admin/Plugin/manage?type=2'));
}
$db = model('reg_promotion');
$info = $db->find();
$coupons = model('coupons')->field('id,name')->order('id DESC')->select();
foreach ($coupons as $k => $v) {
    $coupons[$k]['num'] = getCouponsCount($v['id'], 0);
}
if(IS_POST){
	$_POST['start_time'] = strtotime($_POST['start_time']);
	$_POST['end_time'] = strtotime($_POST['end_time']);
	if($_POST['start_time'] > $_POST['end_time']){
		showmessage('结束时间不能大于开始时间');
	}
	$db->update($_POST);
	showmessage('设置成功');
}
?>
<div class="list_order">
	<form name="form1" id="form1" action="" method="post">
	<div class="sm">
		您正在设置注册营销策略，如开启此处营销，全局设置中注册设置中的赠送设置将失效，注册赠送规则将以此处为准。
	</div>
	<dl class="gzzt clearfix mt10">
		<dt>
			营销	<br>状态
		</dt>
		<dd>
			<div class="time fl">
				<strong>促销时间：</strong>
				<input type="text" id="start" name='start_time' value='<?php echo $info['start_time'] ? date('Y-m-d H:i',$info['start_time']) : date('Y-m-d H:i') ?>' datatype="*"  style='width:120px' />
				<span>
					~
				</span>
				<input type="text" id="end" name='end_time' value='<?php echo $info['end_time'] ? date('Y-m-d H:i',$info['end_time']) : date('Y-m-d H:i',strtotime('+ 1 month')) ?>' datatype="*"  style='width:120px' />
				</select>
			</div>
			<div class="state fl">
				<strong>注册营销启用状态：：</strong>
				<label>
					<input name="status" value="0" id="RadioGroup1_0" type="radio" <?php echo $info['status']==0 ? 'checked=checked' : '' ?>>
					关闭
				</label>
				<label>
					<input name="status" value="1" id="RadioGroup1_1" type="radio" <?php echo $info['status']==1 ? 'checked=checked' : '' ?>>
					开启
				</label>
			</div>
		</dd>
	</dl>
	<div class="youhui mt10">
		<ul>
			<li class="borm">
				<strong>赠送优惠券：</strong>
				<select class="input" name="coupons_id">
					<option value="0">
						请选择一个优惠券,不选择则不赠送
					</option>
					<?php foreach ($coupons as $key => $vo): ?>
                      	<option value="<?php echo $vo['id']; ?>" <?php echo $info['coupons_id']==$vo['id'] ? 'selected' : '' ?> ><?php echo $vo['name']; ?>(<?php echo $vo['num']; ?>)</option>
                     <?php endforeach ?>
				</select>
			</li>
			<li>
				<strong>赠送积分：</strong>
				<input name="pay_points" value="<?php echo $info['pay_points']?>" class="input" type="text">
				<p>
					积分和优惠券必须选择一个或两个都选择
				</p>
			</li>
		</ul>
	</div>
	<div class="sub1 mt10">
		<a href="javascript:void(document.getElementById('form1').submit())">
			完 成
		</a>
		<a href="#">
			取 消
		</a>
	</div>
	<input type="hidden" name="id" value="<?php echo $info['id']?>">
	</form>
</div>

<!-- this should go after your </body> -->
<?php echo jsfile('hddate');?>
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