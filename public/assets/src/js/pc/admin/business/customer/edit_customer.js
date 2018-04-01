$(function(){
	
	//使select的placeholder有效
	function bind_select_placeholder(controller) {
		if(controller.find('option:selected').hasClass('placeholder')) {
			controller.css({'color': '#BDBDBD'});
		}
		controller.on('change', function(){
			if($(this).find('option:selected').hasClass('placeholder')) {
				$(this).css({'color': '#BDBDBD'});
			} else {
				$(this).css({'color': '#000000'});
			}
		});
	}
	
	//使计算成本相关输入框有效
	function bind_cost_total_calculation(controller) {
		controller.bind('input propertychange', function(){
			var customer_cost_day = $(this).closest('tr').find('.txt-customer-cost-day').val();
			if($.isNumeric(customer_cost_day)) {
				customer_cost_day = parseFloat(customer_cost_day);
			} else {
				customer_cost_day = 0;
			}
			var customer_cost_people = $(this).closest('tr').find('.txt-customer-cost-people').val();
			if($.isNumeric(customer_cost_people)) {
				customer_cost_people = parseFloat(customer_cost_people);
			} else {
				customer_cost_people = 0;
			}
			var customer_cost_each = $(this).closest('tr').find('.txt-customer-cost-each').val();
			if($.isNumeric(customer_cost_each)) {
				customer_cost_each = parseFloat(customer_cost_each);
			} else {
				customer_cost_each = 0;
			}
			var customer_cost_total = customer_cost_day * customer_cost_people * customer_cost_each;
			$(this).closest('tr').find('.span-customer-cost-total').text(customer_cost_total);
			var cost_total = 0;
			$('.span-customer-cost-total').each(function(){
				cost_total += parseFloat($(this).text());
			});
			$('.span-cost-total').text(cost_total);
		});
	}
	
	//使成本项目选择框有效
	function bind_customer_cost_type_change(controller) {
		controller.change(function(){
			var customer_cost_type_id = $(this).val();
			if(customer_cost_type_id == '1') {
				$(this).closest('tr').find('.txt-customer-cost-desc').attr('readonly', false);
				$(this).closest('tr').find('.txt-customer-cost-desc').removeClass('readonly');
			} else {
				$(this).closest('tr').find('.txt-customer-cost-desc').attr('readonly', true);
				$(this).closest('tr').find('.txt-customer-cost-desc').addClass('readonly');
				$(this).closest('tr').find('.txt-customer-cost-desc').val('');
			}
		});
	}
	
	//使景点选择按钮有效
	function bind_select_spot_hope(controller) {
		controller.click(function(){
			var spot_id = $(this).closest('li').attr('data-id');
			var spot_name = $(this).closest('li').attr('data-name');
			var html_ul = [];
			html_ul.push('<li data-id="' + spot_id + '" data-name="' + spot_name + '">');
				html_ul.push(spot_name);
				html_ul.push('<p class="btn-spot-hope-unselect" id="btn-spot-hope-unselect-' + spot_id + '">×</p>');
				html_ul.push('<input type="hidden" name="spot_hope_list[]" value="' + spot_id + '" />');
			html_ul.push('</li>');
			$('#ul-spot-hope-list-selected').append(html_ul.join(''));
			$(this).closest('li').remove();
			bind_unselect_spot_hope($('#btn-spot-hope-unselect-' + spot_id));
		});
	}
	
	//使景点取消按钮有效
	function bind_unselect_spot_hope(controller) {
		controller.click(function(){
			var spot_id = $(this).closest('li').attr('data-id');
			var spot_name = $(this).closest('li').attr('data-name');
			var html_ul = [];
			html_ul.push('<li data-id="' + spot_id + '" data-name="' + spot_name + '">');
				html_ul.push('<p class="btn-spot-hope-select" id="btn-spot-hope-select-' + spot_id + '"></p>');
				html_ul.push(spot_name);
			html_ul.push('</li>');
			$('#ul-spot-hope-list').append(html_ul.join(''));
			$(this).closest('li').remove();
			bind_select_spot_hope($('#btn-spot-hope-select-' + spot_id));
		});
	}
	
	//使检索框有效
	function bind_search_area(controller) {
		controller.bind('input propertychange', function(){
			var search_name = $(this).val();
			if(search_name == "") {
				$(this).closest('.div-search-area').find('.ul-search-list li').show();
			} else {
				$(this).closest('.div-search-area').find('.ul-search-list li').each(function(){
					var name = $(this).attr('data-name');
					if(name.indexOf(search_name) != -1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		});
	}
	
	//使添加行表格的删除按钮有效
	function bind_delete_row(controller){
		controller.click(function(){
			$(this).closest('tr').remove();
		});
	}
	
	//使日程设计负责人选择按钮有效
	function bind_select_schedule_staff(schedule_num, user_id) {
		$('#btn-user-select-' + schedule_num + '-' + user_id).click(function(){
			var user_name = $(this).closest('li').attr('data-name');
			var html_ul = [];
			html_ul.push('<li data-userid="' + user_id + '" data-name="' + user_name + '">');
				html_ul.push(user_name);
				html_ul.push('<p class="btn-user-unselect" id="btn-user-unselect-' + schedule_num + '-' + user_id + '">×</p>');
				html_ul.push('<input type="hidden" name="schedule_user_list_' + schedule_num + '[]" value="' + user_id + '" />');
			html_ul.push('</li>');
			$('#ul-schedule-staff-selected-' + schedule_num).append(html_ul.join(''));
			$(this).closest('li').remove();
			bind_unselect_schedule_staff(schedule_num, user_id);
		});
	}
	
	//使日程设计负责人取消按钮有效
	function bind_unselect_schedule_staff(schedule_num, user_id) {
		$('#btn-user-unselect-' + schedule_num + '-' + user_id).click(function(){
			var user_name = $(this).closest('li').attr('data-name');
			var html_ul = [];
			html_ul.push('<li data-userid="' + user_id + '" data-name="' + user_name + '">');
				html_ul.push('<p class="btn-user-select" id="btn-user-select-' + schedule_num + '-' + user_id + '"></p>');
				html_ul.push(user_name);
			html_ul.push('</li>');
			$('#ul-schedule-staff-list-' + schedule_num).append(html_ul.join(''));
			$(this).closest('li').remove();
			bind_select_schedule_staff(schedule_num, user_id);
		});
	}
	
	//使添加详细日程按钮有效
	function bind_add_schedule_detail(controller) {
		controller.click(function(){
			var row = $(this).closest('.tb-schedule-detail').attr('data-row');
			var schedule_num = $(this).closest('.tb-schedule-detail').attr('data-schedulenum');
			var html = [];
			
			html.push('<tr>');
				html.push('<td>');
					html.push('<p class="btn-delete" id="btn-delete-schedule-detail-' + schedule_num + '-' + row + '">－</p>');
					html.push('<input type="hidden" name="schedule_row_' + schedule_num + '[]" value="' + row + '" />');
				html.push('</td>');
				html.push('<td>');
					html.push('<select name="schedule_start_at_' + schedule_num + '_' + row + '" id="sel-schedule-start-at-' + schedule_num + '-' + row + '" class="sel-schedule-time">');
						html.push('<option value="" class="placeholder">-请选择-</option>');
						for (var hour = 0; hour < 24; hour++) {
							for (var minute = 0; minute < 60; minute = minute + 30) {
								var time = ( '00' + hour ).slice( -2 ) + ":" + ( '00' + minute ).slice( -2 );
								html.push('<option value="' + time + '">' + time + '</option>');
							}
						}
					html.push('</select>');
					html.push(' ～ ');
					html.push('<select name="schedule_end_at_' + schedule_num + '_' + row + '" id="sel-schedule-end-at-' + schedule_num + '-' + row + '" class="sel-schedule-time">');
						html.push('<option value="" class="placeholder">-请选择-</option>');
						for (var hour = 0; hour < 24; hour++) {
							for (var minute = 0; minute < 60; minute = minute + 30) {
								var time = ( '00' + hour ).slice( -2 ) + ":" + ( '00' + minute ).slice( -2 );
								html.push('<option value="' + time + '">' + time + '</option>');
							}
						}
					html.push('</select>');
				html.push('</td>');
				html.push('<td>');
					html.push('<select name="schedule_type_' + schedule_num + '_' + row + '" id="sel-schedule-type-' + schedule_num + '-' + row + '">');
						html.push('<option value="" class="placeholder">-请选择-</option>');
						$.ajax({
							type: "POST",
							url: '/interface/admin/api_schedule_type_list/',
							data: {page: 'edit_customer'},
							dataType: "json",
							success: function(result) {
								if(result.result) {
									var schedule_type_list = result.schedule_type_list;
									var html_select = [];
									$.each(schedule_type_list,function(index,val){
										html_select.push('<option value="' + val.schedule_type_id + '">');
											html_select.push(val.schedule_type_name);
										html_select.push('</option>');
									});
									$('#sel-schedule-type-' + schedule_num + '-' + row).append(html_select.join(''));
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
				html.push('<td><input type="text" name="schedule_desc_' + schedule_num + '_' + row + '" placeholder="请输入内容" /></td>');
			html.push('</tr>');
			
			$(this).closest('tr').before(html.join(''));
			
			bind_delete_schedule_detail($('#btn-delete-schedule-detail-' + schedule_num + '-' + row));
			bind_select_placeholder($('#sel-schedule-start-at-' + schedule_num + '-' + row));
			bind_select_placeholder($('#sel-schedule-end-at-' + schedule_num + '-' + row));
			bind_select_placeholder($('#sel-schedule-type-' + schedule_num + '-' + row));
			
			new_row = parseInt(row) + 1;
			$(this).closest('.tb-schedule-detail').attr('data-row', new_row);
		});
	}
	
	//使删除详细日程按钮有效
	function bind_delete_schedule_detail(controller) {
		controller.click(function(){
			$(this).closest('tr').remove();
		});
	}
	
	//使删除日程设计按钮有效
	function bind_delete_schedule(controller) {
		controller.click(function(){
			$(this).closest('.schedule-block').remove();
		});
	}
	
	//生成一个日程设计输入框
	function html_schedule_area(schedule_num) {
		var html = [];
		
		html.push('<div class="schedule-block">');
			html.push('<table class="content-form-talbe-inner">');
				html.push('<tr>');
					html.push('<th>日期</th>');
					html.push('<td>');
						html.push('<input type="text" name="schedule_date_' + schedule_num + '" id="cal-schedule-date-' + schedule_num + '" value="" placeholder="请输入日期 例:2030/01/01" />');
					html.push('</td>');
				html.push('</tr>');
				html.push('<tr>');
					html.push('<th>负责人</th>');
					html.push('<td>');
						html.push('<ul class="ul-schedule-staff-selected" id="ul-schedule-staff-selected-' + schedule_num + '"></ul>');
						html.push('<div class="div-search-area">');
							html.push('<div class="div-schedule-staff-search"><input type="text" class="txt-schedule-staff-search" id="txt-schedule-staff-search-' + schedule_num + '" placeholder="请输入负责人姓名" /></div>');
							html.push('<ul class="ul-schedule-staff-list ul-search-list" id="ul-schedule-staff-list-' + schedule_num + '">');
								$.ajax({
									type: "POST",
									url: '/interface/admin/api_simple_user_list/',
									data: {page: 'edit_customer'},
									dataType: "json",
									success: function(result) {
										if(result.result) {
											user_list = result.user_list;
											var html_ul = [];
											$.each(user_list,function(index,val){
												html_ul.push('<li data-userid="' + val.user_id + '" data-name="' + val.user_name + '">');
													html_ul.push('<p class="btn-user-select" id="btn-user-select-' + schedule_num + '-' + val.user_id + '"></p>');
													html_ul.push(val.user_name);
												html_ul.push('</li>');
											});
											$('#ul-schedule-staff-list-' + schedule_num).append(html_ul.join(''));
											$.each(user_list,function(index,val){
												bind_select_schedule_staff(schedule_num, val.user_id);
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
						html.push('</div>');
					html.push('</td>');
				html.push('</tr>');
				html.push('<tr>');
					html.push('<th>详细日程</th>');
					html.push('<td>');
						html.push('<table class="tb-add-row-table tb-schedule-detail" data-row="0" data-schedulenum="' + schedule_num + '">');
							html.push('<tr>');
								html.push('<th class="th-delete"></th>');
								html.push('<th class="th-time">时间</th>');
								html.push('<th class="th-type">类型</th>');
								html.push('<th class="th-desc">内容</th>');
							html.push('</tr>');
							html.push('<tr><th colspan="4" class="th-add" id="th-schedule-detail-add-' + schedule_num + '">添加一行详细日程</td></tr>');
						html.push('</table>');
					html.push('</td>');
				html.push('</tr>');
			html.push('</table>');
			html.push('<p class="btn-schedule-delete" id="btn-schedule-delete-' + schedule_num + '">删除日程</p>');
			html.push('<input type="hidden" name="schedule_num[]" value="' + schedule_num + '" />');
		html.push('</div>');
		
		return html.join('');
	}
	
	//初始计算成本相关输入框绑定
	$('.txt-customer-cost-day').each(function(){bind_cost_total_calculation($(this));});
	$('.txt-customer-cost-people').each(function(){bind_cost_total_calculation($(this));});
	$('.txt-customer-cost-each').each(function(){bind_cost_total_calculation($(this));});
	
	//初始成本项目选择框绑定
	$('.sel-customer-cost-type').each(function(){bind_customer_cost_type_change($(this));});
	
	//初始景点选择功能绑定
	$('.btn-spot-hope-select').each(function(){bind_select_spot_hope($(this));});
	$('.btn-spot-hope-unselect').each(function(){bind_unselect_spot_hope($(this));});
	
	//初始检索功能绑定
	$('.txt-search').each(function(){bind_search_area($(this));});
	
	//初始改变实际成本项目功能绑定
	$('.sel-customer-cost-type').each(function(){bind_customer_cost_type_change($(this));});
	
	//初始日历功能绑定
	$('.calendar').datepicker();
	
	//初始选择负责人按钮绑定
	$('.btn-user-select').each(function(){
		var schedule_num = $(this).closest('li').attr('data-schedulenum');
		var user_id = $(this).closest('li').attr('data-userid');
		bind_select_schedule_staff(schedule_num, user_id);
	});
	
	//初始删除负责人按钮绑定
	$('.btn-user-unselect').each(function(){
		var schedule_num = $(this).closest('li').attr('data-schedulenum');
		var user_id = $(this).closest('li').attr('data-userid');
		bind_unselect_schedule_staff(schedule_num, user_id);
	});
	
	//初始详细日程添加按钮绑定
	$('.tb-schedule-detail .th-add').each(function(){bind_add_schedule_detail($(this));});
	
	//初始详细日程删除按钮绑定
	$('.tb-schedule-detail .btn-delete').each(function(){bind_delete_schedule_detail($(this));});
	
	//改变是否有希望去的景点的选择
	$('.rdo-spot-hope-flag').change(function(){
		var spot_hope_flag = $(this).val();
		if(spot_hope_flag == '1') {
			$(this).closest('td').find('.div-spot-hope-list').slideDown();
		} else {
			$(this).closest('td').find('.div-spot-hope-list').slideUp();
		}
	});
	
	//改变是否有希望去的景点的选择
	$('.rdo-hotel-reserve-flag').change(function(){
		var hotel_reserve_flag = $(this).val();
		if(hotel_reserve_flag == '1') {
			$(this).closest('td').find('.div-hotel-reserve-list').slideDown();
		} else {
			$(this).closest('td').find('.div-hotel-reserve-list').slideUp();
		}
	});
	
	//添加一行酒店预约
	$('.tb-hotel-reserve-list th.th-add').click(function(){
		var row = $('#tb-hotel-reserve-list').attr('data-row');
		var html = [];
		
		html.push('<tr>');
			html.push('<td>');
				html.push('<p class="btn-delete" id="btn-delete-hotel-reserve-' + row + '">－</p>');
				html.push('<input type="hidden" name="hotel_reserve_row[]" value="' + row + '" />');
			html.push('</td>');
			html.push('<td>');
				html.push('<select class="sel-hotel-type" name="hotel_type_' + row + '" id="sel-hotel-type-' + row + '">');
					html.push('<option value="" class="placeholder">-请选择-</option>');
					$.ajax({
						type: "POST",
						url: '/interface/admin/api_hotel_type_list/',
						data: {page: 'edit_customer'},
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
			html.push('<td>');
				html.push('<select class="sel-room-type" name="room_type_' + row + '" id="sel-room-type-' + row + '">');
					html.push('<option value="" class="placeholder">-请选择-</option>');
					$.ajax({
						type: "POST",
						url: '/interface/admin/api_room_type_list/',
						data: {page: 'edit_customer'},
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
			html.push('<td><input type="number" name="people_num_' + row + '" class="txt-people-num" placeholder="人数" /></td>');
			html.push('<td><input type="number" name="room_num_' + row + '" class="txt-room-num" placeholder="间数" /></td>');
			html.push('<td><input type="number" name="day_num_' + row + '" class="txt-day-num" placeholder="天数" /></td>');
			html.push('<td><input type="text" name="comment_' + row + '" value="" placeholder="请输入备注" /></td>');
		html.push('</tr>');
		
		$(this).closest('tr').before(html.join(''));
		
		bind_select_placeholder($('#sel-hotel-type-' + row));
		bind_select_placeholder($('#sel-room-type-' + row));
		bind_delete_row($('#btn-delete-hotel-reserve-' + row));
		
		new_row = parseInt(row) + 1;
		$('#tb-hotel-reserve-list').attr('data-row', new_row);
	});
	
	//添加一行实际成本
	$('.tb-customer-cost th.th-add').click(function(){
		var row = $('#tb-customer-cost').attr('data-row');
		var html = [];
		
		html.push('<tr>');
			html.push('<td>');
				html.push('<p class="btn-delete" id="btn-delete-customer-cost-' + row + '">－</p>');
				html.push('<input type="hidden" name="customer_cost_row[]" value="' + row + '" />');
			html.push('</td>');
			html.push('<td>');
				html.push('<select name="customer_cost_type_' + row + '" id="sel-customer-cost-type-' + row + '">');
					html.push('<option value="" class="placeholder">-请选择-</option>');
					$.ajax({
						type: "POST",
						url: '/interface/admin/api_customer_cost_type_list/',
						data: {page: 'edit_customer'},
						dataType: "json",
						success: function(result) {
							if(result.result) {
								customer_cost_type_list = result.customer_cost_type_list;
								var html_select = [];
								$.each(customer_cost_type_list,function(index,val){
									html_select.push('<option value="' + val.customer_cost_type_id + '">');
										html_select.push(val.customer_cost_type_name);
									html_select.push('</option>');
								});
								$('#sel-customer-cost-type-' + row).append(html_select.join(''));
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
			html.push('<td><input type="text" name="customer_cost_desc_' + row + '" class="txt-customer-cost-desc readonly" value="" placeholder="请输入简述" readonly="readonly" /></td>');
			html.push('<td><input type="number" name="customer_cost_day_' + row + '" class="txt-customer-cost-day" id="txt-customer-cost-day-' + row + '" placeholder="天数" /></td>');
			html.push('<td><input type="number" name="customer_cost_people_' + row + '" class="txt-customer-cost-people" id="txt-customer-cost-people-' + row + '" placeholder="人数" /></td>');
			html.push('<td><input type="number" name="customer_cost_each_' + row + '" class="txt-customer-cost-each" id="txt-customer-cost-each-' + row + '" placeholder="单价" /></td>');
			html.push('<td class="td-total"><span class="span-customer-cost-total">0</span></td>');
		html.push('</tr>');
		
		$(this).closest('tr').before(html.join(''));
		
		bind_cost_total_calculation($('#txt-customer-cost-day-' + row));
		bind_cost_total_calculation($('#txt-customer-cost-people-' + row));
		bind_cost_total_calculation($('#txt-customer-cost-each-' + row));
		bind_customer_cost_type_change($('#sel-customer-cost-type-' + row));
		bind_select_placeholder($('#sel-customer-cost-type-' + row));
		bind_delete_row($('#btn-delete-customer-cost-' + row));
		
		new_row = parseInt(row) + 1;
		$('#tb-customer-cost').attr('data-row', new_row);
	});
	
	//添加一条日程设计
	$('#schedule-add').click(function(){
		var schedule_num = $('#schedule-area').attr('data-schedulenum');
		
		$('#schedule-area').append(html_schedule_area(schedule_num));
		$('#cal-schedule-date-' + schedule_num).datepicker();
		bind_delete_schedule($('#btn-schedule-delete-' + schedule_num));
		bind_add_schedule_detail($('#th-schedule-detail-add-' + schedule_num));
		bind_search_area($('#txt-schedule-staff-search-' + schedule_num));
		
		new_schedule_num = parseInt(schedule_num) + 1;
		$('#schedule-area').attr('data-schedulenum', new_schedule_num);
	});
	
});