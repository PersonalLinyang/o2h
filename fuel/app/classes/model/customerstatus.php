<?php

class Model_Customerstatus extends Model
{

	/*
	 * 获取全部有效顾客状态列表
	 */
	public static function GetCustomerStatusListActive() {
		$sql = "SELECT customer_status_id, customer_status_name FROM m_customer_status WHERE delete_flag = 0 ORDER BY sort_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 检查顾客状态ID是否有效
	 */
	public static function CheckCustomerStatusIdActive($customer_status_id) {
		$sql = "SELECT customer_status_id FROM m_customer_status WHERE customer_status_id = :customer_status_id AND delete_flag = 0";
		$query = DB::query($sql);
		$query->param('customer_status_id', $customer_status_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}

