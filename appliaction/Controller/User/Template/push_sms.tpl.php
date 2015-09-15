<?php  include $this->admin_tpl('header'); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">会员管理</a> > 群发短信
    </div>
   	<div class="goods mt10">
        <div class="vip_ss">
        	<p><strong>搜索会员：</strong><input type="text" placeholder="输入会员用户名/手机号" class="input_ss" /><input type="submit" class="button_search" style="margin-left: 30px;" value="查询"></p>
            <ul>
            	<li>
                	<strong>会员等级：</strong><a href="#" class="hover">全部</a><a href="#">V1等级</a><a href="#">V2等级</a><a href="#">V3等级</a><a href="#">V4等级</a><a href="#">V5等级</a><a href="#">V6等级</a><span>[选择发放优惠券的会员等级]</span>
                </li>
                <li>
                	<strong>消费记录：</strong><a href="#" class="hover">全部</a><a href="#">最近7天</a><a href="#">最近30天</a><a href="#">最近3个月</a><a href="#">最近6个月</a><a href="#">最近1年</a><span>[选择最近时间段有过消费记录的会员]</span>
                </li>
                <li>
                	<strong>商品信息：</strong><strong>商品分类：</strong><select><option>所有分类</option></select><strong>商品品牌：</strong><select><option>所有品牌</option></select><span>[选择购买过某个分类商品的会员]</span>
                </li>
            </ul>
        </div>
        <div class="vip_tj mt10 clearfix">
        	<strong>会员<br />统计</strong><b>共发放短信的会员数量：<font>15235</font></b><span>请选择发放优惠券的会员条件，系统将计算共发放优惠券数量</span>
        </div>
        <div class="vip_fa mt10 clearfix">
        	<strong>短信内容：</strong>
            <textarea class="input"></textarea>
            <p>填写发送的短信内容，本功能需要先到全局设置配置短信</p>
        </div>
        <div class="submit">
            <a href="#">发　送</a><a href="#">取　消</a>
        </div>
    </div>
</div>
<?php include $this->admin_tpl('copyright'); ?>