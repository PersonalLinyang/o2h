<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $spot['spot_name']; ?></title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<link rel="canonical" href="https://www.ltdo2h.com/spot/<?php echo $spot['spot_id']; ?>">
	<?php echo Asset::css('pc/common.css'); ?>
	<?php echo Asset::css('pc/spot/spot_detail.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/common/google-analytics.js'); ?>
	<?php echo Asset::js('pc/common.js'); ?>
	<?php echo Asset::js('pc/spot/spot_detail.js'); ?>
</head>
<body>
	<div class="div-content-area">
		<div class="div-spot-title"><?php echo $spot['spot_name']; ?></div>
		<?php foreach($spot['spot_detail_list'] as $detail_info): ?>
		<div><div class="div-detail-title"><?php echo $detail_info['spot_detail_name']; ?></div></div>
		<div class="div-detail-content">
			<?php if($detail_info['spot_start_month'] != '1' || $detail_info['spot_end_month'] != '12'): ?>
			<div><span class="font-bold">开放时间：</span>每年<?php echo $detail_info['spot_start_month']; ?>月～<?php $detail_info['two_year_flag'] == '1' ? '来年' : ''; ?><?php echo $detail_info['spot_end_month']; ?>月</div>
			<?php endif; ?>
			<div class="div-image-detail">
				<div class="div-btn-image">
					<?php if(count($detail_info['image_list']) > 1): ?>
					<p class="btn-image btn-image-prev active"></p>
					<?php endif; ?>
				</div>
				<div class="div-img-detail">
					<ul class="ul-img-detail <?php echo count($detail_info['image_list']) > 1 ? 'ul-img-detail-slide' : ''; ?>" style="width: <?php echo count($detail_info['image_list']) * 600; ?>px">
						<?php foreach($detail_info['image_list'] as $image_key => $image_id): ?>
						<li index="<?php echo $image_key; ?>">
							<div class="div-img-detail-cell">
								<img class="img-detail" src="/assets/img/pc/upload/spot/<?php echo $spot['spot_id']; ?>/<?php echo $detail_info['spot_detail_id']; ?>/<?php echo $image_id; ?>_main.jpg">
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="div-btn-image">
					<?php if(count($detail_info['image_list']) > 1): ?>
					<p class="btn-image btn-image-next active"></p>
					<?php endif; ?>
				</div>
			</div>
			<div><?php echo nl2br($detail_info['spot_description_text']); ?></div>
		</div>
		<?php endforeach ?>
	</div>
</body>
</html>
