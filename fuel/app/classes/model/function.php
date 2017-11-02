<?php

class Model_Function extends Model
{
	/*
	 * 添加功能
	 */
	public static function InsertFunction($params) {
		$sql_insert = "INSERT INTO m_function(function_name, function_group_id) VALUES(:function_name, :function_group_id)";
		$query_insert = DB::query($sql_insert);
		$query_insert->param(':function_name', $params['function_name']);
		$query_insert->param(':function_group_id', $params['function_group_id']);
		$result_insert = $query_insert->execute();
		
		return $result_insert;
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
	 * 添加功能前添加信息查验
	 */
	public static function CheckInsertFunction($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!isset($params['function_name'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_name';
		} elseif(empty($params['function_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if(!isset($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_group';
		} elseif(!is_numeric($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_group';
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

