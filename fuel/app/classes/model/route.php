<?php

class Model_Route extends Model
{
	
	/*
	 * 添加路线
	 */
	public static function InsertRoute($params) {
		//添加路线
		$sql_insert_route = "INSERT INTO t_route(route_name, route_description, route_price_min, route_price_max, route_base_cost, route_traffic_cost, route_parking_cost, route_status, created_at, modified_at) "
						. "VALUES(:route_name, :route_description, :route_price_min, :route_price_max, :route_base_cost, :route_traffic_cost, :route_parking_cost, :route_status, now(), now())";
		$query_insert_route = DB::query($sql_insert_route);
		$query_insert_route->param(':route_name', $params['route_name']);
		$query_insert_route->param(':route_description', $params['route_description']);
		$query_insert_route->param(':route_price_min', $params['route_price_min']);
		$query_insert_route->param(':route_price_max', $params['route_price_max']);
		$query_insert_route->param(':route_base_cost', $params['route_base_cost']);
		$query_insert_route->param(':route_traffic_cost', $params['route_traffic_cost']);
		$query_insert_route->param(':route_parking_cost', $params['route_parking_cost']);
		$query_insert_route->param(':route_status', $params['route_status']);
		$result_insert_route = $query_insert_route->execute();
		
		if($result_insert_route) {
			//添加详细日程
			$route_id = intval($result_insert_route[0]);
			foreach($params['detail_list'] as $detail) {
				$sql_insert_detail = "INSERT INTO t_route_detail(route_id, route_detail_day, route_detail_title, route_detail_content, " 
									. "route_spot_list, route_breakfast, route_lunch, route_dinner, route_hotel) "
									. "VALUES(:route_id, :route_detail_day, :route_detail_title, :route_detail_content, " 
									. ":route_spot_list, :route_breakfast, :route_lunch, :route_dinner, :route_hotel)";
				$query_insert_detail = DB::query($sql_insert_detail);
				$query_insert_detail->param(':route_id', $route_id);
				$query_insert_detail->param(':route_detail_day', $detail['route_detail_day']);
				$query_insert_detail->param(':route_detail_title', $detail['route_detail_title']);
				$query_insert_detail->param(':route_detail_content', $detail['route_detail_content']);
				$query_insert_detail->param(':route_spot_list', implode(',', $detail['route_spot_list']));
				$query_insert_detail->param(':route_breakfast', $detail['route_breakfast']);
				$query_insert_detail->param(':route_lunch', $detail['route_lunch']);
				$query_insert_detail->param(':route_dinner', $detail['route_dinner']);
				$query_insert_detail->param(':route_hotel', $detail['route_hotel']);
				$result_insert_detail = $query_insert_detail->execute();
			}
		}
		
		return $result_insert_route;
	}
	
	/*
	 * 根据ID删除路线
	 */
	public static function DeleteRouteById($route_id) {
		$sql_delete_detail = "DELETE FROM t_route_detail WHERE route_id = :route_id";
		$query_delete_detail = DB::query($sql_delete_detail);
		$query_delete_detail->param(':route_id', $route_id);
		$result_delete_detail = $query_delete_detail->execute();
		
		$sql_delete_route = "DELETE FROM t_route WHERE route_id = :route_id";
		$query_delete_route = DB::query($sql_delete_route);
		$query_delete_route->param(':route_id', $route_id);
		$result_delete_route = $query_delete_route->execute();
		
		return $result_delete_route;
	}
	
	/*
	 * 根据ID删除路线(批量)
	 */
	public static function DeleteRouteByIdList($route_id_list) {
		$sql_where_list = array();
		$sql_param_list = array();
		foreach($route_id_list as $route_id_counter => $route_id) {
			$sql_where_list[] = ':route_id_' . $route_id_counter;
			$sql_param_list[':route_id_' . $route_id_counter] = $route_id;
		}
		$sql_where = implode(', ', $sql_where_list);
		
		$sql_delete_detail = "DELETE FROM t_route_detail WHERE route_id IN (" . $sql_where . ")";
		$query_delete_detail = DB::query($sql_delete_detail);
		foreach($sql_param_list as $key => $value) {
			$query_delete_detail->param($key, $value);
		}
		$result_delete_detail = $query_delete_detail->execute();
		
		$sql_delete_route = "DELETE FROM t_route WHERE route_id IN (" . $sql_where . ")";
		$query_delete_route = DB::query($sql_delete_route);
		foreach($sql_param_list as $key => $value) {
			$query_delete_route->param($key, $value);
		}
		$result_delete_route = $query_delete_route->execute();
		
		return $result_delete_route;
	}
	
