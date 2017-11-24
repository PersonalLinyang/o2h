<?php

class Model_Spot extends Model
{
	/*
	 * 添加景点
	 */
	public static function InsertSpot($params) {
		//添加景点
		$sql_insert_spot = "INSERT INTO t_spot(spot_name, spot_area, spot_type, free_flag, spot_price, spot_status, created_at, modified_at) "
						. "VALUES(:spot_name, :spot_area, :spot_type, :free_flag, :spot_price, :spot_status, now(), now())";
		$query_insert_spot = DB::query($sql_insert_spot);
		$query_insert_spot->param(':spot_name', $params['spot_name']);
		$query_insert_spot->param(':spot_area', $params['spot_area']);
		$query_insert_spot->param(':spot_type', $params['spot_type']);
		$query_insert_spot->param(':free_flag', $params['free_flag']);
		$query_insert_spot->param(':spot_price', $params['spot_price']);
		$query_insert_spot->param(':spot_status', $params['spot_status']);
		$result_insert_spot = $query_insert_spot->execute();
		
		if($result_insert_spot) {
			//添加景点详情
			$spot_id = intval($result_insert_spot[0]);
			foreach($params['detail_list'] as $detail) {
				$sql_insert_detail = "INSERT INTO t_spot_detail(spot_id, spot_sort_id, spot_detail_name, spot_description_text, " 
									. "image_list, two_year_flag, spot_start_month, spot_end_month) "
									. "VALUES(:spot_id, :spot_sort_id, :spot_detail_name, :spot_description_text, " 
									. ":image_list, :two_year_flag, :spot_start_month, :spot_end_month)";
				$query_insert_detail = DB::query($sql_insert_detail);
				$query_insert_detail->param(':spot_id', $spot_id);
				$query_insert_detail->param(':spot_sort_id', $detail['spot_sort_id']);
				$query_insert_detail->param(':spot_detail_name', $detail['spot_detail_name']);
				$query_insert_detail->param(':spot_description_text', $detail['spot_description_text']);
				$image_list = array();
				for($i = 0; $i < $detail['image_number']; $i++) {
					$image_list[] = $i;
				}
				$query_insert_detail->param(':image_list', implode(',', $image_list));
				$query_insert_detail->param(':two_year_flag', $detail['two_year_flag']);
				$query_insert_detail->param(':spot_start_month', $detail['spot_start_month']);
				$query_insert_detail->param(':spot_end_month', $detail['spot_end_month']);
				$result_insert_detail = $query_insert_detail->execute();
			}
		}
		
		return $result_insert_spot;
	}
	
	/*
	 * 更新景点
	 */
	public static function UpdateSpot($params) {
		//更新景点
		$sql_update_spot = "UPDATE t_spot SET spot_name=:spot_name, spot_area=:spot_area, spot_type=:spot_type, free_flag=:free_flag, "
						. "spot_price=:spot_price, spot_status=:spot_status, modified_at=now() WHERE spot_id=:spot_id";
		$query_update_spot = DB::query($sql_update_spot);
		$query_update_spot->param(':spot_id', $params['spot_id']);
		$query_update_spot->param(':spot_name', $params['spot_name']);
		$query_update_spot->param(':spot_area', $params['spot_area']);
		$query_update_spot->param(':spot_type', $params['spot_type']);
		$query_update_spot->param(':free_flag', $params['free_flag']);
		$query_update_spot->param(':spot_price', $params['spot_price']);
		$query_update_spot->param(':spot_status', $params['spot_status']);
		$result_update_spot = $query_update_spot->execute();
		
		if($result_update_spot) {
			//删除原有景点详情
			$sql_delete_detail = "DELETE FROM t_spot_detail WHERE spot_id=:spot_id";
			$query_delete_detail = DB::query($sql_delete_detail);
			$query_delete_detail->param(':spot_id', $params['spot_id']);
			$result_delete_detail = $query_delete_detail->execute();
			
			//更新景点详情
			foreach($params['detail_list'] as $detail) {
				$sql_update_detail = "INSERT INTO t_spot_detail(spot_id, spot_sort_id, spot_detail_name, spot_description_text, " 
									. "image_list, two_year_flag, spot_start_month, spot_end_month) "
									. "VALUES(:spot_id, :spot_sort_id, :spot_detail_name, :spot_description_text, " 
									. ":image_list, :two_year_flag, :spot_start_month, :spot_end_month)";
				$query_update_detail = DB::query($sql_update_detail);
				$query_update_detail->param(':spot_id', $params['spot_id']);
				$query_update_detail->param(':spot_sort_id', $detail['spot_sort_id']);
				$query_update_detail->param(':spot_detail_name', $detail['spot_detail_name']);
				$query_update_detail->param(':spot_description_text', $detail['spot_description_text']);
				$image_list = $detail['image_sort'];
				for($i = 1; $i <= $detail['image_number_upload']; $i++) {
					$image_list[] = $i + intval($detail['max_image_sort']);
				}
				$query_update_detail->param(':image_list', implode(',', $image_list));
				$query_update_detail->param(':two_year_flag', $detail['two_year_flag']);
				$query_update_detail->param(':spot_start_month', $detail['spot_start_month']);
				$query_update_detail->param(':spot_end_month', $detail['spot_end_month']);
				$result_update_detail = $query_update_detail->execute();
			}
		}
		
		return $result_update_spot;
	}
	
