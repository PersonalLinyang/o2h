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
			
			$data['customer_source_list'] = Model_Customersource::GetCustomerSourceListAll();
			$data['route_list'] = Model_Route::GetRouteSimpleListAll();
			$data['spot_list'] = Model_Spot::SelectSpotSimpleListAll();
			$data['hotel_type_list'] = Model_Hoteltype::GetHotelTypeListAll();
			$data['room_type_list'] = Model_Roomtype::GetRoomTypeListAll();
			$data['customer_cost_type_list'] = Model_Customercosttype::GetCustomerCostTypeListExceptOther();
			
			$data['input_customer_name'] = '';
			$data['input_customer_source'] = '';
			$data['input_men_num'] = '';
			$data['input_women_num'] = '';
			$data['input_children_num'] = '';
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
			$data['input_customer_cost'] = array();
			$data['input_dinner_demand'] = '';
			$data['input_airplane_num'] = '';
			$data['input_cost_total'] = '';
			
			if(isset($_POST['page'])) {
				$error_message_list = array();
				
				//数据来源检验
				if($_POST['page'] == 'add_customer') {
					var_dump($_POST);
					exit;
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
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if(isset($_POST['page'])) {
				if($_POST['page'] == 'add_customer') {
					$customer_cost_type_list = Model_Customercosttype::GetCustomerCostTypeListExceptOther();
					$result = json_encode($customer_cost_type_list);
				}
			}
//		}
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
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if(isset($_POST['page'])) {
				if($_POST['page'] == 'add_customer') {
					$hotel_type_list = Model_Hoteltype::GetHotelTypeListAll();
					$result = json_encode($hotel_type_list);
				}
			}
//		}
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
//		if(isset($_SESSION['login_user']['permission'][5][7][1]) && isset($_POST['delete_id'], $_POST['page'])) {
			if(isset($_POST['page'])) {
				if($_POST['page'] == 'add_customer') {
					$room_type_list = Model_Roomtype::GetRoomTypeListAll();
					$result = json_encode($room_type_list);
				}
			}
//		}
		return $result;
	}

}