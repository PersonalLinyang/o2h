<?php

class Model_Customercosttype extends Model
{

	/*
	 * 获取全部成本项目列表
	 */
	public static function GetCustomerCostTypeListExceptOther() {
		$sql = "SELECT * FROM m_customer_cost_type WHERE customer_cost_type_id>1 ORDER BY customer_cost_type_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查成本项目ID是否存在
	 */
	public static function CheckExistCustomerCostTypeId($customer_cost_type_id) {
		$sql = "SELECT * FROM m_customer_cost_type WHERE customer_cost_type_id = :customer_cost_type_id";
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

