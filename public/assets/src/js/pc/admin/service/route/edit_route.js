$(function(){
	
	//使删除详细日程按钮有效
	function bind_delete_detail(detail_day) {
		$('#btn-detail-delete-' + detail_day).click(function(){
			$(this).closest('.route-detail-block').remove();
			detail_day_sort();
		});
	}
	
	//使详细日程显示/隐藏详情按钮有效
	function bind_show_detail(detail_day) {
		$('#btn-detail-info-show-' + detail_day).click(function(){
			if($(this).hasClass('active')) {
				$(this).text('隐藏详情');
				$(this).removeClass('active');
				$(this).closest('.route-detail-block').find('.div-detail-info').fadeIn();
			} else {
				$(this).text('显示详情');
				$(this).addClass('active');
				$(this).closest('.route-detail-block').find('.div-detail-info').fadeOut();
			}
		});
	}
	
	//使景点名称输入框有效
	function bind_search_spot(detail_day) {
		$('#txt-spot-search-' + detail_day).bind('input propertychange', function(){
			var search_name = $(this).val();
			if(search_name == "") {
				$('#ul-spot-list-' + detail_day).find('li').show();
			} else {
				$('#ul-spot-list-' + detail_day).find('li').each(function(){
					var spot_name = $(this).attr('data-spotname');
					if(spot_name.indexOf(search_name) != -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		});
	}
	
	//使景点选择按钮有效
	function bind_select_spot(detail_day, spot_id) {
		$('#btn-spot-select-' + detail_day + '-' + spot_id).click(function(){
			var spot_name = $(this).closest('li').attr('data-spotname');
			var html_ul = [];
			html_ul.push('<li data-spotid="' + spot_id + '" data-spotname="' + spot_name + '">');
				html_ul.push(spot_name);
				html_ul.push('<p class="btn-spot-unselect" id="btn-spot-unselect-' + detail_day + '-' + spot_id + '">×</p>');
				html_ul.push('<input type="hidden" name="route_spot_list_' + detail_day + '[]" value="' + spot_id + '" />');
			html_ul.push('</li>');
			$('#ul-spot-list-selected-' + detail_day).append(html_ul.join(''));
			$(this).closest('li').remove();
			bind_unselect_spot(detail_day, spot_id);
		});
	}
	
	//使景点取消按钮有效
	function bind_unselect_spot(detail_day, spot_id) {
		$('#btn-spot-unselect-' + detail_day + '-' + spot_id).click(function(){
			var spot_name = $(this).closest('li').attr('data-spotname');
			var html_ul = [];
			html_ul.push('<li data-spotid="' + spot_id + '" data-spotname="' + spot_name + '">');
				html_ul.push('<p class="btn-spot-select" id="btn-spot-select-' + detail_day + '-' + spot_id + '"></p>');
				html_ul.push(spot_name);
			html_ul.push('</li>');
			$('#ul-spot-list-' + detail_day).append(html_ul.join(''));
			$(this).closest('li').remove();
			bind_select_spot(detail_day, spot_id);
		});
	}
	
	//详细日程Day更新
	function detail_day_sort() {
		$('.span-day').each(function(i){
			$(this).text(i+1);
			$(this).closest('.route-detail-block').find('.hid-detail-day').val(i+1);
		});
	}
	
	//生成一个详细日程输入框
	function html_detail_area(detail_day) {
		var html = [];
		
		html.push('<div class="route-detail-block">');
			html.push('<div class="div-detail-button-area">');
				html.push('<p class="btn-detail-day">DAY <span class="span-day"></span></p>');
				html.push('<p class="btn-detail-info-show" id="btn-detail-info-show-' + detail_day + '">隐藏详情</p>');
			html.push('</div>');
			html.push('<div class="div-detail-info">');
				html.push('<table class="content-form-talbe-inner">');
					html.push('<tr>');
						html.push('<th>标题</th>');
						html.push('<td><input type="text" name="route_detail_title_' + detail_day + '" value="" maxlength="100" placeholder="请输入标题(100字以内)" /></td>');
					html.push('</tr>');
					html.push('<tr>');
						html.push('<th>简介</th>');
						html.push('<td><textarea name="route_detail_content_' + detail_day + '" placeholder="请输入简介"></textarea></td>');
					html.push('</tr>');
					html.push('<tr>');
						html.push('<th>景点</th>');
						html.push('<td class="td-spot-list">');
							html.push('<ul class="ul-spot-list-selected" id="ul-spot-list-selected-' + detail_day + '"></ul>');
							html.push('<div class="div-spot-search"><input type="text" class="txt-spot-search" id="txt-spot-search-' + detail_day + '" placeholder="请输入要查找的景点名" /></div>');
							html.push('<ul class="ul-spot-list" id="ul-spot-list-' + detail_day + '">');
								$.ajax({
									type: "POST",
									url: '/admin/api_simple_spot_list/',
									data: {page: 'edit_route'},
									dataType: "json",
									success: function(result) {
										if(result.result) {
											spot_list = result.spot_list;
											var html_ul = [];
											$.each(spot_list,function(index,val){
												html_ul.push('<li data-spotid="' + val.spot_id + '" data-spotname="' + val.spot_name + '">');
													html_ul.push('<p class="btn-spot-select" id="btn-spot-select-' + detail_day + '-' + val.spot_id + '"></p>');
													html_ul.push(val.spot_name);
												html_ul.push('</li>');
											});
											$('#ul-spot-list-' + detail_day).append(html_ul.join(''));
											$.each(spot_list,function(index,val){
												bind_select_spot(detail_day, val.spot_id);
											});
										}
									},
									error: function(XMLHttpRequest, textStatus, errorThrown) {
										console.log("XMLHttpRequest : " + XMLHttpRequest.status);
										console.log("textStatus : " + textStatus);
										console.log("errorThrown : " + errorThrown.message);
									}
								});
							html.push('</ul>');
						html.push('</td>');
					html.push('</tr>');
					html.push('<tr>');
						html.push('<th>早餐</th>');
						html.push('<td><textarea name="route_breakfast_' + detail_day + '" placeholder="请输入早餐信息"></textarea></td>');
					html.push('</tr>');
					html.push('<tr>');
						html.push('<th>午餐</th>');
						html.push('<td><textarea name="route_lunch_' + detail_day + '" placeholder="请输入午餐信息"></textarea></td>');
					html.push('</tr>');
					html.push('<tr>');
						html.push('<th>晚餐</th>');
						html.push('<td><textarea name="route_dinner_' + detail_day + '" placeholder="请输入晚餐信息"></textarea></td>');
					html.push('</tr>');
					html.push('<tr>');
						html.push('<th>酒店</th>');
						html.push('<td><textarea name="route_hotel_' + detail_day + '" placeholder="请输入酒店信息"></textarea></td>');
					html.push('</tr>');
				html.push('</table>');
				html.push('<p class="btn-detail-delete" id="btn-detail-delete-' + detail_day + '">删除本日日程</p>');
			html.push('</div>');
			html.push('<input type="hidden" class="hid-detail-num" name="route_detail_num[]" value="' + detail_day + '" />');
			html.push('<input type="hidden" class="hid-detail-day" name="route_detail_day_' + detail_day + '" value="" />');
		html.push('</div>');
		
		return html.join('');
	}
	
	//绑定初始删除详细日程按钮
	$('.btn-detail-delete').click(function(){
		$(this).closest('.route-detail-block').remove();
		detail_day_sort();
	});
	
	//绑定初始详细日程显示/隐藏详情按钮
	$('.btn-detail-info-show').click(function(){
		if($(this).hasClass('active')) {
			$(this).text('隐藏详情');
			$(this).removeClass('active');
			$(this).closest('.route-detail-block').find('.div-detail-info').fadeIn();
		} else {
			$(this).text('显示详情');
			$(this).addClass('active');
			$(this).closest('.route-detail-block').find('.div-detail-info').fadeOut();
		}
	});
	
	//绑定初始详细日程景点名称输入框
	$('.txt-spot-search').bind('input propertychange', function(){
		var search_name = $(this).val();
		if(search_name == "") {
			$(this).closest('.td-spot-list').find('.ul-spot-list li').show();
		} else {
			$(this).closest('.td-spot-list').find('.ul-spot-list li').each(function(){
				var spot_name = $(this).attr('data-spotname');
				if(spot_name.indexOf(search_name) != -1) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		}
	});
	
	//绑定初始详细日程景点选择按钮
	$('.btn-spot-select').each(function(){
		var spot_id = $(this).closest('li').attr('data-spotid');
		var detail_day = $(this).closest('.route-detail-block').find('.hid-detail-num').val();
		bind_select_spot(detail_day, spot_id);
	});
	
	//绑定初始详细日程景点取消按钮
	$('.btn-spot-unselect').each(function(){
		var spot_id = $(this).closest('li').attr('data-spotid');
		var detail_day = $(this).closest('.route-detail-block').find('.hid-detail-num').val();
		bind_unselect_spot(detail_day, spot_id);
	});
	
	//初始详细日程天数调整
	detail_day_sort();

	$('#file-main-image').uploadThumbs({
		position : '#div-thumb-main-image',
		imgbreak : true
	});
	
	//Day调整顺序
	$('#route-detail-area').sortable({
		connectWith: ".route-detail-area",
		handle: ".btn-detail-day",
		placeholder: "div-sort-temp",
		start: function(event, ui){
			var height = ui.item.height();
			ui.placeholder.height(height);
		},
		stop: function(event, ui){
			detail_day_sort();
		}
	});
	
	//成本计算
	$('.txt-cost').bind('input propertychange', function(){
		var base_cost = $.trim($('#txt-base-cost').val());
		var traffic_cost = $.trim($('#txt-traffic-cost').val());
		var parking_cost = $.trim($('#txt-parking-cost').val());
		
		if(base_cost == '') {
			base_cost = 0;
		}
		if(traffic_cost == '') {
			traffic_cost = 0;
		}
		if(parking_cost == '') {
			parking_cost = 0;
		}
		
		if($.isNumeric(base_cost) && $.isNumeric(traffic_cost) && $.isNumeric(parking_cost)) {
			var total_base = parseFloat(base_cost) + parseFloat(traffic_cost) + parseFloat(parking_cost);
			$('#lbl_total_cost').text(total_base);
		} else {
			$('#lbl_total_cost').text('请在基本成本,交通费,停车费输入数字');
		}
	});
	
	//绑定添加详细日程输入框按钮事件
	$('#route-detail-add').click(function(){
		var detail_day = $('#route-detail-area').attr('data-detailday');
		
		//添加一个新的详细日程输入框
		$('#route-detail-area').append(html_detail_area(detail_day));
		bind_delete_detail(detail_day);
		bind_show_detail(detail_day);
		bind_search_spot(detail_day);
		detail_day_sort();
		
		new_detail_day = parseInt(detail_day) + 1;
		$('#route-detail-area').attr('data-detailday', new_detail_day);
	});
	
});