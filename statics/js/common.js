function redirect(url) {location.href = url;}
/**
 * 二秒自动关闭提示框
 */
function msg_time(msg) {
	dialog({
		id: 'msg_time',
		title: '提示',
		content: msg,
		width: 300
	}).show();
	setTimeout(function(){dialog.get('msg_time').close().remove();},
	2000);
}

/**
 * 确认对话框
 */
function msg_confirm(msg, url) {
	dialog({
		id: 'msg_confirm',
		title: '提示',
		width: 300,
		content: msg,
		okValue: '确定',
		ok: function() {
			window.location.href = url;
			return false;
		},
		cancelValue: '取消',
		cancel: function() {}
	}).show();

}

//ajax get请求
$('.confirm').live("click",
function() {
	var that = this;
	var target = $(this).attr('url');
	if ($(this).hasClass('confirm')) {
		if (!confirm('确认要执行该操作吗?')) {
			return false;
		}
		window.location.href = target;
	}
})

/**
 * 添加到收藏夹
 */
function favorite_add(obj) {
	var url = obj;
	$.getJSON(url,
	function(json) {
		if (json.status === 0) {
			msg_confirm(json.info, json.url);
			return;
		} else {
			msg_time(json.info);
		}

	})
}

$(document).ready(function($) {
	//ajax获取
	$(".ajax-get").live('click',
	function(event) {
		var url = $(this).attr('url');
		var _this = this;
		$.getJSON(url, {},
		function(data) {
			try {
				if (data.status == 1) {
					$(_this).toggleClass('ajax_on ajax_off');
				}
		} catch(e) {
			//TODO handle the exception
			}

		});
	});
});
$(function() {
    SetTableRowColor()
});
//用CSS控制奇偶行的颜色  
function SetTableRowColor() {
	$("tbody>tr:odd").addClass("even"); //为奇数行添加样式  
	$("tbody>tr:even").addClass("odd"); //为偶数行添加样式 
	$("tbody tr:first").css('height', '30px'); //固定标题行高度
	$("tbody>tr").not("tbody tr:first").mouseover(function() {$(this).addClass("selected");}).mouseout(function(){ $(this).removeClass("selected");});//鼠标移动的高亮显示
}

//排序
function EditSort(e, target){
	var target;
	var that = e;
	$.get(target).success(function(data){if(data.status == 1){location.reload();}});
}

//全选反选
$(".check-all").live("click",
function() {$(".ids:visible").prop("checked", this.checked);});
$(".ids").live("click",
function() {
	var option = $(".ids:visible");
	option.each(function(i) {
		if (!this.checked)
			{
				$(".check-all:visible").prop("checked", false);
				return false;
			} 
		else 
		{
			$(".check-all:visible").prop("checked", true);
		}
	});
});
/**
 * 显示和收起后台导航
 */
$(".ico_left").toggle(function() {$(".side").animate({left: "-200px"});
	$("#Container").animate({left: "0"});
	$(".welcome").animate({paddingLeft: "10px"});
	$(this).find("img").attr("src", respath + "images/ico_8a.png");
},
function(){
	$(".side").animate({left: "0px"});
	$("#Container").animate({left: "200px"});
	$(".welcome").animate({paddingLeft: "65px"    });
	$(this).find("img").attr("src", respath + "images/ico_8.png");
});

/**
 *日期比较 
 **/
function checkDateTime(beginValue, endValue) {
	var flag = 0;
	if (beginValue != null && beginValue != "" && endValue != null && endValue != "") 
	{
		var dateS = beginValue.split('-'); //日期是用'-'分隔,如果你日期用'/'分隔的话,你将这行和下行的'-'换成'/'即可
		var dateE = endValue.split('-');
		var beginDate = new Date(dateS[0], dateS[1], dateS[2]).getTime(); //如果日期格式不是年月日,需要把new Date的参数调整
		var endDate = new Date(dateE[0], dateE[1], dateE[2]).getTime();
		if (beginDate > endDate)
			{
				flag = 1;
			} else if (beginDate == endDate)
			{
				flag = 0;
			} 
			else
			{
				flag = -1;
			}
	}
	return flag;
}

