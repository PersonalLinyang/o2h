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
		} elseif(mb_strlen($params['function_name']) > 50) {
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
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_function WHERE function_name = :function_name AND function_group_id = :function_group_id";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param(':function_name', $params['function_name']);
			$query_duplication->param(':function_group_id', $params['function_group_id']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				$result['result'] = false;
				$result['error'][] = 'duplication';
			}
		}
		
		return $result;
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

















	
	/*
	 * 根据ID删除功能
	 */
	public static function DeleteFunctionById($function_id) {
		$sql_pdelete = "DELETE FROM t_permission WHERE function_id = :function_id";
		$query_pdelete = DB::query($sql_pdelete);
		$query_pdelete->param(':function_id', $function_id);
		$result_pdelete = $query_pdelete->execute();
		
		$sql_adelete = "DELETE FROM m_authority WHERE function_id = :function_id";
		$query_adelete = DB::query($sql_adelete);
		$query_adelete->param(':function_id', $function_id);
		$result_adelete = $query_adelete->execute();
		
		$sql_fdelete = "DELETE FROM m_function WHERE function_id = :function_id";
		$query_fdelete = DB::query($sql_fdelete);
		$query_fdelete->param(':function_id', $function_id);
		$result_fdelete = $query_fdelete->execute();
		
		return $result_fdelete;
	}

	/*
	 * 更新功能
	 */
	public static function UpdateFunction($params) {
		$sql_update = "UPDATE m_function SET function_name = :function_name WHERE function_id = :function_id";
		$query_update = DB::query($sql_update);
		$query_update->param(':function_id', $params['function_id']);
		$query_update->param(':function_name', $params['function_name']);
		$result_update = $query_update->execute();
		
		return $result_update;
	}
	
	/*
	 * 根据ID获取功能信息
	 */
	public static function SelectFunctionById($function_id) {
		if(!is_numeric($function_id)) {
			return false;
		}
		
		$sql = "SELECT f.function_id, f.function_name, f.function_group_id sub_group_id, sg.function_group_name sub_group_name, sg.function_group_parent master_group_id, mg.function_group_name master_group_name " 
				. "FROM m_function f LEFT JOIN m_function_group sg ON f.function_group_id = sg.function_group_id LEFT JOIN m_function_group mg ON sg.function_group_parent = mg.function_group_id " 
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
	 * 更新功能前更新信息查验
	 */
	public static function CheckUpdateFunction($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!isset($params['function_id'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_id';
		} elseif(!is_numeric($params['function_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if(!isset($params['function_name'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_name';
		} elseif(empty($params['function_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_function WHERE function_name = :function_name AND function_group_id = (SELECT function_group_id FROM m_function WHERE function_id = :function_id)";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param(':function_id', $params['function_id']);
			$query_duplication->param(':function_name', $params['function_name']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				if($result_duplication[0]['function_id'] == $params['function_id']) {
					$result['result'] = false;
					$result['error'][] = 'nomodify';
				} else {
					$result['result'] = false;
					$result['error'][] = 'duplication';
				}
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
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM m_function WHERE function_id = :function_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':function_id', $function_id);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}

}

