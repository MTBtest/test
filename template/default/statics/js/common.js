function redirect(url) {location.href = url;}
/**
 * 二秒自动关闭提示框
 */
function msg_time(msg) {
	dialog({
		id: 'msg_time', title: '提示', content: msg, width: 300
	}).show();
	setTimeout(function () {
		dialog.get('msg_time').close().remove();
	}, 2000);
}
/**
 *系统模板图片效果 
 */
$(function(){
	$(".module_pro").hover(function()
	{
		$(this).addClass('active');
		$(this).children().find(".pro_name").stop().animate({height: '+65px'}, "500");
	},function() 
	{
		$(this).removeClass('active');
		$(this).children().find(".pro_name").stop().animate({height: '+45px'}, "500");
	});
});

/**
 * 卖家承诺 保障显示效果切换
 */
$(function(){
	$(".sell_Promise li").hover(function(){$(this).css({width: '190px'}).children("a").addClass('hover').parent().siblings().css({width: '45px'}).children("a").removeClass('hover');;})
});
/**
 * 新品推荐切换
**/

/**
 * 查看公告
 */
$(function(){$(".news li .bgblue").click(function(){$(this).parent().siblings(".notic_p").toggle(); });});	
$(function(){
	$(".fabiao em").click(function(){
		$(".fabiao").hide()
	});
	$(".wyao").click(function()
	{
		$(".fabiao").show()
	});
});
	$(".load_head").click(function(){
		$(".upload").show()
	});
	$(".upload dt em").click(function(){
		$(".upload").hide()
	});
$(function(){
	$(".jifen span").hover(function()
	{
		$(this).find("em").addClass("hover");
		$(this).find("p").show();
	},function()
	{
		$(this).find("em").removeClass("hover");
		$(this).find("p").hide();
	});
})
/**
* 浏览过的商品去掉首个商品border
**/
$(function() {$(".goods-details-L .goods-details-L-other:first").css("border", "none");})
/**
* 添加到收藏夹
**/
function favorite_add(obj){
	var id = obj;
	var url = "{:U('User/Favorite/add')}";
	$.getJSON(url, {goods_id:""+obj+""}, function(json){})
}
try{if (window.console && window.console.log) {console.clear();console.log("\u6b22\u8fce\u4f7f\u7528\u8fea\u7c73\u76d2\u5b50\u65d7\u4e0b\u6d77\u76d7\u7535\u5546\u7cfb\u7edf\uff1a");console.log("\x25\x63\x31\uff0c\u4e00\u6b3e\u514d\u8d39\u5f00\u6e90\u7684\u7535\u5b50\u5546\u52a1\u5e73\u53f0\u7ba1\u7406\u7cfb\u7edf\n\x32\uff0c\u57fa\u4e8e\u76ee\u524d\u6700\u6d41\u884c\u7684\x57\x45\x42\x32\x2e\x30\u7684\u67b6\u6784\uff08\x70\x68\x70\x2b\x6d\x79\x73\x71\x6c\uff09\n\x33\uff0c\u5f53\u524d\u6700\u6d41\u884c\u7684\u4f01\u4e1a\u7ea7\u7535\u5b50\u5546\u52a1\u7ba1\u7406\u7cfb\u7edf\n\x34\uff0c\u62e5\u6709\u8d85\u5f3a\u7684\u6269\u5c55\u6027\u80fd\u548c\u6d3b\u8dc3\u7684\u7b2c\u4e09\u65b9\u5f00\u53d1\u8005\n\x35\uff0c\u826f\u597d\u7684\u5f00\u53d1\u6846\u67b6\u3001\u6587\u6863\uff0c\u8f7b\u677e\u6269\u5c55\u3001\u5b9a\u5236\u79c1\u6709\u529f\u80fd\n","\x63\x6f\x6c\x6f\x72\x3a\x67\x72\x65\x65\x6e\x3b\x6c\x69\x6e\x65\x2d\x68\x65\x69\x67\x68\x74\x3a\x32\x35\x70\x78\x3b");console.log("\x25\x63\u5b98\u65b9\u7f51\u7ad9\uff1a\x68\x74\x74\x70\x3A\x2F\x2F\x77\x77\x77\x2E\x68\x61\x69\x64\x61\x6F\x2E\x6C\x61","\x63\x6f\x6c\x6f\x72\x3a\x72\x65\x64");console.log("\x25\x63\u5b98\u65b9\u8bba\u575b\uff1a\x68\x74\x74\x70\x3A\x2F\x2F\x62\x62\x73\x2E\x68\x61\x69\x64\x61\x6F\x2E\x6C\x61","\x63\x6f\x6c\x6f\x72\x3a\x62\x6c\x75\x65")}}catch(e){}

//设为首页
function SetHome(obj,url){
  	try{
	    obj.style.behavior='url(#default#homepage)';
	    obj.setHomePage(url);
  	}catch(e){
    if(window.netscape){
     	try{
        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
    	}catch(e){
        alert("抱歉，此操作被浏览器拒绝！\n\n请在浏览器地址栏输入“about:config”并回车然后将[signed.applets.codebase_principal_support]设置为'true'");
     	}
    }else{
    	alert("抱歉，您所使用的浏览器无法完成此操作。\n\n您需要手动将【"+url+"】设置为首页。");
    }
 }
}
  
//收藏本站
function AddFavorite(title, url) {
	try {
	   window.external.addFavorite(url, title);
	}
	catch (e) {
	    try {
	       window.sidebar.addPanel(title, url, "");
	    }
	    catch (e) {
	      alert("抱歉，您所使用的浏览器无法完成此操作。\n\n加入收藏失败，请进入新网站后使用Ctrl+D进行添加");
	    }
	}
}