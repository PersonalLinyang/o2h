<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>餐饮信息修改 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/service/edit_restaurant.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('common/jquery.uploadThumbs.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>餐饮信息修改</h1>
			<form method="post" action="" class="content-form" enctype="multipart/form-data">
				<table class="tb-content-form">
					<tr>
						<th>餐饮名</th>
						<td><input type="text" name="restaurant_name" value="<?php echo $input_restaurant_name; ?>" /></td>
					</tr>
					<tr>
						<th>餐饮所属地区</th>
						<td>
							<select name="restaurant_area">
								<option value=""></option>
								<?php foreach($area_list as $area): ?>
								<option value="<?php echo $area['area_id']; ?>"<?php echo $input_restaurant_area == $area['area_id'] ? ' selected' : ''; ?>>
									<?php echo $area['area_description']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>餐饮类别</th>
						<td>
							<select name="restaurant_type">
								<option value=""></option>
								<?php foreach($restaurant_type_list as $restaurant_type): ?>
								<option value="<?php echo $restaurant_type['restaurant_type_id']; ?>"<?php echo $input_restaurant_type == $restaurant_type['restaurant_type_id'] ? ' selected' : ''; ?>>
									<?php echo $restaurant_type['restaurant_type_name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>价格</th>
						<td>
							<input type="text" name="restaurant_price_min" class="price" value="<?php echo $input_restaurant_price_min; ?>" /> ～
							<input type="text" name="restaurant_price_max" class="price" value="<?php echo $input_restaurant_price_max; ?>" />
						</td>
					</tr>
					<tr>
						<th>公开状态</th>
						<td>
							<input type="radio" name="restaurant_status" value="0" id="restaurant-status-0" <?php echo $input_restaurant_status == '1' ? '' : 'checked '; ?>/>
							<label class="lbl-for-radio<?php echo $input_restaurant_status == '1' ? '' : ' active'; ?>" for="restaurant-status-0" data-for="chk-restaurant-status">未公开</label>
							<input type="radio" name="restaurant_status" value="1" id="restaurant-status-1" <?php echo $input_restaurant_status == '1' ? 'checked ' : ''; ?>/>
							<label class="lbl-for-radio<?php echo $input_restaurant_status == '1' ? ' active' : ''; ?>" for="restaurant-status-1" data-for="chk-restaurant-status">公开</label>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<ul class="button-group">
								<li class="button-yes btn-form-submit">保存</li>
								<li class="button-no"><a href="/admin/restaurant_detail/<?php echo $restaurant_id; ?>/">取消</a></li>
							</ul>
						</td>
					</tr>
				</table>
				<input type="hidden" name="page" value="modify_restaurant" />
			</form>
		</div>
	</div>
</body>
</html>
