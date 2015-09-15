<?php include $this->admin_tpl("header"); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">统计报表</a> > 商品数据分析
    </div>
    <div class="line_white"></div>
	<dl class="time_ss mt10" id="J-search-date-container">
    	<dt>
        	<strong>自定义时间段：</strong>
            <form action="<?php echo __APP__ ?>" style="display: inline-block;" onsubmit="return checkform()">
            <input type="hidden" name="m" value="<?php echo GROUP_NAME ?>">
            <input type="hidden" name="c" value="<?php echo MODULE_NAME ?>">
            <input type="hidden" name="a" value="<?php echo ACTION_NAME ?>">
            <input type="hidden" name="days" value="-2">
            <input type="text" name="start_time" id="start" value="<?php echo $_GET['start_time']; ?>" tabindex="1" class="date-search-input time_input2   sDatepicker " autocomplete="off" /> - <input type="text" name="end_time" id="end" value="<?php echo $_GET['end_time'] ?>" tabindex="2" class="date-search-input time_input2   sDatepicker " autocomplete="off" />
            <input type="submit" class="button_search5" value="确定" />
            </form>
            <span><a href="<?php echo U('goods', array('days' => '1')) ?>" <?php if ($days == 1): ?>class="hover"<?php endif ?>>今天</a></span>
            <span><a href="<?php echo U('goods', array('days' => '-1')) ?>" <?php if ($days == -1): ?>class="hover"<?php endif ?>>昨天</a></span>
            <span><a href="<?php echo U('goods', array('days' => '7')) ?>" <?php if ($days == 7): ?>class="hover"<?php endif ?>>最近7天</a></span>
            <span><a href="<?php echo U('goods', array('days' => '30')) ?>" <?php if ($days == 30): ?>class="hover"<?php endif ?>>最近1个月</a></span>
            <span><a href="<?php echo U('goods', array('days' => '90')) ?>" <?php if ($days == 90): ?>class="hover"<?php endif ?>>3个月</a></span>
            <span><a href="<?php echo U('goods', array('days' => '365')) ?>" <?php if ($days == 365): ?>class="hover"<?php endif ?>>1年</a></span>
        </dt>
        <dd>
        	<div class="range-date" id="J-date-slider"></div>
        </dd>
    </dl>
    <dl class="blue_table mt10">
    	<dt><strong>详细数据</strong></dt>
        <dd>
        	<table>
            	<tr>
                	<th>商品条码</th>
                    <th>商品名称</th>
                    <th>浏览量</th>
                    <th>销售数量</th>
                    <th>购买率</th>
                    <th>销售额</th>
                    <th>销售贡献</th>
                </tr>
                <?php foreach ($lists as $r): ?>
                <tr >
                	<td><?php if ($r['barcode']): ?><?php echo $r['barcode'] ?><?php else: ?>-<?php endif ?></td>
                    <td><?php echo $r['name'] ?></td>
                    <td><?php echo $r['hits'] ?></td>
                    <td><?php echo $r['total_shop_number'] ?></td>
                    <td><?php echo sprintf('%.2f', $r['total_shop_number'] / $r['hits'] * 100)  ?>%</td>
                    <td><?php echo MONUNIT?><?php echo sprintf('%.2f', $r['total_shop_price']) ?></td>
                    <td><?php echo sprintf('%.4f', ($r['total_shop_price']) / $count_price * 100) ?>%</td>
                </tr>
                <?php endforeach ?>
            </table>
        </dd>
    </dl>
    <div class="page fr" id="page"><?php echo $pages; ?></div>
    <div class="clear"></div>
    <?php include $this->admin_tpl('copyright') ?>
</div>
	<script type="text/javascript">
    	function checkform() {
    		var flag=checkDateTime($('#start').val(),$('#end').val());
	        if (flag==1)
	        {
	         alert("开始日期>结束日期");
	         return false;
	        }else if (flag==0){
	         alert("开始日期==结束日期");
	         return false;
	        }
    	}
    </script>
	<?php echo jsfile('hddate');?>
	<script>
	$(function() {
		var start = {
		    elem: '#start',
		    format: 'YYYY-MM-DD',
		    //min: laydate.now(), //设定最小日期为当前日期
		    max: '2099-06-16 23:59:59', //最大日期
		    istime: false,
		    istoday: true,
		    choose: function(datas){
		         end.min = datas; //开始日选好后，重置结束日的最小日期
		         end.start = datas //将结束日的初始值设定为开始日
		    }
		};
		var end = {
		    elem: '#end',
		    format: 'YYYY-MM-DD',
		    //min: laydate.now(),
		    max: '2099-06-16 23:59:59',
		    istime: false,
		    istoday: true,
		    choose: function(datas){
		        start.max = datas; //结束日选好后，重置开始日的最大日期
		    }
		};
		laydate(start);
		laydate(end);
	});
	</script>
</body>
</html>