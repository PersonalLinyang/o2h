$(function(){
	//点击变更顾客状态按钮
	$('.btn-customer-status').click(function(){
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-customer-status').fadeIn();
	});
	
	//点击顾客状态弹窗中的确定按钮
	$('#popup-customer-status-yes').click(function(){
		$('#form-customer-status').submit();
	});
	
	//点击顾客状态弹窗中的取消按钮
	$('#popup-customer-status-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-customer-status').hide();
	});
	
	//点击变更失效状态按钮
	$('.btn-customer-delete').click(function(){
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-customer-delete').fadeIn();
	});
	
	//点击失效弹窗中的确定按钮
	$('#popup-customer-delete-yes').click(function(){
		$('#form-customer-delete').submit();
	});
	
	//点击失效弹窗中的取消按钮
	$('#popup-customer-delete-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-customer-delete').hide();
	});
});