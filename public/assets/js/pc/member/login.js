$(function(){
	//显示控件右侧弹出警告
	function Display_Tooltip(name,text){
		var name2 = name.replace("[]","");
		$("#efo_balloon_"+name2 ).remove();

		var targetOffset = $("*[name='"+ name +"']").offset();
		var targetWidth  = $("*[name='"+ name +"']").width();
		$("body").prepend("<div id='efo_balloon_"+name2+"'><div><span></span>"+ text +"</div></div>");

		var balloonLeft = targetOffset.left + targetWidth + 30;
		var balloonTop = targetOffset.top+19;
		$("#efo_balloon_"+name2 ).css({
			left:balloonLeft,
			top:balloonTop,
			position:"absolute",
			"z-index":"2"
		});
		$("#efo_balloon_"+ name2 +" div").css({
			fontSize:"13px",
			fontWeight:"bold",
			minWidth:"50px",
			maxWidth:"150px",
			padding:"5px 10px",
			marginTop:"-18px",
			background:"#000000",
			color:"#ffffff",
			borderRadius:"5px",
			position:"relative"
		});
		$("#efo_balloon_"+ name2 +" span").css({
			position:"absolute",
			top:"8px",
			left:"-12px",
			width:"0",
			height:"0",
			border:"5px solid transparent",
			borderRight:"10px solid #000000",
			margin:"auto 0"
		});

		$("#efo_balloon_"+name2).hide();
		if(!jQuery.support.style) $("#efo_balloon_"+name2).show(); // IE6,7
		else $("#efo_balloon_"+name2).fadeIn("slow"); // それ以外
	}

	//删除控件右侧弹出警告
	function Delete_Tooltip(name){
		if(!jQuery.support.style) $("#efo_balloon_"+name).hide();	// IE6,7
		else $("#efo_balloon_"+name).fadeOut("slow"); // それ以外
		return;
	}

	//姓名验证
	function checkErrorName(value) {
		if($.trim(value) == '') {
			return '未输入';
		} else if(value.length > 50) {
			return '不能超过50字';
		} else {
			return false;
		}
	}

	//电子邮箱验证
	function checkErrorEmail(value) {
		if($.trim(value) == '') {
			return '未输入';
		} else if(value.length > 200) {
			return '不能超过200字';
		} else if(!value.match(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/)) {
			return '电子邮箱不符合格式';
		} else {
			return false;
		}
	}

	//密码验证
	function checkErrorPassword(value) {
		if($.trim(value) == '') {
			return '未输入';
		} else if(!value.match(/^[0-9A-Za-z]{6,16}$/)) {
			return '请输入6～16位字母数字';
		} else {
			return false;
		}
	}

	//确认密码验证
	function checkErrorRePassword(value, password) {
		if($.trim(value) == '') {
			return '未输入';
		} else if(!value.match(/^[0-9A-Za-z]{6,16}$/)) {
			return '请输入6～16位字母数字';
		} else if(value != password) {
			return '两次输入的密码不一致';
		}else {
			return false;
		}
	}

	//联系电话验证
	function checkErrorTel(value) {
		if($.trim(value) != '') {
			if(!value.match(/^[-_0-9]{1,20}$/)) {
				return '联系电话不符合格式';
			}
		}
		return false;
	}

	//微信号验证
	function checkErrorWechat(value) {
		if($.trim(value) != '') {
			if(!value.match(/^[a-zA-Z]{1}[-_a-zA-Z0-9]{5,19}$/)) {
				return '微信号不符合格式';
			}
		}
		return false;
	}

	//QQ号验证
	function checkErrorQQ(value) {
		if($.trim(value) != '') {
			if(!value.match(/^[1-9][0-9]{4,9}$/)) {
				return 'QQ号不符合格式';
			}
		}
		return false;
	}

	//注册按钮有效检验
	function checkAccessInput() {
		var name = $("input[name='member_name']").val();
		var email = $("input[name='member_email_access']").val();
		var password = $("input[name='member_password_access']").val();
		var repassword = $("input[name='member_repassword']").val();
		var tel = $("input[name='member_tel']").val();
		var wechat = $("input[name='member_wechat']").val();
		var qq = $("input[name='member_qq']").val();

		if(!checkErrorName(name) && !checkErrorEmail(email) && !checkErrorPassword(password) && !checkErrorRePassword(repassword, password)
				&& !checkErrorTel(tel) && !checkErrorWechat(wechat) && !checkErrorQQ(qq)) {
			$('.btn-access').addClass('active');
			return true;
		} else {
			$('.btn-access').removeClass('active');
			return false;
		}
	}

	//初始注册按钮
	checkAccessInput();

	//姓名验证
	$("input[name='member_name']").keyup(function(){
		var result = checkErrorName($(this).val());
		if(!result) {
			Delete_Tooltip('member_name');
		}
		checkAccessInput();
	});
	$("input[name='member_name']").blur(function(){
		var result = checkErrorName($(this).val());
		if(result) {
			Display_Tooltip('member_name', result);
		} else {
			Delete_Tooltip('member_name');
		}
		checkAccessInput();
	});

	//电子邮箱验证
	$("input[name='member_email_access']").keyup(function(){
		var result = checkErrorEmail($(this).val());
		if(!result) {
			Delete_Tooltip('member_email_access');
		}
		checkAccessInput();
	});
	$("input[name='member_email_access']").blur(function(){
		var result = checkErrorEmail($(this).val());
		if(result) {
			Display_Tooltip('member_email_access', result);
		} else {
			Delete_Tooltip('member_email_access');
		}
		checkAccessInput();
	});

	//密码验证
	$("input[name='member_password_access']").keyup(function(){
		var result = checkErrorPassword($(this).val());
		if(!result) {
			Delete_Tooltip('member_password_access');
		}
		checkAccessInput();
	});
	$("input[name='member_password_access']").blur(function(){
		var result = checkErrorPassword($(this).val());
		if(result) {
			Display_Tooltip('member_password_access', result);
		} else {
			Delete_Tooltip('member_password_access');
		}
		checkAccessInput();
	});

	//确认密码验证
	$("input[name='member_repassword']").keyup(function(){
		var result = checkErrorRePassword($(this).val(), $("input[name='member_password_access']").val());
		if(!result) {
			Delete_Tooltip('member_repassword');
		}
		checkAccessInput();
	});
	$("input[name='member_repassword']").blur(function(){
		var result = checkErrorRePassword($(this).val(), $("input[name='member_password_access']").val());
		if(result) {
			Display_Tooltip('member_repassword', result);
		} else {
			Delete_Tooltip('member_repassword');
		}
		checkAccessInput();
	});

	//联系电话验证
	$("input[name='member_tel']").keyup(function(){
		var result = checkErrorTel($(this).val());
		if(!result) {
			Delete_Tooltip('member_tel');
		}
		checkAccessInput();
	});
	$("input[name='member_tel']").blur(function(){
		var result = checkErrorTel($(this).val());
		if(result) {
			Display_Tooltip('member_tel', result);
		} else {
			Delete_Tooltip('member_tel');
		}
		checkAccessInput();
	});

	//微信号验证
	$("input[name='member_wechat']").keyup(function(){
		var result = checkErrorWechat($(this).val());
		if(!result) {
			Delete_Tooltip('member_wechat');
		}
		checkAccessInput();
	});
	$("input[name='member_wechat']").blur(function(){
		var result = checkErrorWechat($(this).val());
		if(result) {
			Display_Tooltip('member_wechat', result);
		} else {
			Delete_Tooltip('member_wechat');
		}
		checkAccessInput();
	});

	//QQ号验证
	$("input[name='member_qq']").keyup(function(){
		var result = checkErrorQQ($(this).val());
		if(!result) {
			Delete_Tooltip('member_qq');
		}
		checkAccessInput();
	});
	$("input[name='member_qq']").blur(function(){
		var result = checkErrorQQ($(this).val());
		if(result) {
			Display_Tooltip('member_qq', result);
		} else {
			Delete_Tooltip('member_qq');
		}
		checkAccessInput();
	});

	//注册按钮点击事件
	$('.btn-access').click(function(){
		if($(this).hasClass('active') && !$(this).hasClass('disabled')) {
			$(this).addClass('disabled');
			$('#form-access').submit();
		}
	});
});