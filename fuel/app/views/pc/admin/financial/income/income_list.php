<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>收入管理 - O2H管理系统</title>
	<?php echo Asset::css('common/jquery-ui.min.css'); ?>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::css('pc/admin/financial/income/income_list.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('common/jquery-ui.min.js'); ?>
	<?php echo Asset::js('common/jquery.ui.datepicker.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/financial/income/income_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<?php if($edit_able_flag): ?>
				<li><a href="/admin/add_income/">添加收入记录</a></li>
				<?php endif; ?>
				<?php if($delete_able_flag): ?>
				<li id="btn-content-menu-delete-checked">删除选中收入记录</li>
				<?php endif; ?>
				<li class="btn-content-menu" id="btn-content-menu-select">筛选排序</li>
				<?php if($export_able_flag): ?>
				<li class="btn-content-menu" id="btn-content-menu-export">导出收入记录列表</li>
				<?php endif; ?>
				<?php if($income_type_able_flag): ?>
				<li><a href="/admin/income_type_list/">收入项目管理</a></li>
				<?php endif; ?>
			</ul>
			<div class="content-menu-select content-menu-control-area" id="div-content-menu-select">
				<form action="/admin/income_list/" method="get" id="form-content-menu-select">
					<table>
						<tr>
							<th rowspan="7" class="th-parent">筛选条件</th>
							<th>收入项目</th>
							<td>
								<?php foreach($income_type_list as $income_type): ?>
								<input type="checkbox" name="select_income_type[]" value="<?php echo $income_type['income_type_id']; ?>" id="chb-select-room-<?php echo $income_type['income_type_id']; ?>" <?php echo in_array($income_type['income_type_id'], $select_income_type) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check<?php echo in_array($income_type['income_type_id'], $select_income_type) ? ' active' : ''; ?>" for="chb-select-room-<?php echo $income_type['income_type_id']; ?>"><?php echo $income_type['income_type_name']; ?></label>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<th>收入说明</th>
							<td><input type="text" name="select_income_desc" value="<?php echo $select_income_desc; ?>" /></td>
						</tr>
						<tr>
							<th>收入金额</th>
							<td>
								<input type="text" name="select_price_min" class="price" value="<?php echo $select_price_min; ?>" />
								～
								<input type="text" name="select_price_max" class="price" value="<?php echo $select_price_max; ?>" />
								元
							</td>
						</tr>
						<tr>
							<th>收入日期</th>
							<td>
								<input type="text" name="select_income_at_min" class="calendar" value="<?php echo $select_income_at_min; ?>" />
								～
								<input type="text" name="select_income_at_max" class="calendar" value="<?php echo $select_income_at_max; ?>" />
							</td>
						</tr>
						<tr>
							<th>登录者</th>
							<td>
								<input type="checkbox" name="select_self_flag" value="1" id="chb-select-self-flag" <?php echo $select_self_flag == 1 ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check<?php echo $select_self_flag == 1 ? ' active' : ''; ?>" for="chb-select-self-flag">仅显示由我登录的收入记录</label>
							</td>
						</tr>
					</table>
					<table>
						<tr>
							<th rowspan="2" class="th-parent">排序条件</th>
							<th>排序项目</th>
							<td>
								<input type="radio" name="sort_column" value="income_type" id="rdb-sort-type" <?php echo $sort_column == "income_type" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'income_type' ? ' active' : ''; ?>" for="rdb-sort-type" data-for="rdb-sort-column">收入项目</label>
								<input type="radio" name="sort_column" value="income_price" id="rdb-sort-price" <?php echo $sort_column == "income_price" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'income_price' ? ' active' : ''; ?>" for="rdb-sort-price" data-for="rdb-sort-column">收入金额</label>
								<input type="radio" name="sort_column" value="income_at" id="rdb-sort-income-at" <?php echo $sort_column == "income_at" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'income_at' ? ' active' : ''; ?>" for="rdb-sort-income-at" data-for="rdb-sort-column">收入日期</label>
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
					<li class="button"><a href="/admin/income_list/">恢复初始</a></li>
					<li class="button-no" id="btn-content-menu-select-cancel">取消</li>
				</ul>
			</div>
			
			<?php if($export_able_flag): ?>
			<div class="content-menu-export content-menu-control-area" id="div-content-menu-export">
				<form action="/admin/export_income/" method="post" id="form-content-menu-export" enctype="multipart/form-data">
					<input type="hidden" name="select_income_desc" value="<?php echo $select_income_desc; ?>" />
					<input type="hidden" name="select_income_type" value="<?php echo implode(',', $select_income_type); ?>" />
					<input type="hidden" name="select_price_min" value="<?php echo $select_price_min; ?>" />
					<input type="hidden" name="select_price_max" value="<?php echo $select_price_max; ?>" />
					<input type="hidden" name="select_income_at_min" value="<?php echo $select_income_at_min; ?>" />
					<input type="hidden" name="select_income_at_max" value="<?php echo $select_income_at_max; ?>" />
					<input type="hidden" name="select_self_flag" value="<?php echo $select_self_flag; ?>" />
					<input type="hidden" name="sort_column" value="<?php echo $sort_column; ?>" />
					<input type="hidden" name="sort_method" value="<?php echo $sort_method; ?>" />
					<input type="hidden" name="export_model" value="" id="hid-content-menu-export-model" />
					<input type="hidden" name="page" value="income_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" class="btn-content-menu-export" id="btn-content-menu-export-review">导出</li>
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
		
		<?php if($income_count): ?>
		<div class="content-main">
			<h1>收入记录一览</h1>
			<div class="div-content-list">
				<p>
					共为您检索到<span class="strong"><?php echo $income_count; ?></span>条收入记录信息
					目前显示的是其中的第<span class="strong"><?php echo $start_number; ?></span><?php if($start_number != $end_number): ?>～<span class="strong"><?php echo $end_number; ?></span><?php endif; ?>条
				</p>
				<table class="tb-content-list">
					<tr>
						<?php if($delete_able_flag): ?>
						<th class="th-check"></th>
						<th class="th-delete"></th>
						<?php endif; ?>
						<?php if($edit_able_flag): ?>
						<th class="th-modify"></th>
						<?php endif; ?>
						<th class="th-id">ID</th>
						<th class="th-type">收入项目</th>
						<th class="th-desc">收入说明</th>
						<th class="th-price">收入金额(元)</th>
						<th class="th-date">收入日期</th>
						<th class="th-date">最近更新</th>
					</tr>
					<?php foreach($income_list as $income): ?>
					<tr>
						<?php if($delete_able_flag): ?>
						<td>
							<?php if(($user_id_self == $income['created_by'] || $delete_other_able_flag) && !$income['approval_status']): ?>
							<label class="lbl-for-check" for="delete-id-checked-<?php echo $income['income_id']; ?>"></label>
							<?php endif; ?>
						</td>
						<td>
							<?php if(($user_id_self == $income['created_by'] || $delete_other_able_flag) && !$income['approval_status']): ?>
							<p class="btn-controller btn-delete" data-value="<?php echo $income['income_id']; ?>">削除</p>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<?php if($edit_able_flag): ?>
						<td>
							<?php if(($user_id_self == $income['created_by'] || $edit_other_able_flag) && !$income['approval_status']): ?>
							<p class="btn-controller btn-modify"><a href="/admin/modify_income/<?php echo $income['income_id']; ?>/">修改</a></p>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<td><a href="/admin/income_detail/<?php echo $income['income_id']; ?>/"><?php echo $income['income_id']; ?></a></td>
						<td><?php echo $income['income_type_name']; ?></td>
						<td class="td-desc"><?php echo mb_strlen($income['income_desc']) > 22 ? (mb_substr($income['income_desc'], 0, 20) . '･･･') : $income['income_desc']; ?></td>
						<td><?php echo $income['income_price']; ?></td>
						<td><?php echo date('Y/m/d', strtotime($income['income_at'])); ?></td>
						<td><?php echo date('Y/m/d', strtotime($income['modified_at'])); ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
			<?php if($page_number > 1): ?>
			<ul class="ul-list-pager">
				<?php if($page > 1): ?>
				<li class="li-link long"><a href="/admin/income_list/<?php echo ($page - 1); ?>/<?php echo $get_params; ?>">上一页</a></li>
				<li class="li-link"><a href="/admin/income_list/<?php echo $get_params; ?>">1</a></li>
				<?php endif; ?>
				<?php if(($page - $page_link_max) > 2): ?>
				<li>...</li>
				<?php endif;?>
				<?php for($i = $page_link_max; $i >= 1; $i--): ?>
				<?php if(($page-$i) > 1): ?>
				<li class="li-link"><a href="/admin/income_list/<?php echo ($page - $i); ?>/<?php echo $get_params; ?>"><?php echo ($page - $i); ?></a></li>
				<?php endif; ?>
				<?php endfor; ?>
				<li class="active"><?php echo $page; ?></li>
				<?php for($i = 1; $i <= $page_link_max; $i++): ?>
				<?php if(($page + $i) < $page_number): ?>
				<li class="li-link"><a href="/admin/income_list/<?php echo ($page + $i); ?>/<?php echo $get_params; ?>"><?php echo ($page + $i); ?></a></li>
				<?php endif;?>
				<?php endfor; ?>
				<?php if(($page + $page_link_max) < ($page_number - 1)): ?>
				<li>...</li>
				<?php endif;?>
				<?php if($page < $page_number): ?>
				<li class="li-link"><a href="/admin/income_list/<?php echo $page_number; ?>/<?php echo $get_params; ?>"><?php echo $page_number; ?></a></li>
				<li class="li-link long"><a href="/admin/income_list/<?php echo ($page + 1); ?>/<?php echo $get_params; ?>">下一页</a></li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，未能查找到符合筛选条件的收入记录信息<br/>
				请确认筛选条件后重新进行筛选排序
			</p>
		</div>
		<?php endif; ?>
		
		<?php if($delete_able_flag): ?>
		<div class="popup-shadow"></div>
		
		<div class="popup-delete popup">
			<div class="popup-title">删除收入记录确认</div>
			<div class="popup-content center">
				<p>收入记录一经删除将无法还原，<br/>确定要删除「收入记录-<span class="popup-delete-id"></span>」吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_income/" method="post" id="form-delete">
					<input type="hidden" id="input-id" name="delete_id" value />
					<input type="hidden" name="page" value="income_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-delete-yes">确定</li>
					<li class="button-no" id="popup-delete-no">取消</li>
				</ul>
			</div>
		</div>
		
		<div class="popup-delete-checked popup">
			<div class="popup-title">删除收入记录确认</div>
			<div class="popup-content center">
				<p>收入记录一经删除将无法还原，<br/>确定要删除当前选中的所有收入记录吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_income_checked/" method="post" id="form-delete-checked">
					<?php foreach($income_list as $income): ?>
					<input type="checkbox" name="delete_id_checked[]" id="delete-id-checked-<?php echo $income['income_id']; ?>" value="<?php echo $income['income_id']; ?>">
					<?php endforeach; ?>
					<input type="hidden" name="page" value="income_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-delete-checked-yes">确定</li>
					<li class="button-no" id="popup-delete-checked-no">取消</li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	</div>
</body>
</html>
