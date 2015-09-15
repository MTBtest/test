var order_action = (function() {
	var content = '';
	
	function _ajaxpost(url, data, content) {
		art.dialog.prompt(content, function(val){
			data.msg = val;
			$.post(url, data, function(ret) {
				if(ret.status == 1) {
					window.location.reload();
				} else {
					alert(ret.info);
					return false;
				}
			},'JSON');
			return;
		});

	};
	
	/* 订单初始化 */
	return {
		/* 订单确认 */
		order : function(order_status) {
			var param = {
				order_sn : order.order_sn,
				order_status:order_status,
				action:'order'
			};		
			switch (order_status){
				case 1:
					content = '是否确认订单信息无误';
					break;
				case 2:
					content = '是否确认订单已收货并收款';
					break;
				case 3:
					content = '是否确认订单信息无效并作废';
					break;
				case 4:
					content = '是否确认取消订单';
					break;
				default:
					break;
			}
			_ajaxpost(_post, param, content);
		},
		
		/* 付款状态 */
		pay:function(pay_status) {
			var param = {
				order_sn : order.order_sn,
				pay_status:pay_status,
				action:'pay'
			};
			switch (pay_status){
				case 1:
					content = '是否确认已收到订单款项';
					break;
				default:
					break;
			}			
			_ajaxpost(_post, param, content);
		},
		/* 退款 */
		refund:function(pay_status){
			var param = {
				order_sn : order.order_sn,
				pay_status:pay_status,
				action:'refund'
			};
			var content = '是否确认已为该订单退款';
			_ajaxpost(_post, param, content);
		},
		/* 发货状态 */
		delivery:function(delivery_status){
			delivery_status = parseInt(delivery_status);			
			switch (delivery_status){
				case 1:
					art.dialog({
						padding: '0px ',
						id: 'editMoneybox',
						icon: 'question',
						fixed: true,
						lock: true,
						background: '#ddd',
						opacity: 0.3,
						title: '确认发货',
						content: document.getElementById('editDeliverybox'),
						ok:function() {
							var param = {
								order_sn : order.order_sn,
								order_id : order.id,
								delivery_status:delivery_status,
								action:'delivery',
								delivery_id:$("select[name=delivery]").val(),
								delivery_txt:$("select[name=delivery] option:selected").text(),
								delivery_sn : $("input[name=delivery_sn]").val()
							};
							$.post(_post, param , function(ret) {
								if(ret.status == 1) {
									alert(ret.info);
									window.location.reload();
									return true;
								} else {
									alert(ret.info);
									return false;
								}
							}, 'JSON');
							return false;
						},
						cancel:true
					});
					break;
				case 2:
					art.dialog({
						padding: '0px ',
						id: 'order_return',
						icon: 'question',
						fixed: true,
						lock: true,
						background: '#ddd',
						opacity: 0.3,
						title: '确认退货',
						content: document.getElementById('order_return'),
						ok:function() {
							var param = {
								order_sn : order.order_sn,
								delivery_status:delivery_status,
								action:'return',
								return_status: $("input[name=return_status]:checked").val(),
								return_text: $("#return_text").val()
							};
							$.post(_post, param , function(ret) {
								if(ret.status == 1) {
									alert(ret.info);
									window.location.reload();
									return true;
								} else {
									alert(ret.info);
									return false;
								}
							}, 'JSON');
							return false;
						},
						cancel:true
					});					
					break;
				default:
					break;
			}
			
		},

		/* 物流跟踪 */
		kuaidi:function(com) {
			$.post('?m=goods&c=admin_order&a=kuaidi', {
				com:com,
				nu:order.delivery_sn
			}, function(ret) {
				if(ret.status == 1) {
					var _html = '<ul class="kuaidiRow">';
					$.each(ret.data, function(i, n) {    
						_html += '<li>' + n.time + '&nbsp;&nbsp;' + n.context + '</li>';
					});
					_html += '</ul>';
					art.dialog({
						id:'kuaidi',
						title:'物流详情&nbsp;' + '状态：' + ret.message,
						fixed:true,
						lock:true,
						content:_html,
						ok:true
					});
				} else {
					alert(ret.info);
					return false;
				}
			},'JSON');
		},
		
		view_log:function() {
			$.getJSON('?m=goods&c=admin_order&a=view_log', {
				order_sn:order.order_sn
			}, function(ret) {
				if(ret.status == 1) {
					var _html = '<table>'
					$.each(ret.data, function(i, n) {
						_html += '<tr class="order-win">';
						_html += '<td class="order-win1">'+ n.user_name +'</td>';
						_html += '<td>'+ n.action +'</td>';
						_html += '<td>'+ n.msg +'</td>';
						_html += '<td>'+ n.clientip +'</td>';
						_html += '<td>'+ n.dateline +'</td>';
						_html += '</tr>'
					});
					_html += '</table>';
					art.dialog({
						id:'view_log',
						title:'订单操作详情',
						fixed:true,
						lock:true,
						content:_html,
						ok:true
					});
				} else {
					alert(ret.info);
					return false;
				}
			})
		},
		
		/* 修改价格 */
		editPrice:function() {
			
		},
		
		init:function() {
			if (typeof(order) != 'object') {
				alert('无法读取此订单信息！');
				location.href = '/';
				return;
			}
		}

	};
})();