/* 订单处理类 */
var hd_order = (function() {
	var order = {}
	
	var submitBtn = $(".agree-order-fr-btn");
	
	function error(msg) {
		msg = msg || '禁止提交';
		submitBtn.addClass('disabled').text(msg);
	}	
	return{
		init : function() {
			order = {
				"address": $("#address-box").find('.selected').attr('data-addressid'),//收货地址
				"pay_type":	$("#pay_type_box input[name=pay_type]:checked").val(),//支付方式
				"delivery_id": $("#delivery_box input[name=delivery_id]:checked").val(), //配送方式
				"sn": $(".cd_key select").val(),//优惠券
				"promotion_id": $("#promotion_id").val(),
				'invoicerate': 0,//索取发票
			}
			if($("input[name=invoicerate]").attr('checked') == 'checked') {
				order.invoicerate = 1;
			}
			$.getJSON('?m=goods&c=order&a=getOrderInfo', order, function(ret) {
				$("#delivery_box .pay-choose-1").remove();
				if(ret.delivery == false) {
					$("#delivery_tips").html("很抱歉，此地区暂时无法配送").show();
					error('无法配送');
				} else {
					var _html = '';
					var deliverys = {};
					var k = 0;
					$.each(ret.delivery, function(i, n) {
						var _margin = (i > 0) ? 'pay-margin ' : '';
						var _checked = (n.id == order.delivery_id) ? 'checked' : '';
						_html += '<div class="'+ _margin +'pay-choose-1"><label for="delivery_id_'
						+ n.id +'"><input type="radio" name="delivery_id" value="'
						+ n.id +'" id="delivery_id_'
						+ n.id +'" '+ _checked +'>&nbsp;'
						+ n.name +'<span class="pay-choose-1-msg">'
						+ n.descript +'</span></label></div>';
						deliverys[k] = n;
						k++;
					});
					$("#delivery_box .pay-choose-1").remove();
					$("#delivery_tips").before(_html).show();
					$("#delivery_tips").html('').hide();
                    // 显示支付方式
                    var _pays = '';
                    var pays_type = {};
                    if (ret.pays.length == undefined && deliverys != undefined) {
                    	order.delivery_id = (order.delivery_id != undefined) ? order.delivery_id : deliverys[0]['id'];
	                    $.getJSON('?m=goods&c=order&a=getOrderInfo', order, function(data) {
                    		var k = 0 ;
	                    	$.each(data.pays,function(i,n) {
	                    		n['pay_id'] = i;
	                    		pays_type[k] = n;
	                    		k++;
	                    	})
		                    if(k < 1) {
		                    	$("#pay_type_box .pay-choose-1").remove();
		                        $("#pays_tips").text("系统暂未开启任何支付方式").show();
		                    } else {
		                        $.each(pays_type, function(i, n){
		                        	var _checked = '';
		                            if (order.pay_type == n.pay_id || (order.pay_type == undefined && i === '0')) {
		                            	_checked = 'checked';
		                            }
		                            if (order.pay_type == undefined && i === '0') {
		                            	order.pay_type = n.pay_id;
		                            }
		                            _pays += '<div class="pay-margin pay-choose-1"><label><input type="radio" name="pay_type" value="'+ n.pay_id +'" '+ _checked +'>'+ n.name +'<span class="pay-choose-1-msg">'+ n.description +'</span></label></div>';
		                        });
		                        $("#pays_tips").hide();
		                        $("#pay_type_box .pay-choose-1").remove();
                    			$("#pay_type_box .pay-single").after(_pays);
		                    }
	                    })
                    } else {
                    	var k = 0;
                    	$.each(ret.pays,function(i,n) {
                    		n['pay_id'] = i;
                    		pays_type[k] = n;
                    		k++;
                    	})
                    	if(k < 1) {
                    		$("#pay_type_box .pay-choose-1").remove();
	                        $("#pays_tips").text("系统暂未开启任何支付方式").show();
	                    } else {
	                        $.each(pays_type, function(i, n){
	                            var _checked = '';
	                            if (order.pay_type == n.pay_id || (order.pay_type == undefined && i === '0')) {
		                            	_checked = 'checked';
		                            }
		                        if (order.pay_type == undefined && i === '0') {
	                            	order.pay_type = n.pay_id;
	                            }
	                            _pays += '<div class="pay-margin pay-choose-1"><label><input type="radio" name="pay_type" value="'+ n.pay_id +'" '+ _checked +'>'+ n.name +'<span class="pay-choose-1-msg">'+ n.description +'</span></label></div>';
	                        });
	                        $("#pays_tips").hide();
		                    $("#pay_type_box .pay-choose-1").remove();
		                    $("#pay_type_box .pay-single").after(_pays);
	                    }
                    }
					submitBtn.removeClass('disabled').text('提交订单').show();
				}
				$("#p_delivery span").text(ret.p_delivery);//运费
				$("#p_coupons span").text(ret.p_coupons);
				$("#p_give_point span").text(ret.p_give_point);//积分
				$("#p_promotion span").text(ret.p_promotion);//订单优惠
				if(ret.p_invoicerate > 0) {
					$("#p_invoicerate span").text(ret.p_invoicerate);
					$("#p_invoicerate").show();
				} else {
					$("#p_invoicerate span").text('0')
					$("#p_invoicerate").hide();
				}
				$("#real_amount").text(ret.real_amount);//实际费用
			});

		},
		
		/* 设置收获地址 */
		setAddress : function(_this) {
			var address = $(_this).attr('data-addressid');
			$(_this).siblings().find('div.border-img-top-2').hide();
			$(_this).siblings().find('.add-address-make').hide();
			$(_this).siblings().removeClass('selected');
			$(_this).addClass("selected");
			$(_this).find('div.border-img-top-2').show();
			$(_this).find('.add-address-make').show();
			order.address = address;
			this.init();
		},
		
		/* 订单提交 */
		submit:function() {
			if(submitBtn.hasClass('disabled')) return false;
			order.postscript = $("input[name='postscript']").val();//订单备注
			order.invoice_title = $("input[name='invoice_title']").val();//发票抬头
			order.invoice_checked = $("input[name=invoicerate]").attr('checked');//索要发票
			order.invoice_type = $("select[name='invoice_type']").val();//发票内容
            if(order.invoice_checked == 'checked' && !order.invoice_title){
				alert('请填写发票抬头');
				return false;
			}
			if (order.pay_type == undefined) {
				alert('请选择支付方式');
				return false;
			}
			submitBtn.addClass('disabled').text('创建订单..');
			$.ajax({
				type:'GET',
				url:'?m=goods&c=order&a=submit',
				data:order,
				dataType : 'JSON',
				success:function(ret) {
					if(ret.status == 1) {
						submitBtn.text('订单创建成功');
						setTimeout("redirect('"+ ret.url +"');", 500);
					} else {
						alert(ret.info);	
						submitBtn.removeClass('disabled').text('重新提交');
						return false;
					}
				}
			});
		}
	};
	
})();