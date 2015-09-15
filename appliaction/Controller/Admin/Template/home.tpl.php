<?php  include $this->admin_tpl('header');?>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.cookie.js"></script>
<style type="text/css">
/* 关闭消息提示x按钮 */
.tisi .closetips{float:right;margin-right:10px;color:#636363;}
.tisi .closetips:hover {color:red;text-decoration:none;}
</style>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">站点设置</a> > 后台首页
    </div>
    <span class="line_white"></span>
    <script type="text/javascript" src="http://update.haidao.la/checkup.php?version=<?php echo getconfig('version')?>"></script>
    <?php if (file_exists(DOC_ROOT.'install/')): ?>
        <div class="tisi mt5" tips-id="09">系统检测到存在安装向导目录[install]。为了安全起见，建议您尽快删除或重命名。<a href="javascript:;" class="closetips" title="关闭后24小时内将不再显示">X</a></div>
    <?php endif ?>
    <dl class="box fl mt10">
        <dt><strong>今日统计概况</strong></dt>
        <dd>
            <ul class="odd1">
                <li><span>￥ <?php echo $today_count['real_amount']; ?> 元</span>销售总额：</li>
                <li><span><?php echo $today_count['total_order'];?> 个</span>订单数量：</li>
                <li><span>￥ <?php echo $today_count['per_cust_transaction'] ?> 元</span>平均客单价：</li>
                <li><span><?php echo $today_count['user_reg_num'] ?> 人</span>今日新增会员：</li>
                <li><span><?php echo $today_count['user_comm_num'] ?> 条</span>今日新增商品评论：</li>
            </ul>
        </dd>
    </dl>
    <dl class="box fr mt10">
        <dt><strong>订单处理情况</strong></dt>
        <dd>
            <ul class="odd2">
                <li><span><a href="<?php echo U('Goods/AdminOrder/lists',array('type'=>6)) ?>" title="未处理订单总数"><i><?php echo $order_count['confirm'] ?></i></a> 个</span>未处理订单总数：</li>
                <li><span><a href="<?php echo U('Goods/AdminOrder/lists',array('type'=>4)) ?>" title="待发货订单总数"><i><?php echo $order_count['delivery'] ?></i></a> 个</span>待发货订单总数：</li>
                <li><span><a href="<?php echo __ROOT__ ?>/index.php?m=user&c=member_consult&a=lists" title="商品咨询总数"><?php echo $order_count['consult_total'] ?></a> 条</span>商品咨询总数：</li>
                <li><span><a href="<?php echo __ROOT__ ?>/index.php?m=user&c=member_consult&a=lists" title="未处理咨询总数"><i><?php echo $order_count['consult_reply'] ?></i></a> 条</span>未处理咨询总数：</li>
                <li><span><a href="<?php echo U('Goods/AdminOrder/lists',array('type'=>1)) ?>" title="已完成订单总数"><?php echo $order_count['finish'] ?></a>个</span>已完成订单总数：</li>
            </ul>
        </dd>
    </dl>
    <dl class="box fl mt10">
        <dt><strong>商品信息统计</strong></dt>
        <dd>
            <ul class="odd3">
                <li><span><a href="<?php echo U('Goods/Goods/lists') ?>" title=""><i><?php echo $goods_count['sell_num'] ?></i></a> 件</span>上架商品总数：</li>
                <li><span><a href="<?php echo U('Goods/Goods/lists',array('label'=>3)) ?>" title=""><i><?php echo $goods_count['warn_number'] ?></i></a> 件</span>商品库存警告：</li>
                <li><span><a href="<?php echo U('Goods/Goods/lists',array('label'=>2)) ?>" title=""><i><?php echo $goods_count['goods_message'] ?></i></a> 件</span>商品缺货登记：</li>
                <li><span><i><?php echo $goods_count['coupons_to'] ?></i> 张</span>今日发放优惠券：</li>
                <li><span><i><?php echo $goods_count['coupons_use'] ?></i> 张</span>今日使用优惠券：</li>
            </ul>
        </dd>
    </dl>
    <dl class="box fr mt10">
        <dt><strong>系统信息</strong></dt>
        <dd>
            <ul class="odd4">
                <li><span>海盗 <?php echo C('VERSION') ?>(UTF-8) Release <?php echo C('BUILD'); ?></span>程序版本：</li>
                <li><span><?php echo $sys_info['os'] ?> / PHP v<?php echo $sys_info['phpv'] ?></span>服务器系统及PHP：</li>
                <li><span><?php echo $sys_info['web_server'] ?></span>服务器软件：</li>
                <li><span><?php echo $sys_info['mysqlv'] ?></span>服务器MySQL版本：</li>
                <li><span><?php echo $sys_info['mysqlsize'] ?> MB</span>当前数据库尺寸：</li>
            </ul>
        </dd>
    </dl>
    <div class="clear"></div>
    <div class="team">
        <h3>海盗系统开发团队：</h3>
        <ul class="odd5">
            <li><span>版权所有：</span><strong>迪米盒子科技有限公司</strong></li>
            <li><span>总策划兼产品经理：</span>董　浩</li>
            <li><span>产品设计与研发团队：</span>董　浩　夏雪强　李春林　孔智翱　王小龙　秦秀荣　饶家伟</li>
            <li><span>官方网站：</span><a href="http://www.haidao.la/" target="view_window"  class="kuaidi100" >www.haidao.la</a></li>
            <li><span>官方社区：</span><a href="http://bbs.haidao.la/" target="view_window"  class="kuaidi100" >bbs.haidao.la</a></li>
        </ul>
    </div>
<script>
$(function(){
    $(".odd1 li:even").css("background","#fff");
    $(".odd2 li:even").css("background","#fff");
    $(".odd3 li:even").css("background","#fff");
    $(".odd4 li:even").css("background","#fff");
    $(".odd5 li:even").css("background","#fff");
    // 关闭系统提示信息
    var now_cookie = $.cookie('closetips');
    var str = '';
    $('.closetips').bind('click',function(){
        var obj = $(this);
        if (now_cookie != undefined && now_cookie != '') {
            var arr_cookie = new Array();
            arr_cookie = now_cookie.split(',');
            if ($.inArray(obj.parent().attr('tips-id'),arr_cookie) > -1) {
                str += now_cookie;
            } else {
                str += now_cookie + ',' + obj.parent().attr('tips-id');
            }
        } else {
            str += ',' + obj.parent().attr('tips-id');
        }
        $.cookie('closetips', str , { expires: 1 });
        obj.parent().fadeOut(1000);        
    })
    /* 将已关闭提示的消息隐藏 */
    if (now_cookie != undefined && now_cookie != '') {
        arr_cookie = now_cookie.split(',');
        var div = $('.closetips').parent();
        $.each(div,function(n, v) {
            if ($.inArray($(this).attr('tips-id'),arr_cookie) > -1) {
                $(this).attr('style','display:none;');
            }
        });
    }
});
</script>
<?php include $this->admin_tpl('copyright') ?>