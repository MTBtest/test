var goods_cart = (function() {
    var cart_keys = new Array();
    return {
        /* 获取购物车 */
        initialize :function() {
            var cart_info = {
                total : 0,
                nums : 0
            }
            cart_keys = new Array();
            $('dl.selected').each(function(i, item) {
                var num = parseInt($(this).attr('data-num')), price = parseFloat($(this).attr('data-price')), total = num * price;
                cart_keys.push($(this).attr('data-id'));
                cart_info.nums = cart_info.nums +  num;
                cart_info.total = cart_info.total +  total;
            });
            cart_info.total = cart_info.total.toFixed(2);
            $("#total").text(cart_info.total);
            $("#nums").text(cart_info.nums);
            if(cart_info.nums < 1) {
                $("#btn_buy").removeClass('hover').addClass('disabled');
            } else {
                $("#btn_buy").removeClass('disabled').addClass('hover');
            }
        },

        /* 删除购物车 */
        delete : function(timestamp) {
            hd_alert('确定删除此商品？',2000,'confirm', function() {
                $.post('?m=goods&c=cart&a=delete', {
                    timestamp: timestamp,
                    random : Math.random
                }, function(ret) {
                    if(ret.status == 1) {
                        $("dl.shop[data-id="+ timestamp +"]").remove();                     
                        if($("dl.shop").length == 0) {
                            window.location.reload();
                        }
                    } else {
                        hd_alert(ret.info);
                        return;
                    }
                }, 'JSON');
            });
        },

        /* 更新购物车 */
        update : function(timestamp, num) {
            $.getJSON('?m=goods&c=cart&a=update', {
                timestamp: timestamp,
                num:num,
                random : Math.random
            }, function(ret) {
                if(ret.status == 1) {
                    goods_cart.initialize();
                } else {
                    window.reload();
                }
            })
        },

        /* 创建订单 */
        createOrder : function() {
            var createURI = '?m=goods&c=order&a=index&keys='+ cart_keys.join(',');
            window.location.href = createURI;
        }

    }
})();