$(function(){
	$(".nav").toggle(function(){
		$(this).next(".menu").show();
		
		},function(){
		$(this).next(".menu").hide();
	
	});
	
	
	$(".nav").click(function(){
		
		$(this).next(".menu").trigger();
	
	});
	

	
	$(".zhipei span a").click(function(){
		$(".zhipei span a").removeClass("hover")
		$(this).addClass("hover");
	});
	
	// $(".zhipei p a").click(function(){
	// 	$(this).parents("li").find("p a").removeClass("hover")
	// 	$(this).addClass("hover");
	// });
	
	$(".sai dt span").click(function(){
		
		$(".sai dl").animate({left:"100%"});
		setTimeout(function() {
			$(".sai").hide();
		}, 500)
		
		
		
	});
	
	$(".dldt dt span").click(function(){
		$(".sai").show();
		$(".sai dl").animate({left:"10%"});
		
	});
	
	$(".fuku li").click(function(){
		$(".fuku li em").removeClass("hover");
		$(this).find("em").addClass("hover");
		
	});
	
	// $(".color li p a").click(function(){
	// 	$(this).parents("p").find("a").removeClass("hover");
	// 	$(this).addClass("hover");
		
	// });
	
});
$(function(){
    var tabTitle = ".dldt2 dt strong";
    var tabContent = ".dldt2 dd";
    $(tabTitle + ":first").addClass("hover");
    $(tabContent).not(":first").hide();
    $(tabTitle).unbind("click").bind("click", function(){
        $(this).siblings("strong").removeClass("hover").end().addClass("hover");
        var index = $(tabTitle).index( $(this));
        $(tabContent).eq(index).siblings(tabContent).hide().end().fadeIn(0);
    });
});
/**
 * 设置移动端标题
 * @param {type} string
 * @return mixed
 */
function setTitle(string) {
    $("h2#title").text(string);
}

function goback() {
	if(referer != -1) {
		window.location.href= referer; 
	} else {
		history.go(-1);
	}
}

function empty(v){ 
	switch (typeof v){ 
		case 'undefined' : return true; 
		case 'string' : if(trim(v).length == 0) return true; break;
		case 'boolean' : if(!v) return true; break;
		case 'number' : if(0 === v) return true; break;
		case 'object' : if(null === v) return true; 
		if(undefined !== v.length && v.length==0) return true; 
		for(var k in v){return false;} return true; 
			break; 
	}
	return false; 
}

function redirect (url) {
	window.location.href = url;
}

/* alert和confirm 弹窗效果
*
*  @ text 		: 提示内容
*  @ showTime   : 显示时间
*  @ is_sure    : 是否为confirm弹窗，传入'confirm' 即可
*  @ callback   : confirm弹窗的回调函数
*
*/
function hd_alert(text , showTime , is_sure , callback) {
	// 设置显示时间
	var showTime = (showTime) ? showTime : 1000;
	var html_text = '<style>.alert_box{-webkit-box-shadow: 0 0 2px 2px rgba(0,0,0,0.3);position:fixed;width:220px;z-index:9999;display: block; overflow: hidden;top: 50%; left: 49%; margin-left: -110px;border-radius: 4px;font-size:14px;}.alert_box .alert_content {background-color: rgba(51,51,51,0.9);border-radius: 4px;padding: 15px;  text-align: center;}.alert_box .warnMsg{  color: #fff;}.alert_box .alert_btn{ width: 190px;margin-top: 10px;}.alert_box .alert_btn .ok {width: 80px; height: 30px; line-height: 30px; color: #444; font-size: 14px; background: -webkit-gradient(linear,left top,left bottom,color-stop(0%,#eee),color-stop(100%,#999)); border: 0; border-radius: 2px; padding: 0; margin: 10px 5px 0;}.alert_box .alert_btn .cancel {  width: 80px; height: 30px; line-height: 30px; color: #444; font-size: 14px; background: -webkit-gradient(linear,left top,left bottom,color-stop(0%,#eee),color-stop(100%,#999)); border: 0; border-radius: 2px; padding: 0; margin: 10px 5px 0;}</style>';
		html_text += '<div class="alert_box" style="opacity: 1;"><div class="alert_content"><div class="warnMsg">'+ text +'</div>';
		if (is_sure == 'confirm') {
			html_text += '<div class="alert_btn"><button class="ok">确定</button><button class="cancel">取消</button></div>';
		}
		html_text += '</div></div>';
	$('body').append(html_text);
	if (is_sure == 'confirm') {
		$('.alert_btn .ok').bind('click',function() {
			callback();
			$(".alert_box").fadeOut('slow',function(){
				$(".alert_box").remove();
			});
		})
		$('.alert_btn .cancel').bind('click',function() {
			$(".alert_box").fadeOut('slow',function(){
				$(".alert_box").remove();
			});			
		})
	} else {
		setTimeout(function() {
			$(".alert_box").fadeOut('slow',function(){
				$(".alert_box").remove();
			});
		} , showTime);
	}
}