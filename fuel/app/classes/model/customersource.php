<?php

class Model_Customersource extends Model
{

	/*
	 * 获取全部有效顾客来源列表
	 */
	public static function GetCustomerSourceListActive() {
		$sql = "SELECT customer_source_id, customer_source_name FROM m_customer_source WHERE delete_flag = 0 ORDER BY sort_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查顾客来源ID是否有效
	 */
	public static function CheckCustomerSourceIdActive($customer_source_id) {
		$sql = "SELECT customer_source_id FROM m_customer_source WHERE customer_source_id = :customer_source_id AND delete_flag = 0";
		$query = DB::query($sql);
		$query->param(':customer_source_id', $customer_source_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}

