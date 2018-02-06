<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>餐饮店类别一览 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::css('pc/admin/service/restaurant_type/restaurant_type_list.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/restaurant_type/restaurant_type_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/add_restaurant_type/">添加餐饮店类别</a></li>
				<li class="content-menu-button"><a href="<?php echo $restaurant_list_url; ?>">餐饮店一览</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<?php if(count($restaurant_type_list)): ?>
		<div class="content-main">
			<h1>餐饮店类别一览</h1>
			<div class="div-content-list">
				<p>目前系统中共存在<span class="strong"><?php echo count($restaurant_type_list); ?></span>条餐饮类别信息</p>
				<table class="tb-content-list">
					<tr>
						<th class="th-delete"></th>
						<th class="th-modify"></th>
						<th class="th-name">餐饮店类别名</th>
						<th class="th-number">餐饮店数</th>
						<th class="th-list">关联餐饮店</th>
					</tr>
					<?php foreach($restaurant_type_list as $restaurant_type): ?>
					<tr>
						<td><p class="btn-controller btn-delete" data-value="<?php echo $restaurant_type['restaurant_type_id']; ?>" data-name="<?php echo $restaurant_type['restaurant_type_name']; ?>">削除</p></td>
						<td><p class="btn-controller btn-modify"><a href="/admin/modify_restaurant_type/<?php echo $restaurant_type['restaurant_type_id']; ?>/">修改</a></p></td>
						<td><?php echo $restaurant_type['restaurant_type_name']; ?></td>
						<td><?php echo $restaurant_type['restaurant_count']; ?></td>
						<td><p class="btn-controller"><a href="/admin/restaurant_list/?select_restaurant_type%5B%5D=<?php echo $restaurant_type['restaurant_type_id']; ?>">关联餐饮店</a></p></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
		<?php else: ?>
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，未能找到任何餐饮店类别信息<br/>
				请在添加餐饮店类别后确认本页
			</p>
		</div>
		<?php endif; ?>
		
		<div class="popup-shadow"></div>
		
		<div class="popup-delete popup">
			<div class="popup-title">餐饮店类别删除确认</div>
			<div class="popup-content center">
				<p>餐饮店类别一经删除将无法还原，<br/>确定要删除餐饮店类别-「<span class="popup-delete-name"></span>」吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_restaurant_type/" method="post" id="form-delete">
					<input type="hidden" id="input-id" name="delete_id" value />
					<input type="hidden" name="page" value="restaurant_type_list" />
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
