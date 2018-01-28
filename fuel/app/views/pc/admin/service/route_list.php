<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>路线管理 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/error.css'); ?>
	<?php echo Asset::css('pc/admin/service/route_list.css'); ?>
	<?php echo Asset::js('common/jquery-1.9.1.min.js'); ?>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/route_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li><a href="/admin/add_route/">添加路线</a></li>
				<li id="btn-content-menu-delete-checked">删除选中路线</li>
				<li id="btn-content-menu-select">筛选排序</li>
				<li><a href="/admin/route_type_list/">路线类别管理</a></li>
			</ul>
			<div class="content-menu-select" id="div-content-menu-select">
				<form action="/admin/route_list/" method="get" id="form-content-menu-select">
					<table>
						<tr>
							<th rowspan="5" class="th-parent">筛选条件</th>
							<th>路线名</th>
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
					</table>
					<table>
						<tr>
							<th rowspan="2" class="th-parent">排序条件</th>
							<th>排序项目</th>
							<td class="long">
								<input type="radio" name="sort_column" value="route_name" id="rdb-sort-name" <?php echo $sort_column == "route_name" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'route_name' ? ' active' : ''; ?>" for="rdb-sort-name" data-for="rdb-sort-column">路线名</label>
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
								<label class="lbl-for-radio<?php echo $sort_column == 'created_at' ? ' active' : ''; ?>" for="rdb-sort-created-at" data-for="rdb-sort-column">登录日</label>
								<input type="radio" name="sort_column" value="modified_at" id="rdb-sort-modified-at" <?php echo $sort_column == "modified_at" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'modified_at' ? ' active' : ''; ?>" for="rdb-sort-modified-at" data-for="rdb-sort-column">更新日</label>
								<input type="radio" name="sort_column" value="detail_day_number" id="rdb-sort-detail-day-number" <?php echo $sort_column == "detail_day_number" ? 'checked ' : ''; ?>/>
								<label class="lbl-for-radio<?php echo $sort_column == 'detail_day_number' ? ' active' : ''; ?>" for="rdb-sort-detail-day-number" data-for="rdb-sort-column">天数</label>
							</td>
						</tr>
						<tr>
							<th>排序方式</th>
							<td class="long">
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
					<li class="button-yes"><a href="/admin/route_list/">恢复初始</a></li>
					<li class="button-no" id="btn-content-menu-select-cancel">取消</li>
				</ul>
			</div>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<?php if($route_count): ?>
		<div class="content-main">
			<h1>路线一览</h1>
			<div class="div-content-list">
				<p>
					共为您检索到<span class="strong"><?php echo $route_count; ?></span>条路线信息
					目前显示的是其中的第<span class="strong"><?php echo $start_number; ?></span>～<span class="strong"><?php echo $end_number; ?></span>条
				</p>
				<table class="tb-content-list">
					<tr>
						<th class="th-check"></th>
						<th class="th-delete"></th>
						<th class="th-modify"></th>
						<th class="th-name">路线名</th>
						<th class="th-status">状态</th>
						<th class="th-price">价格<br>(元)</th>
						<th class="th-cost">基本成本<br>(元)</th>
						<th class="th-cost">交通费<br>(元)</th>
						<th class="th-cost">停车费<br>(元)</th>
						<th class="th-cost">成本合计<br>(元)</th>
						<th class="th-day">天数</th>
						<th class="th-date">登录日</th>
						<th class="th-date">更新日</th>
					</tr>
					<?php foreach($route_list as $route): ?>
					<tr>
						<td><label class="lbl-for-check" for="delete-id-checked-<?php echo $route['route_id']; ?>"></label></td>
						<td><p class="btn-controller btn-delete" data-value="<?php echo $route['route_id']; ?>" data-name="<?php echo $route['route_name']; ?>">削除</p></td>
						<td><p class="btn-controller btn-modify"><a href="/admin/modify_route/<?php echo $route['route_id']; ?>/">修改</a></p></td>
						<td><a href="/admin/route_detail/<?php echo $route['route_id']; ?>/"><?php echo $route['route_name']; ?></a></td>
						<td><?php echo $route['route_status'] == '1' ? '公开' : '未公开'; ?></td>
						<td><?php echo ($route['route_price_min'] || $route['route_price_max']) ? ($route['route_price_min'] . '～' . $route['route_price_max']) : ''; ?></td>
						<td><?php echo $route['route_base_cost']; ?></td>
						<td><?php echo $route['route_traffic_cost']; ?></td>
						<td><?php echo $route['route_parking_cost']; ?></td>
						<td><?php echo $route['route_total_cost']; ?></td>
						<td><?php echo $route['detail_day_number']; ?></td>
						<td><?php echo date('y/m/d', strtotime($route['created_at'])); ?></td>
						<td><?php echo date('y/m/d', strtotime($route['modified_at'])); ?></td>
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
				对不起，未能查找到符合筛选条件的路线信息<br/>
				请确认筛选条件后重新进行筛选排序
			</p>
		</div>
		<?php endif; ?>
		
		<div class="popup-shadow"></div>
		
		<div class="popup-delete popup">
			<div class="popup-title">删除路线确认</div>
			<div class="popup-content center">
				<p>路线一经删除将无法还原，<br/>当路线被删除时，使用该路线的路线及客户信息中的相关信息也将被同时清除，<br/>确定要删除「路线-<span class="popup-delete-name"></span>」吗？</p>
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
			<div class="popup-title">删除路线确认</div>
			<div class="popup-content center">
				<p>路线一经删除将无法还原，<br/>当路线被删除时，使用该路线的路线及客户信息中的相关信息也将被同时清除，<br/>确定要删除当前选中的所有路线吗？</p>
			</div>
			<div class="popup-controller">
				<form action="/admin/delete_checked_route/" method="post" id="form-delete-checked">
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
	</div>
</body>
</html>
