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
	
}