	/*
	 * 根据ID删除景点
	 */
	public static function DeleteSpotById($spot_id) {
		$sql_delete = "DELETE FROM t_spot WHERE spot_id = :spot_id";
		$query_delete = DB::query($sql_delete);
		$query_delete->param(':spot_id', $spot_id);
		$result_delete = $query_delete->execute();
		
		return $result_delete;
	}
	
	/*
	 * 根据ID删除景点(批量)
	 */
	public static function DeleteSpotByIdList($spot_id_list) {
		$sql_where_list = array();
		$sql_param_list = array();
		foreach($spot_id_list as $spot_id_counter => $spot_id) {
			$sql_where_list[] = ':spot_id_' . $spot_id_counter;
			$sql_param_list[':spot_id_' . $spot_id_counter] = $spot_id;
		}
		$sql_where = implode(', ', $sql_where_list);
		$sql_delete = "DELETE FROM t_spot WHERE spot_id IN (" . $sql_where . ")";
		$query_delete = DB::query($sql_delete);
		foreach($sql_param_list as $key => $value) {
			$query_delete->param($key, $value);
		}
		$result_delete = $query_delete->execute();
		
		return $result_delete;
	}
	
	/*
	 * 更新景点状态
	 */
	public static function UpdateSpotStatusById($params) {
		$sql_update = "UPDATE t_spot SET spot_status = :spot_status WHERE spot_id = :spot_id";
		$query_update = DB::query($sql_update);
		$query_update->param(':spot_id', $params['spot_id']);
		$query_update->param(':spot_status', $params['spot_status']);
		$result_update = $query_update->execute();
		
		return $result_update;
	}

