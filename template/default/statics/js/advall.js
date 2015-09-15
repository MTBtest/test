
/**
 * 页头通栏广告id=1
 */	
$(function(){
	$(".advid1_close").click(function(){
			$(".advid1").hide();
		})
	});
	
/**
 * 右下角消息广告 id=2
 */
//兼容ie6的fixed代码 
//jQuery(function($j){
//	$j('#pop').positionFixed()
//})
(function($j){
    $j.positionFixed = function(el){
        $j(el).each(function(){
            new fixed(this)
        })
        return el;                  
    }
    $j.fn.positionFixed = function(){
        return $j.positionFixed(this)
    }
    var fixed = $j.positionFixed.impl = function(el){
        var o=this;
        o.sts={
            target : $j(el).css('position','fixed'),
            container : $j(window)
        }
        o.sts.currentCss = {
            top : o.sts.target.css('top'),              
            right : o.sts.target.css('right'),              
            bottom : o.sts.target.css('bottom'),                
            left : o.sts.target.css('left')             
        }
        if(!o.ie6)return;
        o.bindEvent();
    }
    $j.extend(fixed.prototype,{
        ie6 : $.browser.msie && $.browser.version < 7.0,
        bindEvent : function(){
            var o=this;
            o.sts.target.css('position','absolute')
            o.overRelative().initBasePos();
            o.sts.target.css(o.sts.basePos)
            o.sts.container.scroll(o.scrollEvent()).resize(o.resizeEvent());
            o.setPos();
        },
        overRelative : function(){
            var o=this;
            var relative = o.sts.target.parents().filter(function(){
                if($j(this).css('position')=='relative')return this;
            })
            if(relative.size()>0)relative.after(o.sts.target)
            return o;
        },
        initBasePos : function(){
            var o=this;
            o.sts.basePos = {
                top: o.sts.target.offset().top - (o.sts.currentCss.top=='auto'?o.sts.container.scrollTop():0),
                left: o.sts.target.offset().left - (o.sts.currentCss.left=='auto'?o.sts.container.scrollLeft():0)
            }
            return o;
        },
        setPos : function(){
            var o=this;
            o.sts.target.css({
                top: o.sts.container.scrollTop() + o.sts.basePos.top,
                left: o.sts.container.scrollLeft() + o.sts.basePos.left
            })
        },
        scrollEvent : function(){
            var o=this;
            return function(){
                o.setPos();
            }
        },
        resizeEvent : function(){
            var o=this;
            return function(){
                setTimeout(function(){
                    o.sts.target.css(o.sts.currentCss)      
                    o.initBasePos();
                    o.setPos()
                },1)    
            }           
        }
    })
})(jQuery)


function Pop(title,url,intro){
	this.title=title;
	this.url=url;
	this.intro=intro;
	this.apearTime=1000;
	this.hideTime=500;
	this.delay=10000;
	//添加信息
	this.addInfo();
	//显示
	this.showDiv();
	//关闭
  this.closeDiv();
}


Pop.prototype={
  addInfo:function(){
//  $("#popTitle a").attr('href',this.url).html(this.title);
//  $("#popIntro").html(this.intro);
//  $("#popMore a").attr('href',this.url);
  },
  showDiv:function(time){
		if (!($.browser.msie && ($.browser.version == "6.0") && !$.support.style)) {
      $('#pop').slideDown(this.apearTime).delay(this.delay).fadeOut(400);;
    } else{//调用jquery.fixed.js,解决ie6不能用fixed
      $('#pop').show();
			jQuery(function($j){
			    $j('#pop').positionFixed()
			})
    }
  },
  closeDiv:function(){
  	$("#popClose").click(function(){
  		  $('#pop').hide();
  		}
    );
  }
}
//使用参数：1.标题，2.链接地址，3.内容简介
window.onload=function(){
	var pop=new Pop("1.标题","#","内容");
}


/**
 * 滚屏居中广告id=3 
 */
$(function(){
		// 给关闭按钮和遮罩绑定点击之后关闭弹框事件
		$('.mask, .login-box .close').click(function(){
			closeDialog();
		});
		openDialog();
});
	// 打开弹框
	function openDialog () {
		$('.mask, .login-box').fadeIn(200);
	};
	// 关闭弹框
	function closeDialog () {
		$('.mask, .login-box').fadeOut(150);
	};

/**
 * 对联广告id=4
 */
$(document).ready(function(){

	$(".fixediv a").click(function(){
		
		$(".fixediv").fadeOut(400);
		
	});
	
	$(".fixediv").floatadv();
	
});

jQuery.fn.floatadv = function(loaded) {
	var obj = this;
	body_height = parseInt($(window).height());
	block_height = parseInt(obj.height());
	
	top_position = parseInt((body_height/3) - (block_height/3) + $(window).scrollTop());
	if (body_height<block_height) { top_position = 0 + $(window).scrollTop(); };
	
	if(!loaded) {
		obj.css({'position': 'absolute'});
		obj.css({ 'top': top_position });
		$(window).bind('resize', function() { 
			obj.floatadv(!loaded);
		});
		$(window).bind('scroll', function() { 
			obj.floatadv(!loaded);
		});
	} else {
		obj.stop();
		obj.css({'position': 'absolute'});
		obj.animate({ 'top': top_position }, 400, 'linear');
	}
};

/**
 * 列表页左侧广告id=6
 */
$(function(){
	$(".advbox6 .close").click(function(){
			$(".advbox6").hide();
		})
	});
	
/**
 * 列表页产品上方广告id=7
 */
$(function(){
	$(".advbox7 .close").click(function(){
			$(".advbox7").hide();
		})
	});

