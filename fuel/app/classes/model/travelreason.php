<?php

class Model_Travelreason extends Model
{

	/*
	 * 获取全部旅游目的列表
	 */
	public static function GetTravelReasonListAll() {
		$sql = "SELECT * FROM m_travel_reason ORDER BY travel_reason_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查旅游目的ID是否存在
	 */
	public static function CheckExistTravelReasonId($travel_reason_id) {
		$sql = "SELECT * FROM m_travel_reason WHERE travel_reason_id = :travel_reason_id";
		$query = DB::query($sql);
		$query->param(':travel_reason_id', $travel_reason_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}

