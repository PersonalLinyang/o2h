<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>登陆 - O2H管理系统</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<?php echo Asset::css('sp/admin/common.css'); ?>
	<?php echo Asset::css('sp/admin/login.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('sp/admin/login.js'); ?>
</head>
<body class="body-common">
	<div class="area-login">
		<div class="login-head">O2H企业情报管理系统</div>
		<div class="login-body">
			<form id="login-form" action="" method="post">
				<div class="error-message" id="error-message"><?php echo isset($error_message) ? $error_message : ''; ?></div>
				<div class="form-table">
					<table>
						<tr>
							<td>邮箱</td>
						</tr>
						<tr>
							<td><input type="text" name="user_email" id="user-email" value="<?php echo isset($user_email) ? $user_email : ''; ?>" /></td>
						</tr>
						<tr>
							<td>密码</td>
						</tr>
						<tr>
							<td><input type="password" name="user_password" id="user-password" /></td>
						</tr>
						<tr>
							<td class="td-submit">
								<p class="login-button" id="btn-login">登陆</p>
								<p class="login-button" id="btn-password">找回密码</p>
							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	<div>
</body>
</html>
