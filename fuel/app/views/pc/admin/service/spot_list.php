<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>景点管理 - O2H管理系统</title>
	<?php echo Asset::css('pc/admin/common.css'); ?>
	<?php echo Asset::css('pc/admin/service/spot_list.css'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
	<?php echo Asset::js('pc/admin/common.js'); ?>
	<?php echo Asset::js('pc/admin/service/spot_list.js'); ?>
</head>
<body class="body-common">
	<?php echo $header; ?>
	<div class="content-area">
		<div class="content-menu">
			<ul class="content-menu-list">
				<li class="content-menu-button"><a href="/admin/add_spot/">添加景点</a></li>
				<li class="content-menu-button">筛选排序</li>
			</ul>
			<div>
				<table>
					<tr>
						<th rowspan="4">筛选条件</th>
						<th>景点名</th>
						<td><input type="text" name="select_name" /></td>
					</tr>
					<tr>
						<th>景点所属地区</th>
						<td>
							<?php foreach($area_list as $area): ?>
							<input type="checkbox" name="select_area" value="<?php echo $area['area_id']; ?>" id="chb-select-area-<?php echo $area['area_id']; ?>" />
							<label for="chb-select-area-<?php echo $area['area_id']; ?>"><?php echo $area['area_name']; ?></label>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th>景点类别</th>
						<td>
							<?php foreach($spot_type_list as $spot_type): ?>
							<input type="checkbox" name="select_spot_type" value="<?php echo $spot_type['spot_type_id']; ?>" id="chb-select-type-<?php echo $spot_type['spot_type_id']; ?>" />
							<label for="chb-select-type-<?php echo $spot_type['spot_type_id']; ?>"><?php echo $spot_type['spot_type_name']; ?></label>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th>收费状况</th>
						<td>
							<input type="checkbox" name="select_free_flag" value="0" id="chk-free-flag-0" />
							<label for="chk-free-flag-0">收费</label>
							<input type="checkbox" name="select_free_flag" value="1" id="chk-free-flag-1" />
							<label for="chk-free-flag-1">免费</label>
							<span>票价 <input type="number" name="select_price" />～<input type="number" name="select_price" />元</span>
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<th rowspan="2">排序条件</th>
						<th>排序项目</th>
						<td>
							<input type="radio" name="sort-column" value="name" id="rdb-sort-name" />
							<label for="rdb-sort-name">景点名</label>
							<input type="radio" name="sort-column" value="area" id="rdb-sort-area" />
							<label for="rdb-sort-area">景点所属地区</label>
							<input type="radio" name="sort-column" value="type" id="rdb-sort-type" />
							<label for="rdb-sort-type">景点类型</label>
							<input type="radio" name="sort-column" value="price" id="rdb-sort-price" />
							<label for="rdb-sort-price">收费状况</label>
							<input type="radio" name="sort-column" value="modified_at" id="rdb-sort-modified-at" />
							<label for="rdb-sort-modified-at">更新日</label>
							<input type="radio" name="sort-column" value="detail" id="rdb-sort-detail" />
							<label for="rdb-sort-detail">详情数</label>
						</td>
					</tr>
					<tr>
						<th>排序方式</th>
						<td>
							<input type="radio" name="sort-method" value="asc" id="rdb-sort-asc" />
							<label for="rdb-sort-asc">升序</label>
							<input type="radio" name="sort-method" value="desc" id="rdb-sort-desc" />
							<label for="rdb-sort-desc">降序</label>
						</td>
					</tr>
				</table>
				<ul class="button-group">
					<li class="button-yes" id="">确定</li>
					<li class="button-no" id="">取消</li>
				</ul>
			</div>
		</div>
		
		<?php if($success_message): ?>
		<div class="content-success"><?php echo $success_message; ?></div>
		<?php endif; ?>
		
		<?php if($error_message): ?>
		<div class="content-error"><?php echo $error_message; ?></div>
		<?php endif; ?>
		
		<div class="content-main">
			<h1>景点一览</h1>
			<p>共为您检索到<span class="strong"><?php echo count($spot_list); ?></span>条景点信息 </p>
			<div>
				<table class="tb-content-list">
					<tr>
						<th class="th-delete"></th>
						<th class="th-name">景点名</th>
						<th class="th-area">景点所属地区</th>
						<th class="th-type">景点类别</th>
						<th class="th-price">收费状况</th>
						<th class="th-modified-at">更新日</th>
						<th class="th-detail">详情数</th>
					</tr>
					<?php foreach($spot_list as $spot): ?>
					<tr>
						<td><p class="btn-controller btn-delete" data-value="<?php echo $spot['spot_id']; ?>" data-name="<?php echo $spot['spot_name']; ?>">削除</p></td>
						<td><a href="/admin/spot_detail/<?php echo $spot['spot_id']; ?>/"><?php echo $spot['spot_name']; ?></a></td>
						<td><?php echo $spot['spot_area_name']; ?></td>
						<td><?php echo $spot['spot_type_name']; ?></td>
						<td><?php echo $spot['free_flag'] == '1' ? '免费' : ($spot['price'] ? '票价:' . $spot['price'] . '元' : '收费 未设定票价'); ?></td>
						<td><?php echo date('Y/m/d', strtotime($spot['modified_at'])); ?></td>
						<td><?php echo $spot['detail_number']; ?>个</td>
					</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
		
		<div class="popup-shadow"></div>
		
		<div class="popup-delete popup">
			<div class="popup-title">删除景点确认</div>
			<div class="popup-content center">
				<p>景点一经删除将无法还原，<br/>确定要删除「景点-<span class="popup-delete-name"></span>」吗？</p>
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
	</div>
</body>
</html>
