<?php

class Model_Area extends Model
{

	/*
	 * 获取全部有效地方信息
	 */
	public static function GetAreaListActive() {
		$sql_area = "SELECT area_id, area_name, area_description FROM m_area WHERE delete_flag = 0 ORDER BY sort_id";
		$query_area = DB::query($sql_area);
		$area_list = $query_area->execute()->as_array();
		
		return $area_list;
	}

}

