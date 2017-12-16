<?php

class Model_Travelreason extends Model
{

	/*
	 * 获取全部有效旅游目的列表
	 */
	public static function GetTravelReasonListActive() {
		$sql = "SELECT travel_reason_id, travel_reason_name FROM m_travel_reason WHERE delete_flag = 0 ORDER BY sort_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查旅游目的ID是否有效
	 */
	public static function CheckTravelReasonIdActive($travel_reason_id) {
		$sql = "SELECT travel_reason_id FROM m_travel_reason WHERE travel_reason_id = :travel_reason_id AND delete_flag = 0";
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

