<?php

class Model_Customersource extends Model
{

	//获得符合特定条件的顾客来源
	public static function SelectCustomerSourceList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效顾客来源限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mcs.delete_flag = 0 ";
			}
			
			$sql = "SELECT mcs.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_customer_source mcs " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mcs.sort_id, mcs.customer_source_id";
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
	 * 检查顾客来源ID是否存在
	 */
	public static function CheckCustomerSourceIdExist($customer_source_id, $active_check = 0) {
		try {
			$sql = "SELECT customer_source_id FROM m_customer_source WHERE customer_source_id = :customer_source_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('customer_source_id', $customer_source_id);
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

