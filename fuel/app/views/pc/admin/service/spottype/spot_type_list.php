<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>景点类别一览 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::css('pc/admin/service/spot_type/spot_type_list.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/spot_type/spot_type_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/add_spot_type/">添加景点类别</a></li>
				<li class="content-menu-button"><a href="/admin/spot_list/">景点一览</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<?php if(count($spot_type_list)): ?>
		<div class="content-main">
			<h1>景点类别一览</h1>
			<div class="div-content-list">
				<p>目前系统中共存在<span class="strong"><?php echo count($spot_type_list); ?></span>条景点类别信息</p>
				<table class="tb-content-list">
					<tr>
						<th class="th-delete"></th>
						<th class="th-modify"></th>
						<th class="th-name">景点类别名</th>
						<th class="th-number">景点数</th>
						<th class="th-list">关联景点</th>
					</tr>
					<?php foreach($spot_type_list as $spot_type): ?>
					<tr>
						<td><p class="btn-controller btn-delete" data-value="<?php echo $spot_type['spot_type_id']; ?>" data-name="<?php echo $spot_type['spot_type_name']; ?>">削除</p></td>
						<td><p class="btn-controller btn-modify"><a href="/admin/modify_spot_type/<?php echo $spot_type['spot_type_id']; ?>/">修改</a></p></td>
						<td><?php echo $spot_type['spot_type_name']; ?></td>
						<td><?php echo $spot_type['spot_count']; ?></td>
						<td><p class="btn-controller"><a href="/admin/spot_list/?select_spot_type%5B%5D=<?php echo $spot_type['spot_type_id']; ?>">景点一览</a></p></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
		<?php else: ?>
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，本系统尚未添加任何景点类别信息<br/>
				请在添加景点类别后确认本页
			</p>
		</div>
		<?php endif; ?>
		
		<div class="popup-shadow"></div>
		
		<div class="popup-delete popup">
			<div class="popup-title">景点类别确认</div>
			<div class="popup-content center">
				<p>景点类别一经删除将无法还原，<br/>确定要删除「景点类别-<span class="popup-delete-name"></span>」吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_spot_type/" method="post" id="form-delete">
					<input type="hidden" id="input-id" name="delete_id" value />
					<input type="hidden" name="page" value="spot_type_list" />
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
