<?php

class Model_Customercosttype extends Model
{

	/*
	 * 获取全部顾客来源列表
	 */
	public static function GetCustomerCostTypeListExceptOther() {
		$sql = "SELECT customer_cost_type_id, customer_cost_type_name FROM m_customer_cost_type WHERE customer_cost_type_id>1 ORDER BY customer_cost_type_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}

}

