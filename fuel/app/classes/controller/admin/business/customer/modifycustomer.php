<?php
/* 
 * 顾客信息修改页
 */

class Controller_Admin_Business_Customer_Modifycustomer extends Controller_Admin_App
{

	/**
	 * 顾客信息修改页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($customer_id)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		//当前登陆用户
		$login_user_id = $_SESSION['login_user']['id'];
		
		try {
			if(!is_numeric($customer_id)) {
				//顾客信息ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($login_user_id, 'function', 27)) {
				//当前登陆用户不具备修改顾客信息的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['error_message'] = '';
				
				//获取原本顾客信息
				$customer = Model_Customer::SelectCustomer(array('customer_id' => $customer_id, 'active_only' => true));
				
				if(!$customer) {
					//不存在该ID的顾客
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					exit;
				} 
				
				//阅览者ID列表
				$viewer_id_list = array();
				foreach($customer['viewer_list'] as $viewer) {
					$viewer_id_list[] = $viewer['user_id'];
				}
				//编辑者ID列表
				$editor_id_list = array();
				foreach($customer['editor_list'] as $editor) {
					$editor_id_list[] = $editor['user_id'];
				}
				
				if(!in_array($customer['customer_status'], array(1,2,3,4,5,6,7,8,9))) {
					//顾客不是可编辑状态
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				} elseif(!$customer['staff_id']) {
					//顾客未设定负责人
					if(!Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 10)) {
						//当前登陆用户不具备修改未设定负责人顾客信息的权限
						return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					}
				} elseif($customer['staff_id'] != $login_user_id && !in_array($login_user_id, $editor_id_list)) {
					//当前登陆用户不是该ID的顾客负责人且不在该ID的顾客编辑者之内
					if(!Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 11)) {
						//当前登陆用户不具备修改任意顾客信息的权限
						return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					}
				}
				
				$data['error_message'] = '';
				
				//页面标题
				$data['page_title'] ='顾客信息修改';
				//表单页面索引
				$data['form_page_index'] = 'modify_customer';
				//返回页URL
				$data['return_page_url'] = '/admin/customer_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/customer_list')) {
						$data['return_page_url'] = $_SERVER['HTTP_REFERER'];
					}
				}
				//获取自身用户ID
				$data['user_id_self'] = $login_user_id;
				//获取当前负责人
				$data['staff_id_now'] = $customer['staff_id'];
				//获取顾客状态
				$data['customer_status_now'] = $customer['customer_status'];
				
				//获取旅游目的列表
				$travel_reason_list = Model_Travelreason::SelectTravelReasonList(array('active_only' => true));
				$data['travel_reason_list'] = $travel_reason_list ? $travel_reason_list : array();
				//获取用户来源列表
				$customer_source_list = Model_Customersource::SelectCustomerSourceList(array('active_only' => true));
				$data['customer_source_list'] = $customer_source_list ? $customer_source_list : array();
				//获取用户列表
				$user_list = Model_User::SelectUserSimpleList(array('active_only' => true, 'user_type_except' => array(1), 'sort_column' => 'user_name'));
				$data['user_list'] = $user_list ? $user_list : array();
				//获取旅游路线列表
				$route_list = Model_Route::SelectRouteSimpleList(array('active_only' => true, 'route_status' => array(1)));
				$data['route_list'] = $route_list ? $route_list : array();
				//获取景点列表
				$spot_list = Model_Spot::SelectSpotSimpleList(array('active_only' => true, 'spot_status' => array(1)));
				$data['spot_list'] = $spot_list ? $spot_list : array();
				//获取酒店类别列表
				$hotel_type_list = Model_Hoteltype::SelectHotelTypeList(array('active_only' => true));
				$data['hotel_type_list'] = $hotel_type_list ? $hotel_type_list : array();
				//获取房型列表
				$room_type_list = Model_Roomtype::SelectRoomTypeList(array('active_only' => true));
				$data['room_type_list'] = $room_type_list ? $room_type_list : array();
				//获取用户成本类别列表
				$customer_cost_type_list = Model_Customercosttype::SelectCustomerCostTypeList(array('active_only' => true));
				$data['customer_cost_type_list'] = $customer_cost_type_list ? $customer_cost_type_list : array();
				//获取具备编辑任意顾客信息权限的用户列表
				$edit_able_user_list = Model_User::SelectUserSimpleList(array('active_only' => true, 'user_type_except' => array(1), 'permission' => array('permission_type' => 'authority', 'permission_id' => 11), 'sort_column' => 'user_name'));
				$data['edit_able_id_list'] = array(); 
				foreach($edit_able_user_list as $edit_able_user) {
					$data['edit_able_id_list'][] = $edit_able_user['user_id'];
				}
				//获取具备查看任意顾客信息权限的用户列表
				$view_able_user_list = Model_User::SelectUserSimpleList(array('active_only' => true, 'sort_column' => 'user_name', 'permission' => array('permission_type' => 'authority', 'permission_id' => 12)));
				$data['view_able_id_list'] = array(); 
				foreach($view_able_user_list as $view_able_user) {
					$data['view_able_id_list'][] = $view_able_user['user_id'];
				}
				//获取日程类型列表
				$schedule_type_list = Model_Scheduletype::SelectScheduleTypeList(array('active_only' => true));
				$data['schedule_type_list'] = $schedule_type_list ? $schedule_type_list : array();
				
				//form控件默认值设定
				$data['input_customer_name'] = $customer['customer_name'];
				$data['input_customer_source'] = $customer['customer_source'];
				$data['input_customer_gender'] = $customer['customer_gender'];
				$data['input_customer_age'] = $customer['customer_age'];
				$data['input_travel_reason'] = $customer['travel_reason'];
				$data['input_staff_id'] = $customer['staff_id'];
				$data['input_men_num'] = $customer['men_num'];
				$data['input_women_num'] = $customer['women_num'];
				$data['input_children_num'] = $customer['children_num'];
				$data['input_travel_days'] = $customer['travel_days'];
				$data['input_start_at_year'] = $customer['start_at_year'];
				$data['input_start_at_month'] = $customer['start_at_month'];
				$data['input_start_at_day'] = $customer['start_at_day'];
				$data['input_route_id'] = $customer['route_id'];
				$data['input_budget_base'] = $customer['budget_base'];
				$data['input_budget_total'] = $customer['budget_total'];
				$data['input_first_flag'] = $customer['first_flag'];
				$data['input_spot_hope_flag'] = $customer['spot_hope_flag'];
				$data['input_spot_hope_list'] = array();
				foreach($customer['spot_hope_list'] as $spot) {
					$data['input_spot_hope_list'][] = $spot['spot_id'];
				}
				$data['input_spot_hope_other'] = $customer['spot_hope_other'];
				$data['input_hotel_reserve_flag'] = $customer['hotel_reserve_flag'];
				$data['input_hotel_reserve_list'] = $customer['hotel_reserve_list'];
				$data['input_cost_budget'] = $customer['cost_budget'];
				$data['input_turnover'] = $customer['turnover'];
				$data['input_customer_cost_list'] = $customer['customer_cost_list'];
				$data['input_dinner_demand'] = $customer['dinner_demand'];
				$data['input_airplane_num'] = $customer['airplane_num'];
				$data['input_customer_email'] = $customer['customer_email'];
				$data['input_customer_tel'] = $customer['customer_tel'];
				$data['input_customer_wechat'] = $customer['customer_wechat'];
				$data['input_customer_qq'] = $customer['customer_qq'];
				$data['input_cost_total'] = $customer['cost_total'];
				$data['input_comment'] = $customer['comment'];
				$data['input_viewer_id_list'] = $viewer_id_list;
				$data['input_editor_id_list'] = $editor_id_list;
				//日程格式整理
				$data['input_schedule_list'] = array();
				if(count($customer['schedule_list'])) {
					$schedule_list_temp = array();
					$schedule_id_temp = '';
					foreach($customer['schedule_list'] as $schedule) {
						if($schedule['schedule_id'] == $schedule_id_temp) {
							$schedule_list_temp[(count($schedule_list_temp) - 1)]['schedule_user_list'][] = $schedule['user_id'];
						} else {
							$schedule_id_temp = $schedule['schedule_id'];
							$schedule_list_temp[] = array(
								'schedule_date' => date('Y/m/d', strtotime($schedule['start_at'])),
								'schedule_user_list' => array($schedule['user_id']),
								'schedule_detail' => array(
									'start_at' => date('H:i', strtotime($schedule['start_at'])),
									'end_at' => date('H:i', strtotime($schedule['end_at'])),
									'schedule_type' => $schedule['schedule_type'],
									'schedule_desc' => $schedule['schedule_desc'],
								),
							);
						}
					}
					$schedule_list = array();
					$schedule_date_temp = '';
					$schedule_user_temp = '';
					foreach($schedule_list_temp as $schedule) {
						$schedule_user = implode(',', $schedule['schedule_user_list']);
						if($schedule['schedule_date'] == $schedule_date_temp && $schedule_user == $schedule_user_temp) {
							$schedule_list[(count($schedule_list) - 1)]['schedule_detail_list'][] = $schedule['schedule_detail'];
						} else {
							$schedule_date_temp = $schedule['schedule_date'];
							$schedule_user_temp = $schedule_user;
							$schedule_list[] = array(
								'schedule_date' => $schedule['schedule_date'],
								'schedule_user_list' => $schedule['schedule_user_list'],
								'schedule_detail_list' => array($schedule['schedule_detail']),
							);
						}
					}
					$data['input_schedule_list'] = $schedule_list;
				}
				
				if(isset($_POST['page'])) {
					$error_message_list = array();
					
					if($_POST['page'] != $data['form_page_index']) {
						//数据来源不是顾客信息修改页
						return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
					} else {
						//form控件当前值设定
						$data['input_customer_name'] = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : $data['input_customer_name'];
						$data['input_customer_source'] = isset($_POST['customer_source']) ? trim($_POST['customer_source']) : $data['input_customer_source'] ;
						$data['input_customer_gender'] = isset($_POST['customer_gender']) ? trim($_POST['customer_gender']) : $data['input_customer_gender'];
						$data['input_customer_age'] = isset($_POST['customer_age']) ? trim($_POST['customer_age']) : $data['input_customer_age'];
						$data['input_travel_reason'] = isset($_POST['travel_reason']) ? trim($_POST['travel_reason']) : $data['input_travel_reason'];
						$data['input_staff_id'] = isset($_POST['staff_id']) ? trim($_POST['staff_id']) : $data['input_staff_id'];
						$data['input_men_num'] = isset($_POST['men_num']) ? trim($_POST['men_num']) : $data['input_men_num'];
						$data['input_women_num'] = isset($_POST['women_num']) ? trim($_POST['women_num']) : $data['input_women_num'];
						$data['input_children_num'] = isset($_POST['children_num']) ? trim($_POST['children_num']) : $data['input_children_num'];
						$data['input_travel_days'] = isset($_POST['travel_days']) ? trim($_POST['travel_days']) : $data['input_travel_days'];
						$data['input_start_at_year'] = isset($_POST['start_at_year']) ? trim($_POST['start_at_year']) : $data['input_start_at_year'];
						$data['input_start_at_month'] = isset($_POST['start_at_month']) ? trim($_POST['start_at_month']) : $data['input_start_at_month'];
						$data['input_start_at_day'] = isset($_POST['start_at_day']) ? trim($_POST['start_at_day']) : $data['input_start_at_day'];
						$data['input_route_id'] = isset($_POST['route_id']) ? trim($_POST['route_id']) : $data['input_route_id'];
						$data['input_budget_base'] = isset($_POST['budget_base']) ? trim($_POST['budget_base']) : $data['input_budget_base'];
						$data['input_budget_total'] = isset($_POST['budget_total']) ? trim($_POST['budget_total']) : $data['input_budget_total'];
						$data['input_first_flag'] = isset($_POST['first_flag']) ? trim($_POST['first_flag']) : $data['input_first_flag'];
						$data['input_spot_hope_flag'] = isset($_POST['spot_hope_flag']) ? trim($_POST['spot_hope_flag']) : $data['input_spot_hope_flag'];
						$data['input_spot_hope_list'] = (isset($_POST['spot_hope_list']) && is_array($_POST['spot_hope_list'])) ? $_POST['spot_hope_list'] : $data['input_spot_hope_list'];
						$data['input_spot_hope_other'] = isset($_POST['spot_hope_other']) ? trim($_POST['spot_hope_other']) : $data['input_spot_hope_other'];
						$data['input_hotel_reserve_flag'] = isset($_POST['hotel_reserve_flag']) ? trim($_POST['hotel_reserve_flag']) : $data['input_hotel_reserve_flag'];
						$data['input_cost_budget'] = isset($_POST['cost_budget']) ? trim($_POST['cost_budget']) : $data['input_cost_budget'];
						$data['input_turnover'] = isset($_POST['turnover']) ? trim($_POST['turnover']) : $data['input_turnover'];
						$data['input_dinner_demand'] = isset($_POST['dinner_demand']) ? trim($_POST['dinner_demand']) : $data['input_dinner_demand'];
						$data['input_airplane_num'] = isset($_POST['airplane_num']) ? trim($_POST['airplane_num']) : $data['input_airplane_num'];
						$data['input_customer_email'] = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : $data['input_customer_email'];
						$data['input_customer_tel'] = isset($_POST['customer_tel']) ? trim($_POST['customer_tel']) : $data['input_customer_tel'];
						$data['input_customer_wechat'] = isset($_POST['customer_wechat']) ? trim($_POST['customer_wechat']) : $data['input_customer_wechat'];
						$data['input_customer_qq'] = isset($_POST['customer_qq']) ? trim($_POST['customer_qq']) : $data['input_customer_qq'];
						$data['input_comment'] = isset($_POST['comment']) ? trim($_POST['comment']) : $data['input_comment'];
						//form控件值设定 酒店预约
						if(isset($_POST['hotel_reserve_row']) && is_array($_POST['hotel_reserve_row'])) {
							$data['input_hotel_reserve_list'] = array();
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
						//form控件值设定 实际成本
						$cost_total = 0;
						if(isset($_POST['customer_cost_row']) && is_array($_POST['customer_cost_row'])) {
							$data['input_customer_cost_list'] = array();
							foreach($_POST['customer_cost_row'] as $row_num) {
								$customer_cost_total = 0;
								if(isset($_POST['customer_cost_day_' . $row_num]) && isset($_POST['customer_cost_people_' . $row_num]) && isset($_POST['customer_cost_each_' . $row_num])
										&& is_numeric($_POST['customer_cost_day_' . $row_num]) && is_numeric($_POST['customer_cost_people_' . $row_num]) && is_numeric($_POST['customer_cost_each_' . $row_num])) {
									$customer_cost_total = floatval($_POST['customer_cost_day_' . $row_num]) * floatval($_POST['customer_cost_people_' . $row_num]) * floatval($_POST['customer_cost_each_' . $row_num]);
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
						$data['input_cost_total'] = $cost_total;
						//form控件值设定 用户权限
						if(isset($_POST['staff_permission']) && is_array($_POST['staff_permission'])) {
							$data['input_viewer_id_list'] = array();
							$data['input_editor_id_list'] = array();
							foreach($_POST['staff_permission'] as $user_id => $permission) {
								switch($permission) {
									case '1':
										$data['input_viewer_id_list'][] = $user_id;
										break;
									case '2':
										$data['input_editor_id_list'][] = $user_id;
										break;
									default:
										break;
								}
							}
						}
						//form控件值设定 日程
						if(isset($_POST['schedule_num']) && is_array($_POST['schedule_num'])) {
							$schedule_list = array();
							foreach($_POST['schedule_num'] as $schedule_num) {
								$schedule = array();
								$schedule['schedule_date'] = isset($_POST['schedule_date_' . $schedule_num]) ? $_POST['schedule_date_' . $schedule_num] : '';
								$schedule['schedule_user_list'] = isset($_POST['schedule_user_list_' . $schedule_num]) ? $_POST['schedule_user_list_' . $schedule_num] : array();
								$schedule['schedule_detail_list'] = array();
								if(isset($_POST['schedule_row_' . $schedule_num]) && is_array($_POST['schedule_row_' . $schedule_num])) {
									foreach($_POST['schedule_row_' . $schedule_num] as $schedule_row) {
										$schedule_detail = array();
										$schedule_detail['start_at'] = isset($_POST['schedule_start_at_' . $schedule_num . '_' . $schedule_row]) ? $_POST['schedule_start_at_' . $schedule_num . '_' . $schedule_row] : '';
										$schedule_detail['end_at'] = isset($_POST['schedule_end_at_' . $schedule_num . '_' . $schedule_row]) ? $_POST['schedule_end_at_' . $schedule_num . '_' . $schedule_row] : '';
										$schedule_detail['schedule_type'] = isset($_POST['schedule_type_' . $schedule_num . '_' . $schedule_row]) ? $_POST['schedule_type_' . $schedule_num . '_' . $schedule_row] : '';
										$schedule_detail['schedule_desc'] = isset($_POST['schedule_desc_' . $schedule_num . '_' . $schedule_row]) ? $_POST['schedule_desc_' . $schedule_num . '_' . $schedule_row] : '';
										$schedule['schedule_detail_list'][] = $schedule_detail;
									}
								}
								$schedule_list[] = $schedule;
							}
							$data['input_schedule_list'] = $schedule_list;
						}
						
						//修改顾客用数据生成
						$param_update = array(
							'customer_id' => $customer_id,
							'customer_name' => $data['input_customer_name'],
							'customer_status' => $customer['customer_status'],
							'customer_source' => $data['input_customer_source'],
							'customer_gender' => $data['input_customer_gender'],
							'customer_age' => $data['input_customer_age'],
							'travel_reason' => $data['input_travel_reason'],
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
							'form_flag' => $customer['form_flag'],
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
							'customer_email' => $data['input_customer_email'],
							'customer_tel' => $data['input_customer_tel'],
							'customer_wechat' => $data['input_customer_wechat'],
							'customer_qq' => $data['input_customer_qq'],
							'comment' => $data['input_comment'],
							'viewer_list' => $data['input_viewer_id_list'],
							'editor_list' => $data['input_editor_id_list'],
							'schedule_list' => $data['input_schedule_list'],
							'modified_at' => date('Y-m-d H:i:s', time()),
							'modified_by' => $login_user_id,
						);
						
						//输入内容检查
						$result_check = Model_Customer::CheckEditCustomer($param_update);
						
						if($result_check['result']) {
							$result_update = Model_Customer::UpdateCustomer($param_update);
							
							if($result_update) {
								//添加成功 页面跳转
								$_SESSION['modify_customer_success'] = true;
								header('Location: //' . $_SERVER['HTTP_HOST'] . '/admin/customer_detail/' . $customer_id . '/');
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
										$error_message_list[] = '顾客姓名不能超过100字';
										break;
									case 'empty_customer_source':
										$error_message_list[] = '请选择顾客来源';
										break;
									case 'noint_men_num':
									case 'minus_men_num':
									case 'noint_women_num':
									case 'minus_women_num':
									case 'noint_children_num':
									case 'minus_children_num':
										$error_message_list[] = '请为人数部分输入一个非负整数';
										break;
									case 'error_travel_days':
										$error_message_list[] = '请为旅行天数部分输入一个非负整数';
										break;
									case 'empty_start_at_year':
										$error_message_list[] = '请选择来日年';
										break;
									case 'empty_start_at_month':
										$error_message_list[] = '请选择来日月';
										break;
									case 'error_start_at_date':
										$error_message_list[] = '您选择的来日时间不存在,请重新选择';
										break;
									case 'error_budget_base':
									case 'error_budget_total':
										$error_message_list[] = '请为预算部分输入一个金额';
										break;
									case 'empty_hotel_type':
										$error_message_list[] = '请选择要预约的酒店类别';
										break;
									case 'empty_room_type':
										$error_message_list[] = '请选择要预约的房型';
										break;
									case 'empty_people_num':
										$error_message_list[] = '请输入预约人数';
										break;
									case 'noint_people_num':
									case 'minus_people_num':
										$error_message_list[] = '请为酒店预约的人数部分输入一个非负整数';
										break;
									case 'empty_room_num':
										$error_message_list[] = '请输入预约间数';
										break;
									case 'noint_room_num':
									case 'minus_room_num':
										$error_message_list[] = '请为酒店预约的间数部分输入一个非负整数';
										break;
									case 'empty_day_num':
										$error_message_list[] = '请输入预约天数';
										break;
									case 'noint_day_num':
									case 'minus_day_num':
										$error_message_list[] = '请为酒店预约的天数部分输入一个非负整数';
										break;
									case 'error_cost_budget':
										$error_message_list[] = '请为成本报价部分输入一个金额';
										break;
									case 'error_turnover':
										$error_message_list[] = '请为营业额部分输入一个金额';
										break;
									case 'empty_customer_cost_type':
										$error_message_list[] = '请选择实际成本项目';
										break;
									case 'empty_customer_cost_desc':
										$error_message_list[] = '请为项目为「其他」的实际成本输入简述';
										break;
									case 'empty_customer_cost_day':
										$error_message_list[] = '请输入实际成本天数';
										break;
									case 'noint_customer_cost_day':
									case 'minus_customer_cost_day':
										$error_message_list[] = '请为实际成本的天数部分输入一个非负整数';
										break;
									case 'empty_customer_cost_people':
										$error_message_list[] = '请输入实际成本人数';
										break;
									case 'noint_customer_cost_people':
									case 'minus_customer_cost_people':
										$error_message_list[] = '请为实际成本的人数部分输入一个非负整数';
										break;
									case 'empty_customer_cost_day':
										$error_message_list[] = '请输入实际成本天数';
										break;
									case 'noint_customer_cost_day':
									case 'minus_customer_cost_day':
										$error_message_list[] = '请为实际成本的天数部分输入一个非负整数';
										break;
									case 'empty_customer_cost_each':
										$error_message_list[] = '请输入实际成本单价';
										break;
									case 'error_customer_cost_each':
										$error_message_list[] = '请为实际成本的单价部分输入一个金额';
										break;
									case 'long_airplane_num':
										$error_message_list[] = '航班号不能超过20字';
										break;
									case 'empty_customer_contact':
										$error_message_list[] = '请至少留下顾客的一种联系方式';
										break;
									case 'long_customer_email':
										$error_message_list[] = '电子邮箱不能超过200字';
										break;
									case 'long_customer_tel':
										$error_message_list[] = '联系电话不能超过20字';
										break;
									case 'long_customer_wechat':
										$error_message_list[] = '微信号不能超过20字';
										break;
									case 'long_customer_qq':
										$error_message_list[] = 'QQ号不能超过20字';
										break;
									case 'empty_schedule_date':
										$error_message_list[] = '请选择日程日期';
										break;
									case 'format_schedule_date':
										$error_message_list[] = '日程日期格式不符合要求,例:2030/01/01';
										break;
									case 'error_schedule_date':
										$error_message_list[] = '您选择的日程日期不存在,请重新选择';
										break;
									case 'empty_schedule_user_list':
										$error_message_list[] = '请至少为日程设定一位负责人';
										break;
									case 'empty_schedule_detail_list':
										$error_message_list[] = '请至少为日程添加一条详细日程';
										break;
									case 'empty_schedule_time':
										$error_message_list[] = '请选择详细日程时间';
										break;
									case 'error_schedule_time':
										$error_message_list[] = '详细日程开始时间必须早于结束时间';
										break;
									case 'dup_self_schedule_time':
										$error_message_list[] = '当前设定的日程中时间上存在冲突';
										break;
									case 'dup_user_schedule_time':
										$error_message_list[] = '当前设定的日程与担当负责人的日程存在冲突,请确认各负责人日程后进行调整';
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
					}
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/business/customer/edit_customer', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}
	
	/**
	 * 顾客状态更新
	 * @access  public
	 * @return  Response
	 */
	public function action_modifycustomerstatus($param = null)
	{
		try {
			if(isset($_POST['page'], $_POST['modify_id'])) {
				if(is_numeric($_POST['modify_id']) && $_POST['page'] == 'customer_detail') {
					$customer_id = $_POST['modify_id'];
					
					//获取顾客信息
					$customer = Model_Customer::SelectCustomer(array('customer_id' => $customer_id, 'active_only' => true));
					
					if($customer) {
						if($customer['staff_id'] == $_SESSION['login_user']['id'] && in_array($customer['customer_status'], array('1','2','3','4','5','6','7','8','9'))) {
							//当前登陆用户是该顾客负责人
							$params_update = array(
								'customer_id' => $customer_id,
								'customer_status' => $customer['customer_status'],
							);
							
							//数据更新
							$result_update = Model_Customer::UpdateCustomerStatus($params_update);
							
							if($result_update) {
								//更新成功
								$_SESSION['modify_customer_status_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/customer_detail/' . $_POST['modify_id'] . '/');
								exit;
							}
						}
					}
				}
			}
			
			//更新失敗
			$_SESSION['modify_customer_status_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/customer_detail/' . $_POST['modify_id'] . '/');
			exit;
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['modify_customer_status_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/customer_detail/' . $_POST['modify_id'] . '/');
			exit;
		}
	}
	
