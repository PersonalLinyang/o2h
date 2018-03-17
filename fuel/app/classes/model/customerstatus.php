<?php

class Model_Customerstatus extends Model
{

	//获得符合特定条件的顾客状态
	public static function SelectCustomerStatusList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效顾客状态限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mcs.delete_flag = 0 ";
			}
			
			//有效顾客状态限定
			if(isset($params['sort_id_list'])) {
				$sql_where[] = " mcs.sort_id IN :sort_id_list ";
				$sql_params['sort_id_list'] = $params['sort_id_list'];
			}
			
			//未删除顾客的顾客状态限定
			if(isset($params['active_customer_only'])) {
				$sql_where[] = " mcs.customer_status_name NOT LIKE '失效%' ";
			}
			
			$sql = "SELECT mcs.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_customer_status mcs " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mcs.sort_id, mcs.customer_status_id";
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
	 * 获取特定单个顾客状态信息
	 */
	public static function SelectCustomerStatus($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//顾客状态ID限定
			if(isset($params['customer_status_id'])) {
				$sql_where[] = " mcs.customer_status_id = :customer_status_id ";
				$sql_params['customer_status_id'] = $params['customer_status_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " mcs.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql = "SELECT * FROM m_customer_status mcs " 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query = DB::query($sql);
			foreach($sql_params as $param_key => $param_value) {
				$query->param($param_key, $param_value);
			}
			$result = $query->execute()->as_array();
			
			if(count($result) == 1) {
				return $result[0];
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 获取特定顾客状态的下个状态信息
	 */
	public static function SelectNextCustomerStatus($customer_status_id) {
		$next_status_list = array(
			'1' => 2,
			'2' => 3,
			'3' => 4,
			'4' => 5,
			'5' => 6,
			'6' => 7,
			'7' => 8,
			'8' => 9,
			'9' => 10,
		);
		if(in_array($customer_status_id, array_keys($next_status_list))) {
			return Model_Customerstatus::SelectCustomerStatus(array('customer_status_id' => $next_status_list[$customer_status_id], 'active_only' => true));
		} else {
			return false;
		}
	}
	
	/*
	 * 检查顾客状态ID是否存在
	 */
	public static function CheckCustomerStatusIdExist($customer_status_id, $active_check = 0) {
		try {
			$sql = "SELECT customer_status_id FROM m_customer_status WHERE customer_status_id = :customer_status_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('customer_status_id', $customer_status_id);
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
