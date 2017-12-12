<?php

class Model_Customer extends Model
{

	/*
	 * 添加顾客
	 */
	 public static function InsertCustomer($params) {
	 	$sql_customer_columns = '';
	 	$sql_customer_values = '';
	 	$sql_customer_param_list = array();
		$sql_customer_spot_param_list = array();
		$sql_customer_spot_value_list = array();
		$sql_hotel_reserve_param_list = array();
		$sql_hotel_reserve_value_list = array();
		$sql_customer_cost_param_list = array();
		$sql_customer_cost_value_list = array();
	 	
	 	//添加数据整理
	 	foreach($params as $param_key => $param_value) {
	 		switch($param_key) {
	 			case 'spot_hope_list':
					foreach($param_value as $key => $value) {
						$sql_customer_spot_param_list[':spot_id_' . $key] = $value;
						$sql_customer_spot_value_list[] = "(:customer_id, :spot_id_" . $key . ")";
					}
	 				break;
	 			case 'hotel_reserve_list':
	 				foreach($param_value as $key => $value) {
	 					$sql_hotel_reserve_param_list[':row_id_' . $key] = $key;
	 					$sql_hotel_reserve_param_list[':hotel_type_' . $key] = $value['hotel_type'] == '' ? NULL : $value['hotel_type'];;
	 					$sql_hotel_reserve_param_list[':room_type_' . $key] = $value['room_type'] == '' ? NULL : $value['room_type'];;
	 					$sql_hotel_reserve_param_list[':people_num_' . $key] = $value['people_num'] == '' ? NULL : $value['people_num'];;
	 					$sql_hotel_reserve_param_list[':room_num_' . $key] = $value['room_num'] == '' ? NULL : $value['room_num'];;
	 					$sql_hotel_reserve_param_list[':day_num_' . $key] = $value['day_num'] == '' ? NULL : $value['day_num'];;
	 					$sql_hotel_reserve_param_list[':comment_' . $key] = $value['comment'] == '' ? NULL : $value['comment'];;
						$sql_hotel_reserve_value_list[] = "(:customer_id, :row_id_" . $key . ", :hotel_type_" . $key . ", :room_type_" . $key . ", "
														. ":people_num_" . $key . ", :room_num_" . $key . ", :day_num_" . $key . ", :comment_" . $key . ")";
	 				}
	 				break;
	 			case 'customer_cost_list':
	 				foreach($param_value as $key => $value) {
	 					$sql_customer_cost_param_list[':row_id_' . $key] = $key;
	 					$sql_customer_cost_param_list[':customer_cost_type_' . $key] = $value['customer_cost_type'] == '' ? NULL : $value['customer_cost_type'];
	 					$sql_customer_cost_param_list[':customer_cost_desc_' . $key] = $value['customer_cost_desc'] == '' ? NULL : $value['customer_cost_desc'];
	 					$sql_customer_cost_param_list[':customer_cost_day_' . $key] = $value['customer_cost_day'] == '' ? NULL : $value['customer_cost_day'];
	 					$sql_customer_cost_param_list[':customer_cost_people_' . $key] = $value['customer_cost_people'] == '' ? NULL : $value['customer_cost_people'];
	 					$sql_customer_cost_param_list[':customer_cost_each_' . $key] = $value['customer_cost_each'] == '' ? NULL : $value['customer_cost_each'];
	 					$sql_customer_cost_param_list[':customer_cost_total_' . $key] = $value['customer_cost_total'] == '' ? NULL : $value['customer_cost_total'];
						$sql_customer_cost_value_list[] = "(:customer_id, :row_id_" . $key . ", :customer_cost_type_" . $key . ", :customer_cost_desc_" . $key . ", "
														. ":customer_cost_day_" . $key . ", :customer_cost_people_" . $key . ", :customer_cost_each_" . $key . ", :customer_cost_total_" . $key . ")";
	 				}
	 				break;
	 			default:
	 				if($param_value != '') {
	 					$sql_customer_columns .= $param_key . ',';
	 					$sql_customer_values .= ':' . $param_key . ',';
	 					$sql_customer_param_list[':' . $param_key] = $param_value;
	 				}
	 				break;
	 		}
	 	}
	 	
	 	//顾客数据添加
	 	$sql_customer = "INSERT INTO t_customer(" . $sql_customer_columns . " created_at, modified_at) "
	 				. "VALUES(" . $sql_customer_values . " now(), now())";
		$query_customer = DB::query($sql_customer);
		foreach($sql_customer_param_list as $param_key => $param_value) {
			$query_customer->param($param_key, $param_value);
		}
		$result_customer = $query_customer->execute();
		
		if($result_customer) {
			$customer_id = intval($result_customer[0]);
			
	 		//希望景点添加
			if(count($sql_customer_spot_value_list)) {
				$sql_customer_spot = "INSERT INTO r_customer_spot(customer_id, spot_id) VALUES" . implode(", ", $sql_customer_spot_value_list);
				$query_customer_spot = DB::query($sql_customer_spot);
				$query_customer_spot->param(':customer_id', $customer_id);
				foreach($sql_customer_spot_param_list as $param_key => $param_value) {
					$query_customer_spot->param($param_key, $param_value);
				}
				$result_customer_spot = $query_customer_spot->execute();
			}
			
	 		//酒店预定添加
			if(count($sql_hotel_reserve_value_list)) {
				$sql_hotel_reserve = "INSERT INTO t_hotel_reserve(customer_id, row_id, hotel_type, room_type, people_num, room_num, day_num, comment) "
									. "VALUES" . implode(", ", $sql_hotel_reserve_value_list);
				$query_hotel_reserve = DB::query($sql_hotel_reserve);
				$query_hotel_reserve->param(':customer_id', $customer_id);
				foreach($sql_hotel_reserve_param_list as $param_key => $param_value) {
					$query_hotel_reserve->param($param_key, $param_value);
				}
				$result_hotel_reserve = $query_hotel_reserve->execute();
			}
			
	 		//实际成本添加
			if(count($sql_customer_cost_value_list)) {
				$sql_customer_cost = "INSERT INTO t_customer_cost(customer_id, row_id, customer_cost_type, customer_cost_desc, "
									. "customer_cost_day, customer_cost_people, customer_cost_each, customer_cost_total) "
									. "VALUES" . implode(", ", $sql_customer_cost_value_list);
				$query_customer_cost = DB::query($sql_customer_cost);
				$query_customer_cost->param(':customer_id', $customer_id);
				foreach($sql_customer_cost_param_list as $param_key => $param_value) {
					$query_customer_cost->param($param_key, $param_value);
				}
				$result_customer_cost = $query_customer_cost->execute();
			}
		}
		
		return $result_customer;
	 }

