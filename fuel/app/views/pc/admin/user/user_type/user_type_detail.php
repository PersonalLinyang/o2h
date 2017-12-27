<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>用户类型详细信息 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/user/user_type/user_type_detail.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<?php if($edit_able_flag && $user_type['user_type_id'] != $user_type_self): ?>
				<li class="content-menu-button"><a href="/admin/modify_user_type/<?php echo $user_type['user_type_id']; ?>/">编辑用户类型</a></li>
				<?php endif; ?>
				<li class="content-menu-button"><a href="/admin/user_type_list/">返回用户类型一览</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>用户类型详细信息</h1>
			<table class="tb-content-form">
				<tr>
					<th>用户类型名称</th>
					<td><?php echo $user_type['user_type_name']; ?></td>
				</tr>
				<?php if($special_able_flag): ?>
				<tr>
					<th>特殊用户类型</th>
					<td><?php echo $special_level_list[$user_type['special_level']]; ?></td>
				</tr>
				<?php endif; ?>
				<tr>
					<th colspan="2">所持有的权限</th>
				</tr>
				<tr>
					<td colspan="2">
						<?php foreach($permission_list as $master_group_id => $master_group_info): ?>
						<?php if(in_array($master_group_id, $master_group)): ?>
						<div class="permission-area">
							<div class="permission-line"><?php echo $master_group_info['name']; ?></div>
							
							<?php foreach($master_group_info['sub_group_list'] as $sub_group_id => $sub_group_info): ?>
							<?php if(in_array($sub_group_id, $sub_group)): ?>
							<div class="permission-area">
								<div class="permission-line"><?php echo $sub_group_info['name']; ?></div>
								
								<?php foreach($sub_group_info['function_list'] as $function_id => $function_info) :?>
								<?php if(in_array($function_id, $function)): ?>
								<div class="permission-area">
									<div class="permission-line"><?php echo $function_info['name']; ?></div>
									
									<?php foreach($function_info['authority_list'] as $authority_id => $authority_info) :?>
									<?php if(in_array($authority_id, $authority)): ?>
									<div class="permission-area">
										<div class="permission-line"><?php echo $authority_info['name']; ?></div>
									</div>
									<?php endif; ?>
									<?php endforeach; /* authority */ ?>
									
								</div>
								<?php endif; ?>
								<?php endforeach; /* function */ ?>
								
							</div>
							<?php endif; ?>
							<?php endforeach; /* sub_group */ ?>
							
						</div>
						<?php endif; ?>
						<?php endforeach; /* master_group */ ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>
