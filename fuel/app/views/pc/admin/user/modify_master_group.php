<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>权限管理 - O2H管理系统</title>
	<?php //echo Asset::css('pc/admin/common.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
</head>
<body class="body-common">
	<?php //echo $header; ?>
	<div class="content-area">
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<div>
				<form method="post" action="">
					<?php echo $master_group_name; ?>
					<input type="text" name="name" />
					<input type="hidden" name="page" value="modify_mg" />
					<input type="submit" name="submit" value="変更" />
				</form>
			</div>
		</div>
	</div>
</body>
</html>
