$(function(){

	//使上传图片时显示缩略图有效
	function bind_file_thumb(detail_num, image_num) {
		$('#spot-images-' + detail_num + '-' + image_num).uploadThumbs({
			position : '#thumb-' + detail_num + '-' + image_num,
			imgbreak : true
		});
	}
	
	//使删除图片按钮有效
	function bind_delete_thumb(controller) {
		controller.click(function(){
			$(this).closest('.spot-image-block').remove();
		});
	}
	
	//使删除景点详情按钮有效
	function bind_delete_detail(controller) {
		controller.click(function(){
			$(this).closest('.spot-detail-block').remove();
		});
	}
	
	//使景点详情有效期点选按钮有效
	function bind_lbl_for_check(controller) {
		controller.click(function(){
			if($(this).hasClass('active')) {
				$(this).removeClass('active');
			} else {
				$(this).addClass('active');
			}
		});
	}
	
	//生成一个景点详情输入框
	function html_detail_area(detail_id) {
		var html = [];
		
		html.push('<div class="spot-detail-block">');
			html.push('<table class="content-form-talbe-inner">');
				html.push('<tr>');
					html.push('<th>景点详情名</th>');
					html.push('<td><input type="text" name="spot_detail_name_' + detail_id + '" maxlength="100" placeholder="请输入景点详情名(100字以内)" /></td>');
				html.push('</tr>');
				html.push('<tr>');
					html.push('<th>景点介绍</th>');
					html.push('<td><textarea name="spot_description_text_' + detail_id + '" placeholder="请输入景点介绍"></textarea></td>');
				html.push('</tr>');
				html.push('<tr>');
					html.push('<th>景点图片</th>');
					html.push('<td>');
						html.push('<div class="spot-image-area" id="spot-image-area-' + detail_id + '" data-imagenum="1">');
						html.push('</div>');
						html.push('<div id="spot-image-add-' + detail_id + '" class="btn-spot-image-add" data-detailnum="' + detail_id + '"><p>添加图片</p></div>');
					html.push('</td>');
				html.push('</tr>');
				html.push('<tr>');
					html.push('<th>详情公开期</th>');
					html.push('<td class="td-se-time">');
						html.push('<input type="checkbox" name="two_year_flag_' + detail_id + '" id="two-year-flag-' + detail_id + '" />');
						html.push('<label for="two-year-flag-' + detail_id + '" class="lbl-for-check" id="lbl-for-check-' + detail_id + '">跨年</label>');
						html.push('<select name="spot_start_month_' + detail_id + '">');
							html.push('<option value=""></option>');
							for(var i = 1; i < 13; i++){
							html.push('<option value="' + i + '">' + i + '</option>');
							}
						html.push('</select>月～');
						html.push('<select name="spot_end_month_' + detail_id + '">');
							html.push('<option value=""></option>');
							for(var i = 1; i < 13; i++){
							html.push('<option value="' + i + '">' + i + '</option>');
							}
						html.push('</select>月');
					html.push('</td>');
				html.push('</tr>');
			html.push('</table>');
			html.push('<p class="btn-detail-delete" id="detail-delete-' + detail_id + '">删除景点详情</p>');
		html.push('</div>');
		
		return html.join('');
	}
	
	//生成一个图片上传框
	function html_image_area(detail_num, image_num) {
		var html = [];
		
		html.push('<div class="spot-image-block">');
			html.push('<div class="move-handle">调整顺序</div>')
			html.push('<div class="thumb-area" id="thumb-' + detail_num + '-' + image_num + '"></div>');
			html.push('<div class="upload-area">');
				html.push('<label>');
					html.push('<input type="file" name="image_file_' + detail_num + '_' + image_num + '" id="spot-images-' + detail_num + '-' + image_num + '" multiple="multiple" accept="image/jpeg,image/png" />');
					html.push('<p class="btn-thumb-upload">上传</p>');
				html.push('</label>');
				html.push('<p id="thumb-delete-' + detail_num + '-' + image_num + '" class="btn-thumb-delete">删除</p>');
			html.push('</div>');
			html.push('<input type="hidden" name="image_type_' + detail_num + '_' + image_num + '" value="new" />');
			html.push('<input type="hidden" name="image_id_list_' + detail_num + '[]" value="' + image_num + '" />');
		html.push('</div>');
		
		return html.join('');
	}
	
	//使图片添加按钮有效
	function bind_image_add(controller) {
		controller.click(function(){
			var detail_num = $(this).attr('data-detailnum');
			var spot_image_area = $(this).closest('td').find('.spot-image-area');
			
			//当前图片上传框数量获取
			var image_num = spot_image_area.attr('data-imagenum');
			//生成一个图片上传框
			spot_image_area.append(html_image_area(detail_num, image_num));
			//使上传图片时显示缩略图有效
			bind_file_thumb(detail_num, image_num);
			//使删除图片按钮有效
			bind_delete_thumb($('#thumb-delete-' + detail_num + '-' + image_num));
			//当前图片上传框数量更新
			image_num++;
			spot_image_area.attr('data-imagenum', image_num);
		});
	}
	
	//上传图片调整顺序
	function bind_image_sort(controller) {
		var controller_id = controller[0].id;
		controller.sortable({
			connectWith: controller_id,
			handle: ".move-handle",
			placeholder: "div-sort-temp",
			start: function(event, ui){
				var height = ui.item.height();
				ui.placeholder.height(height);
			},
			stop: function(event, ui){
			}
		});
	}
	
	//绑定初始图片添加按钮
	$('.btn-spot-image-add').each(function(){
		bind_image_add($(this));
	});
	
	//绑定初始图片顺序调整功能
	$('.spot-image-area').each(function(){
		bind_image_sort($(this));
	});
	
	//绑定初始图片上传按钮
	$('input[type="file"]').each(function() {
		var detail_num = $(this).closest('td').find('.btn-spot-image-add').attr('data-detailnum');
		var image_num = $(this).closest('.spot-image-block').attr('data-imagenum');
		bind_file_thumb(detail_num, image_num);
	});
	
	//绑定初始删除图片按钮
	$('.btn-thumb-delete').click(function(){
		$(this).closest('.spot-image-block').remove();
	});
	
	//绑定初始删除景点详情按钮
	$('.btn-detail-delete').click(function(){
		$(this).closest('.spot-detail-block').remove();
	});
	
	//收费/免费切换处理
	$('input[name="free_flag"]').change(function(){
		if($(this).val() == '0') {
			$('.div-spot-price-area').fadeIn();
		} else {
			$('.div-spot-price-area').fadeOut();
		}
	});
	
	//添加一行特别价格
	$('.tb-special-price th.th-add').click(function(){
		var html = [];
		
		html.push('<tr>');
			html.push('<td><input type="text" name="special_price_name[]" value="" maxlength="30" placeholder="请输入价格条件" /></td>');
			html.push('<td><input type="number" name="special_price[]" value="" placeholder="请输入价格" /></td>');
		html.push('</tr>');
		
		$(this).closest('tr').before(html.join(''));
	});
	
	//绑定添加详情输入框按钮事件
	$('#spot-detail-add').click(function(){
		var detail_num = $('#spot-detail-area').attr('data-detailnum');
		
		//添加一个新的详情输入框
		$('#spot-detail-area').append(html_detail_area(detail_num));
		bind_lbl_for_check($('#lbl-for-check-' + detail_num));
		bind_delete_detail($('#detail-delete-' + detail_num));
		bind_image_add($('#spot-image-add-' + detail_num));
		bind_image_sort($('#spot-image-area-' + detail_num));
		bind_file_thumb(detail_num, 0);
		bind_delete_thumb($('#thumb-delete-' + detail_num + '-0'));
		
		new_detail_num = parseInt(detail_num) + 1;
		$('#spot-detail-area').attr('data-detailnum', new_detail_num);
	});
	
});