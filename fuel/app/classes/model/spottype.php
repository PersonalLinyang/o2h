<?php

class Model_Spottype extends Model
{

	/*
	 * 获取全部景点类别信息
	 */
	public static function GetSpotTypeListAll() {
		$sql_spot_type = "SELECT * FROM m_spot_type ORDER BY spot_type_id";
		$query_spot_type = DB::query($sql_spot_type);
		$spot_type_list = $query_spot_type->execute()->as_array();
		
		return $spot_type_list;
	}

}

