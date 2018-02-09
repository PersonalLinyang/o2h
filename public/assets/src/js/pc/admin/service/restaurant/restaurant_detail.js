$(function(){
	//点击更新公开状态按钮
	$('.btn-restaurant-status').click(function(){
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-restaurant-status').fadeIn();
	});
	
	//点击弹窗中的确定按钮
	$('#popup-restaurant-status-yes').click(function(){
		$('#form-restaurant-status').submit();
	});
	
	//点击弹窗中的取消按钮
	$('#popup-restaurant-status-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-restaurant-status').hide();
	});
});