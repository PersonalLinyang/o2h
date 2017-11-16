<?php

class Model_Hotel extends Model
{
	/*
	 * 添加酒店
	 */
	public static function InsertHotel($params) {
		//添加酒店
		$sql_insert_hotel = "INSERT INTO t_hotel(hotel_name, hotel_area, hotel_type, hotel_price, hotel_status, created_at, modified_at) "
						. "VALUES(:hotel_name, :hotel_area, :hotel_type, :hotel_price, :hotel_status, now(), now())";
		$query_insert_hotel = DB::query($sql_insert_hotel);
		$query_insert_hotel->param(':hotel_name', $params['hotel_name']);
		$query_insert_hotel->param(':hotel_area', $params['hotel_area']);
		$query_insert_hotel->param(':hotel_type', $params['hotel_type']);
		$query_insert_hotel->param(':hotel_price', $params['hotel_price']);
		$query_insert_hotel->param(':hotel_status', $params['hotel_status']);
		$result_insert_hotel = $query_insert_hotel->execute();
		
		return $result_insert_hotel;
	}
	
	/*
	 * 更新酒店
	 */
	public static function UpdateHotel($params) {
		//更新酒店
		$sql_update_hotel = "UPDATE t_hotel SET hotel_name=:hotel_name, hotel_area=:hotel_area, hotel_type=:hotel_type, "
						. "hotel_price=:hotel_price, hotel_status=:hotel_status, modified_at=now() WHERE hotel_id=:hotel_id";
		$query_update_hotel = DB::query($sql_update_hotel);
		$query_update_hotel->param(':hotel_id', $params['hotel_id']);
		$query_update_hotel->param(':hotel_name', $params['hotel_name']);
		$query_update_hotel->param(':hotel_area', $params['hotel_area']);
		$query_update_hotel->param(':hotel_type', $params['hotel_type']);
		$query_update_hotel->param(':hotel_price', $params['hotel_price']);
		$query_update_hotel->param(':hotel_status', $params['hotel_status']);
		$result_update_hotel = $query_update_hotel->execute();
		
		return $result_update_hotel;
	}
	
	/*
	 * 根据ID删除酒店
	 */
	public static function DeleteHotelById($hotel_id) {
		$sql_delete = "DELETE FROM t_hotel WHERE hotel_id = :hotel_id";
		$query_delete = DB::query($sql_delete);
		$query_delete->param(':hotel_id', $hotel_id);
		$result_delete = $query_delete->execute();
		
		return $result_delete;
	}
	
	/*
	 * 根据ID删除酒店(批量)
	 */
	public static function DeleteHotelByIdList($hotel_id_list) {
		$sql_where_list = array();
		$sql_param_list = array();
		foreach($hotel_id_list as $hotel_id_counter => $hotel_id) {
			$sql_where_list[] = ':hotel_id_' . $hotel_id_counter;
			$sql_param_list[':hotel_id_' . $hotel_id_counter] = $hotel_id;
		}
		$sql_where = implode(', ', $sql_where_list);
		$sql_delete = "DELETE FROM t_hotel WHERE hotel_id IN (" . $sql_where . ")";
		$query_delete = DB::query($sql_delete);
		foreach($sql_param_list as $key => $value) {
			$query_delete->param($key, $value);
		}
		$result_delete = $query_delete->execute();
		
		return $result_delete;
	}
	
	/*
	 * 更新酒店状态
	 */
	public static function UpdateHotelStatusById($params) {
		$sql_update = "UPDATE t_hotel SET hotel_status = :hotel_status WHERE hotel_id = :hotel_id";
		$query_update = DB::query($sql_update);
		$query_update->param(':hotel_id', $params['hotel_id']);
		$query_update->param(':hotel_status', $params['hotel_status']);
		$result_update = $query_update->execute();
		
		return $result_update;
	}

