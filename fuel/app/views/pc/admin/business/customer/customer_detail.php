<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>顾客详细信息 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/business/customer/customer_detail.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/business/customer/customer_detail.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<?php if($edit_able_flag): ?>
				<li class="content-menu-button"><a href="/admin/modify_customer/<?php echo $customer_info['customer_id']; ?>/">修改申请信息</a></li>
				<?php endif; ?>
				<?php if(in_array($customer_info['customer_status'], array('1','2','3','4','5','6','7','8','9')) && $customer_info['staff_id'] == $user_id_self): ?>
				<li class="content-menu-button btn-customer-status"><?php echo $btn_status_text; ?></li>
				<li class="content-menu-button btn-customer-delete">设为失效数据</li>
				<?php endif; ?>
				<li class="content-menu-button"><a href="/admin/customer_list/">返回顾客一览</a></li>
			</ul>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>顾客详细信息</h1>
			
			<h3>负责人信息</h3>
			<table class="tb-content-detail">
				<tr>
					<th>主负责人</th>
					<td>
						<?php if($customer_info['staff_name']) : ?>
						<?php echo $customer_info['staff_name']; ?>
						<?php else : ?>
						-
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>阅览权限</th>
					<td>
						<?php if(count($customer_info['viewer_list'])) : ?>
						<?php foreach($customer_info['viewer_list'] as $viewer): ?>
						<?php echo $viewer['user_name']; ?>　
						<?php endforeach; ?>
						<?php else : ?>
						-
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>编辑权限</th>
					<td>
						<?php if(count($customer_info['editor_list'])) : ?>
						<?php foreach($customer_info['editor_list'] as $editor): ?>
						<?php echo $editor['user_name']; ?>　
						<?php endforeach; ?>
						<?php else : ?>
						-
						<?php endif; ?>
					</td>
				</tr>
			</table>
			
			<h3>申请信息</h3>
			<table class="tb-content-detail">
				<tr>
					<th>ID</th>
					<td><?php echo $customer_info['customer_id']; ?></td>
				</tr>
				<tr>
					<th>姓名</th>
					<td><?php echo $customer_info['customer_name']; ?></td>
				</tr>
				<tr>
					<th>当前状态</th>
					<td><?php echo $customer_info['customer_status_name']; ?></td>
				</tr>
				<tr>
					<th>性别</th>
					<td>
						<?php if($customer_info['customer_gender'] == '1') : ?>
						男
						<?php elseif($customer_info['customer_gender'] == '2') : ?>
						女
						<?php else : ?>
						-
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>年龄</th>
					<td>
						<?php if($customer_info['customer_age'] == '1') : ?>
						15岁以内
						<?php elseif($customer_info['customer_age'] == '2') : ?>
						15～30岁
						<?php elseif($customer_info['customer_age'] == '3') : ?>
						30～45岁
						<?php elseif($customer_info['customer_age'] == '4') : ?>
						45～60岁
						<?php elseif($customer_info['customer_age'] == '5') : ?>
						60岁以上
						<?php else : ?>
						-
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>旅游目的</th>
					<td><?php echo $customer_info['travel_reason_name'] ? $customer_info['travel_reason_name'] : '-'; ?></td>
				</tr>
				<tr>
					<th>顾客来源</th>
					<td><?php echo $customer_info['customer_source_name'] ? $customer_info['customer_source_name'] : '-'; ?></td>
				</tr>
				<tr>
					<th>顾客会员号</th>
					<td>
						<?php if($customer_info['member_id']) : ?>
						<a target="_blank" href="/admin/member_detail/<?php echo $customer_info['member_id']; ?>/"><?php echo $customer_info['member_id']; ?></a>
						<?php else : ?>
						-
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>人数</th>
					<td>
						男性 <?php echo is_null($customer_info['men_num']) ?  '-' : $customer_info['men_num'] . ' 人'; ?>　　　　
						女性 <?php echo is_null($customer_info['women_num']) ?  '-' : $customer_info['women_num'] . ' 人'; ?>　　　　
						儿童 <?php echo is_null($customer_info['children_num']) ?  '-' : $customer_info['children_num'] . ' 人'; ?>
					</td>
				</tr>
				<tr>
					<th>旅行天数</th>
					<td><?php echo is_null($customer_info['travel_days']) ?  '-' : $customer_info['travel_days'] . ' 天'; ?></td>
				</tr>
				<tr>
					<th>来日时间</th>
					<td>
						<?php echo is_null($customer_info['start_at_year']) ?  '-' : $customer_info['start_at_year'] . ' 年'; ?>
						<?php echo is_null($customer_info['start_at_month']) ?  '' : $customer_info['start_at_month'] . ' 月'; ?>
						<?php echo is_null($customer_info['start_at_day']) ?  '' : $customer_info['start_at_day'] . ' 日'; ?>
					</td>
				</tr>
				<tr>
					<th>基本旅游路线</th>
					<td>
						<?php if(is_null($customer_info['route_id'])) : ?>
						-
						<?php elseif($customer_info['route_id'] == '0') : ?>
						私人定制路线
						<?php else : ?>
						<a target="_blank" href="/admin/route_detail/<?php echo $customer_info['route_id']; ?>/"><?php echo $customer_info['route_name']; ?></a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>预算(不含吃住)</th>
					<td><?php echo is_null($customer_info['budget_base']) ?  '-' : $customer_info['budget_base'] . ' 元'; ?></td>
				</tr>
				<tr>
					<th>预算(含吃住)</th>
					<td><?php echo is_null($customer_info['budget_total']) ?  '-' : $customer_info['budget_total'] . ' 元'; ?></td>
				</tr>
				<tr>
					<th>首次利用</th>
					<td><?php echo $customer_info['first_flag'] ? '是' : '否'; ?></td>
				</tr>
				<tr>
					<th>目标景点</th>
					<td>
						<?php if($customer_info['spot_hope_flag']) : ?>
						<?php if(count($customer_info['spot_hope_list']) || $customer_info['spot_hope_other']) : ?>
						<p>
							<?php foreach($customer_info['spot_hope_list'] as $spot_hope) : ?>
							<a target="_black" href="/admin/spot_detail/<?php echo $spot_hope['spot_id']; ?>"><?php echo $spot_hope['spot_name']; ?></a>　
							<?php endforeach; ?>
						</p>
						<p><?php echo nl2br($customer_info['spot_hope_other']); ?></p>
						<?php else : ?>
						有
						<?php endif; ?>
						<?php else : ?>
						无
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>酒店预约</th>
					<td>
						<?php if($customer_info['hotel_reserve_flag']) : ?>
						需要
						<?php if(count($customer_info['hotel_reserve_list'])) : ?>
						<a href="#h3-hotel-reserve" class="link-scroll">酒店预约详情</a>
						<?php endif; ?>
						<?php else : ?>
						不需要
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>成本报价</th>
					<td><?php echo is_null($customer_info['cost_budget']) ?  '-' : $customer_info['cost_budget'] . ' 元'; ?></td>
				</tr>
				<tr>
					<th>营业额</th>
					<td><?php echo is_null($customer_info['turnover']) ?  '-' : $customer_info['turnover'] . ' 元'; ?></td>
				</tr>
				<tr>
					<th>实际成本</th>
					<td>
						<?php if(is_null($customer_info['cost_total'])) : ?>
						-
						<?php else : ?>
						<?php echo $customer_info['cost_total']; ?> 日元
						<?php if(count($customer_info['customer_cost_list'])) : ?>
						<a href="#h3-customer-cost" class="link-scroll">查看实际成本明细</a>
						<?php endif; //count($customer_info['customer_cost_list']) ?>
						<?php endif; //is_null($customer_info['cost_total']) ?>
					</td>
				</tr>
				<tr>
					<th>餐饮注意事项</th>
					<td><?php echo $customer_info['dinner_demand'] ? nl2br($customer_info['dinner_demand']) : '-'; ?></td>
				</tr>
				<tr>
					<th>航班号</th>
					<td><?php echo $customer_info['airplane_num'] ? $customer_info['airplane_num'] : '-'; ?></td>
				</tr>
				<tr>
					<th>电子邮箱</th>
					<td><?php echo $customer_info['customer_email'] ? $customer_info['customer_email'] : '-'; ?></td>
				</tr>
				<tr>
					<th>联系电话</th>
					<td><?php echo $customer_info['customer_tel'] ? $customer_info['customer_tel'] : '-'; ?></td>
				</tr>
				<tr>
					<th>微信号</th>
					<td><?php echo $customer_info['customer_wechat'] ? $customer_info['customer_wechat'] : '-'; ?></td>
				</tr>
				<tr>
					<th>QQ号</th>
					<td><?php echo $customer_info['customer_qq'] ? $customer_info['customer_qq'] : '-'; ?></td>
				</tr>
				<tr>
					<th>备注</th>
					<td><?php echo $customer_info['comment'] ? nl2br($customer_info['comment']) : '-'; ?></td>
				</tr>
			</table>
			<p class="system-comment">
				※ 本顾客由<?php echo $customer_info['created_name']; ?>于<?php echo date('Y年m月d日H:i', strtotime($customer_info['created_at'])); ?>登录
				<?php if($customer_info['created_at'] != $customer_info['modified_at']): ?>
				，<?php if($customer_info['modified_name'] != $customer_info['created_name']): ?>由<?php echo $customer_info['modified_name']; ?><?php endif; ?>于<?php echo date('Y年m月d日H:i', strtotime($customer_info['modified_at'])); ?>更新至当前状态
				<?php endif; ?>
			</p>
			
			<?php if($customer_info['hotel_reserve_flag']) : ?>
			<h3 id="h3-hotel-reserve">酒店预约详情</h3>
			<?php if(count($customer_info['hotel_reserve_list'])) : ?>
			<table class="tb-content-list tb-hotel-reserve">
				<tr>
					<th class="th-type">酒店类型</th>
					<th class="th-type">房型</th>
					<th class="th-number">人数</th>
					<th class="th-number">间数</th>
					<th class="th-number">天数</th>
					<th class="th-comment">备注</th>
				</tr>
				<?php foreach($customer_info['hotel_reserve_list'] as $hotel_reserve) : ?>
				<tr>
					<td><?php echo is_null($hotel_reserve['hotel_type_name']) ?  '-' : $hotel_reserve['hotel_type_name']; ?></td>
					<td><?php echo is_null($hotel_reserve['room_type_name']) ?  '-' : $hotel_reserve['room_type_name']; ?></td>
					<td><?php echo is_null($hotel_reserve['people_num']) ?  '-' : $hotel_reserve['people_num']; ?></td>
					<td><?php echo is_null($hotel_reserve['room_num']) ?  '-' : $hotel_reserve['room_num']; ?></td>
					<td><?php echo is_null($hotel_reserve['day_num']) ?  '-' : $hotel_reserve['day_num']; ?></td>
					<td><?php echo $hotel_reserve['comment'] ? $hotel_reserve['comment'] : '-'; ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
			<?php else: ?>
			<p class="p-content-null">目前尚未登录任何酒店预约详情</p>
			<?php endif; ?>
			<?php endif; ?>
			
			<h3>日程设计</h3>
			<?php if(count($customer_info['schedule_list'])) : ?>
			<?php foreach($customer_info['schedule_list'] as $schedule): ?>
			<table class="tb-content-list tb-schedule">
				<tr><th class="th-date"><?php echo date('Y年m月d日', strtotime($schedule['schedule_date'])); ?></th><tr>
				<?php foreach($schedule['schedule_info_list'] as $schedule_info): ?>
				<tr>
					<td class="td-info">
						<p class="p-staff">
							<span class="strong">负责人</span>：
							<?php foreach($schedule_info['schedule_user_list'] as $schedule_user): ?>
							<?php echo $schedule_user['user_name']; ?>
							<?php endforeach; ?>
						</p>
						<table class="tb-content-inner tb-schedule-inner">
							<?php foreach($schedule_info['schedule_detail_list'] as $schedule_detail): ?>
							<tr>
								<th class="th-time">时间</th>
								<th class="th-type">类型</th>
								<th class="th-desc">内容</th>
							</tr>
							<tr>
								<td><?php echo $schedule_detail['start_at'] . '～' . $schedule_detail['end_at']; ?></td>
								<td><?php echo $schedule_detail['schedule_type']; ?></td>
								<td class="td-desc"><?php echo $schedule_detail['schedule_desc']; ?></td>
							</tr>
							<?php endforeach; ?>
						</table>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<?php endforeach; ?>
			<?php else: ?>
			<p class="p-content-null">目前尚未登录任何日程信息</p>
			<?php endif; ?>
			
			<h3 id="h3-customer-cost">实际成本明细</h3>
			<?php if(!is_null($customer_info['cost_total']) && count($customer_info['customer_cost_list'])) : ?>
			<table class="tb-content-list tb-customer-cost">
				<tr>
					<th class="th-type">项目</th>
					<th class="th-desc">简述</th>
					<th class="th-number">天数</th>
					<th class="th-number">人数</th>
					<th class="th-number">单价</th>
					<th class="th-number">合计</th>
				</tr>
				<?php foreach($customer_info['customer_cost_list'] as $customer_cost) : ?>
				<tr>
					<td><?php echo is_null($customer_cost['customer_cost_type_name']) ?  '-' : $customer_cost['customer_cost_type_name']; ?></td>
					<td class="td-desc"><?php echo $customer_cost['customer_cost_desc'] ? $customer_cost['customer_cost_desc'] : '-'; ?></td>
					<td><?php echo is_null($customer_cost['customer_cost_day']) ?  '-' : $customer_cost['customer_cost_day']; ?></td>
					<td><?php echo is_null($customer_cost['customer_cost_people']) ?  '-' : $customer_cost['customer_cost_people']; ?></td>
					<td><?php echo is_null($customer_cost['customer_cost_each']) ?  '-' : $customer_cost['customer_cost_each']; ?></td>
					<td><?php echo is_null($customer_cost['customer_cost_total']) ?  '-' : $customer_cost['customer_cost_total']; ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
			<?php else: ?>
			<p class="p-content-null">目前尚未登录任何实际成本记录</p>
			<?php endif; ?>
		</div>
		
		<?php if(in_array($customer_info['customer_status'], array('1','2','3','4','5','6','7','8','9')) && $customer_info['staff_id'] == $user_id_self): ?>
		<div class="popup-shadow"></div>
		
		<div class="popup-customer-status popup">
			<div class="popup-title">顾客状态变更确认</div>
			<div class="popup-content center">
				<p>
					当前「<?php echo $customer_info['customer_name']; ?>」的状态为「<?php echo $customer_info['customer_status_name']; ?>」，<br/>
					顾客状态一经修改将无法还原，确定要将「<?php echo $customer_info['customer_name']; ?>」的状态设置为「<?php echo $next_status_name; ?>」吗？
				</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_customer_status/" method="post" id="form-customer-status">
					<input type="hidden" name="modify_id" value="<?php echo $customer_info['customer_id']; ?>" />
					<input type="hidden" name="page" value="customer_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-customer-status-yes">确定</li>
					<li class="button-no" id="popup-customer-status-no">取消</li>
				</ul>
			</div>
		</div>
		
		<div class="popup-customer-delete popup">
			<div class="popup-title">失效变更确认</div>
			<div class="popup-content center">
				<p>
					当前「<?php echo $customer_info['customer_name']; ?>」的状态为「<?php echo $customer_info['customer_status_name']; ?>」，<br/>
					顾客状态一经设为失效将无法还原，确定要将「<?php echo $customer_info['customer_name']; ?>」的状态设置为失效吗？
					如果确认设置为失效，请选择失效原因
				</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/modify_customer_delete/" method="post" id="form-customer-delete">
					<select name="modify_reason">
						<option value="" class="placeholder">请选择失效原因</option>
						<?php foreach($customer_status_delete_list as $customer_status): ?>
						<option value="<?php echo $customer_status['customer_status_id']; ?>"><?php echo $customer_status['customer_status_name']; ?></option>
						<?php endforeach; ?>
					</select>
					<input type="hidden" name="modify_id" value="<?php echo $customer_info['customer_id']; ?>" />
					<input type="hidden" name="page" value="customer_detail" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-customer-delete-yes">确定</li>
					<li class="button-no" id="popup-customer-delete-no">取消</li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	</div>
</body>
</html>
