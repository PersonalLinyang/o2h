$(function(){
	//退出登陆
	$('#link-logout').click(function(){
		$('#logout-form').submit();
	});

	//顶端菜单按钮悬停事件
	$(".header-navi li").hover(function(){
		if($(this).hasClass("js-navi-sub")) {
			//有子菜单
			$(this).addClass("active-sub");
			$(this).find("div.header-sub-navi").stop().show();
		} else {
			//无子菜单
			$(this).addClass("active");
		}
	}, function(){
		if($(this).hasClass("js-navi-sub")) {
			//有子菜单
			$(this).find("div.header-sub-navi").stop().hide();
			$(this).removeClass("active-sub");
		} else {
			//无子菜单
			$(this).removeClass("active");
		}
	});
	
	//内容主体表单提交事件
	$(".btn-form-submit").click(function(){
		$(this).closest("form").submit();
	});
	
	//点击弹窗中背景
	$('.popup-shadow').click(function(){
		$('.popup-shadow').hide();
		$('.popup').hide();
	});
	
	//单选按钮点击事件
	$('.lbl-for-radio').click(function(){
		var name = $(this).attr('data-for');
		$('[data-for="' + name + '"]').removeClass("active");
		$(this).addClass('active');
	});
	
	//点选按钮点击事件
	$('.lbl-for-check').click(function(){
		if($(this).hasClass('active')) {
			$(this).removeClass('active');
		} else {
			$(this).addClass('active');
		}
	});

	//一览页筛选排序按钮点击事件
	$('#btn-content-menu-select').click(function(){
		if($(this).hasClass('active')) {
			$(this).removeClass('active');
			$('#div-content-menu-select').slideUp();
		} else {
			$(this).addClass('active');
			$('#div-content-menu-select').slideDown();
		}
	});
	//一览页筛选排序取消按钮点击事件
	$('#btn-content-menu-select-cancel').click(function(){
		$('#btn-content-menu-select').removeClass('active');
		$('#div-content-menu-select').slideUp();
	});
	//一览页筛选排序区域筛选排序按钮点击事件
	$('#btn-content-menu-select-submit').click(function(){
		$('#form-content-menu-select').submit();
	});
	
	// 項目が変更された時、条件によって色変更
	$('select').each(function(){
		if($(this).find('option:selected').hasClass('placeholder')) {
			$(this).css({'color': '#BDBDBD'});
		}
	});
	$('select').on('change', function(){
		if($(this).find('option:selected').hasClass('placeholder')) {
			$(this).css({'color': '#BDBDBD'});
		} else {
			$(this).css({'color': '#000000'});
		}
	});
});