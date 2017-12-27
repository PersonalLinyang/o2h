<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>用户类型一览 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::css('pc/admin/user/user_type/user_type_list.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/user/user_type/user_type_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<?php if($edit_able_flag): ?>
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/add_user_type/">添加用户类型</a></li>
			</ul>
		</div>
		<?php endif; ?>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<?php if(count($user_type_list)): ?>
		<div class="content-main">
			<h1>用户类型一览</h1>
			<div class="div-content-list">
				<p>共找到<span class="strong"><?php echo count($user_type_list); ?></span>条用户类型信息</p>
				<table class="tb-content-list">
					<tr>
						<?php if($delete_able_flag): ?>
						<th class="th-delete"></th>
						<?php endif; ?>
						<?php if($edit_able_flag): ?>
						<th class="th-modify"></th>
						<?php endif; ?>
						<th class="th-name">用户类型名</th>
						<?php if($special_able_flag): ?>
						<th class="th-level">类型等级</th>
						<?php endif; ?>
						<th class="th-number">用户数</th>
					</tr>
					<?php foreach($user_type_list as $user_type): ?>
					<tr>
						<?php if($delete_able_flag): ?>
						<td>
							<?php if($user_type['user_type_id'] != $user_type_self): ?>
							<p class="btn-controller btn-delete" data-value="<?php echo $user_type['user_type_id']; ?>" data-name="<?php echo $user_type['user_type_name']; ?>">削除</p>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<?php if($edit_able_flag): ?>
						<td>
							<?php if($user_type['user_type_id'] != $user_type_self): ?>
							<p class="btn-controller"><a href="/admin/modify_user_type/<?php echo $user_type['user_type_id']; ?>/">编辑</a></p>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<td><a href="/admin/user_type_detail/<?php echo $user_type['user_type_id']; ?>/"><?php echo $user_type['user_type_name']; ?></a></td>
						<?php if($special_able_flag): ?>
						<td><?php echo $special_level_list[$user_type['special_level']]; ?></td>
						<?php endif; ?>
						<td><a href="/admin/user_list/?select_user_type%5B%5D=<?php echo $user_type['user_type_id']; ?>"><?php echo $user_type['user_count']; ?>人</a></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
		<?php else: ?>
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，未能找到任何用户类型信息
			</p>
		</div>
		<?php endif; ?>
		
		<?php if($delete_able_flag): ?>
		<div class="popup-shadow"></div>
		<div class="popup-delete popup">
			<div class="popup-title">用户类型删除确认</div>
			<div class="popup-content center">
				<p>用户类型一经删除将无法还原，<br/>当用户类型被删除时，属于该用户类型的用户将被设置为未设定类型，<br/>确定要删除「用户类型-<span class="popup-delete-name"></span>」吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_user_type/" method="post" id="form-delete">
					<input type="hidden" id="input-id" name="delete_id" value />
					<input type="hidden" name="page" value="user_type_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-delete-yes">确定</li>
					<li class="button-no" id="popup-delete-no">取消</li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	</div>
</body>
</html>
