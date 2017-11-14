<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $spot_info['spot_name']; ?> - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/spot_detail.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/modify_spot/<?php echo $spot_info['spot_id']; ?>/">信息修改</a></li>
				<?php if($spot_info['spot_status'] == '1'): ?>
				<li class="content-menu-button btn-spot-status">设为未公开</li>
				<?php else: ?>
				<li class="content-menu-button btn-spot-status">设为公开</li>
				<?php endif; ?>
				<li class="content-menu-button"><a href="/admin/spot_list/">景点一览</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>景点信息 - <?php echo $spot_info['spot_name']; ?></h1>
			<h3>基本信息</h3>
			<table class="tb-content-detail">
				<tr>
					<th>景点名</th>
					<td><?php echo $spot_info['spot_name']; ?></td>
				</tr>
				<tr>
					<th>景点所属地区</th>
					<td><?php echo $spot_info['spot_area_description']; ?></td>
				</tr>
				<tr>
					<th>景点类别</th>
					<td><?php echo $spot_info['spot_type_name']; ?></td>
				</tr>
				<tr>
					<th>收费状况</th>
					<td><?php echo $spot_info['free_flag'] == '0' ? '收费' . ($spot_info['price'] ? ('　票价:' . $spot_info['price'] . '元') : '') : '免费'; ?></td>
				</tr>
				<tr>
					<th>公开状态</th>
					<td><?php echo $spot_info['spot_status'] == '1' ? '公开' : '未公开'; ?></td>
				</tr>
				<tr>
					<th>登录时间</th>
					<td><?php echo date('Y年m月d日　H:i:s', strtotime($spot_info['created_at'])); ?></td>
				</tr>
				<tr>
					<th>最新修改时间</th>
					<td><?php echo date('Y年m月d日　H:i:s', strtotime($spot_info['modified_at'])); ?></td>
				</tr>
			</table>
			<h3>景点详情</h3>
			<?php foreach($spot_info['detail_list'] as $detail_info): ?>
			<table class="tb-content-detail">
				<tr>
					<th>景点详情名</th>
					<td><?php echo $detail_info['spot_detail_name']; ?></td>
				</tr>
				<tr>
					<th>景点介绍</th>
					<td><?php echo nl2br($detail_info['spot_description_text']); ?></td>
				</tr>
				<tr>
					<th>景点图片</th>
					<td>
						<p>共计<span class="strong"><?php echo count($detail_info['image_list']); ?></span>张图片</p>
						<div class="image-list">
							<?php foreach($detail_info['image_list'] as $image_key => $image_id): ?>
							<div class="image-block<?php echo $image_key == 0 ? ' main' : '';?>">
								<img src="/assets/img/pc/upload/spot/<?php echo $spot_info['spot_id']; ?>/<?php echo $detail_info['spot_sort_id']; ?>/<?php echo $image_id; ?>_thumb.jpg" 
									alt="<?php echo $spot_info['spot_name']; ?>-<?php echo $detail_info['spot_detail_name']; ?>-<?php echo $image_id; ?>" />
							</div>
							<?php endforeach; ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>详情公开期</th>
					<td>
						<?php 
						echo $detail_info['spot_start_month'] . '月' . ($detail_info['spot_start_month'] == $detail_info['spot_end_month'] ? '' 
							: '～' . ($detail_info['two_year_flag'] == '1' ? '次年' : '') . $detail_info['spot_end_month'] . '月'); 
						?>
					</td>
				</tr>
			<table>
			<?php endforeach; ?>
		</div>
		
		<div class="popup-shadow"></div>
		
		<?php if($spot_info['spot_status'] == '1'): ?>
		<div class="popup-spot-status popup">
			<div class="popup-title">未公开景点设置确认</div>
			<div class="popup-content center">
				<p>景点设置为未公开景点后普通用户将无法通过宣传系统查看本景点的详细信息，<br/>确定要将景点「<?php echo $spot_info['spot_name']; ?>」设置为未公开吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_spot_status/" method="post" id="form-spot-status">
					<input type="hidden" name="modify_value" value="protected" />
					<input type="hidden" name="modify_id" value="<?php echo $spot_info['spot_id']; ?>" />
					<input type="hidden" name="page" value="spot_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-spot-status-yes">确定</li>
					<li class="button-no" id="popup-spot-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php else: ?>
		<div class="popup-spot-status popup">
			<div class="popup-title">公开景点设置确认</div>
			<div class="popup-content center">
				<p>景点设置为公开景点后普通用户将可以通过宣传系统查看本景点的详细信息，<br/>确定要将景点「<?php echo $spot_info['spot_name']; ?>」设置为公开吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_spot_status/" method="post" id="form-spot-status">
					<input type="hidden" name="modify_value" value="publish" />
					<input type="hidden" name="modify_id" value="<?php echo $spot_info['spot_id']; ?>" />
					<input type="hidden" name="page" value="spot_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-spot-status-yes">确定</li>
					<li class="button-no" id="popup-spot-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	</div>
</body>
</html>
