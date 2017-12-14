<?php

class Model_Customercosttype extends Model
{

	/*
	 * 获取全部有效成本项目列表
	 */
	public static function GetCustomerCostTypeListActive() {
		$sql = "SELECT customer_cost_type_id, customer_cost_type_name FROM m_customer_cost_type WHERE delete_flag = 0 ORDER BY sort_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查成本项目ID是否有效
	 */
	public static function CheckCustomerCostTypeIdActive($customer_cost_type_id) {
		$sql = "SELECT customer_cost_type_id FROM m_customer_cost_type WHERE customer_cost_type_id = :customer_cost_type_id AND delete_flag = 0";
		$query = DB::query($sql);
		$query->param(':customer_cost_type_id', $customer_cost_type_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}

