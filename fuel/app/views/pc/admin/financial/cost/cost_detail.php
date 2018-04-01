<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>支出记录详情 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/financial/cost/cost_detail.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/financial/cost/cost_detail.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<?php if($edit_able_flag): ?>
				<li class="content-menu-button"><a href="/admin/modify_cost/<?php echo $cost_info['cost_id']; ?>/">信息修改</a></li>
				<?php endif; ?>
				<li class="content-menu-button"><a href="<?php echo $cost_list_url; ?>">支出记录一览</a></li>
				<?php if($approval_able_flag): ?>
				<?php if($cost_info['approval_status'] == '1' && date('Y-m-d', strtotime($cost_info['approval_at'])) == date('Y-m-d', time())): ?>
				<li class="content-menu-button btn-approval-status">设为未确认</li>
				<?php elseif($cost_info['approval_status'] == '0'): ?>
				<li class="content-menu-button btn-approval-status">确认支出</li>
				<?php endif; //cost_info['approval_status'] ?>
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
			<h1>支出记录详情</h1>
			<h3>基本信息</h3>
			<table class="tb-content-detail">
				<tr>
					<th>支出项目</th>
					<td><?php echo $cost_info['cost_type_name']; ?></td>
				</tr>
				<tr>
					<th>支出说明</th>
					<td><?php echo nl2br($cost_info['cost_desc']); ?></td>
				</tr>
				<tr>
					<th>确认状态</th>
					<td><?php echo $cost_info['approval_status'] == '1' ? '已确认' : '未确认'; ?></td>
				</tr>
				<tr>
					<th>支出日期</th>
					<td><?php echo date('Y年m月d日', strtotime($cost_info['cost_at'])); ?></td>
				</tr>
				<tr>
					<th>支出金额</th>
					<td>
						<?php if(is_null($cost_info['cost_price'])) : ?>
						-
						<?php else : ?>
						<?php echo $cost_info['cost_price']; ?> 元
						<?php if(count($cost_info['cost_detail_list'])) : ?>
						<a href="#h3-cost-detail" class="link-scroll">支出明细</a>
						<?php endif; //count($customer_info['cost_detail_list']) ?>
						<?php endif; //is_null($cost_info['cost_price']) ?>
					</td>
				</tr>
			</table>
			<p class="system-comment">
				※ 本支出记录由<?php echo $cost_info['created_by'] ? $cost_info['created_name'] : '管理系统'; ?>于<?php echo date('Y年m月d日H:i', strtotime($cost_info['created_at'])); ?>登录
				<?php if($cost_info['created_at'] != $cost_info['modified_at']): ?>
				，<?php if($cost_info['modified_name'] != $cost_info['created_name']): ?>由<?php echo $cost_info['modified_name']; ?><?php endif; ?>于<?php echo date('Y年m月d日H:i', strtotime($cost_info['modified_at'])); ?>更新至当前状态
				<?php endif; ?>
				<?php if($cost_info['approval_status'] == '1'): ?>
				, <?php if($cost_info['approval_name'] != $cost_info['created_name']): ?>由<?php echo $cost_info['approval_name']; ?><?php endif; ?>于<?php echo date('Y年m月d日H:i', strtotime($cost_info['approval_at'])); ?>确认
				<?php endif; ?>
			</p>
			
			<h3 id="h3-cost-detail">支出明细</h3>
			<?php if(!is_null($cost_info['cost_price']) && count($cost_info['cost_detail_list'])) : ?>
			<table class="tb-content-list tb-cost-detail">
				<tr>
					<th class="th-desc">款项</th>
					<th class="th-number">单价</th>
					<th class="th-number">数量</th>
					<th class="th-total">小计</th>
				</tr>
				<?php foreach($cost_info['cost_detail_list'] as $cost_detail) : ?>
				<tr>
					<td class="td-desc"><?php echo is_null($cost_detail['cost_detail_desc']) ?  '-' : $cost_detail['cost_detail_desc']; ?></td>
					<td><?php echo is_null($cost_detail['cost_detail_each']) ?  '-' : $cost_detail['cost_detail_each']; ?></td>
					<td><?php echo is_null($cost_detail['cost_detail_count']) ?  '-' : $cost_detail['cost_detail_count']; ?></td>
					<td><?php echo is_null($cost_detail['cost_detail_total']) ?  '-' : $cost_detail['cost_detail_total']; ?>元</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<?php endif; ?>
		</div>
		
		<?php if($approval_able_flag): ?>
		<div class="popup-shadow"></div>
		
		<?php if($cost_info['approval_status'] == '1' && date('Y-m-d', strtotime($cost_info['approval_at'])) == date('Y-m-d', time())): ?>
		<div class="popup-approval-status popup">
			<div class="popup-title">未确认支出记录设置确认</div>
			<div class="popup-content center">
				<p>支出记录设置为未确认后将不会影响公司账面资金额，<br/>确定要将支出记录设置为未确认吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_cost_status/" method="post" id="form-approval-status">
					<input type="hidden" name="modify_value" value="0" />
					<input type="hidden" name="modify_id" value="<?php echo $cost_info['cost_id']; ?>" />
					<input type="hidden" name="page" value="cost_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-approval-status-yes">确定</li>
					<li class="button-no" id="popup-approval-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php elseif($cost_info['approval_status'] == '0'): ?>
		<div class="popup-approval-status popup">
			<div class="popup-title">支出记录确认</div>
			<div class="popup-content center">
				<p>支出记录设置为已确认后将于今晚变动公司账面资金额，<br/>确定要将确认支出记录吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_cost_status/" method="post" id="form-approval-status">
					<input type="hidden" name="modify_value" value="1" />
					<input type="hidden" name="modify_id" value="<?php echo $cost_info['cost_id']; ?>" />
					<input type="hidden" name="page" value="cost_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-approval-status-yes">确定</li>
					<li class="button-no" id="popup-approval-status-no">取消</li>
				</ul>
			</div>
		</div>
		<?php endif; //cost_info['approval_status'] ?>
		<?php endif; //approval_able_flag ?>
	</div>
</body>
</html>