	/*
	 * 按条件获得景点列表
	 */
	public static function SelectSpotList($params) {
		$sql_where = "";
		$sql_order_column = "created_at";
		$sql_order_method = "desc";
		$sql_params = array();
		$sql_offset = 0;
		$sql_limit = 20;
		foreach($params as $key => $value) {
			switch($key) {
				case 'spot_name':
					$sql_where_list_name = array();
					foreach($value as $name_counter => $name) {
						$sql_where_list_name[] = "spot_name LIKE :spot_name_" . $name_counter;
						$sql_params[':spot_name_' . $name_counter] = '%' . $name . '%';
					}
					if(count($sql_where_list_name)) {
						$sql_where .= " AND (" . implode(' OR ', $sql_where_list_name) . ") ";
					}
					break;
				case 'spot_status':
					$sql_where_list_status = array();
					foreach($value as $status_counter => $status) {
						if(is_numeric($status)) {
							$sql_where_list_status[] = ":spot_status_" . $status_counter;
							$sql_params[':spot_status_' . $status_counter] = intval($status);
						}
					}
					if(count($sql_where_list_status)) {
						$sql_where .= " AND ts.spot_status IN (" . implode(', ', $sql_where_list_status) . ") ";
					}
					break;
				case 'spot_area':
					$sql_where_list_area = array();
					foreach($value as $area_counter => $area) {
						if(is_numeric($area)) {
							$sql_where_list_area[] = ":spot_area_id_" . $area_counter;
							$sql_params[':spot_area_id_' . $area_counter] = intval($area);
						}
					}
					if(count($sql_where_list_area)) {
						$sql_where .= " AND ts.spot_area IN (" . implode(', ', $sql_where_list_area) . ") ";
					}
					break;
				case 'spot_type':
					$sql_where_list_type = array();
					foreach($value as $type_counter => $type) {
						if(is_numeric($type)) {
							$sql_where_list_type[] = ":spot_type_id_" . $type_counter;
							$sql_params[':spot_type_id_' . $type_counter] = intval($type);
						}
					}
					if(count($sql_where_list_type)) {
						$sql_where .= " AND ts.spot_type IN (" . implode(', ', $sql_where_list_type) . ") ";
					}
					break;
				case 'free_flag':
					$sql_where_list_free_flag = array();
					$sql_where_list_price = array();
					if(in_array('1', $value)) {
						$sql_where_list_free_flag[] = "free_flag = 1";
					}
					if(in_array('0', $value)) {
						if(isset($params['price_min'])) {
							if(is_numeric($params['price_min'])) {
								$sql_where_list_price[] = "spot_price >= :price_min";
								$sql_params[':price_min'] = floatval($params['price_min']);
							}
						}
						if(isset($params['price_max'])) {
							if(is_numeric($params['price_max'])) {
								$sql_where_list_price[] = "spot_price <= :price_max";
								$sql_params[':price_max'] = floatval($params['price_max']);
							}
						}
						if(count($sql_where_list_price)) {
							$sql_where_list_free_flag[] = "(free_flag = 0 AND " . implode(" AND ", $sql_where_list_price) . ")";
						} else {
							$sql_where_list_free_flag[] = "free_flag = 0";
						}
					}
					if(count($sql_where_list_free_flag)) {
						$sql_where .= " AND (" . implode(' OR ', $sql_where_list_free_flag) . ") ";
					}
					break;
				case 'sort_column':
					$sort_column_list = array('spot_name', 'spot_area_id', 'spot_type_id', 'spot_status', 'spot_price', 'created_at', 'modified_at', 'detail_number');
					if(in_array($value, $sort_column_list)) {
						$sql_order_column = $value;
					}
					break;
				case 'sort_method':
					if(in_array($value, array('asc', 'desc'))) {
						$sql_order_method = $value;
					}
					break;
				case 'page':
					if(is_numeric($value)) {
						$num_per_page = $sql_limit;
						if(isset($params['num_per_page'])) {
							if(is_numeric($params['num_per_page'])) {
								$num_per_page = intval($params['num_per_page']);
							}
						}
						$sql_offset = (intval($value) - 1) * $num_per_page;
					}
					break;
				case 'num_per_page':
					if(is_numeric($value)) {
						$sql_limit = intval($value);
					}
					break;
				case '':
					break;
			}
		}

		$sql_count = "SELECT COUNT(DISTINCT ts.spot_id) spot_count FROM t_spot ts LEFT JOIN t_spot_detail tsd ON ts.spot_id = tsd.spot_id WHERE 1=1 " . $sql_where;
		$query_count = DB::query($sql_count);
		foreach ($sql_params as $key => $value) {
			$query_count->param($key, $value);
		}
		$result_count = $query_count->execute()->as_array();

		if(count($result_count)) {
			$spot_count = intval($result_count[0]['spot_count']);

			if($spot_count) {
				$sql_select = "SELECT ts.spot_id, ts.spot_name, ts.spot_status, ts.spot_area spot_area_id, ma.area_name spot_area_name, " 
						. "ts.spot_type spot_type_id, mst.spot_type_name, ts.free_flag, ts.spot_price, ts.created_at, ts.modified_at, " 
						. "COUNT(tsd.spot_sort_id) detail_number " 
						. "FROM t_spot ts " 
						. "LEFT JOIN m_area ma ON ts.spot_area = ma.area_id "
						. "LEFT JOIN m_spot_type mst ON ts.spot_type = mst.spot_type_id "
						. "LEFT JOIN t_spot_detail tsd ON ts.spot_id = tsd.spot_id "
						. "WHERE 1=1 " . $sql_where
						. "GROUP BY spot_id, spot_name, spot_status, spot_area_id, spot_area_name, spot_type_id, spot_type_name, " 
						. "free_flag, spot_price, created_at, modified_at "
						. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
						. "LIMIT " . $sql_limit . " OFFSET " . $sql_offset;
				$query_select = DB::query($sql_select);
				foreach ($sql_params as $key => $value) {
					$query_select->param($key, $value);
				}
				$result_select = $query_select->execute()->as_array();

				if(count($result_select)) {
					$result = array(
						'spot_count' => $spot_count,
						'spot_list' => $result_select,
						'start_number' => $sql_offset + 1,
						'end_number' => count($result_select) + $sql_offset,
					);
					return $result;
				}
			}
		}

		return false;
	}
	