	/*
	 * 更新路线
	 */
	public static function UpdateRoute($params) {
		//更新路线
		$sql_update_route = "UPDATE t_route "
						. "SET route_name=:route_name, route_description=:route_description, route_price_min=:route_price_min, route_price_max=:route_price_max, "
						. "route_base_cost=:route_base_cost, route_traffic_cost=:route_traffic_cost, route_parking_cost=:route_parking_cost, route_status=:route_status, modified_at=now() "
						. "WHERE route_id=:route_id";
		$query_update_route = DB::query($sql_update_route);
		$query_update_route->param(':route_id', $params['route_id']);
		$query_update_route->param(':route_name', $params['route_name']);
		$query_update_route->param(':route_description', $params['route_description']);
		$query_update_route->param(':route_price_min', $params['route_price_min']);
		$query_update_route->param(':route_price_max', $params['route_price_max']);
		$query_update_route->param(':route_base_cost', $params['route_base_cost']);
		$query_update_route->param(':route_traffic_cost', $params['route_traffic_cost']);
		$query_update_route->param(':route_parking_cost', $params['route_parking_cost']);
		$query_update_route->param(':route_status', $params['route_status']);
		$result_update_route = $query_update_route->execute();
		
		if($result_update_route) {
			//删除原有详细日程
			$sql_delete_detail = "DELETE FROM t_route_detail WHERE route_id=:route_id";
			$query_delete_detail = DB::query($sql_delete_detail);
			$query_delete_detail->param(':route_id', $params['route_id']);
			$result_delete_detail = $query_delete_detail->execute();
			
			//更新详细日程
			foreach($params['detail_list'] as $detail) {
				$sql_update_detail = "INSERT INTO t_route_detail(route_id, route_detail_day, route_detail_title, route_detail_content, " 
									. "route_spot_list, route_breakfast, route_lunch, route_dinner, route_hotel) "
									. "VALUES(:route_id, :route_detail_day, :route_detail_title, :route_detail_content, " 
									. ":route_spot_list, :route_breakfast, :route_lunch, :route_dinner, :route_hotel)";
				$query_update_detail = DB::query($sql_update_detail);
				$query_update_detail->param(':route_id', $params['route_id']);
				$query_update_detail->param(':route_detail_day', $detail['route_detail_day']);
				$query_update_detail->param(':route_detail_title', $detail['route_detail_title']);
				$query_update_detail->param(':route_detail_content', $detail['route_detail_content']);
				$query_update_detail->param(':route_spot_list', implode(',', $detail['route_spot_list']));
				$query_update_detail->param(':route_breakfast', $detail['route_breakfast']);
				$query_update_detail->param(':route_lunch', $detail['route_lunch']);
				$query_update_detail->param(':route_dinner', $detail['route_dinner']);
				$query_update_detail->param(':route_hotel', $detail['route_hotel']);
				$result_update_detail = $query_update_detail->execute();
			}
		}
		
		return $result_update_route;
	}
	
	/*
	 * 更新路线状态
	 */
	public static function UpdateRouteStatusById($params) {
		$sql_update = "UPDATE t_route SET route_status = :route_status WHERE route_id = :route_id";
		$query_update = DB::query($sql_update);
		$query_update->param(':route_id', $params['route_id']);
		$query_update->param(':route_status', $params['route_status']);
		$result_update = $query_update->execute();
		
		return $result_update;
	}
	
