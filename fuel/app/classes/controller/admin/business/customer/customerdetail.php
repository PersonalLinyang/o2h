<?php
/* 
 * 顾客详细信息页
 */

class Controller_Admin_Business_Customer_Customerdetail extends Controller_Admin_App
{

	/**
	 * 顾客详细信息页
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
				//顾客ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($login_user_id, 'sub_group', 13)) {
				//当前登陆用户不具备查看顾客的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//获取返回一览页时的一览页URL
				$data['customer_list_url'] = '/admin/customer_list/';
				if(isset($_SERVER['HTTP_REFERER'])) {
					if(strstr($_SERVER['HTTP_REFERER'], 'admin/customer_list')) {
						//通过一览页链接进入
						$data['customer_list_url'] = $_SERVER['HTTP_REFERER'];
					} elseif(strstr($_SERVER['HTTP_REFERER'], 'admin/modify_customer/' . $customer_id) || strstr($_SERVER['HTTP_REFERER'], 'admin/customer_detail/' . $customer_id)) {
						if(isset($_SESSION['customer_list_url_detail'])) {
							$data['customer_list_url'] = $_SESSION['customer_list_url_detail'];
						}
					}
				}
				//暂时保留一览页URL
				$_SESSION['customer_list_url_detail'] = $data['customer_list_url'];
				
				//获取顾客信息
				$customer = Model_Customer::SelectCustomer(array('customer_id' => $customer_id));
				
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
				
				if(!$customer) {
					//不存在该ID的顾客
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				} elseif(empty($customer['staff_id'])) {
					//顾客未设定负责人
					if(!Model_Permission::CheckPermissionByUser($login_user_id, 'function', 28)) {
						//不具备获取未设定负责人顾客信息的权限
						return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					}
				} elseif($customer['staff_id'] != $login_user_id && !in_array($login_user_id, $viewer_id_list)) {
					//不是负责人且不在公开对象内
					if(!Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 12)) {
						//不具备获取任意顾客信息的权限
						return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					}
				}
				
				//编辑权限
				$edit_able_flag = false;
				if(in_array($customer['customer_status'], array('1','2','3','4','5','6','7','8','9'))) {
					if(!$customer['staff_id']) {
						//未设定负责人
						$edit_able_flag = Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 10);
					} elseif($customer['staff_id'] == $login_user_id) {
						//当前登陆用户为负责人
						$edit_able_flag = true;
					} else {
						//负责人为其他用户
						if(in_array($login_user_id, $editor_id_list) || Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 11)) {
							//具备该顾客编辑权限或编辑任意顾客信息权限
							$edit_able_flag = true;
						}
					}
				}
				$data['edit_able_flag'] = $edit_able_flag;
				
				//获取自身用户ID
				$data['user_id_self'] = $login_user_id;
				
				//日程格式整理
				if(count($customer['schedule_list'])) {
					$schedule_list_temp = array();
					$schedule_id_temp = '';
					foreach($customer['schedule_list'] as $schedule) {
						if($schedule['schedule_id'] == $schedule_id_temp) {
							$schedule_list_temp[(count($schedule_list_temp) - 1)]['schedule_user_id_list'][] = $schedule['user_id'];
							$schedule_list_temp[(count($schedule_list_temp) - 1)]['schedule_user_list'][] = array(
								'user_id' => $schedule['user_id'],
								'user_name' => $schedule['user_name'],
							);
						} else {
							$schedule_id_temp = $schedule['schedule_id'];
							$schedule_list_temp[] = array(
								'schedule_date' => date('Y/m/d', strtotime($schedule['start_at'])),
								'schedule_user_id_list' => array($schedule['user_id']),
								'schedule_user_list' => array(
									array(
										'user_id' => $schedule['user_id'],
										'user_name' => $schedule['user_name'],
									),
								),
								'schedule_detail' => array(
									'start_at' => date('H:i', strtotime($schedule['start_at'])),
									'end_at' => date('H:i', strtotime($schedule['end_at'])),
									'schedule_type' => $schedule['schedule_type_name'],
									'schedule_desc' => $schedule['schedule_desc'],
								),
							);
						}
					}
					$schedule_list = array();
					$schedule_date_temp = '';
					$schedule_user_temp = '';
					foreach($schedule_list_temp as $schedule) {
						$schedule_user = implode(',', $schedule['schedule_user_id_list']);
						if($schedule['schedule_date'] == $schedule_date_temp) {
							$schedule_index = count($schedule_list) - 1;
							
							if($schedule_user == $schedule_user_temp) {
								$info_index = count($schedule_list[$schedule_index]['schedule_info_list']) - 1;
								$schedule_list[$schedule_index]['schedule_info_list'][$info_index]['schedule_detail_list'][] = $schedule['schedule_detail'];
							} else {
								$schedule_list[$schedule_index]['schedule_info_list'][] = array(
									'schedule_user_list' => $schedule['schedule_user_list'],
									'schedule_detail_list' => array($schedule['schedule_detail']),
								);
							}
						} else {
							$schedule_date_temp = $schedule['schedule_date'];
							$schedule_user_temp = $schedule_user;
							$schedule_list[] = array(
								'schedule_date' => $schedule['schedule_date'],
								'schedule_info_list' => array(
									array(
										'schedule_user_list' => $schedule['schedule_user_list'],
										'schedule_detail_list' => array($schedule['schedule_detail']),
									),
								),
							);
						}
					}
					$customer['schedule_list'] = $schedule_list;
				}
				
				//状态修改按钮关联
				if(in_array($customer['customer_status'], array('1','2','3','4','5','6','7','8','9')) && $customer['staff_id'] == $login_user_id) {
					$btn_status_text_list = array(
						'1' => '完成需求确认',
						'2' => '完成行程制定',
						'3' => '完成信息确认',
						'4' => '定金到账',
						'5' => '完成接机',
						'6' => '结束旅行',
						'7' => '尾款到账',
						'8' => '完成送机',
						'9' => '完成最终整理',
					);
					$data['btn_status_text'] = $btn_status_text_list[$customer['customer_status']];
					$next_status = Model_Customerstatus::SelectNextCustomerStatus($customer['customer_status']);
					$data['next_status_name'] = $next_status['customer_status_name'];
				}
				
				//获取用户来源列表
				$customer_status_delete_list = Model_Customerstatus::SelectCustomerStatusList(array('sort_id_list' => array(2), 'active_only' => true));
				$data['customer_status_delete_list'] = $customer_status_delete_list ? $customer_status_delete_list : array();
				
				//顾客信息
				$data['customer_info'] = $customer;
				
				//输出提示信息
				if(isset($_SESSION['add_customer_success'])) {
					$data['success_message'] = '顾客信息添加成功';
					unset($_SESSION['add_customer_success']);
				}
				if(isset($_SESSION['modify_customer_success'])) {
					$data['success_message'] = '顾客信息修改成功';
					unset($_SESSION['modify_customer_success']);
				}
				if(isset($_SESSION['modify_customer_status_success'])) {
					$data['success_message'] = '顾客状态变更成功';
					unset($_SESSION['modify_customer_status_success']);
				}
				if(isset($_SESSION['modify_customer_status_error'])) {
					$data['error_message'] = '顾客状态变更失敗';
					unset($_SESSION['modify_customer_status_error']);
				}
				if(isset($_SESSION['modify_customer_delete_success'])) {
					$data['success_message'] = '失效变更成功';
					unset($_SESSION['modify_customer_delete_success']);
				}
				if(isset($_SESSION['modify_customer_delete_error'])) {
					$data['error_message'] = '失效变更失敗';
					unset($_SESSION['modify_customer_delete_error']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/business/customer/customer_detail', $data, false));
				
				//顾客信息
				$data['customer_info'] = $customer;
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}