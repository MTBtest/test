<?php include $this->admin_tpl("header"); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH; ?>admin/cloud.css" />
	<div class="content">
	    <div class="site">
	        Haidao Board <a href="#">应用商店</a> > 云平台检测
	    </div>
    	<span class="line_white"></span>
    	<!--云检测-->
		<div class="yun-content">
			<!--正在检测-->
			<div class="yuntest-box test-ing">
				<div class="yun-mac fl">
					<!--安全图片-->
					<!--<p class="test-num">100</p>-->
					<p class="nosafe"></p>
				</div>
				<div class="yun-text fl">
					<p class="test-word-t trip">建议立即体检！</p>
					<div class="test-number-pb bar" style="display: none;">
						<!--滚动条-->
						<div class="test-score"></div>
					</div>
					<p class="test-word-t-p">检测系统可以防范黑客入侵，保障电商数据安全，还可检测系统最新版本，一键升级养成良好的数据备份习惯，让数据丢失从此变成浮云，建议您每天坚持备份数据</p>
					<!--按钮-->
					<div>
						<!--体检按钮-->
						<span class="test-btn2 check_all">
							立即检测
						</span>
						<!--检测结束出现修复按钮-->
						<!--<span class="test-btn2">一键修复</span>-->
					</div>
				</div>
			</div>
			<!--检测内容区域-下-->
			<div class="test-jsct">
				<ul>
					<li>
						<!--检测名称-->
						<span class="testpro-t">云平台接口检测 ：</span>
						<span class="testpro-explain">检测系统服务器环境配置和数据库运行状况   正常</span>
						<!--检测不正常提示-->
						<span class="testpro-btn setup_1">未检测</span>
					</li>
					<li>
						<!--检测名称-->
						<span class="testpro-t">版本检测：</span>
						<span class="testpro-explain"> 当前版本  <i class='current_version'><?php echo getconfig('version')?></i></span>
						<!--升级按钮-->
						<span class="testpro-btn setup_2">未检测</span>
					</li>
					<li>
						<!--检测名称-->
						<span class="testpro-t">数据库备份 ：</span>
						<span class="testpro-explain">检测是否备份了最新数据已防止数据丢失</span>
						<!--备份按钮-->
						<span class="testpro-btn setup_3">未检测</span>
					</li>
				</ul>
			</div>
		</div>
	<script type="text/javascript">
		var info_trip = $(".trip");
		var info_ico = $(".yun-mac p");
		var load_img = "<?php echo IMG_PATH?>admin/loading.gif";
		var score_num = 100 ;
		var setup_success = 0;
		$('.check_all').live('click',function(){
			if ($('.check_all').hasClass('disabled')) return false;
			$(this).text('正在检测').addClass('disabled');
			$('.setup_1,.setup_2,.setup_3').removeClass('testpro-btn,test-success-btn, test-error-btn').addClass('test-normal-btn').html('正在检测');
			var url = "<?php echo U('Admin/Cloud/checkcloud')?>";
			$.getJSON(url,{},function(data){
				update_progress(data.obj,data.css,data.text,data.score,0);
			})
			var url = "<?php echo U('Admin/Cloud/checkup')?>";
			$.getJSON(url,{},function(data){
				update_progress(data.obj,data.css,data.text,data.score,0);
			})
			var url = "<?php echo U('Admin/Cloud/checkdb')?>";
			$.getJSON(url,{},function(data){
				update_progress(data.obj,data.css,data.text,data.score,0);
			})
		})
		function update_progress(_obj,_css,_text,_score,_status){
			//if ($('.check_all a').hasClass('disabled')) return false;
			if(_status == 1) {
				//$('.check_all a').text('重新检测');
				if(_score <100){
					info_trip.text('您的系统正处于不安全状态，建议您立即修复。');
					$('.test-word-t-p').text('定期做系统检测可以降低电商运营中的损失概率');
				}else{
					info_trip.text('您的系统正在稳定运行，请继续保持');
					$('.test-word-t-p').text('检测系统可以防范黑客入侵，保障电商数据安全，还可检测系统最新版本，一键升级养成良好的数据备份习惯，让数据丢失从此变成浮云，建议您每天坚持备份数据');
				}
			}
			//console.debug(_obj,_css,_text,_score,_status,_status == '1');
			info_ico.removeClass().addClass("test-num").text('100');
			$('.'+_obj).removeClass('testpro-btn','test-normal-btn','test-success-btn','test-error-btn').addClass(_css).html(_text);
			score_num -= _score;
			setup_success++;
			if(setup_success == 3){
				setup_success = 0 ;
				info_ico.text(score_num);
				score_num = 100;
				$('.check_all').text('重新检测').removeClass('disabled');
			}
		}
	</script>
	<script type="text/javascript">
		var _maq = _maq || [];
		_maq.push(['_setAccount', '<?php echo getconfig('site_name')?>']);
		_maq.push(['_setAction', 'cloud']);
		_maq.push(['_setVersion', '<?php echo getconfig('version')?>']);
		_maq.push(['_setEmail', '<?php echo session('ADMIN_EMAIL'); ?>']);
		(function() {
		    var ma = document.createElement('script'); ma.type = 'text/javascript'; ma.async = true;
		    ma.src = ('https:' == document.location.protocol ? 'https://cloud.haidao.la' : 'http://cloud.haidao.la') + '/ma.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ma, s);
		})();
	</script>
<?php include $this->admin_tpl("copyright"); ?>
