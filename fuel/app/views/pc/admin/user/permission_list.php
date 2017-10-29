<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>系统权限管理 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/user/permission_list.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/user/permission_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/add_master_group/">添加主组</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<?php if(count($permission_list)): ?>
		<div class="content-main">
			<h1>系统权限一览</h1>
			<?php foreach($permission_list as $master_group_id => $master_group_info): ?>
			<div class="permission-line permission-mastergroup" >
				<p class="btn-controller"><a href="/admin/add_sub_group/?master_group_id=<?php echo $master_group_id; ?>">添加副组</a></p>
				<p class="btn-controller btn-delete" data-type="mg" data-value="<?php echo $master_group_id; ?>" data-name="<?php echo $master_group_info['name']; ?>">削除主组</p>
				<p class="btn-controller"><a href="/admin/modify_master_group/?master_group_id=<?php echo $master_group_id; ?>">修改名称</a></p>
				<p class="permission-id"><?php echo $master_group_id; ?></p>
				<p class="permission-name"><?php echo $master_group_info['name']; ?></p>
			</div>
			<?php foreach($master_group_info['sub_group_list'] as $sub_group_id => $sub_group_info) :?>
			<div class="permission-line permission-subgroup">
				<p class="btn-controller"><a href="/admin/add_function/?sub_group_id=<?php echo $sub_group_id; ?>">添加功能</a></p>
				<p class="btn-controller btn-delete" data-type="sg" data-value="<?php echo $sub_group_id; ?>" data-name="<?php echo $sub_group_info['name']; ?>">削除副组</p>
				<p class="btn-controller"><a href="/admin/modify_sub_group/?sub_group_id=<?php echo $sub_group_id; ?>">修改名称</a></p>
				<p class="permission-id"><?php echo $master_group_id . '-' . $sub_group_id; ?></p>
				<p class="permission-name"><?php echo $sub_group_info['name']; ?></p>
			</div>
			<?php foreach($sub_group_info['function_list'] as $function_id => $function_info) :?>
			<div class="permission-line permission-function">
				<p class="btn-controller"><a href="/admin/add_authority/?function_id=<?php echo $function_id; ?>">添加权限</a></p>
				<p class="btn-controller btn-delete" data-type="f" data-value="<?php echo $function_id; ?>" data-name="<?php echo $function_info['name']; ?>">削除功能</p>
				<p class="btn-controller"><a href="/admin/modify_function/?function_id=<?php echo $function_id; ?>">修改名称</a></p>
				<p class="permission-id"><?php echo $master_group_id . '-' . $sub_group_id . '-' . $function_id; ?></p>
				<p class="permission-name"><?php echo $function_info['name']; ?></p>
			</div>
			<?php foreach($function_info['authority_list'] as $authority_id => $authority_info) :?>
			<div class="permission-line permission-authority">
				<p class="btn-controller btn-delete" data-type="a" data-value="<?php echo $authority_id; ?>" data-name="<?php echo $authority_info['name']; ?>">削除权限</p>
				<p class="btn-controller"><a href="/admin/modify_authority/?authority_id=<?php echo $authority_id; ?>">修改名称</a></p>
				<p class="permission-id"><?php echo $master_group_id . '-' . $sub_group_id . '-' . $function_id . '-' . $authority_id; ?></p>
				<p class="permission-name"><?php echo $authority_info['name']; ?></p>
			</div>
			<?php endforeach; /* authority */ ?>
			<?php endforeach; /* function */ ?>
			<?php endforeach; /* sub_group */ ?>
			<?php endforeach; /* master_group */ ?>
		</div>
		<?php else: ?>
		<div class="content-main"><p class="strong">目前尚未设定任何权限，请添加主权限组</p></div>
		<?php endif; ?>
		
		<div class="popup-shadow"></div>
		
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
	</div>
</body>
</html>
