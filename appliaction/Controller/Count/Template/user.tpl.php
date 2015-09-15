<?php include $this->admin_tpl("header"); ?>
<script src="<?php echo JS_PATH;?>highcharts.js"></script>
<script src="<?php echo JS_PATH;?>exporting.js"></script>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">统计报表</a> > 会员数据分析
    </div>
	<div class="line_white"></div>
    <dl class="gzzt clearfix mt10">
        <dt>会员<br />统计</dt>
        <dd>
            <div class="boxl fl">
            	<span><b>今日新增会员：</b><font><?php echo $user_count['today'] ?></font></span><span>本周新增会员：<b><?php echo $user_count['week'] ?></b></span><span>本月新增会员：<b><?php echo $user_count['month'] ?></b></span>
            </div>
            <div class="boxr fl">
                <strong>新增会员较昨天同时段：<img src="<?php echo IMG_PATH?>admin/icon_<?php echo ($user_gain >= 0) ? 'up' : 'down';?>.png" alt="涨幅图标"/><font><?php echo $user_gain; ?>%</font></strong><strong>会员总数：<?php echo $user_count['total']; ?></strong>
            </div>
        </dd>
    </dl>
	<dl class="charts mt10">
    	<dt>
            <form action="<?php echo __APP__; ?>" method="get" name="form_select" onsubmit="return checkform()">
            <p>
                <a href="<?php echo U('user', array('days' => '7'));?>" <?php if ($_GET['days'] == 7): ?>class="hover"<?php endif ?>>最近7天</a>
                <a href="<?php echo U('user', array('days' => '30'));?>" <?php if ($_GET['days'] == 30): ?>class="hover"<?php endif ?>>最近30天</a>
                <input type="hidden" name="m" value="<?php echo GROUP_NAME;?>"/>
                <input type="hidden" name="c" value="<?php echo MODULE_NAME;?>"/>
                <input type="hidden" name="a" value="<?php echo ACTION_NAME;?>"/>
                <input type="hidden" name="days" value="-1"/>
                <span>自定义时间段:</span>
                <input type="text" name="start_time" id="start" value="<?php echo $_GET['start_time'] ?>" class="time_input2 input_ind  sDatepicker " style="width: 120px;"/><em style="margin-right: 8px;">~</em>
                <input type="text" name="end_time" id="end" value="<?php echo $_GET['end_time'] ?>" class="time_input2 input_ind  sDatepicker " style="width: 120px;"/>
                <input type="submit" class="button_search" value="确定" />
            </p>
            </form>
            <strong>新增会员概况</strong>
            <b>新增会员：</b><font><?php echo $days_user_count ?></font><span>[数据为当前选中时间周期内]</span>
        </dt>
        <dd id="container" style="min-width: 220px; height: 220px; margin: 0 auto"></dd>
    </dl>
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
    <dl class="blue_table mt10">
    	<dt><p class="pr fr"><!-- <a href="#">[下载当前会员数据]</a> --></p><strong>会员购买数据</strong></dt>
        <dd>
        	<table id="user_list">
            	<tr>
                	<th>排名</th>
                    <th>会员名</th>
                    <th>成交订单数</th>
                    <th>成交订单金额</th>
                    <th>取消订单数</th>
                </tr>
            </table>
        </dd>
    </dl>
    <div class="page fr" id="page"><?php echo $pages; ?></div>
    <div class="clear"></div>
    <?php include $this->admin_tpl('copyright') ?>
</div>
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
<script type="text/javascript">
$(function() {
    var days = '<?php echo $days ?>';
    var n = Math.ceil(days / 8);
    var sell_days = <?php echo json_encode($sell_days) ?>;
    $('#container').highcharts({
        chart: {
            type: 'area',
            spacingBottom: 30
        },
        title: {
            text: ' <?php echo mdate($start_time, 'Y-m-d');?>-<?php echo mdate($end_time, 'Y-m-d') ?> '
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 60,
            y: 20,
            floating: true,
            borderWidth: 1,
            backgroundColor: '#FFFFFF'
        },
        xAxis: {
            categories: sell_days,
            labels: {
                step:n,
            }
        },
        yAxis: {
            title: {
                text: ' 新增会员 '
            }
        },
        tooltip: {
            crosshairs: true,
            shared: true,
            headerFormat:'<b>{point.x}</b><br/>'
        },
        plotOptions: {
            area: {
                fillOpacity: 0.3
            }
        },
        credits: {
            enabled: false
        },
        series:<?php echo json_encode($series)?>
    });
    get_user_list(1);
});
$.urlParam = function(name, url){
	var url = url || window.location.href;
	var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(url);
	if(!results) return false;
	return results[1] || 0;
}
$("#page a").live('click', function() {
    var urlstr = $(this).attr('href').toString();
    var page = $.urlParam('page', urlstr);
    if(page != false) {
    	get_user_list(page);
    }
    return false;
});
function get_user_list(page) {
    var data = <?php echo json_encode($_GET); ?>;
    data.page = page;
    $.getJSON("<?php echo U('public_userlist') ?>", data, function(ret) {
        $("#page").html(ret.pages);
        var _html = '';
        $.each(ret.lists, function(i, n) {
            _html += '<tr>' +
            '<td>'+n.rank+'</td>' +
            '<td>'+n.username+'</td>' +
            '<td>'+n.order_total+'</td>' +
            '<td>'+n.order_price +'</td>' +
            '<td>'+n.order_canpel+'</td>' +
            '</tr>';
        });
        $("#user_list tr:gt(0)").remove(); 
        $("#user_list tr:first").after(_html);
    });
    return false;
}
</script>
</body>
</html>