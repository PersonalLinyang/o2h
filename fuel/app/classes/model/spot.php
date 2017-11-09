<?php

class Model_Spot extends Model
{
	/*
	 * 添加景点
	 */
	public static function InsertSpot($params) {
		//添加景点
		$sql_insert_spot = "INSERT INTO t_spot(spot_name, spot_area, spot_type, free_flag, price) "
						. "VALUES(:spot_name, :spot_area, :spot_type, :free_flag, :price)";
		$query_insert_spot = DB::query($sql_insert_spot);
		$query_insert_spot->param(':spot_name', $params['spot_name']);
		$query_insert_spot->param(':spot_area', $params['spot_area']);
		$query_insert_spot->param(':spot_type', $params['spot_type']);
		$query_insert_spot->param(':free_flag', $params['free_flag']);
		$query_insert_spot->param(':price', $params['price']);
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
	 * 按条件获得景点列表
	 */
	public static function SelectSpotList($param) {
		$sql = "SELECT ts.spot_id, ts.spot_name, ts.spot_area spot_area_id, ma.area_name spot_area_name, ts.spot_type spot_type_id, mst.spot_type_name, " 
				. "ts.free_flag, ts.price, ts.created_at, ts.modified_at, COUNT(tsd.spot_detail_id) detail_number " 
				. "FROM t_spot ts " 
				. "LEFT JOIN m_area ma ON ts.spot_area = ma.area_id "
				. "LEFT JOIN m_spot_type mst ON ts.spot_type = mst.spot_type_id "
				. "LEFT JOIN t_spot_detail tsd ON ts.spot_id = tsd.spot_id "
				. "GROUP BY spot_id, spot_name, spot_area_id, spot_area_name, spot_type_id, spot_type_name, " 
				. "free_flag, price, created_at "
				. "ORDER BY spot_id DESC ";
		$query = DB::query($sql);
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return $result;
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
				if(!is_numeric($params['price'])) {
					$result['result'] = false;
					$result['error'][] = 'nonum_price';
				} elseif($params['price'] < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_price';
				}
			}
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
				//景点公开区
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

}

