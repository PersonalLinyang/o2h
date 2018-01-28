<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>修改餐饮类别名称 - O2H管理系统</title>
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
			<h1>修改餐饮类别名称</h1>
			<form method="post" action="" class="content-form">
				<table class="tb-content-form">
					<tr>
						<th>修改前餐饮类别名称</th>
						<td><?php echo $restaurant_type_name; ?></td>
					</tr>
					<tr>
						<th>修改后餐饮类别名称</th>
						<td><input type="text" name="name" value="<?php echo $input_restaurant_type_name; ?>" /></td>
					</tr>
					<tr>
						<td colspan="2">
							<ul class="button-group">
								<li class="button-yes btn-form-submit">保存</li>
								<li class="button-no"><a href="/admin/restaurant_type_list/">取消</a></li>
							</ul>
						</td>
					</tr>
				</table>
				<input type="hidden" name="page" value="modify_restaurant_type" />
			</form>
		</div>
	</div>
</body>
</html>