	/*
	 * 获取全部旅游路线简易信息列表
	 */
	public static function GetRouteSimpleListAll() {
		$sql = "SELECT route_id, route_name FROM t_route WHERE route_status = 1";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 获取全部有效公开旅游路线简易信息列表
	 */
	public static function GetActiveRouteSimpleListAll() {
		$sql = "SELECT route_id, route_name FROM t_route WHERE delete_flag = 0 AND route_status = 1";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查旅游路线ID是否有效公开
	 */
	public static function CheckActiveRouteId($route_id) {
		$sql = "SELECT * FROM t_user WHERE route_id = :route_id AND delete_flag = 0 AND route_status = 1";
		$query = DB::query($sql);
		$query->param(':route_id', $route_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * 按条件获得路线列表
	 */
	public static function SelectRouteList($params) {
		$sql_where = "";
		$sql_order_column = "created_at";
		$sql_order_method = "desc";
		$sql_params = array();
		$sql_offset = 0;
		$sql_limit = 20;
		foreach($params as $key => $value) {
			switch($key) {
				case 'route_name':
					$sql_where_list_name = array();
					foreach($value as $name_counter => $name) {
						$sql_where_list_name[] = "tr.route_name LIKE :route_name_" . $name_counter;
						$sql_params[':route_name_' . $name_counter] = '%' . $name . '%';
					}
					if(count($sql_where_list_name)) {
						$sql_where .= " AND (" . implode(' OR ', $sql_where_list_name) . ") ";
					}
					break;
				case 'route_status':
					$sql_where_list_status = array();
					foreach($value as $status_counter => $status) {
						if(is_numeric($status)) {
							$sql_where_list_status[] = ":route_status_" . $status_counter;
							$sql_params[':route_status_' . $status_counter] = intval($status);
						}
					}
					if(count($sql_where_list_status)) {
						$sql_where .= " AND tr.route_status IN (" . implode(', ', $sql_where_list_status) . ") ";
					}
					break;
				case 'price_min':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.route_price_max >= :price_min ";
						$sql_params[':price_min'] = floatval($value);
					}
					break;
				case 'price_max':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.route_price_min <= :price_max ";
						$sql_params[':price_max'] = floatval($value);
					}
					break;
				case 'base_cost_min':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.route_base_cost >= :base_cost_min ";
						$sql_params[':base_cost_min'] = floatval($value);
					}
					break;
				case 'base_cost_max':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.route_base_cost <= :base_cost_max ";
						$sql_params[':base_cost_max'] = floatval($value);
					}
					break;
				case 'traffic_cost_min':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.route_traffic_cost >= :traffic_cost_min ";
						$sql_params[':traffic_cost_min'] = floatval($value);
					}
					break;
				case 'traffic_cost_max':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.route_traffic_cost <= :traffic_cost_max ";
						$sql_params[':traffic_cost_max'] = floatval($value);
					}
					break;
				case 'parking_cost_min':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.route_parking_cost >= :parking_cost_min ";
						$sql_params[':parking_cost_min'] = floatval($value);
					}
					break;
				case 'parking_cost_max':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.route_parking_cost <= :parking_cost_max ";
						$sql_params[':parking_cost_max'] = floatval($value);
					}
					break;
				case 'total_cost_min':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.route_total_cost >= :total_cost_min ";
						$sql_params[':total_cost_min'] = floatval($value);
					}
					break;
				case 'total_cost_max':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.route_total_cost <= :total_cost_max ";
						$sql_params[':total_cost_max'] = floatval($value);
					}
					break;
				case 'sort_column':
					$sort_column_list = array(
						'route_name', 'route_status', 'route_price_min', 'route_price_max', 'route_base_cost', 'route_traffic_cost', 
						'route_parking_cost', 'route_total_cost', 'created_at', 'modified_at', 'detail_day_number'
					);
					if(in_array($value, $sort_column_list)) {
						$sql_order_column = $value;
					}
					break;
				case 'sort_method':
					if(in_array($value, array('asc', 'desc'))) {
						$sql_order_method = $value;
					}
					break;
				case 'page':
					if(is_numeric($value)) {
						$num_per_page = $sql_limit;
						if(isset($params['num_per_page'])) {
							if(is_numeric($params['num_per_page'])) {
								$num_per_page = intval($params['num_per_page']);
							}
						}
						$sql_offset = (intval($value) - 1) * $num_per_page;
					}
					break;
				case 'num_per_page':
					if(is_numeric($value)) {
						$sql_limit = intval($value);
					}
					break;
				case '':
					break;
			}
		}

		$sql_count = "SELECT COUNT(DISTINCT tr.route_id) route_count FROM t_route tr LEFT JOIN t_route_detail trd ON tr.route_id = trd.route_id WHERE 1=1 " . $sql_where;
		$query_count = DB::query($sql_count);
		foreach ($sql_params as $key => $value) {
			$query_count->param($key, $value);
		}
		$result_count = $query_count->execute()->as_array();

		if(count($result_count)) {
			$route_count = intval($result_count[0]['route_count']);

			if($route_count) {
				$sql_select = "SELECT tr.route_id, tr.route_name, tr.route_status, tr.route_price_min, tr.route_price_max, tr.route_base_cost, " 
						. "tr.route_traffic_cost, tr.route_parking_cost, tr.route_base_cost+tr.route_traffic_cost+tr.route_parking_cost route_total_cost, " 
						. "count(trd.route_detail_day) detail_day_number, tr.created_at, tr.modified_at " 
						. "FROM t_route tr " 
						. "LEFT JOIN t_route_detail trd ON tr.route_id = trd.route_id "
						. "WHERE 1=1 " . $sql_where
						. "GROUP BY route_id "
						. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
						. "LIMIT " . $sql_limit . " OFFSET " . $sql_offset;
				$query_select = DB::query($sql_select);
				foreach ($sql_params as $key => $value) {
					$query_select->param($key, $value);
				}
				$result_select = $query_select->execute()->as_array();
				
				if(count($result_select)) {
					$result = array(
						'route_count' => $route_count,
						'route_list' => $result_select,
						'start_number' => $sql_offset + 1,
						'end_number' => count($result_select) + $sql_offset,
					);
					return $result;
				}
			}
		}

		return false;
	}
	
	/*
	 * 根据ID获取路线详细信息
	 */
	public static function SelectRouteInfoByRouteId($route_id) {
		if(!is_numeric($route_id)) {
			return false;
		}
		
		$sql_route = "SELECT route_id, route_name, route_description, route_price_min, route_price_max, " 
				. "route_base_cost, route_parking_cost, route_traffic_cost, route_status, created_at, modified_at " 
				. "FROM t_route " 
				. "WHERE route_id = :route_id ";
		$query_route = DB::query($sql_route);
		$query_route->param(':route_id', $route_id);
		$result_route = $query_route->execute()->as_array();
		
		if(count($result_route) == 1) {
			$result = $result_route[0];
			$result['detail_list'] = array();
			
			$sql_detail = "SELECT route_detail_day, route_detail_title, route_detail_content, route_spot_list, route_breakfast, route_lunch, route_dinner, route_hotel "
								. "FROM t_route_detail "
								. "WHERE route_id = :route_id " 
								. "ORDER BY route_detail_day ASC ";
			$query_detail = DB::query($sql_detail);
			$query_detail->param(':route_id', $route_id);
			$result_detail = $query_detail->execute()->as_array();
			
			if(count($result_detail)) {
				foreach($result_detail as $detail_info) {
					$spot_list = array();
					if($detail_info['route_spot_list']) {
						$spot_id_list_tmp = explode(',', $detail_info['route_spot_list']);
						$spot_id_list = array();
						foreach($spot_id_list_tmp as $spot_id) {
							if(is_numeric($spot_id)) {
								$spot_id_list[] = $spot_id;
							}
						}
						$spot_list = Model_Spot::SelectSpotSimpleListById($spot_id_list);
					}

					$result['detail_list'][] = array(
						'route_detail_day' => $detail_info['route_detail_day'],
						'route_detail_title' => $detail_info['route_detail_title'],
						'route_detail_content' => $detail_info['route_detail_content'],
						'route_spot_list' => $spot_list,
						'route_breakfast' => $detail_info['route_breakfast'],
						'route_lunch' => $detail_info['route_lunch'],
						'route_dinner' => $detail_info['route_dinner'],
						'route_hotel' => $detail_info['route_hotel'],
					);
				}
			}
			return $result;
		} else {
			return false;
		}
	}
	
	/*
	 * 添加路线前添加信息查验
	 */
	public static function CheckInsertRoute($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		//路线名称
		if(empty($params['route_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		} elseif(mb_strlen($params['route_name']) > 100) {
			$result['result'] = false;
			$result['error'][] = 'long_name';
		}
		//路线简介
		if(empty($params['route_description'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_description';
		}
		//价格
		if(!is_numeric($params['route_price_min']) || !is_numeric($params['route_price_max'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_price';
		} elseif($params['route_price_min'] < 0 || $params['route_price_max'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_price';
		} elseif(is_numeric($params['route_price_min']) && is_numeric($params['route_price_max'])) {
			if(floatval($params['route_price_min']) > floatval($params['route_price_max'])) {
				$result['result'] = false;
				$result['error'][] = 'reverse_price';
			}
		}
		//基本成本
		if(!is_numeric($params['route_base_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_base_cost';
		} elseif($params['route_base_cost'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_base_cost';
		}
		//交通费
		if(!is_numeric($params['route_traffic_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_traffic_cost';
		} elseif($params['route_traffic_cost'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_traffic_cost';
		}
		//停车费
		if(!is_numeric($params['route_parking_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_parking_cost';
		} elseif($params['route_parking_cost'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_parking_cost';
		}
		//公开状态
		if(!in_array($params['route_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		//详细日程
		$detail_day_list = array();
		if(!count($params['detail_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_detail';
		} else {
			foreach($params['detail_list'] as $detail) {
				//天数
				if(!is_numeric($detail['route_detail_day'])) {
					$result['result'] = false;
					$result['error'][] = 'nonum_detail_day';
				} elseif($detail['route_detail_day'] < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_detail_day';
				} elseif($detail['route_detail_day'] > count($params['detail_list'])) {
					$result['result'] = false;
					$result['error'][] = 'over_detail_day';
				} elseif(in_array($detail['route_detail_day'], $detail_day_list)) {
					$result['result'] = false;
					$result['error'][] = 'duplication_detail_day';
				} else {
					$detail_day_list[] = $detail['route_detail_day'];
				}
				//详细日程标题
				if(empty($detail['route_detail_title'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_detail_title';
				} elseif(mb_strlen($detail['route_detail_title']) > 100) {
					$result['result'] = false;
					$result['error'][] = 'long_detail_title';
				}
				//详细日程简介
				if(empty($detail['route_detail_content'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_detail_content';
				}
				//详细日程景点
				if(!is_array($detail['route_spot_list'])) {
					$result['result'] = false;
					$result['error'][] = 'noarray_spot_list';
				} else {
					$result_spot_check = Model_Spot::CheckDeleteSpotByIdList($detail['route_spot_list']);
					if(!$result_spot_check['result'] && in_array('noexist_spot_id', $result_spot_check['error'])) {
						$result['result'] = false;
						$result['error'][] = 'noexist_spot_id';
					}
				}
			}
		}
		
		$result['error'] = array_unique($result['error']);
		
		return $result;
	}
	
	/*
	 * 删除路线前删除ID查验
	 */
	public static function CheckDeleteRouteById($route_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($route_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM t_route WHERE route_id = :route_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':route_id', $route_id);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除路线前删除ID查验(批量)
	 */
	public static function CheckDeleteRouteByIdList($route_id_list) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_array($route_id_list)) {
			$result['result'] = false;
			$result['error'][] = 'noarray_route_id';
		} elseif(!count($route_id_list)) {
			$result['result'] = false;
			$result['error'][] = 'empty_route_id';
		} else {
			foreach($route_id_list as $route_id) {
				if(!is_numeric($route_id)) {
					$result['result'] = false;
					$result['error'][] = 'nonum_route_id';
					break;
				}
			}
			
			if($result['result']) {
				$sql_where_list = array();
				$sql_param_list = array();
				foreach($route_id_list as $route_id_counter => $route_id) {
					$sql_where_list[] = ':route_id_' . $route_id_counter;
					$sql_param_list[':route_id_' . $route_id_counter] = $route_id;
				}
				$sql_where = implode(', ', $sql_where_list);
				$sql_exist = "SELECT * FROM t_route WHERE route_id IN (" . $sql_where . ")";
				$query_exist = DB::query($sql_exist);
				foreach($sql_param_list as $key => $value) {
					$query_exist->param($key, $value);
				}
				$result_exist = $query_exist->execute()->as_array();
				
				if(count($result_exist) != count($route_id_list)) {
					$result['result'] = false;
					$result['error'][] = 'noexist_route_id';
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 更新路线前更新信息查验
	 */
	public static function CheckUpdateRoute($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		//路线名称
		if(empty($params['route_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		} elseif(mb_strlen($params['route_name']) > 100) {
			$result['result'] = false;
			$result['error'][] = 'long_name';
		}
		//路线简介
		if(empty($params['route_description'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_description';
		}
		//价格
		if(!is_numeric($params['route_price_min']) || !is_numeric($params['route_price_max'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_price';
		} elseif($params['route_price_min'] < 0 || $params['route_price_max'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_price';
		} elseif(is_numeric($params['route_price_min']) && is_numeric($params['route_price_max'])) {
			if(floatval($params['route_price_min']) > floatval($params['route_price_max'])) {
				$result['result'] = false;
				$result['error'][] = 'reverse_price';
			}
		}
		//基本成本
		if(!is_numeric($params['route_base_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_base_cost';
		} elseif($params['route_base_cost'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_base_cost';
		}
		//交通费
		if(!is_numeric($params['route_traffic_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_traffic_cost';
		} elseif($params['route_traffic_cost'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_traffic_cost';
		}
		//停车费
		if(!is_numeric($params['route_parking_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_parking_cost';
		} elseif($params['route_parking_cost'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_parking_cost';
		}
		//公开状态
		if(!in_array($params['route_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		//详细日程
		$detail_day_list = array();
		if(!count($params['detail_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_detail';
		} else {
			foreach($params['detail_list'] as $detail) {
				//天数
				if(!is_numeric($detail['route_detail_day'])) {
					$result['result'] = false;
					$result['error'][] = 'nonum_detail_day';
				} elseif($detail['route_detail_day'] < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_detail_day';
				} elseif($detail['route_detail_day'] > count($params['detail_list'])) {
					$result['result'] = false;
					$result['error'][] = 'over_detail_day';
				} elseif(in_array($detail['route_detail_day'], $detail_day_list)) {
					$result['result'] = false;
					$result['error'][] = 'duplication_detail_day';
				} else {
					$detail_day_list[] = $detail['route_detail_day'];
				}
				//详细日程标题
				if(empty($detail['route_detail_title'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_detail_title';
				} elseif(mb_strlen($detail['route_detail_title']) > 100) {
					$result['result'] = false;
					$result['error'][] = 'long_detail_title';
				}
				//详细日程简介
				if(empty($detail['route_detail_content'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_detail_content';
				}
				//详细日程景点
				if(!is_array($detail['route_spot_list'])) {
					$result['result'] = false;
					$result['error'][] = 'noarray_spot_list';
				} else {
					$result_spot_check = Model_Spot::CheckDeleteSpotByIdList($detail['route_spot_list']);
					if(!$result_spot_check['result'] && in_array('noexist_spot_id', $result_spot_check['error'])) {
						$result['result'] = false;
						$result['error'][] = 'noexist_spot_id';
					}
				}
			}
		}
		
		$result['error'] = array_unique($result['error']);
		
		return $result;
	}
	
	/*
	 * 更新路线公开状态前更新信息查验
	 */
	public static function CheckUpdateRouteStatusById($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!in_array($params['route_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_route_status';
		}
		if(!is_numeric($params['route_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_route_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM t_route WHERE route_id = :route_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':route_id', $params['route_id']);
			$result_exist = $query_exist->execute()->as_array();
			
			if(count($result_exist) != 1) {
				$result['result'] = false;
				$result['error'][] = 'noexist_route_id';
			}
		}
		
		return $result;
	}
	
	/*
	 * 根据ID检查路线是否存在
	 */
	public static function CheckRouteExistByRouteId($route_id) {
		if(!is_numeric($route_id)) {
			return false;
		}
		
		$sql_route = "SELECT route_id FROM t_route WHERE route_id = :route_id ";
		$query_route = DB::query($sql_route);
		$query_route->param(':route_id', $route_id);
		$result_route = $query_route->execute()->as_array();
		
		if(count($result_route) == 1) {
			return true;
		} else {
			return false;
		}
	}
	
}

