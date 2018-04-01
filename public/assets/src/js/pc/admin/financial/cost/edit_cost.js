$(function(){
	
	//使计算支出相关输入框有效
	function bind_cost_total_calculation(controller) {
		controller.bind('input propertychange', function(){
			var cost_detail_each = $(this).closest('tr').find('.txt-cost-detail-each').val();
			if($.isNumeric(cost_detail_each)) {
				cost_detail_each = parseFloat(cost_detail_each);
			} else {
				cost_detail_each = 0;
			}
			var cost_detail_count = $(this).closest('tr').find('.txt-cost-detail-count').val();
			if($.isNumeric(cost_detail_count)) {
				cost_detail_count = parseFloat(cost_detail_count);
			} else {
				cost_detail_count = 0;
			}
			var cost_detail_total = cost_detail_each * cost_detail_count;
			$(this).closest('tr').find('.span-cost-detail-total').text(cost_detail_total);
			var cost_price = 0;
			$('.span-cost-detail-total').each(function(){
				cost_price += parseFloat($(this).text());
			});
			$('.span-cost-price').text(cost_price);
		});
	}
	
	//使添加行表格的删除按钮有效
	function bind_delete_row(controller){
		controller.click(function(){
			$(this).closest('tr').remove();
		});
	}
	
	//初始计算成本相关输入框绑定
	$('.txt-cost-detail-each').each(function(){bind_cost_total_calculation($(this));});
	$('.txt-cost-detail-count').each(function(){bind_cost_total_calculation($(this));});
	
	//初始日历功能绑定
	$('.calendar').datepicker();
	
	//添加一行实际成本
	$('.tb-cost-detail th.th-add').click(function(){
		var row = $('#tb-cost-detail').attr('data-row');
		var html = [];
		
		html.push('<tr>');
			html.push('<td>');
				html.push('<p class="btn-delete" id="btn-delete-cost-detial-' + row + '">－</p>');
				html.push('<input type="hidden" name="cost_detail_row[]" value="' + row + '" />');
			html.push('</td>');
			html.push('<td><input type="text" name="cost_detail_desc_' + row + '" class="txt-cost-detail-desc" placeholder="请输入简述" /></td>');
			html.push('<td><input type="number" name="cost_detail_each_' + row + '" class="txt-cost-detail-each" id="txt-cost-detail-each-' + row + '" placeholder="单价" /></td>');
			html.push('<td><input type="number" name="cost_detail_count_' + row + '" class="txt-cost-detail-count" id="txt-cost-detail-count-' + row + '" placeholder="数量" /></td>');
			html.push('<td class="td-total"><span class="span-cost-detail-total">0</span></td>');
		html.push('</tr>');
		
		$(this).closest('tr').before(html.join(''));
		
		bind_cost_total_calculation($('#txt-cost-detail-each-' + row));
		bind_cost_total_calculation($('#txt-cost-detail-count-' + row));
		bind_delete_row($('#btn-delete-cost-detial-' + row));
		
		new_row = parseInt(row) + 1;
		$('#tb-cost-detail').attr('data-row', new_row);
	});
	
});