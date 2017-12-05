<?php

class Model_Customer extends Model
{

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
			if(!is_numeric($params['men_num'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_men_num';
			} elseif(intval($params['men_num']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_men_num';
			} elseif($params['men_num'] - intval($params['men_num']) > 0) {
				$result['result'] = false;
				$result['error'][] = 'noint_men_num';
			}
		}
		if(!empty($params['women_num'])) {
			if(!is_numeric($params['women_num'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_women_num';
			} elseif(intval($params['women_num']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_women_num';
			} elseif($params['women_num'] - intval($params['women_num']) > 0) {
				$result['result'] = false;
				$result['error'][] = 'noint_women_num';
			}
		}
		if(!empty($params['children_num'])) {
			if(!is_numeric($params['children_num'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_children_num';
			} elseif(intval($params['children_num']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_children_num';
			} elseif($params['children_num'] - intval($params['children_num']) > 0) {
				$result['result'] = false;
				$result['error'][] = 'noint_children_num';
			}
		}
		
		//旅行天数
		if(!empty($params['travel_days'])) {
			if(!is_numeric($params['travel_days'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_travel_days';
			} elseif(intval($params['travel_days']) < 1) {
				$result['result'] = false;
				$result['error'][] = 'error_travel_days';
			} elseif($params['travel_days'] - intval($params['travel_days']) > 0) {
				$result['result'] = false;
				$result['error'][] = 'noint_travel_days';
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
			if(!is_numeric($params['budget_base'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_budget_base';
			} elseif(intval($params['budget_base']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_budget_base';
			}
		}
		if(!empty($params['budget_total'])) {
			if(!is_numeric($params['budget_total'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_budget_total';
			} elseif(intval($params['budget_total']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_budget_total';
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
					if(!is_numeric($hotel_reserve['people_num'])) {
						$result['result'] = false;
						$result['error'][] = 'nonum_people_num';
					} elseif(intval($hotel_reserve['people_num']) < 1) {
						$result['result'] = false;
						$result['error'][] = 'error_people_num';
					} elseif($hotel_reserve['people_num'] - intval($hotel_reserve['people_num']) > 0) {
						$result['result'] = false;
						$result['error'][] = 'noint_people_num';
					}
				}
				
				//间数
				if(!empty($hotel_reserve['room_num'])) {
					if(!is_numeric($hotel_reserve['room_num'])) {
						$result['result'] = false;
						$result['error'][] = 'nonum_room_num';
					} elseif(intval($hotel_reserve['room_num']) < 1) {
						$result['result'] = false;
						$result['error'][] = 'error_room_num';
					} elseif($hotel_reserve['room_num'] - intval($hotel_reserve['room_num']) > 0) {
						$result['result'] = false;
						$result['error'][] = 'noint_room_num';
					}
				}
				
				//天数
				if(!empty($hotel_reserve['day_num'])) {
					if(!is_numeric($hotel_reserve['day_num'])) {
						$result['result'] = false;
						$result['error'][] = 'nonum_day_num';
					} elseif(intval($hotel_reserve['day_num']) < 1) {
						$result['result'] = false;
						$result['error'][] = 'error_day_num';
					} elseif($hotel_reserve['day_num'] - intval($hotel_reserve['day_num']) > 0) {
						$result['result'] = false;
						$result['error'][] = 'noint_day_num';
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
			if(!is_numeric($params['cost_budget'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_cost_budget';
			} elseif(intval($params['cost_budget']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_cost_budget';
			}
		}
		
		//成本报价
		if(!empty($params['turnover'])) {
			if(!is_numeric($params['turnover'])) {
				$result['result'] = false;
				$result['error'][] = 'nonum_turnover';
			} elseif(intval($params['turnover']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_turnover';
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
					if(!is_numeric($customer_cost['customer_cost_day'])) {
						$result['result'] = false;
						$result['error'][] = 'nonum_customer_cost_day';
					} elseif(intval($customer_cost['customer_cost_day']) < 1) {
						$result['result'] = false;
						$result['error'][] = 'error_customer_cost_day';
					} elseif($customer_cost['customer_cost_day'] - intval($customer_cost['customer_cost_day']) > 0) {
						$result['result'] = false;
						$result['error'][] = 'noint_customer_cost_day';
					}
				}
				
				//人数
				if(!empty($customer_cost['customer_cost_people'])) {
					if(!is_numeric($customer_cost['customer_cost_people'])) {
						$result['result'] = false;
						$result['error'][] = 'nonum_customer_cost_people';
					} elseif(intval($customer_cost['customer_cost_people']) < 1) {
						$result['result'] = false;
						$result['error'][] = 'error_customer_cost_people';
					} elseif($customer_cost['customer_cost_people'] - intval($customer_cost['customer_cost_people']) > 0) {
						$result['result'] = false;
						$result['error'][] = 'noint_customer_cost_people';
					}
				}
				
				//单价
				if(!empty($customer_cost['customer_cost_each'])) {
					if(!is_numeric($customer_cost['customer_cost_each'])) {
						$result['result'] = false;
						$result['error'][] = 'nonum_customer_cost_each';
					} elseif(intval($customer_cost['customer_cost_each']) < 1) {
						$result['result'] = false;
						$result['error'][] = 'error_customer_cost_each';
					} elseif($customer_cost['customer_cost_each'] - intval($customer_cost['customer_cost_each']) > 0) {
						$result['result'] = false;
						$result['error'][] = 'noint_customer_cost_each';
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
		
		$result['error'] = array_unique($result['error']);
		
		return $result;
	}

}

