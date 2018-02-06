<?php

class Model_Hotel extends Model
{

	/*
	 * 添加酒店
	 */
	public static function InsertHotel($params) {
		try {
			//添加酒店
			$sql_hotel = "INSERT INTO t_hotel(hotel_name, hotel_area, hotel_type, hotel_price, hotel_status, "
						. "delete_flag, created_at, created_by, modified_at, modified_by) "
						. "VALUES(:hotel_name, :hotel_area, :hotel_type, :hotel_price, :hotel_status, "
						. "0, :created_at, :created_by, :modified_at, :modified_by)";
			$query_hotel = DB::query($sql_hotel);
			$query_hotel->param('hotel_name', $params['hotel_name']);
			$query_hotel->param('hotel_area', $params['hotel_area']);
			$query_hotel->param('hotel_type', $params['hotel_type']);
			$query_hotel->param('hotel_price', $params['hotel_price']);
			$query_hotel->param('hotel_status', $params['hotel_status']);
			$time_now = date('Y-m-d H:i:s', time());
			$query_hotel->param('created_at', $time_now);
			$query_hotel->param('created_by', $params['created_by']);
			$query_hotel->param('modified_at', $time_now);
			$query_hotel->param('modified_by', $params['modified_by']);
			$result_hotel = $query_hotel->execute();
			
			if($result_hotel) {
				//新酒店ID
				$hotel_id = intval($result_hotel[0]);
				
				//添加可选房型
				$sql_values_room = array();
				$sql_params_room = array();
				foreach($params['room_type_list'] as $param_key => $room_type_id) {
					$sql_values_room[] = "(:hotel_id, :room_type_" . $param_key . ")";
					$sql_params_room['room_type_' . $param_key] = $room_type_id;
				}
				
				if(count($sql_values_room)) {
					$sql_room = "INSERT INTO r_hotel_room(hotel_id, room_type_id) VALUES" . implode(",", $sql_values_room);
					$query_room = DB::query($sql_room);
					$query_room->param('hotel_id', $hotel_id);
					foreach($sql_params_room as $param_key => $param_value) {
						$query_room->param($param_key, $param_value);
					}
					$result_room = $query_room->execute();
				}
				
				return $hotel_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除酒店
	 */
	public static function DeleteHotel($params) {
		try {
			//删除酒店
			$sql_hotel = "UPDATE t_hotel SET delete_flag = 1, hotel_status=0, modified_at=:modified_at, modified_by=:modified_by WHERE hotel_id IN :hotel_id_list";
			$query_hotel = DB::query($sql_hotel);
			$query_hotel->param('hotel_id_list', $params['hotel_id_list']);
			$query_hotel->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_hotel->param('modified_by', $params['deleted_by']);
			$result_hotel = $query_hotel->execute();
			
			//删除可选房型
			$sql_room = "DELETE FROM r_hotel_room WHERE hotel_id IN :hotel_id_list";
			$query_room = DB::query($sql_room);
			$query_room->param('hotel_id_list', $params['hotel_id_list']);
			$result_room = $query_room->execute();
			
			return $result_hotel;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新酒店
	 */
	public static function UpdateHotel($params) {
		try {
			//更新酒店
			$sql_hotel = "UPDATE t_hotel "
						. "SET hotel_name=:hotel_name, hotel_area=:hotel_area, hotel_type=:hotel_type, "
						. "hotel_price=:hotel_price, hotel_status=:hotel_status, modified_at=:modified_at, modified_by=:modified_by "
						. "WHERE hotel_id=:hotel_id";
			$query_hotel = DB::query($sql_hotel);
			$query_hotel->param('hotel_id', $params['hotel_id']);
			$query_hotel->param('hotel_name', $params['hotel_name']);
			$query_hotel->param('hotel_area', $params['hotel_area']);
			$query_hotel->param('hotel_type', $params['hotel_type']);
			$query_hotel->param('hotel_price', $params['hotel_price']);
			$query_hotel->param('hotel_status', $params['hotel_status']);
			$query_hotel->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_hotel->param('modified_by', $params['modified_by']);
			$result_hotel = $query_hotel->execute();
			
			//删除原有可选房型
			$sql_room_delete = "DELETE FROM r_hotel_room WHERE hotel_id=:hotel_id";
			$query_room_delete = DB::query($sql_room_delete);
			$query_room_delete->param('hotel_id', $params['hotel_id']);
			$result_room_delete = $query_room_delete->execute();
			
			//添加可选房型
			$sql_values_room = array();
			$sql_params_room = array();
			foreach($params['room_type_list'] as $param_key => $room_type_id) {
				$sql_values_room[] = "(:hotel_id, :room_type_" . $param_key . ")";
				$sql_params_room['room_type_' . $param_key] = $room_type_id;
			}
			
			if(count($sql_values_room)) {
				$sql_room = "INSERT INTO r_hotel_room(hotel_id, room_type_id) VALUES" . implode(",", $sql_values_room);
				$query_room = DB::query($sql_room);
				$query_room->param('hotel_id', $params['hotel_id']);
				foreach($sql_params_room as $param_key => $param_value) {
					$query_room->param($param_key, $param_value);
				}
				$result_room = $query_room->execute();
			}
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新酒店状态
	 */
	public static function UpdateHotelStatus($params) {
		try {
			$sql = "UPDATE t_hotel SET hotel_status = :hotel_status WHERE hotel_id = :hotel_id";
			$query = DB::query($sql);
			$query->param('hotel_id', $params['hotel_id']);
			$query->param('hotel_status', $params['hotel_status']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 按条件获得酒店列表
	 */
	public static function SelectHotelList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "created_at";
			$sql_order_method = "desc";
			$sql_limit = "";
			$sql_offset = "";
			
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'hotel_id_list':
						if(count($param_value)) {
							$sql_where[] = " th.hotel_id IN :hotel_id_list ";
							$sql_params['hotel_id_list'] = $param_value;
						}
						break;
					case 'hotel_name':
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $name_key => $name) {
								$sql_sub_where[] = "th.hotel_name LIKE :hotel_name_" . $name_key;
								$sql_params['hotel_name_' . $name_key] = '%' . $name . '%';
							}
							$sql_where[] = " (" . implode(" OR ", $sql_sub_where) . ") ";
						}
						break;
					case 'hotel_status':
						if(count($param_value)) {
							$sql_where[] = " th.hotel_status IN :hotel_status_list ";
							$sql_params['hotel_status_list'] = $param_value;
						}
						break;
					case 'hotel_area':
						if(count($param_value)) {
							$sql_where[] = " th.hotel_area IN :hotel_area_list ";
							$sql_params['hotel_area_list'] = $param_value;
						}
						break;
					case 'hotel_type':
						if(count($param_value)) {
							$sql_where[] = " th.hotel_type IN :hotel_type_list ";
							$sql_params['hotel_type_list'] = $param_value;
						}
						break;
					case 'price_min':
						if(is_numeric($param_value)) {
							$sql_where[] = " th.hotel_price >= :price_min ";
							$sql_params['price_min'] = floatval($param_value);
						}
						break;
					case 'price_max':
						if(is_numeric($param_value)) {
							$sql_where[] = " th.hotel_price <= :price_max ";
							$sql_params['price_max'] = floatval($param_value);
						}
						break;
					case 'room_type':
						if(count($param_value)) {
							$sql_where[] = " th.hotel_id IN (SELECT hotel_id FROM r_hotel_room WHERE room_type_id IN :room_type_list) ";
							$sql_params['room_type_list'] = $param_value;
						}
						break;
					case 'created_by':
						$sql_where[] = " th.created_by = :created_by ";
						$sql_params['created_by'] = $param_value;
						break;
					case 'active_only':
						$sql_where[] = " th.delete_flag = 0 ";
						break;
					case 'sort_column':
						$sort_column_list = array('hotel_name', 'hotel_area', 'hotel_type', 'hotel_status', 'hotel_price', 'created_at', 'modified_at');
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
			
			//符合条件的酒店总数获取
			$sql_count = "SELECT COUNT(DISTINCT th.hotel_id) hotel_count "
						. "FROM t_hotel th "
						. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_count = DB::query($sql_count);
			foreach ($sql_params as $param_key => $param_value) {
				$query_count->param($param_key, $param_value);
			}
			$result_count = $query_count->execute()->as_array();
			
			if(count($result_count)) {
				$hotel_count = intval($result_count[0]['hotel_count']);
				
				if($hotel_count) {
					//酒店信息获取
					$sql_hotel = "SELECT th.*, ma.area_name hotel_area_name, mht.hotel_type_name " 
							. "FROM t_hotel th " 
							. "LEFT JOIN m_area ma ON th.hotel_area = ma.area_id "
							. "LEFT JOIN m_hotel_type mht ON th.hotel_type = mht.hotel_type_id "
							. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
							. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
							. $sql_limit . $sql_offset;
					$query_hotel = DB::query($sql_hotel);
					foreach ($sql_params as $param_key => $param_value) {
						$query_hotel->param($param_key, $param_value);
					}
					$result_hotel = $query_hotel->execute()->as_array();
					
					if(count($result_hotel)) {
						$hotel_list = array();
						$hotel_id_list = array();
						foreach($result_hotel as $hotel) {
							$hotel_list[$hotel['hotel_id']] = $hotel;
							$hotel_list[$hotel['hotel_id']]['room_type_list'] = array();
							$hotel_id_list[] = intval($hotel['hotel_id']);
						}
						
						//可选房型信息获取
						if(isset($params['room_flag'])) {
							$sql_room = "SELECT rhr.hotel_id, mrt.* "
									. "FROM r_hotel_room rhr "
									. "LEFT JOIN m_room_type mrt ON mrt.room_type_id = rhr.room_type_id "
									. "WHERE rhr.hotel_id IN :hotel_id_list "
									. "ORDER BY rhr.hotel_id ASC, mrt.room_type_id ASC";
							$query_room = DB::query($sql_room);
							$query_room->param('hotel_id_list', $hotel_id_list);
							$result_room = $query_room->execute()->as_array();
							
							if(count($result_room)) {
								foreach($result_room as $room_type) {
									$hotel_list[$room_type['hotel_id']]['room_type_list'][] = $room_type;
								}
							}
						}
						
						//返回值整理
						$result = array(
							'hotel_count' => $hotel_count,
							'hotel_list' => $hotel_list,
							'start_number' => $sql_offset + 1,
							'end_number' => count($result_hotel) + $sql_offset,
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
	 * 获取特定单个酒店信息
	 */
	public static function SelectHotel($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//酒店ID限定
			if(isset($params['hotel_id'])) {
				$sql_where[] = " th.hotel_id = :hotel_id ";
				$sql_params['hotel_id'] = $params['hotel_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " th.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql_hotel = "SELECT th.*, ma.area_name hotel_area_name, ma.area_description hotel_area_description, mht.hotel_type_name, tuc.user_name created_name, tum.user_name modified_name " 
					. "FROM t_hotel th " 
					. "LEFT JOIN m_area ma ON th.hotel_area = ma.area_id " 
					. "LEFT JOIN m_hotel_type mht ON th.hotel_type = mht.hotel_type_id " 
					. "LEFT JOIN t_user tuc ON th.created_by = tuc.user_id " 
					. "LEFT JOIN t_user tum ON th.modified_by = tum.user_id " 
					. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_hotel = DB::query($sql_hotel);
			foreach($sql_params as $param_key => $param_value) {
				$query_hotel->param($param_key, $param_value);
			}
			$result_hotel = $query_hotel->execute()->as_array();
			
			if(count($result_hotel) == 1) {
				$result = $result_hotel[0];
				
				$sql_room = "SELECT mrt.* FROM m_room_type mrt WHERE mrt.room_type_id IN (SELECT room_type_id FROM r_hotel_room WHERE hotel_id = :hotel_id)";
				$query_room = DB::query($sql_room);
				$query_room->param('hotel_id', $result['hotel_id']);
				$result_room = $query_room->execute()->as_array();
				
				$result['room_type_list'] = $result_room;
				
				return $result;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 编辑酒店前编辑信息查验
	 */
	public static function CheckEditHotel($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		//酒店名称
		if(empty($params['hotel_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_hotel_name';
		} elseif(mb_strlen($params['hotel_name']) > 100) {
			$result['result'] = false;
			$result['error'][] = 'long_hotel_name';
		} elseif(Model_Hotel::CheckHotelNameDuplication($params['hotel_id'], $params['hotel_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_hotel_name';
		}
		
		//酒店区域
		if(empty($params['hotel_area'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_hotel_area';
		} elseif(!is_numeric($params['hotel_area']) || !is_int($params['hotel_area'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_hotel_area';
		} elseif(!Model_Area::CheckAreaIdExist($params['hotel_area'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_hotel_area';
		}
		
		//酒店类型
		if(empty($params['hotel_type'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_hotel_type';
		} elseif(!is_numeric($params['hotel_type']) || !is_int($params['hotel_type'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_hotel_type';
		} elseif(!Model_Hoteltype::CheckHotelTypeIdExist($params['hotel_type'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_hotel_type';
		}
		
		//价格
		if(empty($params['hotel_price'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_hotel_price';
		} elseif(!is_numeric($params['hotel_price']) || !is_int($params['hotel_price'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_hotel_price';
		} elseif(intval($params['hotel_price']) < 0) {
			$result['result'] = false;
			$result['error'][] = 'minus_hotel_price';
		}
		
		//公开状态
		if(!in_array($params['hotel_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		
		//房型
		if(!is_array($params['room_type_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_room';
		} elseif(!count($params['room_type_list'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_room';
		} elseif(!Model_Roomtype::CheckRoomTypeIdListExist($params['room_type_list'], true)) {
			$result['result'] = false;
			$result['error'][] = 'error_room';
		}
		
		return $result;
	}
	
	/*
	 * 删除酒店前删除信息查验
	 */
	public static function CheckDeleteHotel($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_array($params['hotel_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_hotel_id';
		} elseif(!count($params['hotel_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_hotel_id';
		} else {
			$all_num_flag = true;
			
			foreach($params['hotel_id_list'] as $hotel_id) {
				if(!is_numeric($hotel_id)) {
					$result['result'] = false;
					$all_num_flag = false;
					$result['error'][] = 'nonum_hotel_id';
					break;
				}
			}
			
			if($all_num_flag) {
				$params_select = array('hotel_id_list' => $params['hotel_id_list']);
				$result_select = Model_Hotel::SelectHotelList($params_select);
				
				if($result_select['hotel_count'] != count(array_unique($params['hotel_id_list']))) {
					$result['result'] = false;
					$result['error'][] = 'error_hotel_id';
				} elseif($params['self_only']) {
					foreach($result_select['hotel_list'] as $hotel_select) {
						if($hotel_select['delete_by'] != $hotel_select) {
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
	 * 更新酒店公开状态前更新信息查验
	 */
	public static function CheckUpdateHotelStatus($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!in_array($params['hotel_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_hotel_status';
		}
		
		return $result;
	}
	
	/*
	 * 酒店名称重复查验
	 */
	public static function CheckHotelNameDuplication($hotel_id, $hotel_name) {
		try {
			//数据获取
			$sql = "SELECT hotel_id FROM t_hotel WHERE hotel_name = :hotel_name AND delete_flag = 0" . ($hotel_id ? " AND hotel_id != :hotel_id " : "");
			$query = DB::query($sql);
			if($hotel_id) {
				$query->param('hotel_id', $hotel_id);
			}
			$query->param('hotel_name', $hotel_name);
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
	 * 批量导入酒店用模板Excel更新
	 */
	public static function ModifyHotelModelExcel() {
		try {
			//修改批量导入酒店用模板Excel
			//Excel处理用组件
			include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
			include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
			
			//读取模板
			$xls = PHPExcel_IOFactory::load(DOCROOT . '/assets/xls/model/import_hotel_model.xls');
			$sheet_hotel = $xls->getSheetByName('酒店');
			$sheet_room = $xls->getSheetByName('参考-可选房型');
			
			//酒店类别名列表
			$configs = array();
			$hotel_type_list = Model_HotelType::SelectHotelTypeList(array('active_only' => 1));
			foreach($hotel_type_list as $hotel_type) {
				$configs[] = $hotel_type['hotel_type_name'];
			}
			
			//房型列表
			$room_type_list = Model_RoomType::SelectRoomTypeList(array('active_only' => 1));
			
			for($row_counter = 3; $row_counter < 101; $row_counter++) {
				//编辑酒店地区下拉列表
				$validation_area = $sheet_hotel->getCell('B' . $row_counter)->getDataValidation();
				$validation_area->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$validation_area->setAllowBlank(true);
				$validation_area->setShowDropDown(true);
				$validation_area->setFormula1('"北海道地方,東北地方,関東地方,中部地方,近畿地方,中国地方,四国地方,九州地方"');
				
				//编辑酒店类别下拉列表
				$validation_type = $sheet_hotel->getCell('C' . $row_counter)->getDataValidation();
				$validation_type->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$validation_type->setAllowBlank(true);
				$validation_type->setShowDropDown(true);
				$validation_type->setFormula1('"' . implode(',', $configs) . '"');
			}
			
			//编辑「参考-可选房型」表
			$row_counter = 1;
			foreach($room_type_list as $room_type) {
				$sheet_room->setCellValue('B' . $row_counter, $room_type['room_type_name']);
				$row_counter++;
			}
			$sheet_room->removeColumn('A');
			
			//更新文件
			$writer = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			$writer->save(DOCROOT . '/assets/xls/model/import_hotel_model.xls');
			
			//释放缓存
			$xls->disconnectWorksheets();
			unset($writer);
			unset($sheet_hotel);
			unset($sheet_room);
			unset($xls);
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

}
