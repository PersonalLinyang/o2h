<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>路线信息修改 - O2H管理系统</title>
	<?php echo Asset::css('common/jquery-ui.min.css'); ?>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/service/edit_route.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('common/jquery-ui.min.js'); ?>
	<?php echo Asset::js('common/jquery.uploadThumbs.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/edit_route.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>路线信息修改</h1>
			<form method="post" action="" class="content-form" enctype="multipart/form-data">
				<table class="tb-content-form">
					<tr>
						<th>路线名</th>
						<td><input type="text" name="route_name" value="<?php echo $input_route_name; ?>" maxlength="100" placeholder="请输入路线名(100字以内)" /></td>
					</tr>
					<tr>
						<th>路线简介</th>
						<td><textarea name="route_description" placeholder="请输入路线简介"><?php echo $input_route_description; ?></textarea></td>
					</tr>
					<tr>
						<th>主图</th>
						<td>
							<div class="div-main-image">
								<div class="thumb-area" id="div-thumb-main-image">
									<?php if($input_main_image): ?>
									<img src="<?php echo $input_main_image; ?>" class="thumb">
									<input type="hidden" name="main_image_tmp" value="<?php echo $input_main_image; ?>" />
									<?php endif; ?>
									<input type="hidden" name="main_image_url" value="<?php echo $main_image_url; ?>" />
								</div>
								<div class="upload-area">
									<label>
										<input type="file" name="main_image" id="file-main-image" multiple="multiple" accept="image/jpeg,image/png" />
										<p class="btn-thumb-upload">上传</p>
									</label>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th>价格</th>
						<td>
							<input type="text" name="route_price_min" class="price" value="<?php echo $input_route_price_min; ?>" placeholder="请输入底价(数字)" /> ～
							<input type="text" name="route_price_max" class="price" value="<?php echo $input_route_price_max; ?>" placeholder="请输入顶价(数字)" />
						</td>
					</tr>
					<tr>
						<th>基本成本</th>
						<td><input type="text" class="txt-cost" id="txt-base-cost" name="route_base_cost" value="<?php echo $input_route_base_cost; ?>" placeholder="请输入基本成本(数字)" /></td>
					</tr>
					<tr>
						<th>交通费</th>
						<td><input type="text" class="txt-cost" id="txt-traffic-cost" name="route_traffic_cost" value="<?php echo $input_route_traffic_cost; ?>" placeholder="请输入交通费(数字)" /></td>
					</tr>
					<tr>
						<th>停车费</th>
						<td><input type="text" class="txt-cost" id="txt-parking-cost" name="route_parking_cost" value="<?php echo $input_route_parking_cost; ?>" placeholder="请输入停车费(数字)" /></td>
					</tr>
					<tr>
						<th>成本合计</th>
						<td><label id="lbl_total_cost"><?php echo $input_route_total_cost; ?></label></td>
					</tr>
					<tr>
						<th>公开状态</th>
						<td>
							<input type="radio" name="route_status" value="0" id="route-status-0" <?php echo $input_route_status == '1' ? '' : 'checked '; ?>/>
							<label class="lbl-for-radio<?php echo $input_route_status == '1' ? '' : ' active'; ?>" for="route-status-0" data-for="chk-route-status">未公开</label>
							<input type="radio" name="route_status" value="1" id="route-status-1" <?php echo $input_route_status == '1' ? 'checked ' : ''; ?>/>
							<label class="lbl-for-radio<?php echo $input_route_status == '1' ? ' active' : ''; ?>" for="route-status-1" data-for="chk-route-status">公开</label>
						</td>
					</tr>
					<tr>
						<th>详细日程</th>
						<td>
							<div id="route-detail-area" data-detailday="<?php echo count($input_detail_list) + 1; ?>">
								<?php foreach($input_detail_list as $input_detail): ?>
								<div class="route-detail-block">
									<div class="div-detail-button-area">
										<p class="btn-detail-day">DAY <span class="span-day"><?php echo $input_detail['route_detail_day']; ?></span></p>
										<p class="btn-detail-info-show" id="btn-detail-info-show-<?php echo $input_detail['route_detail_day']; ?>">隐藏详情</p>
									</div>
									<div class="div-detail-info">
										<table class="content-form-talbe-inner">
											<tr>
												<th>标题</th>
												<td>
													<input type="text" name="route_detail_title_<?php echo $input_detail['route_detail_day']; ?>" 
														value="<?php echo $input_detail['route_detail_title']; ?>" maxlength="100" placeholder="请输入标题(100字以内)" />
												</td>
											</tr>
											<tr>
												<th>简介</th>
												<td><textarea name="route_detail_content_<?php echo $input_detail['route_detail_day']; ?>" placeholder="请输入简介"><?php echo $input_detail['route_detail_content']; ?></textarea></td>
											</tr>
											<tr>
												<th>景点</th>
												<td class="td-spot-list">
													<ul class="ul-spot-list-selected" id="ul-spot-list-selected-<?php echo $input_detail['route_detail_day']; ?>">
													<?php foreach($spot_list as $spot): ?>
														<?php if(in_array($spot['spot_id'], $input_detail['route_spot_list'])): ?>
														<li data-spotid="<?php echo $spot['spot_id']; ?>" data-spotname="<?php echo $spot['spot_name']; ?>">
															<?php echo $spot['spot_name']; ?>
															<p class="btn-spot-unselect" id="btn-spot-unselect-<?php echo $input_detail['route_detail_day']; ?>-<?php echo $spot['spot_id']; ?>">×</p>
															<input type="hidden" name="route_spot_list_<?php echo $input_detail['route_detail_day']; ?>[]" value="<?php echo $spot['spot_id']; ?>" />
														</li>
														<?php endif; ?>
													<?php endforeach; ?>
													</ul>
													<div class="div-spot-search">
														<input type="text" class="txt-spot-search" id="txt-spot-search-<?php echo $input_detail['route_detail_day']; ?>" placeholder="请输入要查找的景点名" />
													</div>
													<ul class="ul-spot-list" id="ul-spot-list-<?php echo $input_detail['route_detail_day']; ?>">
													<?php foreach($spot_list as $spot): ?>
														<?php if(!in_array($spot['spot_id'], $input_detail['route_spot_list'])): ?>
														<li data-spotid="<?php echo $spot['spot_id']; ?>" data-spotname="<?php echo $spot['spot_name']; ?>">
															<p class="btn-spot-select" id="btn-spot-select-<?php echo $input_detail['route_detail_day']; ?>-<?php echo $spot['spot_id']; ?>"></p>
															<?php echo $spot['spot_name']; ?>
														</li>
														<?php endif; ?>
													<?php endforeach; ?>
													</ul>
												</td>
											</tr>
											<tr>
												<th>早餐</th>
												<td><textarea name="route_breakfast_<?php echo $input_detail['route_detail_day']; ?>" placeholder="请输入早餐信息"><?php echo $input_detail['route_breakfast']; ?></textarea></td>
											</tr>
											<tr>
												<th>午餐</th>
												<td><textarea name="route_lunch_<?php echo $input_detail['route_detail_day']; ?>" placeholder="请输入午餐信息"><?php echo $input_detail['route_lunch']; ?></textarea></td>
											</tr>
											<tr>
												<th>晚餐</th>
												<td><textarea name="route_dinner_<?php echo $input_detail['route_detail_day']; ?>" placeholder="请输入晚餐信息"><?php echo $input_detail['route_dinner']; ?></textarea></td>
											</tr>
											<tr>
												<th>酒店</th>
												<td><textarea name="route_hotel_<?php echo $input_detail['route_detail_day']; ?>" placeholder="请输入酒店信息"><?php echo $input_detail['route_hotel']; ?></textarea></td>
											</tr>
										</table>
										<p class="btn-detail-delete" id="btn-detail-delete-<?php echo $input_detail['route_detail_day']; ?>">删除景点详情</p>
									</div>
									<input type="hidden" class="hid-detail-num" name="route_detail_num[]" value="<?php echo $input_detail['route_detail_day']; ?>" />
									<input type="hidden" class="hid-detail-day" name="route_detail_day_<?php echo $input_detail['route_detail_day']; ?>" value="" />
								</div>
								<?php endforeach; ?>
							</div>
							<div id="route-detail-add"><p>添加详细日程</p></div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<ul class="button-group">
								<li class="button-yes btn-form-submit">添加</li>
								<li class="button-no"><a href="/admin/route_list/">取消</a></li>
							</ul>
						</td>
					</tr>
				</table>
				<input type="hidden" name="page" value="modify_route" />
			</form>
		</div>
	</div>
</body>
</html>
