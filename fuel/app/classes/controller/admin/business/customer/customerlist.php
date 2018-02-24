<?php
/* 
 * 顾客信息一览页
 */

class Controller_Admin_Business_Customer_Customerlist extends Controller_Admin_App
{

	/**
	 * 顾客信息一览页
	 * @access  public
	 * @return  Response
	 */
	public function action_index($page = 1)
	{
		$data = array();
		
		//调用共用Header
		$data['header'] = Request::forge('admin/common/header')->execute()->response();
		
		//当前登陆用户
		$login_user_id = $_SESSION['login_user']['id'];
		
		try {
			if(!is_numeric($page)) {
				//页数不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($login_user_id, 'sub_group', 13)) {
				//当前登陆用户不具备查看顾客信息的权限
				return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
			} else {
				$data['success_message'] = '';
				$data['error_message'] = '';
				
				//是否具备添加顾客信息权限
				$data['add_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 26);
				//是否具备编辑顾客信息权限
				$data['modify_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 27);
				//是否具备编辑未设定负责人的顾客信息权限
				$data['modify_new_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 10);
				//是否具备编辑任意顾客信息权限
				$data['modify_any_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 11);
				//是否具备查看负责外的顾客信息(未设定负责人)权限
				$data['view_new_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 28);
				//是否具备查看任意顾客信息权限
				$data['view_any_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 12);
				//是否具备删除顾客信息权限
				$data['delete_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 29);
				//是否具备删除未设定负责人的顾客信息权限
				$data['delete_new_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 13);
				//是否具备删除任意顾客信息权限
				$data['delete_any_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'authority', 14);
				//是否具备查看已删除顾客信息权限
				$data['view_deleted_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 30);
				//是否具备批量导入顾客信息权限
				$data['import_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 31);
				//是否具备导出顾客信息列表权限
				$data['export_able_flag'] = Model_Permission::CheckPermissionByUser($login_user_id, 'function', 32);
				
				//获取顾客状态列表
				if($data['view_deleted_able_flag']) {
					$data['customer_status_list'] = Model_Customerstatus::SelectCustomerStatusList(array('active_only' => true));
				} else {
					$data['customer_status_list'] = Model_Customerstatus::SelectCustomerStatusList(array('active_only' => true, 'active_customer_only' => true));
				}
				//获取顾客状态列表
				$data['customer_source_list'] = Model_Customersource::SelectCustomerSourceList(array('active_only' => true));
				
				//获取自身用户ID
				$data['user_id_self'] = $login_user_id;
				
				//每页显示顾客数
				$num_per_page = 20;
				//本页前后最大可链接页数
				$data['page_link_max'] = 3;
				
				//检索条件
				$data['select_name'] = isset($_GET['select_name']) ? preg_replace('/( |　)/', ' ', $_GET['select_name']) : '';
				$data['select_status'] = isset($_GET['select_status']) && is_array($_GET['select_status']) ? $_GET['select_status'] : array();
				$data['select_source'] = isset($_GET['select_source']) && is_array($_GET['select_source']) ? $_GET['select_source'] : array();
				$data['select_people_min'] = isset($_GET['select_people_min']) ? $_GET['select_people_min'] : '';
				$data['select_people_max'] = isset($_GET['select_people_max']) ? $_GET['select_people_max'] : '';
				$data['select_days_min'] = isset($_GET['select_days_min']) ? $_GET['select_days_min'] : '';
				$data['select_days_max'] = isset($_GET['select_days_max']) ? $_GET['select_days_max'] : '';
				$data['select_start_at_min'] = isset($_GET['select_start_at_min']) ? $_GET['select_start_at_min'] : '';
				$data['select_start_at_max'] = isset($_GET['select_start_at_max']) ? $_GET['select_start_at_max'] : '';
				$data['select_created_at_min'] = isset($_GET['select_created_at_min']) ? $_GET['select_created_at_min'] : '';
				$data['select_created_at_max'] = isset($_GET['select_created_at_max']) ? $_GET['select_created_at_max'] : '';
				$data['select_staff_pattern'] = isset($_GET['select_staff_pattern']) && is_array($_GET['select_staff_pattern']) ? $_GET['select_staff_pattern'] : array();
				$data['sort_column'] = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';
				$data['sort_method'] = isset($_GET['sort_method']) ? $_GET['sort_method'] : 'desc';
				$data['get_params'] = count($_GET) ? '?' . http_build_query($_GET) : '';
				
				//显示结果默认值
				$data['customer_list'] = array();
				$data['customer_count'] = 0;
				$data['start_number'] = 0;
				$data['end_number'] = 0;
				$data['page_number'] = 1;
				$data['page'] = $page;
				
				//获取顾客信息
				$view_permission = 1;
				if($data['view_any_able_flag']) {
					$view_permission = 3;
				} elseif($data['view_new_able_flag']) {
					$view_permission = 2;
				}
				$params_select = array(
					'customer_name' => $data['select_name'] ? explode(' ', $data['select_name']) : array(),
					'customer_status' => $data['select_status'],
					'customer_source' => $data['select_source'],
					'people_min' => $data['select_people_min'],
					'people_max' => $data['select_people_max'],
					'days_min' => $data['select_days_min'],
					'days_max' => $data['select_days_max'],
					'start_at_min' => $data['select_start_at_min'],
					'start_at_max' => $data['select_start_at_max'],
					'created_at_min' => $data['select_created_at_min'],
					'created_at_max' => $data['select_created_at_max'],
					'staff_pattern' => $data['select_staff_pattern'],
					'sort_column' => $data['sort_column'],
					'sort_method' => $data['sort_method'],
					'page' => $page,
					'num_per_page' => $num_per_page,
					'view_permission' => $view_permission,
					'publish_flag' => true,
					'editor_flag' => true,
				);
				if(!$data['view_deleted_able_flag']) {
					$params_select['active_only'] = true;
				}
				
				$result_select = Model_Customer::SelectCustomerList($params_select, $login_user_id);
				
				//整理显示内容
				if($result_select) {
					$customer_count = $result_select['customer_count'];
					$data['customer_count'] = $customer_count;
					$data['customer_list'] = $result_select['customer_list'];
					$data['start_number'] = $result_select['start_number'];
					$data['end_number'] = $result_select['end_number'];
					if($customer_count > $num_per_page) {
						$data['page_number'] = ceil($customer_count/$num_per_page);
					}
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/business/customer/customer_list', $data, false));
			}
		} catch (Exception $e) {
			//发生系统异常
			return Response::forge(View::forge($this->template . '/admin/error/system_error', $data, false));
		}
	}

}