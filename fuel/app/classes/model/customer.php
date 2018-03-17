<?php

class Model_Customer extends Model
{

	/*
	 * 添加顾客
	 */
	public static function InsertCustomer($params) {
		try {
			//添加顾客
			$sql_values_customer = array();
			$default_customer = array(
				'customer_name' => '',
				'customer_status' => 0,
				'customer_source' => 0,
				'customer_gender' => null,
				'customer_age' => null,
				'travel_reason' => null,
				'member_id' => null,
				'staff_id' => null,
				'men_num' => null,
				'women_num' => null,
				'children_num' => null,
				'travel_days' => null,
				'start_at_year' => null,
				'start_at_month' => null,
				'start_at_day' => null,
				'route_id' => null,
				'budget_base' => null,
				'budget_total' => null,
				'form_flag' => 0,
				'first_flag' => 0,
				'spot_hope_flag' => 0,
				'spot_hope_other' => null,
				'hotel_reserve_flag' => 0,
				'cost_budget' => null,
				'turnover' => null,
				'cost_total' => null,
				'dinner_demand' => null,
				'airplane_num' => null,
				'comment' => null,
				'delete_flag' => 0,
				'created_at' => null,
				'created_by' => 0,
				'modified_at' => null,
				'modified_by' => 0,
			);
			$field_customer = array_keys($default_customer);
			
			foreach($params as $param_key => $param_value) {
				if(in_array($param_key, $field_customer)) {
					if(empty($param_value)) {
						$sql_values_customer[$param_key] = $default_customer[$param_key];
					} else {
						$sql_values_customer[$param_key] = $param_value;
					}
				}
			}
			
			$sql_customer = "INSERT INTO t_customer(" . implode(", ", array_keys($sql_values_customer)) . ") "
						. "VALUES(:" . implode(", :", array_keys($sql_values_customer)) . ")";
			$query_customer = DB::query($sql_customer);
			foreach($sql_values_customer as $param_key => $param_value) {
				$query_customer->param($param_key, $param_value);
			}
			$result_customer = $query_customer->execute();
			
			if($result_customer) {
				//新顾客ID
				$customer_id = intval($result_customer[0]);
				
				//添加目标景点
				if(isset($params['spot_hope_list'])) {
					$sql_values_spot = array();
					$sql_params_spot = array();
					foreach($params['spot_hope_list'] as $param_key => $spot_id) {
						$sql_values_spot[] = "(:customer_id, :spot_id_" . $param_key . ")";
						$sql_params_spot['spot_id_' . $param_key] = $spot_id;
					}
					
					if(count($sql_values_spot)) {
						$sql_spot = "INSERT INTO r_customer_spot(customer_id, spot_id) VALUES" . implode(",", $sql_values_spot);
						$query_spot = DB::query($sql_spot);
						$query_spot->param('customer_id', $customer_id);
						foreach($sql_params_spot as $param_key => $param_value) {
							$query_spot->param($param_key, $param_value);
						}
						$result_spot = $query_spot->execute();
					}
				}
				
				//添加酒店预约
				if(isset($params['hotel_reserve_list'])) {
					$sql_values_hotel = array();
					$sql_params_hotel = array();
					foreach($params['hotel_reserve_list'] as $param_key => $hotel_reserve) {
						$sql_values_hotel[] = "(:customer_id, :row_id_" . $param_key . ", :hotel_type_" . $param_key . ", :room_type_" . $param_key . ", " 
											. ":people_num_" . $param_key . ", :room_num_" . $param_key . ", :day_num_" . $param_key . ", :comment_" . $param_key . ")";
						$sql_params_hotel['row_id_' . $param_key] = $param_key;
						$sql_params_hotel['hotel_type_' . $param_key] = $hotel_reserve['hotel_type'] ? $hotel_reserve['hotel_type'] : null;
						$sql_params_hotel['room_type_' . $param_key] = $hotel_reserve['room_type'] ? $hotel_reserve['room_type'] : null;
						$sql_params_hotel['people_num_' . $param_key] = $hotel_reserve['people_num'] ? $hotel_reserve['people_num'] : null;
						$sql_params_hotel['room_num_' . $param_key] = $hotel_reserve['room_num'] ? $hotel_reserve['room_num'] : null;
						$sql_params_hotel['day_num_' . $param_key] = $hotel_reserve['day_num'] ? $hotel_reserve['day_num'] : null;
						$sql_params_hotel['comment_' . $param_key] = $hotel_reserve['comment'] ? $hotel_reserve['comment'] : null;
					}
					
					if(count($sql_values_hotel)) {
						$sql_hotel = "INSERT INTO e_hotel_reserve(customer_id, row_id, hotel_type, room_type, people_num, room_num, day_num, comment) VALUES" . implode(",", $sql_values_hotel);
						$query_hotel = DB::query($sql_hotel);
						$query_hotel->param('customer_id', $customer_id);
						foreach($sql_params_hotel as $param_key => $param_value) {
							$query_hotel->param($param_key, $param_value);
						}
						$result_hotel = $query_hotel->execute();
					}
				}
				
				//添加实际成本
				if(isset($params['customer_cost_list'])) {
					$sql_values_cost = array();
					$sql_params_cost = array();
					foreach($params['customer_cost_list'] as $param_key => $customer_cost) {
						$sql_values_cost[] = "(:customer_id, :row_id_" . $param_key . ", :customer_cost_type_" . $param_key . ", :customer_cost_desc_" . $param_key . ", " 
											. ":customer_cost_day_" . $param_key . ", :customer_cost_people_" . $param_key . ", :customer_cost_each_" . $param_key . ", " 
											. ":customer_cost_total_" . $param_key . ")";
						$sql_params_cost['row_id_' . $param_key] = $param_key;
						$sql_params_cost['customer_cost_type_' . $param_key] = $customer_cost['customer_cost_type'] ? $customer_cost['customer_cost_type'] : null;
						$sql_params_cost['customer_cost_desc_' . $param_key] = $customer_cost['customer_cost_desc'] ? $customer_cost['customer_cost_desc'] : null;
						$sql_params_cost['customer_cost_day_' . $param_key] = $customer_cost['customer_cost_day'] ? $customer_cost['customer_cost_day'] : null;
						$sql_params_cost['customer_cost_people_' . $param_key] = $customer_cost['customer_cost_people'] ? $customer_cost['customer_cost_people'] : null;
						$sql_params_cost['customer_cost_each_' . $param_key] = $customer_cost['customer_cost_each'] ? $customer_cost['customer_cost_each'] : null;
						$sql_params_cost['customer_cost_total_' . $param_key] = $customer_cost['customer_cost_total'] ? $customer_cost['customer_cost_total'] : null;
					}
					
					if(count($sql_values_cost)) {
						$sql_cost = "INSERT INTO e_customer_cost(customer_id, row_id, customer_cost_type, customer_cost_desc, customer_cost_day, customer_cost_people, customer_cost_each, customer_cost_total)"
									. " VALUES" . implode(",", $sql_values_cost);
						$query_cost = DB::query($sql_cost);
						$query_cost->param('customer_id', $customer_id);
						foreach($sql_params_cost as $param_key => $param_value) {
							$query_cost->param($param_key, $param_value);
						}
						$result_cost = $query_cost->execute();
					}
				}
				
				return $customer_id;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新顾客
	 */
	public static function UpdateCustomer($params) {
		try {
			//更新顾客信息
			$sql_customer = "UPDATE t_customer "
						. "SET customer_name=:customer_name, customer_status=:customer_status, customer_source=:customer_source, customer_gender=:customer_gender, "
						. "customer_age=:customer_age, travel_reason=:travel_reason, staff_id=:staff_id, men_num=:men_num, women_num=:women_num, children_num=:children_num, "
						. "travel_days=:travel_days, start_at_year=:start_at_year, start_at_month=:start_at_month, start_at_day=:start_at_day, route_id=:route_id, "
						. "budget_base=:budget_base, budget_total=:budget_total, form_flag=:form_flag, first_flag=:first_flag, spot_hope_flag=:spot_hope_flag, "
						. "spot_hope_other=:spot_hope_other, hotel_reserve_flag=:hotel_reserve_flag, cost_budget=:cost_budget, turnover=:turnover, cost_total=:cost_total, "
						. "dinner_demand=:dinner_demand, airplane_num=:airplane_num, customer_email=:customer_email, customer_tel=:customer_tel, customer_wechat=:customer_wechat, "
						. "customer_qq=:customer_qq, comment=:comment, modified_at=:modified_at, modified_by=:modified_by "
						. "WHERE customer_id=:customer_id";
			$query_customer = DB::query($sql_customer);
			$query_customer->param('customer_id', $params['customer_id']);
			$query_customer->param('customer_name', $params['customer_name']);
			$query_customer->param('customer_status', $params['customer_status']);
			$query_customer->param('customer_source', $params['customer_source']);
			$query_customer->param('customer_gender', $params['customer_gender']);
			$query_customer->param('customer_age', $params['customer_age']);
			$query_customer->param('travel_reason', $params['travel_reason']);
			$query_customer->param('staff_id', $params['staff_id']);
			$query_customer->param('men_num', $params['men_num']);
			$query_customer->param('women_num', $params['women_num']);
			$query_customer->param('children_num', $params['children_num']);
			$query_customer->param('travel_days', $params['travel_days']);
			$query_customer->param('start_at_year', $params['start_at_year']);
			$query_customer->param('start_at_month', $params['start_at_month']);
			$query_customer->param('start_at_day', $params['start_at_day']);
			$query_customer->param('route_id', $params['route_id']);
			$query_customer->param('budget_base', $params['budget_base']);
			$query_customer->param('budget_total', $params['budget_total']);
			$query_customer->param('form_flag', $params['form_flag']);
			$query_customer->param('first_flag', $params['first_flag']);
			$query_customer->param('spot_hope_flag', $params['spot_hope_flag']);
			$query_customer->param('spot_hope_other', $params['spot_hope_other']);
			$query_customer->param('hotel_reserve_flag', $params['hotel_reserve_flag']);
			$query_customer->param('cost_budget', $params['cost_budget']);
			$query_customer->param('turnover', $params['turnover']);
			$query_customer->param('cost_total', $params['cost_total']);
			$query_customer->param('dinner_demand', $params['dinner_demand']);
			$query_customer->param('airplane_num', $params['airplane_num']);
			$query_customer->param('customer_email', $params['customer_email']);
			$query_customer->param('customer_tel', $params['customer_tel']);
			$query_customer->param('customer_wechat', $params['customer_wechat']);
			$query_customer->param('customer_qq', $params['customer_qq']);
			$query_customer->param('comment', $params['comment']);
			$query_customer->param('modified_at', date('Y-m-d H:i:s', time()));
			$query_customer->param('modified_by', $params['modified_by']);
			$result_customer = $query_customer->execute();
			
			if(isset($params['spot_hope_list'])) {
				//删除原有目标景点
				$sql_spot_delete = "DELETE FROM r_customer_spot WHERE customer_id=:customer_id";
				$query_spot_delete = DB::query($sql_spot_delete);
				$query_spot_delete->param('customer_id', $params['customer_id']);
				$result_spot_delete = $query_spot_delete->execute();
				
				//添加目标景点
				$sql_values_spot = array();
				$sql_params_spot = array();
				foreach($params['spot_hope_list'] as $param_key => $spot_id) {
					$sql_values_spot[] = "(:customer_id, :spot_id_" . $param_key . ")";
					$sql_params_spot['spot_id_' . $param_key] = $spot_id;
				}
				
				if(count($sql_values_spot)) {
					$sql_spot = "INSERT INTO r_customer_spot(customer_id, spot_id) VALUES" . implode(",", $sql_values_spot);
					$query_spot = DB::query($sql_spot);
					$query_spot->param('customer_id', $params['customer_id']);
					foreach($sql_params_spot as $param_key => $param_value) {
						$query_spot->param($param_key, $param_value);
					}
					$result_spot = $query_spot->execute();
				}
			}
			
			if(isset($params['spot_hope_list'])) {
				//删除原有酒店预约
				$sql_hotel_delete = "DELETE FROM e_hotel_reserve WHERE customer_id=:customer_id";
				$query_hotel_delete = DB::query($sql_hotel_delete);
				$query_hotel_delete->param('customer_id', $params['customer_id']);
				$result_hotel_delete = $query_hotel_delete->execute();
				
				//添加酒店预约
				$sql_values_hotel = array();
				$sql_params_hotel = array();
				foreach($params['hotel_reserve_list'] as $param_key => $hotel_reserve) {
					$sql_values_hotel[] = "(:customer_id, :row_id_" . $param_key . ", :hotel_type_" . $param_key . ", :room_type_" . $param_key . ", " 
										. ":people_num_" . $param_key . ", :room_num_" . $param_key . ", :day_num_" . $param_key . ", :comment_" . $param_key . ")";
					$sql_params_hotel['row_id_' . $param_key] = $param_key;
					$sql_params_hotel['hotel_type_' . $param_key] = $hotel_reserve['hotel_type'] ? $hotel_reserve['hotel_type'] : null;
					$sql_params_hotel['room_type_' . $param_key] = $hotel_reserve['room_type'] ? $hotel_reserve['room_type'] : null;
					$sql_params_hotel['people_num_' . $param_key] = $hotel_reserve['people_num'] ? $hotel_reserve['people_num'] : null;
					$sql_params_hotel['room_num_' . $param_key] = $hotel_reserve['room_num'] ? $hotel_reserve['room_num'] : null;
					$sql_params_hotel['day_num_' . $param_key] = $hotel_reserve['day_num'] ? $hotel_reserve['day_num'] : null;
					$sql_params_hotel['comment_' . $param_key] = $hotel_reserve['comment'] ? $hotel_reserve['comment'] : null;
				}
				
				if(count($sql_values_hotel)) {
					$sql_hotel = "INSERT INTO e_hotel_reserve(customer_id, row_id, hotel_type, room_type, people_num, room_num, day_num, comment) VALUES" . implode(",", $sql_values_hotel);
					$query_hotel = DB::query($sql_hotel);
					$query_hotel->param('customer_id', $params['customer_id']);
					foreach($sql_params_hotel as $param_key => $param_value) {
						$query_hotel->param($param_key, $param_value);
					}
					$result_hotel = $query_hotel->execute();
				}
			}
			
			if(isset($params['customer_cost_list'])) {
				//删除原有实际成本
				$sql_cost_delete = "DELETE FROM e_customer_cost WHERE customer_id=:customer_id";
				$query_cost_delete = DB::query($sql_cost_delete);
				$query_cost_delete->param('customer_id', $params['customer_id']);
				$result_cost_delete = $query_cost_delete->execute();
				
				//添加实际成本
				$sql_values_cost = array();
				$sql_params_cost = array();
				foreach($params['customer_cost_list'] as $param_key => $customer_cost) {
					$sql_values_cost[] = "(:customer_id, :row_id_" . $param_key . ", :customer_cost_type_" . $param_key . ", :customer_cost_desc_" . $param_key . ", " 
										. ":customer_cost_day_" . $param_key . ", :customer_cost_people_" . $param_key . ", :customer_cost_each_" . $param_key . ", " 
										. ":customer_cost_total_" . $param_key . ")";
					$sql_params_cost['row_id_' . $param_key] = $param_key;
					$sql_params_cost['customer_cost_type_' . $param_key] = $customer_cost['customer_cost_type'] ? $customer_cost['customer_cost_type'] : null;
					$sql_params_cost['customer_cost_desc_' . $param_key] = $customer_cost['customer_cost_desc'] ? $customer_cost['customer_cost_desc'] : null;
					$sql_params_cost['customer_cost_day_' . $param_key] = $customer_cost['customer_cost_day'] ? $customer_cost['customer_cost_day'] : null;
					$sql_params_cost['customer_cost_people_' . $param_key] = $customer_cost['customer_cost_people'] ? $customer_cost['customer_cost_people'] : null;
					$sql_params_cost['customer_cost_each_' . $param_key] = $customer_cost['customer_cost_each'] ? $customer_cost['customer_cost_each'] : null;
					$sql_params_cost['customer_cost_total_' . $param_key] = $customer_cost['customer_cost_total'] ? $customer_cost['customer_cost_total'] : null;
				}
				
				if(count($sql_values_cost)) {
					$sql_cost = "INSERT INTO e_customer_cost(customer_id, row_id, customer_cost_type, customer_cost_desc, customer_cost_day, customer_cost_people, customer_cost_each, customer_cost_total)"
								. " VALUES" . implode(",", $sql_values_cost);
					$query_cost = DB::query($sql_cost);
					$query_cost->param('customer_id', $params['customer_id']);
					foreach($sql_params_cost as $param_key => $param_value) {
						$query_cost->param($param_key, $param_value);
					}
					$result_cost = $query_cost->execute();
				}
			}
			
			if(isset($params['viewer_list'])) {
				//删除原有阅览者
				$sql_viewer_delete = "DELETE FROM r_customer_viewer WHERE customer_id=:customer_id";
				$query_viewer_delete = DB::query($sql_viewer_delete);
				$query_viewer_delete->param('customer_id', $params['customer_id']);
				$result_viewer_delete = $query_viewer_delete->execute();
				
				//添加阅览者
				$sql_values_viewer = array();
				$sql_params_viewer = array();
				foreach($params['viewer_list'] as $param_key => $user_id) {
					$sql_values_viewer[] = "(:customer_id, :user_id_" . $param_key . ")";
					$sql_params_viewer['user_id_' . $param_key] = $user_id;
				}
				
				if(count($sql_values_viewer)) {
					$sql_viewer = "INSERT INTO r_customer_viewer(customer_id, user_id) VALUES" . implode(",", $sql_values_viewer);
					$query_viewer = DB::query($sql_viewer);
					$query_viewer->param('customer_id', $params['customer_id']);
					foreach($sql_params_viewer as $param_key => $param_value) {
						$query_viewer->param($param_key, $param_value);
					}
					$result_viewer = $query_viewer->execute();
				}
			}
			
			if(isset($params['editor_list'])) {
				//删除原有编辑者
				$sql_editor_delete = "DELETE FROM r_customer_editor WHERE customer_id=:customer_id";
				$query_editor_delete = DB::query($sql_editor_delete);
				$query_editor_delete->param('customer_id', $params['customer_id']);
				$result_editor_delete = $query_editor_delete->execute();
				
				//添加编辑者
				$sql_values_editor = array();
				$sql_params_editor = array();
				foreach($params['editor_list'] as $param_key => $user_id) {
					$sql_values_editor[] = "(:customer_id, :user_id_" . $param_key . ")";
					$sql_params_editor['user_id_' . $param_key] = $user_id;
				}
				
				if(count($sql_values_editor)) {
					$sql_editor = "INSERT INTO r_customer_editor(customer_id, user_id) VALUES" . implode(",", $sql_values_editor);
					$query_editor = DB::query($sql_editor);
					$query_editor->param('customer_id', $params['customer_id']);
					foreach($sql_params_editor as $param_key => $param_value) {
						$query_editor->param($param_key, $param_value);
					}
					$result_editor = $query_editor->execute();
				}
			}
			
			if(isset($params['schedule_list'])) {
				//删除日程
				$sql_rus_delete = "DELETE FROM r_user_schedule WHERE schedule_id IN (SELECT schedule_id FROM r_customer_schedule WHERE customer_id=:customer_id)";
				$query_rus_delete = DB::query($sql_rus_delete);
				$query_rus_delete->param('customer_id', $params['customer_id']);
				$result_rus_delete = $query_rus_delete->execute();
				
				$sql_schedule_delete = "DELETE FROM t_schedule WHERE schedule_id IN (SELECT schedule_id FROM r_customer_schedule WHERE customer_id=:customer_id)";
				$query_schedule_delete = DB::query($sql_schedule_delete);
				$query_schedule_delete->param('customer_id', $params['customer_id']);
				$result_schedule_delete = $query_schedule_delete->execute();
				
				$sql_rcs_delete = "DELETE FROM r_customer_schedule WHERE customer_id=:customer_id";
				$query_rcs_delete = DB::query($sql_rcs_delete);
				$query_rcs_delete->param('customer_id', $params['customer_id']);
				$result_rcs_delete = $query_rcs_delete->execute();
				
				//添加日程
				foreach($params['schedule_list'] as $schedule) {
					foreach($schedule['schedule_detail_list'] as $schedule_detail) {
						$sql_schedule = "INSERT INTO t_schedule(start_at, end_at, schedule_type, schedule_desc, created_at, created_by, modified_at, modified_by) "
									. "VALUES(:start_at, :end_at, :schedule_type, :schedule_desc, :created_at, :created_by, :modified_at, :modified_by)";
						$query_schedule = DB::query($sql_schedule);
						$query_schedule->param('start_at', $schedule['schedule_date'] . ' ' . $schedule_detail['start_at'] . ':00');
						$query_schedule->param('end_at',  $schedule['schedule_date'] . ' ' . $schedule_detail['end_at'] . ':00');
						$query_schedule->param('schedule_type', $schedule_detail['schedule_type']);
						$query_schedule->param('schedule_desc', $schedule_detail['schedule_desc']);
						$query_schedule->param('created_at', date('Y-m-d H:i:s', time()));
						$query_schedule->param('created_by', $params['modified_by']);
						$query_schedule->param('modified_at', date('Y-m-d H:i:s', time()));
						$query_schedule->param('modified_by', $params['modified_by']);
						$result_schedule = $query_schedule->execute();
						
						if($result_schedule) {
							//新日程ID
							$schedule_id = intval($result_schedule[0]);
							
							//添加顾客日程
							$sql_rcs = "INSERT INTO r_customer_schedule(customer_id, schedule_id) VALUES(:customer_id, :schedule_id)";
							$query_rcs = DB::query($sql_rcs);
							$query_rcs->param('customer_id', $params['customer_id']);
							$query_rcs->param('schedule_id', $schedule_id);
							$result_rcs = $query_rcs->execute();
							
							//添加用户日程
							$sql_values_rus = array();
							$sql_params_rus = array();
							foreach($schedule['schedule_user_list'] as $param_key => $user_id) {
								$sql_values_rus[] = "(:user_id_" . $param_key . ", :schedule_id)";
								$sql_params_rus['user_id_' . $param_key] = $user_id;
							}
							
							if(count($sql_values_rus)) {
								$sql_rus = "INSERT INTO r_user_schedule(user_id, schedule_id) VALUES" . implode(",", $sql_values_rus);
								$query_rus = DB::query($sql_rus);
								$query_rus->param('schedule_id', $schedule_id);
								foreach($sql_params_rus as $param_key => $param_value) {
									$query_rus->param($param_key, $param_value);
								}
								$result_rus = $query_rus->execute();
							}
						}
					}
				}
			}
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新顾客状态
	 */
	public static function UpdateCustomerStatus($params) {
		try {
			$next_status = Model_Customerstatus::SelectNextCustomerStatus($params['customer_status']);
			
			if($next_status) {
				$sql = "UPDATE t_customer SET customer_status = :customer_status WHERE customer_id = :customer_id";
				$query = DB::query($sql);
				$query->param('customer_id', $params['customer_id']);
				$query->param('customer_status', $next_status['customer_status_id']);
				$result = $query->execute();
				
				return true;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 更新顾客状态为失效
	 */
	public static function UpdateCustomerDelete($params) {
		try {
			$sql = "UPDATE t_customer SET customer_status = :customer_status WHERE customer_id = :customer_id";
			$query = DB::query($sql);
			$query->param('customer_id', $params['customer_id']);
			$query->param('customer_status', $params['customer_status']);
			$result = $query->execute();
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/*
	 * 按条件获得顾客列表
	 */
	public static function SelectCustomerList($params, $select_user_id = null) {
		try {
			$sql_where = array();
			$sql_params = array();
			$sql_order_column = "created_at";
			$sql_order_method = "desc";
			$sql_limit = "";
			$sql_offset = "";
			
			foreach($params as $param_key => $param_value) {
				switch($param_key) {
					case 'customer_name':
						//姓名(模糊检索)
						if(count($param_value)) {
							$sql_sub_where = array();
							foreach($param_value as $name_key => $name) {
								$sql_sub_where[] = "tc.customer_name LIKE :customer_name_" . $name_key;
								$sql_params['customer_name_' . $name_key] = '%' . $name . '%';
							}
							$sql_where[] = " (" . implode(" OR ", $sql_sub_where) . ") ";
						}
						break;
					case 'customer_status':
						//当前状态
						if(count($param_value)) {
							$sql_where[] = " tc.customer_status IN :customer_status_list ";
							$sql_params['customer_status_list'] = $param_value;
						}
						break;
					case 'customer_source':
						//顾客来源
						if(count($param_value)) {
							$sql_where[] = " tc.customer_source IN :customer_source_list ";
							$sql_params['customer_source_list'] = $param_value;
						}
						break;
					case 'people_min':
						//人数(～人以上)
						if(is_numeric($param_value)) {
							$sql_where[] = " (tc.men_num+tc.women_num+tc.children_num) >= :people_min ";
							$sql_params['people_min'] = floatval($param_value);
						}
						break;
					case 'people_max':
						//人数(～人以内)
						if(is_numeric($param_value)) {
							$sql_where[] = " (tc.men_num+tc.women_num+tc.children_num) <= :people_max ";
							$sql_params['people_max'] = floatval($param_value);
						}
						break;
					case 'days_min':
						//天数(～天以上)
						if(is_numeric($param_value)) {
							$sql_where[] = " tc.travel_days >= :days_min ";
							$sql_params['days_min'] = floatval($param_value);
						}
						break;
					case 'days_max':
						//天数(～天以内)
						if(is_numeric($param_value)) {
							$sql_where[] = " tc.travel_days <= :days_max ";
							$sql_params['days_max'] = floatval($param_value);
						}
						break;
					case 'start_at_min':
						//来日时间(～之后)
						if(strtotime($param_value)) {
							$start_at_date = date('Y/n/j', strtotime($param_value));
							list($start_at_year, $start_at_month, $start_at_day) = explode('/', $start_at_date);
							$sql_where[] = " ((tc.start_at_year > :start_at_year_min AND tc.start_at_month IS NOT NULL AND tc.start_at_day IS NOT NULL) " 
										. "OR (tc.start_at_year = :start_at_year_min AND tc.start_at_month > :start_at_month_min AND tc.start_at_day IS NOT NULL) " 
										. "OR (tc.start_at_year = :start_at_year_min AND tc.start_at_month = :start_at_month_min AND tc.start_at_day >= :start_at_day_min)) ";
							$sql_params['start_at_year_min'] = intval($start_at_year);
							$sql_params['start_at_month_min'] = intval($start_at_month);
							$sql_params['start_at_day_min'] = intval($start_at_day);
						}
						break;
					case 'start_at_max':
						//来日时间(～之前)
						if(strtotime($param_value)) {
							$start_at_date = date('Y/n/j', strtotime($param_value));
							list($start_at_year, $start_at_month, $start_at_day) = explode('/', $start_at_date);
							$sql_where[] = " ((tc.start_at_year < :start_at_year_max AND tc.start_at_month IS NOT NULL AND tc.start_at_day IS NOT NULL) " 
										. "OR (tc.start_at_year = :start_at_year_max AND tc.start_at_month < :start_at_month_max AND tc.start_at_day IS NOT NULL) " 
										. "OR (tc.start_at_year = :start_at_year_max AND tc.start_at_month = :start_at_month_max AND tc.start_at_day <= :start_at_day_max)) ";
							$sql_params['start_at_year_max'] = intval($start_at_year);
							$sql_params['start_at_month_max'] = intval($start_at_month);
							$sql_params['start_at_day_max'] = intval($start_at_day);
						}
						break;
					case 'created_at_min':
						//登录时间(～之后)
						if(strtotime($param_value)) {
							$sql_where[] = " tc.created_at >= :created_at_min ";
							$sql_params['created_at_min'] = date('Y-m-d', strtotime($param_value)) . ' 00:00:00';
						}
						break;
					case 'created_at_max':
						//登录时间(～之前)
						if(strtotime($param_value)) {
							$sql_where[] = " tc.created_at <= :created_at_max ";
							$sql_params['created_at_max'] = date('Y-m-d', strtotime($param_value)) . ' 23:59:59';
						}
						break;
					case 'staff_pattern':
						//负责人限定
						if(count($param_value)) {
							$sql_sub_where = array();
							if(in_array('1', $param_value)) {
								$sql_sub_where[] = " tc.staff_id = :select_user_id ";
							}
							if(in_array('2', $param_value)) {
								$sql_sub_where[] = " tc.staff_id IS NULL ";
							}
							if(in_array('3', $param_value)) {
								$sql_sub_where[] = " (tc.staff_id != :select_user_id AND tc.staff_id IS NOT NULL) ";
							}
							$sql_where[] = " (" . implode(" OR ", $sql_sub_where) . ") ";
							$sql_params['select_user_id'] = intval($select_user_id);
						}
						break;
					case 'view_permission':
						//查看顾客信息权限
						switch($param_value) {
							case 1:
								//默认权限
								$sql_where[] = " (tc.staff_id = :select_user_id OR tc.customer_id IN (SELECT customer_id FROM r_customer_publish WHERE user_id = :select_user_id)) ";
								$sql_params['select_user_id'] = intval($select_user_id);
								break;
							case 2:
								//具备查看负责外的顾客信息(未设定负责人)权限
								$sql_where[] = " (tc.staff_id = :select_user_id OR tc.customer_id IN (SELECT customer_id FROM r_customer_publish WHERE user_id = :select_user_id) OR tc.staff_id IS NULL) ";
								$sql_params['select_user_id'] = intval($select_user_id);
								break;
							case 3:
								//具备备查看任意顾客信息权限
								break;
						}
						break;
					case 'active_only':
						//仅显示未删除顾客信息
						$sql_where[] = " tc.delete_flag = 0 ";
						break;
					case 'sort_column':
						//排序项目
						$sort_column_list = array('customer_name', 'customer_status', 'customer_source', 'people_num', 'travel_days', 'created_at', 'modified_at');
						if(in_array($param_value, $sort_column_list)) {
							$sql_order_column = $param_value;
						} elseif($param_value == 'start_at') {
							$sql_order_column = 'start_at_year, start_at_month, start_at_day';
						}
						break;
					case 'sort_method':
						//排序方法
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
			
			//符合条件的顾客总数获取
			$sql_count = "SELECT COUNT(DISTINCT tc.customer_id) customer_count "
						. "FROM t_customer tc "
						. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_count = DB::query($sql_count);
			foreach ($sql_params as $param_key => $param_value) {
				$query_count->param($param_key, $param_value);
			}
			$result_count = $query_count->execute()->as_array();
			
			if(count($result_count)) {
				$customer_count = intval($result_count[0]['customer_count']);
				
				if($customer_count) {
					//顾客信息获取
					$sql_customer = "SELECT tc.*, tc.men_num+tc.women_num+tc.children_num people_num, mcst.customer_status_name, mcso.customer_source_name, tu.user_name staff_name " 
							. "FROM t_customer tc " 
							. "LEFT JOIN m_customer_status mcst ON tc.customer_status = mcst.customer_status_id "
							. "LEFT JOIN m_customer_source mcso ON tc.customer_source = mcso.customer_source_id "
							. "LEFT JOIN t_user tu ON tc.staff_id = tu.user_id "
							. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "")
							. "ORDER BY " . $sql_order_column . " " . $sql_order_method . " "
							. $sql_limit . $sql_offset;
					$query_customer = DB::query($sql_customer);
					foreach ($sql_params as $param_key => $param_value) {
						$query_customer->param($param_key, $param_value);
					}
					$result_customer = $query_customer->execute()->as_array();
					
					if(count($result_customer)) {
						$customer_list = array();
						foreach($result_customer as $customer) {
							$customer_list[$customer['customer_id']] = $customer;
						}
						
						//返回值整理
						$result = array(
							'customer_count' => $customer_count,
							'customer_list' => $customer_list,
							'start_number' => $sql_offset + 1,
							'end_number' => count($result_customer) + $sql_offset,
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
	 * 获取特定单个顾客信息
	 */
	public static function SelectCustomer($params) {
		try {
			$sql_where = array();
			$sql_params = array();
			
			//顾客ID限定
			if(isset($params['customer_id'])) {
				$sql_where[] = " tc.customer_id = :customer_id ";
				$sql_params['customer_id'] = $params['customer_id'];
			}
			//有效性限定
			if(isset($params['active_only'])) {
				if($params['active_only']) {
					$sql_where[] = " tc.delete_flag = 0 ";
				}
			}
			
			//数据获取
			$sql_customer = "SELECT tc.*, mcst.customer_status_name, mcso.customer_source_name, mtr.travel_reason_name, "
						. "tus.user_name staff_name, tr.route_name, tuc.user_name created_name, tum.user_name modified_name " 
						. "FROM t_customer tc " 
						. "LEFT JOIN m_customer_status mcst ON tc.customer_status = mcst.customer_status_id " 
						. "LEFT JOIN m_customer_source mcso ON tc.customer_source = mcso.customer_source_id " 
						. "LEFT JOIN m_travel_reason mtr ON tc.travel_reason = mtr.travel_reason_id " 
						. "LEFT JOIN t_route tr ON tc.route_id = tr.route_id " 
						. "LEFT JOIN t_user tus ON tc.staff_id = tus.user_id " 
						. "LEFT JOIN t_user tuc ON tc.created_by = tuc.user_id " 
						. "LEFT JOIN t_user tum ON tc.modified_by = tum.user_id " 
						. (count($sql_where) ? (" WHERE " . implode(" AND ", $sql_where)) : "");
			$query_customer = DB::query($sql_customer);
			foreach($sql_params as $param_key => $param_value) {
				$query_customer->param($param_key, $param_value);
			}
			$result_customer = $query_customer->execute()->as_array();
			
			if(count($result_customer) == 1) {
				$result = $result_customer[0];
				
				//获取目标景点
				$sql_spot = "SELECT ts.spot_id, ts.spot_name FROM t_spot ts WHERE ts.spot_id IN (SELECT spot_id FROM r_customer_spot WHERE customer_id = :customer_id)";
				$query_spot = DB::query($sql_spot);
				$query_spot->param('customer_id', $result['customer_id']);
				$result_spot = $query_spot->execute()->as_array();
				$result['spot_hope_list'] = $result_spot;
				
				//获取酒店预约
				$sql_hotel = "SELECT ehr.*, mht.hotel_type_name, mrt.room_type_name "
						. "FROM e_hotel_reserve ehr "
						. "LEFT JOIN m_hotel_type mht ON mht.hotel_type_id = ehr.hotel_type "
						. "LEFT JOIN m_room_type mrt ON mrt.room_type_id = ehr.room_type "
						. "WHERE ehr.customer_id = :customer_id "
						. " ORDER BY ehr.row_id ASC ";
				$query_hotel = DB::query($sql_hotel);
				$query_hotel->param('customer_id', $result['customer_id']);
				$result_hotel = $query_hotel->execute()->as_array();
				$result['hotel_reserve_list'] = $result_hotel;
				
				//获取实际成本
				$sql_cost = "SELECT ecc.*, mcct.customer_cost_type_name "
						. "FROM e_customer_cost ecc "
						. "LEFT JOIN m_customer_cost_type mcct ON mcct.customer_cost_type_id = ecc.customer_cost_type "
						. "WHERE ecc.customer_id = :customer_id "
						. " ORDER BY ecc.row_id ASC ";
				$query_cost = DB::query($sql_cost);
				$query_cost->param('customer_id', $result['customer_id']);
				$result_cost = $query_cost->execute()->as_array();
				$result['customer_cost_list'] = $result_cost;
				
				//获取阅览者
				$sql_viewer = "SELECT tu.user_id, tu.user_name FROM t_user tu WHERE tu.user_id IN (SELECT user_id FROM r_customer_viewer WHERE customer_id = :customer_id)";
				$query_viewer = DB::query($sql_viewer);
				$query_viewer->param('customer_id', $result['customer_id']);
				$result['viewer_list'] = $query_viewer->execute()->as_array();
				
				//获取编辑者
				$sql_editor = "SELECT tu.user_id, tu.user_name FROM t_user tu WHERE tu.user_id IN (SELECT user_id FROM r_customer_editor WHERE customer_id = :customer_id)";
				$query_editor = DB::query($sql_editor);
				$query_editor->param('customer_id', $result['customer_id']);
				$result['editor_list'] = $query_editor->execute()->as_array();
				
				//获取日程
				$sql_schedule = "SELECT ts.*, mst.schedule_type_name, rus.user_id, tu.user_name " 
							. "FROM (SELECT * FROM t_schedule WHERE schedule_id IN (SELECT schedule_id FROM r_customer_schedule WHERE customer_id = :customer_id)) ts " 
							. "LEFT JOIN (SELECT * FROM r_user_schedule WHERE schedule_id IN (SELECT schedule_id FROM r_customer_schedule WHERE customer_id = :customer_id)) rus " 
							. "ON ts.schedule_id = rus.schedule_id " 
							. "LEFT JOIN t_user tu ON tu.user_id = rus.user_id " 
							. "LEFT JOIN m_schedule_type mst ON mst.schedule_type_id = ts.schedule_type " 
							. "ORDER BY ts.start_at, ts.schedule_id ASC";
				$query_schedule = DB::query($sql_schedule);
				$query_schedule->param('customer_id', $result['customer_id']);
				$result['schedule_list'] = $query_schedule->execute()->as_array();
				
				return $result;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/*
	 * 编辑顾客前信息查验
	 */
	public static function CheckEditCustomer($params) {
		$result = array(
			'result' => true,
			'error' => array(),
		);
		
		//顾客姓名
		if(empty($params['customer_name'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_customer_name';
		} elseif(mb_strlen($params['customer_name']) > 100) {
			$result['result'] = false;
			$result['error'][] = 'long_customer_name';
		}
		
		//顾客状态
		if(empty($params['customer_status'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_customer_status';
		} elseif(!is_numeric($params['customer_status']) || !is_int($params['customer_status'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_customer_status';
		} elseif(!Model_Customerstatus::CheckCustomerStatusIdExist($params['customer_status'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'noexist_customer_status';
		}
		
		//顾客性别
		if(!empty($params['customer_gender'])) {
			if(!in_array($params['customer_gender'], array('1', '2'))) {
				$result['result'] = false;
				$result['error'][] = 'error_customer_gender';
			}
		}
		
		//顾客年龄
		if(!empty($params['customer_age'])) {
			if(!in_array($params['customer_age'], array('1', '2', '3', '4', '5'))) {
				$result['result'] = false;
				$result['error'][] = 'error_customer_age';
			}
		}
		
		//旅游目的
		if(!empty($params['travel_reason'])) {
			if(!is_numeric($params['travel_reason']) || !is_int($params['travel_reason'] + 0)) {
				$result['result'] = false;
				$result['error'][] = 'noint_travel_reason';
			} elseif(!Model_Travelreason::CheckTravelReasonIdExist($params['travel_reason'], 1)) {
				$result['result'] = false;
				$result['error'][] = 'noexist_travel_reason';
			}
		}
		
		//顾客来源
		if(empty($params['customer_source'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_customer_source';
		} elseif(!is_numeric($params['customer_source']) || !is_int($params['customer_source'] + 0)) {
			$result['result'] = false;
			$result['error'][] = 'noint_customer_source';
		} elseif(!Model_Customersource::CheckCustomerSourceIdExist($params['customer_source'], 1)) {
			$result['result'] = false;
			$result['error'][] = 'noexist_customer_source';
		}
		
		//会员编号
		if(!empty($params['member_id'])) {
		}
		
		//负责人
		if(!empty($params['staff_id'])) {
			if(!is_numeric($params['staff_id']) || !is_int($params['staff_id'] + 0)) {
				$result['result'] = false;
				$result['error'][] = 'noint_staff_id';
			} elseif(!Model_User::CheckUserIdExist($params['staff_id'], 1)) {
				$result['result'] = false;
				$result['error'][] = 'noactive_staff_id';
			}
		}
		
		//人数
		if(!empty($params['men_num'])) {
			if(!is_numeric($params['men_num']) || !is_int($params['men_num'] + 0)) {
				$result['result'] = false;
				$result['error'][] = 'noint_men_num';
			} elseif(intval($params['men_num']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_men_num';
			}
		}
		if(!empty($params['women_num'])) {
			if(!is_numeric($params['women_num']) || !is_int($params['women_num'] + 0)) {
				$result['result'] = false;
				$result['error'][] = 'noint_women_num';
			} elseif(intval($params['women_num']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_women_num';
			}
		}
		if(!empty($params['children_num'])) {
			if(!is_numeric($params['children_num']) || !is_int($params['children_num'] + 0)) {
				$result['result'] = false;
				$result['error'][] = 'noint_children_num';
			} elseif(intval($params['children_num']) < 0) {
				$result['result'] = false;
				$result['error'][] = 'minus_children_num';
			}
		}
		
		//旅行天数
		if(!empty($params['travel_days'])) {
			if(!is_numeric($params['travel_days']) || !is_int($params['travel_days'] + 0)) {
				$result['result'] = false;
				$result['error'][] = 'noint_travel_days';
			}
		}
		
		//来日时间
		if(empty($params['start_at_year'])) {
			if(!empty($params['start_at_month']) || !empty($params['start_at_day'])) {
				$result['result'] = false;
				$result['error'][] = 'empty_start_at_year';
			}
		} elseif(!in_array($params['start_at_year'], array(date('Y', time()), date('Y', strtotime("+1 year"))))) {
			$result['result'] = false;
			$result['error'][] = 'error_start_at_year';
		}
		if(empty($params['start_at_month'])) {
			if(!empty($params['start_at_day'])) {
				$result['result'] = false;
				$result['error'][] = 'empty_start_at_month';
			}
		} elseif(!in_array($params['start_at_month'], array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'))) {
			$result['result'] = false;
			$result['error'][] = 'noint_start_at_month';
		}
		if(!empty($params['start_at_day'])) {
			if(!is_numeric($params['start_at_day']) || !is_int($params['start_at_day'] + 0)) {
				$result['result'] = false;
				$result['error'][] = 'noint_start_at_day';
			} elseif(intval($params['start_at_day']) < 1 || intval($params['start_at_day']) > 31) {
				$result['result'] = false;
				$result['error'][] = 'error_start_at_day';
			}
		}
		if(!empty($params['start_at_year']) && !empty($params['start_at_month']) && !empty($params['start_at_day']) && 
				!checkdate($params['start_at_month'], $params['start_at_day'], $params['start_at_year'])) {
			$result['result'] = false;
			$result['error'][] = 'error_start_at_date';
		}
		
		//基本旅游路线
		if(!empty($params['route_id'])) {
			if(!is_numeric($params['route_id']) || !is_int($params['route_id'] + 0)) {
				$result['result'] = false;
				$result['error'][] = 'noint_route_id';
			} elseif($params['route_id'] != '0' && !Model_Route::CheckRouteIdExist($params['route_id'], 1)) {
				$result['result'] = false;
				$result['error'][] = 'error_route_id';
			}
		}
		
		//预算
		if(!empty($params['budget_base'])) {
			if(!preg_match('/^(\d+)(\.\d{1,2})?$/', $params['budget_base'])) {
				$result['result'] = false;
				$result['error'][] = 'error_budget_base';
			}
		}
		if(!empty($params['budget_total'])) {
			if(!preg_match('/^(\d+)(\.\d{1,2})?$/', $params['budget_total'])) {
				$result['result'] = false;
				$result['error'][] = 'error_budget_total';
			}
		}
		
		//表单FLAG
		if(!in_array($params['form_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_form_flag';
		}
		
		//首次利用
		if(!in_array($params['first_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_first_flag';
		}
		
		//目标景点
		if(!in_array($params['spot_hope_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_spot_hope_flag';
		}
		if(!is_array($params['spot_hope_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_spot_hope_list';
		} elseif(count($params['spot_hope_list'])) {
			$spot_list = Model_Spot::SelectSpotSimpleList(array('spot_id_list' => $params['spot_hope_list'], 'spot_status' => array(1), 'active_only' => true));
			if(count($spot_list) != count($params['spot_hope_list'])) {
				$result['result'] = false;
				$result['error'][] = 'error_spot_list';
			}
		}
		
		//酒店预约
		if(!in_array($params['hotel_reserve_flag'], array('0', '1'))) {
			$result['result'] = false;
			$result['error'][] = 'nobool_hotel_reserve_flag';
		}
		if(!is_array($params['hotel_reserve_list'])) {
			$result['result'] = false;
			$result['error'][] = 'noarray_hotel_reserve_list';
		} elseif(count($params['hotel_reserve_list'])) {
			foreach($params['hotel_reserve_list'] as $hotel_reserve) {
				//酒店类型
				if(empty($hotel_reserve['hotel_type'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_hotel_type';
				} elseif(!is_numeric($hotel_reserve['hotel_type']) || !is_int($hotel_reserve['hotel_type'] + 0)) {
					$result['result'] = false;
					$result['error'][] = 'noint_hotel_type';
				} elseif(!Model_Hoteltype::CheckHotelTypeIdExist($hotel_reserve['hotel_type'], 1)) {
					$result['result'] = false;
					$result['error'][] = 'noexist_hotel_type';
				}
				
				//房型
				if(empty($hotel_reserve['room_type'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_room_type';
				} elseif(!is_numeric($hotel_reserve['room_type']) || !is_int($hotel_reserve['hotel_type'] + 0)) {
					$result['result'] = false;
					$result['error'][] = 'noint_room_type';
				} elseif(!Model_Roomtype::CheckRoomTypeIdExist($hotel_reserve['room_type'], 1)) {
					$result['result'] = false;
					$result['error'][] = 'noexist_room_type';
				}
				
				//人数
				if(empty($hotel_reserve['people_num'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_people_num';
				} elseif(!is_numeric($hotel_reserve['people_num']) || !is_int($hotel_reserve['people_num'] + 0)) {
					$result['result'] = false;
					$result['error'][] = 'noint_people_num';
				} elseif(intval($hotel_reserve['people_num']) < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_people_num';
				}
				
				//间数
				if(empty($hotel_reserve['room_num'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_room_num';
				} elseif(!is_numeric($hotel_reserve['room_num']) || !is_int($hotel_reserve['room_num'] + 0)) {
					$result['result'] = false;
					$result['error'][] = 'noint_room_num';
				} elseif(intval($hotel_reserve['room_num']) < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_room_num';
				}
				
				//天数
				if(empty($hotel_reserve['day_num'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_day_num';
				} elseif(!is_numeric($hotel_reserve['day_num']) || !is_int($hotel_reserve['day_num'] + 0)) {
					$result['result'] = false;
					$result['error'][] = 'noint_day_num';
				} elseif(intval($hotel_reserve['day_num']) < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_day_num';
				}
			}
		}
		
		//成本报价
		if(!empty($params['cost_budget'])) {
			if(!preg_match('/^(\d+)(\.\d{1,2})?$/', $params['cost_budget'])) {
				$result['result'] = false;
				$result['error'][] = 'error_cost_budget';
			}
		}
		
		//成本报价
		if(!empty($params['turnover'])) {
			if(!preg_match('/^(\d+)(\.\d{1,2})?$/', $params['turnover'])) {
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
				if(empty($customer_cost['customer_cost_type'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_customer_cost_type';
				} elseif(!is_numeric($customer_cost['customer_cost_type']) || !is_int($customer_cost['customer_cost_type'] + 0)) {
					$result['result'] = false;
					$result['error'][] = 'noint_customer_cost_type';
				} elseif(!Model_Customercosttype::CheckCustomerCostTypeIdExist($customer_cost['customer_cost_type'], 1)) {
					$result['result'] = false;
					$result['error'][] = 'noexist_customer_cost_type';
				}
				
				//简述
				if($customer_cost['customer_cost_type'] == '1') {
					if(empty($customer_cost['customer_cost_desc'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_customer_cost_desc';
					}
				}
				
				//天数
				if(empty($customer_cost['customer_cost_day'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_customer_cost_day';
				} elseif(!is_numeric($customer_cost['customer_cost_day']) || !is_int($customer_cost['customer_cost_day'] + 0)) {
					$result['result'] = false;
					$result['error'][] = 'noint_customer_cost_day';
				} elseif(intval($customer_cost['customer_cost_day']) < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_customer_cost_day';
				}
				
				//人数
				if(empty($customer_cost['customer_cost_people'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_customer_cost_people';
				} elseif(!is_numeric($customer_cost['customer_cost_people']) || !is_int($customer_cost['customer_cost_people'] + 0)) {
					$result['result'] = false;
					$result['error'][] = 'noint_customer_cost_people';
				} elseif(intval($customer_cost['customer_cost_people']) < 0) {
					$result['result'] = false;
					$result['error'][] = 'minus_customer_cost_people';
				}
				
				//单价
				if(empty($customer_cost['customer_cost_each'])) {
					$result['result'] = false;
					$result['error'][] = 'empty_customer_cost_each';
				} elseif(!preg_match('/^(\d+)(\.\d{1,2})?$/', $customer_cost['customer_cost_each'])) {
					$result['result'] = false;
					$result['error'][] = 'error_customer_cost_each';
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
		
		//联系方式
		if(empty($params['customer_email']) && empty($params['customer_tel']) && empty($params['customer_wechat']) && empty($params['customer_qq'])) {
			$result['result'] = false;
			$result['error'][] = 'empty_customer_contact';
		}
		
		//电子邮箱
		if(!empty($params['customer_email'])) {
			if(mb_strlen($params['customer_email']) > 200) {
				$result['result'] = false;
				$result['error'][] = 'long_customer_email';
			}
		}
		
		//联系电话
		if(!empty($params['customer_tel'])) {
			if(mb_strlen($params['customer_tel']) > 20) {
				$result['result'] = false;
				$result['error'][] = 'long_customer_tel';
			}
		}
		
		//微信号
		if(!empty($params['customer_wechat'])) {
			if(mb_strlen($params['customer_wechat']) > 20) {
				$result['result'] = false;
				$result['error'][] = 'long_customer_wechat';
			}
		}
		
		//QQ号
		if(!empty($params['customer_qq'])) {
			if(mb_strlen($params['customer_qq']) > 20) {
				$result['result'] = false;
				$result['error'][] = 'long_customer_qq';
			}
		}
		
		//日程设计
		if(isset($params['schedule_list'])) {
			if(!is_array($params['schedule_list'])) {
				$result['result'] = false;
				$result['error'][] = 'noarray_schedule_list';
			} elseif(count($params['schedule_list'])) {
				$schedule_time_list = array();
				foreach($params['schedule_list'] as $schedule) {
					//日程日期
					if(empty($schedule['schedule_date'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_schedule_date';
					} elseif(!preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $schedule['schedule_date'])) {
						$result['result'] = false;
						$result['error'][] = 'format_schedule_date';
					} else {
						list($year, $month, $day) = explode('/', $schedule['schedule_date']);
						if(!checkdate(intval($month), intval($day), intval($year))) {
							$result['result'] = false;
							$result['error'][] = 'error_schedule_date';
						}
					}
					
					//日程负责人
					if(!is_array($schedule['schedule_user_list'])) {
						$result['result'] = false;
						$result['error'][] = 'noarray_schedule_user_list';
					} elseif(!count($schedule['schedule_user_list'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_schedule_user_list';
					} else {
						$user_list = Model_User::SelectUserSimpleList(array('user_id_list' => $schedule['schedule_user_list'], 'user_type_except' => array(1), 'active_only' => true));
						if(count($user_list) != count($schedule['schedule_user_list'])) {
							$result['result'] = false;
							$result['error'][] = 'error_schedule_user_list';
						}
					}
					
					//详细日程
					if(!is_array($schedule['schedule_detail_list'])) {
						$result['result'] = false;
						$result['error'][] = 'noarray_schedule_detail_list';
					} elseif(!count($schedule['schedule_detail_list'])) {
						$result['result'] = false;
						$result['error'][] = 'empty_schedule_detail_list';
					} else {
						foreach($schedule['schedule_detail_list'] as $schedule_detail) {
							//详细日程时间
							if(empty($schedule_detail['start_at']) || empty($schedule_detail['end_at'])) {
								$result['result'] = false;
								$result['error'][] = 'empty_schedule_time';
							} elseif($schedule_detail['start_at'] >= $schedule_detail['end_at']) {
								$result['result'] = false;
								$result['error'][] = 'error_schedule_time';
							} elseif(isset($schedule_time_list[$schedule['schedule_date']])) {
								foreach($schedule_time_list[$schedule['schedule_date']] as $schedule_time) {
									if($schedule_detail['start_at'] < $schedule_time['end_at'] && $schedule_time['start_at'] < $schedule_detail['end_at']) {
										$result['result'] = false;
										$result['error'][] = 'dup_self_schedule_time';
										break;
									} else {
										$schedule_time_list[$schedule['schedule_date']][] = array('start_at' => $schedule_detail['start_at'], 'end_at' => $schedule_detail['end_at']);
									}
								}
							} else {
								$schedule_time_list[$schedule['schedule_date']] = array(array('start_at' => $schedule_detail['start_at'], 'end_at' => $schedule_detail['end_at']));
								
								if(is_array($schedule['schedule_user_list']) && count($schedule['schedule_user_list']) && $params['customer_id']) {
									foreach($schedule['schedule_user_list'] as $schedule_user_id) {
										if(Model_Schedule::CheckScheduleDuplication($schedule_user_id, $schedule_detail['start_at'], $schedule_detail['end_at'], $params['customer_id'])) {
											$result['result'] = false;
											$result['error'][] = 'dup_user_schedule_time';
											break;
										}
									}
								}
							}
							
							//详细日程类型
							if(empty($schedule_detail['schedule_type'])) {
								$result['result'] = false;
								$result['error'][] = 'empty_schedule_type';
							} elseif(!is_numeric($schedule_detail['schedule_type']) || !is_int($schedule_detail['schedule_type'] + 0)) {
								$result['result'] = false;
								$result['error'][] = 'noint_schedule_type';
							} elseif(!Model_Scheduletype::CheckScheduleTypeIdExist($schedule_detail['schedule_type'], 1)) {
								$result['result'] = false;
								$result['error'][] = 'noexist_schedule_type';
							}
							
							//详细日程内容
							if(empty($schedule_detail['schedule_desc'])) {
								$result['result'] = false;
								$result['error'][] = 'empty_schedule_desc';
							}
						}
					}
				}
			}
		}
		
		return $result;
	}

}

