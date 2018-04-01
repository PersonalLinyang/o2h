<?php

class Model_Incometype extends Model
{

	/*
	 * 添加收入项目
	 */
	public static function InsertIncomeType($params) {
		try {
			$sql = "INSERT INTO m_income_type(income_type_name, delete_flag, sort_id) VALUES(:income_type_name, 0, 1)";
			$query = DB::query($sql);
			$query->param('income_type_name', $params['income_type_name']);
			$result = $query->execute();
			
			if($result) {
				//新收入项目ID
				$income_type_id = intval($result[0]);
				return $income_type_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除收入项目
	 */
	public static function DeleteIncomeType($params) {
		try {
			//删除收入项目
			$sql_type = "UPDATE m_income_type SET delete_flag = 1 WHERE income_type_id = :income_type_id";
			$query_type = DB::query($sql_type);
			$query_type->param('income_type_id', $params['income_type_id']);
			$result_type = $query_type->execute();
			
			return $result_type;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新收入项目名称
	 */
	public static function UpdateIncomeType($params) {
		try {
			$sql = "UPDATE m_income_type SET income_type_name = :income_type_name WHERE income_type_id = :income_type_id";
			$query = DB::query($sql);
			$query->param('income_type_id', $params['income_type_id']);
			$query->param('income_type_name', $params['income_type_name']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	//获得符合特定条件的收入项目
	public static function SelectIncomeTypeList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效收入项目限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mit.delete_flag = 0 ";
			}
			
			$sql = "SELECT mit.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_income_type mit " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mit.sort_id, mit.income_type_id";
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
	 * 获取特定单个收入项目信息
	 */
	public static function SelectIncomeType($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//收入项目ID限定
			if(isset($params['income_type_id'])) {
				$sql_where[] = " mst.income_type_id = :income_type_id ";
				$sql_params['income_type_id'] = $params['income_type_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " mst.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql = "SELECT * FROM m_income_type mst " 
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
	 * 编辑收入项目前编辑信息查验
	 */
	public static function CheckEditIncomeType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//收入项目名称
		if(empty($params['income_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_income_type_name';
		} elseif(mb_strlen($params['income_type_name']) > 50) {
			$result['result'] = false;
			$result['error'][] = 'long_income_type_name';
		} elseif(Model_Incometype::CheckIncomeTypeNameDuplication($params['income_type_id'], $params['income_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_income_type_name';
		}
		
		return $result;
	}
	
	/*
	 * 删除收入项目前删除信息查验
	 */
	public static function CheckDeleteIncomeType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($params['income_type_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_income_type_id';
		} elseif(!Model_Incometype::CheckIncomeTypeIdExist($params['income_type_id'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_income_type_id';
		}
		
		return $result;
	}
	
	/*
	 * 检查收入项目ID是否存在
	 */
	public static function CheckIncomeTypeIdExist($income_type_id, $active_check = 0) {
		try {
			$sql = "SELECT income_type_id FROM m_income_type WHERE income_type_id = :income_type_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('income_type_id', $income_type_id);
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
	 * 收入项目名称重复查验
	 */
	public static function CheckIncomeTypeNameDuplication($income_type_id, $income_type_name) {
		try {
			//数据获取
			$sql = "SELECT income_type_id FROM m_income_type WHERE income_type_name = :income_type_name AND delete_flag = 0" . ($income_type_id ? " AND income_type_id != :income_type_id " : "");
			$query = DB::query($sql);
			if($income_type_id) {
				$query->param('income_type_id', $income_type_id);
			}
			$query->param('income_type_name', $income_type_name);
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

