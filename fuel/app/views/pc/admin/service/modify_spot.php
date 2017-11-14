<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>景点信息修改 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/service/edit_spot.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('common/jquery.uploadThumbs.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/edit_spot.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>景点信息修改</h1>
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
						<th>收费/免费</th>
						<td>
							<input type="radio" name="free_flag" value="0" id="free-flag-0" <?php echo $input_free_flag == '0' ? 'checked ' : ''; ?>/>
							<label class="lbl-for-radio<?php echo $input_free_flag == '0' ? ' active' : ''; ?>" for="free-flag-0" data-for="chk-free-flag">收费</label>
							<input type="radio" name="free_flag" value="1" id="free-flag-1" <?php echo $input_free_flag == '0' ? '' : 'checked '; ?>/>
							<label class="lbl-for-radio<?php echo $input_free_flag == '0' ? '' : ' active'; ?>" for="free-flag-1" data-for="chk-free-flag">免费</label>
						</td>
					</tr>
					<tr>
						<th>票价</th>
						<td><input type="text" name="price" value="<?php echo $input_price; ?>" <?php echo $input_free_flag == '0' ? '' : 'class="readonly" readonly="readonly" '; ?>/></td>
					</tr>
					<tr>
						<th>公开状态</th>
						<td>
							<input type="radio" name="spot_status" value="0" id="spot-status-0" <?php echo $input_spot_status == '1' ? '' : 'checked '; ?>/>
							<label class="lbl-for-radio<?php echo $input_spot_status == '1' ? '' : ' active'; ?>" for="spot-status-0" data-for="chk-spot-status">未公开</label>
							<input type="radio" name="spot_status" value="1" id="spot-status-1" <?php echo $input_spot_status == '1' ? 'checked ' : ''; ?>/>
							<label class="lbl-for-radio<?php echo $input_spot_status == '1' ? ' active' : ''; ?>" for="spot-status-1" data-for="chk-spot-status">公开</label>
						</td>
					</tr>
					<tr>
						<th>景点详情</th>
						<td>
							<div id="spot-detail-area" data-detailnum="<?php echo count($input_detail_list) + 1; ?>">
								<?php foreach($input_detail_list as $input_detail): ?>
								<div class="spot-detail-block">
									<table class="content-form-talbe-inner">
										<tr>
											<th>景点详情名</th>
											<td><input type="text" name="spot_detail_name_<?php echo $input_detail['spot_sort_id']; ?>" value="<?php echo $input_detail['spot_detail_name']; ?>" /></td>
										</tr>
										<tr>
											<th>景点介绍</th>
											<td><textarea name="spot_description_text_<?php echo $input_detail['spot_sort_id']; ?>"><?php echo $input_detail['spot_description_text']; ?></textarea></td>
										</tr>
										<tr>
											<th>景点图片</th>
											<td>
												<?php if(count($input_detail['image_list_db'])): ?>
												<div class="spot-image-area">
													<p class="center strong">已上传图片排序</p>
													<?php foreach($input_detail['image_list_db'] as $input_image): ?>
													<div class="spot-image-block">
														<div class="thumb-area">
															<img class="thumb" src="/assets/img/pc/upload/spot/<?php echo $spot_id; ?>/<?php echo $input_detail['spot_sort_id']; ?>/<?php echo $input_image; ?>_thumb.jpg">
															<input type="hidden" name="spot_image_sort_<?php echo $input_detail['spot_sort_id']; ?>[]" value="<?php echo $input_image; ?>" />
														</div>
														<div class="upload-area three-button">
															<p class="btn-thumb-prev"></p>
															<p class="btn-thumb-next"></p>
															<p class="btn-thumb-delete">删除</p>
														</div>
													</div>
													<?php endforeach; ?>
												</div>
												<?php endif; ?>
												<div class="spot-image-area" id="spot-image-area-<?php echo $input_detail['spot_sort_id']; ?>" 
														data-imagenum="<?php echo count($input_detail['image_list_db']) + count($input_detail['image_list_upload']) + 1;?>">
													<p class="center strong">图片添加</p>
												<?php if(count($input_detail['image_list_upload'])): ?>
													<?php foreach($input_detail['image_list_upload'] as $input_image): ?>
													<div class="spot-image-block">
														<div class="thumb-area"><img class="thumb" src="/assets/img/tmp/<?php echo $_SESSION['login_user']['id']; ?>/spot/<?php echo $input_image; ?>"></div>
														<div class="upload-area one-button">
															<p class="btn-thumb-delete">删除</p>
														</div>
														<input type="hidden" name="spot_image_tmp_<?php echo $input_detail['spot_sort_id']; ?>[]" value="<?php echo $input_image; ?>" />
													</div>
													<?php endforeach; ?>
												<?php else: ?>
													<div class="spot-image-block">
														<div class="thumb-area" id="thumb-<?php echo $input_detail['spot_sort_id']; ?>-0"></div>
														<div class="upload-area">
															<label>
																<input type="file" name="spot_images_<?php echo $input_detail['spot_sort_id']; ?>[]" id="spot-images-<?php echo $input_detail['spot_sort_id']; ?>-0" multiple="multiple" accept="image/jpeg,image/png" />
																<p class="btn-thumb-upload">上传</p>
															</label>
															<p class="btn-thumb-delete">删除</p>
														</div>
													</div>
												<?php endif; ?>
												</div>
												<div id="spot-image-add-<?php echo $input_detail['spot_sort_id']; ?>" class="btn-spot-image-add" data-detailnum="<?php echo $input_detail['spot_sort_id']; ?>"><p>添加图片</p></div>
											</td>
										</tr>
										<tr>
											<th>详情公开期</th>
											<td class="td-se-time">
												<input type="checkbox" name="two_year_flag_<?php echo $input_detail['spot_sort_id']; ?>" 
														id="two-year-flag-<?php echo $input_detail['spot_sort_id']; ?>" <?php echo $input_detail['two_year_flag'] == '1' ? 'checked ' : '';?>/>
												<label for="two-year-flag-<?php echo $input_detail['spot_sort_id']; ?>" class="lbl-for-check<?php echo $input_detail['two_year_flag'] == '1' ? ' active' : '';?>">
													跨年
												</label>
												<select name="spot_start_month_<?php echo $input_detail['spot_sort_id']; ?>">
													<option value=""></option>
													<?php for($i = 1; $i < 13; $i++): ?>
													<option value="<?php echo $i; ?>"<?php echo $i == $input_detail['spot_start_month'] ? ' selected' : ''; ?>><?php echo $i; ?></option>
													<?php endfor; ?>
												</select>月～
												<select name="spot_end_month_<?php echo $input_detail['spot_sort_id']; ?>">
													<option value=""></option>
													<?php for($i = 1; $i < 13; $i++): ?>
													<option value="<?php echo $i; ?>"<?php echo $i == $input_detail['spot_end_month'] ? ' selected' : ''; ?>><?php echo $i; ?></option>
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
								<li class="button-no"><a href="/admin/spot_detail/<?php echo $spot_id; ?>/">取消</a></li>
							</ul>
						</td>
					</tr>
				</table>
				<input type="hidden" name="page" value="modify_spot" />
			</form>
		</div>
	</div>
</body>
</html>
