var goods_detail = (function() {
    var page = { 
        comment : 1,
        consult : 1
    };

    return {
        init : function() {
			if (typeof(goods) != 'object') {
				hd_alert('无法读取此商品信息！');
				location.href = '/';
				return;
			}
            this.getCollectStatus();
            this.getCartCount();
        },
        
        /* 收藏状态 */
        getCollectStatus:function() {
            $.getJSON("?m=user&c=collect&a=getCollectStatus", {
                goods_id:goods.goods_id
            }, function(ret) {
                if(ret.status == 1) {
                    $("#collect a").addClass("hover");
                }
            });
        },


		/* 获取购物车总数 */
		getCartCount : function () {
			$.getJSON('?m=goods&c=cart&a=getCartList', function(ret) {
				cart_nums = ret.goods_num;
				if(cart_nums > 0) {
                    $("#cart_total").text(cart_nums).show();
                } else {
                    $("#cart_total").hide();
                }
			});
		},
        
        /* 加入购物车 */
		cart_add : function() {
			if(!allow_buy) return;
			$.getJSON("?m=goods&c=cart&a=add", {
        		goods_id:goods.goods_id, 
        		product_id:goods.product_id,
        		num:1,
        		ajax:1
			}, function(ret) {
				if (ret.status == 1) {
					hd_alert('您选择的商品已加入购物车');
					cart_nums++;
					$("#cart_total").text(cart_nums).show();
				} else {
					hd_alert(ret.info);
					return false;
				}
			});
		},
        
        /* 加载评论 */
        loadComment : function() {
        	if($('p.more[data-load=comment]').hasClass('disabled') || $("div.none[data-comment=none]").length > 0) return;
            $.getJSON('?m=goods&c=api&a=comment', {
            	goods_id : goods.goods_id,
            	page : page.comment,
            	limit : 5,
            	random : Math.random
            }, function(ret) {
            	if(ret.lists === null || ret.status == 'error') {
            		if(page.comment == 1) {
            			$("#comment_result").after('<div class="none" data-comment="none"><img src="'+ theme_path +'img/ico_34.png" /><p>没有查询到记录</p></div>').show();
            			$('p.more[data-load=comment]').parent().hide();            			
            			return;
            		} else {
            			$('p.more[data-load=comment]').text('没有更多了').addClass('disabled');
            		}
            	} else {
            		var content = template('loadTemplate', ret);
            		page.comment++;
            		$('#comment_result ul').append(content);
            	}
            });
        },

        /* 加载咨询 */
        loadConsult : function() {
        	if($('p.more[data-load=consult]').hasClass('disabled') || $("div.none[data-comment=none]").length > 0) return;
            $.getJSON('?m=goods&c=api&a=consult', {
            	goods_id : goods.goods_id,
            	page : page.consult,
                status : 1,
            	limit : 5,
            	random : Math.random
            }, function(ret) {
            	if(ret.lists === null || ret.status == 'error') {
            		if(page.consult == 1) {
            			$("#consult_result").after('<div class="none"><img src="'+ theme_path +'img/ico_34.png" /><p>没有查询到记录</p></div>').show();
            			$('p.more[data-load=consult]').parent().hide();
            			return;
            		} else {
            			$('p.more[data-load=consult]').text('没有更多了').addClass('disabled');
            		}
            	} else {
            		var content = template('loadTemplate', ret);
            		page.consult++;
            		$('#consult_result ul').append(content);
            		return;	
            	}
            });        	
        }
    }
})();