<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $route_info['route_name']; ?> - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/route_detail.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/modify_route/<?php echo $route_info['route_id']; ?>/">信息修改</a></li>
				<?php if($route_info['route_status'] == '1'): ?>
				<li class="content-menu-button btn-route-status">设为未公开</li>
				<?php else: ?>
				<li class="content-menu-button btn-route-status">设为公开</li>
				<?php endif; ?>
				<li class="content-menu-button"><a href="/admin/route_list/">路线一览</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>路线信息 - <?php echo $route_info['route_name']; ?></h1>
			<h3>基本信息</h3>
			<table class="tb-content-detail">
				<tr>
					<th>路线名</th>
					<td><?php echo $route_info['route_name']; ?></td>
				</tr>
				<tr>
					<th>路线简介</th>
					<td><?php echo nl2br($route_info['route_description']); ?></td>
				</tr>
				<tr>
					<th>主图</th>
					<td>
						<?php if($route_info['main_image']): ?>
						<img src="<?php echo $route_info['main_image']; ?>" />
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>价格</th>
					<td><?php echo ($route_info['route_price_min'] !== '' || $route_info['route_price_max'] !== '') ? ($route_info['route_price_min'] . '～' . $route_info['route_price_max'] . '元') : ''; ?></td>
				</tr>
				<tr>
					<th>基本成本</th>
					<td><?php echo $route_info['route_base_cost'] !== '' ? ($route_info['route_base_cost'] . '元') : ''; ?></td>
				</tr>
				<tr>
					<th>交通费</th>
					<td><?php echo $route_info['route_parking_cost'] !== '' ? ($route_info['route_parking_cost'] . '元') : ''; ?></td>
				</tr>
				<tr>
					<th>停车费</th>
					<td><?php echo $route_info['route_traffic_cost'] !== '' ? ($route_info['route_traffic_cost'] . '元') : ''; ?></td>
				</tr>
				<tr>
					<th>成本合计</th>
					<td><?php echo $route_info['route_total_cost'] !== '' ? ($route_info['route_total_cost'] . '元') : ''; ?></td>
				</tr>
				<tr>
					<th>公开状态</th>
					<td><?php echo $route_info['route_status'] == '1' ? '公开' : '未公开'; ?></td>
				</tr>
				<tr>
					<th>登录时间</th>
					<td><?php echo date('Y年m月d日　H:i:s', strtotime($route_info['created_at'])); ?></td>
				</tr>
				<tr>
					<th>最新修改时间</th>
					<td><?php echo date('Y年m月d日　H:i:s', strtotime($route_info['modified_at'])); ?></td>
				</tr>
			</table>
			<h3>详细日程</h3>
			<?php foreach($route_info['detail_list'] as $detail_info): ?>
			<table class="tb-content-detail">
				<tr>
					<th colspan="2">DAY <?php echo $detail_info['route_detail_day']; ?></th>
				</tr>
				<tr>
					<th>标题</th>
					<td><?php echo $detail_info['route_detail_title']; ?></td>
				</tr>
				<tr>
					<th>简介</th>
					<td><?php echo nl2br($detail_info['route_detail_content']); ?></td>
				</tr>
				<tr>
					<th>景点</th>
					<td>
						<?php foreach($detail_info['route_spot_list'] as $route_spot): ?>
						<a href="/admin/spot_detail/<?php echo $route_spot['spot_id']; ?>/"><?php echo $route_spot['spot_name']; ?></a>
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<th>早餐</th>
					<td><?php echo nl2br($detail_info['route_breakfast']); ?></td>
				</tr>
				<tr>
					<th>午餐</th>
					<td><?php echo nl2br($detail_info['route_lunch']); ?></td>
				</tr>
				<tr>
					<th>晚餐</th>
					<td><?php echo nl2br($detail_info['route_dinner']); ?></td>
				</tr>
				<tr>
					<th>酒店</th>
					<td><?php echo nl2br($detail_info['route_hotel']); ?></td>
				</tr>
			<table>
			<?php endforeach; ?>
		</div>
		
		<div class="popup-shadow"></div>
		
		<?php if($route_info['route_status'] == '1'): ?>
		<div class="popup-route-status popup">
			<div class="popup-title">未公开路线设置确认</div>
			<div class="popup-content center">
				<p>路线设置为未公开路线后普通用户将无法通过宣传系统查看本路线的详细信息，<br/>确定要将路线「<?php echo $route_info['route_name']; ?>」设置为未公开吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_route_status/" method="post" id="form-route-status">
					<input type="hidden" name="modify_value" value="protected" />
					<input type="hidden" name="modify_id" value="<?php echo $route_info['route_id']; ?>" />
					<input type="hidden" name="page" value="route_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-route-status-yes">确定</li>
					<li class="button-no" id="popup-route-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php else: ?>
		<div class="popup-route-status popup">
			<div class="popup-title">公开路线设置确认</div>
			<div class="popup-content center">
				<p>路线设置为公开路线后普通用户将可以通过宣传系统查看本路线的详细信息，<br/>确定要将路线「<?php echo $route_info['route_name']; ?>」设置为公开吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_route_status/" method="post" id="form-route-status">
					<input type="hidden" name="modify_value" value="publish" />
					<input type="hidden" name="modify_id" value="<?php echo $route_info['route_id']; ?>" />
					<input type="hidden" name="page" value="route_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-route-status-yes">确定</li>
					<li class="button-no" id="popup-route-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	</div>
</body>
</html>
