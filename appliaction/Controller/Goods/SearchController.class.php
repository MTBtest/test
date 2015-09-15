<?php
/**
 *      搜索中心
 *      [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      http://www.haidao.la
 *      tel:400-600-2042
 */
class SearchController extends HomeBaseController{
    public function _initialize() {
        parent::_initialize();
        $this->db = model('Goods');
    }

    /* 搜索结果页 */
    public function index() {
    	$keyword = remove_xss($_GET['keyword']);
		if(strlen($keyword) < 2 || strlen($keyword) > 30) {
			showmessage('关键字长度必须在2-30个字符之间');
		}
		$_where = "`name` LIKE '%{$keyword}%' OR `keyword` LIKE '%{$keyword}%' OR `brief` LIKE '%{$keyword}%'";
		
		if($_GET['sort']) {
            list($_sort, $_by) = str2arr($_GET['sort']);
            $_by = ($_by == 'ASC') ? 'DESC' : 'ASC';
            switch ($_sort) {
                case 'sale':
                    $_order = "`sales_number` ".(($_by == 'ASC') ? 'DESC' : 'ASC');
                    break;
                case 'hits':
                    $_order = "`hits` ".(($_by == 'ASC') ? 'DESC' : 'ASC');
                    break;
                case 'price':
                    $_order = "`min_shop_price` ".(($_by == 'ASC') ? 'DESC' : 'ASC');
                    break;
                default: 
                    break;
            } 
        }
		$page = PAGE;
		$SEO = seo(0, '搜索 '.$keyword.' 的商品结果');
        include template('search');
    }
}