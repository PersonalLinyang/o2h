<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $page_title; ?> - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/user/user_type/edit_user_type.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/user/user_type/edit_user_type.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1><?php echo $page_title; ?></h1>
			<form method="post" action="" class="content-form">
				<table class="tb-content-form">
					<tr>
						<th>用户类型名称</th>
						<td><input type="text" name="user_type_name" value="<?php echo $input_user_type_name; ?>" /></td>
					</tr>
					<?php if($special_able_flag): ?>
					<tr>
						<th>特殊用户类型</th>
						<td>
							<div class="radio-group">
								<input type="radio" name="special_level" value="1" id="rdo-special-level-1" <?php echo $input_special_level == '1' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_special_level == '1' ? ' active' : ''; ?>" for="rdo-special-level-1" data-for="rdo-special-level">是</label>
								<input type="radio" name="special_level" value="0" id="rdo-special-level-0" <?php echo $input_special_level == '1' ? '' : 'checked '; ?>/>
								<label class="lbl-for-radio<?php echo $input_special_level == '1' ? '' : ' active'; ?>" for="rdo-special-level-0" data-for="rdo-special-level">否</label>
							</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<th colspan="2">请选择该用户类型所持有的权限</th>
					</tr>
					<tr>
						<td colspan="2">
							<?php foreach($permission_list as $master_group_id => $master_group_info): ?>
							<div class="permission-area">
								<?php if(count($master_group_info['sub_group_list'])): ?>
								<p class="btn-permission-show"></p>
								<?php endif; ?>
								<div class="permission-line">
									<input type="checkbox" name="master_group[]" id="chk-master-group-<?php echo $master_group_id; ?>" value="<?php echo $master_group_id; ?>"
											<?php echo in_array($master_group_id, $input_master_group) ? ' checked' : '';?>/>
									<label for="chk-master-group-<?php echo $master_group_id; ?>" 
											class="lbl-for-check-permission<?php echo in_array($master_group_id, $input_master_group) ? ' active' : '';?>">
										<span<?php echo $master_group_info['special_flag'] ? ' class="strong"' : ''; ?>><?php echo $master_group_info['name']; ?></span>
									</label>
								</div>
								<?php foreach($master_group_info['sub_group_list'] as $sub_group_id => $sub_group_info): ?>
								<div class="permission-area">
									<?php if(count($sub_group_info['function_list'])): ?>
									<p class="btn-permission-show"></p>
									<?php endif; ?>
									<div class="permission-line">
										<input type="checkbox" name="sub_group[]" id="chk-sub-group-<?php echo $sub_group_id; ?>" value="<?php echo $sub_group_id; ?>"
												<?php echo in_array($sub_group_id, $input_sub_group) ? ' checked' : '';?>/>
										<label for="chk-sub-group-<?php echo $sub_group_id; ?>" 
												class="lbl-for-check-permission<?php echo in_array($sub_group_id, $input_sub_group) ? ' active' : '';?>">
											<span<?php echo $sub_group_info['special_flag'] ? ' class="strong"' : ''; ?>><?php echo $sub_group_info['name']; ?></span>
										</label>
									</div>
									<?php foreach($sub_group_info['function_list'] as $function_id => $function_info) :?>
									<div class="permission-area">
										<?php if(count($function_info['authority_list'])): ?>
										<p class="btn-permission-show"></p>
										<?php endif; ?>
										<div class="permission-line">
											<input type="checkbox" name="function[]" id="chk-function-<?php echo $function_id; ?>" value="<?php echo $function_id; ?>"
													<?php echo in_array($function_id, $input_function) ? ' checked' : '';?>/>
											<label for="chk-function-<?php echo $function_id; ?>" 
													class="lbl-for-check-permission<?php echo in_array($function_id, $input_function) ? ' active' : '';?>">
												<span<?php echo $function_info['special_flag'] ? ' class="strong"' : ''; ?>><?php echo $function_info['name']; ?></span>
											</label>
										</div>
										<?php foreach($function_info['authority_list'] as $authority_id => $authority_info) :?>
										<div class="permission-area">
											<div class="permission-line">
												<input type="checkbox" name="authority[]" id="chk-authority-<?php echo $authority_id; ?>" value="<?php echo $authority_id; ?>"
														<?php echo in_array($authority_id, $input_authority) ? ' checked' : '';?>/>
												<label for="chk-authority-<?php echo $authority_id; ?>" 
														class="lbl-for-check-permission<?php echo in_array($authority_id, $input_authority) ? ' active' : '';?>">
													<span<?php echo $authority_info['special_flag'] ? ' class="strong"' : ''; ?>><?php echo $authority_info['name']; ?></span>
												</label>
											</div>
										</div>
										<?php endforeach; /* authority */ ?>
									</div>
									<?php endforeach; /* function */ ?>
								</div>
								<?php endforeach; /* sub_group */ ?>
							</div>
							<?php endforeach; /* master_group */ ?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<ul class="button-group">
								<li class="button-yes btn-form-submit">保存</li>
								<li class="button-no"><a href="/admin/user_type_list/">取消</a></li>
							</ul>
						</td>
					</tr>
				</table>
				<input type="hidden" name="page" value="<?php echo $form_page_index; ?>" />
			</form>
		</div>
	</div>
</body>
</html>
