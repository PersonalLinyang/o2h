<?php

class Model_Usertype extends Model
{

	/*
	 * 添加用户类型
	 */
	public static function InsertUserType($params) {
		try {
			//添加用户类型
			$sql_user_type = "INSERT INTO m_user_type(user_type_name, special_level, sort_id) VALUES(:user_type_name, :special_level, :sort_id)";
			$query_user_type = DB::query($sql_user_type);
			$query_user_type->param('user_type_name', $params['user_type_name']);
			$query_user_type->param('special_level', $params['special_level']);
			$query_user_type->param('sort_id', (2 - intval($params['special_level'])));
			$result_user_type = $query_user_type->execute();
			
			//添加成功的同时为该用户类型添加权限
			if($result_user_type) {
				$user_type_id = intval($result_user_type[0]);
				$permission_list = array();
				
				foreach($params['permission'] as $permission_type => $permission_id_list) {
					foreach($permission_id_list as $permission_id) {
						$permission_list[] = array(
							'permission_type' => $permission_type,
							'permission_id' => $permission_id,
						);
					}
				}
				
				if(count($permission_list)) {
					$result_permission = Model_Permission::InsertPermissionList($user_type_id, $permission_list);
				}
				
				return $user_type_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新用户类型
	 */
	public static function UpdateUserType($params) {
		try {
			//更新用户类型
			$sql_user_type = "UPDATE m_user_type SET "
							. "user_type_name = :user_type_name, "
							. "special_level = :special_level, "
							. "sort_id = :sort_id "
							. "WHERE user_type_id = :user_type_id";
			$query_user_type = DB::query($sql_user_type);
			$query_user_type->param('user_type_id', $params['user_type_id']);
			$query_user_type->param('user_type_name', $params['user_type_name']);
			$query_user_type->param('special_level', $params['special_level']);
			$query_user_type->param('sort_id', (2 - intval($params['special_level'])));
			$result_user_type = $query_user_type->execute();
			
			//删除当前该用户类型所持有的全部权限
			Model_Permission::DeletePermissionByUserType($params['user_type_id']);
			
			//更新成功的同时为该用户类型更新权限
			$permission_list = array();
			foreach($params['permission'] as $permission_type => $permission_id_list) {
				foreach($permission_id_list as $permission_id) {
					$permission_list[] = array(
						'permission_type' => $permission_type,
						'permission_id' => $permission_id,
					);
				}
			}
			if(count($permission_list)) {
				Model_Permission::InsertPermissionList($params['user_type_id'], $permission_list);
			}
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 根据ID删除用户类型
	 */
	public static function DeleteUserType($user_type_id) {
		try {
			$sql_user = "UPDATE t_user SET user_type = 2 WHERE user_type = :user_type";
			$query_user = DB::query($sql_user);
			$query_user->param('user_type', $user_type_id);
			$result_user = $query_user->execute();
			
			$sql_permission = "DELETE FROM r_permission WHERE user_type_id = :user_type_id";
			$query_permission = DB::query($sql_permission);
			$query_permission->param('user_type_id', $user_type_id);
			$result_permission = $query_permission->execute();
			
			$sql_user_type = "DELETE FROM m_user_type WHERE user_type_id = :user_type_id";
			$query_user_type = DB::query($sql_user_type);
			$query_user_type->param('user_type_id', $user_type_id);
			$result_user_type = $query_user_type->execute();
			
			return $result_user_type;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 获得特定条件的用户类型列表
	 */
	public static function SelectUserTypeListWithUserNum($params = array()) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//用户类型ID限定
			if(isset($params['special_level'])) {
				$sql_where[] = " mut.special_level IN :special_level ";
				$sql_params['special_level'] = $params['special_level'];
			}
			
			//数据获取
			$sql = "SELECT mut.*, COUNT(tu.user_id) user_count "
				. "FROM m_user_type mut "
				. "LEFT JOIN t_user tu ON tu.user_type = mut.user_type_id " 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
				. "GROUP BY mut.user_type_id "
				. "ORDER BY mut.sort_id, mut.user_type_id ";
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
	 * 获得特定单个用户类型信息
	 */
	public static function SelectUserType($params = array()) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//用户类型ID限定
			if(isset($params['user_type_id'])) {
				$sql_where[] = " mut.user_type_id = :user_type_id ";
				$sql_params['user_type_id'] = $params['user_type_id'];
			}
			
			//数据获取
			$sql = "SELECT mut.* FROM m_user_type mut " . (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
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
	 * 添加/更新用户类型前信息查验
	 */
	public static function CheckEditUserType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//用户类型名称
		if(empty($params['user_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_user_type_name';
		} elseif(mb_strlen($params['user_type_name']) > 30) {
			$result['result'] = false;
			$result['error'][] = 'long_user_type_name';
		} elseif(Model_Usertype::CheckUserTypeNameDuplication($params['user_type_id'], $params['user_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_user_type_name';
		}
		
		//特殊用户类型
		if(!in_array($params['special_level'], array('1', '0'))) {
			$result['result'] = false;
			$result['error'][] = 'error_special_level';
		}
		
		//权限是否全部为数字
		foreach($params['permission'] as $permission_type => $permission_id_list) {
			foreach($permission_id_list as $permission_id) {
				if(!is_numeric($permission_id)) {
					$result['result'] = false;
					$result['error'][] = 'nonum_permission';
					break;
				}
			}
			if(in_array('nonum_permission', $result['error'])) {
				break;
			}
		}
		
		//是否包含不应存在的权限
		if(!in_array('nonum_permission', $result['error'])) {
			if(count($params['permission']['1'])) {
				if(count($params['permission']['2'])) {
					$sub_group_list = Model_Functiongroup::SelectSubGroupIdList(array('master_group_id_list' => $params['permission']['1']));
					foreach($params['permission']['2'] as $sub_group_id) {
						if(!in_array($sub_group_id, $sub_group_list)) {
							$result['result'] = false;
							$result['error'][] = 'error_permission1';
							break;
						}
					}
					
					if(!in_array('error_permission', $result['error'])) {
						if(count($params['permission']['3'])) {
							$function_list = Model_Function::SelectFunctionIdList(array('function_group_id_list' => $params['permission']['2']));
							foreach($params['permission']['3'] as $function_id) {
								if(!in_array($function_id, $function_list)) {
									$result['result'] = false;
									$result['error'][] = 'error_permission2';
									break;
								}
							}
							
							if(!in_array('error_permission', $result['error']) && count($params['permission']['4'])) {
								$authority_list = Model_Authority::SelectAuthorityIdList(array('function_id_list' => $params['permission']['3']));
								foreach($params['permission']['4'] as $authority_id) {
									if(!in_array($authority_id, $authority_list)) {
										$result['result'] = false;
										$result['error'][] = 'error_permission3';
										break;
									}
								}
							}
						} elseif(count($params['permission']['4'])) {
							$result['result'] = false;
							$result['error'][] = 'error_permission4';
						}
					}
				} elseif(count($params['permission']['3']) || count($params['permission']['4'])) {
					$result['result'] = false;
					$result['error'][] = 'error_permission5';
				}
			} elseif(count($params['permission']['2']) || count($params['permission']['3']) || count($params['permission']['4'])) {
				$result['result'] = false;
				$result['error'][] = 'error_permission6';
			}
		}
		
		return $result;
	}
	
	/*
	 * 用户类型名称重复查验
	 */
	public static function CheckUserTypeNameDuplication($user_type_id, $user_type_name) {
		try {
			//数据获取
			$sql = "SELECT user_type_id FROM m_user_type WHERE user_type_name = :user_type_name" . ($user_type_id ? " AND user_type_id != :user_type_id" : "");
			$query = DB::query($sql);
			if($user_type_id) {
				$query->param('user_type_id', $user_type_id);
			}
			$query->param('user_type_name', $user_type_name);
			$result = $query->execute()->as_array();
			
			if(count($result)) {
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return true;
		}
	}

}

