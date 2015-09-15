<?php include $this -> admin_tpl("header"); ?>
<style type="text/css">
	.vip_ss_fa li select{float: none;}
	input.small{margin: 0px;}
</style>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">运营推广</a> > 商品促销
    </div>
    <span class="line_white"></span>
    <div class="install mt10">
   	<div class="list_order" >
   		<form name="form" action="" method="post" class="addfrom" >
    	<div class="vip_ss">
        	<p><strong>规则名称：</strong>
        		<input type="text" name="name" placeholder="输入一个订单促销规则名称" class="input" datatype="*" value="<?php echo $info['name']?>">
        	</p>
        	<div class="vip_ss_fa clearfix">
                <strong>规则描述：</strong>
                <textarea name="description"><?php echo $info['description']?></textarea>
                <font>规则描述信息将在购物车显示! 为空则显示规则名称</font>
            </div>
        </div>
        <dl class="gzzt clearfix mt10">
        	<dt>规则<br />状态</dt>
            <dd>
            	<div class="time fl">
                	<strong>促销时间：</strong>
                	<input type="text" id="start" name='start_time' value='<?php echo $info['start_time'] ? date('Y-m-d H:i',$info['start_time']) : date('Y-m-d H:i') ?>' datatype="*"  style='width:120px' />
				<span>
					~
				</span>
				<input type="text" id="end" name='end_time' value='<?php echo $info['end_time'] ? date('Y-m-d H:i',$info['end_time']) : date('Y-m-d H:i',strtotime('+ 1 month')) ?>' datatype="*"  style='width:120px' />
                </div>
                <div class="state fl">
                	<strong>促销启用状态：</strong>
					<label><input type="radio" name="status" value="0"  <?php echo $info['status']==0 ? 'checked=checked' : '' ?>> 关闭 </label>
					<label><input type="radio" name="status" value="1"  <?php echo $info['status']==1 || !isset($info) ? 'checked=checked' : '' ?>> 开启 </label>
                </div>
            </dd>
        </dl>
        <div class="vip_ss mt10">
        	<p><strong>优惠条件：</strong>
				<select id="prom_type" name="award_type">
					<?php foreach($parse_type as $k=>$v):?>
						<option value="<?php echo $k?>"><?php echo $v?></option>
					<?php endforeach;?>
				</select>
			</p>
			<div class="mb5">
				<strong>满足金额：</strong><input class="small ml15" type="text" value="<?php echo $info['money']?>" name="money" datatype="num" />
			</div>
        	<div class="vip_ss_fa clearfix">
                <strong>优惠规则：</strong>
                <ul>
                    <div id="expression" >
                    	<li>
                    		<b class="red">*</b>折扣： <input name="award_value" type="text" class="small"  value="<?php echo $info['award_value']?>" datatype="numrange" min="1"  max="100" />
                    		<label>% 折扣值(1-100 如果打9折，请输入90)</label>
						</li>
                    </div>
                </ul>
                <font>请先选择优惠条件，再配置优惠规则</font>
            </div>
        </div>
        <div style="margin-top: 10px;">
        	<strong>请选择参与商品促销的商品：</strong>
        </div>
        <div class="choose-pro mt10">
        	<div class="choose-pro-div">
        		<a href="javascript:goods_search()" class="fl chooseBtn1"><img src="<?php echo IMG_PATH; ?>admin/order-50.png"/></a>
        		<a href="javascript:void(0)" onclick="delAll()" class="fr"><img src="<?php echo IMG_PATH; ?>admin/order-51.png" alt="" /></a>
        	</div>
        	<div class="clear"></div>
        	<table border="" cellspacing="" cellpadding="" class="chooseBG" id="goodsRowTable">
        		<tr>
        			<th>商品名称</th>
        			<th>销售价</th>
        			<th>库存</th>
        			<th>操作</th>
        		</tr>
        		<tbody id="goodsBaseBody">
        		<?php foreach($goods_list as $k=>$v):?>
        			<tr>
        			<td class="mo_text"><?php echo $v['name']?></td>
        			<td><?php echo format_price($v['min_shop_price'],$v['max_shop_price'])?></td>
        			<td><?php echo getGoodsNumber($v['id'],NULL,'goods_number')?></td>
        			<td><input name="goods_id[]" type="hidden" value="<?php echo $v['id']?>" /><a href="javascript:void(0)" onclick="$(this).parents('tr').remove();">删除</a></td>
        			</tr>
    			<?php endforeach;?>
        		</tbody>
        		<script id="goodsRowTemplate" type="text/html">
        		<%for(var item in templateData){%>
                <%item = templateData[item]%>
        		<tr>
        			<td class="mo_text"><%=item.name%></td>
        			<td><%=item.shop_price%></td>
        			<td><%=item.goods_number%></td>
        			<td><input name="goods_id[]" type="hidden" value="<%=item['id'] %>" /><a href="javascript:void(0)" onclick="$(this).parents('tr').remove();">删除</a></td>
        		</tr>
        		<%}%>
        		</script>
        	</table>
        </div>
		<div class="clear"></div>
		<div class="submit">
			<?php if(isset($info)):?>
				<input type="hidden" name="id" id="id" value="<?php echo $info['id']?>" />
			<?php endif;?>
			<input type="submit" class="button_search" value="提交" />
			<a href="<?php echo U('lists')?>">返回</a>
        </div>
        </form>
    </div>
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
<!--模板-->
<script type="text/javascript" src="<?php echo JS_PATH; ?>artTemplate/artTemplate.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>artTemplate/artTemplate-plugin.js"></script>
<script type="text/javascript">
var award_type = <?php echo $info['award_type'] ? $info['award_type'] : 1 ?>;
var award_value = <?php echo $info['award_value'] ? $info['award_value'] : 1 ?>;
var tempUrl = "<?php echo U('Goods/goods/search_goods?prom_id=0') ?>";
var coupons_list =<?php echo json_encode($coupons_list) ?>;
$(function() {
	//类型选择
	$("#prom_type").live("change",function(){
		  var type = parseInt($("#prom_type").val());
		  var expression = '';
		  switch(type){
		    case 1:{
		      expression = '<li><b class="red">*</b>折扣： <input name="award_value" type="text" class="small"  value="" datatype="numrange" min="1"  max="99" /> <label>% 折扣值(1-100 如果打9折，请输入90)</label> </li>';
		      break;
		    }
		    case 2:{
		      expression = '<li><b class="red">*</b>优惠金额： <input name="award_value" type="text" class="small"  value="" datatype="num" /> <label>立减金额（元）</label> </li>';
		      break;
		    }
		    case 3:{
		      _html='';
		      $.each(coupons_list,function(index,item){
				_html+='<option value="'+item.id+'">'+item.name+'('+item.num+')</option>'
		      });
		      expression = '<li><b class="red">*</b>优惠券：<select name="award_value">'+ _html +'</option>{/query}</select></li>';
		      break;
		    }
		    case 4:{
		      expression = '';
		      break;
		    }
		  }
		  $("#expression").html(expression);
	});
	//表单验证
	$(".addfrom").Validform({
		tiptype :function(){}
	});
	//初始选中
	$("#prom_type").attr("value",award_type).trigger("change");
	$("[name='award_value']").val(award_value);
})
</script>
<script type="text/javascript">
    //选择商品
    function goods_search(){
    	idArr = [];
		$('[name="goods_id[]"]').each(function(){
			idArr.push($(this).val());
		})
    	art.dialog.open(tempUrl, {
			title: '设置商品的规格',
			background: '#ddd',
			opacity: 0.3,
			width: '875px',
			title: '选择商品',
			okVal:'确认所选商品',
			ok:function(iframeWin, topWin){
				var rs = $(iframeWin.document).find('.ids:checked');
				goodsArr = [];
				rs.each(function() {
					var data_id = $(this).attr('data-id');
					var data_name = $(this).attr('data-name');
					var data_shop_price = $(this).attr('data-shop-price');
					var data_goods_number = $(this).attr('data-goods-number');
					if($.inArray(data_id, idArr) == -1){
						idArr.push(data_id)
						goodsArr.push({
							'id': data_id,
							'name': data_name,
							'shop_price': data_shop_price,
							'goods_number': data_goods_number
						});
					}
				});
				var goodsRowHtml = template('goodsRowTemplate', {'templateData': goodsArr});
				$('#goodsBaseBody').append(goodsRowHtml);
			}
    	})
    }
	/**
	 * 清除规格
	 */
	function delAll() {
		if (confirm('是否真要清空?')) {
			$("#goodsBaseBody tr").remove();
		}
	}
</script>