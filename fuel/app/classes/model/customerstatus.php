<?php

class Model_Customerstatus extends Model
{

	/*
	 * 获取全部顾客状态列表
	 */
	public static function GetCustomerStatusListAll() {
		$sql = "SELECT * FROM m_customer_status ORDER BY customer_status_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查顾客状态ID是否存在
	 */
	public static function CheckExistCustomerStatusId($customer_status_id) {
		$sql = "SELECT * FROM m_customer_status WHERE customer_status_id = :customer_status_id";
		$query = DB::query($sql);
		$query->param(':customer_status_id', $customer_status_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}

