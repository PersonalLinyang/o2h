$(function(){
	//点击删除选中景点按钮
	$('#btn-content-menu-delete').click(function(){
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-delete-checked').fadeIn();
	});
	
	//点击删除选中景点弹窗中的确定按钮
	$('#popup-delete-checked-yes').click(function(){
		$('#form-delete-checked').submit();
	});
	
	//点击删除选中景点弹窗中的取消按钮
	$('#popup-delete-checked-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-delete-checked').hide();
	});

	//点击表格中的删除按钮
	$('.btn-delete').click(function(){
		//获得删除数据信息
		var delete_value = $(this).data('value');
		var delete_name = $(this).data('name');
		
		//修改弹窗信息
		$('.popup-delete-name').html(delete_name);
		$('#input-id').val(delete_value);
		
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-delete').fadeIn();
	});
	
	//点击删除特定景点弹窗中的确定按钮
	$('#popup-delete-yes').click(function(){
		$('#form-delete').submit();
	});
	
	//点击删除特定景点弹窗中的取消按钮
	$('#popup-delete-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-delete').hide();
	});

	//点击筛选部分中的收费按钮
	$('#lbl-for-check-free-flag-0').click(function(){
		if($('#area-select-price').hasClass('active')) {
			$('#area-select-price').fadeOut();
			$('#area-select-price').removeClass('active');
		} else {
			$('#area-select-price').fadeIn();
			$('#area-select-price').addClass('active');
		}
	});
});