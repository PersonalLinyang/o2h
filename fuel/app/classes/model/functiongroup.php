<?php

class Model_Functiongroup extends Model
{

	/*
	 * 添加功能组
	 */
	public static function InsertFunctionGroup($params) {
		try {
			$sql_group = "INSERT INTO m_function_group(function_group_name, special_flag"
						. ($params['function_group_type'] == 2 ? ", function_group_parent" : "")
						. ") VALUES(:function_group_name, :special_flag"
						. ($params['function_group_type'] == 2 ? ", :function_group_parent" : "") . ")";
			$query_group = DB::query($sql_group);
			$query_group->param('function_group_name', $params['function_group_name']);
			if($params['function_group_type'] == 2) {
				$query_group->param('function_group_parent', $params['function_group_parent']);
			}
			$query_group->param('special_flag', $params['special_flag']);
			$result_group = $query_group->execute();
			
			//添加成功的同时为系统管理员添加该权限
			if($result_group) {
				$group_id = intval($result_group[0]);
				
				$sql_permission = "INSERT INTO r_permission(user_type_id, permission_type, permission_id) VALUES(1, :permission_type, :permission_id)";
				$query_permission = DB::query($sql_permission);
				$query_permission->param('permission_type', $params['function_group_type']);
				$query_permission->param('permission_id', $group_id);
				$result_permission = $query_permission->execute();
			}
			
			return $result_group;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新功能组
	 */
	public static function UpdateFunctionGroup($params) {
		try {
			//更新功能组
			$sql_info = "UPDATE m_function_group SET function_group_name = :function_group_name, special_flag = :special_flag WHERE function_group_id = :function_group_id";
			$query_info = DB::query($sql_info);
			$query_info->param('function_group_id', $params['function_group_id']);
			$query_info->param('function_group_name', $params['function_group_name']);
			$query_info->param('special_flag', $params['special_flag']);
			$result_info = $query_info->execute();
			
			if($result_info && $params['special_flag']=='1') {
				//功能组被更新为特殊功能组时
				//更新下属副功能组为特殊功能组
				$sql_group = "UPDATE m_function_group SET special_flag = 1 WHERE function_group_parent = :function_group_id";
				$query_group = DB::query($sql_group);
				$query_group->param('function_group_id', $params['function_group_id']);
				$result_group = $query_group->execute();
				
				//更新下属功能为特殊功能
				$sql_function = "UPDATE m_function SET special_flag = 1 WHERE function_group_id IN "
							. "(SELECT function_group_id FROM m_function_group WHERE function_group_id = :function_group_id OR function_group_parent = :function_group_id)";
				$query_function = DB::query($sql_function);
				$query_function->param('function_group_id', $params['function_group_id']);
				$result_function = $query_function->execute();
				
				//更新下属权限为特殊权限
				$sql_authority = "UPDATE m_authority SET special_flag = 1 WHERE function_id IN "
							. "(SELECT function_id FROM m_function WHERE function_group_id IN "
							. "(SELECT function_group_id FROM m_function_group WHERE function_group_id = :function_group_id OR function_group_parent = :function_group_id))";
				$query_authority = DB::query($sql_authority);
				$query_authority->param('function_group_id', $params['function_group_id']);
				$result_authority = $query_authority->execute();
			}
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 根据ID删除功能组
	 */
	public static function DeleteFunctionGroup($function_group_id) {
		try{
			//删除相关权限许可
			$sql_permission_authority = "DELETE FROM r_permission WHERE permission_type = 4 AND permission_id IN "
									. "(SELECT authority_id FROM m_authority WHERE function_id IN "
									. "(SELECT function_id FROM m_function WHERE function_group_id IN "
									. "(SELECT function_group_id FROM m_function_group WHERE function_group_parent = :function_group_id OR function_group_id = :function_group_id)))";
			$query_permission_authority = DB::query($sql_permission_authority);
			$query_permission_authority->param('function_group_id', $function_group_id);
			$result_permission_authority = $query_permission_authority->execute();
			
			$sql_permission_function = "DELETE FROM r_permission WHERE permission_type = 3 AND permission_id IN "
									. "(SELECT function_id FROM m_function WHERE function_group_id IN "
									. "(SELECT function_group_id FROM m_function_group WHERE function_group_parent = :function_group_id OR function_group_id = :function_group_id))";
			$query_permission_function = DB::query($sql_permission_function);
			$query_permission_function->param('function_group_id', $function_group_id);
			$result_permission_function = $query_permission_function->execute();
			
			$sql_permission_function = "DELETE FROM r_permission WHERE permission_type = 2 AND permission_id IN "
									. "(SELECT function_group_id FROM m_function_group WHERE function_group_parent = :function_group_id OR function_group_id = :function_group_id)";
			$query_permission_function = DB::query($sql_permission_function);
			$query_permission_function->param('function_group_id', $function_group_id);
			$result_permission_function = $query_permission_function->execute();
			
			$sql_permission_sub = "DELETE FROM r_permission WHERE permission_type = 1 AND permission_id = :function_group_id";
			$query_permission_sub = DB::query($sql_permission_sub);
			$query_permission_sub->param('function_group_id', $function_group_id);
			$result_permission_sub = $query_permission_sub->execute();
			
			//删除权限
			$sql_authority = "DELETE FROM m_authority WHERE function_id IN " 
						. "(SELECT function_id FROM m_function WHERE function_group_id IN " 
						. "(SELECT function_group_id FROM m_function_group WHERE function_group_parent = :function_group_id OR function_group_id = :function_group_id))";
			$query_authority = DB::query($sql_authority);
			$query_authority->param('function_group_id', $function_group_id);
			$result_authority = $query_authority->execute();
			
			//删除功能
			$sql_function = "DELETE FROM m_function WHERE function_group_id IN " 
						. "(SELECT function_group_id FROM m_function_group WHERE function_group_parent = :function_group_id OR function_group_id = :function_group_id)";
			$query_function = DB::query($sql_function);
			$query_function->param('function_group_id', $function_group_id);
			$result_function = $query_function->execute();
			
			//删除功能组
			$sql_group = "DELETE FROM m_function_group WHERE function_group_id = :function_group_id OR function_group_parent = :function_group_id";
			$query_group = DB::query($sql_group);
			$query_group->param('function_group_id', $function_group_id);
			$result_group = $query_group->execute();
			
			return $result_group;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 获得特定单个主功能组信息
	 */
	public static function SelectMasterGroup($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//用户类型ID限定
			if(isset($params['function_group_id'])) {
				$sql_where[] = " mfg.function_group_id = :function_group_id ";
				$sql_params['function_group_id'] = $params['function_group_id'];
			}
			
			$sql = "SELECT mfg.* FROM m_function_group mfg WHERE mfg.function_group_parent IS NULL " . (count($sql_where) ? (" AND " . implode(" AND ", $sql_where)) : "");
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
	 * 获得特定单个副功能组信息
	 */
	public static function SelectSubGroup($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//用户类型ID限定
			if(isset($params['sub_group_id'])) {
				$sql_where[] = " sg.function_group_id = :sub_group_id ";
				$sql_params['sub_group_id'] = $params['sub_group_id'];
			}
			
			$sql = "SELECT mg.function_group_id master_group_id, mg.function_group_name master_group_name, mg.special_flag master_special_flag, "
				. "sg.function_group_id sub_group_id, sg.function_group_name sub_group_name, sg.special_flag sub_special_flag "
				. "FROM (SELECT * FROM m_function_group WHERE function_group_parent IS NOT NULL) sg "
				. "LEFT JOIN (SELECT * FROM m_function_group WHERE function_group_parent IS NULL) mg ON sg.function_group_parent = mg.function_group_id " 
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
	 * 根据主功能组ID列表获得所属副功能组ID列表
	 */
	public static function SelectSubGroupIdList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//用户类型ID限定
			if(isset($params['master_group_id_list'])) {
				if(count($params['master_group_id_list'])) {
					$sql_where[] = " mfg.function_group_parent IN :master_group_id_list ";
					$sql_params['master_group_id_list'] = $params['master_group_id_list'];
				} else {
					return false;
				}
			}
			
			$sql = "SELECT mfg.function_group_id FROM m_function_group mfg " . (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");;
			$query = DB::query($sql);
			foreach($sql_params as $param_key => $param_value) {
				$query->param($param_key, $param_value);
			}
			$result = $query->execute()->as_array();
			
			if(count($result)) {
				$sub_group_id_list = array();
				foreach($result as $sub_group) {
					$sub_group_id_list[] = $sub_group['function_group_id'];
				}
				return $sub_group_id_list;
			} else {
				return array();
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 添加/更新功能组前信息查验
	 */
	public static function CheckEditFunctionGroup($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(empty($params['function_group_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_function_group_name';
		} elseif(mb_strlen($params['function_group_name']) > 30) {
			$result['result'] = false;
			$result['error'][] = 'long_function_group_name';
		} elseif(Model_Functiongroup::CheckFunctionGroupNameDuplication($params['function_group_name'], $params['function_group_id'], $params['function_group_type'], $params['function_group_parent'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_function_group_name';
		}
		
		if($params['function_group_type'] == 2) {
			if(!is_numeric($params['function_group_parent'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_function_group_parent';
			} elseif(!Model_Functiongroup::CheckFunctionGroupIdExist($params['function_group_parent'], 1)) {
				$result['result'] = false;
				$result['error'][] = 'noexist_function_group_parent';
			}
		}
		
		if(!in_array($params['special_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'error_special_flag';
		}
		
		return $result;
	}
	
	/*
	 * 删除功能组前删除ID查验
	 */
	public static function CheckDeleteFunctionGroup($function_group_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($function_group_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		} elseif(!Model_Functiongroup::CheckFunctionGroupIdExist($function_group_id)) {
			$result['result'] = false;
			$result['error'][] = 'noexist_id';
		}
		
		return $result;
	}
	
	/*
	 * 检查功能组ID是否存在
	 */
	public static function CheckFunctionGroupIdExist($function_group_id, $function_group_type = 0) {
		try {
			//不区分主组副组共同查找
			$sql = "SELECT function_group_id FROM m_function_group WHERE function_group_id = :function_group_id";
			if($function_group_type == 1) {
				//单独查找主组
				$sql .= " AND function_group_parent IS NULL ";
			} elseif($function_group_type == 2) {
				//单独查找副组
				$sql .= " AND function_group_parent IS NOT NULL ";
			}
			$query = DB::query($sql);
			$query->param('function_group_id', $function_group_id);
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
	
	/*
	 * 功能组名称重复查验
	 */
	public static function CheckFunctionGroupNameDuplication($function_group_name, $function_group_id, $function_group_type = 0, $function_group_parent = 0) {
		try {
			//不区分主组副组共同查找
			$sql = "SELECT function_group_id FROM m_function_group WHERE function_group_name = :function_group_name " . ($function_group_id ? " AND function_group_id != :function_group_id " : " ");
			if($function_group_type == 1) {
				//单独查找主组
				$sql .= " AND function_group_parent IS NULL ";
			} elseif($function_group_type == 2 && is_numeric($function_group_parent)) {
				//单独查找副组
				$sql .= " AND function_group_parent = :function_group_parent ";
			}
			$query = DB::query($sql);
			$query->param('function_group_name', $function_group_name);
			if($function_group_id) {
				$query->param('function_group_id', $function_group_id);
			}
			if($function_group_type == 2 && is_numeric($function_group_parent)) {
				$query->param('function_group_parent', $function_group_parent);
			}
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

