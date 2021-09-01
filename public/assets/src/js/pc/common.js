$(function(){
	//MENU按钮点击事件
	$('.btn-header').click(function(){
		if($(this).hasClass('active') && !$(this).hasClass('disabled')) {
			$(this).removeClass('active');
			$(this).addClass('disabled');
			$('.btn-menu-company').animate({top:'-482px', marginLeft:'-31px', opacity:'0'}, 600);
			setTimeout(function(){$('.btn-menu-member').animate({top:'-420px', marginLeft:'-31px', opacity:'0'}, 600)}, 100);
			setTimeout(function(){$('.btn-menu-entry').animate({top:'-358px', marginLeft:'-31px', opacity:'0'}, 600)}, 200);
			setTimeout(function(){$('.btn-menu-feature').animate({top:'-296px', marginLeft:'-31px', opacity:'0'}, 600)}, 300);
			setTimeout(function(){$('.btn-menu-route').animate({top:'-234px', marginLeft:'-31px', opacity:'0'}, 600)}, 400);
			setTimeout(function(){$('.btn-menu-home').animate({top:'-172px', marginLeft:'-31px', opacity:'0'}, 600)}, 500);
			setTimeout(function(){$('.btn-header').removeClass('disabled')}, 600);
		} else if(!$(this).hasClass('disabled')) {
			$(this).addClass('active');
			$(this).addClass('disabled');
			$('.btn-menu-home').animate({top:'-128px', marginLeft:'-195px', opacity:'1'}, 600);
			setTimeout(function(){$('.btn-menu-route').animate({top:'-114px', marginLeft:'-151px', opacity:'1'}, 600)}, 100);
			setTimeout(function(){$('.btn-menu-feature').animate({top:'-132px', marginLeft:'-75px', opacity:'1'}, 600)}, 200);
			setTimeout(function(){$('.btn-menu-entry').animate({top:'-194px', marginLeft:'13px', opacity:'1'}, 600)}, 300);
			setTimeout(function(){$('.btn-menu-member').animate({top:'-300px', marginLeft:'89px', opacity:'1'}, 600)}, 400);
			setTimeout(function(){$('.btn-menu-company').animate({top:'-438px', marginLeft:'133px', opacity:'1'}, 600)}, 500);
			setTimeout(function(){$('.btn-header').removeClass('disabled')}, 600);
		}
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

	//标签点击事件
	$('.lbl-for-tab').click(function(){
		var name = $(this).attr('data-for');
		var index = $(this).attr('data-index');
		$('[data-for="' + name + '"]').removeClass("active");
		$('[data-index="' + index + '"]').addClass('active');
	});

	//数字按钮点击事件
	$('.minus-for-number').click(function(){
		var name = $(this).attr('data-for');
		var value_now = $('#' + name).val();
		if(!value_now) {
			value_now = 0;
		}
		if(value_now >= 1) {
			value_now = parseInt(value_now) - 1;
		}
		$('#' + name).val(value_now);
	});

	//数字按钮点击事件
	$('.plus-for-number').click(function(){
		var name = $(this).attr('data-for');
		var value_now = $('#' + name).val();
		if(!value_now) {
			value_now = 0;
		}
		value_now = parseInt(value_now) + 1;
		$('#' + name).val(value_now);
	});
});