<?php

class Model_Costtype extends Model
{

	/*
	 * 添加支出项目
	 */
	public static function InsertCostType($params) {
		try {
			$sql = "INSERT INTO m_cost_type(cost_type_name, delete_flag, sort_id) VALUES(:cost_type_name, 0, 1)";
			$query = DB::query($sql);
			$query->param('cost_type_name', $params['cost_type_name']);
			$result = $query->execute();
			
			if($result) {
				//新支出项目ID
				$cost_type_id = intval($result[0]);
				return $cost_type_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除支出项目
	 */
	public static function DeleteCostType($params) {
		try {
			//删除支出项目
			$sql_type = "UPDATE m_cost_type SET delete_flag = 1 WHERE cost_type_id = :cost_type_id";
			$query_type = DB::query($sql_type);
			$query_type->param('cost_type_id', $params['cost_type_id']);
			$result_type = $query_type->execute();
			
			return $result_type;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新支出项目名称
	 */
	public static function UpdateCostType($params) {
		try {
			$sql = "UPDATE m_cost_type SET cost_type_name = :cost_type_name WHERE cost_type_id = :cost_type_id";
			$query = DB::query($sql);
			$query->param('cost_type_id', $params['cost_type_id']);
			$query->param('cost_type_name', $params['cost_type_name']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	//获得符合特定条件的支出项目
	public static function SelectCostTypeList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效支出项目限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mct.delete_flag = 0 ";
			}
			
			$sql = "SELECT mct.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_cost_type mct " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mct.sort_id, mct.cost_type_id";
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
	 * 获取特定单个支出项目信息
	 */
	public static function SelectCostType($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//支出项目ID限定
			if(isset($params['cost_type_id'])) {
				$sql_where[] = " mst.cost_type_id = :cost_type_id ";
				$sql_params['cost_type_id'] = $params['cost_type_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " mst.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql = "SELECT * FROM m_cost_type mst " 
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
	 * 编辑支出项目前编辑信息查验
	 */
	public static function CheckEditCostType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//支出项目名称
		if(empty($params['cost_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_cost_type_name';
		} elseif(mb_strlen($params['cost_type_name']) > 50) {
			$result['result'] = false;
			$result['error'][] = 'long_cost_type_name';
		} elseif(Model_Costtype::CheckCostTypeNameDuplication($params['cost_type_id'], $params['cost_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_cost_type_name';
		}
		
		return $result;
	}
	
	/*
	 * 删除支出项目前删除信息查验
	 */
	public static function CheckDeleteCostType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($params['cost_type_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_cost_type_id';
		} elseif(!Model_Costtype::CheckCostTypeIdExist($params['cost_type_id'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_cost_type_id';
		}
		
		return $result;
	}
	
	/*
	 * 检查支出项目ID是否存在
	 */
	public static function CheckCostTypeIdExist($cost_type_id, $active_check = 0) {
		try {
			$sql = "SELECT cost_type_id FROM m_cost_type WHERE cost_type_id = :cost_type_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('cost_type_id', $cost_type_id);
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
	 * 支出项目名称重复查验
	 */
	public static function CheckCostTypeNameDuplication($cost_type_id, $cost_type_name) {
		try {
			//数据获取
			$sql = "SELECT cost_type_id FROM m_cost_type WHERE cost_type_name = :cost_type_name AND delete_flag = 0" . ($cost_type_id ? " AND cost_type_id != :cost_type_id " : "");
			$query = DB::query($sql);
			if($cost_type_id) {
				$query->param('cost_type_id', $cost_type_id);
			}
			$query->param('cost_type_name', $cost_type_name);
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

