<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>顾客详细信息 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/modify_customer/<?php echo $customer_info['customer_id']; ?>/">信息修改</a></li>
				<li class="content-menu-button"><a href="/admin/customer_list/">顾客一览</a></li>
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
					<th>负责人</th>
					<td>
						<?php if($customer_info['staff_id'] && $customer_info['staff_name']) : ?>
						<a target="_blank" href="/admin/user_detail/<?php echo $customer_info['staff_id']; ?>/"><?php echo $customer_info['staff_name']; ?></a>
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
						<a href="#tb-hotel-reserve">酒店预约详情</a>
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
						<a href="#tb-customer-cost">查看实际成本明细</a>
						<?php endif; ?>
						<?php endif; ?>
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
					<th>备注</th>
					<td><?php echo $customer_info['comment'] ? nl2br($customer_info['comment']) : '-'; ?></td>
				</tr>
			</table>
			<?php if($customer_info['hotel_reserve_flag'] && count($customer_info['hotel_reserve_list'])) : ?>
			<h3>酒店预约详情</h3>
			<table class="tb-content-list" id="tb-hotel-reserve">
				<tr>
					<th class="th-hotel-type">酒店类型</th>
					<th class="th-room-type">房型</th>
					<th class="th-people-num">人数</th>
					<th class="th-room-num">间数</th>
					<th class="th-day-num">天数</th>
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
			<?php endif; ?>
			<?php if(!is_null($customer_info['cost_total']) && count($customer_info['customer_cost_list'])) : ?>
			<h3>实际成本明细</h3>
			<table class="tb-content-list" id="tb-customer-cost">
				<tr>
					<th class="th-type">项目</th>
					<th class="th-name">简述</th>
					<th class="th-day">天数</th>
					<th class="th-people">人数</th>
					<th class="th-each">单价</th>
					<th class="th-total">合计</th>
				</tr>
				<?php foreach($customer_info['customer_cost_list'] as $customer_cost) : ?>
				<tr>
					<td><?php echo is_null($customer_cost['customer_cost_type_name']) ?  '-' : $customer_cost['customer_cost_type_name']; ?></td>
					<td><?php echo $customer_cost['customer_cost_desc'] ? $customer_cost['customer_cost_desc'] : '-'; ?></td>
					<td><?php echo is_null($customer_cost['customer_cost_day']) ?  '-' : $customer_cost['customer_cost_day']; ?></td>
					<td><?php echo is_null($customer_cost['customer_cost_people']) ?  '-' : $customer_cost['customer_cost_people']; ?></td>
					<td><?php echo is_null($customer_cost['customer_cost_each']) ?  '-' : $customer_cost['customer_cost_each']; ?></td>
					<td><?php echo is_null($customer_cost['customer_cost_total']) ?  '-' : $customer_cost['customer_cost_total']; ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
			<?php endif; ?>
		</div>
	</div>
</body>
</html>
