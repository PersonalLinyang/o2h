$(function(){
	//使上传图片时显示缩略图有效
	function bind_file_thumb(detail_id, area_id) {
		$('#spot-images-' + detail_id + '-' + area_id).uploadThumbs({
			position : '#thumb-' + detail_id + '-' + area_id,
			imgbreak : true
		});
	}
	
	//使删除图片按钮有效
	function bind_delete_thumb(detail_id, area_id) {
		$('#thumb-delete-' + detail_id + '-' + area_id).click(function(){
			$(this).closest('.spot-image-block').remove();
		});
	}
	
	//使删除景点详情按钮有效
	function bind_delete_detail(detail_id) {
		$('#detail-delete-' + detail_id).click(function(){
			$(this).closest('.spot-detail-block').remove();
		});
	}
	
	//生成一个图片上传框
	function html_image_area(detail_id, area_id) {
		var html = [];
		
		html.push('<div class="spot-image-block">');
			html.push('<div id="thumb-' + detail_id + '-' + area_id + '"></div>');
			html.push('<div class="upload">');
			html.push('<label>');
				html.push('<input type="file" name="spot_images_' + detail_id + '[]" id="spot-images-' + detail_id + '-' + area_id + '" multiple="multiple" accept="image/jpeg,image/png" />');
				html.push('<p>上传图片</p>');
			html.push('</label>');
			if(area_id) {
				html.push('<p id="thumb-delete-' + detail_id + '-' + area_id + '" class="btn-thumb-delete">删除</p>');
			}
			html.push('</div>');
		html.push('</div>');
		
		return html.join('');
	}
	
	//使图片添加按钮有效
	function bind_image_add(detail_id) {
		//当前图片上传框数量获取
		var image_num = $('#spot-image-area-' + detail_id).attr('data-imagenum');
		//生成一个图片上传框
		$('#spot-image-area-' + detail_id).append(html_image_area(detail_id, image_num));
		//使上传图片时显示缩略图有效
		bind_file_thumb(detail_id, image_num);
		bind_delete_thumb(detail_id, image_num);
		//当前图片上传框数量更新
		image_num++;
		$('#spot-image-area-' + detail_id).attr('data-imagenum', image_num);
	}
	
	//生成一个景点详情输入框
	function html_detail_area(detail_id) {
		var html = [];
		
		html.push('<div class="spot-detail-block">');
			html.push('<table>');
				html.push('<tr>');
					html.push('<th>景点详情名</th>');
					html.push('<td><input type="text" name="spot_detail_name_' + detail_id + '" /></td>');
				html.push('</tr>');
				html.push('<tr>');
					html.push('<th>景点介绍</th>');
					html.push('<td><textarea name="spot_description_text_' + detail_id + '"></textarea></td>');
				html.push('</tr>');
				html.push('<tr>');
					html.push('<th>景点图片</th>');
					html.push('<td class="spot-image-area">');
						html.push('<div id="spot-image-area-' + detail_id + '" data-imagenum="1">');
							html.push(html_image_area(detail_id, 0));
						html.push('</div>');
						html.push('<div id="spot-image-add-' + detail_id + '" class="btn-spot-image-add" data-detailnum="' + detail_id + '"><p>+</p></div>');
					html.push('</td>');
				html.push('</tr>');
				html.push('<tr>');
					html.push('<th>详情公开期</th>');
					html.push('<td>');
						html.push('<input type="checkbox" name="two_year_flag_' + detail_id + '" id="two-year-flag-' + detail_id + '" />');
						html.push('<label for="two-year-flag-' + detail_id + '">跨年</label>');
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
	
	//绑定初始图片添加按钮
	$('.btn-spot-image-add').click(function(){
		var detail_num = $(this).attr('data-detailnum');
		bind_image_add(detail_num);
	});
	
	//绑定初始图片上传按钮
	$('input[type="file"]').each(function() {
		var id = $(this)[0].id;
		var index_array = id.match(/\d/);
		var detail_num = index_array[0];
		bind_file_thumb(detail_num, 0);
		bind_delete_thumb(detail_num, 0);
	});
	
	//绑定初始删除图片按钮
	$('.btn-thumb-delete').click(function(){
		$(this).closest('.spot-image-block').remove();
	});
	
	//绑定初始删除景点详情按钮
	$('.btn-detail-delete').click(function(){
		$(this).closest('.spot-detail-block').remove();
	});
	
	//绑定添加详情输入框按钮事件
	$('#spot-detail-add').click(function(){
		var detail_num = $('#spot-detail-area').attr('data-detailnum');
		
		//添加一个新的详情输入框
		$('#spot-detail-area').append(html_detail_area(detail_num));
		$('#spot-image-add-' + detail_num).click(function(){bind_image_add(detail_num);});
		bind_delete_detail(detail_num);
		bind_file_thumb(detail_num, 0);
		bind_delete_thumb(detail_num, 0);
		
		new_detail_num = parseInt(detail_num) + 1;
		$('#spot-detail-area').attr('data-detailnum', new_detail_num);
	});
	
	//收费/免费切换处理
	$('input[name="free_flag"]').change(function(){
		if($(this).val() == '0') {
			$('input[name="price"]:text').attr('readonly', false);
			$('input[name="price"]:text').removeClass('readonly');
		} else {
			$('input[name="price"]:text').attr('readonly', true);
			$('input[name="price"]:text').addClass('readonly');
			$('input[name="price"]:text').val('');
		}
	});
});