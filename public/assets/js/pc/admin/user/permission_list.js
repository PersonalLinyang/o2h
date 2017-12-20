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
			$('.popup-delete-text').html('删除主功能组的同时，将一同删除该功能组下属的所有功能组、功能、权限<br>并对于设定了相关功能组、功能、权限的用户的权限设定进行调整<br>主功能组一经删除将无法还原，确定要删除「主功能组-' + delete_name + '」吗？');
			$('#form-delete').attr('action', '/admin/delete_master_group/');
		} else if(delete_type == 'sg') {
			$('.popup-delete-type').html('副功能组');
			$('.popup-delete-text').html('删除副功能组的同时，将一同删除该功能组下属的所有功能、权限<br>并对于设定了相关功能组、功能、权限的用户的权限设定进行调整<br>副功能组一经删除将无法还原，确定要删除「副功能组-' + delete_name + '」吗？');
			$('#form-delete').attr('action', '/admin/delete_sub_group/');
		} else if(delete_type == 'f') {
			$('.popup-delete-type').html('功能');
			$('.popup-delete-text').html('删除功能的同时，将一同删除该功能下属的所有权限<br>并对于设定了相关功能、权限的用户的权限设定进行调整<br>功能一经删除将无法还原，确定要删除「功能-' + delete_name + '」吗？');
			$('#form-delete').attr('action', '/admin/delete_function/');
		} else if(delete_type == 'a') {
			$('.popup-delete-type').html('权限');
			$('.popup-delete-text').html('删除权限的同时，将对于设定了该权限的用户的权限设定进行调整<br>权限一经删除将无法还原，确定要删除「权限-' + delete_name + '」吗？');
			$('#form-delete').attr('action', '/admin/delete_authority/');
		}
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