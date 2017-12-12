<?php
/* 
 * 顾客登录页
 */

class Controller_Admin_Customer_Addcustomer extends Controller_Admin_App
{

	/**
	 * 顾客登录页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($param = null)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['error_message'] = '';
			
			//必要列表信息取得
			$data['travel_reason_list'] = Model_Travelreason::GetTravelReasonListAll();
			$data['customer_source_list'] = Model_Customersource::GetCustomerSourceListAll();
			$data['user_list'] = Model_User::GetActiveUserSimpleListAll();
			$data['route_list'] = Model_Route::GetActiveRouteSimpleListAll();
			$data['spot_list'] = Model_Spot::SelectSpotSimpleListAll();
			$data['hotel_type_list'] = Model_Hoteltype::GetHotelTypeListAll();
			$data['room_type_list'] = Model_Roomtype::GetRoomTypeListAll();
			$data['customer_cost_type_list'] = Model_Customercosttype::GetCustomerCostTypeListExceptOther();
			
			//form控件默认值设定
			$data['input_customer_name'] = '';
			$data['input_customer_source'] = '';
			$data['input_customer_gender'] = '';
			$data['input_customer_age'] = '';
			$data['input_travel_reason'] = '';
			$data['input_staff_id'] = '';
			$data['input_men_num'] = '';
			$data['input_women_num'] = '';
			$data['input_children_num'] = '';
			$data['input_travel_days'] = '';
			$data['input_start_at_year'] = '';
			$data['input_start_at_month'] = '';
			$data['input_start_at_day'] = '';
			$data['input_route_id'] = '';
			$data['input_budget_base'] = '';
			$data['input_budget_total'] = '';
			$data['input_first_flag'] = '';
			$data['input_spot_hope_flag'] = '';
			$data['input_spot_hope_list'] = array();
			$data['input_spot_hope_other'] = '';
			$data['input_hotel_reserve_flag'] = '';
			$data['input_hotel_reserve_list'] = array();
			$data['input_cost_budget'] = '';
			$data['input_turnover'] = '';
			$data['input_customer_cost_list'] = array();
			$data['input_dinner_demand'] = '';
			$data['input_airplane_num'] = '';
			$data['input_cost_total'] = '';
			$data['input_comment'] = '';
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				
				//数据来源检验
				if($_POST['page'] == 'add_customer') {
					//form控件值设定
					$data['input_customer_name'] = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
					$data['input_customer_source'] = isset($_POST['customer_source']) ? trim($_POST['customer_source']) : '';
					$data['input_customer_gender'] = isset($_POST['customer_gender']) ? trim($_POST['customer_gender']) : '';
					$data['input_customer_age'] = isset($_POST['customer_age']) ? trim($_POST['customer_age']) : '';
					$data['input_travel_reason'] = isset($_POST['travel_reason']) ? trim($_POST['travel_reason']) : '';
					$data['input_staff_id'] = isset($_POST['staff_id']) ? trim($_POST['staff_id']) : '';
					$data['input_men_num'] = isset($_POST['men_num']) ? trim($_POST['men_num']) : '';
					$data['input_women_num'] = isset($_POST['women_num']) ? trim($_POST['women_num']) : '';
					$data['input_children_num'] = isset($_POST['children_num']) ? trim($_POST['children_num']) : '';
					$data['input_travel_days'] = isset($_POST['travel_days']) ? trim($_POST['travel_days']) : '';
					$data['input_start_at_year'] = isset($_POST['start_at_year']) ? trim($_POST['start_at_year']) : '';
					$data['input_start_at_month'] = isset($_POST['start_at_month']) ? trim($_POST['start_at_month']) : '';
					$data['input_start_at_day'] = isset($_POST['start_at_day']) ? trim($_POST['start_at_day']) : '';
					$data['input_route_id'] = isset($_POST['route_id']) ? trim($_POST['route_id']) : '';
					$data['input_budget_base'] = isset($_POST['budget_base']) ? trim($_POST['budget_base']) : '';
					$data['input_budget_total'] = isset($_POST['budget_total']) ? trim($_POST['budget_total']) : '';
					$data['input_first_flag'] = isset($_POST['first_flag']) ? trim($_POST['first_flag']) : '';
					$data['input_spot_hope_flag'] = isset($_POST['spot_hope_flag']) ? trim($_POST['spot_hope_flag']) : '';
					$data['input_spot_hope_list'] = isset($_POST['route_spot_hope_list']) ? (is_array($_POST['route_spot_hope_list']) ? $_POST['route_spot_hope_list'] : array()) : array();
					$data['input_spot_hope_other'] = isset($_POST['spot_hope_other']) ? trim($_POST['spot_hope_other']) : '';
					$data['input_hotel_reserve_flag'] = isset($_POST['hotel_reserve_flag']) ? trim($_POST['hotel_reserve_flag']) : '';
					$data['input_cost_budget'] = isset($_POST['cost_budget']) ? trim($_POST['cost_budget']) : '';
					$data['input_turnover'] = isset($_POST['turnover']) ? trim($_POST['turnover']) : '';
					$data['input_dinner_demand'] = isset($_POST['dinner_demand']) ? trim($_POST['dinner_demand']) : '';
					$data['input_airplane_num'] = isset($_POST['airplane_num']) ? trim($_POST['airplane_num']) : '';
					$data['input_comment'] = isset($_POST['comment']) ? trim($_POST['comment']) : '';
					
					//form控件值设定 酒店预约
					if(isset($_POST['hotel_reserve_row'])) {
						if(is_array($_POST['hotel_reserve_row'])) {
							foreach($_POST['hotel_reserve_row'] as $row_num) {
								$data['input_hotel_reserve_list'][] = array(
									'hotel_type' => isset($_POST['hotel_type_' . $row_num]) ? trim($_POST['hotel_type_' . $row_num]) : '',
									'room_type' => isset($_POST['room_type_' . $row_num]) ? trim($_POST['room_type_' . $row_num]) : '',
									'people_num' => isset($_POST['people_num_' . $row_num]) ? trim($_POST['people_num_' . $row_num]) : '',
									'room_num' => isset($_POST['room_num_' . $row_num]) ? trim($_POST['room_num_' . $row_num]) : '',
									'day_num' => isset($_POST['day_num_' . $row_num]) ? trim($_POST['day_num_' . $row_num]) : '',
									'comment' => isset($_POST['comment_' . $row_num]) ? trim($_POST['comment_' . $row_num]) : '',
								);
							}
						}
					}
					
					//form控件值设定 实际成本
					$cost_total = 0;
					if(isset($_POST['customer_cost_row'])) {
						if(is_array($_POST['customer_cost_row'])) {
							foreach($_POST['customer_cost_row'] as $row_num) {
								$customer_cost_total = 0;
								if(isset($_POST['customer_cost_day_' . $row_num]) && isset($_POST['customer_cost_people_' . $row_num]) && isset($_POST['customer_cost_each_' . $row_num])) {
									if(is_numeric($_POST['customer_cost_day_' . $row_num]) && is_numeric($_POST['customer_cost_people_' . $row_num]) && is_numeric($_POST['customer_cost_each_' . $row_num])) {
										$customer_cost_total = floatval($_POST['customer_cost_day_' . $row_num]) * floatval($_POST['customer_cost_people_' . $row_num]) * floatval($_POST['customer_cost_each_' . $row_num]);
									}
								}
								
								$data['input_customer_cost_list'][] = array(
									'customer_cost_type' => isset($_POST['customer_cost_type_' . $row_num]) ? trim($_POST['customer_cost_type_' . $row_num]) : '',
									'customer_cost_desc' => isset($_POST['customer_cost_desc_' . $row_num]) ? trim($_POST['customer_cost_desc_' . $row_num]) : '',
									'customer_cost_day' => isset($_POST['customer_cost_day_' . $row_num]) ? trim($_POST['customer_cost_day_' . $row_num]) : '',
									'customer_cost_people' => isset($_POST['customer_cost_people_' . $row_num]) ? trim($_POST['customer_cost_people_' . $row_num]) : '',
									'customer_cost_each' => isset($_POST['customer_cost_each_' . $row_num]) ? trim($_POST['customer_cost_each_' . $row_num]) : '',
									'customer_cost_total' => $customer_cost_total,
								);
								
								$cost_total += $customer_cost_total;
							}
						}
					}
					$data['input_cost_total'] = $cost_total;
					
					//添加顾客用数据生成
					$param_insert = array(
						'customer_name' => $data['input_customer_name'],
						'customer_status' => '1',
						'customer_source' => $data['input_customer_source'],
						'customer_gender' => $data['input_customer_gender'],
						'customer_age' => $data['input_customer_age'],
						'travel_reason' => $data['input_travel_reason'],
						'member_id' => '',
						'staff_id' => $data['input_staff_id'],
						'men_num' => $data['input_men_num'],
						'women_num' => $data['input_women_num'],
						'children_num' => $data['input_children_num'],
						'travel_days' => $data['input_travel_days'],
						'start_at_year' => $data['input_start_at_year'],
						'start_at_month' => $data['input_start_at_month'],
						'start_at_day' => $data['input_start_at_day'],
						'route_id' => $data['input_route_id'],
						'budget_base' => $data['input_budget_base'],
						'budget_total' => $data['input_budget_total'],
						'form_flag' => '0',
						'first_flag' => $data['input_first_flag'],
						'spot_hope_flag' => $data['input_spot_hope_flag'],
						'spot_hope_list' => $data['input_spot_hope_list'],
						'spot_hope_other' => $data['input_spot_hope_other'],
						'hotel_reserve_flag' => $data['input_hotel_reserve_flag'],
						'hotel_reserve_list' => $data['input_hotel_reserve_list'],
						'cost_budget' => $data['input_cost_budget'],
						'turnover' => $data['input_turnover'],
						'customer_cost_list' => $data['input_customer_cost_list'],
						'cost_total' => $data['input_cost_total'],
						'dinner_demand' => $data['input_dinner_demand'],
						'airplane_num' => $data['input_airplane_num'],
						'comment' => $data['input_comment'],
					);
					
					//输入内容检查
					$result_check = Model_Customer::CheckInsertCustomer($param_insert);
					
					if($result_check['result']) {
						$result_insert = Model_Customer::InsertCustomer($param_insert);
						
						if($result_insert) {
							//添加成功 页面跳转
							$customer_id = $result_insert[0];
							$_SESSION['add_customer_success'] = true;
							header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/customer/' . $customer_id . '/');
							exit;
						} else {
							$error_message_list[] = '数据库错误：数据添加失败';
						}
					} else {
						foreach($result_check['error'] as $insert_error) {
							switch($insert_error) {
								case 'empty_customer_name':
									$error_message_list[] = '请输入顾客姓名';
									break;
								case 'long_customer_name':
									$error_message_list[] = '顾客姓名不能超过50字';
									break;
								case 'error_men_num':
								case 'error_women_num':
								case 'error_children_num':
									$error_message_list[] = '请为人数部分输入0～99以内的整数';
									break;
								case 'error_travel_days':
									$error_message_list[] = '请为旅行天数部分输入0～99以内的整数';
									break;
								case 'empty_start_at_year':
									$error_message_list[] = '请选择来日年';
									break;
								case 'empty_start_at_month':
									$error_message_list[] = '请选择来日月';
									break;
								case 'error_start_at_date':
									$error_message_list[] = '您选择的来日日期不存在,请重新选择';
									break;
								case 'error_budget_base':
								case 'error_budget_total':
									$error_message_list[] = '请为预算部分输入0～10万以内且不多于2位小数的数字';
									break;
								case 'error_people_num':
									$error_message_list[] = '请为酒店预约的人数部分输入0～99以内的整数';
									break;
								case 'error_room_num':
									$error_message_list[] = '请为酒店预约的间数部分输入0～99以内的整数';
									break;
								case 'error_day_num':
									$error_message_list[] = '请为酒店预约的天数部分输入0～99以内的整数';
									break;
								case 'long_hotel_comment':
									$error_message_list[] = '酒店预约的备注部分不能超过200字';
									break;
								case 'error_cost_budget':
									$error_message_list[] = '请为成本报价部分输入0～10万以内且不多于2位小数的数字';
									break;
								case 'error_turnover':
									$error_message_list[] = '请为营业额部分输入0～10万以内且不多于2位小数的数字';
									break;
								case 'error_customer_cost_day':
									$error_message_list[] = '请为实际成本的天数部分输入0～99以内的整数';
									break;
								case 'error_customer_cost_people':
									$error_message_list[] = '请为实际成本的人数部分输入0～99以内的整数';
									break;
								case 'error_customer_cost_each':
									$error_message_list[] = '请为实际成本的单价部分输入0～10万以内且不多于2位小数的数字';
									break;
								case 'long_airplane_num':
									$error_message_list[] = '航班号不能超过20字';
									break;
								default:
									$error_message_list[] = '发生系统错误,请重新尝试添加';
									break;
							}
						}
					}
					
					$error_message_list = array_unique($error_message_list);
					
					//输出错误信息
					if(count($error_message_list)) {
						$data['error_message'] = implode('<br/>', $error_message_list);
					}
				} else {
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				}
			}
			
			//调用View
			return Response::forge(View::forge($this->template . '/admin/customer/add_customer', $data, false));
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

	/**
	 * 获取成本项目列表
	 * @access  public
	 * @return  Response
	 */
	public function action_customercosttypelist($page = null)
	{
		$result = '';
		if(isset($_POST['page'])) {
			if($_POST['page'] == 'add_customer') {
				$customer_cost_type_list = Model_Customercosttype::GetCustomerCostTypeListExceptOther();
				$result = json_encode($customer_cost_type_list);
			}
		}
		return $result;
	}

	/**
	 * 获取酒店类型列表
	 * @access  public
	 * @return  Response
	 */
	public function action_hoteltypelist($page = null)
	{
		$result = '';
		if(isset($_POST['page'])) {
			if($_POST['page'] == 'add_customer') {
				$hotel_type_list = Model_Hoteltype::GetHotelTypeListAll();
				$result = json_encode($hotel_type_list);
			}
		}
		return $result;
	}

	/**
	 * 获取房型列表
	 * @access  public
	 * @return  Response
	 */
	public function action_roomtypelist($page = null)
	{
		$result = '';
		if(isset($_POST['page'])) {
			if($_POST['page'] == 'add_customer') {
				$room_type_list = Model_Roomtype::GetRoomTypeListAll();
				$result = json_encode($room_type_list);
			}
		}
		return $result;
	}

}