/**
 * Created by Administrator on 2014/9/4.
 */
$(function() {
	//菜单折叠
		$(".headermenu").hover(function() {
			//            $(".headermenu_li").stop().slideToggle(400);
			$(".headermenu_li").show();
		}, function() {
			$(".headermenu_li ul li").removeClass('listli_01hover').addClass('listli_01');
			// $(".headermenu_li").slideToggle(400);
			$(".headermenu_li").hide();
		})
	//子菜单
	$('.headermenu_li ul li').hover(function() {

		$(this).removeClass('listli_01').addClass('listli_01hover').siblings('li').removeClass('listli_01hover').addClass('listli_01');
		$(".listli_01box_ul li").removeClass("listli_01").removeClass("listli_01hover");
		//            $('.list-con').hide();
		$(this).find('.list-con').show().siblings("li").find(".list-con").hide();

	}, function() {
		$('.list-con').hide();
	});
	//    菜单换图片
	/*    $(".headermenu").hover(function () {
	        var _tc = $(".menudown").attr("src");
	        if (_tc == "../style/images/homeimages/down_11.png") {
	            $(".menudown").attr("src", "../style/images/homeimages/up_11.png");
	        } else {
	            $(".menudown").attr("src", "../style/images/homeimages/down_11.png");
	        }
	    });*/
});