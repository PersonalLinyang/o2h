<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $page_title; ?> - O2H管理系统</title>
	<?php echo Asset::css('common/jquery-ui.min.css'); ?>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/service/spot/edit_spot.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('common/jquery-ui.min.js'); ?>
	<?php echo Asset::js('common/jquery.uploadThumbs.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/spot/edit_spot.js'); ?>
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
						<th>景点名</th>
						<td><input type="text" name="spot_name" value="<?php echo $input_spot_name; ?>" /></td>
					</tr>
					<tr>
						<th>景点所属地区</th>
						<td>
							<select name="spot_area">
								<option value=""></option>
								<?php foreach($area_list as $area): ?>
								<option value="<?php echo $area['area_id']; ?>"<?php echo $input_spot_area == $area['area_id'] ? ' selected' : ''; ?>>
									<?php echo $area['area_description']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>景点类别</th>
						<td>
							<select name="spot_type">
								<option value=""></option>
								<?php foreach($spot_type_list as $spot_type): ?>
								<option value="<?php echo $spot_type['spot_type_id']; ?>"<?php echo $input_spot_type == $spot_type['spot_type_id'] ? ' selected' : ''; ?>>
									<?php echo $spot_type['spot_type_name']; ?>
								</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>价格</th>
						<td>
							<div class="radio-group">
								<input type="radio" name="free_flag" value="0" id="free-flag-0" <?php echo $input_free_flag == '0' ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $input_free_flag == '0' ? ' active' : ''; ?>" for="free-flag-0" data-for="chk-free-flag">收费</label>
								<input type="radio" name="free_flag" value="1" id="free-flag-1" <?php echo $input_free_flag == '0' ? '' : 'checked '; ?>/>
								<label class="lbl-for-radio<?php echo $input_free_flag == '0' ? '' : ' active'; ?>" for="free-flag-1" data-for="chk-free-flag">免费</label>
							</div>
							<div class="div-spot-price-area"<?php echo $input_free_flag == '0' ? '' : ' style="display: none;"'; ?>>
								<input type="number" name="spot_price" value="<?php echo $input_spot_price; ?>" />
								<table class="tb-add-row-table tb-special-price">
									<tr>
										<th class="th-name">价格条件</th>
										<th class="th-price">价格</th>
									</tr>
									<?php foreach($input_special_price_list as $special_price): ?>
									<tr>
										<td><input type="text" name="special_price_name[]" value="<?php echo $special_price['special_price_name']; ?>" maxlength="30" placeholder="请输入价格条件" /></td>
										<td><input type="number" name="special_price[]" value="<?php echo $special_price['special_price']; ?>" placeholder="请输入价格" /></td>
									</tr>
									<?php endforeach; ?>
									<tr><th colspan="2" class="th-add">添加一行特别价格</td></tr>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<th>公开状态</th>
						<td>
							<input type="radio" name="spot_status" value="1" id="spot-status-1" <?php echo $input_spot_status == '1' ? 'checked ' : ''; ?>/>
							<label class="lbl-for-radio<?php echo $input_spot_status == '1' ? ' active' : ''; ?>" for="spot-status-1" data-for="chk-spot-status">公开</label>
							<input type="radio" name="spot_status" value="0" id="spot-status-0" <?php echo $input_spot_status == '1' ? '' : 'checked '; ?>/>
							<label class="lbl-for-radio<?php echo $input_spot_status == '1' ? '' : ' active'; ?>" for="spot-status-0" data-for="chk-spot-status">未公开</label>
						</td>
					</tr>
					<tr>
						<th>景点详情</th>
						<td>
							<div id="spot-detail-area" data-detailnum="<?php echo $max_detail_num + 1; ?>">
								<?php foreach($input_spot_detail_list as $spot_detail): ?>
								<div class="spot-detail-block">
									<table class="content-form-talbe-inner">
										<tr>
											<th>景点详情名</th>
											<td><input type="text" name="spot_detail_name_<?php echo $spot_detail['spot_detail_id']; ?>" value="<?php echo $spot_detail['spot_detail_name']; ?>" /></td>
										</tr>
										<tr>
											<th>景点介绍</th>
											<td><textarea name="spot_description_text_<?php echo $spot_detail['spot_detail_id']; ?>"><?php echo $spot_detail['spot_description_text']; ?></textarea></td>
										</tr>
										<tr>
											<th>景点图片</th>
											<td>
												<div class="spot-image-area" id="spot-image-area-<?php echo $spot_detail['spot_detail_id']; ?>" data-imagenum="<?php echo $spot_detail['max_image_id'] + 1; ?>">
													<?php foreach($spot_detail['image_list'] as $image_info): ?>
													<div class="spot-image-block" data-imagenum="<?php echo $image_info['image_id']; ?>">
														<div class="move-handle">调整顺序</div>
														<div class="thumb-area" id="thumb-<?php echo $spot_detail['spot_detail_id']; ?>-<?php echo $image_info['image_id']; ?>">
															<img src="/assets/img/<?php echo $image_info['image_name']; ?>" class="thumb" />
														</div>
														<div class="upload-area">
															<label>
																<input type="file" name="image_file_<?php echo $spot_detail['spot_detail_id']; ?>_<?php echo $image_info['image_id']; ?>" 
																		id="spot-images-<?php echo $spot_detail['spot_detail_id']; ?>-<?php echo $image_info['image_id']; ?>" multiple="multiple" accept="image/jpeg,image/png" />
																<p class="btn-thumb-upload">上传</p>
															</label>
															<p id="thumb-delete-<?php echo $spot_detail['spot_detail_id']; ?>-<?php echo $image_info['image_id']; ?>" class="btn-thumb-delete">删除</p>
														</div>
														<input type="hidden" name="image_name_<?php echo $spot_detail['spot_detail_id']; ?>_<?php echo $image_info['image_id']; ?>" value="<?php echo $image_info['image_name']; ?>" />
														<input type="hidden" name="image_type_<?php echo $spot_detail['spot_detail_id']; ?>_<?php echo $image_info['image_id']; ?>" value="<?php echo $image_info['image_type']; ?>" />
														<input type="hidden" name="image_id_list_<?php echo $spot_detail['spot_detail_id']; ?>[]" value="<?php echo $image_info['image_id']; ?>" />
													</div>
													<?php endforeach; ?>
												</div>
												<div id="spot-image-add-<?php echo $spot_detail['spot_detail_id']; ?>" class="btn-spot-image-add" data-detailnum="<?php echo $spot_detail['spot_detail_id']; ?>">
													<p>添加图片</p>
												</div>
											</td>
										</tr>
										<tr>
											<th>详情公开期</th>
											<td class="td-se-time">
												<input type="checkbox" name="two_year_flag_<?php echo $spot_detail['spot_detail_id']; ?>" 
														id="two-year-flag-<?php echo $spot_detail['spot_detail_id']; ?>" <?php echo $spot_detail['two_year_flag'] == '1' ? 'checked ' : '';?>/>
												<label for="two-year-flag-<?php echo $spot_detail['spot_detail_id']; ?>" class="lbl-for-check<?php echo $spot_detail['two_year_flag'] == '1' ? ' active' : '';?>">
													跨年
												</label>
												<select name="spot_start_month_<?php echo $spot_detail['spot_detail_id']; ?>">
													<option value=""></option>
													<?php for($i = 1; $i < 13; $i++): ?>
													<option value="<?php echo $i; ?>"<?php echo $i == $spot_detail['spot_start_month'] ? ' selected' : ''; ?>><?php echo $i; ?></option>
													<?php endfor; ?>
												</select>月～
												<select name="spot_end_month_<?php echo $spot_detail['spot_detail_id']; ?>">
													<option value=""></option>
													<?php for($i = 1; $i < 13; $i++): ?>
													<option value="<?php echo $i; ?>"<?php echo $i == $spot_detail['spot_end_month'] ? ' selected' : ''; ?>><?php echo $i; ?></option>
													<?php endfor; ?>
												</select>月
											</td>
										</tr>
									</table>
									<p class="btn-detail-delete">删除景点详情</p>
								</div>
								<?php endforeach; ?>
							</div>
							<div id="spot-detail-add"><p>添加景点详情</p></div>
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
