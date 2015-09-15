<?php
/**
 * 后台 - 主题设置
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class ThemeController extends AdminBaseController
{
	public function _initialize() {
		parent::_initialize();
		libfile('Theme');
		$this->theme = new Theme();
	}

	/* 主题列表 */
	public function manage() {
		$lists = $this->theme->get_list();
		include $this->admin_tpl('theme_list');
	}

	/* 设置默认 */
	public function setdefault($theme = '') {
		if(empty($theme)) showmessage('参数错误');
		$file = CONF_PATH.'theme.php';
		$conf = include ($file);
		$conf['TMPL_THEME'] = $theme;
        if (is_writable($file)) {
            file_put_contents($file, "<?php \nreturn " . stripslashes(var_export($conf, true)) . ";", LOCK_EX);
            libfile('Dir');
            $dir = new Dir();
            $dir->delDir(CACHE_PATH);
            showmessage('默认主题设置成功', U('manage'), 1);
        } else {
        	showmessage('默认主题设置失败，请检查权限');
        }
	}
}