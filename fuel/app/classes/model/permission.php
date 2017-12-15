<?php

class Model_Permission extends Model
{

	/*
	 * 获取全部系统权限列表
	 */
	public static function GetPermissionListAll() {
		$permission_list = array();
		$group_parent_list = array();
		$function_group_list = array();
		
		$sql_master_group = "SELECT function_group_id, function_group_name, special_flag FROM m_function_group WHERE function_group_parent IS NULL ORDER BY function_group_id ASC";
		$query_master_group = DB::query($sql_master_group);
		$result_master_group = $query_master_group->execute()->as_array();
		
		foreach($result_master_group as $master_group) {
			$permission_list[$master_group['function_group_id']] = array(
				'name' => $master_group['function_group_name'],
				'special_flag' => $master_group['special_flag'],
				'sub_group_list' => array(),
			);
		}
		
		$sql_sub_group = "SELECT function_group_id, function_group_name, function_group_parent, special_flag FROM m_function_group WHERE function_group_parent IS NOT NULL ORDER BY function_group_id ASC";
		$query_sub_group = DB::query($sql_sub_group);
		$result_sub_group = $query_sub_group->execute()->as_array();
		
		foreach($result_sub_group as $sub_group) {
			if(isset($permission_list[$sub_group['function_group_parent']])) {
				$permission_list[$sub_group['function_group_parent']]['sub_group_list'][$sub_group['function_group_id']] = array(
					'name' => $sub_group['function_group_name'],
					'special_flag' => $sub_group['special_flag'],
					'function_list' => array(),
				);
				$group_parent_list[$sub_group['function_group_id']] = $sub_group['function_group_parent'];
			}
		}
		
		$sql_function = "SELECT function_id, function_name, function_group_id, special_flag FROM m_function ORDER BY function_id ASC";
		$query_function = DB::query($sql_function);
		$result_function = $query_function->execute()->as_array();
		
		foreach($result_function as $function) {
			if(isset($group_parent_list[$function['function_group_id']])) {
				$permission_list[$group_parent_list[$function['function_group_id']]]['sub_group_list'][$function['function_group_id']]['function_list'][$function['function_id']] = array(
					'name' => $function['function_name'],
					'special_flag' => $function['special_flag'],
					'authority_list' => array(),
				);
				$function_group_list[$function['function_id']] = array('master' => $group_parent_list[$function['function_group_id']], 'sub' => $function['function_group_id']);
			}
		}
		
		$sql_authority = "SELECT authority_id, authority_name, function_id, special_flag FROM m_authority ORDER BY authority_id ASC";
		$query_authority = DB::query($sql_authority);
		$result_authority = $query_authority->execute()->as_array();
		
		foreach($result_authority as $authority) {
			if(isset($function_group_list[$authority['authority_id']])) {
				$psermission_list[$function_group_list[$authority['authority_id']]['master']]['sub_group_list'][$function_group_list[$authority['authority_id']]['sub']]['function_list'][$authority['function_id']]['authority_list'][$authority['authority_id']] = array(
					'name' => $authority['authority_name'],
					'special_flag' => $authority['special_flag'],
				);
			}
		}
		
		return $permission_list;
	}

}

