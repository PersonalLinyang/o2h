<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $restaurant_info['restaurant_name']; ?> - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/restaurant_detail.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/modify_restaurant/<?php echo $restaurant_info['restaurant_id']; ?>/">信息修改</a></li>
				<?php if($restaurant_info['restaurant_status'] == '1'): ?>
				<li class="content-menu-button btn-restaurant-status">设为未公开</li>
				<?php else: ?>
				<li class="content-menu-button btn-restaurant-status">设为公开</li>
				<?php endif; ?>
				<li class="content-menu-button"><a href="/admin/restaurant_list/">餐饮一览</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>餐饮信息 - <?php echo $restaurant_info['restaurant_name']; ?></h1>
			<table class="tb-content-detail">
				<tr>
					<th>餐饮名</th>
					<td><?php echo $restaurant_info['restaurant_name']; ?></td>
				</tr>
				<tr>
					<th>餐饮所属地区</th>
					<td><?php echo $restaurant_info['restaurant_area_description']; ?></td>
				</tr>
				<tr>
					<th>餐饮类别</th>
					<td><?php echo $restaurant_info['restaurant_type_name']; ?></td>
				</tr>
				<tr>
					<th>价格</th>
					<td><?php echo ($restaurant_info['restaurant_price_min'] || $restaurant_info['restaurant_price_max']) ? ($restaurant_info['restaurant_price_min'] . '～' . $restaurant_info['restaurant_price_max'] . '元/人') : ''; ?></td>
				</tr>
				<tr>
					<th>公开状态</th>
					<td><?php echo $restaurant_info['restaurant_status'] == '1' ? '公开' : '未公开'; ?></td>
				</tr>
				<tr>
					<th>登录时间</th>
					<td><?php echo date('Y年m月d日　H:i:s', strtotime($restaurant_info['created_at'])); ?></td>
				</tr>
				<tr>
					<th>最新修改时间</th>
					<td><?php echo date('Y年m月d日　H:i:s', strtotime($restaurant_info['modified_at'])); ?></td>
				</tr>
			</table>
		</div>
		
		<div class="popup-shadow"></div>
		
		<?php if($restaurant_info['restaurant_status'] == '1'): ?>
		<div class="popup-restaurant-status popup">
			<div class="popup-title">未公开餐饮设置确认</div>
			<div class="popup-content center">
				<p>餐饮设置为未公开餐饮后普通用户将无法通过宣传系统查看本餐饮的详细信息，<br/>确定要将餐饮「<?php echo $restaurant_info['restaurant_name']; ?>」设置为未公开吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_restaurant_status/" method="post" id="form-restaurant-status">
					<input type="hidden" name="modify_value" value="protected" />
					<input type="hidden" name="modify_id" value="<?php echo $restaurant_info['restaurant_id']; ?>" />
					<input type="hidden" name="page" value="restaurant_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-restaurant-status-yes">确定</li>
					<li class="button-no" id="popup-restaurant-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php else: ?>
		<div class="popup-restaurant-status popup">
			<div class="popup-title">公开餐饮设置确认</div>
			<div class="popup-content center">
				<p>餐饮设置为公开餐饮后普通用户将可以通过宣传系统查看本餐饮的详细信息，<br/>确定要将餐饮「<?php echo $restaurant_info['restaurant_name']; ?>」设置为公开吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_restaurant_status/" method="post" id="form-restaurant-status">
					<input type="hidden" name="modify_value" value="publish" />
					<input type="hidden" name="modify_id" value="<?php echo $restaurant_info['restaurant_id']; ?>" />
					<input type="hidden" name="page" value="restaurant_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-restaurant-status-yes">确定</li>
					<li class="button-no" id="popup-restaurant-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	</div>
</body>
</html>
