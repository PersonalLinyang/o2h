<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<meta name="description" content="<?php echo $description; ?>">
	<meta name="keywords" content="<?php echo $keywords; ?>">
	<link rel="canonical" href="<?php echo $canonical; ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<?php echo Asset::css('sp/common.css'); ?>
	<?php echo Asset::css('sp/index.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('sp/common.js'); ?>
	<?php echo Asset::js('sp/google-analytics.js'); ?>
</head>
<body>
	<?php echo $header; ?>
	<div id="mainv">
		<!-- メインビジュアルアリア -->
		<?php echo $mainv; ?>
	</div>
	<div id="mission">
		<!-- 目標アリア -->
		<?php echo $mission; ?>
	</div>
	<div id="project">
		<!-- 目標アリア -->
		<?php echo $project; ?>
	</div>
	<div id="company">
		<!-- 会社概要 -->
		<?php echo $company; ?>
	</div>
	<?php echo $footer; ?>
</body>
</html>
