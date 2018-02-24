<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>顾客信息管理 - O2H管理系统</title>
	<?php echo Asset::css('common/jquery-ui.min.css'); ?>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::css('pc/admin/business/customer/customer_list.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('common/jquery-ui.min.js'); ?>
	<?php echo Asset::js('common/jquery.ui.datepicker.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/business/customer/customer_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<?php if($add_able_flag): ?>
				<li><a href="/admin/add_customer/">添加顾客信息</a></li>
				<?php endif; ?>
				<li class="btn-content-menu" id="btn-content-menu-select">筛选排序</li>
				<?php if($import_able_flag): ?>
				<li class="btn-content-menu" id="btn-content-menu-import">批量导入顾客信息</li>
				<?php endif; ?>
				<?php if($export_able_flag): ?>
				<li class="btn-content-menu" id="btn-content-menu-export">导出顾客信息列表</li>
				<?php endif; ?>
			</ul>
			<div class="content-menu-select content-menu-control-area" id="div-content-menu-select">
				<form action="/admin/customer_list/" method="get" id="form-content-menu-select">
					<table>
						<tr>
							<th rowspan="<?php echo ($view_new_able_flag || view_any_able_flag) ? '8' : '7'; ?>" class="th-parent">筛选条件</th>
							<th>姓名</th>
							<td><input type="text" name="select_name" value="<?php echo $select_name; ?>" /></td>
						</tr>
						<tr>
							<th>当前状态</th>
							<td>
								<?php foreach($customer_status_list as $customer_status): ?>
								<input type="checkbox" name="select_status[]" value="<?php echo $customer_status['customer_status_id']; ?>" id="chb-select-status-<?php echo $customer_status['customer_status_id']; ?>" <?php echo in_array($customer_status['customer_status_id'], $select_status) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check <?php echo in_array($customer_status['customer_status_id'], $select_status) ? ' active' : ''; ?>" for="chb-select-status-<?php echo $customer_status['customer_status_id']; ?>"><?php echo $customer_status['customer_status_name']; ?></label>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<th>顾客来源</th>
							<td>
								<?php foreach($customer_source_list as $customer_source): ?>
								<input type="checkbox" name="select_source[]" value="<?php echo $customer_source['customer_source_id']; ?>" id="chb-select-source-<?php echo $customer_source['customer_source_id']; ?>" <?php echo in_array($customer_source['customer_source_id'], $select_source) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check <?php echo in_array($customer_source['customer_source_id'], $select_source) ? ' active' : ''; ?>" for="chb-select-source-<?php echo $customer_source['customer_source_id']; ?>"><?php echo $customer_source['customer_source_name']; ?></label>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<th>人数</th>
							<td>
								<input type="text" name="select_people_min" class="number" value="<?php echo $select_people_min; ?>" />
								～
								<input type="text" name="select_people_max" class="number" value="<?php echo $select_people_max; ?>" />
								人
							</td>
						</tr>
						<tr>
							<th>天数</th>
							<td>
								<input type="text" name="select_days_min" class="number" value="<?php echo $select_days_min; ?>" />
								～
								<input type="text" name="select_days_max" class="number" value="<?php echo $select_days_max; ?>" />
								天
							</td>
						</tr>
						<tr>
							<th>来日时间</th>
							<td>
								<input type="text" name="select_start_at_min" class="calendar" value="<?php echo $select_start_at_min; ?>" />
								～
								<input type="text" name="select_start_at_max" class="calendar" value="<?php echo $select_start_at_max; ?>" />
							</td>
						</tr>
						<tr>
							<th>登录时间</th>
							<td>
								<input type="text" name="select_created_at_min" class="calendar" value="<?php echo $select_created_at_min; ?>" />
								～
								<input type="text" name="select_created_at_max" class="calendar" value="<?php echo $select_created_at_max; ?>" />
							</td>
						</tr>
						<?php if($view_new_able_flag || view_any_able_flag): ?>
						<tr>
							<th>负责人</th>
							<td>
								<input type="checkbox" name="select_staff_pattern[]" value="1" id="chb-select-staff-pattern-1" <?php echo in_array('1', $select_staff_pattern) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check<?php echo in_array('1', $select_staff_pattern) ? ' active' : ''; ?>" for="chb-select-staff-pattern-1">由我负责的顾客</label>
								<?php if($view_new_able_flag): ?>
								<input type="checkbox" name="select_staff_pattern[]" value="2" id="chb-select-staff-pattern-2" <?php echo in_array('2', $select_staff_pattern) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check<?php echo in_array('2', $select_staff_pattern) ? ' active' : ''; ?>" for="chb-select-staff-pattern-2">未设定负责人的顾客</label>
								<?php endif; ?>
								<?php if($view_any_able_flag): ?>
								<input type="checkbox" name="select_staff_pattern[]" value="3" id="chb-select-staff-pattern-3" <?php echo in_array('3', $select_staff_pattern) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check<?php echo in_array('3', $select_staff_pattern) ? ' active' : ''; ?>" for="chb-select-staff-pattern-3">由他人负责的顾客</label>
								<?php endif; ?>
							</td>
						</tr>
						<?php endif; ?>
					</table>
					<table>
						<tr>
							<th rowspan="2" class="th-parent">排序条件</th>
							<th>排序项目</th>
							<td>
								<input type="radio" name="sort_column" value="customer_name" id="rdb-sort-name" <?php echo $sort_column == "customer_name" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'customer_name' ? ' active' : ''; ?>" for="rdb-sort-name" data-for="rdb-sort-column">姓名</label>
								<input type="radio" name="sort_column" value="customer_status" id="rdb-sort-status" <?php echo $sort_column == "customer_status" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'customer_status' ? ' active' : ''; ?>" for="rdb-sort-status" data-for="rdb-sort-column">当前状态</label>
								<input type="radio" name="sort_column" value="customer_source" id="rdb-sort-source" <?php echo $sort_column == "customer_source" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'customer_source' ? ' active' : ''; ?>" for="rdb-sort-source" data-for="rdb-sort-column">顾客来源</label>
								<input type="radio" name="sort_column" value="people_num" id="rdb-sort-people" <?php echo $sort_column == "people_num" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'people_num' ? ' active' : ''; ?>" for="rdb-sort-people" data-for="rdb-sort-column">人数</label>
								<input type="radio" name="sort_column" value="travel_days" id="rdb-sort-days" <?php echo $sort_column == "travel_days" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'travel_days' ? ' active' : ''; ?>" for="rdb-sort-days" data-for="rdb-sort-column">天数</label>
								<input type="radio" name="sort_column" value="start_at" id="rdb-sort-start-at" <?php echo $sort_column == "start_at" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'start_at' ? ' active' : ''; ?>" for="rdb-sort-start-at" data-for="rdb-sort-column">来日时间</label>
								<input type="radio" name="sort_column" value="created_at" id="rdb-sort-created-at" <?php echo $sort_column == "created_at" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'created_at' ? ' active' : ''; ?>" for="rdb-sort-created-at" data-for="rdb-sort-column">登录时间</label>
								<input type="radio" name="sort_column" value="modified_at" id="rdb-sort-modified-at" <?php echo $sort_column == "modified_at" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'modified_at' ? ' active' : ''; ?>" for="rdb-sort-modified-at" data-for="rdb-sort-column">最近更新</label>
							</td>
						</tr>
						<tr>
							<th>排序方式</th>
							<td>
								<input type="radio" name="sort_method" value="asc" id="rdb-sort-asc" <?php echo $sort_method == "asc" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_method == 'asc' ? ' active' : ''; ?>" for="rdb-sort-asc" data-for="rdb-sort-method">升序</label>
								<input type="radio" name="sort_method" value="desc" id="rdb-sort-desc" <?php echo $sort_method == "desc" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_method == 'desc' ? ' active' : ''; ?>" for="rdb-sort-desc" data-for="rdb-sort-method">降序</label>
							</td>
						</tr>
					</table>
				</form>
				<ul class="button-group">
					<li class="button-yes" id="btn-content-menu-select-submit">筛选排序</li>
					<li class="button"><a href="/admin/customer_list/">恢复初始</a></li>
					<li class="button-no" id="btn-content-menu-select-cancel">取消</li>
				</ul>
			</div>
			
			<?php if($import_able_flag): ?>
			<div class="content-menu-import content-menu-control-area" id="div-content-menu-import">
				<form action="/admin/import_customer/" method="post" id="form-content-menu-import" enctype="multipart/form-data">
					<div class="upload-area">
						<label>
							<input type="file" name="file_customer_list" accept=".xls,.xlsx" class="file-content-menu" />
							<p class="btn-upload">请上传写有要导入的景点信息的Excel文件</p>
						</label>
					</div>
					<input type="hidden" name="page" value="customer_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="btn-content-menu-import-submit">导入</li>
					<li class="button"><a href="/assets/xls/model/import_customer_model.xls" download>下载模板</a></li>
					<li class="button-no" id="btn-content-menu-import-cancel">取消</li>
				</ul>
			</div>
			<?php endif; ?>
			
			<?php if($export_able_flag): ?>
			<div class="content-menu-export content-menu-control-area" id="div-content-menu-export">
				<form action="/admin/export_customer/" method="post" id="form-content-menu-export" enctype="multipart/form-data">
					<input type="hidden" name="select_name" value="<?php echo $select_name; ?>" />
					<input type="hidden" name="select_status" value="<?php echo implode(',', $select_status); ?>" />
					<input type="hidden" name="select_source" value="<?php echo implode(',', $select_source); ?>" />
					<input type="hidden" name="select_people_min" value="<?php echo $select_people_min; ?>" />
					<input type="hidden" name="select_people_max" value="<?php echo $select_people_max; ?>" />
					<input type="hidden" name="select_days_min" value="<?php echo $select_days_min; ?>" />
					<input type="hidden" name="select_days_max" value="<?php echo $select_days_max; ?>" />
					<input type="hidden" name="select_start_at_min" value="<?php echo $select_start_at_min; ?>" />
					<input type="hidden" name="select_start_at_max" value="<?php echo $select_start_at_max; ?>" />
					<input type="hidden" name="select_created_at_min" value="<?php echo $select_created_at_min; ?>" />
					<input type="hidden" name="select_created_at_max" value="<?php echo $select_created_at_max; ?>" />
					<input type="hidden" name="select_staff_pattern" value="<?php echo implode(',', $select_staff_pattern); ?>" />
					<input type="hidden" name="sort_column" value="<?php echo $sort_column; ?>" />
					<input type="hidden" name="sort_method" value="<?php echo $sort_method; ?>" />
					<input type="hidden" name="export_model" value="" id="hid-content-menu-export-model" />
					<input type="hidden" name="page" value="customer_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" class="btn-content-menu-export" id="btn-content-menu-export-review">阅览模式导出</li>
					<li class="button-yes" class="btn-content-menu-export" id="btn-content-menu-export-backup">备份模式导出</li>
					<li class="button-no" id="btn-content-menu-export-cancel">取消</li>
				</ul>
			</div>
			<?php endif; ?>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<?php if($customer_count): ?>
		<div class="content-main">
			<h1>顾客信息一览</h1>
			<div class="div-content-list">
				<p>
					共为您检索到<span class="strong"><?php echo $customer_count; ?></span>条顾客信息
					目前显示的是其中的第<span class="strong"><?php echo $start_number; ?></span><?php if($start_number != $end_number): ?>～<span class="strong"><?php echo $end_number; ?></span><?php endif; ?>条
				</p>
				<table class="tb-content-list">
					<tr>
						<?php if($delete_able_flag): ?>
						<th class="th-delete"></th>
						<?php endif; ?>
						<?php if($modify_able_flag): ?>
						<th class="th-modify"></th>
						<?php endif; ?>
						<th class="th-name">姓名</th>
						<th class="th-status">当前状态</th>
						<th class="th-source">顾客来源</th>
						<th class="th-staff">负责人</th>
						<th class="th-people">人数</th>
						<th class="th-days">天数</th>
						<th class="th-start-at">来日时间</th>
						<th class="th-created-at">登录时间</th>
						<th class="th-modified-at">最近更新</th>
					</tr>
					<?php foreach($customer_list as $customer): ?>
					<tr>
						<?php if($delete_able_flag): ?>
						<td>
							<?php if($user_id_self == $customer['staff_id'] || (empty($customer['staff_id']) && $delete_new_able_flag) || $delete_any_able_flag): ?>
							<p class="btn-controller btn-delete" data-value="<?php echo $customer['customer_id']; ?>" data-name="<?php echo $customer['customer_name']; ?>">削除</p>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<?php if($modify_able_flag): ?>
						<td>
							<?php if($user_id_self == $customer['staff_id'] || (empty($customer['staff_id']) && $modify_new_able_flag) || $modify_any_able_flag): ?>
							<p class="btn-controller btn-modify"><a href="/admin/modify_customer/<?php echo $customer['customer_id']; ?>/">修改</a></p>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<td><a href="/admin/customer_detail/<?php echo $customer['customer_id']; ?>/"><?php echo $customer['customer_name']; ?></a></td>
						<td><?php echo $customer['customer_status_name']; ?></td>
						<td><?php echo $customer['customer_source_name']; ?></td>
						<td><?php echo $customer['staff_name']; ?></td>
						<td><?php echo $customer['people_num']; ?></td>
						<td><?php echo $customer['travel_days']; ?></td>
						<td>
							<?php
							if($customer['start_at_year'] && $customer['start_at_month'] && $customer['start_at_day']) {
								echo $customer['start_at_year'] . '/' . str_pad(intval($customer['start_at_month']), 2, 0, STR_PAD_LEFT) . '/' . str_pad(intval($customer['start_at_day']), 2, 0, STR_PAD_LEFT);
							} else {
								echo $customer['start_at_year'] ? ($customer['start_at_year'] .  '年' . ($customer['start_at_month'] ? ($customer['start_at_month'] . '月') : '')) : '';
							}
							?>
						</td>
						<td><?php echo date('Y/m/d', strtotime($customer['created_at'])); ?></td>
						<td><?php echo date('Y/m/d', strtotime($customer['modified_at'])); ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
			<?php if($page_number > 1): ?>
			<ul class="ul-list-pager">
				<?php if($page > 1): ?>
				<li class="li-link long"><a href="/admin/customer_list/<?php echo ($page - 1); ?>/<?php echo $get_params; ?>">上一页</a></li>
				<li class="li-link"><a href="/admin/customer_list/<?php echo $get_params; ?>">1</a></li>
				<?php endif; ?>
				<?php if(($page - $page_link_max) > 2): ?>
				<li>...</li>
				<?php endif;?>
				<?php for($i = $page_link_max; $i >= 1; $i--): ?>
				<?php if(($page-$i) > 1): ?>
				<li class="li-link"><a href="/admin/customer_list/<?php echo ($page - $i); ?>/<?php echo $get_params; ?>"><?php echo ($page - $i); ?></a></li>
				<?php endif; ?>
				<?php endfor; ?>
				<li class="active"><?php echo $page; ?></li>
				<?php for($i = 1; $i <= $page_link_max; $i++): ?>
				<?php if(($page + $i) < $page_number): ?>
				<li class="li-link"><a href="/admin/customer_list/<?php echo ($page + $i); ?>/<?php echo $get_params; ?>"><?php echo ($page + $i); ?></a></li>
				<?php endif;?>
				<?php endfor; ?>
				<?php if(($page + $page_link_max) < ($page_number - 1)): ?>
				<li>...</li>
				<?php endif;?>
				<?php if($page < $page_number): ?>
				<li class="li-link"><a href="/admin/customer_list/<?php echo $page_number; ?>/<?php echo $get_params; ?>"><?php echo $page_number; ?></a></li>
				<li class="li-link long"><a href="/admin/customer_list/<?php echo ($page + 1); ?>/<?php echo $get_params; ?>">下一页</a></li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，未能查找到符合筛选条件的顾客信息<br/>
				请确认筛选条件后重新进行筛选排序
			</p>
		</div>
		<?php endif; ?>
		
		<?php if($delete_able_flag): ?>
		<div class="popup-shadow"></div>
		
		<div class="popup-delete popup">
			<div class="popup-title">删除顾客确认</div>
			<div class="popup-content center">
				<p>顾客一经删除将无法还原，<br/>当顾客被删除时，不具备查看已删除顾客信息权限的用户将无法查看该顾客的个人信息，<br/>确定要删除顾客信息-「<span class="popup-delete-name"></span>」吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_hotel/" method="post" id="form-delete">
					<input type="hidden" id="input-id" name="delete_id" value />
					<input type="hidden" name="page" value="hotel_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-delete-yes">确定</li>
					<li class="button-no" id="popup-delete-no">取消</li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	</div>
</body>
</html>