	/**
	 * 顾客失效
	 * @access  public
	 * @return  Response
	 */
	public function action_modifycustomerdelete($param = null)
	{
		try {
			if(isset($_POST['page'], $_POST['modify_id'], $_POST['modify_reason'])) {
				if(is_numeric($_POST['modify_id']) && $_POST['page'] == 'customer_detail') {
					$customer_id = $_POST['modify_id'];
					
					//获取顾客信息
					$customer = Model_Customer::SelectCustomer(array('customer_id' => $customer_id, 'active_only' => true));
					
					if($customer) {
						if($customer['staff_id'] == $_SESSION['login_user']['id'] && in_array($customer['customer_status'], array('1','2','3','4','5','6','7','8','9'))) {
							//当前登陆用户是该顾客负责人
							$params_update = array(
								'customer_id' => $customer_id,
								'customer_status' => $_POST['modify_reason'],
							);
							
							//数据更新
							$result_update = Model_Customer::UpdateCustomerDelete($params_update);
							
							if($result_update) {
								//更新成功
								$_SESSION['modify_customer_delete_success'] = true;
								header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/customer_detail/' . $_POST['modify_id'] . '/');
								exit;
							}
						}
					}
				}
			}
			
			//更新失敗
			$_SESSION['modify_customer_delete_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/customer_detail/' . $_POST['modify_id'] . '/');
			exit;
		} catch (Exception $e) {
			//发生系统异常
			$_SESSION['modify_customer_delete_error'] = true;
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/customer_detail/' . $_POST['modify_id'] . '/');
			exit;
		}
	}

}