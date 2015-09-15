/**
 *浮动栏_Floatmenu
 */
$(function() {
	elems("top10", "cur");
	//    固定caseboxr
	jQuery(function() {
		//指定的高度，侧边栏距顶部距离+侧边栏高度+可视页面的高度
		var sideTop = jQuery(".Floatmenu").offset().top;
		var scTop = function() {
			if (typeof window.pageYOffset != 'undefined') {
				return window.pageYOffset;
			} else if (typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat') {
				return document.documentElement.scrollTop
			} else if (typeof document.body != 'undefined') {
				return document.body.scrollTop;
			}
		};
		jQuery(window).scroll(function() {
			if (scTop() > sideTop) {
				if (jQuery("#fixed-siderbar").length == 0) {
					//下面是要显示的模块，复制侧边栏中的标签云内容，追加到侧边栏的底部
					var tag = jQuery(".Floatmenu").clone().html();
					var html = "<div id='fixed-siderbar'><div id='fixed-wrap'><div id='fixedTag' class='widget  widget_tag_cloud' >" + tag + "</div></div></div>";
					jQuery(".Floatmenu").after(html);
				} else {
					jQuery(".Floatmenu").hide();
					jQuery("#fixed-siderbar").show();
				}
			} else {
				jQuery(".Floatmenu").show();
				jQuery("#fixed-siderbar").hide();
			};
		});
	});

	/**
	 * 折叠频道
	 */
	$(function() {
		$(".sidebarBox_Title").click(function() {
			var _self = $(this).find("i");
			$(this).next(".sidebarBox_Title_li").slideToggle(500, function() {
				if (_self.attr('class') == "reduce_list") {
					_self.removeClass().addClass("add_list");
				} else {
					_self.removeClass().addClass("reduce_list");
				}
			})
		})
	});

	/**
	 * 更多品牌-展开收起
	 */
	$(function() {
		$("#b1").click(function() {
			$(".Milkname_P01").toggleClass("aa");
		})
	});
	$(function() {
		$("#b12").click(function() {
			$(".Milkname_P01").toggleClass("aa");
		})
	});

	/**
	 * 更多按钮箭头
	 */
	$(".Milkname_morebtn").click(function() {
		$(".Milkname_more").toggle();
		$(".Milkname_more2").toggle();
	});

	/**
	 * 一周排行
	 * @param {type} id 当前ID
	 * @param {type} cur 总数
	 */
	function elems(id, cur) {
		var id = document.getElementById(id).getElementsByTagName("li");
		for (var i = 0; i < id.length; i++) {
			id[0].className = "cur";
			id[i].onmouseover = function() {
				this.className = "";
				for (var j = 0; j < id.length; j++) {
					if ((id[j].getAttribute("class") == cur) || (id[j].getAttribute("className") == cur)) {
						id[j].className = "";
						break;
					}
				}
				this.className = cur;
			}
		}
	}
});