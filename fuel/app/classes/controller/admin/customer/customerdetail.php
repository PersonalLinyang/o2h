<?php
/* 
 * 顾客详细信息页
 */

class Controller_Admin_Customer_Customerdetail extends Controller_Admin_App
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
		
//		if(isset($_SESSION['login_user']['permission'][5][7][1])) {
			$data['success_message'] = '';
			$data['error_message'] = '';
			
			//获取顾客详细信息
			$customer_info = Model_Customer::SelectCustomerInfoByCustomerId($customer_id);
			
			if($customer_info) {
				$data['customer_info'] = $customer_info;
				
				
				if(isset($_SESSION['add_customer_success'])) {
					$data['success_message'] = '顾客添加成功';
					unset($_SESSION['add_customer_success']);
				}
				
				//调用View
				return Response::forge(View::forge($this->template . '/admin/customer/customer_detail', $data, false));
			} else {
				return Response::forge(View::forge($this->template . '/admin/error/access_error', $data, false));
			}
//		} else {
//			return Response::forge(View::forge($this->template . '/admin/error/permission_error', $data, false));
//		}
	}

}