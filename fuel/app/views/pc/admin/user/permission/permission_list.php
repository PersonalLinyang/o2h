<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>系统权限管理 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/user/permission/permission_list.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/user/permission/permission_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<?php if($edit_able_flag): ?>
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/add_master_group/">添加主组</a></li>
			</ul>
		</div>
		<?php endif; ?>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<?php if(count($permission_list)): ?>
		<div class="content-main">
			<h1>系统权限一览</h1>
			<p class="strong">系统权限一览</p>
			<?php foreach($permission_list as $master_group_id => $master_group_info): ?>
			<div class="permission-area">
				<?php if(count($master_group_info['sub_group_list'])): ?>
				<p class="btn-permission-show"></p>
				<?php endif; ?>
				<div class="permission-line">
					<p class="permission-id"><?php echo $master_group_id; ?></p>
					<p class="permission-name<?php echo $master_group_info['special_flag'] ? ' strong' : ''; ?>"><?php echo $master_group_info['name']; ?></p>
					<?php if($edit_able_flag): ?>
					<p class="btn-controller"><a href="/admin/add_sub_group/<?php echo $master_group_id; ?>/">添加副组</a></p>
					<p class="btn-controller"><a href="/admin/modify_master_group/<?php echo $master_group_id; ?>/">修改名称</a></p>
					<?php endif; ?>
					<?php if($delete_able_flag): ?>
					<p class="btn-controller btn-delete" data-type="mg" data-value="<?php echo $master_group_id; ?>" data-name="<?php echo $master_group_info['name']; ?>">削除主组</p>
					<?php endif; ?>
				</div>
				<?php foreach($master_group_info['sub_group_list'] as $sub_group_id => $sub_group_info): ?>
				<div class="permission-area">
					<?php if(count($sub_group_info['function_list'])): ?>
					<p class="btn-permission-show"></p>
					<?php endif; ?>
					<div class="permission-line">
						<p class="permission-id"><?php echo $master_group_id . '-' . $sub_group_id; ?></p>
						<p class="permission-name<?php echo $sub_group_info['special_flag'] ? ' strong' : ''; ?>"><?php echo $sub_group_info['name']; ?></p>
						<?php if($edit_able_flag): ?>
						<p class="btn-controller"><a href="/admin/add_function/<?php echo $sub_group_id; ?>/">添加功能</a></p>
						<p class="btn-controller"><a href="/admin/modify_sub_group/<?php echo $sub_group_id; ?>/">修改名称</a></p>
						<?php endif; ?>
						<?php if($delete_able_flag): ?>
						<p class="btn-controller btn-delete" data-type="sg" data-value="<?php echo $sub_group_id; ?>" data-name="<?php echo $sub_group_info['name']; ?>">削除副组</p>
						<?php endif; ?>
					</div>
					<?php foreach($sub_group_info['function_list'] as $function_id => $function_info) :?>
					<div class="permission-area">
						<?php if(count($function_info['authority_list'])): ?>
						<p class="btn-permission-show"></p>
						<?php endif; ?>
						<div class="permission-line">
							<p class="permission-id"><?php echo $master_group_id . '-' . $sub_group_id . '-' . $function_id; ?></p>
							<p class="permission-name<?php echo $function_info['special_flag'] ? ' strong' : ''; ?>"><?php echo $function_info['name']; ?></p>
							<?php if($edit_able_flag): ?>
							<p class="btn-controller"><a href="/admin/add_authority/<?php echo $function_id; ?>/">添加权限</a></p>
							<p class="btn-controller"><a href="/admin/modify_function/<?php echo $function_id; ?>/">修改名称</a></p>
							<?php endif; ?>
							<?php if($delete_able_flag): ?>
							<p class="btn-controller btn-delete" data-type="f" data-value="<?php echo $function_id; ?>" data-name="<?php echo $function_info['name']; ?>">削除功能</p>
							<?php endif; ?>
						</div>
						<?php foreach($function_info['authority_list'] as $authority_id => $authority_info) :?>
						<div class="permission-area">
							<div class="permission-line">
								<p class="permission-id"><?php echo $master_group_id . '-' . $sub_group_id . '-' . $function_id . '-' . $authority_id; ?></p>
								<p class="permission-name<?php echo $authority_info['special_flag'] ? ' strong' : ''; ?>"><?php echo $authority_info['name']; ?></p>
								<?php if($edit_able_flag): ?>
								<p class="btn-controller"><a href="/admin/modify_authority/<?php echo $authority_id; ?>/">修改名称</a></p>
								<?php endif; ?>
								<?php if($delete_able_flag): ?>
								<p class="btn-controller btn-delete" data-type="a" data-value="<?php echo $authority_id; ?>" data-name="<?php echo $authority_info['name']; ?>">削除权限</p>
								<?php endif; ?>
							</div>
						</div>
						<?php endforeach; /* authority */ ?>
					</div>
					<?php endforeach; /* function */ ?>
				</div>
				<?php endforeach; /* sub_group */ ?>
			</div>
			<?php endforeach; /* master_group */ ?>
		</div>
		<?php else: ?>
		<div class="content-main"><p class="strong">目前尚未设定任何权限，请添加主权限组</p></div>
		<?php endif; ?>
		
		<?php if($delete_able_flag): ?>
		<div class="popup-shadow"></div>
		<div class="popup-delete popup">
			<div class="popup-title"><span class="popup-delete-type"></span>删除确认</div>
			<div class="popup-content center">
				<p class="popup-delete-text"></p>
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
		<?php endif; ?>
	</div>
</body>
</html>
