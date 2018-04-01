<?php

class Model_Route extends Model
{

	/*
	 * 添加旅游路线
	 */
	public static function InsertRoute($params) {
		//添加旅游路线
		try {
			//添加旅游路线
			$sql_route = "INSERT INTO t_route(route_name, route_description, route_price_min, route_price_max, route_base_cost, route_traffic_cost, route_parking_cost, "
						. "route_status, delete_flag, created_at, created_by, modified_at, modified_by) "
						. "VALUES(:route_name, :route_description, :route_price_min, :route_price_max, :route_base_cost, :route_traffic_cost, :route_parking_cost, "
						. ":route_status, 0, :created_at, :created_by, :modified_at, :modified_by)";
			$query_route = DB::query($sql_route);
			$query_route->param('route_name', $params['route_name']);
			$query_route->param('route_description', $params['route_description']);
			$query_route->param('route_price_min', $params['route_price_min']);
			$query_route->param('route_price_max', $params['route_price_max']);
			$query_route->param('route_base_cost', $params['route_base_cost']);
			$query_route->param('route_traffic_cost', $params['route_traffic_cost']);
			$query_route->param('route_parking_cost', $params['route_parking_cost']);
			$query_route->param('route_status', $params['route_status']);
			$time_now = date('Y-m-d H:i:s', time());
			$query_route->param('created_at', $time_now);
			$query_route->param('created_by', $params['created_by']);
			$query_route->param('modified_at', $time_now);
			$query_route->param('modified_by', $params['modified_by']);
			$result_route = $query_route->execute();
			
			if($result_route) {
				//新旅游路线ID
				$route_id = intval($result_route[0]);
				
				//添加详细日程
				foreach($params['detail_list'] as $detail) {
					$sql_detail = "INSERT INTO e_route_detail(route_id, route_detail_day, route_detail_title, route_detail_content, " 
								. "route_spot_list, route_breakfast, route_lunch, route_dinner, route_hotel) "
								. "VALUES(:route_id, :route_detail_day, :route_detail_title, :route_detail_content, " 
								. ":route_spot_list, :route_breakfast, :route_lunch, :route_dinner, :route_hotel)";
					$query_detail = DB::query($sql_detail);
					$query_detail->param('route_id', $route_id);
					$query_detail->param('route_detail_day', $detail['route_detail_day']);
					$query_detail->param('route_detail_title', $detail['route_detail_title']);
					$query_detail->param('route_detail_content', $detail['route_detail_content']);
					$query_detail->param('route_spot_list', implode(',', $detail['route_spot_list']));
					$query_detail->param('route_breakfast', $detail['route_breakfast']);
					$query_detail->param('route_lunch', $detail['route_lunch']);
					$query_detail->param('route_dinner', $detail['route_dinner']);
					$query_detail->param('route_hotel', $detail['route_hotel']);
					$result_detail = $query_detail->execute();
				}
				
				return $route_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除旅游路线
	 */
	public static function DeleteRoute($params) {
		try {
			//删除旅游路线
			$sql_route = "UPDATE t_route SET delete_flag = 1, route_status=0, modified_at=:modified_at, modified_by=:modified_by WHERE route_id IN :route_id_list";
			$query_route = DB::query($sql_route);
			$query_route->param('route_id_list', $params['route_id_list']);
			$query_route->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_route->param('modified_by', $params['deleted_by']);
			$result_route = $query_route->execute();
			
			//删除详细日程
			$sql_detail = "DELETE FROM e_route_detail WHERE route_id IN :route_id_list";
			$query_detail = DB::query($sql_detail);
			$query_detail->param('route_id_list', $params['route_id_list']);
			$result_detail = $query_detail->execute();
			
			return $result_route;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新旅游路线
	 */
	public static function UpdateRoute($params) {
		try {
			//更新旅游路线
			$sql_route = "UPDATE t_route "
						. "SET route_name=:route_name, route_description=:route_description, route_price_min=:route_price_min, route_price_max=:route_price_max, route_base_cost=:route_base_cost, "
						. "route_traffic_cost=:route_traffic_cost, route_parking_cost=:route_parking_cost, route_status=:route_status, modified_at=:modified_at, modified_by=:modified_by "
						. "WHERE route_id=:route_id";
			$query_route = DB::query($sql_route);
			$query_route->param('route_id', $params['route_id']);
			$query_route->param('route_name', $params['route_name']);
			$query_route->param('route_description', $params['route_description']);
			$query_route->param('route_price_min', $params['route_price_min']);
			$query_route->param('route_price_max', $params['route_price_max']);
			$query_route->param('route_base_cost', $params['route_base_cost']);
			$query_route->param('route_traffic_cost', $params['route_traffic_cost']);
			$query_route->param('route_parking_cost', $params['route_parking_cost']);
			$query_route->param('route_status', $params['route_status']);
			$query_route->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_route->param('modified_by', $params['modified_by']);
			$result_route = $query_route->execute();
			
			//删除原有详细日程
			$sql_detail_delete = "DELETE FROM e_route_detail WHERE route_id=:route_id";
			$query_detail_delete = DB::query($sql_detail_delete);
			$query_detail_delete->param('route_id', $params['route_id']);
			$result_detail_delete = $query_detail_delete->execute();
			
			//更新详细日程
			$sql_values_detail = array();
			$sql_params_detail = array();
			foreach($params['detail_list'] as $detail_key => $route_detail) {
				$sql_values_detail[] = "(:route_id, :route_detail_day_" . $detail_key . ", "
									. ":route_detail_title_" . $detail_key . ", :route_detail_content_" . $detail_key . ", " 
									. ":route_spot_list_" . $detail_key . ", :route_breakfast_" . $detail_key . ", "
									. ":route_lunch_" . $detail_key . ", :route_dinner_" . $detail_key . ", :route_hotel_" . $detail_key . ")";
				
				$sql_params_detail[':route_detail_day_' . $detail_key] = $route_detail['route_detail_day'];
				$sql_params_detail[':route_detail_title_' . $detail_key] = $route_detail['route_detail_title'];
				$sql_params_detail[':route_detail_content_' . $detail_key] = $route_detail['route_detail_content'];
				$sql_params_detail[':route_spot_list_' . $detail_key] = implode(',', $route_detail['route_spot_list']);
				$sql_params_detail[':route_breakfast_' . $detail_key] = $route_detail['route_breakfast'];
				$sql_params_detail[':route_lunch_' . $detail_key] = $route_detail['route_lunch'];
				$sql_params_detail[':route_dinner_' . $detail_key] = $route_detail['route_dinner'];
				$sql_params_detail[':route_hotel_' . $detail_key] = $route_detail['route_hotel'];
			}
			
			if(count($sql_values_detail)) {
				$sql_detail_insert = "INSERT INTO e_route_detail(route_id, route_detail_day, route_detail_title, route_detail_content, " 
									. "route_spot_list, route_breakfast, route_lunch, route_dinner, route_hotel) VALUES" . implode(",", $sql_values_detail);
				$query_detail_insert = DB::query($sql_detail_insert);
				$query_detail_insert->param('route_id', $params['route_id']);
				foreach($sql_params_detail as $param_key => $param_value) {
					$query_detail_insert->param($param_key, $param_value);
				}
				$result_detail_insert = $query_detail_insert->execute();
			}
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新旅游路线状态
	 */
	public static function UpdateRouteStatus($params) {
		try {
			$sql = "UPDATE t_route SET route_status = :route_status, modified_at=:modified_at, modified_by=:modified_by WHERE route_id = :route_id";
			$query = DB::query($sql);
			$query->param('route_id', $params['route_id']);
			$query->param('route_status', $params['route_status']);
			$query->param('modified_at', date('Y-m-d H:i:s', time()));
			$query->param('modified_by', $params['modified_by']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 按条件获得旅游路线列表
	 */
	public static function SelectRouteList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "created_at";
			$sql_order_method = "desc";
			$sql_limit = "";
			$sql_offset = "";
			
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'route_id_list':
						if(count($param_value)) {
							$sql_where[] = " tr.route_id IN :route_id_list ";
							$sql_params['route_id_list'] = $param_value;
						}
						break;
					case 'route_name':
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $name_key => $name) {
								$sql_sub_where[] = "tr.route_name LIKE :route_name_" . $name_key;
								$sql_params['route_name_' . $name_key] = '%' . $name . '%';
							}
							$sql_where[] = " (" . implode(" OR ", $sql_sub_where) . ") ";
						}
						break;
					case 'route_status':
						if(count($param_value)) {
							$sql_where[] = " tr.route_status IN :route_status_list ";
							$sql_params['route_status_list'] = $param_value;
						}
						break;
					case 'price_min':
						if(is_numeric($param_value)) {
							$sql_where[] = " tr.route_price_max >= :price_min ";
							$sql_params['price_min'] = floatval($param_value);
						}
						break;
					case 'price_max':
						if(is_numeric($param_value)) {
							$sql_where[] = " tr.route_price_min <= :price_max ";
							$sql_params['price_max'] = floatval($param_value);
						}
						break;
					case 'base_cost_min':
						if(is_numeric($param_value)) {
							$sql_where[] = " tr.route_base_cost >= :base_cost_min ";
							$sql_params[':base_cost_min'] = floatval($param_value);
						}
						break;
					case 'base_cost_max':
						if(is_numeric($param_value)) {
							$sql_where[] = " tr.route_base_cost <= :base_cost_max ";
							$sql_params[':base_cost_max'] = floatval($param_value);
						}
						break;
					case 'traffic_cost_min':
						if(is_numeric($param_value)) {
							$sql_where[] = " tr.route_traffic_cost >= :traffic_cost_min ";
							$sql_params[':traffic_cost_min'] = floatval($param_value);
						}
						break;
					case 'traffic_cost_max':
						if(is_numeric($param_value)) {
							$sql_where[] = " tr.route_traffic_cost <= :traffic_cost_max ";
							$sql_params[':traffic_cost_max'] = floatval($param_value);
						}
						break;
					case 'parking_cost_min':
						if(is_numeric($param_value)) {
							$sql_where[] = " AND tr.route_parking_cost >= :parking_cost_min ";
							$sql_params[':parking_cost_min'] = floatval($param_value);
						}
						break;
					case 'parking_cost_max':
						if(is_numeric($param_value)) {
							$sql_where[] = " AND tr.route_parking_cost <= :parking_cost_max ";
							$sql_params[':parking_cost_max'] = floatval($param_value);
						}
						break;
					case 'total_cost_min':
						if(is_numeric($param_value)) {
							$sql_where[] = " AND tr.route_total_cost >= :total_cost_min ";
							$sql_params[':total_cost_min'] = floatval($param_value);
						}
						break;
					case 'total_cost_max':
						if(is_numeric($param_value)) {
							$sql_where[] = " AND tr.route_total_cost <= :total_cost_max ";
							$sql_params[':total_cost_max'] = floatval($param_value);
						}
						break;
					case 'created_by':
						$sql_where[] = " tr.created_by = :created_by ";
						$sql_params['created_by'] = $param_value;
						break;
					case 'active_only':
						$sql_where[] = " tr.delete_flag = 0 ";
						break;
					case 'sort_column':
						$sort_column_list = array(
							'route_name', 'route_status', 'route_price_min', 'route_price_max', 'route_base_cost', 'route_traffic_cost', 
							'route_parking_cost', 'route_total_cost', 'created_at', 'modified_at', 'detail_day_number');
						if(in_array($param_value, $sort_column_list)) {
							$sql_order_column = $param_value;
						}
						break;
					case 'sort_method':
						if(in_array($param_value, array('asc', 'desc'))) {
							$sql_order_method = $param_value;
						}
						break;
					default:
						break;
				}
			}
			
			if(isset($params['num_per_page']) && isset($params['page'])) {
				$sql_limit = intval($params['num_per_page']);
				$sql_offset = (intval($params['page']) - 1) * $sql_limit;
				$sql_limit = " LIMIT " . $sql_limit;
				$sql_offset = " OFFSET " . $sql_offset;
			}
			
			//符合条件的旅游路线总数获取
			$sql_count = "SELECT COUNT(DISTINCT tr.route_id) route_count "
						. "FROM t_route tr "
						. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_count = DB::query($sql_count);
			foreach ($sql_params as $key => $value) {
				$query_count->param($key, $value);
			}
			$result_count = $query_count->execute()->as_array();

			if(count($result_count)) {
				$route_count = intval($result_count[0]['route_count']);

				if($route_count) {
					//旅游路线信息获取
					$sql_route = "SELECT tr.*, tr.route_base_cost+tr.route_traffic_cost+tr.route_parking_cost route_total_cost, COUNT(trd.route_detail_day) detail_day_number " 
							. "FROM t_route tr " 
							. "LEFT JOIN e_route_detail trd ON tr.route_id = trd.route_id "
							. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
							. "GROUP BY tr.route_id "
							. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
							. $sql_limit . $sql_offset;
					$query_route = DB::query($sql_route);
					foreach($sql_params as $param_key => $param_value) {
						$query_route->param($param_key, $param_value);
					}
					$result_route = $query_route->execute()->as_array();
					
					if(count($result_route)) {
						$route_list = array();
						$route_id_list = array();
						foreach($result_route as $route) {
							$route_list[$route['route_id']] = $route;
							$route_list[$route['route_id']]['detail_list'] = array();
							$route_id_list[] = intval($route['route_id']);
						}
						
						//旅游路线详细日程获取
						if(isset($params['detail_flag'])) {
							$sql_detail = "SELECT * FROM e_route_detail WHERE route_id IN :route_id_list ORDER BY route_id ASC, route_detail_day ASC";
							$query_detail = DB::query($sql_detail);
							$query_detail->param('route_id_list', $route_id_list);
							$result_detail = $query_detail->execute()->as_array();
							
							if(count($result_detail)) {
								foreach($result_detail as $route_detail) {
									$spot_list = array();
									if($route_detail['route_spot_list']) {
										$spot_id_list = explode(',', $route_detail['route_spot_list']);
										$spot_list = Model_Spot::SelectSpotSimpleList(array('spot_id_list' => $spot_id_list));
									}
									
									$route_list[$route_detail['route_id']]['detail_list'][] = array(
										'route_detail_day' => $route_detail['route_detail_day'],
										'route_detail_title' => $route_detail['route_detail_title'],
										'route_detail_content' => $route_detail['route_detail_content'],
										'route_spot_list' => $spot_list,
										'route_breakfast' => $route_detail['route_breakfast'],
										'route_lunch' => $route_detail['route_lunch'],
										'route_dinner' => $route_detail['route_dinner'],
										'route_hotel' => $route_detail['route_hotel'],
									);
								}
							}
						}
						
						$result = array(
							'route_count' => $route_count,
							'route_list' => $route_list,
							'start_number' => $sql_offset + 1,
							'end_number' => count($result_route) + $sql_offset,
						);
						return $result;
					}
				}
			}
			
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 按条件获得旅游路线简易列表
	 */
	public static function SelectRouteSimpleList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "created_at";
			$sql_order_method = "desc";
			
			//检索条件处理
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'route_status':
						if(count($param_value)) {
							$sql_where[] = " tr.route_status IN :route_status_list ";
							$sql_params['route_status_list'] = $param_value;
						}
						break;
					case 'active_only':
						$sql_where[] = " tr.delete_flag = 0 ";
						break;
					default:
						break;
				}
			}
			
			//符合条件的旅游路线简易列表获取
			$sql = "SELECT tr.route_id, tr.route_name "
				. "FROM t_route tr "
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
				. "ORDER BY " . $sql_order_column . " " . $sql_order_method;
			$query = DB::query($sql);
			foreach($sql_params as $param_key => $param_value) {
				$query->param($param_key, $param_value);
			}
			$result = $query->execute()->as_array();
			
			return $result;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 获取特定单个旅游路线信息
	 */
	public static function SelectRoute($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//旅游路线ID限定
			if(isset($params['route_id'])) {
				$sql_where[] = " tr.route_id = :route_id ";
				$sql_params['route_id'] = $params['route_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " tr.delete_flag = 0 ";
				}
			}
			//路线状态限定
			if(isset($params['active_only'])) {
				if(count($params['active_only'])) {
					$sql_where[] = " tr.route_status IN :route_status_list ";
					$sql_params['route_status_list'] = $param_value;
				}
			}
			
			//数据获取
			$sql_route = "SELECT tr.*, tuc.user_name created_name, tum.user_name modified_name " 
					. "FROM t_route tr " 
					. "LEFT JOIN t_user tuc ON tr.created_by = tuc.user_id " 
					. "LEFT JOIN t_user tum ON tr.modified_by = tum.user_id " 
							. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_route = DB::query($sql_route);
			foreach($sql_params as $param_key => $param_value) {
				$query_route->param($param_key, $param_value);
			}
			$result_route = $query_route->execute()->as_array();
			
			if(count($result_route) == 1) {
				$result = $result_route[0];
				$result['detail_list'] = array();
				
				//路线日程获取
				$sql_detail = "SELECT erd.* "
							. "FROM e_route_detail erd "
							. "WHERE erd.route_id IN ("
							. "SELECT tr.route_id "
							. "FROM t_route tr "
							. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
							. ")";
				$query_detail = DB::query($sql_detail);
				foreach($sql_params as $param_key => $param_value) {
					$query_detail->param($param_key, $param_value);
				}
				$result_detail = $query_detail->execute()->as_array();
				
				if(count($result_detail)) {
					foreach($result_detail as $detail_info) {
						$spot_list = array();
						if($detail_info['route_spot_list']) {
							$spot_id_list = explode(',', $detail_info['route_spot_list']);
							$spot_list = Model_Spot::SelectSpotSimpleList(array('spot_id_list' => $spot_id_list));
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
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 编辑旅游路线前编辑信息查验
	 */
	public static function CheckEditRoute($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//旅游路线名
		if(empty($params['route_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_route_name';
		} elseif(mb_strlen($params['route_name']) > 100) {
			$result['result'] = false;
			$result['error'][] = 'long_route_name';
		} elseif(Model_Route::CheckRouteNameDuplication($params['route_id'], $params['route_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_route_name';
		}
		
		//旅游路线简介
		if(empty($params['route_description'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_route_description';
		}
		
		//价格
		if(empty($params['route_price_min']) || empty($params['route_price_max'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_route_price';
		} elseif(!is_numeric($params['route_price_min']) || !is_numeric($params['route_price_max'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_route_price';
		} elseif(floatval($params['route_price_min']) < 0 || floatval($params['route_price_max']) < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_route_price';
		} elseif(floatval($params['route_price_min']) > floatval($params['route_price_max'])) {
			$result['result'] = false;
			$result['error'][] = 'error_route_price';
		}
		
		//基本成本
		if(empty($params['route_base_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_route_base_cost';
		} elseif(!is_numeric($params['route_base_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_route_base_cost';
		} elseif(floatval($params['route_base_cost']) < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_route_base_cost';
		}
		
		//交通费
		if(empty($params['route_traffic_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_route_traffic_cost';
		} elseif(!is_numeric($params['route_traffic_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_route_traffic_cost';
		} elseif(floatval($params['route_traffic_cost']) < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_route_traffic_cost';
		}
		
		//停车费
		if(empty($params['route_parking_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_route_parking_cost';
		} elseif(!is_numeric($params['route_parking_cost'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_route_parking_cost';
		} elseif(floatval($params['route_parking_cost']) < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_route_parking_cost';
		}
		
		//公开状态
		if(!in_array($params['route_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_route_status';
		}
		
		//详细日程
		$detail_day_list = array();
		if(!count($params['detail_list'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_detail_list';
		} else {
			foreach($params['detail_list'] as $detail) {
				//天数
				if(empty($detail['route_detail_day'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_route_detail_day';
				} elseif(!is_numeric($detail['route_detail_day']) || !is_int($detail['route_detail_day'] + 0)) {
					$result['result'] = false;
					$result['error'][] = 'noint_route_detail_day';
				} elseif($detail['route_detail_day'] < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_route_detail_day';
				} elseif($detail['route_detail_day'] > count($params['detail_list'])) {
					$result['result'] = false;
					$result['error'][] = 'over_route_detail_day';
				} elseif(in_array($detail['route_detail_day'], $detail_day_list)) {
					$result['result'] = false;
					$result['error'][] = 'dup_route_detail_day';
				} else {
					$detail_day_list[] = $detail['route_detail_day'];
				}
				
				//详细日程标题
				if(empty($detail['route_detail_title'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_route_detail_title';
				} elseif(mb_strlen($detail['route_detail_title']) > 100) {
					$result['result'] = false;
					$result['error'][] = 'long_route_detail_title';
				}
				
				//详细日程简介
				if(empty($detail['route_detail_content'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_route_detail_content';
				}
				
				//详细日程景点
				if(!is_array($detail['route_spot_list'])) {
					$result['result'] = false;
					$result['error'][] = 'noarray_spot_list';
				} elseif(count($detail['route_spot_list'])) {
					$spot_list = Model_Spot::SelectSpotSimpleList(array('spot_id_list' => $detail['route_spot_list'], 'spot_status' => array(1), 'active_only' => true));
					if(count($spot_list) != count($detail['route_spot_list'])) {
						$result['result'] = false;
						$result['error'][] = 'error_spot_list';
					}
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除旅游路线前删除信息查验
	 */
	public static function CheckDeleteRoute($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_array($params['route_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_route_id';
		} elseif(!count($params['route_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_route_id';
		} else {
			$all_num_flag = true;
			
			foreach($params['route_id_list'] as $route_id) {
				if(!is_numeric($route_id)) {
					$result['result'] = false;
					$all_num_flag = false;
					$result['error'][] = 'nonum_route_id';
					break;
				}
			}
			
			if($all_num_flag) {
				$params_select = array('route_id_list' => $params['route_id_list']);
				$result_select = Model_Route::SelectRouteList($params_select);
				
				if($result_select['route_count'] != count(array_unique($params['route_id_list']))) {
					$result['result'] = false;
					$result['error'][] = 'error_route_id';
				} elseif($params['self_only']) {
					foreach($result_select['route_list'] as $route_select) {
						if($params['delete_by'] != $route_select['created_by']) {
							$result['result'] = false;
							$result['error'][] = 'error_creator';
							break;
						}
					}
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 更新旅游路线公开状态前更新信息查验
	 */
	public static function CheckUpdateRouteStatus($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!in_array($params['route_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_route_status';
		}
		
		return $result;
	}
	
	/*
	 * 旅游路线名重复查验
	 */
	public static function CheckRouteNameDuplication($route_id, $route_name) {
		try {
			//数据获取
			$sql = "SELECT route_id FROM t_route WHERE route_name = :route_name AND delete_flag = 0" . ($route_id ? " AND route_id != :route_id " : "");
			$query = DB::query($sql);
			if($route_id) {
				$query->param('route_id', $route_id);
			}
			$query->param('route_name', $route_name);
			$result = $query->execute()->as_array();
			
			if(count($result)) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return true;
		}
	}
	
	/*
	 * 检查旅游路线ID是否存在
	 */
	public static function CheckRouteIdExist($route_id, $active_check = 0) {
		try {
			$sql = "SELECT route_id FROM t_route WHERE route_id = :route_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('route_id', $route_id);
			$result = $query->execute()->as_array();
			
			if(count($result)) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
}

