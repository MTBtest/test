/**
 * 加减数量
 */
$(function () {
    /**
     * 减商品数量
     */
    $(".goods-num-reduce").click(function () {
        var _self = $(this).next(".goods-number");  //当前输入框的下一个元素
        var val = _self.val();      //输入框的值
        val = parseInt(val) - 1;      //值减去1
        _self.val(val);         //输入框的值就等于点击后的值
        if (val < 1) {
            val = 1;
            _self.val(val);
        }
        //总数等于
        //getTotal();
//
    });
    /**
     * 加商品数量
     */
    $(".goods-num-add").click(function () {
//
        var _self = $(this).prev(".goods-number");  //当前输入框的下一个元素
        var val = _self.val();      //输入框的值
        val = parseInt(val) + 1;      //值加上1
        if(val > $("#data_goodsNumber").html()){
            _self.val($("#data_goodsNumber").html());
        }else{
            _self.val(val);     //输入框的值就等于点击后的值
        }
        //总数等于
        //getTotal();
    });
    $(".new-address-img").click(function () {
        $(".add-new-address").hide();
    })

});

/**
 * 选中的商品改变选择样式
 */
$(window).load(function () {
    $(".add-address-2").click(function () {
        $(".border-img-top-3").show();

        $(".add-address-2 .add-address-make-1").show();
        $(".border-img-top-2").hide();

        $(".add-address-make").hide();
    });
    $(".add-address-1").click(function () {
        $(".border-img-top-3").hide();

        $(".add-address-2 .add-address-make-1").hide();
        $(".border-img-top-2").show();

        $(".add-address-make").show();
    });

    /**
     * 浮动商品详情
     */
    $('.goods-details-R-float li').click(function(){
        var ind = $(this).index();
        var topVal;
        if(ind==0){
            topVal = $('.goods-details-R').offset().top;
        }else if(ind==1){
            topVal = $('.goods-details-R-comment').offset().top;
        }else if(ind==2){
            topVal = $('#zixun').offset().top;
        }
        if($(".goods-details-txt").css("display")=="block"&&ind!=0){
            topVal = topVal-$(".goods-details-txt").height()-61;
        }
        $('body,html').stop().animate({scrollTop:topVal-40},1000)
    })
    
    var sideTop = $(".goods-details-R").offset().top;
    $(window).scroll(scrolls);
    scrolls();
    function scrolls(){
        var fixRight = $('.goods-details-R-float li');
        var sTop = $(window).scrollTop();
        var f1,f2,f3;
        f1 = $('.goods-details-R').offset().top;
        f2 = $('.goods-details-R-comment').offset().top;
        f3 = $('#zixun').offset().top;
        if(sTop>f1){
            $(".goods-details-R-float").css({position:"fixed"});
            $(".goods-details-txt").hide();
        }else{
            $(".goods-details-R-float").css({position:"relative"});
            $(".goods-details-txt").show();
        }
        if(sTop>=f1){
            fixRight.eq(0).addClass('pro-datails-cursor').siblings().removeClass('pro-datails-cursor');
        }
        if(sTop>=f2-40){
            fixRight.eq(1).addClass('pro-datails-cursor').siblings().removeClass('pro-datails-cursor');
        }
        if(sTop>=f3-40){
            fixRight.eq(2).addClass('pro-datails-cursor').siblings().removeClass('pro-datails-cursor');
        }
    }
});
