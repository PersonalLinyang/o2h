<?php

class Model_Restaurant extends Model
{
	/*
	 * 添加餐饮
	 */
	public static function InsertRestaurant($params) {
		//添加餐饮
		$sql_insert_restaurant = "INSERT INTO t_restaurant(restaurant_name, restaurant_area, restaurant_type, restaurant_price_min, restaurant_price_max, restaurant_status, created_at, modified_at) "
						. "VALUES(:restaurant_name, :restaurant_area, :restaurant_type, :restaurant_price_min, :restaurant_price_max, :restaurant_status, now(), now())";
		$query_insert_restaurant = DB::query($sql_insert_restaurant);
		$query_insert_restaurant->param('restaurant_name', $params['restaurant_name']);
		$query_insert_restaurant->param('restaurant_area', $params['restaurant_area']);
		$query_insert_restaurant->param('restaurant_type', $params['restaurant_type']);
		$query_insert_restaurant->param('restaurant_price_min', $params['restaurant_price_min']);
		$query_insert_restaurant->param('restaurant_price_max', $params['restaurant_price_max']);
		$query_insert_restaurant->param('restaurant_status', $params['restaurant_status']);
		$result_insert_restaurant = $query_insert_restaurant->execute();
		
		return $result_insert_restaurant;
	}
	
	/*
	 * 更新餐饮
	 */
	public static function UpdateRestaurant($params) {
		//更新餐饮
		$sql_update_restaurant = "UPDATE t_restaurant SET restaurant_name=:restaurant_name, restaurant_area=:restaurant_area, restaurant_type=:restaurant_type, "
						. "restaurant_price_min=:restaurant_price_min, restaurant_price_max=:restaurant_price_max, restaurant_status=:restaurant_status, modified_at=now() WHERE restaurant_id=:restaurant_id";
		$query_update_restaurant = DB::query($sql_update_restaurant);
		$query_update_restaurant->param('restaurant_id', $params['restaurant_id']);
		$query_update_restaurant->param('restaurant_name', $params['restaurant_name']);
		$query_update_restaurant->param('restaurant_area', $params['restaurant_area']);
		$query_update_restaurant->param('restaurant_type', $params['restaurant_type']);
		$query_update_restaurant->param('restaurant_price_min', $params['restaurant_price_min']);
		$query_update_restaurant->param('restaurant_price_max', $params['restaurant_price_max']);
		$query_update_restaurant->param('restaurant_status', $params['restaurant_status']);
		$result_update_restaurant = $query_update_restaurant->execute();
		
		return $result_update_restaurant;
	}
	
	/*
	 * 根据ID删除餐饮
	 */
	public static function DeleteRestaurantById($restaurant_id) {
		$sql_delete = "DELETE FROM t_restaurant WHERE restaurant_id = :restaurant_id";
		$query_delete = DB::query($sql_delete);
		$query_delete->param('restaurant_id', $restaurant_id);
		$result_delete = $query_delete->execute();
		
		return $result_delete;
	}
	
	/*
	 * 根据ID删除餐饮(批量)
	 */
	public static function DeleteRestaurantByIdList($restaurant_id_list) {
		$sql_where_list = array();
		$sql_param_list = array();
		foreach($restaurant_id_list as $restaurant_id_counter => $restaurant_id) {
			$sql_where_list[] = ':restaurant_id_' . $restaurant_id_counter;
			$sql_param_list[':restaurant_id_' . $restaurant_id_counter] = $restaurant_id;
		}
		$sql_where = implode(', ', $sql_where_list);
		$sql_delete = "DELETE FROM t_restaurant WHERE restaurant_id IN (" . $sql_where . ")";
		$query_delete = DB::query($sql_delete);
		foreach($sql_param_list as $key => $value) {
			$query_delete->param($key, $value);
		}
		$result_delete = $query_delete->execute();
		
		return $result_delete;
	}
	
	/*
	 * 更新餐饮状态
	 */
	public static function UpdateRestaurantStatusById($params) {
		$sql_update = "UPDATE t_restaurant SET restaurant_status = :restaurant_status WHERE restaurant_id = :restaurant_id";
		$query_update = DB::query($sql_update);
		$query_update->param('restaurant_id', $params['restaurant_id']);
		$query_update->param('restaurant_status', $params['restaurant_status']);
		$result_update = $query_update->execute();
		
		return $result_update;
	}

