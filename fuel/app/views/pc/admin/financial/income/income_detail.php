<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>收入记录详情 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/financial/income/income_detail.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/financial/income/income_detail.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<?php if($edit_able_flag): ?>
				<li class="content-menu-button"><a href="/admin/modify_income/<?php echo $income_info['income_id']; ?>/">信息修改</a></li>
				<?php endif; ?>
				<li class="content-menu-button"><a href="<?php echo $income_list_url; ?>">收入记录一览</a></li>
				<?php if($approval_able_flag): ?>
				<?php if($income_info['approval_status'] == '1' && date('Y-m-d', strtotime($income_info['approval_at'])) == date('Y-m-d', time())): ?>
				<li class="content-menu-button btn-approval-status">设为未确认</li>
				<?php elseif($income_info['approval_status'] == '0'): ?>
				<li class="content-menu-button btn-approval-status">确认收入</li>
				<?php endif; //income_info['approval_status'] ?>
				<?php endif; //approval_able_flag ?>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>收入记录详情</h1>
			<h3>基本信息</h3>
			<table class="tb-content-detail">
				<tr>
					<th>收入项目</th>
					<td><?php echo $income_info['income_type_name']; ?></td>
				</tr>
				<tr>
					<th>收入说明</th>
					<td><?php echo nl2br($income_info['income_desc']); ?></td>
				</tr>
				<tr>
					<th>确认状态</th>
					<td><?php echo $income_info['approval_status'] == '1' ? '已确认' : '未确认'; ?></td>
				</tr>
				<tr>
					<th>收入日期</th>
					<td><?php echo date('Y年m月d日', strtotime($income_info['income_at'])); ?></td>
				</tr>
				<tr>
					<th>收入金额</th>
					<td>
						<?php if(is_null($income_info['income_price'])) : ?>
						-
						<?php else : ?>
						<?php echo $income_info['income_price']; ?> 元
						<?php if(count($income_info['income_detail_list'])) : ?>
						<a href="#h3-income-detail" class="link-scroll">收入明细</a>
						<?php endif; //count($customer_info['income_detail_list']) ?>
						<?php endif; //is_null($income_info['income_price']) ?>
					</td>
				</tr>
			</table>
			<p class="system-comment">
				※ 本收入记录由<?php echo $income_info['created_by'] ? $income_info['created_name'] : '管理系统'; ?>于<?php echo date('Y年m月d日H:i', strtotime($income_info['created_at'])); ?>登录
				<?php if($income_info['created_at'] != $income_info['modified_at']): ?>
				，<?php if($income_info['modified_name'] != $income_info['created_name']): ?>由<?php echo $income_info['modified_name']; ?><?php endif; ?>于<?php echo date('Y年m月d日H:i', strtotime($income_info['modified_at'])); ?>更新至当前状态
				<?php endif; ?>
				<?php if($income_info['approval_status'] == '1'): ?>
				, <?php if($income_info['approval_name'] != $income_info['created_name']): ?>由<?php echo $income_info['approval_name']; ?><?php endif; ?>于<?php echo date('Y年m月d日H:i', strtotime($income_info['approval_at'])); ?>确认
				<?php endif; ?>
			</p>
			
			<h3 id="h3-income-detail">收入明细</h3>
			<?php if(!is_null($income_info['income_price']) && count($income_info['income_detail_list'])) : ?>
			<table class="tb-content-list tb-income-detail">
				<tr>
					<th class="th-desc">款项</th>
					<th class="th-number">单价</th>
					<th class="th-number">数量</th>
					<th class="th-total">小计</th>
				</tr>
				<?php foreach($income_info['income_detail_list'] as $income_detail) : ?>
				<tr>
					<td class="td-desc"><?php echo is_null($income_detail['income_detail_desc']) ?  '-' : $income_detail['income_detail_desc']; ?></td>
					<td><?php echo is_null($income_detail['income_detail_each']) ?  '-' : $income_detail['income_detail_each']; ?></td>
					<td><?php echo is_null($income_detail['income_detail_count']) ?  '-' : $income_detail['income_detail_count']; ?></td>
					<td><?php echo is_null($income_detail['income_detail_total']) ?  '-' : $income_detail['income_detail_total']; ?>元</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<?php endif; ?>
		</div>
		
		<?php if($approval_able_flag): ?>
		<div class="popup-shadow"></div>
		
		<?php if($income_info['approval_status'] == '1' && date('Y-m-d', strtotime($income_info['approval_at'])) == date('Y-m-d', time())): ?>
		<div class="popup-approval-status popup">
			<div class="popup-title">未确认收入记录设置确认</div>
			<div class="popup-content center">
				<p>收入记录设置为未确认后将不会影响公司账面资金额，<br/>确定要将收入记录设置为未确认吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_income_status/" method="post" id="form-approval-status">
					<input type="hidden" name="modify_value" value="0" />
					<input type="hidden" name="modify_id" value="<?php echo $income_info['income_id']; ?>" />
					<input type="hidden" name="page" value="income_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-approval-status-yes">确定</li>
					<li class="button-no" id="popup-approval-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php elseif($income_info['approval_status'] == '0'): ?>
		<div class="popup-approval-status popup">
			<div class="popup-title">收入记录确认</div>
			<div class="popup-content center">
				<p>收入记录设置为已确认后将于今晚变动公司账面资金额，<br/>确定要将确认收入记录吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_income_status/" method="post" id="form-approval-status">
					<input type="hidden" name="modify_value" value="1" />
					<input type="hidden" name="modify_id" value="<?php echo $income_info['income_id']; ?>" />
					<input type="hidden" name="page" value="income_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-approval-status-yes">确定</li>
					<li class="button-no" id="popup-approval-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php endif; //income_info['approval_status'] ?>
		<?php endif; //approval_able_flag ?>
	</div>
</body>
</html>
