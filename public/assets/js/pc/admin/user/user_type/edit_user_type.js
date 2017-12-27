$(function(){

	/* 点击持有权限单选按钮 */
	$('.lbl-for-check-permission').click(function(){
		if($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).closest('.permission-area').children('.permission-area').find('input[type="checkbox"]').prop('checked', false);
			$(this).closest('.permission-area').children('.permission-area').find('.lbl-for-check-permission').removeClass('active');
		} else {
			$(this).addClass('active');
			$(this).closest('.permission-area').parents('.permission-area').children('.permission-line').find('input[type="checkbox"]').prop('checked', true);
			$(this).closest('.permission-area').parents('.permission-area').children('.permission-line').find('.lbl-for-check-permission').addClass('active');
		}
	});
	
	//点击展开/收起按钮
	$('.btn-permission-show').click(function(){
		if($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).closest('.permission-area').children('.permission-area').fadeIn();
		} else {
			$(this).addClass('active');
			$(this).closest('.permission-area').children('.permission-area').fadeOut();
		}
	});

});