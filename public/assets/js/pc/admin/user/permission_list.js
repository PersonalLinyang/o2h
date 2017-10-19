$(function(){
	$('.btn-delete').click(function(){
		var delete_type = $(this).data('type');
		var delete_value = $(this).data('value');
		var delete_name = $(this).data('name');
		
		if(delete_type == 'mg') {
			$('#popup-delete-type').html('主功能组');
			$('#form-delete').attr('action', '/admin/delete_master_group/');
		} else if(delete_type == 'sg') {
			$('#popup-delete-type').html('副功能组');
			$('#form-delete').attr('action', '/admin/delete_sub_group/');
		} else if(delete_type == 'f') {
			$('#popup-delete-type').html('功能');
			$('#form-delete').attr('action', '/admin/delete_function/');
		} else if(delete_type == 'a') {
			$('#popup-delete-type').html('权限');
			$('#form-delete').attr('action', '/admin/delete_authority/');
		}
		
		$('#popup-delete-name').html(delete_name);
		$('#input-id').val(delete_value);
	});
	
	$('#popup-delete-yes').click(function(){
		$('#form-delete').submit();
	});
});