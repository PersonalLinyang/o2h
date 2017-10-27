<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>页面访问错误 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，您所访问的页面发生系统错误<br/>
				请勿修改页面表单中的控件名称<br/>
				并检查所访问的页面网址是否正确
			</p>
		</div>
	</div>
</body>
</html>
