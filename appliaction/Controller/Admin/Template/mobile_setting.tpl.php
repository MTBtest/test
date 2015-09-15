<?php  include $this->admin_tpl('header'); ?>
<div class="content">
    <div class="site">Haidao Board <a href="#">站点设置</a> > 移动端设置</div>
    <span class="line_white"></span>
    <div class="install tabs mt10">
        <dl>
            <form name="form" method="post" action="<?php echo U('Site/insert'); ?>">
                <dd>
                    <ul class="web p0">
                        <li>
                            <strong>是否开启移动端：</strong>
                            <label><input type="radio" name="mobile_enabled" value="1" <?php if (C('mobile_enabled')): ?>checked<?php endif ?>/>开启</label>&nbsp;
                            <label><input type="radio" name="mobile_enabled" value="0" <?php if (!C('mobile_enabled')): ?>checked<?php endif ?>/>关闭</label>
                            <span style="margin-left:204px";>必须开启放可访问</span>
                        </li>
                        <li>
                            <strong>是否自动跳转：</strong>
                            <label><input type="radio" name="mobile_redirect" value="1" <?php if (C('mobile_redirect')): ?>checked<?php endif ?>/>开启</label>&nbsp;
                            <label><input type="radio" name="mobile_redirect" value="0" <?php if (!C('mobile_redirect')): ?>checked<?php endif ?>/>关闭</label>
                            <span style="margin-left:204px";>是否允许移动端设备自动识别会跳转到移动端域名</span>
                        </li>
                        <li>
                            <strong>移动端域名：</strong>
                            <input type="text" class="text_input" name="mobile_domain" value="<?php echo C('mobile_domain') ?>" />
                            <span style="margin-left:-20px";>移动端专属域名,不带http及末尾的/(如：m.haidao.la)</span>
                        </li>
                    </ul>
                </dd>
                <div class="input1">
                    <input type="submit" value="提交" class="button_search">
                </div>
                <input type="hidden" name="files" value="mobile.php" />
            </form>
        </dl>
<?php include $this->admin_tpl('copyright') ?>