<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>添加酒店 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
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
			<h1>添加酒店</h1>
			<form method="post" action="" class="content-form" enctype="multipart/form-data">
				<table class="tb-content-form">
					<tr>
						<th>酒店名</th>
						<td><input type="text" name="hotel_name" value="<?php echo $input_hotel_name; ?>" /></td>
					</tr>
					<tr>
						<th>酒店所属地区</th>
						<td>
							<select name="hotel_area">
								<option value=""></option>
								<?php foreach($area_list as $area): ?>
								<option value="<?php echo $area['area_id']; ?>"<?php echo $input_hotel_area == $area['area_id'] ? ' selected' : ''; ?>>
									<?php echo $area['area_description']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>酒店类别</th>
						<td>
							<select name="hotel_type">
								<option value=""></option>
								<?php foreach($hotel_type_list as $hotel_type): ?>
								<option value="<?php echo $hotel_type['hotel_type_id']; ?>"<?php echo $input_hotel_type == $hotel_type['hotel_type_id'] ? ' selected' : ''; ?>>
									<?php echo $hotel_type['hotel_type_name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>价格</th>
						<td><input type="text" name="hotel_price" value="<?php echo $input_hotel_price; ?>" /></td>
					</tr>
					<tr>
						<th>公开状态</th>
						<td>
							<input type="radio" name="hotel_status" value="0" id="hotel-status-0" <?php echo $input_hotel_status == '1' ? '' : 'checked '; ?>/>
							<label class="lbl-for-radio<?php echo $input_hotel_status == '1' ? '' : ' active'; ?>" for="hotel-status-0" data-for="chk-hotel-status">未公开</label>
							<input type="radio" name="hotel_status" value="1" id="hotel-status-1" <?php echo $input_hotel_status == '1' ? 'checked ' : ''; ?>/>
							<label class="lbl-for-radio<?php echo $input_hotel_status == '1' ? ' active' : ''; ?>" for="hotel-status-1" data-for="chk-hotel-status">公开</label>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<ul class="button-group">
								<li class="button-yes btn-form-submit">添加</li>
								<li class="button-no"><a href="/admin/hotel_list/">取消</a></li>
							</ul>
						</td>
					</tr>
				</table>
				<input type="hidden" name="page" value="add_hotel" />
			</form>
		</div>
	</div>
</body>
</html>
