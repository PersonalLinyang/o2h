<?php

class Model_Roomtype extends Model
{

	/*
	 * 添加房型
	 */
	public static function InsertRoomType($params) {
		try {
			$sql = "INSERT INTO m_room_type(room_type_name, delete_flag, sort_id) VALUES(:room_type_name, 0, 1)";
			$query = DB::query($sql);
			$query->param('room_type_name', $params['room_type_name']);
			$result = $query->execute();
			
			if($result) {
				//新房型ID
				$room_type_id = intval($result[0]);
				return $room_type_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除房型
	 */
	public static function DeleteRoomType($params) {
		try {
			//删除房型
			$sql_type = "UPDATE m_room_type SET delete_flag = 1 WHERE room_type_id = :room_type_id";
			$query_type = DB::query($sql_type);
			$query_type->param('room_type_id', $params['room_type_id']);
			$result_type = $query_type->execute();
			
			return $result_type;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新房型名称
	 */
	public static function UpdateRoomType($params) {
		try {
			$sql = "UPDATE m_room_type SET room_type_name = :room_type_name WHERE room_type_id = :room_type_id";
			$query = DB::query($sql);
			$query->param('room_type_id', $params['room_type_id']);
			$query->param('room_type_name', $params['room_type_name']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	//获得符合特定条件的房型
	public static function SelectRoomTypeList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效酒店类别限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mrt.delete_flag = 0 ";
			}
			//获取所属酒店数
			if(isset($params['hotel_count_flag'])) {
				$sql_select[] = " COUNT(rhr.hotel_id) hotel_count ";
				$sql_from[] = " LEFT JOIN (SELECT * FROM r_hotel_room WHERE hotel_id IN (SELECT hotel_id FROM t_hotel WHERE delete_flag = 0)) rhr "
							. "ON rhr.room_type_id = mrt.room_type_id ";
				$sql_group_by[] = " mrt.room_type_id ";
			}
			
			$sql = "SELECT mrt.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_room_type mrt " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mrt.sort_id, mrt.room_type_id";
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
	 * 获取特定单个房型信息
	 */
	public static function SelectRoomType($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//房型ID限定
			if(isset($params['room_type_id'])) {
				$sql_where[] = " mrt.room_type_id = :room_type_id ";
				$sql_params['room_type_id'] = $params['room_type_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " mrt.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql = "SELECT * FROM m_room_type mrt " 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query = DB::query($sql);
			foreach($sql_params as $param_key => $param_value) {
				$query->param($param_key, $param_value);
			}
			$result = $query->execute()->as_array();
			
			if(count($result) == 1) {
				return $result[0];
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 编辑房型前编辑信息查验
	 */
	public static function CheckEditRoomType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//房型名称
		if(empty($params['room_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_room_type_name';
		} elseif(mb_strlen($params['room_type_name']) > 50) {
			$result['result'] = false;
			$result['error'][] = 'long_room_type_name';
		} elseif(Model_Roomtype::CheckRoomTypeNameDuplication($params['room_type_id'], $params['room_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_room_type_name';
		}
		
		return $result;
	}
	
	/*
	 * 删除房型前删除信息查验
	 */
	public static function CheckDeleteRoomType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($params['room_type_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_room_type_id';
		} elseif(!Model_Roomtype::CheckRoomTypeIdExist($params['room_type_id'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_room_type_id';
		} else {
			//获取酒店信息
			$params_select = array(
				'room_type' => array($params['room_type_id']),
				'active_only' => 1,
			);
			$room_select = Model_Hotel::SelectHotelList($params_select);
			
			if($room_select['hotel_count']) {
				$result['result'] = false;
				$result['error'][] = 'error_hotel_list';
			}
		}
		
		return $result;
	}
	
	/*
	 * 检查房型ID是否存在
	 */
	public static function CheckRoomTypeIdExist($room_type_id, $active_check = 0) {
		try {
			$sql = "SELECT room_type_id FROM m_room_type WHERE room_type_id = :room_type_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('room_type_id', $room_type_id);
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
	
	/*
	 * 检查房型ID是否全部存在
	 */
	public static function CheckRoomTypeIdListExist($room_type_id_list, $active_check = 0) {
		try {
			$sql = "SELECT room_type_id FROM m_room_type WHERE room_type_id IN :room_type_id_list " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('room_type_id_list', $room_type_id_list);
			$result = $query->execute()->as_array();
			
			if(count($result) == count(array_unique($room_type_id_list))) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 房型名称重复查验
	 */
	public static function CheckRoomTypeNameDuplication($room_type_id, $room_type_name) {
		try {
			//数据获取
			$sql = "SELECT room_type_id FROM m_room_type WHERE room_type_name = :room_type_name AND delete_flag = 0" . ($room_type_id ? " AND room_type_id != :room_type_id " : "");
			$query = DB::query($sql);
			if($room_type_id) {
				$query->param('room_type_id', $room_type_id);
			}
			$query->param('room_type_name', $room_type_name);
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

}

