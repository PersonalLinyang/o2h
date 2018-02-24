<?php

class Model_Travelreason extends Model
{

	//获得符合特定条件的旅游目的
	public static function SelectTravelReasonList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效旅游目的限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mtr.delete_flag = 0 ";
			}
			
			$sql = "SELECT mtr.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_travel_reason mtr " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mtr.sort_id, mtr.travel_reason_id";
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
	 * 检查旅游目的ID是否存在
	 */
	public static function CheckTravelReasonIdExist($travel_reason_id, $active_check = 0) {
		try {
			$sql = "SELECT travel_reason_id FROM m_travel_reason WHERE travel_reason_id = :travel_reason_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('travel_reason_id', $travel_reason_id);
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

