$(function(){
	//点击删除选中收入记录按钮
	$('#btn-content-menu-delete-checked').click(function(){
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-delete-checked').fadeIn();
	});
	
	//点击删除选中收入记录弹窗中的确定按钮
	$('#popup-delete-checked-yes').click(function(){
		$('#form-delete-checked').submit();
	});
	
	//点击删除选中收入记录弹窗中的取消按钮
	$('#popup-delete-checked-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-delete-checked').hide();
	});

	//点击表格中的删除按钮
	$('.btn-delete').click(function(){
		//获得删除数据信息
		var delete_value = $(this).data('value');
		var delete_id = $(this).data('value');
		
		//修改弹窗信息
		$('.popup-delete-id').html(delete_id);
		$('#input-id').val(delete_value);
		
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-delete').fadeIn();
	});
	
	//点击删除特定收入记录弹窗中的确定按钮
	$('#popup-delete-yes').click(function(){
		$('#form-delete').submit();
	});
	
	//点击删除特定收入记录弹窗中的取消按钮
	$('#popup-delete-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-delete').hide();
	});
	
	//绑定检索中的日历控件
	$('.calendar').datepicker();
});