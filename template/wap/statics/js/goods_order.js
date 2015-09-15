var goods_order = (function() {
    var order = {

    }
    var submitBtn = $("#btn_order");
    return {
        /* 获取订单数据 */
        initialize :function() {
            order = {
                address: norder_keys.address_id,
                pay_type: norder_keys.pay_type,
                delivery_id: norder_keys.delivery_id,
                promotion_id: norder_keys.promotion_id,
                sn: norder_keys.coupons_sn,
                invoicerate: norder_keys.invoicerate,
            }
            $.getJSON('?m=goods&c=order&a=getOrderInfo', order, function(ret) {
                $("#real_amount").text(ret.real_amount);//实际费用
            });
        },

        /* 创建订单数据 */
        submit : function(timestamp, num) {
            if(submitBtn.hasClass('disabled')) return false;
            order.postscript = $("input[name='postscript']").val();//订单备注
            order.invoice_title = norder_keys.invoice_title;
            order.invoice_type = norder_keys.invoice_type;

            submitBtn.addClass('disabled').text('提交中');
            $.post('?m=goods&c=order&a=submit', order, function(ret) {
                if(ret.status == 1) {
                    submitBtn.text('订单创建成功');
                    setTimeout("redirect('"+ ret.url +"');", 500);
                } else {
                    hd_alert(ret.info);
                    submitBtn.removeClass('disabled').text('重新结算');
                    return false;
                } 
            }, 'JSON');
        }

    }
})();