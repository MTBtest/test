<?php include $this->admin_tpl('header'); ?>
<div class="content">
    <div class="site">
        Haidao Board <a href="#">订单管理</a> > 快速100API设置
    </div>
    <span class="line_white"></span>
    <div class="install mt10">
        <dl>
            <dd>
                <form name="form" method="post" action="<?php echo U('Admin/Site/insert?ct=kuaidi')?>">
                    <ul class="web">
                        <li>
                            <strong>申请好的API：</strong>
                            <input type="text" value="<?php echo getconfig('kuaidi_key')?>" class="text_input" name="kuaidi_key" /><span>请到 <a href="http://www.kuaidi100.com/openapi/" target="view_window"  class="kuaidi100">快递100官网</a> 申请</span>
                        </li>
                    </ul>
                    <div class="input1">
                         <input type="submit" class='button_search' value='提交'/>
                    </div>
                    <input type="hidden" name="files" value="kuaidi.php" />
                </form>
            </dd>
        </dl>
    </div>
	<?php include $this->admin_tpl('copyright') ?>