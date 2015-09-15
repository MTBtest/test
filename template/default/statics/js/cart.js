/**
 *商品结算，收货地址
 */
$(function() {
	/**
	 *数量减计算价格
	 */
	$(".reduce").click(function() {
		var price = $(this).parents().siblings("li").children(".price").text();  //取当前元素父元素同级叫li元素的子元素price的文本
		price = parseFloat(price);			  //保留小数
		var _self = $(this).next(".count-input");  //当前输入框的下一个元素
		var val = _self.val();	  //输入框的值
		val = parseInt(val) - 1;	  //值减去1
		var change = -1;
		_self.val(val);		 //输入框的值就等于点击后的值
		if (val < 1) {
			val = 1;
			change = 0;
			_self.val(val);
		}
		_self.attr('data-num',_self.val());
		getTotal(val,change, price, $(this));
	});

	/**
	 *数量加计算价格
	 */
	$(".add").click(function() {
		var price = $(this).parents().siblings("li").children(".price").text();
		price = parseFloat(price);
		var _self = $(this).prev(".count-input");
		var val = _self.val();
		val = parseInt(val) + 1;
		var change = 1;
		_self.val(val);
		_self.attr('data-num',_self.val());
		getTotal(val,change, price, $(this));
	});

	/**
	 * 改变数量计算价格
	 */
	$(".count-input").change(function() {
		var price = $(this).parents().siblings("li").children(".price").text();
		var val = $(this).val();  //取当前值
		change = $(this).val()-$(this).attr('data-num');
		$(this).val(val);
		$(this).attr('data-num',$(this).val());
		getTotal(val,change, price,$(this));
	});

	/**
	 * 计算总价
	 */
	function getTotal(val,change,price,self) {
		var sumprice = self.parents().siblings("li").children(".price-total").text(); 
		sumprice = parseFloat(sumprice);
		var val, totalprice = 0;
		var num = val;
		var sum = sumprice + change * price;
		self.parents().next().find('.price-total').text(sum.toFixed(2));  //计算结果保留2位小数
		$(".price-total").each(function(i) {
			val = $(this).text();
			totalprice = totalprice + parseFloat(val);
		});
		var timestamp = self.parents('div').attr('data-id');
		$.getJSON('?m=goods&c=cart&a=update', {timestamp:timestamp, num:num}, function(ret){
			if(ret.status == 1) {hd_cart.getCartCount()}
		});   	
		$("#priceTotal > span").text(totalprice.toFixed(2));
	}
	/**
	 * 删除
	 */
	$(".deleteAll").click(function() {
		var conf = confirm('确定要删除吗？');
		var parent = $(this).parents('div').attr('data-id');
		   if (conf) {
			   $.getJSON('?m=goods&c=cart&a=delete', {
				   timestamp:parent,
				   ajax:1
			   }, function(ret) {
			   	if(ret.status == 1) {
			   		if($('.shppingBox').children('.cart_01').length == 1){
						location.reload();
					}else{
				   	$('div[data-id='+ parent +']').fadeOut('slow',function () {
				   		$(this).prev('.checkboxshop').remove();
				   		$(this).remove();
				   		getTotal(0, 0, 0,$(this).siblings('li.cart_01e'));
				   	});
					   hd_cart.getCartCount();
					   return true;
				   } 
				}else {
					   alert(ret.info);
					   return false;
				   }
			   });
		   }
	});

	/**
	 * 点击其他地址改变选择地址样式
	 */	
	$("#cart_box").hover(function(){
		if(hd_cart.getCartList()){
			$(this).find("dt").addClass("hover");
			$(this).find("dd").show();
		}		
	},function(){
		$(this).find("dt").removeClass("hover")
		$(this).find("dd").hide();
	});	
	
	
});

var hd_cart = (function() {
	var cart_box = $("#cart_box");
	return {		
		/* 获取购物车总数 */
		getCartCount : function () {
			$.getJSON('?m=goods&c=cart&a=getCartList', function(ret){
				$("#cart_list_box p #count").text(ret.goods_count);
				$("#cart_list_box p #goods_num").text(ret.goods_num);
				$("#cart_list_box p #total_price").text(MONUNIT+ret.total_price.toFixed(2));
				$("#cart_box .shop_number > em").text(ret.goods_num);
			});
		},
		/* 清空购物车 */
		clear:function(){
			$.getJSON('?m=goods&c=cart&a=clear', function(ret){
				if(ret.status == 1) {
					$("#cart_box .shop_number > em").text('0');
					$("#cart_box ul").html('');
					$("#cart_list_box p #count").text(0);
					$("#cart_list_box p #goods_num").text(0);
					$("#cart_list_box p #total_price").text(MONUNIT+"0.00");
				} else {
					alert('购物车清空失败，请重试');
					return false;
				}
			});
		},
		
		/* 获取购物车列表 */
		getCartList: function () {
			var _html = '';
			$.ajax({
				type:"get",
				url:"?m=goods&c=cart&a=getCartList",
				dataType:"JSON",
				async:false,
				success:function(ret) {
					$.each(ret.list, function(i, n) {
						_html += '<li id="cart_item_'+ i +'"><a href="/index.php?m=goods&c=index&a=detail&id='+n.id+'" target="_blank"><img src="'+ n.goods_img +'" width="50" height="50"/></a>'
						 + '<strong><a href="/index.php?m=goods&c=index&a=detail&id='+n.id+'" target="_blank">'+ n.name +'</a></strong>'
						 + '<span><font class="red">'+MONUNIT+ n.shop_price +'×'+ n.goods_num +'</font><a href="javascript:void(0)" onclick="hd_cart.delCartItem(\''+ i +'\');">删除</a></span></li>';
					});
					$("#cart_box ul").html(_html);
					$("#cart_list_box p #count").text(ret.goods_count);
					$("#cart_list_box p #goods_num").text(ret.goods_num);
					$("#cart_list_box p #total_price").text(MONUNIT+ret.total_price.toFixed(2));
				}
			});
			return true;
		},
		
		/* 删除单个商品 */
		delCartItem:function(k) {
			$.getJSON('?m=goods&c=cart&a=delete', {
				timestamp:k,
				ajax:1
			}, function(ret){
				if(ret.status == 1) {
					$('li#cart_item_' + k).fadeOut("slow");
					hd_cart.init();
					return true;
				} else {
					alert('删除失败', ret.info);
					return false;
				}
			});
			
		},
		
		/*  初始化头部购物车 */
		init : function() {
			hd_cart.getCartCount();
		}		
		
	};
})();
