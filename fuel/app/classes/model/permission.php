<?php

class Model_Permission extends Model
{

	/*
	 * 
	 */
	public static function GetPermissionListAll() {
		$permission_list = array();
		
		$sql_permission = "SELECT mg.function_group_id master_group_id, mg.function_group_name master_group_name, " 
					. "sg.function_group_id sub_group_id, sg. function_group_name sub_group_name, " 
					. "f.function_id, f.function_name, a.authority_id, a.authority_name " 
					. "FROM (SELECT * FROM m_function_group WHERE function_group_parent IS NULL) mg " 
					. "LEFT JOIN (SELECT * FROM m_function_group WHERE function_group_parent IS NOT NULL) sg ON sg.function_group_parent = mg.function_group_id "
					. "LEFT JOIN m_function f ON f.function_group_id = sg.function_group_id " 
					. "LEFT JOIN m_authority a ON a.function_id = f.function_id ";
		$query_permission = DB::query($sql_permission);
		$result_permission = $query_permission->execute()->as_array();
		
		foreach($result_permission as $permission) {
			if(isset($permission_list[$permission['master_group_id']])) {
				if($permission['sub_group_id']) {
					if(isset($permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']])) {
						if($permission['function_id']) {
							if(isset($permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']]['function_list'][$permission['function_id']])) {
								if($permission['authority_id']) {
									$permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']]['function_list'][$permission['function_id']]['authority_list'][$permission['authority_id']] = array(
										'name' => $permission['authority_name'],
									);
								}
							} else {
								$permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']]['function_list'][$permission['function_id']] = array(
									'name' => $permission['function_name'],
									'authority_list' => array(),
								);
								if($permission['authority_id']) {
									$permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']]['function_list'][$permission['function_id']]['authority_list'][$permission['authority_id']] = array(
										'name' => $permission['authority_name'],
									);
								}
							}
						}
					} else {
						$permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']] = array(
							'name' => $permission['sub_group_name'],
							'function_list' => array(),
						);
						if($permission['function_id']) {
							$permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']]['function_list'][$permission['function_id']] = array(
								'name' => $permission['function_name'],
								'authority_list' => array(),
							);
							if($permission['authority_id']) {
								$permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']]['function_list'][$permission['function_id']]['authority_list'][$permission['authority_id']] = array(
									'name' => $permission['authority_name'],
								);
							}
						}
					}
				}
			} else {
				$permission_list[$permission['master_group_id']] = array(
					'name' => $permission['master_group_name'],
					'sub_group_list' => array(),
				);
				if($permission['sub_group_id']) {
					$permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']] = array(
						'name' => $permission['sub_group_name'],
						'function_list' => array(),
					);
					if($permission['function_id']) {
						$permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']]['function_list'][$permission['function_id']] = array(
							'name' => $permission['function_name'],
							'authority_list' => array(),
						);
						if($permission['authority_id']) {
							$permission_list[$permission['master_group_id']]['sub_group_list'][$permission['sub_group_id']]['function_list'][$permission['function_id']]['authority_list'][$permission['authority_id']] = array(
								'name' => $permission['authority_name'],
							);
						}
					}
				}
			}
		}
		
		return $permission_list;
	}

}