	/*
	 * 按条件获得酒店列表
	 */
	public static function SelectHotelList($params) {
		$sql_where = "";
		$sql_order_column = "created_at";
		$sql_order_method = "desc";
		$sql_params = array();
		$sql_offset = 0;
		$sql_limit = 20;
		foreach($params as $key => $value) {
			switch($key) {
				case 'hotel_name':
					$sql_where_list_name = array();
					foreach($value as $name_counter => $name) {
						$sql_where_list_name[] = "th.hotel_name LIKE :hotel_name_" . $name_counter;
						$sql_params[':hotel_name_' . $name_counter] = '%' . $name . '%';
					}
					if(count($sql_where_list_name)) {
						$sql_where .= " AND (" . implode(' OR ', $sql_where_list_name) . ") ";
					}
					break;
				case 'hotel_status':
					$sql_where_list_status = array();
					foreach($value as $status_counter => $status) {
						if(is_numeric($status)) {
							$sql_where_list_status[] = ":hotel_status_" . $status_counter;
							$sql_params[':hotel_status_' . $status_counter] = intval($status);
						}
					}
					if(count($sql_where_list_status)) {
						$sql_where .= " AND th.hotel_status IN (" . implode(', ', $sql_where_list_status) . ") ";
					}
					break;
				case 'hotel_area':
					$sql_where_list_area = array();
					foreach($value as $area_counter => $area) {
						if(is_numeric($area)) {
							$sql_where_list_area[] = ":hotel_area_id_" . $area_counter;
							$sql_params[':hotel_area_id_' . $area_counter] = intval($area);
						}
					}
					if(count($sql_where_list_area)) {
						$sql_where .= " AND th.hotel_area IN (" . implode(', ', $sql_where_list_area) . ") ";
					}
					break;
				case 'hotel_type':
					$sql_where_list_type = array();
					foreach($value as $type_counter => $type) {
						if(is_numeric($type)) {
							$sql_where_list_type[] = ":hotel_type_id_" . $type_counter;
							$sql_params[':hotel_type_id_' . $type_counter] = intval($type);
						}
					}
					if(count($sql_where_list_type)) {
						$sql_where .= " AND th.hotel_type IN (" . implode(', ', $sql_where_list_type) . ") ";
					}
					break;
				case 'price_min':
					if(is_numeric($value)) {
						$sql_where .= " AND th.hotel_price >= :price_min ";
						$sql_params[':price_min'] = floatval($value);
					}
					break;
				case 'price_max':
					if(is_numeric($value)) {
						$sql_where .= " AND th.hotel_price <= :price_max ";
						$sql_params[':price_max'] = floatval($value);
					}
					break;
				case 'sort_column':
					$sort_column_list = array('hotel_name', 'hotel_area_id', 'hotel_type_id', 'hotel_status', 'hotel_price', 'created_at', 'modified_at');
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

		$sql_count = "SELECT COUNT(DISTINCT th.hotel_id) hotel_count FROM t_hotel th WHERE 1=1 " . $sql_where;
		$query_count = DB::query($sql_count);
		foreach ($sql_params as $key => $value) {
			$query_count->param($key, $value);
		}
		$result_count = $query_count->execute()->as_array();

		if(count($result_count)) {
			$hotel_count = intval($result_count[0]['hotel_count']);

			if($hotel_count) {
				$sql_select = "SELECT th.hotel_id, th.hotel_name, th.hotel_status, th.hotel_area hotel_area_id, ma.area_name hotel_area_name, " 
						. "th.hotel_type hotel_type_id, mht.hotel_type_name, th.hotel_price, th.created_at, th.modified_at " 
						. "FROM t_hotel th " 
						. "LEFT JOIN m_area ma ON th.hotel_area = ma.area_id "
						. "LEFT JOIN m_hotel_type mht ON th.hotel_type = mht.hotel_type_id "
						. "WHERE 1=1 " . $sql_where
						. "GROUP BY hotel_id, hotel_name, hotel_status, hotel_area_id, hotel_area_name, hotel_type_id, hotel_type_name, created_at, modified_at "
						. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
						. "LIMIT " . $sql_limit . " OFFSET " . $sql_offset;
				$query_select = DB::query($sql_select);
				foreach ($sql_params as $key => $value) {
					$query_select->param($key, $value);
				}
				$result_select = $query_select->execute()->as_array();

				if(count($result_select)) {
					$result = array(
						'hotel_count' => $hotel_count,
						'hotel_list' => $result_select,
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
	 * 根据ID获取酒店详细信息`
	 */
	public static function SelectHotelInfoByHotelId($hotel_id) {
		if(!is_numeric($hotel_id)) {
			return false;
		}
		
		$sql_hotel = "SELECT th.hotel_id, th.hotel_name, th.hotel_area hotel_area_id, ma.area_name hotel_area_name, ma.area_description hotel_area_description, " 
				. "th.hotel_type hotel_type_id, mht.hotel_type_name, th.hotel_price, th.hotel_status, th.created_at, th.modified_at " 
				. "FROM t_hotel th " 
				. "LEFT JOIN m_area ma ON th.hotel_area = ma.area_id " 
				. "LEFT JOIN m_hotel_type mht ON th.hotel_type = mht.hotel_type_id " 
				. "WHERE th.hotel_id = :hotel_id ";
		$query_hotel = DB::query($sql_hotel);
		$query_hotel->param(':hotel_id', $hotel_id);
		$result_hotel = $query_hotel->execute()->as_array();
		
		if(count($result_hotel) == 1) {
			$result = $result_hotel[0];
			return $result;
		} else {
			return false;
		}
	}

	/*
	 * 获得全部酒店数
	 */
	public static function GetHotelTotalCount() {
		$sql = "SELECT count(*) hotel_count FROM t_hotel";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			return intval($result[0]['hotel_count']);
		} else {
			return false;
		}
	}
	
	/*
	 * 添加酒店前添加信息查验
	 */
	public static function CheckInsertHotel($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		//酒店名称
		if(empty($params['hotel_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		//酒店区域
		if(!is_numeric($params['hotel_area'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_area';
		}
		//酒店类型
		if(!is_numeric($params['hotel_type'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_type';
		}
		//价格
		if(!is_numeric($params['hotel_price'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_price';
		} elseif($params['hotel_price'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_price';
		}
		//公开状态
		if(!in_array($params['hotel_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		
		return $result;
	}
	
	/*
	 * 删除酒店前删除ID查验
	 */
	public static function CheckDeleteHotelById($hotel_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($hotel_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM t_hotel WHERE hotel_id = :hotel_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':hotel_id', $hotel_id);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除酒店前删除ID查验(批量)
	 */
	public static function CheckDeleteHotelByIdList($hotel_id_list) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		foreach($hotel_id_list as $hotel_id) {
			if(!is_numeric($hotel_id)) {
				$result['result'] = false;
				$result['error'][] = 'nonum_id';
				break;
			}
		}
		
		if($result['result']) {
			$sql_where_list = array();
			$sql_param_list = array();
			foreach($hotel_id_list as $hotel_id_counter => $hotel_id) {
				$sql_where_list[] = ':hotel_id_' . $hotel_id_counter;
				$sql_param_list[':hotel_id_' . $hotel_id_counter] = $hotel_id;
			}
			$sql_where = implode(', ', $sql_where_list);
			$sql_exist = "SELECT * FROM t_hotel WHERE hotel_id IN (" . $sql_where . ")";
			$query_exist = DB::query($sql_exist);
			foreach($sql_param_list as $key => $value) {
				$query_exist->param($key, $value);
			}
			$result_exist = $query_exist->execute()->as_array();
			
			if(count($result_exist) != count($hotel_id_list)) {
				$result['result'] = false;
				$result['error'][] = 'noexist_hotel_id';
			}
		}
		
		return $result;
	}
	
	/*
	 * 修改酒店前修改信息查验
	 */
	public static function CheckUpdateHotel($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		//酒店名称
		if(empty($params['hotel_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		//酒店区域
		if(!is_numeric($params['hotel_area'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_area';
		}
		//酒店类型
		if(!is_numeric($params['hotel_type'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_type';
		}
		//价格
		if(!is_numeric($params['hotel_price'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_price';
		} elseif($params['hotel_price'] < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_price';
		}
		//公开状态
		if(!in_array($params['hotel_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		
		return $result;
	}
	
	/*
	 * 更新酒店公开状态前更新信息查验
	 */
	public static function CheckUpdateHotelStatusById($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!in_array($params['hotel_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_hotel_status';
		}
		if(!is_numeric($params['hotel_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_hotel_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM t_hotel WHERE hotel_id = :hotel_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':hotel_id', $params['hotel_id']);
			$result_exist = $query_exist->execute()->as_array();
			
			if(count($result_exist) != 1) {
				$result['result'] = false;
				$result['error'][] = 'noexist_hotel_id';
			}
		}
		
		return $result;
	}

}

