$(function(){

	var step_index = 0;

	//点击为酒店预订添加一行按钮
	function bind_plus_row_hr (controller) {
		controller.click(function(){
			var row = $('#div-hotel-reserve-area').attr('data-row');
			var html = [];
			
			html.push('<tr>');
				html.push('<td class="td-button">');
					html.push('<p class="btn-hotel-reserve btn-plus-hotel-reserve" id="btn-plus-hotel-reserve-' + row + '">＋</p>');
				html.push('</td>');
				html.push('<td class="td-button">');
					html.push('<p class="btn-hotel-reserve btn-minus-hotel-reserve" id="btn-minus-hotel-reserve-' + row + '">－</p>');
				html.push('</td>');
				html.push('<td class="td-select">');
					html.push('<select name="hotel_type_' + row + '" id="sel-hotel-type-' + row + '">');
						html.push('<option></option>');
						$.ajax({
							type: "POST",
							url: '/interface/home/api_hotel_type_list/',
							data: {page: 'index'},
							dataType: "json",
							success: function(result) {
								if(result.result) {
									hotel_type_list = result.hotel_type_list;
									var html_select = [];
									$.each(hotel_type_list,function(index,val){
										html_select.push('');
										html_select.push('<option value="' + val.hotel_type_id + '">');
											html_select.push(val.hotel_type_name);
										html_select.push('</option>');
									});
									$('#sel-hotel-type-' + row).append(html_select.join(''));
								}
							},
							error: function(XMLHttpRequest, textStatus, errorThrown) {
								console.log("XMLHttpRequest : " + XMLHttpRequest.status);
								console.log("textStatus : " + textStatus);
								console.log("errorThrown : " + errorThrown.message);
							}
						});
					html.push('</select>');
				html.push('</td>');
				html.push('<td class="td-select">');
					html.push('<select name="room_type_' + row + '" id="sel-room-type-' + row + '">');
						html.push('<option></option>');
						$.ajax({
							type: "POST",
							url: '/interface/home/api_room_type_list/',
							data: {page: 'index'},
							dataType: "json",
							success: function(result) {
								if(result.result) {
									room_type_list = result.room_type_list;
									var html_select = [];
									$.each(room_type_list,function(index,val){
										html_select.push('');
										html_select.push('<option value="' + val.room_type_id + '">');
											html_select.push(val.room_type_name);
										html_select.push('</option>');
									});
									$('#sel-room-type-' + row).append(html_select.join(''));
								}
							},
							error: function(XMLHttpRequest, textStatus, errorThrown) {
								console.log("XMLHttpRequest : " + XMLHttpRequest.status);
								console.log("textStatus : " + textStatus);
								console.log("errorThrown : " + errorThrown.message);
							}
						});
					html.push('</select>');
				html.push('</td>');
				html.push('<td class="td-number">');
					html.push('<p class="minus-for-number btn-for-number" data-for="num-people-' + row + '" id="btn-minus-for-people-' + row + '"></p>');
					html.push('<input class="num-number" type="number" name="people_num_' + row + '" id="num-people-' + row + '" value="0" />');
					html.push('<p class="plus-for-number btn-for-number" data-for="num-people-' + row + '" id="btn-plus-for-people-' + row + '"></p>');
				html.push('</td>');
				html.push('<td class="td-number">');
					html.push('<p class="minus-for-number btn-for-number" data-for="num-room-' + row + '" id="btn-minus-for-room-' + row + '"></p>');
					html.push('<input class="num-number" type="number" name="room_num_' + row + '" id="num-room-' + row + '" value="0" />');
					html.push('<p class="plus-for-number btn-for-number" data-for="num-room-' + row + '" id="btn-plus-for-room-' + row + '"></p>');
				html.push('</td>');
				html.push('<td class="td-number">');
					html.push('<p class="minus-for-number btn-for-number" data-for="num-day-' + row + '" id="btn-minus-for-day-' + row + '"></p>');
					html.push('<input class="num-number" type="number" name="day_num_' + row + '" id="num-day-' + row + '" value="0" />');
					html.push('<p class="plus-for-number btn-for-number" data-for="num-day-' + row + '" id="btn-plus-for-day-' + row + '"></p>');
				html.push('</td>');
				html.push('<td class="td-comment">');
					html.push('<input name="comment_' + row + '" type="text">');
				html.push('</td>');
			html.push('</tr>');
			
			$(this).closest('tr').after(html.join(''));

			//动态绑定
			bind_plus_row_hr($('#btn-plus-hotel-reserve-' + row));
			bind_minus_row_hr($('#btn-minus-hotel-reserve-' + row));
			bind_minus_for_number_hr($('#btn-minus-for-people-' + row));
			bind_minus_for_number_hr($('#btn-minus-for-room-' + row));
			bind_minus_for_number_hr($('#btn-minus-for-day-' + row));
			bind_plus_for_number_hr($('#btn-plus-for-people-' + row));
			bind_plus_for_number_hr($('#btn-plus-for-room-' + row));
			bind_plus_for_number_hr($('#btn-plus-for-day-' + row));
			
			new_row = parseInt(row) + 1;
			$('#div-hotel-reserve-area').attr('data-row', new_row);
		});
	}

	//点击为酒店预订删除一行按钮
	function bind_minus_row_hr (controller) {
		controller.click(function(){
			$(this).closest('tr').remove();
		});
	}

	//点击酒店预订数字部分加号按钮
	function bind_plus_for_number_hr (controller) {
		controller.click(function(){
			var name = $(this).attr('data-for');
			var value_now = $('#' + name).val();
			if(!value_now) {
				value_now = 0;
			}
			value_now = parseInt(value_now) + 1;
			$('#' + name).val(value_now);
		});
	}

	//点击酒店预订数字部分减号按钮
	function bind_minus_for_number_hr (controller) {
		controller.click(function(){
			var name = $(this).attr('data-for');
			var value_now = $('#' + name).val();
			if(!value_now) {
				value_now = 0;
			}
			if(value_now >= 1) {
				value_now = parseInt(value_now) - 1;
			}
			$('#' + name).val(value_now);
		});
	}

	//点击下一步按钮
	$('.btn-next').click(function(){
		if($(this).hasClass('active')) {
			step_index++;
			var left_param = step_index * 1000;
			$('.form-body-slide').animate({left:'-' + left_param + 'px'}, 800);
		}
	});

	//点击返回按钮
	$('.btn-return').click(function(){
		step_index--;
		var left_param = step_index * 1000;
		$('.form-body-slide').animate({left:'-' + left_param + 'px'}, 800);
	});

	//选择旅游路线
	$('.sel-route-id').change(function(){
		var sel_route_id = $(this);
		var route_id = sel_route_id.val();

		if(route_id) {
			$.ajax({
				type: "POST",
				url: '/interface/home/api_route_info/',
				data: {page: 'index', route_id: route_id},
				dataType: "json",
				success: function(result) {
					if(result.result) {
						route = result.route_info;
						var route_info_area = sel_route_id.closest('.form-body-step').find('.div-scroll-route-info');
						route_info_area.find('.route-title').text(route.route_name);
						route_info_area.find('.price').text(route.route_price_min + '～' + route.route_price_max);
						route_info_area.find('.route-description').html(route.route_description);
						var html_spot = [];
						$.each(route.spot_list, function(index, val){
							html_spot.push('<a href="/spot/' + val.spot_id + '/">' + val.spot_name + '</a>');
						});
						route_info_area.find('.p-spot-list-route').html(html_spot.join(''));
						route_info_area.find('.detail-link a').attr('href', '/route/' + route.route_id + '/');
						route_info_area.fadeIn();
						route_info_area.scrollTop(0);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log("XMLHttpRequest : " + XMLHttpRequest.status);
					console.log("textStatus : " + textStatus);
					console.log("errorThrown : " + errorThrown.message);
				}
			});
		} else {
			sel_route_id.closest('.form-body-step').find('.div-scroll-route-info').fadeOut();
		}
	});

	//打开/关闭景点检索区域
	$('.btn-checked-spot').click(function(){
		if($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).html('查看已选中的景点');
			$('.div-checked-spot').slideUp();
		} else {
			$(this).addClass('active');
			$(this).html('关闭');
			$('.div-checked-spot').slideDown();
		}
	});

	//选择景点地区
	$('.li-area-spot-search').click(function(){
		var search_name = $('.txt-spot-search').val();
		if($(this).hasClass('active')) {
			$(this).removeClass('active');
			if(search_name == "") {
				$(this).closest('.div-content-tab').find('.li-spot').show();
			} else {
				$(this).closest('.div-content-tab').find('.li-spot').each(function(){
					var spot_name = $(this).attr('data-spotname');
					if(spot_name.indexOf(search_name) != -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		} else {
			$('.li-area-spot-search').removeClass('active');
			$(this).addClass('active');
			var search_area = $(this).attr('data-areaid');
			if(search_name == "") {
				$(this).closest('.div-content-tab').find('.li-spot').hide();
				$(this).closest('.div-content-tab').find('.li-spot[data-spotarea="' + search_area + '"]').show();
			} else {
				$(this).closest('.div-content-tab').find('.li-spot').hide();
				$(this).closest('.div-content-tab').find('.li-spot[data-spotarea="' + search_area + '"]').each(function(){
					var spot_name = $(this).attr('data-spotname');
					if(spot_name.indexOf(search_name) != -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		}
	});
	
	//绑定景点名称检索输入框
	$('.txt-spot-search').bind('input propertychange', function(){
		var search_name = $(this).val();
		var search_area = $('.li-area-spot-search.active').attr('data-areaid');
		if(search_area) {
			if(search_name == "") {
				$(this).closest('.div-content-tab').find('.li-spot').hide();
				$(this).closest('.div-content-tab').find('.li-spot[data-spotarea="' + search_area + '"]').show();
			} else {
				$(this).closest('.div-content-tab').find('.li-spot[data-spotarea="' + search_area + '"]').each(function(){
					var spot_name = $(this).attr('data-spotname');
					if(spot_name.indexOf(search_name) != -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		} else {
			if(search_name == "") {
				$(this).closest('.div-content-tab').find('.li-spot').show();
			} else {
				$(this).closest('.div-content-tab').find('.li-spot').each(function(){
					var spot_name = $(this).attr('data-spotname');
					if(spot_name.indexOf(search_name) != -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		}
	});

	//点选景点
	$('.lbl-for-check-spot').click(function(){
		var input_for = $(this).attr('for');
		var spot_id = $(this).closest('.li-spot').attr('data-spotid');
		var spot_name = $(this).closest('.li-spot').attr('data-spotname');
		if($('#' + input_for).is(':checked')) {
			//解除选中
			$('#link-spot-checked-' + spot_id).remove();
			if(!$('.link-spot-checked').length) {
				$('.p-spot-list-checked').html('<span>您尚未选中任何景点</span>');
			}
		} else {
			//尚未被选中
			if(!$('.link-spot-checked').length) {
				$('.p-spot-list-checked').html('');
			}
			$('.p-spot-list-checked').append('<a href="/spot/' + spot_id + '/" class="link-spot-checked" id="link-spot-checked-' + spot_id + '">' + spot_name + '</a>');
		}
	});

	//酒店预订区域显隐切换
	$('#btn-hotel-reserve-flag-1').click(function(){
		$('#div-hotel-reserve-area').fadeIn();
	});
	$('#btn-hotel-reserve-flag-0').click(function(){
		$('#div-hotel-reserve-area').fadeOut();
	});

	//初始绑定
	bind_plus_row_hr($('.btn-plus-hotel-reserve'));
	bind_minus_row_hr($('.btn-minus-hotel-reserve'))
});