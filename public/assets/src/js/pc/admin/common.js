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
			$('.btn-content-menu').removeClass('active');
			$('.content-menu-control-area').slideUp();
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

	//一览页批量导入按钮点击事件
	$('#btn-content-menu-import').click(function(){
		if($(this).hasClass('active')) {
			$(this).removeClass('active');
			$('#div-content-menu-import').slideUp();
		} else {
			$('.btn-content-menu').removeClass('active');
			$('.content-menu-control-area').slideUp();
			$(this).addClass('active');
			$('#div-content-menu-import').slideDown();
		}
	});
	//一览页批量导入取消按钮点击事件
	$('#btn-content-menu-import-cancel').click(function(){
		$('#btn-content-menu-import').removeClass('active');
		$('#div-content-menu-import').slideUp();
	});
	//一览页批量导入区域导入按钮点击事件
	$('#btn-content-menu-import-submit').click(function(){
		if(!$(this).hasClass('disabled')) {
			$(this).addClass('disabled');
			$(this).text('请稍候…');
			$('#form-content-menu-import').submit();
		}
	});
	//一览页批量导入区域上传按钮点击事件
	$('.file-content-menu').on('change', function() {
		var file = $(this).prop('files')[0];
		$(this).closest('.upload-area').find('.btn-upload').html(file.name);
	});

	//一览页导出景点列表按钮点击事件
	$('#btn-content-menu-export').click(function(){
		if($(this).hasClass('active')) {
			$(this).removeClass('active');
			$('#div-content-menu-export').slideUp();
		} else {
			$('.btn-content-menu').removeClass('active');
			$('.content-menu-control-area').slideUp();
			$(this).addClass('active');
			$('#div-content-menu-export').slideDown();
		}
	});
	//一览页导出区域取消按钮点击事件
	$('#btn-content-menu-export-cancel').click(function(){
		$('#btn-content-menu-export').removeClass('active');
		$('#div-content-menu-export').slideUp();
	});
	//一览页导出区域阅览模式导出按钮点击事件
	$('#btn-content-menu-export-review').click(function(){
		$('#hid-content-menu-export-model').val('review');
		$('#form-content-menu-export').submit();
	});
	//一览页导出区域备份模式导出按钮点击事件
	$('#btn-content-menu-export-backup').click(function(){
		$('#hid-content-menu-export-model').val('backup');
		$('#form-content-menu-export').submit();
	});
	
	//下拉选择框根据选中选项更改文字颜色
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