<?php include $this->admin_tpl("header"); ?>
<div id="allBox" class="allBox " style="display: block;">
	<div class="allBoxSearch">
		<span class="allBoxSearchPut1 fl">
			<input type="checkbox" name="" id="" value="" class="check-all" />全选
		</span>
		<span class="allBoxSearchPut2 fr">
			<form action="<?php echo U('search_goods'); ?>" method="post" name="searchListForm" >
				<input type="hidden" name="prom_id" value="<?php echo $_GET['prom_id'] ?>">
				<input type="text" value="<?php if ($_GET['keyword']){echo $_GET['keyword'];} ?>" class="input_ss" name="keyword" placeholder="输入名称/货号均可搜索">
				<a href="javascript:void(0)" onclick="$('form').submit()" class="prosearch_03">
					<img src="<?php echo IMG_PATH; ?>admin/prosearch_03.png"/>
				</a>
			</form>
		</span>
	</div>
	<div class="clear"></div>
	<div class="allBoxTable">
		<table>
			<tr>
				<th>选择</th>
				<th>商品名称</th>
				<th>零售价</th><br />
				<th>库存</th>
			</tr>
			<?php if (empty($list)): ?>
				<tr>
					<td colspan="4">该商品不存在或已添加到其他促销活动中...</td>
				</tr>
			<?php endif; ?>
			<?php foreach($list as $k => $v):?>
			<tr>
				<td><input type="checkbox" name="id[]" id="" value="" class="ids" data-id="<?php echo $v['id']?>" data-name="<?php echo $v['name']?>" data-shop-price="<?php echo $v['shop_price']?>" data-goods-number="<?php echo $v['goods_number']?>" /></td>
				<td class="mo_text1"><?php echo $v['name']?></td>
				<td><?php echo $v['shop_price']?></td>
				<td><?php echo $v['goods_number']?></td>
			</tr>
			<?php endforeach;?>
		</table>
	</div>
	<div class="page fr">
		<?php echo $pages?>   
	</div>
</div>
<script type="text/javascript" src="<?php echo JS_PATH;?>admin_page.js"></script>