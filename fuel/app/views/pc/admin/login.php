<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>登陆 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/login.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/login.js'); ?>
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
							<td><input type="text" name="user_email" id="user-email" value="<?php echo isset($user_email) ? $user_email : ''; ?>" /></td>
						</tr>
						<tr>
							<td>密码</td>
							<td><input type="password" name="user_password" id="user-password" /></td>
						</tr>
						<tr>
							<td colspan="2" class="td-submit">
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
