<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>酒店类别一览 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::css('pc/admin/service/hotel_type/hotel_type_list.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/hotel_type/hotel_type_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/add_hotel_type/">添加酒店类别</a></li>
				<li class="content-menu-button"><a href="<?php echo $hotel_list_url; ?>">酒店一览</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<?php if(count($hotel_type_list)): ?>
		<div class="content-main">
			<h1>酒店类别一览</h1>
			<div class="div-content-list">
				<p>目前系统中共存在<span class="strong"><?php echo count($hotel_type_list); ?></span>条酒店类别信息</p>
				<table class="tb-content-list">
					<tr>
						<th class="th-delete"></th>
						<th class="th-modify"></th>
						<th class="th-name">酒店类别名</th>
						<th class="th-number">酒店数</th>
						<th class="th-list">关联酒店</th>
					</tr>
					<?php foreach($hotel_type_list as $hotel_type): ?>
					<tr>
						<td><p class="btn-controller btn-delete" data-value="<?php echo $hotel_type['hotel_type_id']; ?>" data-name="<?php echo $hotel_type['hotel_type_name']; ?>">削除</p></td>
						<td><p class="btn-controller btn-modify"><a href="/admin/modify_hotel_type/<?php echo $hotel_type['hotel_type_id']; ?>/">修改</a></p></td>
						<td><?php echo $hotel_type['hotel_type_name']; ?></td>
						<td><?php echo $hotel_type['hotel_count']; ?></td>
						<td><p class="btn-controller"><a href="/admin/hotel_list/?select_hotel_type%5B%5D=<?php echo $hotel_type['hotel_type_id']; ?>">关联酒店</a></p></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
		<?php else: ?>
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，未能找到任何酒店类别信息<br/>
				请在添加酒店类别后确认本页
			</p>
		</div>
		<?php endif; ?>
		
		<div class="popup-shadow"></div>
		
		<div class="popup-delete popup">
			<div class="popup-title">酒店类别删除确认</div>
			<div class="popup-content center">
				<p>酒店类别一经删除将无法还原，<br/>确定要删除酒店类别-「<span class="popup-delete-name"></span>」吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_hotel_type/" method="post" id="form-delete">
					<input type="hidden" id="input-id" name="delete_id" value />
					<input type="hidden" name="page" value="hotel_type_list" />
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
