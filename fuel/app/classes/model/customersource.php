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

}

