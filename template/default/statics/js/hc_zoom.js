/*
HcZoom 图片缩放特效插件 power by 
*/
jQuery.fn.extend(
{
	hc_zoom : function()
	{
		$(this).each(function(){	
			//小图宽高
			var small_img_w  = $(this).width();
			var small_img_h  = $(this).height();
			//小图对象
			var small_img_obj = $(this);
			//调用核心函数
			var hcoder_zoomer = new hcoder_zoom_main(small_img_obj, small_img_w, small_img_h);
			hcoder_zoomer.run();
		});
	}
});

//核心执行函数
function hcoder_zoom_main(small_img_obj, small_img_w, small_img_h)
{
	this.small_img_obj        =  small_img_obj;
	this.small_img_w          =  small_img_w;
	this.small_img_h          =  small_img_h;
	var small_img_boder_w     =  small_img_obj.css('border-top-width') || 0;
	this.small_img_boder_w    =  parseInt(small_img_boder_w);
	if(this.small_img_boder_w != 0)
	{
		var border_width =  small_img_boder_w;
		var border_style =  small_img_obj.css('border-top-style');
		var border_color =  small_img_obj.css('border-top-color');
		var border      = small_img_boder_w+' '+border_style+' '+border_color;
	}
	else
	{
		var border = 'none';
	}
	//用户自定义信息设置信息
	var sets                  =  small_img_obj.find('img').eq(1).attr('hc_zoom');
	if(typeof(sets) != 'undefined')
	{
		eval('this.sets = {'+sets+'};');
		if(typeof(this.sets.position) == 'undefined')
		{
			this.sets.position = 'right';
		}
		
		if(typeof(this.sets.margin) == 'undefined')
		{
			this.sets.margin = 25;
		}
		
		if(typeof(this.sets.border) == 'undefined')
		{
			this.sets.border = '5px solid #888888';
		}
		
		if(typeof(this.sets.bg_color) == 'undefined')
		{
			this.sets.bg_color = '#FFFFFF';
		}
		
		if(typeof(this.sets.opacity) == 'undefined')
		{
			this.sets.opacity = 0.3;
		}
		
		if(typeof(this.sets.large_border) == 'undefined')
		{
			this.sets.large_border = border;
		}
		
		if(typeof(this.sets.auto_change) == 'undefined')
		{
			this.sets.auto_change = true;
		}
	}
	else
	{
		this.sets             = {
									'position' : 'right',
									'margin'   : 25,
									'border'   : '5px solid #888888',
									'bg_color' : '#FFFFFF',
									'opacity'  : 0.3,
									'large_border'   : border,
									'auto_change'    : true
								};
	}
	//初始比例
	this.hc_zoom_proportion   =  2;
	//放大镜初始宽度
	this.zoomer_w             =  this.small_img_w / this.hc_zoom_proportion;
	//放大镜初始高度
	this.zoomer_h             =  this.small_img_h / this.hc_zoom_proportion;
	//大图url
	this.large_image          =  this.small_img_obj.find('img').eq(1).attr('src');
	//鼠标坐标
	this.hc_mouse             = {x:0, y:0};
	
	//获取小图offset
	this.get_offset           = function()
	{
		var small_offset      = this.small_img_obj.offset();
		small_offset.left     = small_offset.left + this.small_img_boder_w;
		small_offset.top      = small_offset.top  + this.small_img_boder_w;
		return small_offset;
	}
	
	this.run            =  function()
	{
		//定义局部变量用来传递 this
		var hc_zoomer_obj         = this;
		//改善鼠标样式
		this.small_img_obj.css({'cursor':'crosshair'});
		//嵌入放大镜
		$('<div style="width:'+(this.zoomer_w - 10)+'px; height:'+(this.zoomer_h - 10)+'px; left:0px; top:0px; position:absolute; box-shadow: -0px -0px 10px rgba(0,0,0,0.40); z-index:10; border:'+this.sets.border+'; display:none;" class="hcoder_zoomer"></div><div style="position:absolute; left:0px; top:0px; width:'+this.small_img_w+'px; height:'+this.small_img_h+'px; z-index:12; display:none; border:'+this.sets.large_border+'; overflow:hidden;" class="hc_zoom_large_img"><img src="'+this.large_image+'" style="width:'+(this.small_img_w * this.hc_zoom_proportion)+'px;" /></div>').appendTo(this.small_img_obj);
		//嵌入周围四个矩形
		for(i = 1; i <= 4; i++)
		{
			$('<div class="hcoder_zoomer_'+i+'" style="position:absolute; left:0px; top:0px; width:50px; height:50px; z-index:9; background-color:'+this.sets.bg_color+'; display:none;"></div>').appendTo(this.small_img_obj);
			this.small_img_obj.find('.hcoder_zoomer_'+i).css({'opacity':this.sets.opacity});
		}
		//鼠标移入展示放大镜及大图
		this.small_img_obj.hover
		(
			function(e)
			{
				var small_offset  = hc_zoomer_obj.get_offset();
				if(hc_zoomer_obj.sets.position == 'left')
				{
					$(this).find('.hc_zoom_large_img').css({left: small_offset.left - $(this).outerWidth() - hc_zoomer_obj.sets.margin - hc_zoomer_obj.small_img_boder_w, top:small_offset.top - hc_zoomer_obj.small_img_boder_w});
				}
				else
				{
					$(this).find('.hc_zoom_large_img').css({left: small_offset.left + $(this).outerWidth() + hc_zoomer_obj.sets.margin - hc_zoomer_obj.small_img_boder_w, top:small_offset.top - hc_zoomer_obj.small_img_boder_w});
				}
				$(this).find('div').show();
				hc_zoomer_obj.set_zoomer(e, small_offset);
			}
			,
			function()
			{
				$(this).find('.hc_zoom_large_img').hide();
				$(this).find('.hcoder_zoomer').hide();
				for(i = 1; i <= 4; i++)
				{
					$(this).find('.hcoder_zoomer_'+i).hide();
				}
			}
		);
		
		//鼠标移动
		this.small_img_obj.mousemove(function(e)
		{
			hc_zoomer_obj.set_zoomer(e, hc_zoomer_obj.get_offset());
		});
		
		//滑轮滚动
		this.small_img_obj.mousewheel(function(event, delta)
		{
			if(!hc_zoomer_obj.sets.auto_change){return false;}
			hc_zoomer_obj.small_img_obj.find('.hc_zoom_large_img').find('img').stop();
			if(delta > 0)
			{
				hc_zoomer_obj.hc_zoom_proportion -= 0.1;
				if(hc_zoomer_obj.hc_zoom_proportion <= 1)
				{
					hc_zoomer_obj.hc_zoom_proportion = 1;
				}
			}
			else
			{
				hc_zoomer_obj.hc_zoom_proportion += 0.1;
				if(hc_zoomer_obj.hc_zoom_proportion >= 4)
				{
					hc_zoomer_obj.hc_zoom_proportion = 4;
				}
			}
			hc_zoomer_obj.zoomer_w = hc_zoomer_obj.small_img_w / hc_zoomer_obj.hc_zoom_proportion;
			hc_zoomer_obj.zoomer_h = hc_zoomer_obj.small_img_h / hc_zoomer_obj.hc_zoom_proportion;
			hc_zoomer_obj.small_img_obj.find('.hcoder_zoomer').css({width:hc_zoomer_obj.zoomer_w - 10, height:hc_zoomer_obj.zoomer_h - 10});
			hc_zoomer_obj.small_img_obj.find('.hc_zoom_large_img').find('img').animate({width: hc_zoomer_obj.small_img_w * hc_zoomer_obj.hc_zoom_proportion},500);
			var ev = {pageX:hc_zoomer_obj.hc_mouse.x, pageY:hc_zoomer_obj.hc_mouse.y};
			hc_zoomer_obj.set_zoomer(ev, hc_zoomer_obj.get_offset());
		});
	}
	
	//放大镜位置调整
	this.set_zoomer      = function(ev, small_offset)
	{
		this.hc_mouse.x = ev.pageX;
		this.hc_mouse.y = ev.pageY;
		//界限左
		if(this.hc_mouse.x - (this.zoomer_w / 2) < small_offset.left)
		{
			this.hc_mouse.x = small_offset.left + (this.zoomer_w / 2);
		}
		//界限右
		if(this.hc_mouse.x + (this.zoomer_w / 2) > small_offset.left + this.small_img_w)
		{
			this.hc_mouse.x = small_offset.left + this.small_img_w - (this.zoomer_w / 2);
		}
		//界限上
		if(this.hc_mouse.y - (this.zoomer_h / 2) < small_offset.top)
		{
			this.hc_mouse.y = small_offset.top + (this.zoomer_h / 2);
		}
		//界限下
		if(this.hc_mouse.y + (this.zoomer_h / 2) > small_offset.top + this.small_img_h)
		{
			this.hc_mouse.y = small_offset.top + this.small_img_h - (this.zoomer_h / 2);
		}
		//改变zoomer 位置
		this.small_img_obj.find('.hcoder_zoomer').css(
		{
			left : this.hc_mouse.x - (this.zoomer_w / 2),
			top  : this.hc_mouse.y - (this.zoomer_h / 2)
		});
		
		//改变第1个div
		this.small_img_obj.find('.hcoder_zoomer_1').css(
		{
			width  : this.small_img_w,
			height : (this.hc_mouse.y - (this.zoomer_h / 2) - small_offset.top),
			top: small_offset.top,
			left: small_offset.left
		});
		//改变第2个div
		this.small_img_obj.find('.hcoder_zoomer_2').css(
		{
			width  : this.hc_mouse.x - small_offset.left - (this.zoomer_w / 2),
			height : this.zoomer_h,
			left   : small_offset.left,
			top    : this.hc_mouse.y - (this.zoomer_h / 2)
		});
		//改变第3个div
		this.small_img_obj.find('.hcoder_zoomer_3').css(
		{
			width  : small_offset.left + this.small_img_w  - this.hc_mouse.x - (this.zoomer_w/2),
			height : this.zoomer_h,
			top    : this.hc_mouse.y - (this.zoomer_h / 2),
			left   : this.hc_mouse.x + (this.zoomer_w / 2)
		});
		//改变第4个div
		this.small_img_obj.find('.hcoder_zoomer_4').css(
		{
			width  : this.small_img_w,
			height : small_offset.top + this.small_img_h - this.hc_mouse.y - (this.zoomer_h / 2),
			left   : small_offset.left,
			top    :  this.hc_mouse.y + (this.zoomer_h / 2)
		});
		
		this.set_large_image(small_offset);
	}
	
	//调整大图
	this.set_large_image    = function(small_offset)
	{
		var zoomer_sets     = this.small_img_obj.find('.hcoder_zoomer').offset();
		this.small_img_obj.find('.hc_zoom_large_img').find('img').css({'margin-left': (small_offset.left - zoomer_sets.left) * this.hc_zoom_proportion, 'margin-top': (small_offset.top - zoomer_sets.top) * this.hc_zoom_proportion});
	}
}

