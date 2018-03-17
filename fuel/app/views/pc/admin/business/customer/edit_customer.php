<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $page_title; ?> - O2H管理系统</title>
	<?php echo Asset::css('common/jquery-ui.min.css'); ?>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/business/customer/edit_customer.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('common/jquery-ui.min.js'); ?>
	<?php echo Asset::js('common/jquery.ui.datepicker.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/business/customer/edit_customer.js'); ?>
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
				<?php if(!$staff_id_now || $staff_id_now == $user_id_self): ?>
				<h3>负责人信息</h3>
				<table class="tb-content-form">
					<tr>
						<th>主负责人</th>
						<td>
							<div class="div-search-area">
								<div class="div-search-input">
									<input type="text" class="txt-search" placeholder="请输入主负责人姓名" />
								</div>
								<ul class="ul-search-list">
								<?php foreach($user_list as $user): ?>
									<li data-id="<?php echo $user['user_id']; ?>" data-name="<?php echo $user['user_name']; ?>">
										<input type="radio" name="staff_id" value="<?php echo $user['user_id']; ?>" id="rdo-staff-id-<?php echo $user['user_id']; ?>" 
												<?php echo $input_staff_id == $user['user_id'] ? 'checked ' : ''; ?>/>
										<label class="lbl-for-radio lbl-for-radio-staff<?php echo $input_staff_id == $user['user_id'] ? ' active' : ''; ?>" 
												for="rdo-staff-id-<?php echo $user['user_id']; ?>" data-for="rdo-staff-id">
											<?php echo $user['user_name']; ?>
										</label>
									</li>
								<?php endforeach; ?>
								</ul>
							</div>
						</td>
					</tr>
					<?php if($form_page_index == 'modify_customer'): ?>
					<tr>
						<th>设定其他权限</th>
						<td>
							<div class="div-search-area">
								<div class="div-search-input">
									<input type="text" class="txt-search" placeholder="请输入权限设定对象姓名" />
									<div class="div-search-header">
										<p>无关者</p>
										<p>阅览者</p>
										<p>编辑者</p>
										<p>姓名</p>
									</div>
								</div>
								<ul class="ul-search-list">
									<?php foreach($user_list as $user): ?>
									<?php if($user['user_id'] != $input_staff_id): ?>
									<li data-id="<?php echo $user['user_id']; ?>" data-name="<?php echo $user['user_name']; ?>">
										<?php if(!in_array($user['user_id'], $edit_able_id_list) && !in_array($user['user_id'], $view_able_id_list)): ?>
										<input type="radio" name="staff_permission[<?php echo $user['user_id']; ?>]" value="0" id="rdo-permission-<?php echo $user['user_id']; ?>-0" 
												<?php echo (!in_array($user['user_id'], $input_editor_id_list) && !in_array($user['user_id'], $input_viewer_id_list)) ? 'checked ' : ''; ?>/>
										<label class="lbl-for-radio lbl-for-radio-permission<?php echo (!in_array($user['user_id'], $input_editor_id_list) && !in_array($user['user_id'], $input_viewer_id_list)) ? ' active' : ''; ?>" 
												for="rdo-permission-<?php echo $user['user_id']; ?>-0" data-for="rdo-permission-<?php echo $user['user_id']; ?>"></label>
										<?php else: ?>
										<label class="lbl-for-shadow-permission">　</label>
										<?php endif; ?>
										<?php if(!in_array($user['user_id'], $edit_able_id_list)): ?>
										<input type="radio" name="staff_permission[<?php echo $user['user_id']; ?>]" value="1" id="rdo-permission-<?php echo $user['user_id']; ?>-1" 
												<?php echo in_array($user['user_id'], $input_viewer_id_list) ? 'checked ' : ''; ?>/>
										<label class="lbl-for-radio lbl-for-radio-permission<?php echo in_array($user['user_id'], $input_viewer_id_list) ? ' active' : ''; ?>" 
												for="rdo-permission-<?php echo $user['user_id']; ?>-1" data-for="rdo-permission-<?php echo $user['user_id']; ?>"></label>
										<?php else: ?>
										<label class="lbl-for-shadow-permission">　</label>
										<?php endif; ?>
										<input type="radio" name="staff_permission[<?php echo $user['user_id']; ?>]" value="2" id="rdo-permission-<?php echo $user['user_id']; ?>-2" 
												<?php echo in_array($user['user_id'], $input_editor_id_list) ? 'checked ' : ''; ?>/>
										<label class="lbl-for-radio lbl-for-radio-permission<?php echo in_array($user['user_id'], $input_editor_id_list) ? ' active' : ''; ?>" 
												for="rdo-permission-<?php echo $user['user_id']; ?>-2" data-for="rdo-permission-<?php echo $user['user_id']; ?>"></label>
										<?php echo $user['user_name']; ?>
									</li>
									<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							</div>
						</td>
					</tr>
					<?php endif; ?>
				</table>
				<?php endif;?>
				
				<h3>申请信息</h3>
				<table class="tb-content-form">
					<tr>
						<th>姓名</th>
						<td><input type="text" name="customer_name" value="<?php echo $input_customer_name; ?>" placeholder="请输入顾客姓名" /></td>
					</tr>
					<tr>
						<th>性别</th>
						<td>
							<div class="radio-group">
								<input type="radio" name="customer_gender" value="1" id="rdo-customer-gender-1" <?php echo $input_customer_gender == '1' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_customer_gender == '1' ? ' active' : ''; ?>" for="rdo-customer-gender-1" data-for="rdo-customer-gender">男</label>
								<input type="radio" name="customer_gender" value="2" id="rdo-customer-gender-2" <?php echo $input_customer_gender == '2' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_customer_gender == '2' ? ' active' : ''; ?>" for="rdo-customer-gender-2" data-for="rdo-customer-gender">女</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>年龄</th>
						<td>
							<select name="customer_age">
								<option value="" class="placeholder">--请选择年龄段--</option>
								<option value="1"<?php echo $input_customer_age == '1' ? ' selected' : ''; ?>>15岁以内</option>
								<option value="2"<?php echo $input_customer_age == '2' ? ' selected' : ''; ?>>15～30岁</option>
								<option value="3"<?php echo $input_customer_age == '3' ? ' selected' : ''; ?>>30～45岁</option>
								<option value="4"<?php echo $input_customer_age == '4' ? ' selected' : ''; ?>>45～60岁</option>
								<option value="5"<?php echo $input_customer_age == '5' ? ' selected' : ''; ?>>60岁以上</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>旅游目的</th>
						<td>
							<select name="travel_reason">
								<option value="" value="" class="placeholder">--请选择旅游目的--</option>
								<?php foreach($travel_reason_list as $travel_reason): ?>
								<option value="<?php echo $travel_reason['travel_reason_id']; ?>"<?php echo $input_travel_reason == $travel_reason['travel_reason_id'] ? ' selected' : ''; ?>>
									<?php echo $travel_reason['travel_reason_name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
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
						<th>旅行天数</th>
						<td><input type="number" name="travel_days" value="<?php echo $input_travel_days; ?>" placeholder="请输入旅行天数" /></td>
					</tr>
					<tr>
						<th>来日时间</th>
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
								<option value="0"<?php echo $input_route_id == '0' ? ' selected' : ''; ?>>私人定制路线</option>
								<?php foreach($route_list as $route): ?>
								<option value="<?php echo $route['route_id']; ?>"<?php echo $input_route_id == $route['route_id'] ? ' selected' : ''; ?>>
									<?php echo $route['route_name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>预算(元)(不含吃住)</th>
						<td><input type="number" name="budget_base" value="<?php echo $input_budget_base; ?>" placeholder="请输入不含吃住的预算金额" /></td>
					</tr>
					<tr>
						<th>预算(元)(含吃住)</th>
						<td><input type="number" name="budget_total" value="<?php echo $input_budget_total; ?>" placeholder="请输入含吃住的预算金额" /></td>
					</tr>
					<tr>
						<th>首次利用</th>
						<td>
							<div class="radio-group">
								<input type="radio" name="first_flag" value="1" id="rdo-first-flag-1" <?php echo $input_first_flag == '1' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_first_flag == '1' ? ' active' : ''; ?>" for="rdo-first-flag-1" data-for="rdo-first-flag">是</label>
								<input type="radio" name="first_flag" value="0" id="rdo-first-flag-0" <?php echo $input_first_flag == '1' ? '' : 'checked '; ?>/>
								<label class="lbl-for-radio<?php echo $input_first_flag == '1' ? '' : ' active'; ?>" for="rdo-first-flag-0" data-for="rdo-first-flag">否</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>目标景点</th>
						<td>
							<div class="radio-group">
								<input type="radio" name="spot_hope_flag" value="1" class="rdo-spot-hope-flag" id="rdo-spot-hope-flag-1" <?php echo $input_spot_hope_flag == '1' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_spot_hope_flag == '1' ? ' active' : ''; ?>" for="rdo-spot-hope-flag-1" data-for="rdo-spot-hope-flag"><p>有</p></label>
								<input type="radio" name="spot_hope_flag" value="0" class="rdo-spot-hope-flag" id="rdo-spot-hope-flag-0" <?php echo $input_spot_hope_flag == '1' ? '' : 'checked '; ?>/>
								<label class="lbl-for-radio<?php echo $input_spot_hope_flag == '1' ? '' : ' active'; ?>" for="rdo-spot-hope-flag-0" data-for="rdo-spot-hope-flag"><p>无</p></label>
							</div>
							<div class="div-spot-hope-list"<?php echo $input_spot_hope_flag == '1' ? '' : ' style="display: none;"'; ?>>
								<ul class="ul-spot-hope-list-selected" id="ul-spot-hope-list-selected">
								<?php foreach($spot_list as $spot): ?>
									<?php if(in_array($spot['spot_id'], $input_spot_hope_list)): ?>
									<li data-id="<?php echo $spot['spot_id']; ?>" data-name="<?php echo $spot['spot_name']; ?>">
										<?php echo $spot['spot_name']; ?>
										<p class="btn-spot-hope-unselect" id="btn-spot-hope-unselect-<?php echo $spot['spot_id']; ?>">×</p>
										<input type="hidden" name="spot_hope_list[]" value="<?php echo $spot['spot_id']; ?>" />
									</li>
									<?php endif; ?>
								<?php endforeach; ?>
								</ul>
								<div class="div-search-area">
									<div class="div-search-input">
										<input type="text" class="txt-search" placeholder="请输入目标景点" />
									</div>
									<ul class="ul-search-list" id="ul-spot-hope-list">
									<?php foreach($spot_list as $spot): ?>
										<?php if(!in_array($spot['spot_id'], $input_spot_hope_list)): ?>
										<li data-id="<?php echo $spot['spot_id']; ?>" data-name="<?php echo $spot['spot_name']; ?>">
											<p class="btn-spot-hope-select" id="btn-spot-hope-select-<?php echo $spot['spot_id']; ?>"></p>
											<?php echo $spot['spot_name']; ?>
										</li>
										<?php endif; ?>
									<?php endforeach; ?>
									</ul>
								</div>
								<textarea name="spot_hope_other" class="txt-spot-hope-other" placeholder="其他目标景点"><?php echo $input_spot_hope_other; ?></textarea>
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
						<th>餐饮注意事项</th>
						<td><textarea name="dinner_demand" placeholder="请输入餐饮注意事项"><?php echo $input_dinner_demand; ?></textarea></td>
					</tr>
					<tr>
						<th>航班号</th>
						<td><input type="text" name="airplane_num" value="<?php echo $input_airplane_num; ?>" placeholder="请输入航班号" /></td>
					</tr>
					<tr>
						<th>电子邮箱</th>
						<td><input type="text" name="customer_email" value="<?php echo $input_customer_email; ?>" placeholder="请输入电子邮箱" /></td>
					</tr>
					<tr>
						<th>联系电话</th>
						<td><input type="text" name="customer_tel" value="<?php echo $input_customer_tel; ?>" placeholder="请输入联系电话" /></td>
					</tr>
					<tr>
						<th>微信号</th>
						<td><input type="text" name="customer_wechat" value="<?php echo $input_customer_wechat; ?>" placeholder="请输入微信号" /></td>
					</tr>
					<tr>
						<th>QQ号</th>
						<td><input type="text" name="customer_qq" value="<?php echo $input_customer_qq; ?>" placeholder="请输入QQ号" /></td>
					</tr>
					<tr>
						<th>备注</th>
						<td><textarea name="comment" placeholder="请输入备注"><?php echo $input_comment; ?></textarea></td>
					</tr>
				</table>
				
				<h3>酒店预约</h3>
				<table class="tb-content-form">
					<tr>
						<td>
							<div class="radio-group">
								<input type="radio" name="hotel_reserve_flag" value="1" class="rdo-hotel-reserve-flag" id="rdo-hotel-reserve-flag-1" <?php echo $input_hotel_reserve_flag == '1' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_hotel_reserve_flag == '1' ? ' active' : ''; ?>" for="rdo-hotel-reserve-flag-1" data-for="rdo-hotel-reserve-flag">需要</label>
								<input type="radio" name="hotel_reserve_flag" value="0" class="rdo-hotel-reserve-flag" id="rdo-hotel-reserve-flag-0" <?php echo $input_hotel_reserve_flag == '1' ? '' : 'checked '; ?>/>
								<label class="lbl-for-radio<?php echo $input_hotel_reserve_flag == '1' ? '' : ' active'; ?>" for="rdo-hotel-reserve-flag-0" data-for="rdo-hotel-reserve-flag">不需要</label>
							</div>
							<div class="div-hotel-reserve-list"<?php echo $input_hotel_reserve_flag == '1' ? '' : ' style="display: none;"'; ?>>
								<table class="tb-add-row-table tb-hotel-reserve-list" id="tb-hotel-reserve-list" data-row="<?php echo count($input_hotel_reserve_list); ?>">
									<tr>
										<th class="th-delete"></th>
										<th class="th-hotel-type">酒店类型</th>
										<th class="th-room-type">房型</th>
										<th class="th-people-num">人数</th>
										<th class="th-room-num">间数</th>
										<th class="th-day-num">天数</th>
										<th class="th-comment">备注</th>
									</tr>
									<?php foreach($input_hotel_reserve_list as $row_num => $hotel_reserve): ?>
									<tr>
										<td>
											<p class="btn-delete">－</p>
											<input type="hidden" name="hotel_reserve_row[]" value="<?php echo $row_num; ?>" />
										</td>
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
										<td><input type="text" name="comment_<?php echo $row_num; ?>" value="<?php echo $hotel_reserve['comment']; ?>" placeholder="请输入备注" /></td>
									</tr>
									<?php endforeach; ?>
									<tr><th colspan="7" class="th-add">添加一行酒店预约</td></tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
				
				<?php if(in_array($customer_status_now, array(2,3,4,5,6))): ?>
				<h3>日程设计</h3>
				<table class="tb-content-form">
					<tr>
						<td>
							<div id="schedule-area" data-schedulenum="<?php echo count($input_schedule_list) + 1; ?>">
								<?php foreach($input_schedule_list as $schedule_num => $schedule): ?>
								<div class="schedule-block">
									<table class="content-form-talbe-inner">
										<tr>
											<th>日期</th>
											<td>
												<input type="text" name="schedule_date_<?php echo $schedule_num; ?>" class="calendar" value="<?php echo $schedule['schedule_date']; ?>" placeholder="请输入日期 例:2030/01/01" />
											</td>
										</tr>
										<tr>
											<th>负责人</th>
											<td>
												<ul class="ul-schedule-staff-selected" id="ul-schedule-staff-selected-<?php echo $schedule_num; ?>">
												<?php foreach($user_list as $user): ?>
													<?php if(in_array($user['user_id'], $schedule['schedule_user_list'])): ?>
													<li data-schedulenum="<?php echo $schedule_num; ?>" data-userid="<?php echo $user['user_id']; ?>" data-name="<?php echo $user['user_name']; ?>">
														<?php echo $user['user_name']; ?><p class="btn-user-unselect" id="btn-user-unselect-<?php echo $schedule_num; ?>-<?php echo $user['user_id']; ?>">×</p>
														<input type="hidden" name="schedule_user_list_<?php echo $schedule_num; ?>[]" value="<?php echo $user['user_id']; ?>" />
													</li>
													<?php endif; ?>
												<?php endforeach; ?>
												</ul>
												<div class="div-search-area">
													<div class="div-schedule-staff-search"><input type="text" class="txt-search txt-schedule-staff-search" placeholder="请输入负责人姓名" /></div>
													<ul class="ul-schedule-staff-list ul-search-list" id="ul-schedule-staff-list-<?php echo $schedule_num; ?>">
													<?php foreach($user_list as $user): ?>
														<?php if(!in_array($user['user_id'], $schedule['schedule_user_list'])): ?>
														<li data-schedulenum="<?php echo $schedule_num; ?>" data-userid="<?php echo $user['user_id']; ?>" data-name="<?php echo $user['user_name']; ?>">
															<p class="btn-user-select" id="btn-user-select-<?php echo $schedule_num; ?>-<?php echo $user['user_id']; ?>"></p><?php echo $user['user_name']; ?>
														</li>
														<?php endif; ?>
													<?php endforeach; ?>
													</ul>
												</div>
											</td>
										</tr>
										<tr>
											<th>详细日程</th>
											<td>
												<table class="tb-add-row-table tb-schedule-detail" data-row="<?php echo count($schedule['schedule_detail_list']); ?>" data-schedulenum="<?php echo $schedule_num; ?>">
													<tr>
														<th class="th-delete"></th>
														<th class="th-time">时间</th>
														<th class="th-type">类型</th>
														<th class="th-desc">内容</th>
													</tr>
													<?php foreach($schedule['schedule_detail_list'] as $row_num => $schedule_detail): ?>
													<tr>
														<td><p class="btn-delete">－</p><input type="hidden" name="schedule_row_<?php echo $schedule_num; ?>[]" value="<?php echo $row_num; ?>" /></td>
														<td>
															<select name="schedule_start_at_<?php echo $schedule_num; ?>_<?php echo $row_num; ?>" class="sel-schedule-time">
																<option value="" class="placeholder">-请选择-</option>
																<?php for($hour = 0; $hour < 24; $hour++): ?>
																<?php for($minute = 0; $minute < 60; $minute = $minute + 30): ?>
																<?php $time = str_pad($hour, 2, 0, STR_PAD_LEFT) . ':' . str_pad($minute, 2, 0, STR_PAD_LEFT); ?>
																<option value="<?php echo $time; ?>"<?php echo $time == $schedule_detail['start_at'] ? ' selected' : ''; ?>>
																	<?php echo $time; ?>
																</option>
																<?php endfor; ?>
																<?php endfor; ?>
															</select>
															～
															<select name="schedule_end_at_<?php echo $schedule_num; ?>_<?php echo $row_num; ?>"  class="sel-schedule-time">
																<option value="" class="placeholder">-请选择-</option>
																<?php for($hour = 0; $hour < 24; $hour++): ?>
																<?php for($minute = 0; $minute < 60; $minute = $minute + 30): ?>
																<?php $time = str_pad($hour, 2, 0, STR_PAD_LEFT) . ':' . str_pad($minute, 2, 0, STR_PAD_LEFT); ?>
																<option value="<?php echo $time; ?>"<?php echo $time == $schedule_detail['end_at'] ? ' selected' : ''; ?>>
																	<?php echo $time; ?>
																</option>
																<?php endfor; ?>
																<?php endfor; ?>
															</select>
														</td>
														<td>
															<select name="schedule_type_<?php echo $schedule_num; ?>_<?php echo $row_num; ?>">
																<option value="" class="placeholder">-请选择-</option>
																<?php foreach($schedule_type_list as $schedule_type): ?>
																<option value="<?php echo $schedule_type['schedule_type_id']; ?>"
																		<?php echo $schedule_type['schedule_type_id'] == $schedule_detail['schedule_type'] ? ' selected' : ''; ?>>
																	<?php echo $schedule_type['schedule_type_name']; ?>
																</option>
																<?php endforeach; ?>
															</select>
														</td>
														<td><input type="text" name="schedule_desc_<?php echo $schedule_num; ?>_<?php echo $row_num; ?>" value="<?php echo $schedule_detail['schedule_desc']; ?>" placeholder="请输入内容" /></td>
													</tr>
													<?php endforeach; ?>
													<tr><th colspan="4" class="th-add">添加一行详细日程</td></tr>
												</table>
											</td>
										</tr>
									</table>
									<p class="btn-schedule-delete">删除日程</p>
									<input type="hidden" name="schedule_num[]" value="<?php echo $schedule_num; ?>" />
								</div>
								<?php endforeach; ?>
							</div>
							<div id="schedule-add"><p>添加日程</p></div>
						</td>
					</tr>
				</table>
				<?php endif; ?>
				
				<h3>实际成本(日元)</h3>
				<table class="tb-content-form">
					<tr>
						<td>
							<table class="tb-add-row-table tb-customer-cost" id="tb-customer-cost" data-row="<?php echo count($input_customer_cost_list); ?>">
								<tr><th colspan="8" class="th-customer-cost">当前实际成本 : <span class="span-cost-total"><?php echo $input_cost_total;?></span></th></tr>
								<tr>
									<th class="th-delete"></th>
									<th class="th-type">项目</th>
									<th class="th-name">简述</th>
									<th class="th-day">天数</th>
									<th class="th-people">人数</th>
									<th class="th-each">单价</th>
									<th class="th-total">合计</th>
								</tr>
								<?php foreach($input_customer_cost_list as $row_num => $customer_cost): ?>
								<tr>
									<td><p class="btn-delete">－</p><input type="hidden" name="customer_cost_row[]" value="<?php echo $row_num; ?>" /></td>
									<td>
										<select class="sel-customer-cost-type" name="customer_cost_type_<?php echo $row_num; ?>">
											<option value="" class="placeholder">-请选择-</option>
											<?php foreach($customer_cost_type_list as $customer_cost_type): ?>
											<option value="<?php echo $customer_cost_type['customer_cost_type_id']; ?>"
													<?php echo $customer_cost['customer_cost_type'] == $customer_cost_type['customer_cost_type_id'] ? ' selected' : ''; ?>>
												<?php echo $customer_cost_type['customer_cost_type_name']; ?>
											</option>
											<?php endforeach; ?>
										</select>
									</td>
									<td>
										<input type="text" name="customer_cost_desc_<?php echo $row_num; ?>" class="txt-customer-cost-desc<?php echo $customer_cost['customer_cost_type'] == '1' ? '' : ' readonly'; ?>" 
												value="<?php echo $customer_cost['customer_cost_desc']; ?>" placeholder="请输入简述"<?php echo $customer_cost['customer_cost_type'] == '1' ? '' : ' readonly="readonly"'; ?> />
									</td>
									<td><input type="number" name="customer_cost_day_<?php echo $row_num; ?>" class="txt-customer-cost-day" value="<?php echo $customer_cost['customer_cost_day']; ?>" placeholder="天数" /></td>
									<td><input type="number" name="customer_cost_people_<?php echo $row_num; ?>" class="txt-customer-cost-people" value="<?php echo $customer_cost['customer_cost_people']; ?>" placeholder="人数" /></td>
									<td><input type="number" name="customer_cost_each_<?php echo $row_num; ?>" class="txt-customer-cost-each" value="<?php echo $customer_cost['customer_cost_each']; ?>" placeholder="单价" /></td>
									<td class="td-total"><span class="span-customer-cost-total"><?php echo $customer_cost['customer_cost_total']; ?></span></td>
								</tr>
								<?php endforeach; ?>
								<tr><th colspan="8" class="th-add">添加一行实际成本</td></tr>
							</table>
						</td>
					</tr>
				</table>
				
				<table class="tb-content-form">
					<tr>
						<td>
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
