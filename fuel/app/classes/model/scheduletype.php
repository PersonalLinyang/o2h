<?php

class Model_Scheduletype extends Model
{
	
	//获得符合特定条件的日程类型
	public static function SelectScheduleTypeList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效日程类型限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mst.delete_flag = 0 ";
			}
			
			$sql = "SELECT mst.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_schedule_type mst " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mst.sort_id, mst.schedule_type_id";
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
	 * 检查日程类型ID是否存在
	 */
	public static function CheckScheduleTypeIdExist($schedule_type_id, $active_check = 0) {
		try {
			$sql = "SELECT schedule_type_id FROM m_schedule_type WHERE schedule_type_id = :schedule_type_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('schedule_type_id', $schedule_type_id);
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

