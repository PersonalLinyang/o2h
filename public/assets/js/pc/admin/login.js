$(function(){
	$('.login-button').hover(function(){
		$(this).addClass('active');
	}, function(){
		$(this).removeClass('active');
	});

	$('#btn-login').click(function(){
		var user_email = $('#user-email').val();
		var user_password = $('#user-password').val();
		if(!user_email) {
			$('#error-message').text('※请输入邮箱※');
		} else if(!user_password) {
			$('#error-message').text('※请输入密码※');
		} else {
			$('#login-form').submit();
		}
	});
});