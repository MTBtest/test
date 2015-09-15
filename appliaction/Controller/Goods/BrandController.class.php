<?php
/**
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class BrandController extends HomeBaseController
{
	public function _initialize() {
		parent::_initialize();
		$this->db = model('brand');
	}

	/* 品牌首页 */
	public function index() {
		include template('brand_index');
	}
	
	/* 品牌详情 */
	public function detail($id = 0) {
		$rs = $this->db->detail($id);
		extract($rs);
		$SEO=seo(0,$name);
		include template('brand_list');
	}
}