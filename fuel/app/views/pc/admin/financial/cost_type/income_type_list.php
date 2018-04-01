<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>支出项目一览 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::css('pc/admin/financial/cost_type/cost_type_list.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/financial/cost_type/cost_type_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/add_cost_type/">添加支出项目</a></li>
				<li class="content-menu-button"><a href="<?php echo $cost_list_url; ?>">支出管理</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<?php if(count($cost_type_list)): ?>
		<div class="content-main">
			<h1>支出项目一览</h1>
			<div class="div-content-list">
				<p>目前系统中共存在<span class="strong"><?php echo count($cost_type_list); ?></span>条支出项目信息</p>
				<table class="tb-content-list">
					<tr>
						<th class="th-delete"></th>
						<th class="th-modify"></th>
						<th class="th-name">支出项目名</th>
					</tr>
					<?php foreach($cost_type_list as $cost_type): ?>
					<tr>
						<td><p class="btn-controller btn-delete" data-value="<?php echo $cost_type['cost_type_id']; ?>" data-name="<?php echo $cost_type['cost_type_name']; ?>">削除</p></td>
						<td><p class="btn-controller btn-modify"><a href="/admin/modify_cost_type/<?php echo $cost_type['cost_type_id']; ?>/">修改</a></p></td>
						<td><?php echo $cost_type['cost_type_name']; ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
		<?php else: ?>
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，未能找到任何支出项目信息<br/>
				请在添加支出项目后确认本页
			</p>
		</div>
		<?php endif; ?>
		
		<div class="popup-shadow"></div>
		
		<div class="popup-delete popup">
			<div class="popup-title">支出项目删除确认</div>
			<div class="popup-content center">
				<p>支出项目一经删除将无法还原，<br/>确定要删除支出项目-「<span class="popup-delete-name"></span>」吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_cost_type/" method="post" id="form-delete">
					<input type="hidden" id="input-id" name="delete_id" value />
					<input type="hidden" name="page" value="cost_type_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-delete-yes">确定</li>
					<li class="button-no" id="popup-delete-no">取消</li>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>
