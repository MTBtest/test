<?php include $this->admin_tpl('header'); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">会员管理</a> > 群发站内信
    </div>
    <span class="line_white"></span>
    <div class="goods mt10">
    	  <form class="addform" name="addform" action="<?php echo U('PushMessage/lists'); ?>" method="post" autocomplete="off" submit="javascript:return false">
        <div class="vip_ss">
            <p>
                <strong>搜索会员：</strong>
                <input type="text" name='keyword' placeholder="输入会员用户名/邮箱/手机号" class="input" />
                <input type="button" value="查询" class="button_search"/>
            </p>
            <ul>
                <li class='vip_group'>
                    <strong>会员等级：</strong>
                    <a href="javascript:" class="hover" data='0'>全部</a>
                        <?php foreach ($user_group as $key => $vo): ?>
                        <a href="javascript:" data='<?php echo $vo['id']; ?>'><?php echo $vo['name']; ?></a>
                    <?php endforeach ?>
                    <span style="margin-right:68px;">[选择发放优惠券的会员等级]</span>
                </li>
                <li class='vip_order'>
                    <strong>消费记录：</strong>
                    <a href="javascript:" data='0' class="hover">全部</a>
                    <a href="javascript:" data='7'>最近7天</a>
                    <a href="javascript:" data='30'>最近30天</a>
                    <a href="javascript:" data='90'>最近90天</a>
                    <a href="javascript:" data='180'>最近180天</a>
                    <a href="javascript:" data='365'>最近1年</a>
                    <span style="margin-right:68px;">[选择最近时间段有过消费记录的会员]</span>
                </li>
                <li>
                    <strong>商品信息：</strong>
                    <strong>商品分类：</strong>
                    <select name="category_id" style="width: 160px;" class="category_id">
	                    <option value="0">请选择</option>
	                    <?php foreach ($this->treeMenu as $key => $value): ?>
	                        <option value="<?php echo $value['value']; ?>"
	                    <?php if ($value['value'] == $_GET['category_id']): ?>
	                        selected = "selected"
	                    <?php endif ?>><?php echo $value['text'] ?></option>
	                    <?php endforeach ?>
		            </select>
                   <strong>商品品牌：</strong>
                   <select name="brand_id" class="brand_id" style="margin-right: 48px;">
                            <option value="0">所有品牌</option>
                            <?php foreach ($this->brand as $key => $value): ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                            <?php endforeach ?>
                   </select><span style="margin-right:68px;">[选择购买过某个分类商品的会员]</span>
                </li>
            </ul>
        </div>
        <div class="vip_tj mt10 clearfix">
            <strong>会员<br />统计</strong><b>共发放站内信数量：<font class="member_num">0</font></b><span>请选择发放优惠券的会员条件，系统将计算共发放优惠券数量</span>
            <input type="hidden" datatype="*" name="member_num">
            <input type="hidden" datatype="*" name="member_id">
        </div>
        <div class="vip_fa mt10 clearfix">
        	<ul>
	        	<li>
		        	<strong>短件标题：</strong>
		            <input class="input" name="title" datatype="*" />
		            <p>&nbsp;&nbsp;填写发送的短件标题，本功能需要先到全局设置配置短信</p>
	           	</li>
	           	<li>
		        	<strong>短件内容：</strong>
		            <textarea class="input" name="content" datatype="*"></textarea>
		            <p>&nbsp;&nbsp;填写发送的短件内容，本功能需要先到全局设置配置短信</p>
	            </li>
            </ul>
        </div>
        <div class="submit">
            <a href="javascript:void(demo.submitForm(false))">发 送</a>
        </div>
        </form>
    </div>
     <?php include $this->admin_tpl('copyright'); ?>
</div>
    <script>
        var demo;
        $(function() {
            //搜索
            $(".button_search").click(function() {
                keyword = $(this).prev().val();
                if (keyword.length == 0) {
                    $(this).prev().focus();
                    alert('搜索内容不能为空');
                    return;
                }
                $.get("<?php echo U('PushMessage/search_user/?opt=search'); ?>", {"keyword": keyword.toString()}, function(data) {
                    if (data.status == 0) {
                        art.dialog.tips('无法查到此信息');
                        return;
                    } else {
                        //console.log(data);
                        $(".vip_ss > ul").html("<li><strong>会员信息：</strong>会员名："+data.username+" 邮箱："+data.email+" 手机："+data.mobile_phone+"</li>");
                        $(".member_num").html("1");$("input[name='member_num']").val(1);$("input[name='member_id']").val(data.id);
                    }
                })
            })
             //会员等级
            $(".vip_group >a,.vip_order >a").click(function(){
                $(this).siblings().removeClass("hover");
                $(this).addClass("hover");
                getmember();
            });
            //商品分类
            $(".category_id").change(function(){
                getmember();
            });
            //品牌列表
            $(".brand_id").change(function(){
                getmember();
            });
            //表单验证
            demo = $(".addform").Validform({
                tiptype: function(msg, o, cssctl) {
                    var e = o.obj.context.name;
                    if (e.length > 1 && o.type == 3) {
                        if (e == 'member_num') {
                            alert('没有设置获赠会员！');
                        }
                    }
                },
                showAllError: false
            });
            function getmember(){
            	//取值操作
                var group_id=$(".vip_group >a.hover").attr('data');
                group_id = group_id?group_id:0;
                var order_time=$(".vip_order >a.hover").attr('data');
                order_time = order_time?order_time:0;
                var category=$(".category_id").val();
                category = category?category:0;
                var brand_id=$(".brand_id").val();
                brand_id = brand_id?brand_id:0;
                var url = "<?php echo U('PushMessage/search_user/?opt=search'); ?>";
                $.get(url, {"group_id": group_id.toString(),"order_time": order_time.toString(),"cat_ids": category.toString(),"brand_id":brand_id.toString()}, function(data) {
                    if (data.status == 0) {
                        $(".member_num").html(0);$("input[name='member_num']").val("");$("input[name='member_id']").val("")
                        art.dialog.tips('无法查到此信息');
                        return;
                    } else {
                        $(".member_num").html(data.count);$("input[name='member_num']").val(data.count);$("input[name='member_id']").val(data.ids);
                    }
                })
            }
            //打开默认选择所有会员
           $(".vip_group >a:first").trigger("click");
        })
    </script>