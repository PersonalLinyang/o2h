$(function(){
	//点击更新公开状态按钮
	$('.btn-route-status').click(function(){
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-route-status').fadeIn();
	});
	
	//点击弹窗中的确定按钮
	$('#popup-route-status-yes').click(function(){
		$('#form-route-status').submit();
	});
	
	//点击弹窗中的取消按钮
	$('#popup-route-status-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-route-status').hide();
	});
});