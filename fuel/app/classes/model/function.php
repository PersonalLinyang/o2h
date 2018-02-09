<?php

class Model_Function extends Model
{

	/*
	 * 添加功能
	 */
	public static function InsertFunction($params) {
		try {
			$sql_function = "INSERT INTO m_function(function_name, function_group_id, special_flag) VALUES(:function_name, :function_group_id, :special_flag)";
			$query_function = DB::query($sql_function);
			$query_function->param('function_name', $params['function_name']);
			$query_function->param('function_group_id', $params['function_group_id']);
			$query_function->param('special_flag', $params['special_flag']);
			$result_function = $query_function->execute();
			
			//添加成功的同时为系统管理员添加该权限
			if($result_function) {
				$function_id = intval($result_function[0]);
				
				$sql_permission = "INSERT INTO r_permission(user_type_id, permission_type, permission_id) VALUES(1, 3, :permission_id)";
				$query_permission = DB::query($sql_permission);
				$query_permission->param('permission_id', $function_id);
				$result_permission = $query_permission->execute();
			}
			
			return $result_function;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新功能
	 */
	public static function UpdateFunction($params) {
		try {
			//更新功能
			$sql_info = "UPDATE m_function SET function_name = :function_name, special_flag = :special_flag WHERE function_id = :function_id";
			$query_info = DB::query($sql_info);
			$query_info->param('function_id', $params['function_id']);
			$query_info->param('function_name', $params['function_name']);
			$query_info->param('special_flag', $params['special_flag']);
			$result_info = $query_info->execute();
			
			if($result_info && $params['special_flag']=='1') {
				//功能被更新为特殊功能时更新下属权限为特殊权限
				$sql_authority = "UPDATE m_authority SET special_flag = 1 WHERE function_id = :function_id ";
				$query_authority = DB::query($sql_authority);
				$query_authority->param('function_id', $params['function_id']);
				$result_authority = $query_authority->execute();
			}
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 根据ID删除功能
	 */
	public static function DeleteFunction($function_id) {
		try{
			//删除相关权限许可
			$sql_permission_authority = "DELETE FROM r_permission WHERE permission_type = 4 AND permission_id IN (SELECT authority_id FROM m_authority WHERE function_id = :function_id)";
			$query_permission_authority = DB::query($sql_permission_authority);
			$query_permission_authority->param('function_id', $function_id);
			$result_permission_authority = $query_permission_authority->execute();
			
			$sql_permission_function = "DELETE FROM r_permission WHERE permission_type = 3 AND permission_id = :permission_id";
			$query_permission_function = DB::query($sql_permission_function);
			$query_permission_function->param('permission_id', $function_id);
			$result_permission_function = $query_permission_function->execute();
			
			//删除权限
			$sql_authority = "DELETE FROM m_authority WHERE function_id = :function_id";
			$query_authority = DB::query($sql_authority);
			$query_authority->param('function_id', $function_id);
			$result_authority = $query_authority->execute();
			
			//删除功能
			$sql_function = "DELETE FROM m_function WHERE function_id = :function_id";
			$query_function = DB::query($sql_function);
			$query_function->param('function_id', $function_id);
			$result_function = $query_function->execute();
			
			return $result_function;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 获得特定单个功能信息
	 */
	public static function SelectFunction($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//功能ID限定
			if(isset($params['function_id'])) {
				$sql_where[] = " f.function_id = :function_id ";
				$sql_params['function_id'] = $params['function_id'];
			}
			
			$sql = "SELECT mg.function_group_id master_group_id, mg.function_group_name master_group_name, mg.special_flag master_special_flag, "
				. "sg.function_group_id sub_group_id, sg.function_group_name sub_group_name, sg.special_flag sub_special_flag, "
				. "f.function_id, f.function_name, f.special_flag function_special_flag "
				. "FROM m_function f "
				. "LEFT JOIN (SELECT * FROM m_function_group WHERE function_group_parent IS NOT NULL) sg ON f.function_group_id = sg.function_group_id "
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
	 * 根据副功能组ID列表获得所属功能ID列表
	 */
	public static function SelectFunctionIdList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//用户类型ID限定
			if(isset($params['function_group_id_list'])) {
				if(count($params['function_group_id_list'])) {
					$sql_where[] = " mf.function_group_id IN :function_group_id_list ";
					$sql_params['function_group_id_list'] = $params['function_group_id_list'];
				} else {
					return false;
				}
			}
			
			$sql = "SELECT mf.function_id FROM m_function mf " . (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");;
			$query = DB::query($sql);
			foreach($sql_params as $param_key => $param_value) {
				$query->param($param_key, $param_value);
			}
			$result = $query->execute()->as_array();
			
			if(count($result)) {
				$function_id_list = array();
				foreach($result as $function) {
					$function_id_list[] = $function['function_id'];
				}
				return $function_id_list;
			} else {
				return array();
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 添加/更新功能前信息查验
	 */
	public static function CheckEditFunction($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(empty($params['function_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_function_name';
		} elseif(mb_strlen($params['function_name']) > 30) {
			$result['result'] = false;
			$result['error'][] = 'long_function_name';
		}
		
		if(!is_numeric($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_function_group';
		} elseif(!Model_Functiongroup::CheckFunctionGroupIdExist($params['function_group_id'], 2)) {
			$result['result'] = false;
			$result['error'][] = 'noexist_function_group';
		}
		
		if(!empty($params['function_name']) && is_numeric($params['function_group_id'])) {
			if(Model_Function::CheckFunctionNameDuplication($params['function_name'], $params['function_id'], $params['function_group_id'])) {
				$result['result'] = false;
				$result['error'][] = 'dup_function_name';
			}
		}
		
		if(!in_array($params['special_flag'], array('1', '0'))) {
			$result['result'] = false;
			$result['error'][] = 'error_special_flag';
		}
		
		return $result;
	}
	
	/*
	 * 删除功能前删除ID查验
	 */
	public static function CheckDeleteFunction($function_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($function_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		} elseif(!Model_Function::CheckFunctionIdExist($function_id)) {
			$result['result'] = false;
			$result['error'][] = 'noexist_id';
		}
		
		return $result;
	}
	
	/*
	 * 检查功能ID是否存在
	 */
	public static function CheckFunctionIdExist($function_id) {
		try {
			$sql = "SELECT function_id FROM m_function WHERE function_id = :function_id";
			$query = DB::query($sql);
			$query->param('function_id', $function_id);
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
	 * 功能名称重复查验
	 */
	public static function CheckFunctionNameDuplication($function_name, $function_id, $function_group_id = 0) {
		try {
			$sql = "SELECT function_id FROM m_function WHERE function_name = :function_name " . ($function_id ? " AND function_id != :function_id " : " ");
			if(is_numeric($function_group_id)) {
				$sql .= " AND function_group_id = :function_group_id ";
			}
			$query = DB::query($sql);
			$query->param('function_name', $function_name);
			if($function_id) {
				$query->param('function_id', $function_id);
			}
			if(is_numeric($function_group_id)) {
				$query->param('function_group_id', $function_group_id);
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
