$(function(){
	//点击更新确认状态按钮
	$('.btn-approval-status').click(function(){
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-approval-status').fadeIn();
	});
	
	//点击弹窗中的确定按钮
	$('#popup-approval-status-yes').click(function(){
		$('#form-approval-status').submit();
	});
	
	//点击弹窗中的取消按钮
	$('#popup-approval-status-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-approval-status').hide();
	});
});