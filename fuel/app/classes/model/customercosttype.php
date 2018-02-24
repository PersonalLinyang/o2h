<?php

class Model_Customercosttype extends Model
{

	//获得符合特定条件的成本项目
	public static function SelectCustomerCostTypeList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效成本项目限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mcct.delete_flag = 0 ";
			}
			
			$sql = "SELECT mcct.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_customer_cost_type mcct " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mcct.sort_id, mcct.customer_cost_type_id";
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
	public static function CheckCustomerCostTypeIdExist($customer_cost_type_id, $active_check = 0) {
		try {
			$sql = "SELECT customer_cost_type_id FROM m_customer_cost_type WHERE customer_cost_type_id = :customer_cost_type_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('customer_cost_type_id', $customer_cost_type_id);
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
