/**
 *显示分类 @lcl 2014-11-11 11:47:09
 */

//显示分类
function nb_category(pid, e){
    $(e).parent().nextAll('div').empty();
    $(e).parent().nextAll('div').css('background', '#EFF7FE');
    $(e).parent("div ").find("a ").removeClass("hover ");
    $(e).addClass("hover ");
    var strHTML = "";
    $.each(JsonCategory, function(InfoIndex, Info){
    if (pid == Info.parent_id)
        strHTML += " <a href = 'javascript:void(0)' onclick = 'nb_category(" + Info.id + ",this)' id = " + Info.id + " > " + Info.name + " </a>";
    });
    if (pid == 0){
    	$(".root").html(strHTML);
    } else{
    	$(e).parent().next('div').css('background', '#FFF');
        $(e).parent().next('div').html(strHTML);
    }

}
// 判断是否重复选择
function nb_checkjiafen(val) {
	var arr = [];
	$('input[name="cat_ids[]"]').each(function() {
		arr.push($(this).val());
	});
	var t1 = arr.sort().toString();
	var t2 = $.unique(arr).sort().toString();
	if (t1 == t2) {
		return false;
	} else {
		return true;
	}
}

$(function() {
	$("#jiafen").live("click", function() {
		var id = $('.fendd').find('.hover:last').attr("id");
		if ($('.fendd .hover').length > 1) {
			$(".sl").append("<div>" + $('.fendd').find('.hover:last').html() + "<em><img src='"+IMG_PATH+"admin/ico_close1.png' /></em><input name='cat_ids[]' value='" + $('.fendd').find('.hover:last').attr("id") + "' type='hidden'></div>");
		}
		if (nb_checkjiafen(id)) {
			$(".sl div:last").remove();
			alert('已选择过此类别!');
			return;
		}

	});
	$('.fendd a').live("dblclick", function() {
		if ($(this).parent().nextAll('div').text() == "") {
			$("#jiafen").click();
		} else {
			return;
		}
	});
	$(".sl div em").live("click", function() {
		$(this).parent("div").remove();
	});
}); 