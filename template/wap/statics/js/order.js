/* 确认收货 */
function _confirm(order_sn) {
    hd_alert("确定该操作吗？" , 2000 , 'confirm' , function() {
        $.post("?m=user&c=order&a=confirm", {
        	order_sn:order_sn
        }, function(ret) {
        	if(ret.status == 1) {
        		location.reload();
        		return true;
        	} else {
        		hd_alert(ret.info);
        		return false;
        	}
        }, 'JSON')
    })
}

/* 取消订单 */
function _cancel(order_sn) {
    hd_alert("确定取消该订单吗？" , 2000 , 'confirm' , function() {
        $.post("?m=user&c=order&a=cancel", {
            order_sn:order_sn
        }, function(ret) {
            if(ret.status == 1) {
                location.reload();
                return true;
            } else {
                hd_alert(ret.info);
                return false;
            }
        }, 'JSON')
    })
}

