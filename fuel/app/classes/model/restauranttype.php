<?php

class Model_Restauranttype extends Model
{

	/*
	 * 添加餐饮类别
	 */
	public static function InsertRestaurantType($params) {
		$sql_insert = "INSERT INTO m_restaurant_type(restaurant_type_name) VALUES(:restaurant_type_name)";
		$query_insert = DB::query($sql_insert);
		$query_insert->param('restaurant_type_name', $params['restaurant_type_name']);
		$result_insert = $query_insert->execute();
		
		return $result_insert;
	}
	
	/*
	 * 根据ID删除餐饮类别
	 */
	public static function DeleteRestaurantTypeById($restaurant_type_id) {
		$sql_delete = "DELETE FROM m_restaurant_type WHERE restaurant_type_id = :restaurant_type_id";
		$query_delete = DB::query($sql_delete);
		$query_delete->param('restaurant_type_id', $restaurant_type_id);
		$result_delete = $query_delete->execute();
		
		return $result_delete;
	}
	
	/*
	 * 更新餐饮类别名称
	 */
	public static function UpdateRestaurantType($params) {
		$sql_update = "UPDATE m_restaurant_type SET restaurant_type_name = :restaurant_type_name WHERE restaurant_type_id = :restaurant_type_id";
		$query_update = DB::query($sql_update);
		$query_update->param('restaurant_type_id', $params['restaurant_type_id']);
		$query_update->param('restaurant_type_name', $params['restaurant_type_name']);
		$result_update = $query_update->execute();
		
		return $result_update;
	}

	/*
	 * 获取全部餐饮类别信息
	 */
	public static function GetRestaurantTypeListAll() {
		$sql_restaurant_type = "SELECT mrt.restaurant_type_id, mrt.restaurant_type_name, COUNT(tr.restaurant_id) restaurant_count "
						. "FROM m_restaurant_type mrt LEFT JOIN t_restaurant tr ON tr.restaurant_type = mrt.restaurant_type_id " 
						. "GROUP BY restaurant_type_id, restaurant_type_name ORDER BY restaurant_type_id";
		$query_restaurant_type = DB::query($sql_restaurant_type);
		$restaurant_type_list = $query_restaurant_type->execute()->as_array();
		
		return $restaurant_type_list;
	}
	
	/*
	 * 根据ID获取主功能组信息
	 */
	public static function SelectRestaurantTypeById($restaurant_type_id) {
		if(!is_numeric($restaurant_type_id)) {
			return false;
		}
		
		$sql = "SELECT * FROM m_restaurant_type WHERE restaurant_type_id = :restaurant_type_id";
		$query = DB::query($sql);
		$query->param('restaurant_type_id', $restaurant_type_id);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	/*
	 * 添加餐饮类别前添加信息查验
	 */
	public static function CheckInsertRestaurantType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!isset($params['restaurant_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'noset_name';
		} elseif(empty($params['restaurant_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_restaurant_type WHERE restaurant_type_name = :restaurant_type_name";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param('restaurant_type_name', $params['restaurant_type_name']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				$result['result'] = false;
				$result['error'][] = 'duplication';
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除餐饮类别前删除ID查验
	 */
	public static function CheckDeleteRestaurantTypeById($restaurant_type_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($restaurant_type_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM m_restaurant_type WHERE restaurant_type_id = :restaurant_type_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param('restaurant_type_id', $restaurant_type_id);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}
	
	/*
	 * 更新餐饮类别前更新信息查验
	 */
	public static function CheckUpdateRestaurantType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($params['restaurant_type_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if(empty($params['restaurant_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		
		if($result['result']) {
			$sql_duplication = "SELECT * FROM m_restaurant_type WHERE restaurant_type_name = :restaurant_type_name";
			$query_duplication = DB::query($sql_duplication);
			$query_duplication->param('restaurant_type_name', $params['restaurant_type_name']);
			$result_duplication = $query_duplication->execute()->as_array();
			
			if(count($result_duplication)) {
				if($result_duplication[0]['restaurant_type_id'] == $params['restaurant_type_id']) {
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

