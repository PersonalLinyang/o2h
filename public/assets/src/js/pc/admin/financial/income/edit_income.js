$(function(){
	
	//使计算收入相关输入框有效
	function bind_income_total_calculation(controller) {
		controller.bind('input propertychange', function(){
			var income_detail_each = $(this).closest('tr').find('.txt-income-detail-each').val();
			if($.isNumeric(income_detail_each)) {
				income_detail_each = parseFloat(income_detail_each);
			} else {
				income_detail_each = 0;
			}
			var income_detail_count = $(this).closest('tr').find('.txt-income-detail-count').val();
			if($.isNumeric(income_detail_count)) {
				income_detail_count = parseFloat(income_detail_count);
			} else {
				income_detail_count = 0;
			}
			var income_detail_total = income_detail_each * income_detail_count;
			$(this).closest('tr').find('.span-income-detail-total').text(income_detail_total);
			var income_price = 0;
			$('.span-income-detail-total').each(function(){
				income_price += parseFloat($(this).text());
			});
			$('.span-income-price').text(income_price);
		});
	}
	
	//使添加行表格的删除按钮有效
	function bind_delete_row(controller){
		controller.click(function(){
			$(this).closest('tr').remove();
		});
	}
	
	//初始计算成本相关输入框绑定
	$('.txt-income-detail-each').each(function(){bind_income_total_calculation($(this));});
	$('.txt-income-detail-count').each(function(){bind_income_total_calculation($(this));});
	
	//初始日历功能绑定
	$('.calendar').datepicker();
	
	//添加一行实际成本
	$('.tb-income-detail th.th-add').click(function(){
		var row = $('#tb-income-detail').attr('data-row');
		var html = [];
		
		html.push('<tr>');
			html.push('<td>');
				html.push('<p class="btn-delete" id="btn-delete-income-detial-' + row + '">－</p>');
				html.push('<input type="hidden" name="income_detail_row[]" value="' + row + '" />');
			html.push('</td>');
			html.push('<td><input type="text" name="income_detail_desc_' + row + '" class="txt-income-detail-desc" placeholder="请输入简述" /></td>');
			html.push('<td><input type="number" name="income_detail_each_' + row + '" class="txt-income-detail-each" id="txt-income-detail-each-' + row + '" placeholder="单价" /></td>');
			html.push('<td><input type="number" name="income_detail_count_' + row + '" class="txt-income-detail-count" id="txt-income-detail-count-' + row + '" placeholder="数量" /></td>');
			html.push('<td class="td-total"><span class="span-income-detail-total">0</span></td>');
		html.push('</tr>');
		
		$(this).closest('tr').before(html.join(''));
		
		bind_income_total_calculation($('#txt-income-detail-each-' + row));
		bind_income_total_calculation($('#txt-income-detail-count-' + row));
		bind_delete_row($('#btn-delete-income-detial-' + row));
		
		new_row = parseInt(row) + 1;
		$('#tb-income-detail').attr('data-row', new_row);
	});
	
});