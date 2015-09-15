<?php  include  $this->admin_tpl('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/haidaoblue/easyui.css">
<link rel="stylesheet" type="text/css" href="<?php echo JS_PATH;?>EasyUI/themes/icon.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>EasyUI/hd_default_config.js"></script>
<div class="content">
	<div class="site">
	Haidao Board <a href="#">会员财务管理</a> > 余额日志列表
	</div>
	<span class="line_white"></span>
	<div class="goods mt10">
		<div class="search_ind">
			<input type="hidden" name="m" value="<?php echo GROUP_NAME ?>">
			<input type="hidden" name="c" value="<?php echo MODULE_NAME ?>">
			<input type="hidden" name="a" value="<?php echo ACTION_NAME ?>">
			<span style="margin-right: 10px;">搜索查询 </span>
			<input id="keyword" class="easyui-textbox" name="keyword" style="width:210px;height: 26px;" prompt="输入会员名称查询">
			<a id="search" href="javascript:;" class="easyui-linkbutton" style="height: 26px;padding-right: 10px;">查询</a>
		</div>
		<dl class="mt10">
			<dt><p>
				<a href="javascript:;" class="tabs" data='default' id="default"> 全部日志</a>
				<a href="javascript:;" class="tabs"> 财务设置</a>
			</p>
			</dt>
		</dl>
		<div class="login mt10" style="border: none;border-right: 1px solid #e6e6e6;">
			<!-- 日志信息 -->
			<dl id="admin_moneylog" style="width:100%;"></dl>
			<!-- 财务设置 -->
			<form action="<?php echo U('User/AdminMoneyLog/admin_money_log_set') ?>" method="post">
			<dl id="set_data">
                <dd>
                    <ul class="web">
						<li>
							<strong>是否开启会员充值功能：</strong>
							<b style="margin-right: 44px;">
								<label><input type="radio" name="enable" value="1" <?php if (C('enable') == 1) {  ?>  checked  <?php } ?> /> 开启 </label>
								<label><input type="radio" name="enable" value="0" <?php if (C('enable') == 0) {  ?>  checked  <?php } ?> /> 关闭 </label>
							</b>
							<span style="margin-left:3px";>设置是否开启会员充值功能，开启即可使用</span>
						</li>
						<li id='pays'>
							<strong>请选择支持的充值方式</strong>
                            <?php if (!$pays): ?>
                               <label><a href="<?php echo U('Pay/Pay/manage') ?>">暂未开启任何支付方式，点击这里去配置.</a></label>
                            <?php else: ?>
                                <?php foreach ($pays as $code => $pay): ?>
                                    <label><input type="checkbox" name="pays[]" value="<?php echo $code ?>" <?php if (in_array($code, C('pays'))): ?>checked<?php endif ?> <?php if($code=='ws_wap'||$code=='alipay_escow'){echo 'disabled';} ?>/> <?php echo $pay['pay_name'] ?></label><br/>
                                <?php endforeach ?>
                            <?php endif ?>
						</li>
						<li id="lowest">
							<strong>最低充值额度：</strong>
							<input type="text" value="<?php if (C('lowest')){echo C('lowest');}else{echo 0;}; ?>" name='lowest' class="text_input" datatype="*" /><span>设置最低充值额度，请根据实际情况填写(0为不限)</span>
						</li>
					</ul>
				</dd>
			</dl>
			<div class="input1">
				<input type="hidden" name="files" value="moneylog.php" />
				<input type="submit" value="提交" class="button_search" />
			</div>
			</form>
		</div>
		<div class="clear"></div>
		<?php include $this->admin_tpl('copyright') ?>
	</div>
</div>
<script type="text/javascript">
var dom = $('#admin_moneylog');
var pageSize = <?php echo PAGE_SIZE?>;
var dataurl = '<?php echo U('index')?>';
// var delurl = '<?php echo U('delete')?>';
var keyword = '<?php echo $keyword?>';
var user_id = '<?php echo $user_id?>';
$(function(){
	dom.datagrid({
		url:dataurl,
		striped:true,
		width:'100%',
		checkOnSelect:false,
		fitColumns:true,
		toolbar:[
			// {
			// 	id:'delrows',
			// 	text:'删除',
			// 	iconCls:'icon-del',
			// },'-',
			{
				id:'exportrows',
				text:'导出',
				iconCls:'icon-export',
			}
		],
		frozenColumns:[[
			{field:'id',checkbox:true}
		]],
		queryParams:{
			keyword:keyword
		},
		scrollbarSize:0,
		pagination:true,
		pageSize:pageSize,
		pageList: [pageSize,pageSize*4,pageSize*8,pageSize*16,pageSize*32,pageSize*64],
		//可以设置每页记录条数的列表
		columns:[[
		{field:'username',title:'会员名',width:'20%',halign:'center',align:'center'},
		{field:'money',title:'金额',width:'20%',halign:'center',align:'center',sortable:true},
		{field:'dateline',title:'时间',width:'20%',align:'center',sortable:true,
			formatter:function(value,row,index){
				return $.fn.datebox.defaults.timeformat(value);
			}
		},
		{field:'msg',title:'描述',width:'37%',halign:'center',align:'center',sortable:true}
		]]
	});
	//回车查询
	$('#keyword').textbox('textbox').bind('keydown',function (e) {
		if (e.keyCode == 13) {
			$('#search').trigger('click');
		}
	});
});
//增加查询参数，重新加载表格
$('#search').bind('click',function (){
	var queryParams = dom.datagrid('options').queryParams;
	//查询参数直接添加在queryParams中
	queryParams.keyword = $("#keyword").val();
	dom.datagrid('options').queryParams = queryParams;
	dom.datagrid('reload');
})
/* tab切换 */
$(function() {
	var tabTitle = ".mt10 dt p a";
	$(tabTitle).click(function(){
		$(this).siblings("a").removeClass("hover").end().addClass("hover");
	});
});
$('#default').addClass('hover');
$('#set_data').hide();
$('.tabs').bind('click',function(){
	if ($(this).attr('data') == 'default') {
		$('.easyui-fluid').show();
		$('#set_data').hide();
	} else {
		$('.easyui-fluid').hide();
		$('#set_data').show();
	}
})
/* 开关切换效果 */
if (<?php echo C('enable') ?> == 0) {
	$('#pays').hide();
	$('#lowest').hide();
}
$('input[name=enable]').bind('change',function(){
	if ($('input[name=enable]:checked').val() == 1) {
		$('#pays').show();
		$('#lowest').show();
	}else{
		$('#pays').hide();
		$('#lowest').hide();
	}
})
/* 保存财务设置 */
$(':submit').click(function(event) {
	var data = new Array();
	data['enable'] = $('input[name=enable]:checked').val();
	data['pays'] = jqchk();
	data['lowest'] = $('input[name=lowest]').val();
	if (data['pays'].length==0 && data['enable'] == 1) {
		alert('请选择充值方式！');
		return false;
	}
	if ($.trim(data['lowest']) == '') {
		alert('请填写最低充值金额');
		return false;
	}
	$(this).submit();
});
//jquery获取复选框值
function jqchk(){
	var chk_value =[];
	$('input[name="pays[]"]:checked').each(function(){
		chk_value.push($(this).val());
	});
	return chk_value;
}
</script>
