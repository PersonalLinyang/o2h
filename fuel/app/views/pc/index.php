<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<meta name="description" content="<?php echo $description; ?>">
	<meta name="keywords" content="<?php echo $keywords; ?>">
	<link rel="canonical" href="<?php echo $canonical; ?>">
	<?php echo Asset::css('pc/common.css'); ?>
	<?php echo Asset::css('pc/common_' . $language . '.css'); ?>
	<?php echo Asset::css('pc/index.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/common.js'); ?>
	<?php echo Asset::js('pc/index.js'); ?>
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
