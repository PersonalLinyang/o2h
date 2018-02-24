<?php

class Model_Customerstatus extends Model
{

	//获得符合特定条件的顾客状态
	public static function SelectCustomerStatusList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效顾客状态限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mcs.delete_flag = 0 ";
			}
			
			//未删除顾客的顾客状态限定
			if(isset($params['active_customer_only'])) {
				$sql_where[] = " mcs.customer_status_name NOT LIKE '失效%' ";
			}
			
			$sql = "SELECT mcs.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_customer_status mcs " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mcs.sort_id, mcs.customer_status_id";
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
	 * 检查顾客状态ID是否存在
	 */
	public static function CheckCustomerStatusIdExist($customer_status_id, $active_check = 0) {
		try {
			$sql = "SELECT customer_status_id FROM m_customer_status WHERE customer_status_id = :customer_status_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('customer_status_id', $customer_status_id);
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
