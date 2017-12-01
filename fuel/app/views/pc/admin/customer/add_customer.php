<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>顾客登录 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/customer/edit_customer.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/customer/edit_customer.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>顾客登录</h1>
			<form method="post" action="" class="content-form" enctype="multipart/form-data">
				<table class="tb-content-form">
					<tr>
						<th>顾客名</th>
						<td><input type="text" name="customer_name" value="<?php echo $input_customer_name; ?>" maxlength="50" placeholder="请输入顾客名" /></td>
					</tr>
					<tr>
						<th>顾客来源</th>
						<td>
							<select name="customer_source">
								<option value="" class="placeholder">--请选择顾客来源--</option>
								<?php foreach($customer_source_list as $customer_source): ?>
								<option value="<?php echo $customer_source['customer_source_id']; ?>"<?php echo $input_customer_source == $customer_source['customer_source_id'] ? ' selected' : ''; ?>>
									<?php echo $customer_source['customer_source_name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>人数</th>
						<td>
							男<input class="people-number" type="number" name="men_num" value="<?php echo $input_men_num; ?>" placeholder="请输入人数" />
							女<input class="people-number" type="number" name="women_num" value="<?php echo $input_women_num; ?>" placeholder="请输入人数" />
							儿童<input class="people-number" type="number" name="children_num" value="<?php echo $input_children_num; ?>" placeholder="请输入人数" />
						</td>
					</tr>
					<tr>
						<th>来日日期</th>
						<td>
							<select name="start_at_year" class="date">
								<option value="" class="placeholder">--请选择--</option>
								<?php for($i = intval(date('Y', time())); $i < (intval(date('Y', time())) + 2); $i++): ?>
								<option value="<?php echo $i; ?>"<?php echo $input_start_at_year == $i ? ' selected' : ''; ?>><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>年
							<select name="start_at_month" class="date">
								<option value="" class="placeholder">--请选择--</option>
								<?php for($i = 1; $i < 13; $i++): ?>
								<option value="<?php echo $i; ?>"<?php echo $input_start_at_month == $i ? ' selected' : ''; ?>><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>月
							<select name="start_at_day" class="date">
								<option value="" class="placeholder">--请选择--</option>
								<?php for($i = 1; $i < 32; $i++): ?>
								<option value="<?php echo $i; ?>"<?php echo $input_start_at_day == $i ? ' selected' : ''; ?>><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>日
						</td>
					</tr>
					<tr>
						<th>基本旅游路线</th>
						<td>
							<select name="route_id">
								<option value="" class="placeholder">--请选择基本旅游路线--</option>
								<option value="0" class="placeholder">定制路线</option>
								<?php foreach($route_list as $route): ?>
								<option value="<?php echo $route['route_id']; ?>"<?php echo $input_route_id == $route['route_id'] ? ' selected' : ''; ?>>
									<?php echo $route['route_name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>预算(不含吃住)</th>
						<td><input type="number" name="budget_base" value="<?php echo $input_budget_base; ?>" placeholder="请输入不含吃住的预算金额" /></td>
					</tr>
					<tr>
						<th>预算(含吃住)</th>
						<td><input type="number" name="budget_total" value="<?php echo $input_budget_total; ?>" placeholder="请输入含吃住的预算金额" /></td>
					</tr>
					<tr>
						<th>首次利用</th>
						<td>
							<div class="radio-group">
								<input type="radio" name="first_flag" value="1" id="first-flag-1" <?php echo $input_first_flag == '1' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_first_flag == '1' ? ' active' : ''; ?>" for="first-flag-1" data-for="chk-first-flag">是</label>
								<input type="radio" name="first_flag" value="0" id="first-flag-0" <?php echo $input_first_flag == '1' ? '' : 'checked '; ?>/>
								<label class="lbl-for-radio<?php echo $input_first_flag == '1' ? '' : ' active'; ?>" for="first-flag-0" data-for="chk-first-flag">否</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>目标景点</th>
						<td>
							<div class="radio-group">
								<input type="radio" name="spot_hope_flag" value="1" class="rdo-spot-hope-flag" id="spot-hope-flag-1" <?php echo $input_spot_hope_flag == '1' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_spot_hope_flag == '1' ? ' active' : ''; ?>" for="spot-hope-flag-1" data-for="chk-spot-hope-flag"><p>有</p></label>
								<input type="radio" name="spot_hope_flag" value="0" class="rdo-spot-hope-flag" id="spot-hope-flag-0" <?php echo $input_spot_hope_flag == '1' ? '' : 'checked '; ?>/>
								<label class="lbl-for-radio<?php echo $input_spot_hope_flag == '1' ? '' : ' active'; ?>" for="spot-hope-flag-0" data-for="chk-spot-hope-flag"><p>无</p></label>
							</div>
							<div class="div-spot-hope-list"<?php echo $input_spot_hope_flag == '1' ? '' : ' style="display: none;"'; ?>>
								<ul class="ul-spot-hope-list-selected" id="ul-spot-hope-list-selected">
								<?php foreach($spot_list as $spot): ?>
									<?php if(in_array($spot['spot_id'], $input_spot_hope_list)): ?>
									<li data-spotid="<?php echo $spot['spot_id']; ?>" data-spotname="<?php echo $spot['spot_name']; ?>">
										<?php echo $spot['spot_name']; ?>
										<p class="btn-spot-hope-unselect" id="btn-spot-hope-unselect-<?php echo $spot['spot_id']; ?>">×</p>
										<input type="hidden" name="route_spot_hope_list[]" value="<?php echo $spot['spot_id']; ?>" />
									</li>
									<?php endif; ?>
								<?php endforeach; ?>
								</ul>
								<div class="div-spot-hope-search">
									<input type="text" class="txt-spot-hope-search" id="txt-spot-hope-search" placeholder="请输入目标景点" />
								</div>
								<ul class="ul-spot-hope-list" id="ul-spot-hope-list">
								<?php foreach($spot_list as $spot): ?>
									<?php if(!in_array($spot['spot_id'], $input_spot_hope_list)): ?>
									<li data-spotid="<?php echo $spot['spot_id']; ?>" data-spotname="<?php echo $spot['spot_name']; ?>">
										<p class="btn-spot-hope-select" id="btn-spot-hope-select-<?php echo $spot['spot_id']; ?>"></p>
										<?php echo $spot['spot_name']; ?>
									</li>
									<?php endif; ?>
								<?php endforeach; ?>
								</ul>
								<textarea name="spot_hope_other" placeholder="其他目标景点"><?php echo $input_spot_hope_other; ?></textarea>
							</div>
						</td>
					</tr>
					<tr>
						<th>酒店预约</th>
						<td>
							<div class="radio-group">
								<input type="radio" name="hotel_reserve_flag" value="1" class="rdo-hotel-reserve-flag" id="hotel-reserve-flag-1" <?php echo $input_hotel_reserve_flag == '1' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_hotel_reserve_flag == '1' ? ' active' : ''; ?>" for="hotel-reserve-flag-1" data-for="chk-hotel-reserve-flag">需要</label>
								<input type="radio" name="hotel_reserve_flag" value="0" class="rdo-hotel-reserve-flag" id="hotel-reserve-flag-0" <?php echo $input_hotel_reserve_flag == '1' ? '' : 'checked '; ?>/>
								<label class="lbl-for-radio<?php echo $input_hotel_reserve_flag == '1' ? '' : ' active'; ?>" for="hotel-reserve-flag-0" data-for="chk-hotel-reserve-flag">不需要</label>
							</div>
							<div class="div-hotel-reserve-list"<?php echo $input_hotel_reserve_flag == '1' ? '' : ' style="display: none;"'; ?>>
								<table class="tb-add-row-table tb-hotel-reserve-list" id="tb-hotel-reserve-list" data-costrow="<?php echo count($input_hotel_reserve_list); ?>">
									<tr>
										<th class="th-delete"></th>
										<th class="th-hotel-type">酒店类型</th>
										<th class="th-room-type">房型</th>
										<th class="th-people-num">人数</th>
										<th class="th-room-num">间数</th>
										<th class="th-day-num">天数</th>
										<th class="th-other">备注</th>
									</tr>
									<?php foreach($input_hotel_reserve_list as $row_num => $hotel_reserve): ?>
									<tr>
										<td><p class="btn-delete">－</p></td>
										<td>
											<select class="sel-hotel-type" name="hotel_type_<?php echo $row_num; ?>">
												<option value="" class="placeholder">-请选择-</option>
												<?php foreach($hotel_type_list as $hotel_type): ?>
												<option value="<?php echo $hotel_type['hotel_type_id']; ?>"
														<?php echo $hotel_reserve['hotel_type'] == $hotel_type['hotel_type_id'] ? ' selected' : ''; ?>>
													<?php echo $hotel_type['hotel_type_name']; ?>
												</option>
												<?php endforeach; ?>
											</select>
										</td>
										<td>
											<select class="sel-room-type" name="room_type_<?php echo $row_num; ?>">
												<option value="" class="placeholder">-请选择-</option>
												<?php foreach($room_type_list as $room_type): ?>
												<option value="<?php echo $room_type['room_type_id']; ?>"
														<?php echo $hotel_reserve['room_type'] == $room_type['room_type_id'] ? ' selected' : ''; ?>>
													<?php echo $room_type['room_type_name']; ?>
												</option>
												<?php endforeach; ?>
											</select>
										</td>
										<td><input type="number" name="people_num_<?php echo $row_num; ?>" class="txt-people-num" value="<?php echo $hotel_reserve['people_num']; ?>" placeholder="人数" /></td>
										<td><input type="number" name="room_num_<?php echo $row_num; ?>" class="txt-room-num" value="<?php echo $hotel_reserve['room_num']; ?>" placeholder="间数" /></td>
										<td><input type="number" name="day_num_<?php echo $row_num; ?>" class="txt-day-num" value="<?php echo $hotel_reserve['day_num']; ?>" placeholder="天数" /></td>
										<td><input type="text" name="etc_<?php echo $row_num; ?>" value="<?php echo $hotel_reserve['etc']; ?>" maxlength="50" placeholder="请输入备注" /></td>
									</tr>
									<?php endforeach; ?>
									<tr><th colspan="7" class="th-add">添加一行酒店预约</td></tr>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<th>成本报价(元)</th>
						<td><input type="number" name="cost_budget" value="<?php echo $input_cost_budget; ?>" placeholder="请输入成本报价" /></td>
					</tr>
					<tr>
						<th>营业额(元)</th>
						<td><input type="number" name="turnover" value="<?php echo $input_turnover; ?>" placeholder="请输入营业额" /></td>
					</tr>
					<tr>
						<th>实际成本(日元)</th>
						<td>
							<table class="tb-add-row-table tb-customer-cost" id="tb-customer-cost" data-costrow="<?php echo count($input_customer_cost); ?>">
								<tr><th colspan="8" class="th-customer-cost">当前实际成本 : <span class="span-customer-cost"><?php echo $input_cost_total;?></span></th></tr>
								<tr>
									<th class="th-delete"></th>
									<th class="th-type">项目</th>
									<th class="th-name">简述</th>
									<th class="th-day">天数</th>
									<th class="th-people">人数</th>
									<th class="th-each">单价</th>
									<th class="th-total">合计</th>
								</tr>
								<?php foreach($input_customer_cost as $row_num => $customer_cost): ?>
								<tr>
									<td><p class="btn-delete">－</p></td>
									<td>
										<select class="sel-customer-cost-type" name="customer_cost_type_<?php echo $row_num; ?>">
											<option value="" class="placeholder">-请选择-</option>
											<?php foreach($customer_cost_type_list as $customer_cost_type): ?>
											<option value="<?php echo $customer_cost_type['customer_cost_type_id']; ?>"
													<?php echo $customer_cost['customer_cost_type'] == $customer_cost_type['customer_cost_type_id'] ? ' selected' : ''; ?>>
												<?php echo $customer_cost_type['customer_cost_type_name']; ?>
											</option>
											<option value="1" <?php echo $customer_cost['customer_cost_type'] == '1' ? ' selected' : ''; ?>>其他</option>
											<?php endforeach; ?>
										</select>
									</td>
									<td><input type="text" name="customer_cost_name_<?php echo $row_num; ?>" value="<?php echo $customer_cost['customer_cost_name']; ?>" maxlength="100" placeholder="请输入简述" /></td>
									<td><input type="number" name="cost_day_<?php echo $row_num; ?>" class="txt-cost-day" value="<?php echo $customer_cost['cost_day']; ?>" placeholder="天数" /></td>
									<td><input type="number" name="cost_people_<?php echo $row_num; ?>" class="txt-cost-people" value="<?php echo $customer_cost['cost_people']; ?>" placeholder="人数" /></td>
									<td><input type="number" name="cost_each_<?php echo $row_num; ?>" class="txt-cost-each" value="<?php echo $customer_cost['cost_each']; ?>" placeholder="单价" /></td>
									<td class="td-total"><span class="span-cost-total"><?php echo $customer_cost['cost_total']; ?></span></td>
								</tr>
								<?php endforeach; ?>
								<tr><th colspan="8" class="th-add">添加一行实际成本</td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<th>餐饮注意事项</th>
						<td><textarea name="dinner_demand" placeholder="请输入餐饮注意事项"><?php echo $input_dinner_demand; ?></textarea></td>
					</tr>
					<tr>
						<th>航班号</th>
						<td><input type="text" name="airplane_num" value="<?php echo $input_airplane_num; ?>" maxlength="20" placeholder="请输入航班号" /></td>
					</tr>
					<tr>
						<td colspan="2">
							<ul class="button-group">
								<li class="button-yes btn-form-submit">添加</li>
								<li class="button-no"><a href="/admin/customer_list/">取消</a></li>
							</ul>
						</td>
					</tr>
				</table>
				<input type="hidden" name="page" value="add_customer" />
			</form>
		</div>
	</div>
</body>
</html>
