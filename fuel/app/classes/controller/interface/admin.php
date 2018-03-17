<?php
/* 
 * 管理系统专用接口
 */

class Controller_Interface_Admin extends Controller_Admin_App
{

	/**
	 * 获取成本项目列表
	 * @access  public
	 * @return  Response
	 */
	public function action_api_customer_cost_type_list($page = null)
	{
		$result = array('result' => false, 'customer_cost_type_list' => array());
		try {
			$allow_page_list = array('edit_customer');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					$customer_cost_type_list = Model_Customercosttype::SelectCustomerCostTypeList(array('active_only' => true));
					$result = array('result' => true, 'customer_cost_type_list' => $customer_cost_type_list);
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}

	/**
	 * 获取酒店类别列表
	 * @access  public
	 * @return  Response
	 */
	public function action_api_hotel_type_list($page = null)
	{
		$result = array('result' => false, 'hotel_type_list' => array());
		try {
			$allow_page_list = array('edit_customer');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					$hotel_type_list = Model_Hoteltype::SelectHotelTypeList(array('active_only' => true));
					$result = array('result' => true, 'hotel_type_list' => $hotel_type_list);
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}

	/**
	 * 获取房型列表
	 * @access  public
	 * @return  Response
	 */
	public function action_api_room_type_list($page = null)
	{
		$result = array('result' => false, 'room_type_list' => array());
		try {
			$allow_page_list = array('edit_customer');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					$room_type_list = Model_Roomtype::SelectRoomTypeList(array('active_only' => true));
					$result = array('result' => true, 'room_type_list' => $room_type_list);
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}

	/**
	 * 获取日程类型列表
	 * @access  public
	 * @return  Response
	 */
	public function action_api_schedule_type_list($page = null)
	{
		$result = array('result' => false, 'schedule_type_list' => array());
		try {
			$allow_page_list = array('edit_customer');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					$schedule_type_list = Model_Scheduletype::SelectScheduleTypeList(array('active_only' => true));
					$result = array('result' => true, 'schedule_type_list' => $schedule_type_list);
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}

	/**
	 * 获取景点列表
	 * @access  public
	 * @return  Response
	 */
	public function action_api_simple_spot_list($page = null)
	{
		$result = array('result' => false, 'spot_list' => array());
		try {
			$allow_page_list = array('edit_route');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					$spot_list = Model_Spot::SelectSpotSimpleList(array('active_only' => true, 'spot_status' => array(1)));
					$result = array('result' => true, 'spot_list' => $spot_list);
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}

	/**
	 * 获取用户列表
	 * @access  public
	 * @return  Response
	 */
	public function action_api_simple_user_list($page = null)
	{
		$result = array('result' => false, 'user_list' => array());
		try {
			$allow_page_list = array('edit_customer');
			
			if(isset($_POST['page'])) {
				if(in_array($_POST['page'], $allow_page_list)) {
					$user_list = Model_User::SelectUserSimpleList(array('active_only' => true, 'user_type_except' => array(1)));
					$result = array('result' => true, 'user_list' => $user_list);
				}
			}
		} catch (Exception $e) {
		}
		return json_encode($result);
	}

}