<?php

class Model_Roomtype extends Model
{

	/*
	 * 获取全部房型列表
	 */
	public static function GetRoomTypeListAll() {
		$sql = "SELECT room_type_id, room_type_name FROM m_room_type ORDER BY room_type_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查房型ID是否存在
	 */
	public static function CheckExistRoomTypeId($room_type_id) {
		$sql = "SELECT * FROM m_room_type WHERE room_type_id = :room_type_id";
		$query = DB::query($sql);
		$query->param(':room_type_id', $room_type_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}

