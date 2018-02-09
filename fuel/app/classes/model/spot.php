<?php

class Model_Spot extends Model
{

	/*
	 * 添加景点
	 */
	public static function InsertSpot($params) {
		//添加景点
		try {
			//添加景点
			$sql_spot = "INSERT INTO t_spot(spot_name, spot_area, spot_type, free_flag, spot_price, spot_status, "
						. "delete_flag, created_at, created_by, modified_at, modified_by) "
						. "VALUES(:spot_name, :spot_area, :spot_type, :free_flag, :spot_price, :spot_status, "
						. "0, :created_at, :created_by, :modified_at, :modified_by)";
			$query_spot = DB::query($sql_spot);
			$query_spot->param('spot_name', $params['spot_name']);
			$query_spot->param('spot_area', $params['spot_area']);
			$query_spot->param('spot_type', $params['spot_type']);
			$query_spot->param('free_flag', $params['free_flag']);
			$query_spot->param('spot_price', $params['spot_price']);
			$query_spot->param('spot_status', $params['spot_status']);
			$time_now = date('Y-m-d H:i:s', time());
			$query_spot->param('created_at', $time_now);
			$query_spot->param('created_by', $params['created_by']);
			$query_spot->param('modified_at', $time_now);
			$query_spot->param('modified_by', $params['modified_by']);
			$result_spot = $query_spot->execute();
			
			if($result_spot) {
				//新景点ID
				$spot_id = intval($result_spot[0]);
				
				//添加景点详情
				$sql_values_detail = array();
				$sql_params_detail = array();
				foreach($params['spot_detail_list'] as $detail_key => $spot_detail) {
					$sql_values_detail[] = "(:spot_id, :spot_detail_id_" . $detail_key . ", "
										. ":spot_detail_name_" . $detail_key . ", :spot_description_text_" . $detail_key . ", " 
										. ":image_list_" . $detail_key . ", :two_year_flag_" . $detail_key . ", "
										. ":spot_start_month_" . $detail_key . ", :spot_end_month_" . $detail_key . ")";
					
					$sql_params_detail[':spot_detail_id_' . $detail_key] = $spot_detail['spot_detail_id'];
					$sql_params_detail[':spot_detail_name_' . $detail_key] = $spot_detail['spot_detail_name'];
					$sql_params_detail[':spot_description_text_' . $detail_key] = $spot_detail['spot_description_text'];
					$image_list = array();
					foreach($spot_detail['image_list'] as $image_info) {
						$image_list[] = $image_info['image_id'];
					}
					$sql_params_detail[':image_list_' . $detail_key] = implode(',', $image_list);
					$sql_params_detail[':two_year_flag_' . $detail_key] = $spot_detail['two_year_flag'];
					$sql_params_detail[':spot_start_month_' . $detail_key] = $spot_detail['spot_start_month'];
					$sql_params_detail[':spot_end_month_' . $detail_key] = $spot_detail['spot_end_month'];
				}
				
				if(count($sql_values_detail)) {
					$sql_detail = "INSERT INTO e_spot_detail(spot_id, spot_detail_id, spot_detail_name, spot_description_text, " 
								. "image_list, two_year_flag, spot_start_month, spot_end_month) VALUES" . implode(",", $sql_values_detail);
					$query_detail = DB::query($sql_detail);
					$query_detail->param('spot_id', $spot_id);
					foreach($sql_params_detail as $param_key => $param_value) {
						$query_detail->param($param_key, $param_value);
					}
					$result_detail = $query_detail->execute();
				}
				
				//添加特别价格
				$sql_values_price = array();
				$sql_params_price = array();
				foreach($params['special_price_list'] as $special_price_id => $special_price) {
					$sql_values_price[] = "(:spot_id, :special_price_id_" . $special_price_id . ", :special_price_name_" . $special_price_id . ", :special_price_" . $special_price_id . ")";
					
					$sql_params_price['special_price_id_' . $special_price_id] = $special_price_id;
					$sql_params_price['special_price_name_' . $special_price_id] = $special_price['special_price_name'];
					$sql_params_price['special_price_' . $special_price_id] = $special_price['special_price'];
				}
				
				if(count($sql_values_price)) {
					$sql_price = "INSERT INTO e_spot_special_price(spot_id, special_price_id, special_price_name, special_price) VALUES" . implode(",", $sql_values_price);
					$query_price = DB::query($sql_price);
					$query_price->param('spot_id', $spot_id);
					foreach($sql_params_price as $param_key => $param_value) {
						$query_price->param($param_key, $param_value);
					}
					$result_price = $query_price->execute();
				}
				
				return $spot_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 删除景点
	 */
	public static function DeleteSpot($params) {
		try {
			//删除景点
			$sql_spot = "UPDATE t_spot SET delete_flag = 1, spot_status=0, modified_at=:modified_at, modified_by=:modified_by WHERE spot_id IN :spot_id_list";
			$query_spot = DB::query($sql_spot);
			$query_spot->param('spot_id_list', $params['spot_id_list']);
			$query_spot->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_spot->param('modified_by', $params['deleted_by']);
			$result_spot = $query_spot->execute();
			
			//删除景点详情
			$sql_detail = "DELETE FROM e_spot_detail WHERE spot_id IN :spot_id_list";
			$query_detail = DB::query($sql_detail);
			$query_detail->param('spot_id_list', $params['spot_id_list']);
			$result_detail = $query_detail->execute();
			
			//删除特别价格
			$sql_price = "DELETE FROM e_spot_special_price WHERE spot_id IN :spot_id_list";
			$query_price = DB::query($sql_price);
			$query_price->param('spot_id_list', $params['spot_id_list']);
			$result_price = $query_price->execute();
			
			return $result_spot;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新景点
	 */
	public static function UpdateSpot($params) {
		try {
			//更新景点
			$sql_spot = "UPDATE t_spot "
						. "SET spot_name=:spot_name, spot_area=:spot_area, spot_type=:spot_type, free_flag=:free_flag, "
						. "spot_price=:spot_price, spot_status=:spot_status, modified_at=:modified_at, modified_by=:modified_by "
						. "WHERE spot_id=:spot_id";
			$query_spot = DB::query($sql_spot);
			$query_spot->param('spot_id', $params['spot_id']);
			$query_spot->param('spot_name', $params['spot_name']);
			$query_spot->param('spot_area', $params['spot_area']);
			$query_spot->param('spot_type', $params['spot_type']);
			$query_spot->param('free_flag', $params['free_flag']);
			$query_spot->param('spot_price', $params['spot_price']);
			$query_spot->param('spot_status', $params['spot_status']);
			$query_spot->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_spot->param('modified_by', $params['modified_by']);
			$result_spot = $query_spot->execute();
			
			//删除原有景点详情
			$sql_detail_delete = "DELETE FROM e_spot_detail WHERE spot_id=:spot_id";
			$query_detail_delete = DB::query($sql_detail_delete);
			$query_detail_delete->param('spot_id', $params['spot_id']);
			$result_detail_delete = $query_detail_delete->execute();
			
			//更新景点详情
			$sql_values_detail = array();
			$sql_params_detail = array();
			foreach($params['spot_detail_list'] as $detail_key => $spot_detail) {
				$sql_values_detail[] = "(:spot_id, :spot_detail_id_" . $detail_key . ", "
									. ":spot_detail_name_" . $detail_key . ", :spot_description_text_" . $detail_key . ", " 
									. ":image_list_" . $detail_key . ", :two_year_flag_" . $detail_key . ", "
									. ":spot_start_month_" . $detail_key . ", :spot_end_month_" . $detail_key . ")";
				
				$sql_params_detail[':spot_detail_id_' . $detail_key] = $spot_detail['spot_detail_id'];
				$sql_params_detail[':spot_detail_name_' . $detail_key] = $spot_detail['spot_detail_name'];
				$sql_params_detail[':spot_description_text_' . $detail_key] = $spot_detail['spot_description_text'];
				$image_list = array();
				foreach($spot_detail['image_list'] as $image_info) {
					$image_list[] = $image_info['image_id'];
				}
				$sql_params_detail[':image_list_' . $detail_key] = implode(',', $image_list);
				$sql_params_detail[':two_year_flag_' . $detail_key] = $spot_detail['two_year_flag'];
				$sql_params_detail[':spot_start_month_' . $detail_key] = $spot_detail['spot_start_month'];
				$sql_params_detail[':spot_end_month_' . $detail_key] = $spot_detail['spot_end_month'];
			}
			
			if(count($sql_values_detail)) {
				$sql_detail_insert = "INSERT INTO e_spot_detail(spot_id, spot_detail_id, spot_detail_name, spot_description_text, " 
							. "image_list, two_year_flag, spot_start_month, spot_end_month) VALUES" . implode(",", $sql_values_detail);
				$query_detail_insert = DB::query($sql_detail_insert);
				$query_detail_insert->param('spot_id', $params['spot_id']);
				foreach($sql_params_detail as $param_key => $param_value) {
					$query_detail_insert->param($param_key, $param_value);
				}
				$result_detail_insert = $query_detail_insert->execute();
			}
			
			//删除原有特别价格
			$sql_price_delete = "DELETE FROM e_spot_special_price WHERE spot_id=:spot_id";
			$query_price_delete = DB::query($sql_price_delete);
			$query_price_delete->param('spot_id', $params['spot_id']);
			$result_price_delete = $query_price_delete->execute();
			
			//更新特别价格
			$sql_values_price = array();
			$sql_params_price = array();
			foreach($params['special_price_list'] as $special_price_id => $special_price) {
				$sql_values_price[] = "(:spot_id, :special_price_id_" . $special_price_id . ", :special_price_name_" . $special_price_id . ", :special_price_" . $special_price_id . ")";
				
				$sql_params_price['special_price_id_' . $special_price_id] = $special_price_id;
				$sql_params_price['special_price_name_' . $special_price_id] = $special_price['special_price_name'];
				$sql_params_price['special_price_' . $special_price_id] = $special_price['special_price'];
			}
			
			if(count($sql_values_price)) {
				$sql_price_insert = "INSERT INTO e_spot_special_price(spot_id, special_price_id, special_price_name, special_price) VALUES" . implode(",", $sql_values_price);
				$query_price_insert = DB::query($sql_price_insert);
				$query_price_insert->param('spot_id', $params['spot_id']);
				foreach($sql_params_price as $param_key => $param_value) {
					$query_price_insert->param($param_key, $param_value);
				}
				$result_price_insert = $query_price_insert->execute();
			}
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新景点状态
	 */
	public static function UpdateSpotStatus($params) {
		try {
			$sql = "UPDATE t_spot SET spot_status = :spot_status WHERE spot_id = :spot_id";
			$query = DB::query($sql);
			$query->param('spot_id', $params['spot_id']);
			$query->param('spot_status', $params['spot_status']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	/*
	 * 按条件获得景点列表
	 */
	public static function SelectSpotList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "created_at";
			$sql_order_method = "desc";
			$sql_limit = "";
			$sql_offset = "";
			
			//检索条件处理
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'spot_id_list':
						if(count($param_value)) {
							$sql_where[] = " ts.spot_id IN :spot_id_list ";
							$sql_params['spot_id_list'] = $param_value;
						}
						break;
					case 'spot_name':
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $name_key => $name) {
								$sql_sub_where[] = "ts.spot_name LIKE :spot_name_" . $name_key;
								$sql_params['spot_name_' . $name_key] = '%' . $name . '%';
							}
							$sql_where[] = " (" . implode(" OR ", $sql_sub_where) . ") ";
						}
						break;
					case 'spot_status':
						if(count($param_value)) {
							$sql_where[] = " ts.spot_status IN :spot_status_list ";
							$sql_params['spot_status_list'] = $param_value;
						}
						break;
					case 'spot_area':
						if(count($param_value)) {
							$sql_where[] = " ts.spot_area IN :spot_area_list ";
							$sql_params['spot_area_list'] = $param_value;
						}
						break;
					case 'spot_type':
						if(count($param_value)) {
							$sql_where[] = " ts.spot_type IN :spot_type_list ";
							$sql_params['spot_type_list'] = $param_value;
						}
						break;
					case 'free_flag':
						if(count($param_value)) {
							$sql_sub_where = array();
							$sql_sub_where_price = array();
							if(in_array('1', $param_value)) {
								$sql_sub_where[] = " ts.free_flag = 1 ";
							}
							if(in_array('0', $param_value)) {
								if(isset($params['price_min'])) {
									if(is_numeric($params['price_min'])) {
										$sql_sub_where_price[] = "ts.spot_price >= :price_min";
										$sql_params['price_min'] = floatval($params['price_min']);
									}
								}
								if(isset($params['price_max'])) {
									if(is_numeric($params['price_max'])) {
										$sql_sub_where_price[] = "ts.spot_price <= :price_max";
										$sql_params['price_max'] = floatval($params['price_max']);
									}
								}
								$sql_sub_where[] = "(ts.free_flag=0" . (count($sql_sub_where_price) ? (" AND " . implode(" AND ", $sql_sub_where_price)) : "") . ")";
							}
							$sql_where[] = " (" . implode(" OR ", $sql_sub_where) . ") ";
						}
						break;
					case 'created_by':
						$sql_where[] = " ts.created_by = :created_by ";
						$sql_params['created_by'] = $param_value;
						break;
					case 'active_only':
						$sql_where[] = " ts.delete_flag = 0 ";
						break;
					case 'sort_column':
						$sort_column_list = array('spot_name', 'spot_area', 'spot_type', 'spot_status', 'spot_price', 'created_at', 'modified_at');
						if(in_array($param_value, $sort_column_list)) {
							$sql_order_column = $param_value;
						}
						break;
					case 'sort_method':
						if(in_array($param_value, array('asc', 'desc'))) {
							$sql_order_method = $param_value;
						}
						break;
					default:
						break;
				}
			}
			
			if(isset($params['num_per_page']) && isset($params['page'])) {
				$sql_limit = intval($params['num_per_page']);
				$sql_offset = (intval($params['page']) - 1) * $sql_limit;
				$sql_limit = " LIMIT " . $sql_limit;
				$sql_offset = " OFFSET " . $sql_offset;
			}
			
			//符合条件的景点总数获取
			$sql_count = "SELECT COUNT(DISTINCT ts.spot_id) spot_count "
						. "FROM t_spot ts "
						. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_count = DB::query($sql_count);
			foreach($sql_params as $param_key => $param_value) {
				$query_count->param($param_key, $param_value);
			}
			$result_count = $query_count->execute()->as_array();
			
			if(count($result_count)) {
				$spot_count = intval($result_count[0]['spot_count']);
				
				if($spot_count) {
					//景点信息获取
					$sql_spot = "SELECT ts.*, ma.area_name spot_area_name, mst.spot_type_name " 
							. "FROM t_spot ts " 
							. "LEFT JOIN m_area ma ON ts.spot_area = ma.area_id "
							. "LEFT JOIN m_spot_type mst ON ts.spot_type = mst.spot_type_id "
							. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
							. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
							. $sql_limit . $sql_offset;
					$query_spot = DB::query($sql_spot);
					foreach($sql_params as $param_key => $param_value) {
						$query_spot->param($param_key, $param_value);
					}
					$result_spot = $query_spot->execute()->as_array();
					
					if(count($result_spot)) {
						$spot_list = array();
						$spot_id_list = array();
						foreach($result_spot as $spot) {
							$spot_list[$spot['spot_id']] = $spot;
							$spot_list[$spot['spot_id']]['special_price_list'] = array();
							$spot_list[$spot['spot_id']]['spot_detail_list'] = array();
							$spot_id_list[] = intval($spot['spot_id']);
						}
						
						//特殊价格信息获取
						if(isset($params['price_flag'])) {
							$sql_price = "SELECT * FROM e_spot_special_price WHERE spot_id IN :spot_id_list ORDER BY spot_id ASC, special_price_id ASC";
							$query_price = DB::query($sql_price);
							$query_price->param('spot_id_list', $spot_id_list);
							$result_price = $query_price->execute()->as_array();
							
							if(count($result_price)) {
								foreach($result_price as $special_price) {
									$spot_list[$special_price['spot_id']]['special_price_list'][] = $special_price;
								}
							}
						}
						
						//景点详情信息获取
						if(isset($params['detail_flag'])) {
							$sql_detail = "SELECT * FROM e_spot_detail WHERE spot_id IN :spot_id_list ORDER BY spot_id ASC, spot_detail_id ASC";
							$query_detail = DB::query($sql_detail);
							$query_detail->param('spot_id_list', $spot_id_list);
							$result_detail = $query_detail->execute()->as_array();
							
							if(count($result_detail)) {
								foreach($result_detail as $spot_detail) {
									$spot_list[$spot_detail['spot_id']]['spot_detail_list'][] = $spot_detail;
								}
							}
						}
						
						//返回值整理
						$result = array(
							'spot_count' => $spot_count,
							'spot_list' => $spot_list,
							'start_number' => $sql_offset + 1,
							'end_number' => count($result_spot) + $sql_offset,
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
	 * 按条件获得景点简易列表
	 */
	public static function SelectSpotSimpleList($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "created_at";
			$sql_order_method = "desc";
			
			//检索条件处理
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'spot_id_list':
						if(count($param_value)) {
							$sql_where[] = " ts.spot_id IN :spot_id_list ";
							$sql_params['spot_id_list'] = $param_value;
						}
						break;
					case 'spot_status':
						if(count($param_value)) {
							$sql_where[] = " ts.spot_status IN :spot_status_list ";
							$sql_params['spot_status_list'] = $param_value;
						}
						break;
					case 'active_only':
						$sql_where[] = " ts.delete_flag = 0 ";
						break;
					default:
						break;
				}
			}
			
			//符合条件的景点简易列表获取
			$sql = "SELECT ts.spot_id, ts.spot_name "
						. "FROM t_spot ts "
						. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
						. "ORDER BY " . $sql_order_column . " " . $sql_order_method;
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
	 * 获取特定单个景点信息
	 */
	public static function SelectSpot($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//景点ID限定
			if(isset($params['spot_id'])) {
				$sql_where[] = " ts.spot_id = :spot_id ";
				$sql_params['spot_id'] = $params['spot_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " ts.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql_spot = "SELECT ts.*, ma.area_name spot_area_name, ma.area_description spot_area_description, mst.spot_type_name, tuc.user_name created_name, tum.user_name modified_name " 
					. "FROM t_spot ts " 
					. "LEFT JOIN m_area ma ON ts.spot_area = ma.area_id " 
					. "LEFT JOIN m_spot_type mst ON ts.spot_type = mst.spot_type_id " 
					. "LEFT JOIN t_user tuc ON ts.created_by = tuc.user_id " 
					. "LEFT JOIN t_user tum ON ts.modified_by = tum.user_id " 
					. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_spot = DB::query($sql_spot);
			foreach($sql_params as $param_key => $param_value) {
				$query_spot->param($param_key, $param_value);
			}
			$result_spot = $query_spot->execute()->as_array();
			
			if(count($result_spot) == 1) {
				$result = $result_spot[0];
				
				//获取景点详情信息
				$result['spot_detail_list'] = array();
				
				$sql_where_detail = array();
				$sql_params_detail = array();
				//景点ID限定
				if(isset($params['spot_id'])) {
					$sql_where_detail[] = " esd.spot_id = :spot_id ";
					$sql_params_detail['spot_id'] = $params['spot_id'];
				}
				//数据获取
				$sql_detail = "SELECT esd.* "
									. "FROM e_spot_detail esd "
									. (count($sql_where_detail) ? (" WHERE " . implode(" AND ", $sql_where_detail)) : "")
									. " ORDER BY esd.spot_detail_id ASC ";
				$query_detail = DB::query($sql_detail);
				foreach($sql_params_detail as $param_key => $param_value) {
					$query_detail->param($param_key, $param_value);
				}
				$result_detail = $query_detail->execute()->as_array();
				if(count($result_detail)) {
					foreach($result_detail as $detail_info) {
						$result['spot_detail_list'][] = array(
							'spot_detail_id' => $detail_info['spot_detail_id'],
							'spot_detail_name' => $detail_info['spot_detail_name'],
							'spot_description_text' => $detail_info['spot_description_text'],
							'image_list' => $detail_info['image_list'] ? explode(',', $detail_info['image_list']) : array(),
							'two_year_flag' => $detail_info['two_year_flag'],
							'spot_start_month' => $detail_info['spot_start_month'],
							'spot_end_month' => $detail_info['spot_end_month'],
						);
					}
				}
				
				//获取景点特别价格信息
				$result['special_price_list'] = array();
				$sql_where_price = array();
				$sql_params_price = array();
				//景点ID限定
				if(isset($params['spot_id'])) {
					$sql_where_price[] = " essp.spot_id = :spot_id ";
					$sql_params_price['spot_id'] = $params['spot_id'];
				}
				//数据获取
				$sql_price = "SELECT essp.* "
									. "FROM e_spot_special_price essp "
									. (count($sql_where_price) ? (" WHERE " . implode(" AND ", $sql_where_price)) : "")
									. " ORDER BY essp.special_price_id ASC ";
				$query_price = DB::query($sql_price);
				foreach($sql_params_price as $param_key => $param_value) {
					$query_price->param($param_key, $param_value);
				}
				$result_price = $query_price->execute()->as_array();
				if(count($result_price)) {
					foreach($result_price as $price_info) {
						$result['special_price_list'][] = array(
							'special_price_name' => $price_info['special_price_name'],
							'special_price' => $price_info['special_price'],
						);
					}
				}
				
				return $result;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 编辑景点前编辑信息查验
	 */
	public static function CheckEditSpot($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//景点名称
		if(empty($params['spot_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_spot_name';
		} elseif(mb_strlen($params['spot_name']) > 100) {
			$result['result'] = false;
			$result['error'][] = 'long_spot_name';
		} elseif(Model_Spot::CheckSpotNameDuplication($params['spot_id'], $params['spot_name'])) {
			$result['result'] = false;
			$result['error'][] = 'dup_spot_name';
		}
		
		//景点区域
		if(empty($params['spot_area'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_spot_area';
		} elseif(!is_numeric($params['spot_area']) || !is_int($params['spot_area'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_spot_area';
		} elseif(!Model_Area::CheckAreaIdExist($params['spot_area'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_spot_area';
		}
		
		//景点类型
		if(empty($params['spot_type'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_spot_type';
		} elseif(!is_numeric($params['spot_type']) || !is_int($params['spot_type'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_spot_type';
		} elseif(!Model_Spottype::CheckSpotTypeIdExist($params['spot_type'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'error_spot_type';
		}
		
		//收费/免费FLAG
		if(!in_array($params['free_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_free_flag';
		} elseif($params['free_flag'] == '0') {
			//价格
			if(empty($params['spot_price'])) {
				$result['result'] = false;
				$result['error'][] = 'empty_spot_price';
			} elseif(!is_numeric($params['spot_price']) || !is_int($params['spot_price'] + 0)) {
				$result['result'] = false;
				$result['error'][] = 'noint_spot_price';
			} elseif(intval($params['spot_price']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_spot_price';
			}
			//特别价格
			if(count($params['special_price_list'])) {
				foreach($params['special_price_list'] as $special_price) {
					//价格条件
					if(empty($special_price['special_price_name'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_special_price_name';
					} elseif(mb_strlen($special_price['special_price_name']) > 50) {
						$result['result'] = false;
						$result['error'][] = 'long_special_price_name';
					}
					//价格
					if(!is_numeric($special_price['special_price']) || !is_int($special_price['special_price'] + 0)) {
						$result['result'] = false;
						$result['error'][] = 'noint_special_price';
					} elseif(intval($special_price['special_price']) < 0) {
						$result['result'] = false;
						$result['error'][] = 'minus_special_price';
					}
				}
			}
		}
		
		//公开状态
		if(!in_array($params['spot_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_spot_status';
		}
		
		//景点详情
		if(!count($params['spot_detail_list'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_spot_detail';
		} else {
			foreach($params['spot_detail_list'] as $spot_detail) {
				//景点详情名称
				if(empty($spot_detail['spot_detail_name'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_spot_detail_name';
				} elseif(mb_strlen($spot_detail['spot_detail_name']) > 100) {
					$result['result'] = false;
					$result['error'][] = 'long_spot_detail_name';
				}
				//景点介绍
				if(empty($spot_detail['spot_description_text'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_spot_description_text';
				}
				//景点公开期
				if(!is_numeric($spot_detail['spot_start_month']) || !is_int($spot_detail['spot_start_month'] + 0) 
					|| !is_numeric($spot_detail['spot_end_month']) || !is_int($spot_detail['spot_end_month'] + 0)) {
					$result['result'] = false;
					$result['error'][] = 'noint_spot_se_month';
				} else {
					$spot_start_month = intval($spot_detail['spot_start_month']);
					$spot_end_month = intval($spot_detail['spot_end_month']);
					if($spot_start_month < 1 || $spot_start_month > 12 || $spot_end_month < 1 || $spot_end_month > 12) {
						$result['result'] = false;
						$result['error'][] = 'noexist_spot_se_month';
					} else {
						if($spot_detail['two_year_flag']) {
							//跨年情况下
							if($spot_start_month <= $spot_end_month) {
								$result['result'] = false;
								$result['error'][] = 'overyear_spot_se_month';
							}
						} else {
							//不跨年情况下
							if($spot_start_month > $spot_end_month) {
								$result['result'] = false;
								$result['error'][] = 'minus_spot_se_month';
							}
						}
					}
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除景点前删除信息查验
	 */
	public static function CheckDeleteSpot($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_array($params['spot_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_spot_id';
		} elseif(!count($params['spot_id_list'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_spot_id';
		} else {
			$all_num_flag = true;
			
			foreach($params['spot_id_list'] as $spot_id) {
				if(!is_numeric($spot_id)) {
					$result['result'] = false;
					$all_num_flag = false;
					$result['error'][] = 'nonum_spot_id';
					break;
				}
			}
			
			if($all_num_flag) {
				$params_select = array('spot_id_list' => $params['spot_id_list']);
				$result_select = Model_Spot::SelectSpotList($params_select);
				
				if($result_select['spot_count'] != count(array_unique($params['spot_id_list']))) {
					$result['result'] = false;
					$result['error'][] = 'error_spot_id';
				} elseif($params['self_only']) {
					foreach($result_select['spot_list'] as $spot_select) {
						if($spot_select['delete_by'] != $spot_select) {
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
	 * 更新景点公开状态前更新信息查验
	 */
	public static function CheckUpdateSpotStatus($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!in_array($params['spot_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_spot_status';
		}
		
		return $result;
	}
	
	/*
	 * 景点名称重复查验
	 */
	public static function CheckSpotNameDuplication($spot_id, $spot_name) {
		try {
			//数据获取
			$sql = "SELECT spot_id FROM t_spot WHERE spot_name = :spot_name AND delete_flag = 0" . ($spot_id ? " AND spot_id != :spot_id " : "");
			$query = DB::query($sql);
			if($spot_id) {
				$query->param('spot_id', $spot_id);
			}
			$query->param('spot_name', $spot_name);
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
	 * 批量导入景点用模板Excel更新
	 */
	public static function ModifySpotModelExcel() {
		try {
			//修改批量导入景点用模板Excel
			//Excel处理用组件
			include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel.php');
			include_once(APPPATH . 'modules/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');
			
			//读取模板
			$xls = PHPExcel_IOFactory::load(DOCROOT . '/assets/xls/model/import_spot_model.xls');
			$sheet_spot = $xls->getSheetByName('景点');
			$sheet_detail = $xls->getSheetByName('景点详情');
			
			//景点类别名列表
			$configs = array();
			$spot_type_list = Model_SpotType::SelectSpotTypeList(array('active_only' => 1));
			foreach($spot_type_list as $spot_type) {
				$configs[] = $spot_type['spot_type_name'];
			}
			
			for($row_counter = 3; $row_counter < 101; $row_counter++) {
				//编辑景点-景点地区下拉列表
				$validation_area = $sheet_spot->getCell('B' . $row_counter)->getDataValidation();
				$validation_area->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$validation_area->setAllowBlank(true);
				$validation_area->setShowDropDown(true);
				$validation_area->setFormula1('"北海道地方,東北地方,関東地方,中部地方,近畿地方,中国地方,四国地方,九州地方"');
				
				//编辑景点-景点类别下拉列表
				$validation_type = $sheet_spot->getCell('C' . $row_counter)->getDataValidation();
				$validation_type->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$validation_type->setAllowBlank(true);
				$validation_type->setShowDropDown(true);
				$validation_type->setFormula1('"' . implode(',', $configs) . '"');
				
				//编辑景点-收/免费下拉列表
				$validation_free = $sheet_spot->getCell('D' . $row_counter)->getDataValidation();
				$validation_free->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$validation_free->setAllowBlank(true);
				$validation_free->setShowDropDown(true);
				$validation_free->setFormula1('"收费,免费"');
				
				//编辑景点详情-开始下拉列表
				$validation_free = $sheet_detail->getCell('D' . $row_counter)->getDataValidation();
				$validation_free->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$validation_free->setAllowBlank(true);
				$validation_free->setShowDropDown(true);
				$validation_free->setFormula1('"1月,2月,3月,4月,5月,6月,7月,8月,9月,10月,11月,12月"');
				
				//编辑景点详情-结束下拉列表
				$validation_free = $sheet_detail->getCell('E' . $row_counter)->getDataValidation();
				$validation_free->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$validation_free->setAllowBlank(true);
				$validation_free->setShowDropDown(true);
				$validation_free->setFormula1('"1月,2月,3月,4月,5月,6月,7月,8月,9月,10月,11月,12月,次年1月,次年2月,次年3月,次年4月,次年5月,次年6月,次年7月,次年8月,次年9月,次年10月,次年11月,次年12月"');
			}
			
			//更新文件
			$writer = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			$writer->save(DOCROOT . '/assets/xls/model/import_spot_model.xls');
			
			//释放缓存
			$xls->disconnectWorksheets();
			unset($writer);
			unset($sheet_spot);
			unset($sheet_detail);
			unset($xls);
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	 * 检查一组景点ID是否全部有效
	 */
	public static function CheckActiveSpotIdList($spot_id_list) {
		$result = false;
		
		if(is_array($spot_id_list)) {
			if(!count($spot_id_list)) {
				$result = true;
			} else {
				foreach($spot_id_list as $spot_id) {
					if(!is_numeric($spot_id)) {
						return false;
					}
				}
				
				$sql_where_list = array();
				$sql_param_list = array();
				foreach($spot_id_list as $spot_id_counter => $spot_id) {
					$sql_where_list[] = ':spot_id_' . $spot_id_counter;
					$sql_param_list[':spot_id_' . $spot_id_counter] = $spot_id;
				}
				$sql_where = implode(', ', $sql_where_list);
				$sql = "SELECT * FROM t_spot WHERE spot_id IN (" . $sql_where . ")";
				$query = DB::query($sql);
				foreach($sql_param_list as $key => $value) {
					$query->param($key, $value);
				}
				$result = $query->execute()->as_array();
				
				if(count($result) == count($spot_id_list)) {
					$result = true;
				}
			}
		}
		
		return $result;
	}

}