jQuery.fn.extend(
{
    mousewheel     : function(up, down, preventDefault)
	{
        return this.hover
		(
            function()
			{
                jQuery.event.mousewheel.giveFocus(this, up, down, preventDefault);
            }
			,
            function()
			{
                jQuery.event.mousewheel.removeFocus(this);
            }
        );
    }
	,
    mousewheeldown : function(fn, preventDefault)
	{
        return this.mousewheel(function(){}, fn, preventDefault);
    }
	,
    mousewheelup   : function(fn, preventDefault)
	{
        return this.mousewheel(fn, function(){}, preventDefault);
    }
	,
    unmousewheel   : function()
	{
        return this.each(function()
		{
            jQuery(this).unmouseover().unmouseout();
            jQuery.event.mousewheel.removeFocus(this);
        });
    },
    unmousewheeldown: jQuery.fn.unmousewheel,
    unmousewheelup: jQuery.fn.unmousewheel
});

jQuery.event.mousewheel = {
	giveFocus: function(el, up, down, preventDefault)
	{
        if (el._handleMousewheel) jQuery(el).unmousewheel();
		
        if (preventDefault == window.undefined && down && down.constructor != Function)
		{
            preventDefault = down;
            down = null;
        }

        el._handleMousewheel = function(event)
		{
			if (!event) event = window.event;
			if (event.stopPropagation) event.stopPropagation();
			else event.cancelBubble = true;
			if (event.preventDefault) event.preventDefault();
			else event.returnValue = false;
			
           	var delta = 0;
            if (event.wheelDelta)
			{
                delta = event.wheelDelta/120;
                if (window.opera) delta = - delta;
            }
			else if(event.detail)
			{
                delta = -event.detail / 3;
            }
			
            if (up && (delta > 0 || !down)) up.apply(el, [event, delta]); else if (down && delta < 0) down.apply(el, [event, delta]);
        };

        if(window.addEventListener)
		{
			window.addEventListener('DOMMouseScroll', el._handleMousewheel, false);
		}
        window.onmousewheel = document.onmousewheel = el._handleMousewheel;
    }
	,
    removeFocus: function(el) {
        if (!el._handleMousewheel) return;

        if (window.removeEventListener)
            window.removeEventListener('DOMMouseScroll', el._handleMousewheel, false);
        window.onmousewheel = document.onmousewheel = null;
        el._handleMousewheel = null;
    }
};