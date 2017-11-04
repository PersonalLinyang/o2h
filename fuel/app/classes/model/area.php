<?php

class Model_Area extends Model
{

	/*
	 * 获取全部地方信息
	 */
	public static function GetAreaListAll() {
		$sql_area = "SELECT * FROM m_area ORDER BY area_id";
		$query_area = DB::query($sql_area);
		$area_list = $query_area->execute()->as_array();
		
		return $area_list;
	}

}

