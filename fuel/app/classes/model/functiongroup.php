<?php

class Model_Functiongroup extends Model
{
	/*
	 * 添加主功能组
	 */
	public static function InsertMasterGroup($params) {
		$sql_group = "INSERT INTO m_function_group(function_group_name, special_flag) VALUES(:function_group_name, :special_flag)";
		$query_group = DB::query($sql_group);
		$query_group->param(':function_group_name', $params['function_group_name']);
		$query_group->param(':special_flag', $params['special_flag']);
		$result_group = $query_group->execute();
		
		//添加成功的同时为系统管理员添加该权限
		if($result_group) {
			$group_id = intval($result_group[0]);
			
			$sql_permission = "INSERT INTO r_permission(user_type_id, permission_type, permission_id) VALUES(1, 1, :permission_id)";
			$query_permission = DB::query($sql_permission);
			$query_permission->param(':permission_id', $group_id);
			$result_permission = $query_permission->execute();
		}
		
		return $result_group;
	}
	
	/*
	 * 添加副功能组
	 */
	public static function InsertSubGroup($params) {
		$sql_group = "INSERT INTO m_function_group(function_group_name, function_group_parent, special_flag) VALUES(:function_group_name, :function_group_parent, :special_flag)";
		$query_group = DB::query($sql_group);
		$query_group->param(':function_group_name', $params['function_group_name']);
		$query_group->param(':function_group_parent', $params['function_group_parent']);
		$query_group->param(':special_flag', $params['special_flag']);
		$result_group = $query_group->execute();
		
		//添加成功的同时为系统管理员添加该权限
		if($result_group) {
			$group_id = intval($result_group[0]);
			
			$sql_permission = "INSERT INTO r_permission(user_type_id, permission_type, permission_id) VALUES(1, 2, :permission_id)";
			$query_permission = DB::query($sql_permission);
			$query_permission->param(':permission_id', $group_id);
			$result_permission = $query_permission->execute();
		}
		
		return $result_group;
	}
	
