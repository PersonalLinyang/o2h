<?php

class Model_Hoteltype extends Model
{

	/*
	 * 添加酒店类别
	 */
	public static function InsertHotelType($params) {
		$sql_insert = "INSERT INTO m_hotel_type(hotel_type_name) VALUES(:hotel_type_name)";
		$query_insert = DB::query($sql_insert);
		$query_insert->param(':hotel_type_name', $params['hotel_type_name']);
		$result_insert = $query_insert->execute();
		
		return $result_insert;
	}
	
	/*
	 * 根据ID删除酒店类别
	 */
	public static function DeleteHotelTypeById($hotel_type_id) {
		$sql_delete = "DELETE FROM m_hotel_type WHERE hotel_type_id = :hotel_type_id";
		$query_delete = DB::query($sql_delete);
		$query_delete->param(':hotel_type_id', $hotel_type_id);
		$result_delete = $query_delete->execute();
		
		return $result_delete;
	}
	
	/*
	 * 更新酒店类别名称
	 */
	public static function UpdateHotelType($params) {
		$sql_update = "UPDATE m_hotel_type SET hotel_type_name = :hotel_type_name WHERE hotel_type_id = :hotel_type_id";
		$query_update = DB::query($sql_update);
		$query_update->param(':hotel_type_id', $params['hotel_type_id']);
		$query_update->param(':hotel_type_name', $params['hotel_type_name']);
		$result_update = $query_update->execute();
		
		return $result_update;
	}

	/*
	 * 获取全部有效酒店类别列表
	 */
	public static function GetHotelTypeListActive() {
		$sql_hotel_type = "SELECT hotel_type_id, hotel_type_name FROM m_hotel_type WHERE delete_flag = 0 ORDER BY hotel_type_id";
		$query_hotel_type = DB::query($sql_hotel_type);
		$hotel_type_list = $query_hotel_type->execute()->as_array();
		
		return $hotel_type_list;
	}

	/*
	 * 获取全部酒店类别信息
	 */
	public static function GetHotelTypeInfoAll() {
		$sql_hotel_type = "SELECT mht.hotel_type_id, mht.hotel_type_name, COUNT(th.hotel_id) hotel_count "
						. "FROM m_hotel_type mht LEFT JOIN t_hotel th ON th.hotel_type = mht.hotel_type_id " 
						. "GROUP BY hotel_type_id, hotel_type_name ORDER BY hotel_type_id";
		$query_hotel_type = DB::query($sql_hotel_type);
		$hotel_type_list = $query_hotel_type->execute()->as_array();
		
		return $hotel_type_list;
	}
	
	/*
	 * 根据ID获取主功能组信息
	 */
	public static function SelectHotelTypeById($hotel_type_id) {
		if(!is_numeric($hotel_type_id)) {
			return false;
		}
		
		$sql = "SELECT * FROM m_hotel_type WHERE hotel_type_id = :hotel_type_id";
		$query = DB::query($sql);
		$query->param(':hotel_type_id', $hotel_type_id);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	/*
	 * 添加酒店类别前添加信息查验
	 */
	public static function CheckInsertHotelType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!isset($params['hotel_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_name';
		} elseif(empty($params['hotel_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_hotel_type WHERE hotel_type_name = :hotel_type_name";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param(':hotel_type_name', $params['hotel_type_name']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				$result['result'] = false;
				$result['error'][] = 'duplication';
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除酒店类别前删除ID查验
	 */
	public static function CheckDeleteHotelTypeById($hotel_type_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($hotel_type_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM m_hotel_type WHERE hotel_type_id = :hotel_type_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':hotel_type_id', $hotel_type_id);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}
	
	/*
	 * 更新酒店类别前更新信息查验
	 */
	public static function CheckUpdateHotelType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($params['hotel_type_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if(empty($params['hotel_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_hotel_type WHERE hotel_type_name = :hotel_type_name";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param(':hotel_type_name', $params['hotel_type_name']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				if($result_duplication[0]['hotel_type_id'] == $params['hotel_type_id']) {
					$result['result'] = false;
					$result['error'][] = 'nomodify';
				} else {
					$result['result'] = false;
					$result['error'][] = 'duplication';
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 检查酒店类别ID是否存在
	 */
	public static function CheckExistHotelTypeId($hotel_type_id) {
		$sql = "SELECT * FROM m_hotel_type WHERE hotel_type_id = :hotel_type_id";
		$query = DB::query($sql);
		$query->param(':hotel_type_id', $hotel_type_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}

