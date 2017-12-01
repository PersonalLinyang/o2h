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
			var cost_day = parseFloat($(this).closest('tr').find('.txt-cost-day').val());
			var cost_people = parseFloat($(this).closest('tr').find('.txt-cost-people').val());
			var cost_each = parseFloat($(this).closest('tr').find('.txt-cost-each').val());
			var cost_total = cost_day * cost_people * cost_each;
			$(this).closest('tr').find('.span-cost-total').text(cost_total);
			var customer_cost = 0;
			$('.span-cost-total').each(function(){
				customer_cost += parseFloat($(this).text());
			});
			$('.span-customer-cost').text(customer_cost);
		});
	}
	
	//使成本项目选择框有效
	function bind_customer_cost_type_change(controller) {
		controller.change(function(){
			var customer_cost_type_id = $(this).val();
			if(customer_cost_type_id == '1') {
				$(this).closest('tr').find('.txt-customer-cost-name').attr('readonly', false);
				$(this).closest('tr').find('.txt-customer-cost-name').removeClass('readonly');
			} else {
				$(this).closest('tr').find('.txt-customer-cost-name').attr('readonly', true);
				$(this).closest('tr').find('.txt-customer-cost-name').addClass('readonly');
				$(this).closest('tr').find('.txt-customer-cost-name').val('');
			}
		});
	}
	
	//使景点选择按钮有效
	function bind_select_spot_hope(controller) {
		controller.click(function(){
			var spot_id = $(this).closest('li').attr('data-spotid');
			var spot_name = $(this).closest('li').attr('data-spotname');
			var html_ul = [];
			html_ul.push('<li data-spotid="' + spot_id + '" data-spotname="' + spot_name + '">');
				html_ul.push(spot_name);
				html_ul.push('<p class="btn-spot-hope-unselect" id="btn-spot-hope-unselect-' + spot_id + '">×</p>');
				html_ul.push('<input type="hidden" name="route_spot_hope_list[]" value="' + spot_id + '" />');
			html_ul.push('</li>');
			$('#ul-spot-hope-list-selected').append(html_ul.join(''));
			$(this).closest('li').remove();
			bind_unselect_spot_hope($('#btn-spot-hope-unselect-' + spot_id));
		});
	}
	
	//使景点取消按钮有效
	function bind_unselect_spot_hope(controller) {
		controller.click(function(){
			var spot_id = $(this).closest('li').attr('data-spotid');
			var spot_name = $(this).closest('li').attr('data-spotname');
			var html_ul = [];
			html_ul.push('<li data-spotid="' + spot_id + '" data-spotname="' + spot_name + '">');
				html_ul.push('<p class="btn-spot-hope-select" id="btn-spot-hope-select-' + spot_id + '"></p>');
				html_ul.push(spot_name);
			html_ul.push('</li>');
			$('#ul-spot-hope-list').append(html_ul.join(''));
			$(this).closest('li').remove();
			bind_select_spot_hope($('#btn-spot-hope-select-' + spot_id));
		});
	}
	
	//使添加行表格的删除按钮有效
	function bind_delete_row(controller){
		controller.click(function(){
			$(this).closest('tr').remove();
		});
	}
	
	//初始计算成本相关输入框绑定
	$('.txt-cost-day').each(function(){bind_cost_total_calculation($(this));});
	$('.txt-cost-people').each(function(){bind_cost_total_calculation($(this));});
	$('.txt-cost-each').each(function(){bind_cost_total_calculation($(this));});
	
	//初始成本项目选择框绑定
	$('.sel-customer-cost-type').each(function(){bind_customer_cost_type_change($(this));});
	
	//初始景点选择功能绑定
	$('.btn-spot-hope-select').each(function(){bind_select_spot_hope($(this));});
	$('.btn-spot-hope-unselect').each(function(){bind_unselect_spot_hope($(this));});
	
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
		var cost_row = $('#tb-hotel-reserve-list').attr('data-costrow');
		var html = [];
		
		html.push('<tr>');
			html.push('<td><p class="btn-delete" id="btn-delete-hotel-reserve-' + cost_row + '">－</p></td>');
			html.push('<td>');
				html.push('<select class="sel-hotel-type" name="hotel_type_' + cost_row + '" id="sel-hotel-type-' + cost_row + '">');
					html.push('<option value="" class="placeholder">-请选择-</option>');
					$.ajax({
						type: "POST",
						url: '/admin/add_customer/hotel_type_list/',
						data: {page: 'add_customer'},
						dataType: "json",
						success: function(hotel_type_list) {
							var html_select = [];
							$.each(hotel_type_list,function(index,val){
								html_select.push('');
								html_select.push('<option value="' + val.hotel_type_id + '">');
									html_select.push(val.hotel_type_name);
								html_select.push('</option>');
							});
							$('#sel-hotel-type-' + cost_row).append(html_select.join(''));
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
				html.push('<select class="sel-room-type" name="room_type_' + cost_row + '" id="sel-room-type-' + cost_row + '">');
					html.push('<option value="" class="placeholder">-请选择-</option>');
					$.ajax({
						type: "POST",
						url: '/admin/add_customer/room_type_list/',
						data: {page: 'add_customer'},
						dataType: "json",
						success: function(room_type_list) {
							var html_select = [];
							$.each(room_type_list,function(index,val){
								html_select.push('');
								html_select.push('<option value="' + val.room_type_id + '">');
									html_select.push(val.room_type_name);
								html_select.push('</option>');
							});
							$('#sel-room-type-' + cost_row).append(html_select.join(''));
						},
						error: function(XMLHttpRequest, textStatus, errorThrown) {
							console.log("XMLHttpRequest : " + XMLHttpRequest.status);
							console.log("textStatus : " + textStatus);
							console.log("errorThrown : " + errorThrown.message);
						}
					});
				html.push('</select>');
			html.push('</td>');
			html.push('<td><input type="number" name="people_num_' + cost_row + '" class="txt-people-num" value="0" placeholder="人数" /></td>');
			html.push('<td><input type="number" name="room_num_' + cost_row + '" class="txt-room-num" value="0" placeholder="间数" /></td>');
			html.push('<td><input type="number" name="day_num_' + cost_row + '" class="txt-day-num" value="0" placeholder="天数" /></td>');
			html.push('<td><input type="text" name="etc_' + cost_row + '" value="" maxlength="50" placeholder="请输入备注" /></td>');
		html.push('</tr>');
		
		$(this).closest('tr').before(html.join(''));
		
		bind_select_placeholder($('#sel-hotel-type-' + cost_row));
		bind_select_placeholder($('#sel-room-type-' + cost_row));
		bind_delete_row($('#btn-delete-hotel-reserve-' + cost_row));
		
		new_cost_row = parseInt(cost_row) + 1;
		$('#tb-hotel-reserve-list').attr('data-costrow', new_cost_row);
	});
	
	//添加一行实际成本
	$('.tb-customer-cost th.th-add').click(function(){
		var cost_row = $('#tb-customer-cost').attr('data-costrow');
		var html = [];
		
		html.push('<tr>');
			html.push('<td><p class="btn-delete" id="btn-delete-customer-cost-type-' + cost_row + '">－</p></td>');
			html.push('<td>');
				html.push('<select name="customer_cost_type_' + cost_row + '" id="sel-customer-cost-type-' + cost_row + '">');
					html.push('<option value="" class="placeholder">-请选择-</option>');
					$.ajax({
						type: "POST",
						url: '/admin/add_customer/customer_cost_type_list/',
						data: {page: 'add_customer'},
						dataType: "json",
						success: function(customer_cost_type_list) {
							var html_select = [];
							$.each(customer_cost_type_list,function(index,val){
								html_select.push('<option value="' + val.customer_cost_type_id + '">');
									html_select.push(val.customer_cost_type_name);
								html_select.push('</option>');
							});
							html_select.push('<option value="1">其他</option>');
							$('#sel-customer-cost-type-' + cost_row).append(html_select.join(''));
						},
						error: function(XMLHttpRequest, textStatus, errorThrown) {
							console.log("XMLHttpRequest : " + XMLHttpRequest.status);
							console.log("textStatus : " + textStatus);
							console.log("errorThrown : " + errorThrown.message);
						}
					});
				html.push('</select>');
			html.push('</td>');
			html.push('<td><input type="text" name="customer_cost_name_' + cost_row + '" class="txt-customer-cost-name readonly" value="" maxlength="100" placeholder="请输入简述" readonly="readonly" /></td>');
			html.push('<td><input type="number" name="cost_day_' + cost_row + '" class="txt-cost-day" id="txt-cost-day-' + cost_row + '" value="0" placeholder="天数" /></td>');
			html.push('<td><input type="number" name="cost_people_' + cost_row + '" class="txt-cost-people" id="txt-cost-people-' + cost_row + '" value="0" placeholder="人数" /></td>');
			html.push('<td><input type="number" name="cost_each_' + cost_row + '" class="txt-cost-each" id="txt-cost-each-' + cost_row + '" value="0" placeholder="单价" /></td>');
			html.push('<td class="td-total"><span class="span-cost-total">0</span></td>');
		html.push('</tr>');
		
		$(this).closest('tr').before(html.join(''));
		
		bind_cost_total_calculation($('#txt-cost-day-' + cost_row));
		bind_cost_total_calculation($('#txt-cost-people-' + cost_row));
		bind_cost_total_calculation($('#txt-cost-each-' + cost_row));
		bind_customer_cost_type_change($('#sel-customer-cost-type-' + cost_row));
		bind_select_placeholder($('#sel-customer-cost-type-' + cost_row));
		bind_delete_row($('#btn-delete-customer-cost-type-' + cost_row));
		
		new_cost_row = parseInt(cost_row) + 1;
		$('#tb-customer-cost').attr('data-costrow', new_cost_row);
	});
	
});