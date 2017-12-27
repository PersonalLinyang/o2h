<?php

class Model_Spottype extends Model
{

	/*
	 * 添加景点类别
	 */
	public static function InsertSpotType($params) {
		$sql_insert = "INSERT INTO m_spot_type(spot_type_name) VALUES(:spot_type_name)";
		$query_insert = DB::query($sql_insert);
		$query_insert->param('spot_type_name', $params['spot_type_name']);
		$result_insert = $query_insert->execute();
		
		return $result_insert;
	}
	
	/*
	 * 根据ID删除景点类别
	 */
	public static function DeleteSpotTypeById($spot_type_id) {
		$sql_delete = "DELETE FROM m_spot_type WHERE spot_type_id = :spot_type_id";
		$query_delete = DB::query($sql_delete);
		$query_delete->param('spot_type_id', $spot_type_id);
		$result_delete = $query_delete->execute();
		
		return $result_delete;
	}
	
	/*
	 * 更新景点类别名称
	 */
	public static function UpdateSpotType($params) {
		$sql_update = "UPDATE m_spot_type SET spot_type_name = :spot_type_name WHERE spot_type_id = :spot_type_id";
		$query_update = DB::query($sql_update);
		$query_update->param('spot_type_id', $params['spot_type_id']);
		$query_update->param('spot_type_name', $params['spot_type_name']);
		$result_update = $query_update->execute();
		
		return $result_update;
	}

	/*
	 * 获取全部景点类别信息
	 */
	public static function GetSpotTypeListAll() {
		$sql_spot_type = "SELECT mst.spot_type_id, mst.spot_type_name, COUNT(ts.spot_id) spot_count "
						. "FROM m_spot_type mst LEFT JOIN t_spot ts ON ts.spot_type = mst.spot_type_id " 
						. "GROUP BY spot_type_id, spot_type_name ORDER BY spot_type_id";
		$query_spot_type = DB::query($sql_spot_type);
		$spot_type_list = $query_spot_type->execute()->as_array();
		
		return $spot_type_list;
	}
	
	/*
	 * 根据ID获取主功能组信息
	 */
	public static function SelectSpotTypeById($spot_type_id) {
		if(!is_numeric($spot_type_id)) {
			return false;
		}
		
		$sql = "SELECT * FROM m_spot_type WHERE spot_type_id = :spot_type_id";
		$query = DB::query($sql);
		$query->param('spot_type_id', $spot_type_id);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	/*
	 * 添加景点类别前添加信息查验
	 */
	public static function CheckInsertSpotType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!isset($params['spot_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_name';
		} elseif(empty($params['spot_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_spot_type WHERE spot_type_name = :spot_type_name";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param('spot_type_name', $params['spot_type_name']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				$result['result'] = false;
				$result['error'][] = 'duplication';
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除景点类别前删除ID查验
	 */
	public static function CheckDeleteSpotTypeById($spot_type_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($spot_type_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM m_spot_type WHERE spot_type_id = :spot_type_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param('spot_type_id', $spot_type_id);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}
	
	/*
	 * 更新景点类别前更新信息查验
	 */
	public static function CheckUpdateSpotType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($params['spot_type_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if(empty($params['spot_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_spot_type WHERE spot_type_name = :spot_type_name";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param('spot_type_name', $params['spot_type_name']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				if($result_duplication[0]['spot_type_id'] == $params['spot_type_id']) {
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

}

