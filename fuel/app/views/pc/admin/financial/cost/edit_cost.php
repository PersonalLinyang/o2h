<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $page_title; ?> - O2H管理系统</title>
	<?php echo Asset::css('common/jquery-ui.min.css'); ?>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('common/jquery-ui.min.js'); ?>
	<?php echo Asset::js('common/jquery.ui.datepicker.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/financial/cost/edit_cost.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1><?php echo $page_title; ?></h1>
			
			<form method="post" action="" class="content-form" enctype="multipart/form-data">
				<table class="tb-content-form">
					<tr>
						<th>支出项目</th>
						<td>
							<select name="cost_type">
								<option value="" value="" class="placeholder">--请选择支出项目--</option>
								<?php foreach($cost_type_list as $cost_type): ?>
								<option value="<?php echo $cost_type['cost_type_id']; ?>"<?php echo $input_cost_type == $cost_type['cost_type_id'] ? ' selected' : ''; ?>>
									<?php echo $cost_type['cost_type_name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>支出说明</th>
						<td><textarea name="cost_desc" placeholder="请输入支出说明"><?php echo $input_cost_desc; ?></textarea></td>
					</tr>
					<tr>
						<th>支出日期</th>
						<td>
							<input type="text" name="cost_at" class="calendar" value="<?php echo $input_cost_at; ?>" placeholder="请输入支出日期 例:2018/01/01" />
						</td>
					</tr>
					<tr>
						<th>支出明细</th>
						<td>
							<table class="tb-add-row-table tb-cost-detail" id="tb-cost-detail" data-row="<?php echo count($input_cost_detail_list); ?>">
								<tr><th colspan="5" class="th-cost-price"> 合计: <span class="span-cost-price"><?php echo $input_cost_price;?></span></th></tr>
								<tr>
									<th class="th-delete"></th>
									<th class="th-name">款项</th>
									<th class="th-day">单价</th>
									<th class="th-people">数量</th>
									<th class="th-total">小计</th>
								</tr>
								<?php foreach($input_cost_detail_list as $row_num => $cost_detail): ?>
								<tr>
									<td><p class="btn-delete">－</p><input type="hidden" name="cost_detail_row[]" value="<?php echo $row_num; ?>" /></td>
									<td><input type="text" name="cost_detail_desc_<?php echo $row_num; ?>" class="txt-cost-detail-desc" value="<?php echo $cost_detail['cost_detail_desc']; ?>" placeholder="请输入简述" /></td>
									<td><input type="number" name="cost_detail_each_<?php echo $row_num; ?>" class="txt-cost-detail-each" value="<?php echo $cost_detail['cost_detail_each']; ?>" placeholder="单价" /></td>
									<td><input type="number" name="cost_detail_count_<?php echo $row_num; ?>" class="txt-cost-detail-count" value="<?php echo $cost_detail['cost_detail_count']; ?>" placeholder="数量" /></td>
									<td class="td-total"><span class="span-cost-detail-total"><?php echo $cost_detail['cost_detail_total']; ?></span></td>
								</tr>
								<?php endforeach; ?>
								<tr><th colspan="5" class="th-add">添加一行支出明细</td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<ul class="button-group">
								<li class="button-yes btn-form-submit">保存</li>
								<li class="button-no"><a href="<?php echo $return_page_url; ?>">取消</a></li>
							</ul>
						</td>
					</tr>
				</table>
				
				<input type="hidden" name="page" value="<?php echo $form_page_index; ?>" />
			</form>
		</div>
	</div>
</body>
</html>
