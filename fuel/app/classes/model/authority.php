<?php

class Model_Authority extends Model
{

	/*
	 * 添加权限
	 */
	public static function InsertAuthority($params) {
		$sql_authority = "INSERT INTO m_authority(authority_name, function_id, special_flag) VALUES(:authority_name, :function_id, :special_flag)";
		$query_authority = DB::query($sql_authority);
		$query_authority->param(':authority_name', $params['authority_name']);
		$query_authority->param(':function_id', $params['function_id']);
		$query_authority->param(':special_flag', $params['special_flag']);
		$result_authority = $query_authority->execute();
		
		//添加成功的同时为系统管理员添加该权限
		if($result_authority) {
			$authority_id = intval($result_authority[0]);
			
			$sql_permission = "INSERT INTO r_permission(user_type_id, permission_type, permission_id) VALUES(1, 4, :permission_id)";
			$query_permission = DB::query($sql_permission);
			$query_permission->param(':permission_id', $authority_id);
			$result_permission = $query_permission->execute();
		}
		
		return $query_authority;
	}

	/*
	 * 更新权限名称
	 */
	public static function UpdateAuthorityName($params) {
		$sql = "UPDATE m_authority SET authority_name = :authority_name WHERE authority_id = :authority_id";
		$query = DB::query($sql);
		$query->param(':authority_id', $params['authority_id']);
		$query->param(':authority_name', $params['authority_name']);
		$result = $query->execute();
		
		return $result;
	}
	
	/*
	 * 根据ID删除权限
	 */
	public static function DeleteAuthorityById($authority_id) {
		//删除相关权限许可
		$sql_permission = "DELETE FROM r_permission WHERE permission_type = 4 AND permission_id = :permission_id";
		$query_permission = DB::query($sql_permission);
		$query_permission->param(':permission_id', $authority_id);
		$result_permission = $query_permission->execute();
		
		//删除权限
		$sql_authority = "DELETE FROM m_authority WHERE authority_id = :authority_id";
		$query_authority = DB::query($sql_authority);
		$query_authority->param(':authority_id', $authority_id);
		$result_authority = $query_authority->execute();
		
		return $result_authority;
	}
	
	/*
	 * 根据ID获取权限信息
	 */
	public static function SelectAuthorityById($authority_id) {
		if(!is_numeric($authority_id)) {
			return false;
		}
		
		$sql = "SELECT mg.function_group_id master_group_id, mg.function_group_name master_group_name, mg.special_flag master_special_flag, "
			. "sg.function_group_id sub_group_id, sg.function_group_name sub_group_name, sg.special_flag sub_special_flag, "
			. "f.function_id, f.function_name, f.special_flag function_special_flag, a.authority_id, a.authority_name, a.special_flag authority_special_flag " 
			. "FROM m_authority a "
			. "LEFT JOIN m_function f ON a.function_id = f.function_id "
			. "LEFT JOIN (SELECT * FROM m_function_group WHERE function_group_parent IS NOT NULL) sg ON f.function_group_id = sg.function_group_id "
			. "LEFT JOIN (SELECT * FROM m_function_group WHERE function_group_parent IS NULL) mg ON sg.function_group_parent = mg.function_group_id " 
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
	
	/*
	 * 添加权限前添加信息查验
	 */
	public static function CheckInsertAuthority($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(empty($params['authority_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		} elseif(mb_strlen($params['authority_name']) > 30) {
			$result['result'] = false;
			$result['error'][] = 'long_name';
		}
		
		if(!is_numeric($params['function_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_group';
		} elseif(!Model_Function::CheckFunctionIdExist($params['function_id'])) {
			$result['result'] = false;
			$result['error'][] = 'noexist_group';
		}
		
		if(!empty($params['authority_name']) && is_numeric($params['function_id'])) {
			if(Model_Authority::CheckAuthorityNameExist($params['authority_name'], $params['function_id'])) {
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
	 * 更新权限名称前更新信息查验
	 */
	public static function CheckUpdateAuthorityName($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(empty($params['authority_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		} elseif(mb_strlen($params['authority_name']) > 30) {
			$result['result'] = false;
			$result['error'][] = 'long_name';
		}
		
		if(!is_numeric($params['function_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_group';
		} elseif(!Model_Function::CheckFunctionIdExist($params['function_id'])) {
			$result['result'] = false;
			$result['error'][] = 'noexist_group';
		}
		
		if(!empty($params['authority_name']) && is_numeric($params['function_id'])) {
			if(Model_Authority::CheckAuthorityNameExist($params['authority_name'], $params['function_id'])) {
				$result['result'] = false;
				$result['error'][] = 'dup_name';
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除权限前删除ID查验
	 */
	public static function CheckDeleteAuthorityById($authority_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($authority_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		} elseif(!Model_Authority::CheckAuthorityIdExist($authority_id)) {
			$result['result'] = false;
			$result['error'][] = 'noexist_id';
		}
		
		return $result;
	}
	
	/*
	 * 检查权限ID是否存在
	 */
	public static function CheckAuthorityIdExist($authority_id) {
		$sql = "SELECT authority_id FROM m_authority WHERE authority_id = :authority_id";
		$query = DB::query($sql);
		$query->param(':authority_id', $authority_id);
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
	public static function CheckAuthorityNameExist($authority_name, $function_id) {
		$sql = "SELECT authority_id FROM m_authority WHERE authority_name = :authority_name AND function_id = :function_id";
		$query = DB::query($sql);
		$query->param(':authority_name', $authority_name);
		$query->param(':function_id', $function_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}




















}

