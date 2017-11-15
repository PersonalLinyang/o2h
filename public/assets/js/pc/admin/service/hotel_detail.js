$(function(){
	//点击更新公开状态按钮
	$('.btn-hotel-status').click(function(){
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-hotel-status').fadeIn();
	});
	
	//点击弹窗中的确定按钮
	$('#popup-hotel-status-yes').click(function(){
		$('#form-hotel-status').submit();
	});
	
	//点击弹窗中的取消按钮
	$('#popup-hotel-status-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-hotel-status').hide();
	});
});