/**
 * 倒计时
 * 
 * 只需前台调用count_down(end_time)即可;
 *
 * end_time & 到期时间戳
 *
 **/
function count_down(end_time){
	var timer = null;
	timer = setInterval(function() {
		if (end_time <= 0) {
			$('#timed').html('-');
			clearInterval(timer);
			return;
		}
		$('#timed').html(time_down(end_time));
	}, 1000);
}

function time_down(end_time) {
	var end_time = parseInt(end_time);
	var now_time = parseInt($.now() / 1000);
	var t = end_time - now_time;
	var d = 0;
	var h = 0;
	var m = 0;
	var s = 0;
	if(t >= 0){
		d = setDig(Math.floor(t /60 /60 / 24),2);
		h = setDig(Math.floor(t /60 / 60 % 24),2);
		m = setDig(Math.floor(t / 60 % 60),2);
		s = setDig(Math.floor(t % 60),2);
	}
	var _str = '';
	if (d != 0) {
		_str += "还剩&nbsp;<em>" + d + "</em> 天";
		_str += "<em> " + h + "</em> 时";
	} else {
		_str += "还剩&nbsp;<em> " + h + "</em> 时";
	}
	_str += "<em> " + m + "</em> 分";
	_str += "<em> " + s + "</em> 秒 结束";
	return _str;
}

// 不足两位时用0补齐
function setDig(num , n){ 
	var str = ""+num; 
	while(str.length < n){ 
		str="0"+str 
	} 
	return str; 
} 


try{if (window.console && window.console.log) {console.clear();console.log("\u6b22\u8fce\u4f7f\u7528\u8fea\u7c73\u76d2\u5b50\u65d7\u4e0b\u6d77\u76d7\u7535\u5546\u7cfb\u7edf\uff1a");console.log("\x25\x63\x31\uff0c\u4e00\u6b3e\u514d\u8d39\u5f00\u6e90\u7684\u7535\u5b50\u5546\u52a1\u5e73\u53f0\u7ba1\u7406\u7cfb\u7edf\n\x32\uff0c\u57fa\u4e8e\u76ee\u524d\u6700\u6d41\u884c\u7684\x57\x45\x42\x32\x2e\x30\u7684\u67b6\u6784\uff08\x70\x68\x70\x2b\x6d\x79\x73\x71\x6c\uff09\n\x33\uff0c\u5f53\u524d\u6700\u6d41\u884c\u7684\u4f01\u4e1a\u7ea7\u7535\u5b50\u5546\u52a1\u7ba1\u7406\u7cfb\u7edf\n\x34\uff0c\u62e5\u6709\u8d85\u5f3a\u7684\u6269\u5c55\u6027\u80fd\u548c\u6d3b\u8dc3\u7684\u7b2c\u4e09\u65b9\u5f00\u53d1\u8005\n\x35\uff0c\u826f\u597d\u7684\u5f00\u53d1\u6846\u67b6\u3001\u6587\u6863\uff0c\u8f7b\u677e\u6269\u5c55\u3001\u5b9a\u5236\u79c1\u6709\u529f\u80fd\n","\x63\x6f\x6c\x6f\x72\x3a\x67\x72\x65\x65\x6e\x3b\x6c\x69\x6e\x65\x2d\x68\x65\x69\x67\x68\x74\x3a\x32\x35\x70\x78\x3b");console.log("\x25\x63\u5b98\u65b9\u7f51\u7ad9\uff1a\x68\x74\x74\x70\x3A\x2F\x2F\x77\x77\x77\x2E\x68\x61\x69\x64\x61\x6F\x2E\x6C\x61","\x63\x6f\x6c\x6f\x72\x3a\x72\x65\x64");console.log("\x25\x63\u5b98\u65b9\u8bba\u575b\uff1a\x68\x74\x74\x70\x3A\x2F\x2F\x62\x62\x73\x2E\x68\x61\x69\x64\x61\x6F\x2E\x6C\x61","\x63\x6f\x6c\x6f\x72\x3a\x62\x6c\x75\x65")}}catch(e){}