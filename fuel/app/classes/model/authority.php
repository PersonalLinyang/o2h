<?php

class Model_Authority extends Model
{

	public static function InsertAuthority($params) {
		$sql_insert = "INSERT INTO m_authority(authority_name, function_id) VALUES(:authority_name, :function_id)";
		$query_insert = DB::query($sql_insert);
		$query_insert->param(':authority_name', $params['authority_name']);
		$query_insert->param(':function_id', $params['function_id']);
		$result_insert = $query_insert->execute();
		
		return $result_insert;
	}
	
	public static function DeleteAuthorityById($authority_id) {
		$sql_pdelete = "DELETE FROM t_permission WHERE authority_id = :authority_id";
		$query_pdelete = DB::query($sql_pdelete);
		$query_pdelete->param(':authority_id', $authority_id);
		$result_pdelete = $query_pdelete->execute();
		
		$sql_adelete = "DELETE FROM m_authority WHERE authority_id = :authority_id";
		$query_adelete = DB::query($sql_adelete);
		$query_adelete->param(':authority_id', $authority_id);
		$result_adelete = $query_adelete->execute();
		
		return $result_fgdelete;
	}

	public static function UpdateAuthority($params) {
		$sql_update = "UPDATE m_authority SET authority_name = :authority_name WHERE authority_id = :authority_id";
		$query_update = DB::query($sql_update);
		$query_update->param(':authority_id', $params['authority_id']);
		$query_update->param(':authority_name', $params['authority_name']);
		$result_update = $query_update->execute();
		
		return $result_update;
	}
	
	public static function SelectAuthorityById($authority_id) {
		if(!is_numeric($authority_id)) {
			return false;
		}
		
		$sql = "SELECT a.authority_id, a.authority_name, f.function_id, f.function_name, f.function_group_id sub_group_id, sg.function_group_name sub_group_name, " 
				. "sg.function_group_parent master_group_id, mg.function_group_name master_group_name " 
				. "FROM m_authority a LEFT JOIN m_function f ON a.function_id = f.function_id LEFT JOIN m_function_group sg ON f.function_group_id = sg.function_group_id " 
				. "LEFT JOIN m_function_group mg ON sg.function_group_parent = mg.function_group_id " 
				. "WHERE a.authority_id = :authority_id";
		$query = DB::query($sql);
		$query->param(':authority_id', $authority_id);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	public static function CheckInsertAuthority($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!isset($params['authority_name'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_name';
		} elseif(empty($params['authority_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if(!isset($params['function_id'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_function';
		} elseif(!is_numeric($params['function_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_function';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_authority WHERE authority_name = :authority_name AND function_id = :function_id";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param(':authority_name', $params['authority_name']);
			$query_duplication->param(':function_id', $params['function_id']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				$result['result'] = false;
				$result['error'][] = 'duplication';
			}
		}
		
		return $result;
	}
	
	public static function CheckUpdateAuthority($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!isset($params['authority_id'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_id';
		} elseif(!is_numeric($params['authority_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if(!isset($params['authority_name'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_name';
		} elseif(empty($params['authority_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_authority WHERE authority_name = :authority_name AND function_id = (SELECT function_id FROM m_authority WHERE authority_id = :authority_id)";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param(':authority_id', $params['authority_id']);
			$query_duplication->param(':authority_name', $params['authority_name']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				if($result_duplication[0]['authority_id'] == $params['authority_id']) {
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
	
	public static function CheckDeleteAuthorityById($authority_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($authority_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM m_authority WHERE authority_id = :authority_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':authority_id', $params['authority_id']);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}

}

