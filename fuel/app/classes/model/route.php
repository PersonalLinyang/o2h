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

