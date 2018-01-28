<?php

class Model_Hotel extends Model
{

	/*
	 * 添加酒店
	 */
	public static function InsertHotel($params) {
		//添加酒店
		try {
			//添加酒店
			$sql = "INSERT INTO t_hotel(hotel_name, hotel_area, hotel_type, hotel_price, hotel_status, "
						. "delete_flag, created_at, created_by, modified_at, modified_by) "
						. "VALUES(:hotel_name, :hotel_area, :hotel_type, :hotel_price, :hotel_status, "
						. "0, :created_at, :created_by, :modified_at, :modified_by)";
			$query = DB::query($sql);
			$query->param('hotel_name', $params['hotel_name']);
			$query->param('hotel_area', $params['hotel_area']);
			$query->param('hotel_type', $params['hotel_type']);
			$query->param('hotel_price', $params['hotel_price']);
			$query->param('hotel_status', $params['hotel_status']);
			$time_now = date('Y-m-d H:i:s', time());
			$query->param('created_at', $time_now);
			$query->param('created_by', $params['created_by']);
			$query->param('modified_at', $time_now);
			$query->param('modified_by', $params['modified_by']);
			$result = $query->execute();
			
			if($result) {
				//新酒店ID
				$hotel_id = intval($result[0]);
				return $hotel_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除酒店
	 */
	public static function DeleteHotel($params) {
		try {
			//删除酒店
			$sql = "UPDATE t_hotel SET delete_flag = 1, hotel_status=0, modified_at=:modified_at, modified_by=:modified_by WHERE hotel_id IN :hotel_id_list";
			$query = DB::query($sql);
			$query->param('hotel_id_list', $params['hotel_id_list']);
			$query->param('modified_at', date('Y-m-d H:i:s', time()));
			$query->param('modified_by', $params['deleted_by']);
			$result = $query->execute();
			
			return $result;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新酒店
	 */
	public static function UpdateHotel($params) {
		try {
			//更新酒店
			$sql = "UPDATE t_hotel "
						. "SET hotel_name=:hotel_name, hotel_area=:hotel_area, hotel_type=:hotel_type, "
						. "hotel_price=:hotel_price, hotel_status=:hotel_status, modified_at=:modified_at, modified_by=:modified_by "
						. "WHERE hotel_id=:hotel_id";
			$query = DB::query($sql);
			$query->param('hotel_id', $params['hotel_id']);
			$query->param('hotel_name', $params['hotel_name']);
			$query->param('hotel_area', $params['hotel_area']);
			$query->param('hotel_type', $params['hotel_type']);
			$query->param('hotel_price', $params['hotel_price']);
			$query->param('hotel_status', $params['hotel_status']);
			$query->param('modified_at', date('Y-m-d H:i:s', time()));
			$query->param('modified_by', $params['modified_by']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新酒店状态
	 */
	public static function UpdateHotelStatus($params) {
//		try {
			$sql = "UPDATE t_hotel SET hotel_status = :hotel_status WHERE hotel_id = :hotel_id";
			$query = DB::query($sql);
			$query->param('hotel_id', $params['hotel_id']);
			$query->param('hotel_status', $params['hotel_status']);
			$result = $query->execute();
			
			return true;
//		} catch (Exception $e) {
//			return false;
//		}
	}

	/*
	 * 按条件获得酒店列表
	 */
	public static function SelectHotelList($params) {
//		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "created_at";
			$sql_order_method = "desc";
			$sql_limit = "";
			$sql_offset = "";
			
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'hotel_id_list':
						$sql_sub_where = array();
						foreach($param_value as $status_key => $status) {
							$sql_sub_where[] = ":hotel_id_" . $status_key;
							$sql_params['hotel_id_' . $status_key] = $status;
						}
						$sql_where[] = " th.hotel_id IN (" . implode(', ', $sql_sub_where) . ") ";
						break;
					case 'hotel_name':
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $name_key => $name) {
								$sql_sub_where[] = "th.hotel_name LIKE :hotel_name_" . $name_key;
								$sql_params['hotel_name_' . $name_key] = '%' . $name . '%';
							}
							$sql_where[] = " (" . implode(" OR ", $sql_sub_where) . ") ";
						}
						break;
					case 'hotel_status':
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $status_key => $status) {
								$sql_sub_where[] = ":hotel_status_" . $status_key;
								$sql_params['hotel_status_' . $status_key] = $status;
							}
							$sql_where[] = " th.hotel_status IN (" . implode(', ', $sql_sub_where) . ") ";
						}
						break;
					case 'hotel_area':
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $area_key => $area) {
								$sql_sub_where[] = ":hotel_area_" . $area_key;
								$sql_params['hotel_area_' . $area_key] = $area;
							}
							$sql_where[] = " th.hotel_area IN (" . implode(', ', $sql_sub_where) . ") ";
						}
						break;
					case 'hotel_type':
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $type_key => $type) {
								$sql_sub_where[] = ":hotel_type_" . $type_key;
								$sql_params['hotel_type_' . $type_key] = $type;
							}
							$sql_where[] = " th.hotel_type IN (" . implode(', ', $sql_sub_where) . ") ";
						}
						break;
					case 'price_min':
						if(is_numeric($param_value)) {
							$sql_where[] = " th.hotel_price >= :price_min ";
							$sql_params['price_min'] = floatval($param_value);
						}
						break;
					case 'price_max':
						if(is_numeric($param_value)) {
							$sql_where[] = " th.hotel_price <= :price_max ";
							$sql_params['price_max'] = floatval($param_value);
						}
						break;
					case 'created_by':
						$sql_where[] = " th.created_by = :created_by ";
						$sql_params['created_by'] = $param_value;
						break;
					case 'active_only':
						$sql_where[] = " th.delete_flag = 0 ";
						break;
					case 'sort_column':
						$sort_column_list = array('hotel_name', 'hotel_area', 'hotel_type', 'hotel_status', 'hotel_price', 'created_at', 'modified_at');
						if(in_array($param_value, $sort_column_list)) {
							$sql_order_column = $param_value;
						}
						break;
					case 'sort_method':
						if(in_array($param_value, array('asc', 'desc'))) {
							$sql_order_method = $param_value;
						}
						break;
					case '':
						break;
				}
			}
			
			if(isset($params['num_per_page']) && isset($params['page'])) {
				$sql_limit = intval($params['num_per_page']);
				$sql_offset = (intval($params['page']) - 1) * $sql_limit;
				$sql_limit = " LIMIT " . $sql_limit;
				$sql_offset = " OFFSET " . $sql_offset;
			}
			
			//符合条件的酒店总数获取
			$sql_count = "SELECT COUNT(DISTINCT th.hotel_id) hotel_count "
						. "FROM t_hotel th "
						. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_count = DB::query($sql_count);
			foreach ($sql_params as $param_key => $param_value) {
				$query_count->param($param_key, $param_value);
			}
			$result_count = $query_count->execute()->as_array();

			if(count($result_count)) {
				$hotel_count = intval($result_count[0]['hotel_count']);

				if($hotel_count) {
					//酒店信息获取
					$sql_hotel = "SELECT th.*, ma.area_name hotel_area_name, mht.hotel_type_name " 
							. "FROM t_hotel th " 
							. "LEFT JOIN m_area ma ON th.hotel_area = ma.area_id "
							. "LEFT JOIN m_hotel_type mht ON th.hotel_type = mht.hotel_type_id "
							. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
							. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
							. $sql_limit . $sql_offset;
					$query_hotel = DB::query($sql_hotel);
					foreach ($sql_params as $param_key => $param_value) {
						$query_hotel->param($param_key, $param_value);
					}
					$result_hotel = $query_hotel->execute()->as_array();

					if(count($result_hotel)) {
						$result = array(
							'hotel_count' => $hotel_count,
							'hotel_list' => $result_hotel,
							'start_number' => $sql_offset + 1,
							'end_number' => count($result_hotel) + $sql_offset,
						);
						return $result;
					}
				}
			}
			return false;
