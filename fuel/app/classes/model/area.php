<?php

class Model_Area extends Model
{

	//获得符合特定条件的地区列表
	public static function GetAreaList($params) {
		try{
			$sql_where = array();
			$sql_params = array();
			
			//有效地区限定
			if(isset($params['active_only'])) {
				$sql_where[] = " ma.delete_flag = 0 ";
			}
			
			$sql = "SELECT ma.* FROM m_area ma " . (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") . " ORDER BY ma.sort_id, ma.area_id";
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
	 * 检查地区ID是否存在
	 */
	public static function CheckAreaIdExist($area_id, $active_check = 0) {
		try {
			$sql = "SELECT area_id FROM m_area WHERE area_id = :area_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('area_id', $area_id);
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

}

