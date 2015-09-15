<?php
/**
* 商品分类
*/
class hotword
{
	/**
	 * 获取所有热门关键词
	 * @return array 关键词数组
	 */
	public function lists($data)
	{
		$hotword = str_replace('，',',',C('site_hotword'));
		return explode(',', $hotword);
	}
}