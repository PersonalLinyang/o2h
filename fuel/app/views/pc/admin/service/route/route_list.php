<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>旅游路线管理 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::css('pc/admin/service/route/route_list.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/route/route_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<?php if($edit_able_flag): ?>
				<li><a href="/admin/add_route/">添加旅游路线</a></li>
				<?php endif; ?>
				<?php if($delete_able_flag): ?>
				<li id="btn-content-menu-delete-checked">删除选中旅游路线</li>
				<?php endif; ?>
				<li class="btn-content-menu" id="btn-content-menu-select">筛选排序</li>
				<?php if($import_able_flag): ?>
				<li class="btn-content-menu" id="btn-content-menu-import">批量导入旅游路线</li>
				<?php endif; ?>
				<?php if($export_able_flag): ?>
				<li class="btn-content-menu" id="btn-content-menu-export">导出旅游路线列表</li>
				<?php endif; ?>
			</ul>
			
			<div class="content-menu-select content-menu-control-area" id="div-content-menu-select">
				<form action="/admin/route_list/" method="get" id="form-content-menu-select">
					<table>
						<tr>
							<th rowspan="6" class="th-parent">筛选条件</th>
							<th>旅游路线名</th>
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
							<th>价格</th>
							<td>
								<input type="text" name="select_price_min" class="price" value="<?php echo $select_price_min; ?>" />～<input type="text" name="select_price_max" class="price" value="<?php echo $select_price_max; ?>" />元
							</td>
						</tr>
						<tr>
							<th>基本成本</th>
							<td>
								<input type="text" name="select_base_cost_min" class="price" value="<?php echo $select_base_cost_min; ?>" />～<input type="text" name="select_base_cost_max" class="price" value="<?php echo $select_base_cost_max; ?>" />元
							</td>
							<th>交通费</th>
							<td>
								<input type="text" name="select_traffic_cost_min" class="price" value="<?php echo $select_traffic_cost_min; ?>" />～<input type="text" name="select_traffic_cost_max" class="price" value="<?php echo $select_traffic_cost_max; ?>" />元
							</td>
						</tr>
						<tr>
							<th>停车费</th>
							<td>
								<input type="text" name="select_parking_cost_min" class="price" value="<?php echo $select_parking_cost_min; ?>" />～<input type="text" name="select_parking_cost_max" class="price" value="<?php echo $select_parking_cost_max; ?>" />元
							</td>
							<th>成本合计</th>
							<td>
								<input type="text" name="select_total_cost_min" class="price" value="<?php echo $select_total_cost_min; ?>" />～<input type="text" name="select_total_cost_max" class="price" value="<?php echo $select_total_cost_max; ?>" />元
							</td>
						</tr>
						<tr>
							<th>登录者</th>
							<td>
								<input type="checkbox" name="select_self_flag" value="1" id="chb-select-self-flag" <?php echo $select_self_flag == 1 ? 'checked ' : ''; ?>/>
								<label class="lbl-for-check<?php echo $select_self_flag == 1 ? ' active' : ''; ?>" for="chb-select-self-flag">仅显示由我登录的旅游路线</label>
							</td>
						</tr>
					</table>
					<table>
						<tr>
							<th rowspan="2" class="th-parent">排序条件</th>
							<th>排序项目</th>
							<td class="td-long">
								<input type="radio" name="sort_column" value="route_name" id="rdb-sort-name" <?php echo $sort_column == "route_name" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'route_name' ? ' active' : ''; ?>" for="rdb-sort-name" data-for="rdb-sort-column">旅游路线名</label>
								<input type="radio" name="sort_column" value="route_status" id="rdb-sort-status" <?php echo $sort_column == "route_status" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'route_status' ? ' active' : ''; ?>" for="rdb-sort-status" data-for="rdb-sort-column">公开状况</label>
								<input type="radio" name="sort_column" value="route_price_min" id="rdb-sort-price_min" <?php echo $sort_column == "route_price_min" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'route_price_min' ? ' active' : ''; ?>" for="rdb-sort-price_min" data-for="rdb-sort-column">低价格</label>
								<input type="radio" name="sort_column" value="route_price_max" id="rdb-sort-price_max" <?php echo $sort_column == "route_price_max" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'route_price_max' ? ' active' : ''; ?>" for="rdb-sort-price_max" data-for="rdb-sort-column">高价格</label>
								<input type="radio" name="sort_column" value="route_base_cost" id="rdb-sort-base-cost" <?php echo $sort_column == "route_base_cost" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'route_base_cost' ? ' active' : ''; ?>" for="rdb-sort-base-cost" data-for="rdb-sort-column">基本成本</label>
								<input type="radio" name="sort_column" value="route_traffic_cost" id="rdb-sort-traffic-cost" <?php echo $sort_column == "route_traffic_cost" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'route_traffic_cost' ? ' active' : ''; ?>" for="rdb-sort-traffic-cost" data-for="rdb-sort-column">交通费</label>
								<input type="radio" name="sort_column" value="route_parking_cost" id="rdb-sort-parking-cost" <?php echo $sort_column == "route_parking_cost" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'route_parking_cost' ? ' active' : ''; ?>" for="rdb-sort-parking-cost" data-for="rdb-sort-column">停车费</label>
								<input type="radio" name="sort_column" value="route_total_cost" id="rdb-sort-total-cost" <?php echo $sort_column == "route_total_cost" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'route_total_cost' ? ' active' : ''; ?>" for="rdb-sort-total-cost" data-for="rdb-sort-column">成本合计</label>
								<input type="radio" name="sort_column" value="created_at" id="rdb-sort-created-at" <?php echo $sort_column == "created_at" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'created_at' ? ' active' : ''; ?>" for="rdb-sort-created-at" data-for="rdb-sort-column">登录时间</label>
								<input type="radio" name="sort_column" value="modified_at" id="rdb-sort-modified-at" <?php echo $sort_column == "modified_at" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'modified_at' ? ' active' : ''; ?>" for="rdb-sort-modified-at" data-for="rdb-sort-column">最近更新</label>
								<input type="radio" name="sort_column" value="detail_day_number" id="rdb-sort-detail-day-number" <?php echo $sort_column == "detail_day_number" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'detail_day_number' ? ' active' : ''; ?>" for="rdb-sort-detail-day-number" data-for="rdb-sort-column">天数</label>
							</td>
						</tr>
						<tr>
							<th>排序方式</th>
							<td class="td-long">
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
					<li class="button"><a href="/admin/route_list/">恢复初始</a></li>
					<li class="button-no" id="btn-content-menu-select-cancel">取消</li>
				</ul>
			</div>
			
			<?php if($import_able_flag): ?>
			<div class="content-menu-import content-menu-control-area" id="div-content-menu-import">
				<form action="/admin/import_route/" method="post" id="form-content-menu-import" enctype="multipart/form-data">
					<div class="upload-area">
						<label>
							<input type="file" name="file_route_list" accept=".xls,.xlsx" class="file-content-menu" />
							<p class="btn-upload">请上传写有要导入的景点信息的Excel文件</p>
						</label>
					</div>
					<input type="hidden" name="page" value="route_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="btn-content-menu-import-submit">导入</li>
					<li class="button"><a href="/assets/xls/model/import_route_model.xls" download>下载模板</a></li>
					<li class="button-no" id="btn-content-menu-import-cancel">取消</li>
				</ul>
			</div>
			<?php endif; ?>
			
			<?php if($export_able_flag): ?>
			<div class="content-menu-export content-menu-control-area" id="div-content-menu-export">
				<form action="/admin/export_route/" method="post" id="form-content-menu-export" enctype="multipart/form-data">
					<input type="hidden" name="select_name" value="<?php echo $select_name; ?>" />
					<input type="hidden" name="select_status" value="<?php echo implode(',', $select_status); ?>" />
					<input type="hidden" name="select_price_min" value="<?php echo $select_price_min; ?>" />
					<input type="hidden" name="select_price_max" value="<?php echo $select_price_max; ?>" />
					<input type="hidden" name="select_base_cost_min" value="<?php echo $select_base_cost_min; ?>" />
					<input type="hidden" name="select_base_cost_max" value="<?php echo $select_base_cost_max; ?>" />
					<input type="hidden" name="select_traffic_cost_min" value="<?php echo $select_traffic_cost_min; ?>" />
					<input type="hidden" name="select_traffic_cost_max" value="<?php echo $select_traffic_cost_max; ?>" />
					<input type="hidden" name="select_parking_cost_min" value="<?php echo $select_parking_cost_min; ?>" />
					<input type="hidden" name="select_parking_cost_max" value="<?php echo $select_parking_cost_max; ?>" />
					<input type="hidden" name="select_total_cost_min" value="<?php echo $select_total_cost_min; ?>" />
					<input type="hidden" name="select_total_cost_max" value="<?php echo $select_total_cost_max; ?>" />
					<input type="hidden" name="select_self_flag" value="<?php echo $select_self_flag; ?>" />
					<input type="hidden" name="sort_column" value="<?php echo $sort_column; ?>" />
					<input type="hidden" name="sort_method" value="<?php echo $sort_method; ?>" />
					<input type="hidden" name="export_model" value="" id="hid-content-menu-export-model" />
					<input type="hidden" name="page" value="route_list" />
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
		
		<?php if($route_count): ?>
		<div class="content-main">
			<h1>旅游路线一览</h1>
			<div class="div-content-list">
				<p>
					共为您检索到<span class="strong"><?php echo $route_count; ?></span>条旅游路线信息
					目前显示的是其中的第<span class="strong"><?php echo $start_number; ?></span><?php if($start_number != $end_number): ?>～<span class="strong"><?php echo $end_number; ?></span><?php endif; ?>条
				</p>
				<table class="tb-content-list">
					<tr>
						<?php if($delete_able_flag): ?>
						<th class="th-check" rowspan="2"></th>
						<th class="th-delete" rowspan="2"></th>
						<?php endif; ?>
						<?php if($edit_able_flag): ?>
						<th class="th-modify" rowspan="2"></th>
						<?php endif; ?>
						<th class="th-name" rowspan="2">旅游路线名</th>
						<th class="th-status" rowspan="2">状态</th>
						<th class="th-price" rowspan="2">价格<br>(元)</th>
						<th colspan="3">成本(元)</th>
						<th class="th-day" rowspan="2">天数</th>
						<th class="th-date" rowspan="2">最近更新</th>
					</tr>
					<tr>
						<th class="th-cost">基本成本</th>
						<th class="th-cost">交通费</th>
						<th class="th-cost">停车费</th>
					</tr>
					<?php foreach($route_list as $route): ?>
					<tr>
						<?php if($delete_able_flag): ?>
						<td rowspan="2">
							<?php if($user_id_self == $route['created_by'] || $delete_other_able_flag): ?>
							<label class="lbl-for-check" for="delete-id-checked-<?php echo $route['route_id']; ?>"></label>
							<?php endif; ?>
						</td>
						<td rowspan="2">
							<?php if($user_id_self == $route['created_by'] || $delete_other_able_flag): ?>
							<p class="btn-controller btn-delete" data-value="<?php echo $route['route_id']; ?>" data-name="<?php echo $route['route_name']; ?>">削除</p>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<?php if($edit_able_flag): ?>
						<td rowspan="2">
							<?php if($user_id_self == $route['created_by'] || $edit_other_able_flag): ?>
							<p class="btn-controller btn-modify"><a href="/admin/modify_route/<?php echo $route['route_id']; ?>/">修改</a></p>
							<?php endif; ?>
						</td>
						<?php endif; ?>
						<td rowspan="2"><a href="/admin/route_detail/<?php echo $route['route_id']; ?>/"><?php echo $route['route_name']; ?></a></td>
						<td rowspan="2"><?php echo $route['route_status'] == '1' ? '公开' : '未公开'; ?></td>
						<td rowspan="2"><?php echo ($route['route_price_min'] || $route['route_price_max']) ? ($route['route_price_min'] . '～<br>' . $route['route_price_max']) : ''; ?></td>
						<td><?php echo $route['route_base_cost']; ?></td>
						<td><?php echo $route['route_traffic_cost']; ?></td>
						<td><?php echo $route['route_parking_cost']; ?></td>
						<td rowspan="2"><?php echo $route['detail_day_number']; ?></td>
						<td rowspan="2"><?php echo date('y年m月d日', strtotime($route['modified_at'])); ?></td>
					</tr>
					<tr>
						<td colspan="3">合计：<?php echo $route['route_total_cost']; ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
			<?php if($page_number > 1): ?>
			<ul class="ul-list-pager">
				<?php if($page > 1): ?>
				<li class="li-link long"><a href="/admin/route_list/<?php echo ($page - 1); ?>/<?php echo $get_params; ?>">上一页</a></li>
				<li class="li-link"><a href="/admin/route_list/<?php echo $get_params; ?>">1</a></li>
				<?php endif; ?>
				<?php if(($page - $page_link_max) > 2): ?>
				<li>...</li>
				<?php endif;?>
				<?php for($i = $page_link_max; $i >= 1; $i--): ?>
				<?php if(($page-$i) > 1): ?>
				<li class="li-link"><a href="/admin/route_list/<?php echo ($page - $i); ?>/<?php echo $get_params; ?>"><?php echo ($page - $i); ?></a></li>
				<?php endif; ?>
				<?php endfor; ?>
				<li class="active"><?php echo $page; ?></li>
				<?php for($i = 1; $i <= $page_link_max; $i++): ?>
				<?php if(($page + $i) < $page_number): ?>
				<li class="li-link"><a href="/admin/route_list/<?php echo ($page + $i); ?>/<?php echo $get_params; ?>"><?php echo ($page + $i); ?></a></li>
				<?php endif;?>
				<?php endfor; ?>
				<?php if(($page + $page_link_max) < ($page_number - 1)): ?>
				<li>...</li>
				<?php endif;?>
				<?php if($page < $page_number): ?>
				<li class="li-link"><a href="/admin/route_list/<?php echo $page_number; ?>/<?php echo $get_params; ?>"><?php echo $page_number; ?></a></li>
				<li class="li-link long"><a href="/admin/route_list/<?php echo ($page + 1); ?>/<?php echo $get_params; ?>">下一页</a></li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<div class="content-main">
			<p class="error-icon">！</p>
			<p class="error-text">
				对不起，未能查找到符合条件的旅游路线信息<br/>
				请确认筛选条件后重新进行筛选排序
			</p>
		</div>
		<?php endif; ?>
		
		<?php if($delete_able_flag): ?>
		<div class="popup-shadow"></div>
		
		<div class="popup-delete popup">
			<div class="popup-title">删除旅游路线确认</div>
			<div class="popup-content center">
				<p>旅游路线一经删除将无法还原，<br/>确定要删除旅游路线-「<span class="popup-delete-name"></span>」吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_route/" method="post" id="form-delete">
					<input type="hidden" id="input-id" name="delete_id" value />
					<input type="hidden" name="page" value="route_list" />
				</form>
				<ul class="button-group">
					<li class="button-yes" id="popup-delete-yes">确定</li>
					<li class="button-no" id="popup-delete-no">取消</li>
				</ul>
			</div>
		</div>
		
		<div class="popup-delete-checked popup">
			<div class="popup-title">删除旅游路线确认</div>
			<div class="popup-content center">
				<p>旅游路线一经删除将无法还原，<br/>确定要删除当前选中的所有旅游路线吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_route_checked/" method="post" id="form-delete-checked">
					<?php foreach($route_list as $route): ?>
					<input type="checkbox" name="delete_id_checked[]" id="delete-id-checked-<?php echo $route['route_id']; ?>" value="<?php echo $route['route_id']; ?>">
					<?php endforeach; ?>
					<input type="hidden" name="page" value="route_list" />
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