	/*
	 * 根据ID获取主功能组信息
	 */
	public static function SelectMasterGroupById($function_group_id) {
		if(!is_numeric($function_group_id)) {
			return false;
		}
		
		$sql = "SELECT * FROM m_function_group WHERE function_group_id = :function_group_id AND function_group_parent IS NULL";
		$query = DB::query($sql);
		$query->param(':function_group_id', $function_group_id);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	/*
	 * 根据ID获取副功能组信息
	 */
	public static function SelectSubGroupById($sub_group_id) {
		if(!is_numeric($sub_group_id)) {
			return false;
		}
		
		$sql = "SELECT mg.function_group_id master_group_id, mg.function_group_name master_group_name, mg.special_flag master_special_flag, "
			. "sg.function_group_id sub_group_id, sg.function_group_name sub_group_name, sg.special_flag sub_special_flag "
			. "FROM (SELECT * FROM m_function_group WHERE function_group_parent IS NOT NULL) sg "
			. "LEFT JOIN (SELECT * FROM m_function_group WHERE function_group_parent IS NULL) mg ON sg.function_group_parent = mg.function_group_id " 
			. "WHERE sg.function_group_id = :sub_group_id";
		$query = DB::query($sql);
		$query->param(':sub_group_id', $sub_group_id);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	/*
	 * 添加主功能组前添加信息查验
	 */
	public static function CheckInsertMasterGroup($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(empty($params['function_group_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		} elseif(mb_strlen($params['function_group_name']) > 50) {
			$result['result'] = false;
			$result['error'][] = 'long_name';
		} elseif(Model_Functiongroup::CheckMasterGroupNameExist($params['function_group_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_name';
		}
		
		if(!in_array($params['special_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'error_special_flag';
		}
		
		return $result;
	}
	
	/*
	 * 添加副功能组前添加信息查验
	 */
	public static function CheckInsertSubGroup($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(empty($params['function_group_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		} elseif(mb_strlen($params['function_group_name']) > 50) {
			$result['result'] = false;
			$result['error'][] = 'long_name';
		}
		
		if(!is_numeric($params['function_group_parent'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_parent';
		} elseif(!Model_Functiongroup::CheckMasterGroupIdExist($params['function_group_parent'])) {
			$result['result'] = false;
			$result['error'][] = 'noexist_parent';
		}
		
		if(!empty($params['function_group_name']) && is_numeric($params['function_group_parent'])) {
			if(Model_Functiongroup::CheckSubGroupNameExist($params['function_group_name'], $params['function_group_parent'])) {
				$result['result'] = false;
				$result['error'][] = 'dup_name';
			}
		}
		
		if(!in_array($params['special_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'error_special_flag';
		}
		
		return $result;
	}
	
	/*
	 * 检查主功能组ID是否存在
	 */
	public static function CheckMasterGroupIdExist($function_group_id) {
		$sql = "SELECT function_group_id FROM m_function_group WHERE function_group_id = :function_group_id AND function_group_parent IS NULL";
		$query = DB::query($sql);
		$query->param(':function_group_id', $function_group_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * 检查副功能组ID是否存在
	 */
	public static function CheckSubGroupIdExist($function_group_id) {
		$sql = "SELECT function_group_id FROM m_function_group WHERE function_group_id = :function_group_id AND function_group_parent IS NOT NULL";
		$query = DB::query($sql);
		$query->param(':function_group_id', $function_group_id);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * 检查主功能组名称是否存在
	 */
	public static function CheckMasterGroupNameExist($function_group_name) {
		$sql = "SELECT function_group_id FROM m_function_group WHERE function_group_name = :function_group_name AND function_group_parent IS NULL";
		$query = DB::query($sql);
		$query->param(':function_group_name', $function_group_name);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * 检查副功能组名称是否存在
	 */
	public static function CheckSubGroupNameExist($function_group_name, $function_group_parent) {
		$sql = "SELECT function_group_id FROM m_function_group WHERE function_group_name = :function_group_name AND function_group_parent = :function_group_parent";
		$query = DB::query($sql);
		$query->param(':function_group_name', $function_group_name);
		$query->param(':function_group_parent', $function_group_parent);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	 * 根据ID删除主功能组
	 */
	public static function DeleteMasterGroupById($function_group_id) {
		$sql_pdelete = "DELETE FROM t_permission WHERE master_group_id = :function_group_id";
		$query_pdelete = DB::query($sql_pdelete);
		$query_pdelete->param(':function_group_id', $function_group_id);
		$result_pdelete = $query_pdelete->execute();
		
		$sql_adelete = "DELETE FROM m_authority WHERE function_id IN " 
					. "(SELECT function_id FROM m_function WHERE function_group_id IN " 
					. "(SELECT function_group_id FROM m_function_group WHERE function_group_parent = :function_group_id))";
		$query_adelete = DB::query($sql_adelete);
		$query_adelete->param(':function_group_id', $function_group_id);
		$result_adelete = $query_adelete->execute();
		
		$sql_fdelete = "DELETE FROM m_function WHERE function_group_id IN " 
					. "(SELECT function_group_id FROM m_function_group WHERE function_group_parent = :function_group_id)";
		$query_fdelete = DB::query($sql_fdelete);
		$query_fdelete->param(':function_group_id', $function_group_id);
		$result_fdelete = $query_fdelete->execute();
		
		$sql_fgdelete = "DELETE FROM m_function_group WHERE function_group_id = :function_group_id OR function_group_parent = :function_group_id";
		$query_fgdelete = DB::query($sql_fgdelete);
		$query_fgdelete->param(':function_group_id', $function_group_id);
		$result_fgdelete = $query_fgdelete->execute();
		
		return $result_fgdelete;
	}
	
	/*
	 * 根据ID删除副功能组
	 */
	public static function DeleteSubGroupById($function_group_id) {
		$sql_pdelete = "DELETE FROM t_permission WHERE sub_group_id = :function_group_id";
		$query_pdelete = DB::query($sql_pdelete);
		$query_pdelete->param(':function_group_id', $function_group_id);
		$result_pdelete = $query_pdelete->execute();
		
		$sql_adelete = "DELETE FROM m_authority WHERE function_id IN " 
					. "(SELECT function_id FROM m_function WHERE function_group_id = :function_group_id)";
		$query_adelete = DB::query($sql_adelete);
		$query_adelete->param(':function_group_id', $function_group_id);
		$result_adelete = $query_adelete->execute();
		
		$sql_fdelete = "DELETE FROM m_function WHERE function_group_id = :function_group_id";
		$query_fdelete = DB::query($sql_fdelete);
		$query_fdelete->param(':function_group_id', $function_group_id);
		$result_fdelete = $query_fdelete->execute();
		
		$sql_fgdelete = "DELETE FROM m_function_group WHERE function_group_id = :function_group_id";
		$query_fgdelete = DB::query($sql_fgdelete);
		$query_fgdelete->param(':function_group_id', $function_group_id);
		$result_fgdelete = $query_fgdelete->execute();
		
		return $result_fgdelete;
	}
	
	/*
	 * 更新功能组名称
	 */
	public static function UpdateFunctionGroup($params) {
		$sql_update = "UPDATE m_function_group SET function_group_name = :function_group_name WHERE function_group_id = :function_group_id";
		$query_update = DB::query($sql_update);
		$query_update->param(':function_group_id', $params['function_group_id']);
		$query_update->param(':function_group_name', $params['function_group_name']);
		$result_update = $query_update->execute();
		
		return $result_update;
	}
	
	/*
	 * 更新主功能组前更新信息查验
	 */
	public static function CheckUpdateMasterGroup($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!isset($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_id';
		} elseif(!is_numeric($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if(!isset($params['function_group_name'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_name';
		} elseif(empty($params['function_group_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_function_group WHERE function_group_name = :function_group_name AND function_group_parent IS NULL";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param(':function_group_name', $params['function_group_name']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				if($result_duplication[0]['function_group_id'] == $params['function_group_id']) {
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
	 * 更新副功能组前更新信息查验
	 */
	public static function CheckUpdateSubGroup($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!isset($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_id';
		} elseif(!is_numeric($params['function_group_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if(!isset($params['function_group_name'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_name';
		} elseif(empty($params['function_group_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_function_group " 
							. "WHERE function_group_name = :function_group_name AND function_group_parent IS NOT NULL " 
							. "AND function_group_parent = (SELECT function_group_parent FROM m_function_group WHERE function_group_id = :function_group_id)";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param(':function_group_id', $params['function_group_id']);
			$query_duplication->param(':function_group_name', $params['function_group_name']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				if($result_duplication[0]['function_group_id'] == $params['function_group_id']) {
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
	 * 删除主功能组前删除ID查验
	 */
	public static function CheckDeleteMasterGroupById($function_group_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($function_group_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM m_function_group WHERE function_group_id = :function_group_id AND function_group_parent IS NULL";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':function_group_id', $function_group_id);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除副功能组前删除ID查验
	 */
	public static function CheckDeleteSubGroupById($function_group_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($function_group_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM m_function_group WHERE function_group_id = :function_group_id AND function_group_parent IS NOT NULL";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':function_group_id', $function_group_id);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}

}

