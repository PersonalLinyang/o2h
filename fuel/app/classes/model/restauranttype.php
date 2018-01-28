<?php

class Model_Restauranttype extends Model
{

	/*
	 * 添加餐饮类别
	 */
	public static function InsertRestaurantType($params) {
		try {
			$sql = "INSERT INTO m_restaurant_type(restaurant_type_name, delete_flag, sort_id) VALUES(:restaurant_type_name, 0, 1)";
			$query = DB::query($sql);
			$query->param('restaurant_type_name', $params['restaurant_type_name']);
			$result = $query->execute();
			
			if($result) {
				//新餐饮类别ID
				$restaurant_type_id = intval($result[0]);
				return $restaurant_type_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除餐饮类别
	 */
	public static function DeleteRestaurantType($params) {
		try {
			//删除餐饮类别
			$sql_type = "UPDATE m_restaurant_type SET delete_flag = 1 WHERE restaurant_type_id = :restaurant_type_id";
			$query_type = DB::query($sql_type);
			$query_type->param('restaurant_type_id', $params['restaurant_type_id']);
			$result_type = $query_type->execute();
			
			return $result_type;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新餐饮类别名称
	 */
	public static function UpdateRestaurantType($params) {
		try {
			$sql = "UPDATE m_restaurant_type SET restaurant_type_name = :restaurant_type_name WHERE restaurant_type_id = :restaurant_type_id";
			$query = DB::query($sql);
			$query->param('restaurant_type_id', $params['restaurant_type_id']);
			$query->param('restaurant_type_name', $params['restaurant_type_name']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	//获得符合特定条件的餐饮类别
	public static function SelectRestaurantTypeList($params) {
		try{
			$sql_select = array();
			$sql_from = array();
			$sql_where = array();
			$sql_group_by = array();
			$sql_params = array();
			
			//有效餐饮类别限定
			if(isset($params['active_only'])) {
				$sql_where[] = " mst.delete_flag = 0 ";
			}
			//获取所属餐饮数
			if(isset($params['restaurant_count_flag'])) {
				$sql_select[] = " COUNT(ts.restaurant_id) restaurant_count ";
				$sql_from[] = " LEFT JOIN (SELECT * FROM t_restaurant WHERE delete_flag=0) ts ON ts.restaurant_type = mst.restaurant_type_id ";
				$sql_group_by[] = " mst.restaurant_type_id ";
			}
			
			$sql = "SELECT mst.* " . (count($sql_select) ? (", " . implode(", ", $sql_select)) : "") 
				. "FROM m_restaurant_type mst " . (count($sql_from) ? implode(" ", array_unique($sql_from)) : "") 
				. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "") 
				. (count($sql_group_by) ? (" GROUP BY " . implode(", ", array_unique($sql_group_by))) : "") 
				. " ORDER BY mst.sort_id, mst.restaurant_type_id";
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
	 * 获取特定单个餐饮类别信息
	 */
	public static function SelectRestaurantType($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//餐饮类别ID限定
			if(isset($params['restaurant_type_id'])) {
				$sql_where[] = " mst.restaurant_type_id = :restaurant_type_id ";
				$sql_params['restaurant_type_id'] = $params['restaurant_type_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " mst.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql = "SELECT * FROM m_restaurant_type mst " 
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
	 * 编辑餐饮类别前编辑信息查验
	 */
	public static function CheckEditRestaurantType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//餐饮类别名称
		if(empty($params['restaurant_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_restaurant_type_name';
		} elseif(mb_strlen($params['restaurant_type_name']) > 50) {
			$result['result'] = false;
			$result['error'][] = 'long_restaurant_type_name';
		} elseif(Model_Restauranttype::CheckRestaurantTypeNameDuplication($params['restaurant_type_id'], $params['restaurant_type_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_restaurant_type_name';
		}
		
		return $result;
	}
	
	/*
	 * 删除餐饮类别前删除信息查验
	 */
	public static function CheckDeleteRestaurantType($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($params['restaurant_type_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_restaurant_type_id';
		} elseif(!Model_Restauranttype::CheckRestaurantTypeIdExist($params['restaurant_type_id'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_restaurant_type_id';
		} else {
			//获取餐饮信息
			$params_select = array(
				'restaurant_type' => array($params['restaurant_type_id']),
				'active_only' => 1,
			);
			$restaurant_select = Model_Restaurant::SelectRestaurantList($params_select);
			
			if($restaurant_select['restaurant_count']) {
				$result['result'] = false;
				$result['error'][] = 'error_restaurant_list';
			}
		}
		
		return $result;
	}
	
	/*
	 * 检查餐饮类别ID是否存在
	 */
	public static function CheckRestaurantTypeIdExist($restaurant_type_id, $active_check = 0) {
		try {
			$sql = "SELECT restaurant_type_id FROM m_restaurant_type WHERE restaurant_type_id = :restaurant_type_id " . ($active_check ? " AND delete_flag = 0 " : "");
			$query = DB::query($sql);
			$query->param('restaurant_type_id', $restaurant_type_id);
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
	 * 餐饮类别名称重复查验
	 */
	public static function CheckRestaurantTypeNameDuplication($restaurant_type_id, $restaurant_type_name) {
		try {
			//数据获取
			$sql = "SELECT restaurant_type_id FROM m_restaurant_type WHERE restaurant_type_name = :restaurant_type_name AND delete_flag = 0" . ($restaurant_type_id ? " AND restaurant_type_id != :restaurant_type_id " : "");
			$query = DB::query($sql);
			if($restaurant_type_id) {
				$query->param('restaurant_type_id', $restaurant_type_id);
			}
			$query->param('restaurant_type_name', $restaurant_type_name);
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
	
	/*
	 * 餐饮信息上传用模板Excel更新
	 */
	public static function ModifyRestaurantModelExcel() {
		try {
			//修改餐饮上传用模板Excel
			//Excel处理用组件
			include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
			include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
			
			//读取模板
			$xls_model = PHPExcel_IOFactory::load(DOCROOT . '/assets/xls/model/import_restaurant_model.xls');
			$sheet_type = $xls_model->getSheetByName('restaurant_type');
			
			//删除原有信息
			$sheet_type->removeColumn('A');
			
			//写入餐饮类别名列表
			$restaurant_type_list = Model_RestaurantType::SelectRestaurantTypeList(array('active_only' => 1));
			$row_counter = 1;
			foreach($restaurant_type_list as $restaurant_type) {
				$sheet_type->setCellValue('B' . $row_counter, $restaurant_type['restaurant_type_name']);
				$row_counter++;
			}
			
			//删除原有信息
			$sheet_type->removeColumn('A');
			
			//写入餐饮类别名列表
			$restaurant_type_list = Model_RestaurantType::SelectRestaurantTypeList(array('active_only' => 1));
			$row_counter = 1;
			foreach($restaurant_type_list as $restaurant_type) {
				$sheet_type->setCellValue('B' . $row_counter, $restaurant_type['restaurant_type_name']);
				$row_counter++;
			}
			
			//删除原有信息
			$sheet_type->removeColumn('A');
			
			//更新文件
			$writer_xls = PHPExcel_IOFactory::createWriter($xls_model, 'Excel2007');
			$writer_xls->save(DOCROOT . '/assets/xls/model/import_restaurant_model.xls');
			
			//释放缓存
			$xls_model->disconnectWorksheets();
			unset($writer_xls);
			unset($sheet_type);
			unset($xls_model);
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

}

