<?php

class Model_Function extends Model
{

	/*
	 * 添加功能
	 */
	public static function InsertFunction($params) {
		$sql_function = "INSERT INTO m_function(function_name, function_group_id, special_flag) VALUES(:function_name, :function_group_id, :special_flag)";
		$query_function = DB::query($sql_function);
		$query_function->param(':function_name', $params['function_name']);
		$query_function->param(':function_group_id', $params['function_group_id']);
		$query_function->param(':special_flag', $params['special_flag']);
		$result_function = $query_function->execute();
		
		//添加成功的同时为系统管理员添加该权限
		if($result_function) {
			$function_id = intval($result_function[0]);
			
			$sql_permission = "INSERT INTO r_permission(user_type_id, permission_type, permission_id) VALUES(1, 3, :permission_id)";
			$query_permission = DB::query($sql_permission);
			$query_permission->param(':permission_id', $function_id);
			$result_permission = $query_permission->execute();
		}
		
		return $result_function;
	}
	
	/*
	 * 更新功能名称
	 */
	public static function UpdateFunctionName($params) {
		$sql = "UPDATE m_function SET function_name = :function_name WHERE function_id = :function_id";
		$query = DB::query($sql);
		$query->param(':function_id', $params['function_id']);
		$query->param(':function_name', $params['function_name']);
		$result = $query->execute();
		
		return $result;
	}
	
	/*
	 * 根据ID删除功能
	 */
	public static function DeleteFunctionById($function_id) {
		//删除相关权限许可
		$sql_permission_authority = "DELETE FROM r_permission WHERE permission_type = 4 AND permission_id IN (SELECT authority_id FROM m_authority WHERE function_id = :function_id)";
		$query_permission_authority = DB::query($sql_permission_authority);
		$query_permission_authority->param(':function_id', $function_id);
		$result_permission_authority = $query_permission_authority->execute();
		
		$sql_permission_function = "DELETE FROM r_permission WHERE permission_type = 3 AND permission_id = :permission_id";
		$query_permission_function = DB::query($sql_permission_function);
		$query_permission_function->param(':permission_id', $function_id);
		$result_permission_function = $query_permission_function->execute();
		
		//删除权限
		$sql_authority = "DELETE FROM m_authority WHERE function_id = :function_id";
		$query_authority = DB::query($sql_authority);
		$query_authority->param(':function_id', $function_id);
		$result_authority = $query_authority->execute();
		
		//删除功能
		$sql_function = "DELETE FROM m_function WHERE function_id = :function_id";
		$query_function = DB::query($sql_function);
		$query_function->param(':function_id', $function_id);
		$result_function = $query_function->execute();
		
		return $result_function;
	}
	
	/*
	 * 根据ID获取功能信息
	 */
	public static function SelectFunctionById($function_id) {
		if(!is_numeric($function_id)) {
			return false;
		}
		
		$sql = "SELECT mg.function_group_id master_group_id, mg.function_group_name master_group_name, mg.special_flag master_special_flag, "
			. "sg.function_group_id sub_group_id, sg.function_group_name sub_group_name, sg.special_flag sub_special_flag, "
			. "f.function_id, f.function_name, f.special_flag function_special_flag "
			. "FROM m_function f "
			. "LEFT JOIN (SELECT * FROM m_function_group WHERE function_group_parent IS NOT NULL) sg ON f.function_group_id = sg.function_group_id "
			. "LEFT JOIN (SELECT * FROM m_function_group WHERE function_group_parent IS NULL) mg ON sg.function_group_parent = mg.function_group_id " 
			. "WHERE f.function_id = :function_id";
		$query = DB::query($sql);
		$query->param(':function_id', $function_id);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	/*
	 * 添加功能前添加信息查验
	 */
	public static function CheckInsertFunction($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(empty($params['function_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		} elseif(mb_strlen($params['function_name']) > 30) {
			$result['result'] = false;
			$result['error'][] = 'long_name';
		}
		
		if(!is_numeric($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_group';
		} elseif(!Model_Functiongroup::CheckSubGroupIdExist($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'noexist_group';
		}
		
		if(!empty($params['function_name']) && is_numeric($params['function_group_id'])) {
			if(Model_Function::CheckFunctionNameExist($params['function_name'], $params['function_group_id'])) {
				$result['result'] = false;
				$result['error'][] = 'dup_name';
			}
		}
		
		if(!in_array($params['special_flag'], array('1', '0'))) {
			$result['result'] = false;
			$result['error'][] = 'error_special_flag';
		}
		
		return $result;
	}
	
	/*
	 * 更新功能名称前更新信息查验
	 */
	public static function CheckUpdateFunctionName($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(empty($params['function_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		} elseif(mb_strlen($params['function_name']) > 30) {
			$result['result'] = false;
			$result['error'][] = 'long_name';
		}
		
		if(!is_numeric($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_group';
		} elseif(!Model_Functiongroup::CheckSubGroupIdExist($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'noexist_group';
		}
		
		if(!empty($params['function_name']) && is_numeric($params['function_group_id'])) {
			if(Model_Function::CheckFunctionNameExist($params['function_name'], $params['function_group_id'])) {
				$result['result'] = false;
				$result['error'][] = 'dup_name';
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除功能前删除ID查验
	 */
	public static function CheckDeleteFunctionById($function_id) {
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
		$sql = "SELECT function_id FROM m_function WHERE function_id = :function_id";
		$query = DB::query($sql);
		$query->param(':function_id', $function_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * 检查功能名称是否存在
	 */
	public static function CheckFunctionNameExist($function_name, $function_group_id) {
		$sql = "SELECT function_id FROM m_function WHERE function_name = :function_name AND function_group_id = :function_group_id";
		$query = DB::query($sql);
		$query->param(':function_name', $function_name);
		$query->param(':function_group_id', $function_group_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}

}
