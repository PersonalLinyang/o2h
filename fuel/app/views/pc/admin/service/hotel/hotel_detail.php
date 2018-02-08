<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $hotel_info['hotel_name']; ?> - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/hotel/hotel_detail.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<?php if($edit_able_flag): ?>
				<li class="content-menu-button"><a href="/admin/modify_hotel/<?php echo $hotel_info['hotel_id']; ?>/">信息修改</a></li>
				<?php if($hotel_info['hotel_status'] == '1'): ?>
				<li class="content-menu-button btn-hotel-status">设为未公开</li>
				<?php else: ?>
				<li class="content-menu-button btn-hotel-status">设为公开</li>
				<?php endif; //hotel_info['hotel_status'] ?>
				<?php endif; //edit_able_flag ?>
				<li class="content-menu-button"><a href="<?php echo $hotel_list_url; ?>">酒店一览</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>酒店信息 - <?php echo $hotel_info['hotel_name']; ?></h1>
			<table class="tb-content-detail">
				<tr>
					<th>酒店名</th>
					<td><?php echo $hotel_info['hotel_name']; ?></td>
				</tr>
				<tr>
					<th>酒店所属地区</th>
					<td><?php echo $hotel_info['hotel_area_description']; ?></td>
				</tr>
				<tr>
					<th>酒店类别</th>
					<td><?php echo $hotel_info['hotel_type_name']; ?></td>
				</tr>
				<tr>
					<th>价格</th>
					<td><?php echo $hotel_info['hotel_price'] ? ($hotel_info['hotel_price'] . '日元/人夜') : ''; ?></td>
				</tr>
				<tr>
					<th>公开状态</th>
					<td><?php echo $hotel_info['hotel_status'] == '1' ? '公开' : '未公开'; ?></td>
				</tr>
				<tr>
					<th>可选房型</th>
					<td><?php echo implode('、', $room_type_name_list); ?></td>
				</tr>
				<tr>
					<th>登录时间</th>
					<td><?php echo date('Y年m月d日　H:i:s', strtotime($hotel_info['created_at'])); ?></td>
				</tr>
				<tr>
					<th>最新修改时间</th>
					<td><?php echo date('Y年m月d日　H:i:s', strtotime($hotel_info['modified_at'])); ?></td>
				</tr>
			</table>
			<p class="system-comment">
				※ 本酒店由<?php echo $hotel_info['created_name']; ?>于<?php echo date('Y年m月d日H:i', strtotime($hotel_info['created_at'])); ?>登录
				<?php if($hotel_info['created_at'] != $hotel_info['modified_at']): ?>
				，<?php if($hotel_info['modified_name'] != $hotel_info['created_name']): ?>由<?php echo $hotel_info['modified_name']; ?><?php endif; ?>于<?php echo date('Y年m月d日H:i', strtotime($hotel_info['modified_at'])); ?>更新至当前状态
				<?php endif; ?>
			</p>
		</div>
		
		<?php if($edit_able_flag): ?>
		<div class="popup-shadow"></div>
		
		<?php if($hotel_info['hotel_status'] == '1'): ?>
		<div class="popup-hotel-status popup">
			<div class="popup-title">未公开酒店设置确认</div>
			<div class="popup-content center">
				<p>酒店设置为未公开酒店后普通用户将无法通过宣传系统查看本酒店的详细信息，<br/>确定要将酒店「<?php echo $hotel_info['hotel_name']; ?>」设置为未公开吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_hotel_status/" method="post" id="form-hotel-status">
					<input type="hidden" name="modify_value" value="0" />
					<input type="hidden" name="modify_id" value="<?php echo $hotel_info['hotel_id']; ?>" />
					<input type="hidden" name="page" value="hotel_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-hotel-status-yes">确定</li>
					<li class="button-no" id="popup-hotel-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php else: ?>
		<div class="popup-hotel-status popup">
			<div class="popup-title">公开酒店设置确认</div>
			<div class="popup-content center">
				<p>酒店设置为公开酒店后普通用户将可以通过宣传系统查看本酒店的详细信息，<br/>确定要将酒店「<?php echo $hotel_info['hotel_name']; ?>」设置为公开吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_hotel_status/" method="post" id="form-hotel-status">
					<input type="hidden" name="modify_value" value="1" />
					<input type="hidden" name="modify_id" value="<?php echo $hotel_info['hotel_id']; ?>" />
					<input type="hidden" name="page" value="hotel_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-hotel-status-yes">确定</li>
					<li class="button-no" id="popup-hotel-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php endif; //hotel_info['hotel_status'] ?>
		<?php endif; //edit_able_flag ?>
	</div>
</body>
</html>
