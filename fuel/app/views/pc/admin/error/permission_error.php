<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>访问权限错误 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，您不具备权限访问此页面<br/>
				请在权限管理员为您添加权限后重新登陆<br/>
				或继续访问其他页面
			</p>
		</div>
	</div>
</body>
</html>
