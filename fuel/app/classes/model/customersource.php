<?php

class Model_Customersource extends Model
{

	/*
	 * 获取全部顾客来源列表
	 */
	public static function GetCustomerSourceListAll() {
		$sql = "SELECT * FROM m_customer_source ORDER BY customer_source_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查顾客来源ID是否存在
	 */
	public static function CheckExistCustomerSourceId($customer_source_id) {
		$sql = "SELECT * FROM m_customer_source WHERE customer_source_id = :customer_source_id";
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