	/*
	 * 添加顾客前添加信息查验
	 */
	public static function CheckInsertCustomer($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//顾客姓名
		if(empty($params['customer_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_customer_name';
		} elseif(mb_strlen($params['customer_name']) > 50) {
			$result['result'] = false;
			$result['error'][] = 'long_customer_name';
		}
		
		//顾客状态
		if(!is_numeric($params['customer_status'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_customer_status';
		} elseif(!Model_Customerstatus::CheckExistCustomerStatusId($params['customer_status'])) {
			$result['result'] = false;
			$result['error'][] = 'noexist_customer_status';
		}
		
		//顾客性别
		if(!empty($params['customer_gender'])) {
			if(!is_numeric($params['customer_gender'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_customer_gender';
			} elseif(!in_array($params['customer_gender'], array('1', '2'))) {
				$result['result'] = false;
				$result['error'][] = 'error_customer_gender';
			}
		}
		
		//顾客年龄
		if(!empty($params['customer_age'])) {
			if(!is_numeric($params['customer_age'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_customer_age';
			} elseif(!in_array($params['customer_age'], array('1', '2', '3', '4', '5'))) {
				$result['result'] = false;
				$result['error'][] = 'error_customer_age';
			}
		}
		
		//旅游目的
		if(!empty($params['travel_reason'])) {
			if(!is_numeric($params['travel_reason'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_travel_reason';
			} elseif(!Model_Travelreason::CheckExistTravelReasonId($params['travel_reason'])) {
				$result['result'] = false;
				$result['error'][] = 'noexist_travel_reason';
			}
		}
		
		//顾客来源
		if(!is_numeric($params['customer_source'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_customer_source';
		} elseif(!Model_Customersource::CheckExistCustomerSourceId($params['customer_source'])) {
			$result['result'] = false;
			$result['error'][] = 'noexist_customer_source';
		}
		
		//会员编号
		if(!empty($params['member_id'])) {
			if(!is_numeric($params['member_id'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_member_id';
			} elseif(!Model_Member::CheckExistMemberId($params['member_id'])) {
				$result['result'] = false;
				$result['error'][] = 'noexist_member_id';
			}
		}
		
		//负责人
		if(!empty($params['staff_id'])) {
			if(!is_numeric($params['staff_id'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_staff_id';
			} elseif(!Model_User::CheckActiveUserId($params['staff_id'])) {
				$result['result'] = false;
				$result['error'][] = 'noactive_staff_id';
			}
		}
		
		//人数
		if(!empty($params['men_num'])) {
			if(!preg_match('/^(\d{1,2})?$/', $params['men_num'])) {
				$result['result'] = false;
				$result['error'][] = 'error_men_num';
			}
		}
		if(!empty($params['women_num'])) {
			if(!preg_match('/^(\d{1,2})?$/', $params['women_num'])) {
				$result['result'] = false;
				$result['error'][] = 'error_women_num';
			}
		}
		if(!empty($params['children_num'])) {
			if(!preg_match('/^(\d{1,2})?$/', $params['children_num'])) {
				$result['result'] = false;
				$result['error'][] = 'error_children_num';
			}
		}
		
		//旅行天数
		if(!empty($params['travel_days'])) {
			if(!preg_match('/^(\d{1,2})?$/', $params['travel_days'])) {
				$result['result'] = false;
				$result['error'][] = 'error_travel_days';
			}
		}
		
		//来日日期
		if(empty($params['start_at_year'])) {
			if(!empty($params['start_at_month']) || !empty($params['start_at_day'])) {
				$result['result'] = false;
				$result['error'][] = 'empty_start_at_year';
			}
		} else {
			if(!is_numeric($params['start_at_year'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_start_at_year';
			} elseif(!in_array($params['start_at_year'], array(date('Y', time()), date('Y', strtotime("+1 year"))))) {
				$result['result'] = false;
				$result['error'][] = 'error_start_at_year';
			}
		}
		if(empty($params['start_at_month'])) {
			if(!empty($params['start_at_day'])) {
				$result['result'] = false;
				$result['error'][] = 'empty_start_at_month';
			}
		} else {
			if(!is_numeric($params['start_at_month'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_start_at_month';
			} elseif(intval($params['start_at_month']) < 1 || intval($params['start_at_month']) > 12) {
				$result['result'] = false;
				$result['error'][] = 'error_start_at_month';
			}
		}
		if(!empty($params['start_at_day'])) {
			if(!is_numeric($params['start_at_day'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_start_at_day';
			} elseif(intval($params['start_at_day']) < 1 || intval($params['start_at_day']) > 31) {
				$result['result'] = false;
				$result['error'][] = 'error_start_at_day';
			} elseif(!empty($params['start_at_year']) && !empty($params['start_at_month'])) {
				if(!checkdate($params['start_at_month'], $params['start_at_day'], $params['start_at_year'])) {
					$result['result'] = false;
					$result['error'][] = 'error_start_at_date';
				}
			}
		}
		
		//基本旅游路线
		if(!empty($params['route_id'])) {
			if(!is_numeric($params['route_id'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_route_id';
			} elseif($params['route_id'] != '0') {
				if(!Model_Route::CheckActiveRouteId($params['route_id'])) {
					$result['result'] = false;
					$result['error'][] = 'noactive_route_id';
				}
			}
		}
		
		//预算
		if(!empty($params['budget_base'])) {
			if(!preg_match('/^(\d{1,6})(\.\d{1,2})?$/', $params['budget_base'])) {
				$result['result'] = false;
				$result['error'][] = 'error_budget_base';
			}
		}
		if(!empty($params['budget_total'])) {
			if(!preg_match('/^(\d{1,6})(\.\d{1,2})?$/', $params['budget_total'])) {
				$result['result'] = false;
				$result['error'][] = 'error_budget_total';
			}
		}
		
		//form flag
		if(!is_numeric($params['form_flag'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_form_flag';
		} elseif(!in_array($params['form_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_form_flag';
		}
		
		//首次利用
		if(!is_numeric($params['first_flag'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_first_flag';
		} elseif(!in_array($params['first_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_first_flag';
		}
		
		//目标景点
		if(!is_numeric($params['spot_hope_flag'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_spot_hope_flag';
		} elseif(!in_array($params['spot_hope_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_spot_hope_flag';
		}
		if(!is_array($params['spot_hope_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_spot_hope_list';
		} elseif(count($params['spot_hope_list'])) {
			if(!Model_Spot::CheckActiveSpotIdList($params['spot_hope_list'])) {
				$result['result'] = false;
				$result['error'][] = 'noactive_spot_hope_list';
			}
		}
		
		//酒店预约
		if(!is_numeric($params['hotel_reserve_flag'])) {
			$result['result'] = false;
			$result['error'][] = 'nonum_hotel_reserve_flag';
		} elseif(!in_array($params['hotel_reserve_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_hotel_reserve_flag';
		}
		if(!is_array($params['hotel_reserve_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_hotel_reserve_list';
		} elseif(count($params['hotel_reserve_list'])) {
			foreach($params['hotel_reserve_list'] as $hotel_reserve) {
				//酒店类型
				if(!empty($hotel_reserve['hotel_type'])) {
					if(!is_numeric($hotel_reserve['hotel_type'])) {
						$result['result'] = false;
						$result['error'][] = 'nonum_hotel_type';
					} elseif(!Model_Hoteltype::CheckExistHotelTypeId($hotel_reserve['hotel_type'])) {
						$result['result'] = false;
						$result['error'][] = 'noexist_hotel_type';
					}
				}
				
				//房型
				if(!empty($hotel_reserve['room_type'])) {
					if(!is_numeric($hotel_reserve['room_type'])) {
						$result['result'] = false;
						$result['error'][] = 'nonum_room_type';
					} elseif(!Model_Roomtype::CheckExistRoomTypeId($hotel_reserve['room_type'])) {
						$result['result'] = false;
						$result['error'][] = 'noexist_room_type';
					}
				}
				
				//人数
				if(!empty($hotel_reserve['people_num'])) {
					if(!preg_match('/^(\d{1,2})?$/', $hotel_reserve['people_num'])) {
						$result['result'] = false;
						$result['error'][] = 'error_people_num';
					}
				}
				
				//间数
				if(!empty($hotel_reserve['room_num'])) {
					if(!preg_match('/^(\d{1,2})?$/', $hotel_reserve['room_num'])) {
						$result['result'] = false;
						$result['error'][] = 'error_room_num';
					}
				}
				
				//天数
				if(!empty($hotel_reserve['day_num'])) {
					if(!preg_match('/^(\d{1,2})?$/', $hotel_reserve['day_num'])) {
						$result['result'] = false;
						$result['error'][] = 'error_day_num';
					}
				}
				
				//备注
				if(!empty($hotel_reserve['comment'])) {
					if(mb_strlen($hotel_reserve['comment']) > 200) {
						$result['result'] = false;
						$result['error'][] = 'long_hotel_comment';
					}
				}
			}
		}
		
		//成本报价
		if(!empty($params['cost_budget'])) {
			if(!preg_match('/^(\d{1,6})(\.\d{1,2})?$/', $params['cost_budget'])) {
				$result['result'] = false;
				$result['error'][] = 'error_cost_budget';
			}
		}
		
		//成本报价
		if(!empty($params['turnover'])) {
			if(!preg_match('/^(\d{1,6})(\.\d{1,2})?$/', $params['turnover'])) {
				$result['result'] = false;
				$result['error'][] = 'error_turnover';
			}
		}
		
		//实际成本
		if(!is_array($params['customer_cost_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_customer_cost_list';
		} elseif(count($params['customer_cost_list'])) {
			foreach($params['customer_cost_list'] as $customer_cost) {
				//项目
				if(!empty($customer_cost['customer_cost_type'])) {
					if(!is_numeric($customer_cost['customer_cost_type'])) {
						$result['result'] = false;
						$result['error'][] = 'nonum_customer_cost_type';
					} elseif(!Model_Customercosttype::CheckExistCustomerCostTypeId($customer_cost['customer_cost_type'])) {
						$result['result'] = false;
						$result['error'][] = 'noexist_customer_cost_type';
					}
				}
				
				//简述
				if(!empty($customer_cost['customer_cost_desc'])) {
					if(mb_strlen($customer_cost['customer_cost_desc']) > 100) {
						$result['result'] = false;
						$result['error'][] = 'long_customer_cost_desc';
					}
				}
				
				//天数
				if(!empty($customer_cost['customer_cost_day'])) {
					if(!preg_match('/^(\d{1,2})?$/', $customer_cost['customer_cost_day'])) {
						$result['result'] = false;
						$result['error'][] = 'error_customer_cost_day';
					}
				}
				
				//人数
				if(!empty($customer_cost['customer_cost_people'])) {
					if(!preg_match('/^(\d{1,2})?$/', $customer_cost['customer_cost_people'])) {
						$result['result'] = false;
						$result['error'][] = 'error_customer_cost_people';
					}
				}
				
				//单价
				if(!empty($customer_cost['customer_cost_each'])) {
					if(!preg_match('/^(\d{1,6})(\.\d{1,2})?$/', $customer_cost['customer_cost_each'])) {
						$result['result'] = false;
						$result['error'][] = 'error_customer_cost_each';
					}
				}
			}
		}
		
		//航班号
		if(!empty($params['airplane_num'])) {
			if(mb_strlen($params['airplane_num']) > 20) {
				$result['result'] = false;
				$result['error'][] = 'long_airplane_num';
			}
		}
		
		return $result;
	}

}