	/*
	 * 获得全部景点信息的简易列表
	 */
	public static function SelectSpotSimpleListAll() {
		$sql = "SELECT spot_id, spot_name FROM t_spot ORDER BY spot_id";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		return $result;
	}
	
	/*
	 * 根据ID获取景点详细信息`
	 */
	public static function SelectSpotInfoBySpotId($spot_id) {
		if(!is_numeric($spot_id)) {
			return false;
		}
		
		$sql_spot = "SELECT ts.spot_id, ts.spot_name, ts.spot_area spot_area_id, ma.area_name spot_area_name, ma.area_description spot_area_description, " 
				. "ts.spot_type spot_type_id, mst.spot_type_name, ts.free_flag, ts.spot_price, ts.spot_status, ts.created_at, ts.modified_at " 
				. "FROM t_spot ts " 
				. "LEFT JOIN m_area ma ON ts.spot_area = ma.area_id " 
				. "LEFT JOIN m_spot_type mst ON ts.spot_type = mst.spot_type_id " 
				. "WHERE ts.spot_id = :spot_id ";
		$query_spot = DB::query($sql_spot);
		$query_spot->param(':spot_id', $spot_id);
		$result_spot = $query_spot->execute()->as_array();
		
		if(count($result_spot) == 1) {
			$result = $result_spot[0];
			$result['detail_list'] = array();
			
			$sql_spot_detail = "SELECT tsd.spot_sort_id, tsd.spot_detail_name, tsd.spot_description_text, tsd.image_list, tsd.two_year_flag, tsd.spot_start_month, tsd.spot_end_month "
								. "FROM t_spot_detail tsd "
								. "WHERE tsd.spot_id = :spot_id " 
								. "ORDER BY spot_sort_id ASC ";
			$query_spot_detail = DB::query($sql_spot_detail);
			$query_spot_detail->param(':spot_id', $spot_id);
			$result_spot_detail = $query_spot_detail->execute()->as_array();
			
			if(count($result_spot_detail)) {
				foreach($result_spot_detail as $detail_info) {
					$result['detail_list'][] = array(
						'spot_sort_id' => $detail_info['spot_sort_id'],
						'spot_detail_name' => $detail_info['spot_detail_name'],
						'spot_description_text' => $detail_info['spot_description_text'],
						'image_list' => explode(',', $detail_info['image_list']),
						'two_year_flag' => $detail_info['two_year_flag'],
						'spot_start_month' => $detail_info['spot_start_month'],
						'spot_end_month' => $detail_info['spot_end_month'],
					);
				}
			}
			return $result;
		} else {
			return false;
		}
	}

	/*
	 * 获得全部景点数
	 */
	public static function GetSpotTotalCount() {
		$sql = "SELECT count(*) spot_count FROM t_spot";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		if(count($result) == 1) {
			return intval($result[0]['spot_count']);
		} else {
			return false;
		}
	}
	
