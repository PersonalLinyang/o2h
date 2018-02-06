<?php

class Model_Spottype extends Model
{

	/*
	 * 添加景点类别
	 */
	public static function InsertSpotType($params) {
		try {
			$sql = "INSERT INTO m_spot_type(spot_type_name, delete_flag, sort_id) VALUES(:spot_type_name, 0, 1)";
			$query = DB::query($sql);
			$query->param('spot_type_name', $params['spot_type_name']);
			$result = $query->execute();
			
			if($result) {
				//新景点类别ID
				$spot_type_id = intval($result[0]);
				return $spot_type_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除景点类别
	 */
	public static function DeleteSpotType($params) {
		try {
			//删除景点类别
			$sql_type = "UPDATE m_spot_type SET delete_flag = 1 WHERE spot_type_id = :spot_type_id";
			$query_type = DB::query($sql_type);
			$query_type->param('spot_type_id', $params['spot_type_id']);
			$result_type = $query_type->execute();
			
			return $result_type;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新景点类别名称
	 */
	public static function UpdateSpotType($params) {
		try {
			$sql = "UPDATE m_spot_type SET spot_type_name = :spot_type_name WHERE spot_type_id = :spot_type_id";
			$query = DB::query($sql);
			$query->param('spot_type_id', $params['spot_type_id']);
			$query->param('spot_type_name', $params['spot_type_name']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	//获得符合特定条件的景点类别
	public static function SelectSpotTypeList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效景点类别限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mst.delete_flag = 0 ";
			}
			//获取所属景点数
			if(isset($params['spot_count_flag'])) {
				$sql_select[] = " COUNT(ts.spot_id) spot_count ";
				$sql_from[] = " LEFT JOIN (SELECT * FROM t_spot WHERE delete_flag=0) ts ON ts.spot_type = mst.spot_type_id ";
				$sql_group_by[] = " mst.spot_type_id ";
			}
			
			$sql = "SELECT mst.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_spot_type mst " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mst.sort_id, mst.spot_type_id";
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
	 * 获取特定单个景点类别信息
	 */
	public static function SelectSpotType($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//景点类别ID限定
			if(isset($params['spot_type_id'])) {
				$sql_where[] = " mst.spot_type_id = :spot_type_id ";
				$sql_params['spot_type_id'] = $params['spot_type_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " mst.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql = "SELECT * FROM m_spot_type mst " 
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
	 * 编辑景点类别前编辑信息查验
	 */
	public static function CheckEditSpotType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//景点类别名称
		if(empty($params['spot_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_spot_type_name';
		} elseif(mb_strlen($params['spot_type_name']) > 50) {
			$result['result'] = false;
			$result['error'][] = 'long_spot_type_name';
		} elseif(Model_Spottype::CheckSpotTypeNameDuplication($params['spot_type_id'], $params['spot_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_spot_type_name';
		}
		
		return $result;
	}
	
	/*
	 * 删除景点类别前删除信息查验
	 */
	public static function CheckDeleteSpotType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($params['spot_type_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_spot_type_id';
		} elseif(!Model_Spottype::CheckSpotTypeIdExist($params['spot_type_id'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_spot_type_id';
		} else {
			//获取景点信息
			$params_select = array(
				'spot_type' => array($params['spot_type_id']),
				'active_only' => 1,
			);
			$spot_select = Model_Spot::SelectSpotList($params_select);
			
			if($spot_select['spot_count']) {
				$result['result'] = false;
				$result['error'][] = 'error_spot_list';
			}
		}
		
		return $result;
	}
	
	/*
	 * 检查景点类别ID是否存在
	 */
	public static function CheckSpotTypeIdExist($spot_type_id, $active_check = 0) {
		try {
			$sql = "SELECT spot_type_id FROM m_spot_type WHERE spot_type_id = :spot_type_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('spot_type_id', $spot_type_id);
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
	 * 景点类别名称重复查验
	 */
	public static function CheckSpotTypeNameDuplication($spot_type_id, $spot_type_name) {
		try {
			//数据获取
			$sql = "SELECT spot_type_id FROM m_spot_type WHERE spot_type_name = :spot_type_name AND delete_flag = 0" . ($spot_type_id ? " AND spot_type_id != :spot_type_id " : "");
			$query = DB::query($sql);
			if($spot_type_id) {
				$query->param('spot_type_id', $spot_type_id);
			}
			$query->param('spot_type_name', $spot_type_name);
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

