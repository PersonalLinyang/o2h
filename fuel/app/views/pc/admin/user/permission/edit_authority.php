<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $page_title; ?> - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
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
						<th>所属主功能组</th>
						<td><?php echo $master_group_name; ?></td>
					</tr>
					<tr>
						<th>所属副功能组</th>
						<td><?php echo $sub_group_name; ?></td>
					</tr>
					<tr>
						<th>所属功能</th>
						<td><?php echo $function_name; ?></td>
					</tr>
					<tr>
						<th>权限名称</th>
						<td><input type="text" name="authority_name" value="<?php echo $input_authority_name; ?>" /></td>
					</tr>
					<?php if(!$function_special_flag) : ?>
					<tr>
						<th>特殊权限</th>
						<td>
							<div class="radio-group">
								<input type="radio" name="special_flag" value="1" id="rdo-special-flag-1" <?php echo $input_special_flag == '1' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_special_flag == '1' ? ' active' : ''; ?>" for="rdo-special-flag-1" data-for="rdo-special-flag">是</label>
								<input type="radio" name="special_flag" value="0" id="rdo-special-flag-0" <?php echo $input_special_flag == '1' ? '' : 'checked '; ?>/>
								<label class="lbl-for-radio<?php echo $input_special_flag == '1' ? '' : ' active'; ?>" for="rdo-special-flag-0" data-for="rdo-special-flag">否</label>
							</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td colspan="2">
							<ul class="button-group">
								<li class="button-yes btn-form-submit">保存</li>
								<li class="button-no"><a href="/admin/permission_list/">返回</a></li>
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
