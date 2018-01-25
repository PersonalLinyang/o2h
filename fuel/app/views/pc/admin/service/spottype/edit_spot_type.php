<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $page_title; ?> - O2H管理系统</title>
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
			<h1><?php echo $page_title; ?></h1>
			<form method="post" action="" class="content-form">
				<table class="tb-content-form">
					<tr>
						<th>景点类别名称</th>
						<td><input type="text" name="spot_type_name" value="<?php echo $input_spot_type_name; ?>" /></td>
					</tr>
					<tr>
						<td colspan="2">
							<ul class="button-group">
								<li class="button-yes btn-form-submit">保存</li>
								<li class="button-no"><a href="/admin/spot_type_list/">取消</a></li>
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
