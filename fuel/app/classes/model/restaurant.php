<?php

class Model_Restaurant extends Model
{

	/*
	 * 添加餐饮店
	 */
	public static function InsertRestaurant($params) {
		try {
			//添加餐饮店
			$sql = "INSERT INTO t_restaurant(restaurant_name, restaurant_area, restaurant_type, restaurant_price_min, restaurant_price_max, "
						. "restaurant_status, delete_flag, created_at, created_by, modified_at, modified_by) "
						. "VALUES(:restaurant_name, :restaurant_area, :restaurant_type, :restaurant_price_min, :restaurant_price_max, "
						. ":restaurant_status, 0, :created_at, :created_by, :modified_at, :modified_by)";
			$query = DB::query($sql);
			$query->param('restaurant_name', $params['restaurant_name']);
			$query->param('restaurant_area', $params['restaurant_area']);
			$query->param('restaurant_type', $params['restaurant_type']);
			$query->param('restaurant_price_min', $params['restaurant_price_min']);
			$query->param('restaurant_price_max', $params['restaurant_price_max']);
			$query->param('restaurant_status', $params['restaurant_status']);
			$time_now = date('Y-m-d H:i:s', time());
			$query->param('created_at', $time_now);
			$query->param('created_by', $params['created_by']);
			$query->param('modified_at', $time_now);
			$query->param('modified_by', $params['modified_by']);
			$result = $query->execute();
			
			if($result) {
				//新餐饮店ID
				$restaurant_id = intval($result[0]);
				return $restaurant_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除餐饮店
	 */
	public static function DeleteRestaurant($params) {
		try {
			//删除餐饮店
			$sql = "UPDATE t_restaurant SET delete_flag = 1, restaurant_status=0, modified_at=:modified_at, modified_by=:modified_by WHERE restaurant_id IN :restaurant_id_list";
			$query = DB::query($sql);
			$query->param('restaurant_id_list', $params['restaurant_id_list']);
			$query->param('modified_at', date('Y-m-d H:i:s', time()));
			$query->param('modified_by', $params['deleted_by']);
			$result = $query->execute();
			
			return $result;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新餐饮店
	 */
	public static function UpdateRestaurant($params) {
		try {
			//更新餐饮店
			$sql = "UPDATE t_restaurant "
				. "SET restaurant_name=:restaurant_name, restaurant_area=:restaurant_area, restaurant_type=:restaurant_type, "
				. "restaurant_price_min=:restaurant_price_min, restaurant_price_max=:restaurant_price_max, restaurant_status=:restaurant_status, "
				. "modified_at=:modified_at, modified_by=:modified_by "
				. "WHERE restaurant_id=:restaurant_id";
			$query = DB::query($sql);
			$query->param('restaurant_id', $params['restaurant_id']);
			$query->param('restaurant_name', $params['restaurant_name']);
			$query->param('restaurant_area', $params['restaurant_area']);
			$query->param('restaurant_type', $params['restaurant_type']);
			$query->param('restaurant_price_min', $params['restaurant_price_min']);
			$query->param('restaurant_price_max', $params['restaurant_price_max']);
			$query->param('restaurant_status', $params['restaurant_status']);
			$query->param('modified_at', date('Y-m-d H:i:s', time()));
			$query->param('modified_by', $params['modified_by']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新餐饮店状态
	 */
	public static function UpdateRestaurantStatus($params) {
		try {
			$sql = "UPDATE t_restaurant SET restaurant_status = :restaurant_status WHERE restaurant_id = :restaurant_id";
			$query = DB::query($sql);
			$query->param('restaurant_id', $params['restaurant_id']);
			$query->param('restaurant_status', $params['restaurant_status']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	/*
	 * 按条件获得餐饮店列表
	 */
	public static function SelectRestaurantList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "created_at";
			$sql_order_method = "desc";
			$sql_limit = "";
			$sql_offset = "";
			
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'restaurant_id_list':
						if(count($param_value)) {
							$sql_where[] = " tr.restaurant_id IN :restaurant_id_list ";
							$sql_params['restaurant_id_list'] = $param_value;
						}
						break;
					case 'restaurant_name':
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $name_key => $name) {
								$sql_sub_where[] = "tr.restaurant_name LIKE :restaurant_name_" . $name_key;
								$sql_params['restaurant_name_' . $name_key] = '%' . $name . '%';
							}
							$sql_where[] = " (" . implode(" OR ", $sql_sub_where) . ") ";
						}
						break;
					case 'restaurant_status':
						if(count($param_value)) {
							$sql_where[] = " tr.restaurant_status IN :restaurant_status_list ";
							$sql_params['restaurant_status_list'] = $param_value;
						}
						break;
					case 'restaurant_area':
						if(count($param_value)) {
							$sql_where[] = " tr.restaurant_area IN :restaurant_area_list ";
							$sql_params['restaurant_area_list'] = $param_value;
						}
						break;
					case 'restaurant_type':
						if(count($param_value)) {
							$sql_where[] = " tr.restaurant_type IN :restaurant_type_list ";
							$sql_params['restaurant_type_list'] = $param_value;
						}
						break;
					case 'price_min':
						if(is_numeric($param_value)) {
							$sql_where[] = " tr.restaurant_price_max >= :price_min ";
							$sql_params['price_min'] = floatval($param_value);
						}
						break;
					case 'price_max':
						if(is_numeric($param_value)) {
							$sql_where[] = " tr.restaurant_price_min <= :price_max ";
							$sql_params['price_max'] = floatval($param_value);
						}
						break;
					case 'created_by':
						$sql_where[] = " tr.created_by = :created_by ";
						$sql_params['created_by'] = $param_value;
						break;
					case 'active_only':
						$sql_where[] = " tr.delete_flag = 0 ";
						break;
					case 'sort_column':
						$sort_column_list = array('restaurant_name', 'restaurant_area', 'restaurant_type', 'restaurant_status', 'restaurant_price', 'created_at', 'modified_at');
						if(in_array($param_value, $sort_column_list)) {
							$sql_order_column = $param_value;
						}
						break;
					case 'sort_method':
						if(in_array($param_value, array('asc', 'desc'))) {
							$sql_order_method = $param_value;
						}
						break;
					case '':
						break;
				}
			}
			
			if(isset($params['num_per_page']) && isset($params['page'])) {
				$sql_limit = intval($params['num_per_page']);
				$sql_offset = (intval($params['page']) - 1) * $sql_limit;
				$sql_limit = " LIMIT " . $sql_limit;
				$sql_offset = " OFFSET " . $sql_offset;
			}
			
			//符合条件的餐饮店总数获取
			$sql_count = "SELECT COUNT(DISTINCT tr.restaurant_id) restaurant_count "
						. "FROM t_restaurant tr "
						. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_count = DB::query($sql_count);
			foreach ($sql_params as $param_key => $param_value) {
				$query_count->param($param_key, $param_value);
			}
			$result_count = $query_count->execute()->as_array();

			if(count($result_count)) {
				$restaurant_count = intval($result_count[0]['restaurant_count']);

				if($restaurant_count) {
					//餐饮店信息获取
					$sql_restaurant = "SELECT tr.*, ma.area_name restaurant_area_name, mrt.restaurant_type_name " 
									. "FROM t_restaurant tr " 
									. "LEFT JOIN m_area ma ON tr.restaurant_area = ma.area_id "
									. "LEFT JOIN m_restaurant_type mrt ON tr.restaurant_type = mrt.restaurant_type_id "
									. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
									. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
									. $sql_limit . $sql_offset;
					$query_restaurant = DB::query($sql_restaurant);
					foreach ($sql_params as $param_key => $param_value) {
						$query_restaurant->param($param_key, $param_value);
					}
					$result_restaurant = $query_restaurant->execute()->as_array();

					if(count($result_restaurant)) {
						$result = array(
							'restaurant_count' => $restaurant_count,
							'restaurant_list' => $result_restaurant,
							'start_number' => $sql_offset + 1,
							'end_number' => count($result_restaurant) + $sql_offset,
						);
						return $result;
					}
				}
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 获取特定单个餐饮店信息
	 */
	public static function SelectRestaurant($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//餐饮店ID限定
			if(isset($params['restaurant_id'])) {
				$sql_where[] = " tr.restaurant_id = :restaurant_id ";
				$sql_params['restaurant_id'] = $params['restaurant_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " tr.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql_restaurant = "SELECT tr.*, ma.area_name restaurant_area_name, ma.area_description restaurant_area_description, mrt.restaurant_type_name, tuc.user_name created_name, tum.user_name modified_name " 
							. "FROM t_restaurant tr " 
							. "LEFT JOIN m_area ma ON tr.restaurant_area = ma.area_id " 
							. "LEFT JOIN m_restaurant_type mrt ON tr.restaurant_type = mrt.restaurant_type_id " 
							. "LEFT JOIN t_user tuc ON tr.created_by = tuc.user_id " 
							. "LEFT JOIN t_user tum ON tr.modified_by = tum.user_id " 
							. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_restaurant = DB::query($sql_restaurant);
			foreach($sql_params as $param_key => $param_value) {
				$query_restaurant->param($param_key, $param_value);
			}
			$result_restaurant = $query_restaurant->execute()->as_array();
			
			if(count($result_restaurant) == 1) {
				$result = $result_restaurant[0];
				return $result;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 编辑餐饮店前编辑信息查验
	 */
	public static function CheckEditRestaurant($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//餐饮店名称
		if(empty($params['restaurant_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_restaurant_name';
		} elseif(mb_strlen($params['restaurant_name']) > 100) {
			$result['result'] = false;
			$result['error'][] = 'long_restaurant_name';
		} elseif(Model_Restaurant::CheckRestaurantNameDuplication($params['restaurant_id'], $params['restaurant_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_restaurant_name';
		}
		
		//餐饮店区域
		if(empty($params['restaurant_area'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_restaurant_area';
		} elseif(!is_numeric($params['restaurant_area']) || !is_int($params['restaurant_area'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_restaurant_area';
		} elseif(!Model_Area::CheckAreaIdExist($params['restaurant_area'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_restaurant_area';
		}
		
		//餐饮店类型
		if(empty($params['restaurant_type'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_restaurant_type';
		} elseif(!is_numeric($params['restaurant_type']) || !is_int($params['restaurant_type'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_restaurant_type';
		} elseif(!Model_Restauranttype::CheckRestaurantTypeIdExist($params['restaurant_type'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_restaurant_type';
		}
		
		//价格
		if(empty($params['restaurant_price_min']) || empty($params['restaurant_price_max'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_restaurant_price';
		} elseif(!is_numeric($params['restaurant_price_min']) || !is_int($params['restaurant_price_min'] + 0)
				|| !is_numeric($params['restaurant_price_max']) || !is_int($params['restaurant_price_max'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_restaurant_price';
		} elseif(intval($params['restaurant_price_min']) < 0 || intval($params['restaurant_price_max']) < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_restaurant_price';
		} elseif(intval($params['restaurant_price_max']) < intval($params['restaurant_price_min'])) {
			$result['result'] = false;
			$result['error'][] = 'error_restaurant_price';
		}
		
		//公开状态
		if(!in_array($params['restaurant_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		
		return $result;
	}
	
	/*
	 * 删除餐饮店前删除信息查验
	 */
	public static function CheckDeleteRestaurant($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_array($params['restaurant_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_restaurant_id';
		} elseif(!count($params['restaurant_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_restaurant_id';
		} else {
			$all_num_flag = true;
			
			foreach($params['restaurant_id_list'] as $restaurant_id) {
				if(!is_numeric($restaurant_id)) {
					$result['result'] = false;
					$all_num_flag = false;
					$result['error'][] = 'nonum_restaurant_id';
					break;
				}
			}
			
			if($all_num_flag) {
				$params_select = array('restaurant_id_list' => $params['restaurant_id_list']);
				$result_select = Model_Restaurant::SelectRestaurantList($params_select);
				
				if($result_select['restaurant_count'] != count(array_unique($params['restaurant_id_list']))) {
					$result['result'] = false;
					$result['error'][] = 'error_restaurant_id';
				} elseif($params['self_only']) {
					foreach($result_select['restaurant_list'] as $restaurant_select) {
						if($restaurant_select['delete_by'] != $restaurant_select) {
							$result['result'] = false;
							$result['error'][] = 'error_creator';
							break;
						}
					}
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 更新餐饮店公开状态前更新信息查验
	 */
	public static function CheckUpdateRestaurantStatus($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!in_array($params['restaurant_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_restaurant_status';
		}
		
		return $result;
	}
	
	/*
	 * 餐饮店名称重复查验
	 */
	public static function CheckRestaurantNameDuplication($restaurant_id, $restaurant_name) {
		try {
			//数据获取
			$sql = "SELECT restaurant_id FROM t_restaurant WHERE restaurant_name = :restaurant_name AND delete_flag = 0" . ($restaurant_id ? " AND restaurant_id != :restaurant_id " : "");
			$query = DB::query($sql);
			if($restaurant_id) {
				$query->param('restaurant_id', $restaurant_id);
			}
			$query->param('restaurant_name', $restaurant_name);
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
	 * 批量导入餐饮店用模板Excel更新
	 */
	public static function ModifyRestaurantModelExcel() {
		try {
			//修改批量导入餐饮店用模板Excel
			//Excel处理用组件
			include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
			include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
			
			//读取模板
			$xls = PHPExcel_IOFactory::load(DOCROOT . '/assets/xls/model/import_restaurant_model.xls');
			$sheet = $xls->getSheetByName('餐饮店');
			
			//餐饮店类别名列表
			$configs = array();
			$restaurant_type_list = Model_RestaurantType::SelectRestaurantTypeList(array('active_only' => 1));
			foreach($restaurant_type_list as $restaurant_type) {
				$configs[] = $restaurant_type['restaurant_type_name'];
			}
			
			for($row_counter = 3; $row_counter < 101; $row_counter++) {
				//编辑餐饮店地区下拉列表
				$validation_area = $sheet->getCell('B' . $row_counter)->getDataValidation();
				$validation_area->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$validation_area->setAllowBlank(true);
				$validation_area->setShowDropDown(true);
				$validation_area->setFormula1('"北海道地方,東北地方,関東地方,中部地方,近畿地方,中国地方,四国地方,九州地方"');
				
				//编辑餐饮店类别下拉列表
				$validation_type = $sheet->getCell('C' . $row_counter)->getDataValidation();
				$validation_type->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$validation_type->setAllowBlank(true);
				$validation_type->setShowDropDown(true);
				$validation_type->setFormula1('"' . implode(',', $configs) . '"');
			}
			
			//更新文件
			$writer = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			$writer->save(DOCROOT . '/assets/xls/model/import_restaurant_model.xls');
			
			//释放缓存
			$xls->disconnectWorksheets();
			unset($writer);
			unset($sheet);
			unset($xls);
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

}
