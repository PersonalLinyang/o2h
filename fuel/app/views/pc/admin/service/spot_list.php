<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>景点管理 - O2H管理系统</title>
	<?php //echo Asset::css('pc/admin/common.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
</head>
<body class="body-common">
	<?php //echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/add_spot/">添加景点</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<!--<h1>景点一览</h1>-->
		</div>
		
		<div class="popup-shadow"></div>
		<!--
		<div class="popup-delete popup">
			<div class="popup-title">删除<span class="popup-delete-type"></span>确认</div>
			<div class="popup-content center">
				<p><span class="popup-delete-type"></span>一经删除将无法还原，<br/>确定要删除「<span class="popup-delete-type"></span>-<span class="popup-delete-name"></span>」吗？</p>
			</div>
			<div class="popup-controller">
				<form action method="post" id="form-delete">
					<input type="hidden" id="input-id" name="delete_id" value />
					<input type="hidden" name="page" value="permission_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-delete-yes">确定</li>
					<li class="button-no" id="popup-delete-no">取消</li>
				</ul>
			</div>
		</div>
		-->
	</div>
</body>
</html>
