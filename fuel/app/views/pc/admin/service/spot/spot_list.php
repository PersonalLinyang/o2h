<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>景点管理 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::css('pc/admin/service/spot/spot_list.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/spot/spot_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<?php if($edit_able_flag): ?>
				<li><a href="/admin/add_spot/">添加景点</a></li>
				<?php endif; ?>
				<?php if($delete_able_flag): ?>
				<li id="btn-content-menu-delete">删除选中景点</li>
				<?php endif; ?>
				<li class="btn-content-menu" id="btn-content-menu-select">筛选排序</li>
				<?php if($import_able_flag): ?>
				<li class="btn-content-menu" id="btn-content-menu-import">批量导入景点</li>
				<?php endif; ?>
				<?php if($export_able_flag): ?>
				<li class="btn-content-menu" id="btn-content-menu-export">导出景点列表</li>
				<?php endif; ?>
				<?php if($spot_type_able_flag): ?>
				<li><a href="/admin/spot_type_list/">景点类别管理</a></li>
				<?php endif; ?>
			</ul>
			
			<div class="content-menu-select content-menu-control-area" id="div-content-menu-select">
				<form action="/admin/spot_list/" method="get" id="form-content-menu-select">
					<table>
						<tr>
							<th rowspan="6" class="th-parent">筛选条件</th>
							<th>景点名</th>
							<td><input type="text" name="select_name" value="<?php echo $select_name; ?>" /></td>
						</tr>
						<tr>
							<th>公开状态</th>
							<td>
								<input type="checkbox" name="select_status[]" value="0" id="chb-select-status-0" <?php echo in_array('0', $select_status) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check <?php echo in_array('0', $select_status) ? ' active' : ''; ?>" for="chb-select-status-0">未公开</label>
								<input type="checkbox" name="select_status[]" value="1" id="chb-select-status-1" <?php echo in_array('1', $select_status) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check <?php echo in_array('1', $select_status) ? ' active' : ''; ?>" for="chb-select-status-1">公开</label>
							</td>
						</tr>
						<tr>
							<th>景点地区</th>
							<td>
								<?php foreach($area_list as $area): ?>
								<input type="checkbox" name="select_area[]" value="<?php echo $area['area_id']; ?>" id="chb-select-area-<?php echo $area['area_id']; ?>" <?php echo in_array($area['area_id'], $select_area) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check <?php echo in_array($area['area_id'], $select_area) ? ' active' : ''; ?>" for="chb-select-area-<?php echo $area['area_id']; ?>"><?php echo $area['area_name']; ?></label>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<th>景点类别</th>
							<td>
								<?php foreach($spot_type_list as $spot_type): ?>
								<input type="checkbox" name="select_spot_type[]" value="<?php echo $spot_type['spot_type_id']; ?>" id="chb-select-type-<?php echo $spot_type['spot_type_id']; ?>" <?php echo in_array($spot_type['spot_type_id'], $select_spot_type) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check<?php echo in_array($spot_type['spot_type_id'], $select_spot_type) ? ' active' : ''; ?>" for="chb-select-type-<?php echo $spot_type['spot_type_id']; ?>"><?php echo $spot_type['spot_type_name']; ?></label>
								<?php endforeach; ?>
							</td>
						</tr>
						<tr>
							<th>收费状况</th>
							<td>
								<input type="checkbox" name="select_free_flag[]" value="1" id="chk-free-flag-1" <?php echo in_array('1', $select_free_flag) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check<?php echo in_array('1', $select_free_flag) ? ' active' : ''; ?>" for="chk-free-flag-1">免费</label>
								<input type="checkbox" name="select_free_flag[]" value="0" id="chk-free-flag-0" <?php echo in_array('0', $select_free_flag) ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check<?php echo in_array('0', $select_free_flag) ? ' active' : ''; ?>" for="chk-free-flag-0" id="lbl-for-check-free-flag-0">收费</label>
								<p id="area-select-price"<?php echo in_array('0', $select_free_flag) ? ' class="active"' : ''; ?>>
									<span class="strong">价格</span> 
									<input type="text" name="select_price_min" class="price" value="<?php echo $select_price_min; ?>" />
									～
									<input type="text" name="select_price_max" class="price" value="<?php echo $select_price_max; ?>" />
									日元/人
								</p>
							</td>
						</tr>
						<tr>
							<th>登录者</th>
							<td>
								<input type="checkbox" name="select_self_flag" value="1" id="chb-select-self-flag" <?php echo $select_self_flag == 1 ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check<?php echo $select_self_flag == 1 ? ' active' : ''; ?>" for="chb-select-self-flag">仅显示由我登录的景点</label>
							</td>
						</tr>
					</table>
					<table>
						<tr>
							<th rowspan="2" class="th-parent">排序条件</th>
							<th>排序项目</th>
							<td>
								<input type="radio" name="sort_column" value="spot_name" id="rdb-sort-name" <?php echo $sort_column == "spot_name" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'spot_name' ? ' active' : ''; ?>" for="rdb-sort-name" data-for="rdb-sort-column">景点名</label>
								<input type="radio" name="sort_column" value="spot_status" id="rdb-sort-status" <?php echo $sort_column == "spot_status" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'spot_status' ? ' active' : ''; ?>" for="rdb-sort-status" data-for="rdb-sort-column">公开状况</label>
								<input type="radio" name="sort_column" value="spot_area" id="rdb-sort-area" <?php echo $sort_column == "spot_area" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'spot_area' ? ' active' : ''; ?>" for="rdb-sort-area" data-for="rdb-sort-column">景点地区</label>
								<input type="radio" name="sort_column" value="spot_type" id="rdb-sort-type" <?php echo $sort_column == "spot_type" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'spot_type' ? ' active' : ''; ?>" for="rdb-sort-type" data-for="rdb-sort-column">景点类型</label>
								<input type="radio" name="sort_column" value="spot_price" id="rdb-sort-price" <?php echo $sort_column == "spot_price" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'spot_price' ? ' active' : ''; ?>" for="rdb-sort-price" data-for="rdb-sort-column">收费状况</label>
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
					<li class="button"><a href="/admin/spot_list/">恢复初始</a></li>
					<li class="button-no" id="btn-content-menu-select-cancel">取消</li>
				</ul>
			</div>
			
			<?php if($import_able_flag): ?>
			<div class="content-menu-import content-menu-control-area" id="div-content-menu-import">
				<form action="/admin/import_spot/" method="post" id="form-content-menu-import" enctype="multipart/form-data">
					<div class="upload-area">
						<label>
							<input type="file" name="file_spot_list" accept=".xls,.xlsx" class="file-content-menu" />
							<p class="btn-upload">请上传写有要导入的景点信息的Excel文件</p>
						</label>
					</div>
					<input type="hidden" name="page" value="spot_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="btn-content-menu-import-submit">导入</li>
					<li class="button"><a href="/assets/xls/model/import_spot_model.xls" download>下载模板</a></li>
					<li class="button-no" id="btn-content-menu-import-cancel">取消</li>
				</ul>
			</div>
			<?php endif; ?>
			
			<?php if($export_able_flag): ?>
			<div class="content-menu-export content-menu-control-area" id="div-content-menu-export">
				<form action="/admin/export_spot/" method="post" id="form-content-menu-export" enctype="multipart/form-data">
					<input type="hidden" name="select_name" value="<?php echo $select_name; ?>" />
					<input type="hidden" name="select_status" value="<?php echo implode(',', $select_status); ?>" />
					<input type="hidden" name="select_area" value="<?php echo implode(',', $select_area); ?>" />
					<input type="hidden" name="select_spot_type" value="<?php echo implode(',', $select_spot_type); ?>" />
					<input type="hidden" name="select_free_flag" value="<?php echo implode(',', $select_free_flag); ?>" />
					<input type="hidden" name="select_price_min" value="<?php echo $select_price_min; ?>" />
					<input type="hidden" name="select_price_max" value="<?php echo $select_price_max; ?>" />
					<input type="hidden" name="select_self_flag" value="<?php echo $select_self_flag; ?>" />
					<input type="hidden" name="sort_column" value="<?php echo $sort_column; ?>" />
					<input type="hidden" name="sort_method" value="<?php echo $sort_method; ?>" />
					<input type="hidden" name="export_model" value="" id="hid-content-menu-export-model" />
					<input type="hidden" name="page" value="spot_list" />
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
		
		<?php if($spot_count): ?>
		<div class="content-main">
			<h1>景点一览</h1>
			<div class="div-content-list">
				<p>
					共为您检索到<span class="strong"><?php echo $spot_count; ?></span>条景点信息
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
						<th class="th-name">景点名</th>
						<th class="th-status">状态</th>
						<th class="th-area">景点地区</th>
						<th class="th-type">景点类别</th>
						<th class="th-price">收费状况</th>
						<th class="th-modified-at">最近更新</th>
					</tr>
					<?php foreach($spot_list as $spot): ?>
					<tr>
						<?php if($delete_able_flag): ?>
						<td>
							<?php if($user_id_self == $spot['created_by'] || $delete_other_able_flag): ?>
							<label class="lbl-for-check" for="delete-id-checked-<?php echo $spot['spot_id']; ?>"></label>
							<?php endif; ?>
						</td>
						<td>
							<?php if($user_id_self == $spot['created_by'] || $delete_other_able_flag): ?>
							<p class="btn-controller btn-delete" data-value="<?php echo $spot['spot_id']; ?>" data-name="<?php echo $spot['spot_name']; ?>">削除</p>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<?php if($edit_able_flag): ?>
						<td>
							<?php if($user_id_self == $spot['created_by'] || $edit_other_able_flag): ?>
							<p class="btn-controller btn-modify"><a href="/admin/modify_spot/<?php echo $spot['spot_id']; ?>/">修改</a></p>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<td><a href="/admin/spot_detail/<?php echo $spot['spot_id']; ?>/"><?php echo $spot['spot_name']; ?></a></td>
						<td><?php echo $spot['spot_status'] == '1' ? '公开' : '未公开'; ?></td>
						<td><?php echo $spot['spot_area_name']; ?></td>
						<td><?php echo $spot['spot_type_name']; ?></td>
						<td><?php echo $spot['free_flag'] == '1' ? '免费' : ($spot['spot_price'] . '日元/人'); ?></td>
						<td><?php echo date('Y年m月d日', strtotime($spot['modified_at'])); ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
			<?php if($page_number > 1): ?>
			<ul class="ul-list-pager">
				<?php if($page > 1): ?>
				<li class="li-link long"><a href="/admin/spot_list/<?php echo $page == 2 ? $get_params : (($page - 1) . '/' . $get_params); ?>">上一页</a></li>
				<li class="li-link"><a href="/admin/spot_list/<?php echo $get_params; ?>">1</a></li>
				<?php endif; ?>
				<?php if(($page - $page_link_max) > 2): ?>
				<li>...</li>
				<?php endif;?>
				<?php for($i = $page_link_max; $i >= 1; $i--): ?>
				<?php if(($page-$i) > 1): ?>
				<li class="li-link"><a href="/admin/spot_list/<?php echo ($page - $i); ?>/<?php echo $get_params; ?>"><?php echo ($page - $i); ?></a></li>
				<?php endif; ?>
				<?php endfor; ?>
				<li class="active"><?php echo $page; ?></li>
				<?php for($i = 1; $i <= $page_link_max; $i++): ?>
				<?php if(($page + $i) < $page_number): ?>
				<li class="li-link"><a href="/admin/spot_list/<?php echo ($page + $i); ?>/<?php echo $get_params; ?>"><?php echo ($page + $i); ?></a></li>
				<?php endif;?>
				<?php endfor; ?>
				<?php if(($page + $page_link_max) < ($page_number - 1)): ?>
				<li>...</li>
				<?php endif;?>
				<?php if($page < $page_number): ?>
				<li class="li-link"><a href="/admin/spot_list/<?php echo $page_number; ?>/<?php echo $get_params; ?>"><?php echo $page_number; ?></a></li>
				<li class="li-link long"><a href="/admin/spot_list/<?php echo ($page + 1); ?>/<?php echo $get_params; ?>">下一页</a></li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，未能找到符合条件的景点<br/>
				请确认条件后重新进行筛选排序
			</p>
		</div>
		<?php endif; ?>
		
		<?php if($delete_able_flag): ?>
		<div class="popup-shadow"></div>
		
		<div class="popup-delete popup">
			<div class="popup-title">景点删除确认</div>
			<div class="popup-content center">
				<p>景点一经删除将无法还原，<br/>确定要删除景点-「<span class="popup-delete-name"></span>」吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_spot/" method="post" id="form-delete">
					<input type="hidden" id="input-id" name="delete_id" value />
					<input type="hidden" name="page" value="spot_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-delete-yes">确定</li>
					<li class="button-no" id="popup-delete-no">取消</li>
				</ul>
			</div>
		</div>
		
		<div class="popup-delete-checked popup">
			<div class="popup-title">景点删除确认</div>
			<div class="popup-content center">
				<p>景点一经删除将无法还原，<br/>确定要删除当前选中的所有景点吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_spot_checked/" method="post" id="form-delete-checked">
					<?php foreach($spot_list as $spot): ?>
					<input type="checkbox" name="delete_id_checked[]" id="delete-id-checked-<?php echo $spot['spot_id']; ?>" value="<?php echo $spot['spot_id']; ?>">
					<?php endforeach; ?>
					<input type="hidden" name="page" value="spot_list" />
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
