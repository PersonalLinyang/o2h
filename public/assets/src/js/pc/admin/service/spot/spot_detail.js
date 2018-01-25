$(function(){
	//点击更新公开状态按钮
	$('.btn-spot-status').click(function(){
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-spot-status').fadeIn();
	});
	
	//点击弹窗中的确定按钮
	$('#popup-spot-status-yes').click(function(){
		$('#form-spot-status').submit();
	});
	
	//点击弹窗中的取消按钮
	$('#popup-spot-status-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-spot-status').hide();
	});
});