/* 商品相关 */
var page_detail = (function() {

	var buy_num = 0;
	var oInfo = $('div.details_main');

	function error(msg) {
		msg = msg || '请选择你要购买的商品规格';
		$(".xz-notic").html('<p>'+ msg + '</p>').show();
		return false;
	}

	return {
		/* 初始化 */
		init: function() {
			if (typeof(goods) != 'object') {
				alert('无法读取此商品信息！');
				location.href = '/';
				return;
			}
		},

		/* 加入购物车 */
		cart_add : function(product_id) {
			var oInfo = $('div.details_main');
			product_id = product_id || goods.product_id;
			buy_num = oInfo.find("input[name=cart_num]").val() || 1;
			if($('.specCol').length != $('.specCol .goods-color-choose').length){
				error();
				return false;
			}
			$.getJSON("?m=goods&c=cart&a=add", {
        		goods_id:goods.goods_id, 
        		product_id:goods.product_id,
        		num:buy_num,
        		ajax:1
			}, function(ret) {
				if (ret.status == 1) {
					if (site_cartsetup == 1) {
						location.href = ret.url;
					} else if(site_cartsetup == 2) {
						hd_cart.getCartCount();
						art.dialog({
							id: 'succeed',
							fixed: true,
							lock: true,
							content: '<div id="goods-add" style="display: block;"><p>'+ ret.info +'</p><div><span onclick="location.href=location.href;" class="shopCar_T_span2">继续购物</span><span onclick="location.href=\'?m=goods&c=cart&a=index\'" class="shopCar_T_span3"></span></div></div>'
						});
					} else {
						location.href = '?m=goods&c=cart&a=index';
					}					
				} else {
					error(ret.info);
				}
			});
		},
                
		/* 加入收藏 */
		collect : function(goods_id) {
			return $.getJSON("?m=user&c=collect&a=add", {goods_id:goods_id} , function(ret) {
				if (ret.status != 1) {
					art.dialog.alert(ret.info);
				} else {
					art.dialog.succeed(ret.info);
				}
			});
		},

		/* 到货通知 */
		notify : function(product_id) {
			product_id = product_id || goods.product_id;
			var oInfo = $('div.details_main');
			buy_num = oInfo.find("input[name=cart_num]").val() || 1;
			art.dialog({
				padding: '0px',
				id: 'notify',
				background: '#ddd',
				opacity: 0.3,
				title: '到货通知',
				content: '<div id="goods-notice"><div class="goods-notice-box" ><div class="dh_word">如果商品到货，您将收到邮件、短信或电话通知 请填写您的手机号或邮箱地址</div><div class="dh-input">手机号码<input type="text" name="mobile" value="" placeholder="请输入手机号码"/></div><div class="dh-input">邮箱地址<input type="text" name="email" placeholder="请输入您的邮箱" value=""/></div></div></div>',
		        ok: function () {
					var win = this;
					var form = this.DOM.content.find('div');
		        	var mobile = form.find("input[name=mobile]");
		        	var email = form.find("input[name=email]");
		        	if($.trim(mobile.val()).length == 0 && $.trim(email.val()).length == 0) {
		        		alert('手机号码和邮箱必须填写一种');
		        		this.shake && this.shake();
		        		mobile.focus();
		        		return false;
		        	}
		        	$.post('?m=goods&c=index&a=goods_message', {
		        		goods_id:goods.goods_id, 
		        		product_id:goods.product_id,
		        		num:buy_num,
		        		mobile:mobile.val(),
		        		email:email.val(),
		        		ajax:1
		        	}, function(ret) {
		        		if (ret.status == 1) {
		        			art.dialog.succeed(ret.info);
		        			return true;
		        		} else {
		        			alert(ret.info);
		        			return false;
		        		}
		        	}, 'JSON');		        	
				},
				cancel: true
			});
		},

		cancel : function() {
			return;
		}

	};
})();