$(function(){
	//点击表格中的删除按钮
	$('.btn-delete').click(function(){
		//获得删除数据信息
		var delete_type = $(this).data('type');
		var delete_value = $(this).data('value');
		var delete_name = $(this).data('name');
		
		//修改弹窗信息
		if(delete_type == 'mg') {
			$('.popup-delete-type').html('主功能组');
			$('#form-delete').attr('action', '/admin/delete_master_group/');
		} else if(delete_type == 'sg') {
			$('.popup-delete-type').html('副功能组');
			$('#form-delete').attr('action', '/admin/delete_sub_group/');
		} else if(delete_type == 'f') {
			$('.popup-delete-type').html('功能');
			$('#form-delete').attr('action', '/admin/delete_function/');
		} else if(delete_type == 'a') {
			$('.popup-delete-type').html('权限');
			$('#form-delete').attr('action', '/admin/delete_authority/');
		}
		$('.popup-delete-name').html(delete_name);
		$('#input-id').val(delete_value);
		
		//显示弹窗
		$('.popup-shadow').show();
		$('.popup-delete').fadeIn();
	});
	
	//点击弹窗中的确定按钮
	$('#popup-delete-yes').click(function(){
		$('#form-delete').submit();
	});
	
	//点击弹窗中的取消按钮
	$('#popup-delete-no').click(function(){
		$('.popup-shadow').hide();
		$('.popup-delete').hide();
	});
});