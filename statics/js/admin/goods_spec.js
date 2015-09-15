/**
 *添加规格 @lcl 2014-11-11 11:47:09
 */
function selSpec() {
	var model_id = $('[name="model_id"]').val();
	var goods_id = $('[name="id"]').val();
	tempUrl = tempUrl.replace('@model_id@', model_id);
	tempUrl = tempUrl.replace('@goods_id@', goods_id);
	art.dialog.open(tempUrl, {
		title: '设置商品的规格',
		background: '#ddd',
		opacity: 0.3,
		okVal: '生成所有规格',
		width: 620,
		ok: function(iframeWin, topWin) {
			//货品是否已经存在
			if ($('input:hidden[name^="_spec_array"]').length > 0) {
				if ($('#goodsBaseBody tr').length > 1) {
					$("#goodsBaseBody tr").remove();
					initProductTable();
				}

			}
			//添加的规格
			var addSpecObject = $(iframeWin.document).find('.spec_values .hover');
			if (addSpecObject.length == 0) {
				initProductTable();
				return;
			}

			//开始遍历规格
			var specValueData = {}
			var specData = {};
			var selectedNewItem = [];
			addSpecObject.each(function() {
				if ($(this).hasClass('addsx') == true) {	// 如果是全选则排除添加属性这个<li>
					return true;
				}
				var data_id = parseInt($(this).attr('data-id'));
				var data_name = $(this).attr('data-name');
				var data_value = $(this).attr('data-value');
				var data_type = $(this).attr('data-type');
				if (typeof(specValueData[data_id]) == 'undefined') {
					specValueData[data_id] = [];
				}
				specValueData[data_id].push(data_value);
				specData[data_id] = {
					'id': data_id,
					'name': data_name,
					'type': data_type
				};
				selectedNewItem.push({
					'id': data_id,
					'value': data_value,
					'type': data_type
				});
			});
			selectedItem = selectedNewItem;
			
			//生成货品的笛卡尔积
			var specMaxData = descartes(specValueData, specData);
			//从表单中获取默认商品数据
			var productJson = {};
			
			productJson['goods_sn'] = $('[name="_goods_sn[0]"]').val();
			//生成最终的货品数据
			var productList = [];
			for (var i = 0; i < specMaxData.length; i++) {
				var productItem = {};
				for (var index in productJson) {
					//自动组建货品
					if (index == 'goods_sn') {
						//值为空时设置默认货号

						if (productJson[index] == '') {
							productJson[index] = defaultProductNo;
						}

						if (productJson[index].match(/(?:\-\d*)$/) == null) {
							//正常货号生成
							productItem['goods_sn'] = productJson[index] + '-' + (i + 1);
						} else {
							//货号已经存在则替换
							productItem['goods_sn'] = productJson[index].replace(/(?:\-\d*)$/, '-' + (i + 1));
						}

					} else {
						productItem[index] = productJson[index];
					}
				}
				productItem['spec_array'] = specMaxData[i];
				productList.push(productItem);
			}

			//创建规格标题
			var goodsHeadHtml = template('goodsHeadTemplate', {
				'templateData': specData
			});
			$('#goodsBaseHead').html(goodsHeadHtml);
			//创建货品数据表格
			var goodsRowHtml = template('goodsRowTemplate', {
				'templateData': productList
			});
			$('#goodsBaseBody').html(goodsRowHtml);
			
			//标记全部删除
			$("form input[name='del_products_all']").val('yes');
		}
	});
	
}

//笛卡儿积组合
function descartes(list, specData) {
	//parent上一级索引;count指针计数
	var point = {};
	var result = [];
	var pIndex = null;
	var tempCount = 0;
	var temp = [];
	//根据参数列生成指针对象
	for (var index in list) {
		if (typeof list[index] == 'object') {
			point[index] = {
				'parent': pIndex,
				'count': 0
			}
			pIndex = index;
		}
	}
	//单维度数据结构直接返回
	if (pIndex == null) {
		return list;
	}

	//动态生成笛卡尔积
	while (true) {
		for (var index in list) {
			tempCount = point[index]['count'];
			temp.push({
				"id": specData[index].id,
				"type": specData[index].type,
				"name": specData[index].name,
				"value": list[index][tempCount]
			});
		}
		//压入结果数组
		result.push(temp);
		temp = [];
		//检查指针最大值问题
		while (true) {
			if (point[index]['count'] + 1 >= list[index].length) {
				point[index]['count'] = 0;
				pIndex = point[index]['parent'];
				if (pIndex == null) {
					return result;
				}

				//赋值parent进行再次检查
				index = pIndex;
			} else {
				point[index]['count'] ++;
				break;
			}
		}
	}
}

/**
 * 清除规格
 */
function delAll() {
	if (confirm('是否真要清空?')) {
		if ($('#goodsBaseBody tr').length > 1) {
			$("#goodsBaseBody tr").remove();
			initProductTable();
		}
		selectedItem = '';
		//标记全部删除
		$("form input[name='del_products_all']").val('yes');
	}
}

/**
 * 删除选中的规格
 */
function delChecked() {
	var checkeds = $('input[name="checked_id"]:checked');
	if (checkeds.length < 1) {
		alert('请选择您要删除的规格商品');
		return false;
	}
	if (confirm('删除选中的规格，确定?')) {
		if(checkeds.length == $("input[name='checked_id']").length){
            delAll();
            return false;
        }
		checkeds.each(function(i,v) {
			// 获取当前产品的id
			var pro_id = $('input[name="_products_ids['+ $(this).val() +']"]').val();
			if (pro_id) {
				$('form').append('<input type="hidden" name="del_products_ids[]" value="'+pro_id+'">');
			}
			$(this).parent().parent().remove();
			if ($('#goodsBaseBody tr').length == 0) {
				initProductTable();
			}
		})
	}
}

//删除货品
function delProduct(_self) {

	delid=$(_self).prev('input').val();
	if(delid){
		$('form').append('<input type="hidden" name="del_products_ids[]" value="'+delid+'">');
	}

	$(_self).parent().parent().remove();
	if ($('#goodsBaseBody tr').length == 0) {
		initProductTable();
	}
	
}