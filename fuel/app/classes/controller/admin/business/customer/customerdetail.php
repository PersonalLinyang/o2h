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
		
		try {
			if(!is_numeric($customer_id)) {
				//顾客ID不是数字
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			} elseif(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'sub_group', 13)) {
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
//					} elseif(strstr($_SERVER['HTTP_REFERER'], 'admin/modify_customer/' . $customer_id) || strstr($_SERVER['HTTP_REFERER'], 'admin/customer_detail/' . $customer_id)) {
//						if(isset($_SESSION['customer_list_url_detail'])) {
//							$data['customer_list_url'] = $_SESSION['customer_list_url_detail'];
//						}
					}
				}
				//暂时保留一览页URL
				$_SESSION['customer_list_url_detail'] = $data['customer_list_url'];
				
				//获取顾客信息
				$customer = Model_Customer::SelectCustomer(array('customer_id' => $customer_id));
				
				if(!$customer) {
					//不存在该ID的顾客
					return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
				} elseif(empty($customer['staff_id'])) {
					//顾客未设定负责人
					if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'function', 28)) {
						//不具备获取未设定负责人顾客信息的权限
						return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					}
				} elseif($customer['staff_id'] != $_SESSION['login_user']['id'] && !in_array($_SESSION['login_user']['id'], $customer['publish_list'])) {
					//不是负责人且不在公开对象内
					if(!Model_Permission::CheckPermissionByUser($_SESSION['login_user']['id'], 'authority', 12)) {
						//不具备获取任意顾客信息的权限
						return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
					}
				}
				
				//顾客信息
				$data['customer_info'] = $customer;
				
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