	/*
	 * 添加景点前添加信息查验
	 */
	public static function CheckInsertSpot($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		//景点名称
		if(empty($params['spot_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		//景点区域
		if(!is_numeric($params['spot_area'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_area';
		}
		//景点类型
		if(!is_numeric($params['spot_type'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_type';
		}
		//收费/免费FLAG
		if(!in_array($params['free_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_freeflag';
			if($params['free_flag'] == '0') {
				if(!is_numeric($params['spot_price'])) {
					$result['result'] = false;
					$result['error'][] = 'nonum_price';
				} elseif($params['spot_price'] < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_price';
				}
			}
		}
		//公开状态
		if(!in_array($params['spot_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		//景点详情
		if(!count($params['detail_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_detail';
		} else {
			foreach($params['detail_list'] as $detail) {
				//景点详情名称
				if(empty($detail['spot_detail_name'])) {
					$result['result'] = false;
					if(!in_array('empty_detail_name', $result['error'])) {
						$result['error'][] = 'empty_detail_name';
					}
				}
				//景点介绍
				if(empty($detail['spot_description_text'])) {
					$result['result'] = false;
					if(!in_array('empty_description_text', $result['error'])) {
						$result['error'][] = 'empty_description_text';
					}
				}
				//景点图片数
				if(!$detail['image_number']) {
					$result['result'] = false;
					if(!in_array('zero_image', $result['error'])) {
						$result['error'][] = 'zero_image';
					}
				}
				//景点公开期
				if(is_numeric($detail['spot_start_month']) && is_numeric($detail['spot_end_month'])) {
					$spot_start_month = intval($detail['spot_start_month']);
					$spot_end_month = intval($detail['spot_end_month']);
					if($spot_start_month < 1 || $spot_start_month > 12 || $spot_end_month < 1 || $spot_end_month > 12) {
						$result['result'] = false;
						if(!in_array('noexist_se_time', $result['error'])) {
							$result['error'][] = 'noexist_se_time';
						}
					} else {
						if($detail['two_year_flag']) {
							//跨年情况下
							if($spot_start_month <= $spot_end_month) {
								$result['result'] = false;
								if(!in_array('overyear_se_time', $result['error'])) {
									$result['error'][] = 'overyear_se_time';
								}
							}
						} else {
							//不跨年情况下
							if($spot_start_month > $spot_end_month) {
								$result['result'] = false;
								if(!in_array('overyear_se_time', $result['error'])) {
									$result['error'][] = 'minus_se_time';
								}
							}
						}
					}
				} else {
					$result['result'] = false;
					$result['error'][] = 'nonum_se_time';
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除景点前删除ID查验
	 */
	public static function CheckDeleteSpotById($spot_id) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_numeric($spot_id)) {
			$result['result'] = false;
			$result['error'][] = 'nonum_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM t_spot WHERE spot_id = :spot_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':spot_id', $spot_id);
			$result_exist = $query_exist->execute()->as_array();
			
			if(!count($result_exist)) {
				$result['result'] = false;
				$result['error'][] = 'noexist';
			}
		}
		
		return $result;
	}
	
	/*
	 * 删除景点前删除ID查验(批量)
	 */
	public static function CheckDeleteSpotByIdList($spot_id_list) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!is_array($spot_id_list)) {
			$result['result'] = false;
			$result['error'][] = 'noarray_spot_id';
		} elseif(!count($spot_id_list)) {
			$result['result'] = false;
			$result['error'][] = 'empty_spot_id';
		} else {
			foreach($spot_id_list as $spot_id) {
				if(!is_numeric($spot_id)) {
					$result['result'] = false;
					$result['error'][] = 'nonum_spot_id';
					break;
				}
			}
			
			if($result['result']) {
				$sql_where_list = array();
				$sql_param_list = array();
				foreach($spot_id_list as $spot_id_counter => $spot_id) {
					$sql_where_list[] = ':spot_id_' . $spot_id_counter;
					$sql_param_list[':spot_id_' . $spot_id_counter] = $spot_id;
				}
				$sql_where = implode(', ', $sql_where_list);
				$sql_exist = "SELECT * FROM t_spot WHERE spot_id IN (" . $sql_where . ")";
				$query_exist = DB::query($sql_exist);
				foreach($sql_param_list as $key => $value) {
					$query_exist->param($key, $value);
				}
				$result_exist = $query_exist->execute()->as_array();
				
				if(count($result_exist) != count($spot_id_list)) {
					$result['result'] = false;
					$result['error'][] = 'noexist_spot_id';
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 修改景点前修改信息查验
	 */
	public static function CheckUpdateSpot($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		//景点名称
		if(empty($params['spot_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_name';
		}
		//景点区域
		if(!is_numeric($params['spot_area'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_area';
		}
		//景点类型
		if(!is_numeric($params['spot_type'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_type';
		}
		//收费/免费FLAG
		if(!in_array($params['free_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_freeflag';
			if($params['free_flag'] == '0') {
				if(!is_numeric($params['spot_price'])) {
					$result['result'] = false;
					$result['error'][] = 'nonum_price';
				} elseif($params['spot_price'] < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_price';
				}
			}
		}
		//公开状态
		if(!in_array($params['spot_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_status';
		}
		//景点详情
		if(!count($params['detail_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_detail';
		} else {
			foreach($params['detail_list'] as $detail) {
				//景点详情名称
				if(empty($detail['spot_detail_name'])) {
					$result['result'] = false;
					if(!in_array('empty_detail_name', $result['error'])) {
						$result['error'][] = 'empty_detail_name';
					}
				}
				//景点介绍
				if(empty($detail['spot_description_text'])) {
					$result['result'] = false;
					if(!in_array('empty_description_text', $result['error'])) {
						$result['error'][] = 'empty_description_text';
					}
				}
				//景点图片数
				if(!($detail['image_number_upload'] + count($detail['image_sort']))) {
					$result['result'] = false;
					if(!in_array('zero_image', $result['error'])) {
						$result['error'][] = 'zero_image';
					}
				}
				//已上传图片
				foreach($detail['image_sort'] as $image_sort) {
					if(!is_numeric($image_sort)) {
						$result['result'] = false;
						if(!in_array('nonum_imagesort', $result['error'])) {
							$result['error'][] = 'nonum_imagesort';
						}
						break;
					}
				}
				//景点公开期
				if(is_numeric($detail['spot_start_month']) && is_numeric($detail['spot_end_month'])) {
					$spot_start_month = intval($detail['spot_start_month']);
					$spot_end_month = intval($detail['spot_end_month']);
					if($spot_start_month < 1 || $spot_start_month > 12 || $spot_end_month < 1 || $spot_end_month > 12) {
						$result['result'] = false;
						if(!in_array('noexist_se_time', $result['error'])) {
							$result['error'][] = 'noexist_se_time';
						}
					} else {
						if($detail['two_year_flag']) {
							//跨年情况下
							if($spot_start_month <= $spot_end_month) {
								$result['result'] = false;
								if(!in_array('overyear_se_time', $result['error'])) {
									$result['error'][] = 'overyear_se_time';
								}
							}
						} else {
							//不跨年情况下
							if($spot_start_month > $spot_end_month) {
								$result['result'] = false;
								if(!in_array('overyear_se_time', $result['error'])) {
									$result['error'][] = 'minus_se_time';
								}
							}
						}
					}
				} else {
					$result['result'] = false;
					if(!in_array('nonum_se_time', $result['error'])) {
						$result['error'][] = 'nonum_se_time';
					}
				}
			}
		}
		
		return $result;
	}
	
	/*
	 * 更新景点公开状态前更新信息查验
	 */
	public static function CheckUpdateSpotStatusById($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		if(!in_array($params['spot_status'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_spot_status';
		}
		if(!is_numeric($params['spot_id'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_spot_id';
		}
		
		if($result['result']) {
			$sql_exist = "SELECT * FROM t_spot WHERE spot_id = :spot_id";
			$query_exist = DB::query($sql_exist);
			$query_exist->param(':spot_id', $params['spot_id']);
			$result_exist = $query_exist->execute()->as_array();
			
			if(count($result_exist) != 1) {
				$result['result'] = false;
				$result['error'][] = 'noexist_spot_id';
			}
		}
		
		return $result;
	}

}

