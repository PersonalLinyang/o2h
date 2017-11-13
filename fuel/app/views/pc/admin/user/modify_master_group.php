<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>修改主功能组名称 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>修改主功能组名称</h1>
			<form method="post" action="" class="content-form">
				<table class="tb-content-form">
					<tr>
						<th>修改前主功能组名称</th>
						<td><?php echo $master_group_name; ?></td>
					</tr>
					<tr>
						<th>修改后主功能组名称</th>
						<td><input type="text" name="name" /></td>
					</tr>
					<tr>
						<td colspan="2">
							<ul class="button-group">
								<li class="button-yes btn-form-submit">保存</li>
								<li class="button-no"><a href="/admin/permission_list/">返回</a></li>
							</ul>
						</td>
					</tr>
				</table>
				<input type="hidden" name="page" value="modify_mg" />
			</form>
		</div>
	</div>
</body>
</html>
