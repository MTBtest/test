<?php  include  $this->admin_tpl('header'); ?>
<block name="style">
    <style>
        #Validform_msg{display: none}
    </style>
</block>
<div class="content">
<block name="site">
    <div class="site">
        Haidao Board <a href="#">订单管理</a> > 添加订单
    </div>
    <span class="line_white"></span>
</block>
<block name="body">
    <div class="list_order">
        <div class="sm">添加的新订单默认为已确认状态。</div>
        <form method="post" class="addform" autocomplete="off" name='form' action="{$submit_url}" >
        <div class="setup setup1">
            <dl class="blue_table mt10">
                <dt><strong>商品信息</strong></dt>
                <dd>
                    <table id='OrderBox'>
                        <tr>
                            <th>商品名称</th>
                            <th>规格描述</th>
                            <th>库存合计</th>
                            <th>商品价格</th>
                            <th>商品数量</th>
                            <th>小计</th>
                            <th>操作</th>
                        </tr>
                        <!--商品模板-->
                        <script type='text/html' id='goodsTrTemplate'>
                            <tr>
                            <input type="hidden" name="data[]" value="<%=parseJSON(item.text)%>">
                            <td><%=item.name%></td>
                            <td>
                                <%if(item.spec_array){%>
                                <label class="attr">
                                    <%var spec_array = parseJSON(item.spec_array)%>
                                    <%for(var index in spec_array){%>
                                    <%var value = spec_array[index]%>
                                    <%=value['name']%>:
                                    <%if(value['type'] == 1){%>
                                    <%=value['value']%>
                                    <%}else{%>
                                    <img src="<%=value['value']%>" width="15px" height="15px" class="spec_photo" />
                                    <%}%>
                                    <%}%>
                                </label>
                                <input type='hidden' name='pid[]' value='<%=item.id%>' />
                                <%}else{%>
                                <input type='hidden' name='gid[]' value='<%=item.id%>' />
                                无规格
                                <%}%>
                            </td>
                            <td><%=item.goods_number ? item.goods_number : 0 %></td>
                            <td><%=item.shop_price ? item.shop_price : 0%></td>
                            <td><input class="input_shu" price="<%=item.shop_price ? item.shop_price : 0 %>" goods_number="<%=item.goods_number ? item.goods_number : 0 %>" value="<%=item.goods_nums ? item.goods_nums : 1%>" onchange="cnum(this)" /></td>
                            <td><span class="total_price"><%=item.shop_price%></span><input type='hidden' name='total_price[]' value='<%=item.shop_price%>' /><input type='hidden' name="goods_nums[]" value="1" /></td>
                            <td>
                                <a href="javascript:void(0)" onclick="$(this).parent().parent().remove();
                                    csum();">
                                    删除
                                </a>
                            </td>
                            </tr>
                            </script>
                        </table>
                    </dd>
                    <dt><strong>合计</strong><span class="sum_price">0</span><input type="hidden" name="sum_price" ><input type="hidden" name="product_num" ></dt>
                </dl>
                <div class="sspic clearfix mt10">
                    <span>按关键字搜索：</span><input type="text" name="goods_name" class="input_ser" placeholder="输入商品关键字搜索" />
                    <span>按商品货号搜索：</span><input type="text" name="goods_sn" class="input_ser"  placeholder="输入商品货号进行搜索"  />
                    <div class="sub fl mr40"><a href="javascript:" onclick="set_search();">搜　索</a></div>
                </div>
                <dl class="blue_table mt10 search_row">
                    <dt><strong>商品信息</strong><div class='sub fr'><a  style="margin-top: 1px;margin-right: 2px;" href='javascript:' onclick='selToorder(this)'>加入订单</a></div></dt>
                    <dd>
                        <table class="bor1" id="info">
                        </table>
                    </dd>
                </dl>
                <div class="tip" style="margin-top: 10px;margin-left: 0px;">输入费用信息后点击更新费用信息，确认修改费用信息完毕点击下一步查看订单总览</div>
                <div class="submit">
                    <a href="javascript:" onclick="showsetup('setup2')">下一步</a>
                </div>
                </dl>
            </div>
            <!-- setup1 end -->
            <div class="setup setup2" style="display:none">
                <dl class="mt10">
                    <ul class="merge">
                        <li>
                            <div class="sspic clearfix mt10">
                                <span>会员名：</span><input type="text" placeholder="默认为匿名会员" name="user_name">
                                <div class="sub fl mr40"><a onclick="user_search();" href="javascript:">搜&#12288;索</a></div>
                            </div>
                        </li>
                        <div class="tip" >
                            <li><p><label><input type="radio" name="seladd" onclick="setadd(this)" data="[]" checked> 使用新地址 </label></p></li>
                            <li style="line-height:24px;color:#636363;" class="user_address">
                                <!--用户地址模板-->
                                <script type='text/html' id='addressTemplate'>
                                    <%for(var item in templateData){%>
                                        <%item = templateData[item]%>
                                        <p><label><input type="radio" name="seladd" onclick="setadd(this)" data="<%=jsonToString(item)%>" >
                                            <%=item['address_name']%>&#12288;&#12288;
                                            <%=item['prov_name']%>
                                            <%=item['city_name']%>
                                            <%=item['dist_name']%>
                                            <%=item['address']%>&#12288;&#12288
                                            <%=item['mobile']%>&#12288;&#12288;
                                            <%=item['zipcode']%>&#12288;&#12288;
                                        </label></p>
                                    <%}%>
                                </script>
                            </li>
                        </div>
                        <li>
                            <p><strong>收货人姓名：</strong><input type="text" name="accept_name" value="" class="input_ding" style="width:155px;" /></p>
                            <p><strong>所在城市：</strong><span id="areaDiv" ></span></p>
                            <p><strong>详细地址：</strong><input type="text" name="address" value="" class="input_ding" style="width:600px;" /></p>
                            <p><strong>手机号码：</strong><input type="text" name="mobile" value="" class="input_ding" style="width:155px;" /></p>
                            <p><strong>邮政编码：</strong><input type="text" name="zipcode" value="" class="input_ding" style="width:155px;" /></p>
                            <p class="tip">确认收货地址后点击下一步确认支付/配送方式</p>
                            <input type="hidden" name="user_id" value="">
                            <input type="hidden" name="user_name" value="">
                        </li>
                    </ul>
                    <div class="submit">
                        <a href="javascript:" onclick="showsetup('setup1')">上一步</a>
                        <a href="javascript:" onclick="showsetup('setup3')">下一步</a>
                    </div>
                </dl>
            </div>
            <!-- setup2 end -->
            <div class="setup setup3" style="display:none">
                <dl class="mt10">
                    <ul class="merge">
                        <li class="user_delivery">
                                <!--用户配送模板-->
                                <script type='text/html' id='deliveryTemplate'>
                                    <%for(var item in templateData){%>
                                        <%item = templateData[item]%>
                                        <label><input type="radio" name="seldelivery" onclick="setwp(<%=item['wp1']%>,<%=item['wp2']%>)" value="<%=item['id']%>" data="<%=jsonToString(item)%>" >
                                            <%=item['name']%>(<%=item['wp1']%>,<%=item['wp2']%>)&#12288;&#12288; 
                                        </label>
                                    <%}%>
                                </script>
                            </li>
                    </ul>
                    <ul class="merge">
                       <li class="wppanel" style="display:none;"></li> 
                    </ul>
                    <div class="submit">
                        <input type='hidden' name='total_wp'>
                        <a href="javascript:" onclick="showsetup('setup2')">上一步</a>
                        <a href="javascript:" onclick="showsetup('setup4')">下一步</a>
                    </div>
                </dl>
            </div>
            <!-- setup3 end -->
            <div class="setup setup4" style="display:none">
                <dl class="mt10">
                    <div class="submit over">
                        <a href="javascript:" onclick="showsetup('setup3')">上一步</a>
                        <a href="javascript:" onclick="subform();">提交</a>
                    </div>
                </dl>
            </div>
            <!-- setup4 end -->
        </form>
         <?php include $this->admin_tpl('copyright') ?>
        </div>
    </div>
    </block>
    <block name="script">
    <script type="text/javascript" src="<?php echo JS_PATH; ?>json2.js"></script>
    <!--联动-->
    <script type="text/javascript" src="<?php echo JS_PATH; ?>jquery.showselect.js"></script>
    <!--模板-->
    <script type="text/javascript" src="<?php echo JS_PATH; ?>artTemplate/artTemplate.js"></script>
    <script type="text/javascript" src="<?php echo JS_PATH; ?>artTemplate/artTemplate-plugin.js"></script>
    <script>
        //申明变量
        var user_id;
        var seldelivery;
        var selpayment;
        var accept_name;
        var address;
        var mobile;
        var zipcode;
        var areaArr;
        var goodsArr;
        //显示下一步DIV
        function showsetup(obj) {
            if (obj=="setup2"){
                var idArr=[];
                var gidArr=[];
                $("input[name='gid[]']").each(function() {
                    idArr.push($(this).val());
                });
                $("input[name='pid[]']").each(function() {
                    gidArr.push($(this).val());
                });
                if(idArr==0 && gidArr==0){
                    return alert("至少需要选择一件商品!");
                }
            }
            if (obj=="setup3") {
                //解除input限制
                $(".sspic,.search_row,.submit,.tip").show();
                $("input,select").removeAttr("readonly");
                $("input,select").removeAttr("disabled");
                accept_name = $("input[name='accept_name']").val();
                address = $("input[name='address']").val();
                mobile = $("input[name='mobile']").val();
                zipcode = $("input[name='zipcode']").val();
                areaArr = [];
                $("select[name='areaid[]']").each(function() {
                    if($(this).val()>0){
                        areaArr.push($(this).val());
                    }
                });
                user_id = $("input[name='user_id']").val();
                if (accept_name == '') return alert('收货人姓名不能为空!');
                if (areaArr.length != 3) return alert('请选择所在城市!');
                if (address.length < 5) return alert('详细地址最少5个字!');
                if (mobile == '') return alert('手机号码不能为空!');
                if (zipcode == '') return alert('邮政编码不能为空!');
                if (accept_name == '') return alert('收货人姓名不能为空!');
                set_delivery(user_id,areaArr);
            };
            if (obj=="setup4") {
                seldelivery = $("input[name='seldelivery']:checked").val();
                if (seldelivery == undefined) {
                    seldelivery = "";
                }
                selpayment=$("input[name='selpayment']:checked").val();
                if (selpayment == undefined){
                    alert('请选择支付方式!');return;
                }  
                $(".setup").show();
                $(".sspic,.search_row,.submit,.tip").hide();
                $("." + obj).show();
                $(".over").show();
                $("input[type!='hidden'],select").attr({readonly:"readonly",disabled:"disabled"});
            }
            if(obj!="setup4"){
                $(".setup").hide();
                $("." + obj).show();
            }
        }
    </script>
    <script>
        //支付方式数据
        var paymentData=<?php echo json_encode($payment)?>;
        //地区联动
        var regionData=<?php echo json_encode($region)?>;
        var regionArr=[];
        //组织数据
        $.each(regionData,function(i,item){
            parent_id = item.parent_id == 1 ? 0 : item.parent_id;
            regionArr[i]=[item.area_id, ""+item.area_name+"", parent_id];
        });
        $("#areaDiv").showselect(regionArr); 
        //无限级菜单命名
        $("#areaDiv").live("DOMNodeInserted",function () {
            $(this).find("select").attr('name','areaid[]');
        })
    </script>
    <script>
        var goods_arr_id = [];
        var product_arr_id = [];
        /**
         * 查询商品
         */
        function set_search(start) {
            var goods_name = $("input[name='goods_name']");
            var goods_sn = $("input[name='goods_sn']");
            $.getJSON("<?php echo U('Goods/goods_list'); ?>", {"goods_name": "" + goods_name.val() + "", "goods_sn": "" + goods_sn.val() + ""}, function(res) {
                $("#info").html("<tr><th><input type='checkbox' class='check-all'></th><th>商品货号</th><th>商品名称</th><th>商品分类</th><th>本店价</th><th>库存数量</th><th>操作</th></tr>");
                $.each(res, function(i, item) {
                    if ($.isNumeric(item.goods_id)) {
                        var spec_json = $.parseJSON(item.spec_array);
                        var spec_text = "";
                        var sn = item.products_sn;
                        $.each(spec_json, function(key, val) {
                            if (val.type == 1) {
                                spec_text += val.name + ":" + val.value + " ";
                            } else {
                                spec_text += val.name + ":<img src=" + val.value + "> ";
                            }
                        });
                    } else {
                        var sn = item.sn;
                        var spec_json = null;
                        var spec_text = "无规格";
                    }
                    $("#info").append(
                            "<tr>" +
                            "<td><input class='ids' name='selid[]' type='checkbox' value='" + item.id + "' data='" + JSON.stringify(item) + "'></td>" +
                            "<td >" + sn + "</td>" +
                            "<td>" + item.name + "</td>" +
                            "<td align='left'>" + spec_text + "</td>" +
                            "<td>" + item.shop_price + "</td>" +
                            "<td>" + item.goods_number + "</td>" +
                            "<td >查看</td>" +
                            "</tr>");
                });
                $("#info").append(
                        "<tr><td colspan='9' align='right'><div class='sub fr'><a href='javascript:' onclick='selToorder(this)' style='margin-right: 2px;'>加入订单</a></div></td></tr>");
            });
        }
        /**
         * 查询地址
         */
        function user_search(){
            var user_name=$("input[name='user_name']");
            if ($.trim(user_name.val()).length == 0) return alert('请输入用户名后查找');
            $.getJSON("{:U('User_address/address')}",{"user_name": "" + $.trim(user_name.val())+""},function(res){
                var addressHtml = template('addressTemplate', {'templateData':res});
                $('.user_address').html(addressHtml);
            })
        }
        /**
         * 设置地址
         */
        function setadd(obj){
            var jsonData=JSON.parse($(obj).attr('data'));
            var address= jsonData.prov_name + jsonData.city_name + jsonData.dist_name + jsonData.address;
            address = address ? address : "" ;
            $("input[name='accept_name']").val(jsonData.address_name);
            $("input[name='address']").val(jsonData.address);
            $("input[name='mobile']").val(jsonData.mobile);
            $("input[name='zipcode']").val(jsonData.zipcode);
            $("input[name='user_id']").val(jsonData.user_id);
            $("input[name='user_name']").val(jsonData.user_name);
            var areaid=0;
            areaid = jsonData.province ? jsonData.province : areaid ;
            areaid = jsonData.city ? jsonData.city : areaid ;
            areaid = jsonData.district ? jsonData.district : areaid ;
            $("#areaDiv").html("");
            $("#areaDiv").showselect(areaid,regionArr); 
        }
        /**
         * 设置配送方式
         */
        function set_delivery(user_id,areaArr){
            $.post("<?php echo U('Site_delivery/find_list'); ?>",{"region_ids":areaArr.toString()},function(res){
                if (res.length>0){
                    var addressHtml = template('deliveryTemplate', {'templateData':res});
                    $('.user_delivery').html(addressHtml);
                }else{
                    $('.user_delivery').html('此区域无配送方式');
                    setwp(0,0);
                }
                
            },"JSON");
        }
        /**
         * 添加选择
         */
        function selToorder(o) {
            goodsArr=[];
            $("input[name='selid[]']:checked").each(function() {
                var is_ins = false;
                var temp = JSON.parse($(this).attr('data'));
                var insertObject = {"id": temp.id, "name": temp.name, "shop_price": temp.shop_price, "goods_number": temp.goods_number, "product_id": temp.product_id, "spec_array": $.parseJSON(temp.spec_array), "text": JSON.stringify($(this).attr('data'))};
                //记录ID
                if (temp.goods_id) {
                    if (nb_check("pid[]", temp.id) == false)
                        is_ins = true;
                } else {
                    if (nb_check("gid[]", temp.id) == false)
                        is_ins = true;
                }
                if (is_ins == true) {
                    insertGoods(insertObject);
                    csum();
                    goodsArr.push(temp);
                }
            });
        }
        /**
         * 判断是否重复选择
         */
        function nb_check(obj, val) {
            var arr = [];
            $('input[name="' + obj + '"]').each(function() {
                arr.push($(this).val());
            });
            arr.push(val);
            var t1 = arr.sort().toString();
            var t2 = $.unique(arr).sort().toString();

            if (t1 == t2) {
                return false;
            } else {
                return true;
            }
        }
        /**
         * 更改数量
         */
        function cnum(_self) {
            var val = $(_self).val();
            var goods_num = $(_self).attr("goods_number");
            var goods_price = $(_self).attr("price");
            if (parseInt(val) > parseInt(goods_num)) {
                $(_self).val(goods_num);
                alert('对不起,库存不足');
            }
            //更新单个计算价格
            var total_price = parseFloat(val) * parseFloat(goods_price)
            $(_self).parent().next("td").find(".total_price").html(total_price);
            $(_self).parent().next("td").find("input[name='total_price[]']").val(total_price);
            $(_self).parent().next("td").find("input[name='goods_nums[]']").val(val);
            csum();
        }
        /**
         * 更新总价
         */
        function csum() {
            var sum_price = 0;
            var product_num = 0; 
            $('input[name="total_price[]"]').each(function() {
                sum_price += parseFloat($(this).val());
            });
            //商品总数量
            $('input[name="goods_nums[]"]').each(function() {
                product_num = product_num + parseInt($(this).val());
            });
            
            $(".sum_price").html(sum_price.toFixed(2));
            $('input[name="sum_price"]').val(sum_price.toFixed(2));
            $('input[name="product_num"]').val(product_num);
        }
        /**
         * 生成商品信息
         */
        function insertGoods(goodsRow)
        {
            var goodsRow = goodsRow ? goodsRow : {};
            var goodsTrHtml = template('goodsTrTemplate', {item: goodsRow});
            $('#OrderBox').append(goodsTrHtml, goodsRow);
        }
        /**
         * 计算总运费
         */
        function setwp(wp1,wp2){
            var product_num = parseInt($('input[name="product_num"]').val());
            var total_wp = wp1+wp2==0 ? 0 : (product_num-1)*wp2+wp1;
            var paymentRow = "";
            wpRow = "共有商品 " + product_num + "件 运费" + total_wp + "元" ;
            $('input[name="total_wp"]').val(total_wp);
            $.each(paymentData,function(i,item){
                paymentRow += "<label><input type='radio' name='selpayment' value='"+item.pay_code+"' data='"+ item.pay_name +"' > " + item.pay_name + "&#12288;&#12288;</label>";
            })
            $(".wppanel").show();
            $(".wppanel").html(wpRow+"<p>"+paymentRow+"</p>");
        }
        /**
         * 提交订单
         */
        function subform(){
            //组织要提交的数据
            var postData="";
            postData = $(".addform").serialize();
            postData += "&user_id=" + user_id; 
            postData += "&pay_code=" + selpayment;
            postData += "&delivery_id=" + seldelivery;
            postData += "&accept_name=" + accept_name;
            postData += "&address=" + address;
            postData += "&mobile=" + mobile;
            postData += "&zipcode=" + zipcode;
            postData += "&province=" + areaArr[0];
            postData += "&city=" + areaArr[1];
            postData += "&area=" + areaArr[2];
            postData += "&payable_amount=" + $('input[name="sum_price"]').val();
            postData += "&real_amount=" + $('input[name="sum_price"]').val();
            postData += "&payable_freight=" + $('input[name="total_wp"]').val();
            $.post("<?php echo $submit_url; ?>",postData,function(data){
                 if (data.status == "0") {
                    art.dialog({width: 320, time: 5, title: '温馨提示(5秒后关闭)', content: data.info, ok: true});
                }
                if (data.status == "1") {
                    window.location.href = data.url;
                }
            });
        }
    </script>
    </block>