//		} catch (Exception $e) {
//			return false;
//		}
	}
	
	/*
	 * 获取特定单个酒店信息
	 */
	public static function SelectHotel($params) {
//		try {
			$sql_where = array();
			$sql_params = array();
			
			//酒店ID限定
			if(isset($params['hotel_id'])) {
				$sql_where[] = " th.hotel_id = :hotel_id ";
				$sql_params['hotel_id'] = $params['hotel_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " th.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql_hotel = "SELECT th.*, ma.area_name hotel_area_name, ma.area_description hotel_area_description, mht.hotel_type_name, tuc.user_name created_name, tum.user_name modified_name " 
					. "FROM t_hotel th " 
					. "LEFT JOIN m_area ma ON th.hotel_area = ma.area_id " 
					. "LEFT JOIN m_hotel_type mht ON th.hotel_type = mht.hotel_type_id " 
					. "LEFT JOIN t_user tuc ON th.created_by = tuc.user_id " 
					. "LEFT JOIN t_user tum ON th.modified_by = tum.user_id " 
					. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_hotel = DB::query($sql_hotel);
			foreach($sql_params as $param_key => $param_value) {
				$query_hotel->param($param_key, $param_value);
			}
			$result_hotel = $query_hotel->execute()->as_array();
			
			if(count($result_hotel) == 1) {
				$result = $result_hotel[0];
				return $result;
			} else {
				return false;
			}
//		} catch (Exception $e) {
//			return false;
//		}
	}
	
	/*
	 * 编辑酒店前编辑信息查验
	 */
	public static function CheckEditHotel($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		//酒店名称
		if(empty($params['hotel_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_hotel_name';
		} elseif(mb_strlen($params['hotel_name']) > 100) {
			$result['result'] = false;
			$result['error'][] = 'long_hotel_name';
		} elseif(Model_Hotel::CheckHotelNameDuplication($params['hotel_id'], $params['hotel_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_hotel_name';
		}
		
		//酒店区域
		if(empty($params['hotel_area'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_hotel_area';
		} elseif(!is_numeric($params['hotel_area']) || !is_int($params['hotel_area'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_hotel_area';
		} elseif(!Model_Area::CheckAreaIdExist($params['hotel_area'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_hotel_area';
		}
		
		//酒店类型
		if(empty($params['hotel_type'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_hotel_type';
		} elseif(!is_numeric($params['hotel_type']) || !is_int($params['hotel_type'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_hotel_type';
		} elseif(!Model_Hoteltype::CheckHotelTypeIdExist($params['hotel_type'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_hotel_type';
		}
		
		//价格
		if(!is_numeric($params['hotel_price']) || !is_int($params['hotel_price'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_hotel_price';
		} elseif(intval($params['hotel_price']) < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_hotel_price';
		}
		
		//公开状态
		if(!in_array($params['hotel_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		
		return $result;
	}
	
	/*
	 * 删除酒店前删除信息查验
	 */
	public static function CheckDeleteHotel($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_array($params['hotel_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_hotel_id';
		} elseif(!count($params['hotel_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_hotel_id';
		} else {
			$all_num_flag = true;
			
			foreach($params['hotel_id_list'] as $hotel_id) {
				if(!is_numeric($hotel_id)) {
					$result['result'] = false;
					$all_num_flag = false;
					$result['error'][] = 'nonum_hotel_id';
					break;
				}
			}
			
			if($all_num_flag) {
				$params_select = array('hotel_id_list' => $params['hotel_id_list']);
				$result_select = Model_Hotel::SelectHotelList($params_select);
				
				if($result_select['hotel_count'] != count(array_unique($params['hotel_id_list']))) {
					$result['result'] = false;
					$result['error'][] = 'error_hotel_id';
				} elseif($params['self_only']) {
					foreach($result_select['hotel_list'] as $hotel_select) {
						if($hotel_select['delete_by'] != $hotel_select) {
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
	 * 更新酒店公开状态前更新信息查验
	 */
	public static function CheckUpdateHotelStatus($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!in_array($params['hotel_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_hotel_status';
		}
		
		return $result;
	}
	
	/*
	 * 酒店名称重复查验
	 */
	public static function CheckHotelNameDuplication($hotel_id, $hotel_name) {
		try {
			//数据获取
			$sql = "SELECT hotel_id FROM t_hotel WHERE hotel_name = :hotel_name AND delete_flag = 0" . ($hotel_id ? " AND hotel_id != :hotel_id " : "");
			$query = DB::query($sql);
			if($hotel_id) {
				$query->param('hotel_id', $hotel_id);
			}
			$query->param('hotel_name', $hotel_name);
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
	 * 根据ID删除酒店
	 */
	public static function DeleteHotelById($hotel_id) {
		$sql_delete = "DELETE FROM t_hotel WHERE hotel_id = :hotel_id";
		$query_delete = DB::query($sql_delete);
		$query_delete->param('hotel_id', $hotel_id);
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
		$query_hotel->param('hotel_id', $hotel_id);
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
			$query_exist->param('hotel_id', $hotel_id);
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

}

