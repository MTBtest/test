/**
 *商品图册 @lcl 2014-11-11 11:46:57
 */
$(function() {
	//选择默认
	$(".filelist li img").live('click', function() {
		$(".filelist li").css("border", "1px solid #3b72a5");
		$(".filelist li").find("span").hide();
		$(this).parent().css("border", "1px solid #f48c3a");
		$(this).parent().find(".setdef").show();
		//排序
		$(this).parent().siblings().attr("order", "100");
		$(this).parent().attr("order", "99");
		var li = $('.filelist li').toArray().sort(function(a, b) {
			return parseInt($(a).attr("order")) - parseInt($(b).attr("order"));
		});
		$('.filelist').html(li);
	});
	//显示删除
	$(".filelist li").live({
		mouseenter: function() {
			$(this).find('.setdel').addClass('trconb');
			$(this).find('.setdel').show();
		},
		mouseleave: function() {
			$(this).find('.setdel').removeClass('trconb');
			$(this).find('.setdel').hide();
		}
	});
	//删除确认
	$(".setdel").live('click', function() {
		if (confirm('是否删除图片?')) {
			$(this).parent().remove("li");
			return;
		}

	})
});