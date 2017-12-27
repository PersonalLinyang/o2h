<?php

class Model_Roomtype extends Model
{

	/*
	 * 获取全部有效房型列表
	 */
	public static function GetRoomTypeListActive() {
		$sql = "SELECT room_type_id, room_type_name FROM m_room_type WHERE delete_flag = 0 ORDER BY room_type_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查房型ID是否有效
	 */
	public static function CheckRoomTypeIdActive($room_type_id) {
		$sql = "SELECT room_type_id FROM m_room_type WHERE room_type_id = :room_type_id AND delete_flag = 0";
		$query = DB::query($sql);
		$query->param('room_type_id', $room_type_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}