	/*
	 * 按条件获得餐饮列表
	 */
	public static function SelectRestaurantList($params) {
		$sql_where = "";
		$sql_order_column = "created_at";
		$sql_order_method = "desc";
		$sql_params = array();
		$sql_offset = 0;
		$sql_limit = 20;
		foreach($params as $key => $value) {
			switch($key) {
				case 'restaurant_name':
					$sql_where_list_name = array();
					foreach($value as $name_counter => $name) {
						$sql_where_list_name[] = "tr.restaurant_name LIKE :restaurant_name_" . $name_counter;
						$sql_params[':restaurant_name_' . $name_counter] = '%' . $name . '%';
					}
					if(count($sql_where_list_name)) {
						$sql_where .= " AND (" . implode(' OR ', $sql_where_list_name) . ") ";
					}
					break;
				case 'restaurant_status':
					$sql_where_list_status = array();
					foreach($value as $status_counter => $status) {
						if(is_numeric($status)) {
							$sql_where_list_status[] = ":restaurant_status_" . $status_counter;
							$sql_params[':restaurant_status_' . $status_counter] = intval($status);
						}
					}
					if(count($sql_where_list_status)) {
						$sql_where .= " AND tr.restaurant_status IN (" . implode(', ', $sql_where_list_status) . ") ";
					}
					break;
				case 'restaurant_area':
					$sql_where_list_area = array();
					foreach($value as $area_counter => $area) {
						if(is_numeric($area)) {
							$sql_where_list_area[] = ":restaurant_area_id_" . $area_counter;
							$sql_params[':restaurant_area_id_' . $area_counter] = intval($area);
						}
					}
					if(count($sql_where_list_area)) {
						$sql_where .= " AND tr.restaurant_area IN (" . implode(', ', $sql_where_list_area) . ") ";
					}
					break;
				case 'restaurant_type':
					$sql_where_list_type = array();
					foreach($value as $type_counter => $type) {
						if(is_numeric($type)) {
							$sql_where_list_type[] = ":restaurant_type_id_" . $type_counter;
							$sql_params[':restaurant_type_id_' . $type_counter] = intval($type);
						}
					}
					if(count($sql_where_list_type)) {
						$sql_where .= " AND tr.restaurant_type IN (" . implode(', ', $sql_where_list_type) . ") ";
					}
					break;
				case 'price_min':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.restaurant_price_max >= :price_min ";
						$sql_params[':price_min'] = floatval($value);
					}
					break;
				case 'price_max':
					if(is_numeric($value)) {
						$sql_where .= " AND tr.restaurant_price_min <= :price_max ";
						$sql_params[':price_max'] = floatval($value);
					}
					break;
				case 'sort_column':
					$sort_column_list = array('restaurant_name', 'restaurant_area_id', 'restaurant_type_id', 'restaurant_status', 'restaurant_price_min', 'restaurant_price_max', 'created_at', 'modified_at');
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

		$sql_count = "SELECT COUNT(DISTINCT tr.restaurant_id) restaurant_count FROM t_restaurant tr WHERE 1=1 " . $sql_where;
		$query_count = DB::query($sql_count);
		foreach ($sql_params as $key => $value) {
			$query_count->param($key, $value);
		}
		$result_count = $query_count->execute()->as_array();

		if(count($result_count)) {
			$restaurant_count = intval($result_count[0]['restaurant_count']);

			if($restaurant_count) {
				$sql_select = "SELECT tr.restaurant_id, tr.restaurant_name, tr.restaurant_status, tr.restaurant_area restaurant_area_id, ma.area_name restaurant_area_name, " 
						. "tr.restaurant_type restaurant_type_id, mrt.restaurant_type_name, tr.restaurant_price_min, tr.restaurant_price_max, tr.created_at, tr.modified_at " 
						. "FROM t_restaurant tr " 
						. "LEFT JOIN m_area ma ON tr.restaurant_area = ma.area_id "
						. "LEFT JOIN m_restaurant_type mrt ON tr.restaurant_type = mrt.restaurant_type_id "
						. "WHERE 1=1 " . $sql_where
						. "GROUP BY restaurant_id, restaurant_name, restaurant_status, restaurant_area_id, restaurant_area_name, restaurant_type_id, restaurant_type_name, created_at, modified_at "
						. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
						. "LIMIT " . $sql_limit . " OFFSET " . $sql_offset;
				$query_select = DB::query($sql_select);
				foreach ($sql_params as $key => $value) {
					$query_select->param($key, $value);
				}
				$result_select = $query_select->execute()->as_array();

				if(count($result_select)) {
					$result = array(
						'restaurant_count' => $restaurant_count,
						'restaurant_list' => $result_select,
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
	 * 根据ID获取餐饮详细信息`
	 */
	public static function SelectRestaurantInfoByRestaurantId($restaurant_id) {
		if(!is_numeric($restaurant_id)) {
			return false;
		}
		
		$sql_restaurant = "SELECT tr.restaurant_id, tr.restaurant_name, tr.restaurant_area restaurant_area_id, ma.area_name restaurant_area_name, ma.area_description restaurant_area_description, " 
				. "tr.restaurant_type restaurant_type_id, mrt.restaurant_type_name, tr.restaurant_price_min, tr.restaurant_price_max, tr.restaurant_status, tr.created_at, tr.modified_at " 
				. "FROM t_restaurant tr " 
				. "LEFT JOIN m_area ma ON tr.restaurant_area = ma.area_id " 
				. "LEFT JOIN m_restaurant_type mrt ON tr.restaurant_type = mrt.restaurant_type_id " 
				. "WHERE tr.restaurant_id = :restaurant_id ";
		$query_restaurant = DB::query($sql_restaurant);
		$query_restaurant->param('restaurant_id', $restaurant_id);
		$result_restaurant = $query_restaurant->execute()->as_array();
		
		if(count($result_restaurant) == 1) {
			$result = $result_restaurant[0];
			return $result;
		} else {
			return false;
		}
	}

	/*
	 * 获得全部餐饮数
	 */
	public static function GetRestaurantTotalCount() {
		$sql = "SELECT count(*) restaurant_count FROM t_restaurant";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			return intval($result[0]['restaurant_count']);
		} else {
			return false;
		}
	}
	
	/*
	 * 添加餐饮前添加信息查验
	 */
	public static function CheckInsertRestaurant($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		//餐饮名称
		if(empty($params['restaurant_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		//餐饮区域
		if(!is_numeric($params['restaurant_area'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_area';
		}
		//餐饮类型
		if(!is_numeric($params['restaurant_type'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_type';
		}
		//价格
		if(!is_numeric($params['restaurant_price_min']) || !is_numeric($params['restaurant_price_max'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_price';
		} elseif($params['restaurant_price_min'] < 0 || $params['restaurant_price_max'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_price';
		} elseif(is_numeric($params['restaurant_price_min']) && is_numeric($params['restaurant_price_max'])) {
			if(floatval($params['restaurant_price_min']) > floatval($params['restaurant_price_max'])) {
				$result['result'] = false;
				$result['error'][] = 'reverse_price';
			}
		}
		//公开状态
		if(!in_array($params['restaurant_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		
		return $result;
	}
	
	/*
	 * 删除餐饮前删除ID查验
	 */
	public static function CheckDeleteRestaurantById($restaurant_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($restaurant_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM t_restaurant WHERE restaurant_id = :restaurant_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param('restaurant_id', $restaurant_id);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除餐饮前删除ID查验(批量)
	 */
	public static function CheckDeleteRestaurantByIdList($restaurant_id_list) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		foreach($restaurant_id_list as $restaurant_id) {
			if(!is_numeric($restaurant_id)) {
				$result['result'] = false;
				$result['error'][] = 'nonum_id';
				break;
			}
		}
		
		if($result['result']) {
			$sql_where_list = array();
			$sql_param_list = array();
			foreach($restaurant_id_list as $restaurant_id_counter => $restaurant_id) {
				$sql_where_list[] = ':restaurant_id_' . $restaurant_id_counter;
				$sql_param_list[':restaurant_id_' . $restaurant_id_counter] = $restaurant_id;
			}
			$sql_where = implode(', ', $sql_where_list);
			$sql_exist = "SELECT * FROM t_restaurant WHERE restaurant_id IN (" . $sql_where . ")";
			$query_exist = DB::query($sql_exist);
			foreach($sql_param_list as $key => $value) {
				$query_exist->param($key, $value);
			}
			$result_exist = $query_exist->execute()->as_array();
			
			if(count($result_exist) != count($restaurant_id_list)) {
				$result['result'] = false;
				$result['error'][] = 'noexist_restaurant_id';
			}
		}
		
		return $result;
	}
	
	/*
	 * 修改餐饮前修改信息查验
	 */
	public static function CheckUpdateRestaurant($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		//餐饮名称
		if(empty($params['restaurant_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		//餐饮区域
		if(!is_numeric($params['restaurant_area'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_area';
		}
		//餐饮类型
		if(!is_numeric($params['restaurant_type'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_type';
		}
		//价格
		if(!is_numeric($params['restaurant_price_min']) || !is_numeric($params['restaurant_price_max'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_price';
		} elseif($params['restaurant_price_min'] < 0 || $params['restaurant_price_max'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_price';
		} elseif(is_numeric($params['restaurant_price_min']) && is_numeric($params['restaurant_price_max'])) {
			if(floatval($params['restaurant_price_min']) > floatval($params['restaurant_price_max'])) {
				$result['result'] = false;
				$result['error'][] = 'reverse_price';
			}
		}
		//公开状态
		if(!in_array($params['restaurant_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		
		return $result;
	}
	
	/*
	 * 更新餐饮公开状态前更新信息查验
	 */
	public static function CheckUpdateRestaurantStatusById($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!in_array($params['restaurant_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_restaurant_status';
		}
		if(!is_numeric($params['restaurant_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_restaurant_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM t_restaurant WHERE restaurant_id = :restaurant_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param('restaurant_id', $params['restaurant_id']);
			$result_exist = $query_exist->execute()->as_array();
			
			if(count($result_exist) != 1) {
				$result['result'] = false;
				$result['error'][] = 'noexist_restaurant_id';
			}
		}
		
		return $result;
	}

}

